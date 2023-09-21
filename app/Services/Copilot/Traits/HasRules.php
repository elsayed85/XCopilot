<?php

namespace App\Services\Copilot\Traits;

trait HasRules
{
    use HasMessage;

    private array $rules;

    private function setRules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    private function getRulesString(): string
    {
        $rules = '';

        foreach ($this->rules as $rule) {
            $rules .= $rule."\n";
        }

        return $rules;
    }

    private function prepareRules(): void
    {
        $this->setRules(config('github-copilot-chat.rules'));

        $this->addMessage($this->getRulesString(), 'system');
    }
}
