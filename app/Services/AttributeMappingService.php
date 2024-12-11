<?php

namespace App\Services;

use App\Models\ProductType;
use App\Services\OpenAIService;
use Illuminate\Support\Collection;

class AttributeMappingService
{
    public function __construct(
        private readonly OpenAIService $openAIService,
    ) {}

    public function mapAttributes(array $sellerAttributes, ProductType $productType): array
    {
        // TODO Handle case when the attribute or options don't match. Currently we are loosing information. Add a misc attribute for unmapped attributes

        $attributeSchemaString = $this->stringifyProductType($productType);
        $attributeString = $this->stringifySellerAttributes($sellerAttributes);
        $client = $this->openAIService->getClient();
        $response = $this->performAIRequest($client, $attributeString, $attributeSchemaString);
        $mappedAttributes = json_decode($response->choices[0]->message->content, true);
        return $mappedAttributes ?? [];
    }

    /**
     * @param array $sellerAttributes
     * @return string
     */
    public function stringifySellerAttributes(array $sellerAttributes): string
    {
        $attributeString = '';
        foreach ($sellerAttributes as $k => $v) {
            $attributeString .= ' - ' . $k . ': ' . $v . "\n";
        }
        return $attributeString;
    }

    /**
     * @param ProductType $productType
     * @return string
     */
    public function stringifyProductType(ProductType $productType): string
    {
        $attributeSchemaString = '';
        foreach ($productType->attributes as $attribute) {
            $attributeSchemaString .= collect([
                'ID: ' . $attribute->id,
                'Attribute: ' . $attribute->name,
                'Type: ' . $attribute->type,
                'Description: ' . $attribute->description,
                !empty($attribute->unit)
                    ? 'Unit: ' . $attribute->unit
                    : null,
                !empty($attribute->options)
                    ? 'Valid options (You must select an ID): [' . collect($attribute->options)->map(fn($option) => "{$option->id}: \"{$option->label}\"")->join(', ') . ']'
                    : null,
            ])
                ->filter()
                ->join(' | ') . "\n";
        }
        return $attributeSchemaString;
    }

    public function performAIRequest(OpenAI\OpenAIClientWrapper $client, string $attributeString, string $attributeSchemaString): \OpenAI\Responses\Chat\CreateResponse
    {
        $response = $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a product attribute mapper. Map the seller's product attributes to our predefined schema following these rules:
1. Match attributes based on semantic similarity
2. Convert values to match the required type and unit
3. For attributes with predefined options, use only valid values
4. Return the result as a JSON object where keys are our attribute IDs and values are the mapped values
5. Skip attributes that don't have a good match in our schema

Response Format:
{
    \"attribute_id\": value, // where attribute_id is a number and value depends on the attribute type:
    // For 'select' type: value must be one of the provided option IDs (number)
    // For 'text' type: value must be a string
    // For 'number' type: value must be a number matching the specified unit
    // For 'boolean' type: value must be true or false
    // For 'color' type: value must be a valid hex color code (e.g., \"#FF0000\")
}

Example Response:
{
    \"11\": 42,        // select type with option ID 42
    \"22\": \"text\",  // text type
    \"33\": 180,       // number type
    \"44\": true,      // boolean type
    \"55\": \"#FF0000\" // color type
}"
                ],
                [
                    'role' => 'user',
                    'content' => "Map these seller attributes to our schema:

Seller's attributes:
$attributeString

Our attribute schema:
$attributeSchemaString

Respond with a valid JSON object where keys are our attribute IDs and values are the mapped values. You must not write any additional text or add quotes or backticks."
                ]
            ],
            'response_format' => ['type' => 'json_object']
        ]);
        return $response;
    }
}
