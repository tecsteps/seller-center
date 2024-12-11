<?php

namespace App\Services;

use App\Models\ProductType;
use App\Models\SellerProduct;
use App\Services\OpenAIService;

class ProductTypeClassificationService
{
    public function __construct(
        private readonly OpenAIService $openAIService,
    ) {}

    public function determineProductType(SellerProduct $sellerProduct): ProductType
    {
        $availableTypes = ProductType::pluck('name')->toArray();
        $productTypeName = $this->classifyProductType($sellerProduct->name, $availableTypes);

        if (is_null($productTypeName)) {
            throw new \Exception('Product type not found');
        }

        $productType = ProductType::where('name', $productTypeName)->first();

        // TODO handling in case no product type is found

        return $productType;
    }

    private function classifyProductType(string $productName, array $availableTypes): ?string
    {
        $client = $this->openAIService->getClient();

        $response = $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a product classifier. Given a product name, classify it into one of the available product types. If none match, respond with "n/a".'
                ],
                [
                    'role' => 'user',
                    'content' => "Product name: $productName\nAvailable types: " . implode(', ', $availableTypes)
                ]
            ],
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'classify_product',
                        'description' => 'Classify a product into one of the available product types',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'product_type' => [
                                    'type' => 'string',
                                    'enum' => array_merge($availableTypes, ['n/a']),
                                    'description' => 'The type of product or "n/a" if no type matches'
                                ]
                            ],
                            'required' => ['product_type']
                        ]
                    ]
                ]
            ],
            'tool_choice' => ['type' => 'function', 'function' => ['name' => 'classify_product']]
        ]);

        $toolCall = $response->choices[0]->message->toolCalls[0];
        $arguments = json_decode($toolCall->function->arguments, true);

        $productType = $arguments['product_type'] ?? null;
        if ($productType == 'n/a') {
            return null;
        }
        return $productType;
    }
}
