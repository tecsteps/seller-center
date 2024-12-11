<?php

namespace App\Services;

use App\Services\OpenAIService;
use App\Services\OpenAI\OpenAIClientWrapper;

class ProductTranslationService
{
    public function __construct(
        private readonly OpenAIService $openAIService,
    ) {}

    public function translateTexts(array $texts, string $targetLocale): array
    {
        if (array_keys($texts) === range(0, count($texts) - 1)) {
            return $this->translateList($texts, $targetLocale);
        } else {
            return $this->translateAssociative($texts, $targetLocale);
        }
    }

    public function translateString(string $text, string $targetLocale): string
    {
        $translated = $this->translateList([$text], $targetLocale);
        return $translated[0];
    }

    public function translateList(array $texts, string $targetLocale): array
    {
        $client = $this->openAIService->getClient();
        $response = $this->performListTranslation($client, $texts, $targetLocale);
        $translations = json_decode($response->choices[0]->message->content, true);
        return $translations['translated'] ?? $texts;
    }

    public function translateAssociative(array $texts, string $targetLocale): array
    {
        $client = $this->openAIService->getClient();
        $response = $this->performAssociativeTranslation($client, $texts, $targetLocale);
        $translations = json_decode($response->choices[0]->message->content, true);
        return $translations['translated'] ?? $texts;
    }

    private function performListTranslation(OpenAIClientWrapper $client, array $texts, string $targetLocale): \OpenAI\Responses\Chat\CreateResponse
    {
        return $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a product text translator. Your task is to translate the given array to the specified language.
1. Maintain the marketing style and tone of the original text
2. Return a JSON object with a single field 'translated' containing a numeric array of translated values
3. Keep brand names and trademark symbols (®, ™) unchanged

Example response format:
{
    \"translated\": [
        \"translated text 1\",
        \"translated text 2\"
    ]
}"
                ],
                [
                    'role' => 'user',
                    'content' => "Translate these texts to {$targetLocale}:\n\n" . json_encode($texts)
                ]
            ],
            'response_format' => ['type' => 'json_object']
        ]);
    }

    private function performAssociativeTranslation(OpenAIClientWrapper $client, array $texts, string $targetLocale): \OpenAI\Responses\Chat\CreateResponse
    {
        return $client->chat()->create([
            'model' => $this->openAIService->getSmallModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a product text translator. Your task is to translate values in the given object.
1. Maintain the marketing style and tone of the original text
2. Return a JSON object with a single field 'translated' containing an object with the same keys but translated values
3. Keep brand names and trademark symbols (®, ™) unchanged

Example response format:
{
    \"translated\": {
        \"key1\": \"translated value 1\",
        \"key2\": \"translated value 2\"
    }
}"
                ],
                [
                    'role' => 'user',
                    'content' => "Translate these texts to {$targetLocale}:\n\n" . json_encode($texts, JSON_PRETTY_PRINT)
                ]
            ],
            'response_format' => ['type' => 'json_object']
        ]);
    }
}
