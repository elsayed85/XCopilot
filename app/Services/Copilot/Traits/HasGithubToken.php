<?php

namespace App\Services\Copilot\Traits;

use App\Models\GithubAccount;

trait HasGithubToken
{
    private GithubAccount $githubAccount;

    private function setGithub(GithubAccount $githubAccount): static
    {
        $this->githubAccount = $githubAccount;

        return $this;
    }

    private function getGithubToken(): ?string
    {
        return $this->githubAccount->getGithubToken();
    }

    private function getGithubAccount(): GithubAccount
    {
        return $this->githubAccount;
    }
}
