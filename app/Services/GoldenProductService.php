<?php

namespace App\Services;

use App\Models\GoldenProduct;
use App\Models\SellerProduct;
use App\Models\ProductType;
use App\Models\Locale;
use App\Services\OpenAIService;

class GoldenProductService
{
    private $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function createFromSellerProduct(SellerProduct $sellerProduct): GoldenProduct
    {
        if ($sellerProduct->golden_product_id) {
            return $sellerProduct->goldenProduct;
        }

        $productTypeId = $this->determineProductType($sellerProduct);

        // TODO handle the case when the product type is not found

        $goldenProduct = GoldenProduct::create([
            'product_type_id' => $productTypeId,
        ]);

        // Create translations with attributes for each locale
        foreach (Locale::all() as $locale) {
            $translatedName = $this->translateProductData($sellerProduct->name, $locale->code);
            $translatedDescription = $this->translateProductData($sellerProduct->description, $locale->code);

            // Translate attributes if they exist
            $translatedAttributes = null;
            if ($sellerProduct->attributes) {
                $translatedAttributes = json_decode($this->translateProductData(
                    json_encode($sellerProduct->attributes),
                    $locale->code
                ), true);
            }

            $goldenProduct->translations()->create([
                'name' => $translatedName,
                'description' => $translatedDescription,
                'attributes' => $translatedAttributes,
                'locale_id' => $locale->id,
            ]);
        }

        $sellerProduct->update(['golden_product_id' => $goldenProduct->id]);

        return $goldenProduct;
    }

    private function determineProductType(SellerProduct $sellerProduct): int
    {
        $client = $this->openAIService->getClient();

        $availableTypes = ProductType::pluck('name')->toArray();

        $response = $client->chat()->create([
            'model' =>  $this->openAIService->getSmallModel(),
            'messages' => [
                ['role' => 'user', 'content' => $sellerProduct->name],
            ],
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'classify_product',
                        'description' => 'Classify a product into predefined product types',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'product_type' => [
                                    'type' => 'string',
                                    'enum' => $availableTypes,
                                    'description' => 'The type of product',
                                ],
                                'confidence' => [
                                    'type' => 'number',
                                    'minimum' => 0,
                                    'maximum' => 1,
                                    'description' => 'Confidence level in the classification',
                                ],
                            ],
                            'required' => ['product_type', 'confidence'],
                        ],
                    ],
                ],
            ],
        ]);



        $toolCall = $response->choices[0]->message->toolCalls[0];
        $arguments = json_decode($toolCall->function->arguments, true);
        $productType = ProductType::where('name', $arguments['product_type'])->first();

        return $productType ? $productType->id : null;
    }

    private function translateProductData(string $text, string $targetLocale): string
    {
        $client = $this->openAIService->getClient();

        $response = $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Translate the following text to {$targetLocale}:\n\n{$text}"
                ],
            ],
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'translate_text',
                        'description' => 'Translate text to the target language while maintaining the original meaning and tone',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'translated_text' => [
                                    'type' => 'string',
                                    'description' => 'The translated text in the target language',
                                ],
                            ],
                            'required' => ['translated_text'],
                        ],
                    ],
                ],
            ],
        ]);

        $toolCall = $response->choices[0]->message->toolCalls[0];
        $arguments = json_decode($toolCall->function->arguments, true);

        return $arguments['translated_text'];
    }
}
