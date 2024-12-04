<?php

namespace App\Services;

use App\Models\GoldenProduct;
use App\Models\SellerProduct;
use App\Models\ProductType;
use App\Models\Locale;
use App\Services\OpenAIService;
use Illuminate\Support\Facades\Log;

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
        $productType = ProductType::with('attributes')->find($productTypeId);

        // TODO handle the case when the product type is not found

        $goldenProduct = GoldenProduct::create([
            'product_type_id' => $productTypeId,
        ]);

        // Create translations with mapped attributes for each locale
        foreach (Locale::all() as $locale) {
            $translatedName = $this->translateProductData($sellerProduct->name, $locale->code);
            $translatedDescription = $this->translateProductData($sellerProduct->description, $locale->code);

            // Map and translate attributes if they exist
            $mappedAttributes = null;
            if ($sellerProduct->attributes) {
                $mappedAttributes = $this->mapAttributes($sellerProduct->attributes, $productType);
                if ($locale->code !== 'en') {
                    $mappedAttributes = json_decode($this->translateProductData(
                        json_encode($mappedAttributes),
                        $locale->code
                    ), true);
                }
            }

            $goldenProduct->translations()->create([
                'name' => $translatedName,
                'description' => $translatedDescription,
                'attributes' => $mappedAttributes,
                'locale_id' => $locale->id,
            ]);
        }

        $sellerProduct->update(['golden_product_id' => $goldenProduct->id]);

        return $goldenProduct;
    }

    private function mapAttributes(array $sellerAttributes, ProductType $productType): array
    {
        $client = $this->openAIService->getClient();

        // Get the schema of valid attributes from the product type
        $attributeSchema = $productType->attributes->map(function ($attr) {
            return [
                'name' => $attr->name,
                'type' => $attr->type,
                'description' => $attr->description,
                'unit' => $attr->unit,
                'options' => $attr->options,
                'is_variant_attribute' => $attr->is_variant_attribute,
            ];
        })->toArray();

        // Log input data for debugging
        Log::info('Mapping attributes', [
            'seller_attributes' => $sellerAttributes,
            'schema' => $attributeSchema
        ]);

        $response = $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "Map the seller's attributes to the product type schema, following these rules:
                    1. Match attributes based on semantic similarity and field type compatibility
                    2. Convert values to match the required type (text, boolean, number, select, url, color)
                    3. Ensure select values match the available options
                    4. Return a valid JSON object in the format: { schemaFieldName: convertedValue }"
                ],
                [
                    'role' => 'user',
                    'content' => "Please provide a JSON object mapping these seller attributes to the schema:
                    
                    Seller Attributes: " . json_encode($sellerAttributes) . "
                    
                    Schema: " . json_encode($attributeSchema)
                ]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.2
        ]);

        // Log the raw response for debugging
        Log::info('OpenAI Response', [
            'response' => $response
        ]);

        $content = $response->choices[0]->message->content ?? '{}';
        $mappedAttributes = json_decode($content, true);

        // Log the final mapped attributes
        Log::info('Mapped attributes', [
            'mapped' => $mappedAttributes
        ]);

        return $mappedAttributes;
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
        return $text; // TODO

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
