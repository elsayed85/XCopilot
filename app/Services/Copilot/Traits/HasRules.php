<?php

namespace App\Services\Copilot\Traits;

use App\Models\Prompt;

trait HasRules
{
    use HasMessage;

    private Prompt $prompt;

    public function setPrompt(Prompt $prompt): self
    {
        $this->prompt = $prompt;

        $rules = $prompt->getAllRulesAsString();

        if (! empty($rules)) {
            $this->addMessage($rules, 'system');
        }

        return $this;
    }

    public function setPromptWhen(bool $when, Prompt $prompt = null): self
    {
        if ($when) {
            $this->setPrompt($prompt);
        }

        return $this;
    }

    public function setPromptFromArray(array $rules): self
    {
        $rulesAsString = collect($rules)->map(function ($rule) {
            return $rule;
        })->implode("\n");

        if (! empty($rulesAsString)) {
            $this->addMessage($rulesAsString, 'system');
        }

        return $this;
    }
}
