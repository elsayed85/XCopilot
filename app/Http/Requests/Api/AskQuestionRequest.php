<?php

namespace App\Http\Requests\Api;

use App\Models\GithubAccount;
use Illuminate\Foundation\Http\FormRequest;

class AskQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string'],
            'github_account_id' => ['required', 'exists:github_accounts,id'],
            'web_search' => ['nullable', 'boolean'],
            'stream' => ['nullable', 'boolean'],
        ];
    }

    public function getGithubAccount(): GithubAccount
    {
        return GithubAccount::find($this->input('github_account_id'));
    }

    public function authorizeGithubAccount(): bool
    {
        return $this->getGithubAccount()->isOwner(auth()->user()) || $this->getGithubAccount()->isMember(auth()->user());
    }

    public function getQuestion(): string
    {
        return $this->input('question');
    }

    public function shouldUseWebSearch(): bool
    {
        return $this->input('web_search', false);
    }

    public function shouldStream(): bool
    {
        return $this->input('stream', false);
    }
}
