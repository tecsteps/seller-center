<?php

namespace App\Services\OpenAI;

use OpenAI\Resources\Chat;
use OpenAI\Responses\Chat\CreateResponse;
use Illuminate\Support\Facades\Log;

class OpenAIChatWrapper
{
    private Chat $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function create(array $parameters): CreateResponse
    {

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callingClass = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : 'unknown';
        $callingClass = last(explode('\\', $callingClass));
        $callingMethod = debug_backtrace()[1]['function'] ?? 'unknown';

        $suffix = ' - ' . $callingClass . '::' . $callingMethod;

        // if (isset($parameters['tools'][0]['function']['name'])) {
        //     $suffix = ' - ' . $parameters['tools'][0]['function']['name'];
        // } elseif (isset($parameters['tool_choice']['function']['name'])) {
        //     $suffix = ' - ' . $parameters['tool_choice']['function']['name'];
        // }
        Log::info('OpenAI Chat Request' . $suffix, [
            'parameters' => $parameters,
        ]);

        $response = $this->chat->create($parameters);

        Log::info('OpenAI Chat Response' . $suffix, [
            'response' => [
                'id' => $response->id,
                'model' => $response->model,
                'created' => $response->created,
                'choices' => array_map(fn($choice) => [
                    'index' => $choice->index,
                    'message' => [
                        'role' => $choice->message->role,
                        'content' => $choice->message->content,
                        'toolCalls' => $choice->message->toolCalls,
                    ],
                    'finishReason' => $choice->finishReason,
                ], $response->choices),
                'usage' => [
                    'promptTokens' => $response->usage->promptTokens,
                    'completionTokens' => $response->usage->completionTokens,
                    'totalTokens' => $response->usage->totalTokens,
                ],
            ],
        ]);

        return $response;
    }
}
