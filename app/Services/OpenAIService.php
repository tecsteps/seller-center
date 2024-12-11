<?php

namespace App\Services;

use App\Services\OpenAI\OpenAIClientWrapper;
use OpenAI;

class OpenAIService
{
    private OpenAIClientWrapper $client;

    public function __construct()
    {
        $this->client = new OpenAIClientWrapper(OpenAI::client(getenv('OPENAI_API_KEY')));
    }

    public function getClient(): OpenAIClientWrapper
    {
        return $this->client;
    }

    public function getSmallModel(): string
    {
        return getenv('SMALL_MODEL');
    }
}
