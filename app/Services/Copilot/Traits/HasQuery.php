<?php

namespace App\Services\Copilot\Traits;

use App\Services\Copilot\Core\CompletionRequest;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;

trait HasQuery
{
    use HasToken;

    /**
     * @throws \Exception
     */
    private function query(CompletionRequest $completionRequest, $stream = true): ResponseInterface|string
    {
        $response = Http::withHeaders(['User-Agent' => config('github-copilot-chat.user_agent')])
            ->asJson()
            ->withToken($this->getToken())
            ->post('https://copilot-proxy.githubusercontent.com/v1/chat/completions', $completionRequest->toArray());

        if ($response->status() != 200) {
            throw new \Exception("Could not query copilot: {$response->body()}");
        }

        if ($stream) {
            return $response->toPsrResponse();
        }

        return $response->body();
    }
}
