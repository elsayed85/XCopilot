<?php

namespace App\Services\Copilot\Traits;

use App\Services\Copilot\Core\Response\CreateStreamedResponse;
use App\Services\Copilot\Core\Response\StreamResponse;
use Psr\Http\Message\ResponseInterface;

trait HasStream
{
    private string $responseClass = CreateStreamedResponse::class;

    /**
     * @throws \JsonException
     * @throws \ErrorException
     */
    private function stream(ResponseInterface $response): \Generator
    {
        return (new StreamResponse($this->responseClass, $response))->getIterator();
    }
}
