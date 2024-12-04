<?php

namespace App\Services;

use OpenAI;

class OpenAIService
{
    private $client;


    public function __construct()
    {
        $this->client = OpenAI::client(getenv('OPENAI_API_KEY'));
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getSmallModel()
    {
        return getenv('SMALL_MODEL');
    }
}
