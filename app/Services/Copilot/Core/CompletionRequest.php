<?php

namespace App\Services\Copilot\Core;

class CompletionRequest
{
    public bool $stream;

    public bool $intent;

    public string $model;

    public float $temperature;

    public int $top_p;

    private int $n;

    public function __construct(public array $messages)
    {
        $this->stream = config('github-copilot-chat.stream');
        $this->intent = config('github-copilot-chat.intent');
        $this->model = config('github-copilot-chat.model');
        $this->temperature = config('github-copilot-chat.temperature');
        $this->top_p = config('github-copilot-chat.top_p');
        $this->n = config('github-copilot-chat.n');
    }

    public function toArray(): array
    {
        return [
            'stream' => $this->stream,
            'intent' => $this->intent,
            'model' => $this->model,
            'temperature' => $this->temperature,
            'top_p' => $this->top_p,
            'n' => $this->n,
            'messages' => $this->messages,
        ];
    }
}
