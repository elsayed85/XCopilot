<?php

namespace App\Services\Copilot\Core;

use Illuminate\Support\Facades\Http;

class WebSearch
{
    public array $results = [];

    public function fetch($query): static
    {
        $this->results = Http::get('https://ddg-api.herokuapp.com/search?query='.$query.'&limit=3')->json();

        return $this;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function asCopilotMessage(): ?Message
    {
        if (count($this->getResults()) === 0) {
            return null;
        }

        $snippets = '';

        foreach ($this->getResults() as $index => $result) {
            $snippet = '['.($index + 1).'] "'.$result['snippet'].'" URL:'.$result['link'].'.';
            $snippets .= $snippet;
        }

        $response = 'Here are some updated web searches. Use this to improve user response:';
        $response .= $snippets;

        return new Message($response, 'system');
    }
}
