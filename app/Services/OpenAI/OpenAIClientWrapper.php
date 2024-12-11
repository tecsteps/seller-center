<?php

namespace App\Services\OpenAI;

use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponse;

class OpenAIClientWrapper
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function chat(): OpenAIChatWrapper
    {
        return new OpenAIChatWrapper($this->client->chat());
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
