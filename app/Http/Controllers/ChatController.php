<?php

namespace App\Http\Controllers;

use App\Services\Copilot\CopilotApi;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            Authenticate::class,
        ]);
    }

    public function index($chat = null, $conversation = null)
    {
        $githubAccounts = auth()->user()->githubAccounts()->get();
        $sharedGithubAccounts = auth()->user()->sharedGithubAccounts()->get()
            ->map(function ($acc) {
                $acc['account_name'] = 'Shared : '.$acc['account_name'];
                $acc['shared'] = 1;

                return $acc;
            });

        $allAccounts = $githubAccounts->merge($sharedGithubAccounts)->unique();

        return view('chat.index', [
            'chat' => $chat,
            'conversation' => $conversation,
            'github_accounts' => $allAccounts,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function conversation(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $isShared = $request->input('shared') ?? false;

        if ($isShared) {
            $githubAccount = auth()->user()->sharedGithubAccounts()->where('github_accounts.id', request('github_account_id'))->first();
        } else {
            $githubAccount = auth()->user()->githubAccounts()->where('id', request('github_account_id'))->first();
        }

        if (! $githubAccount) {
            return 'Not Authorized';
        }

        $message = request()->input('meta.content.parts')[0];
        $conversation = request()->input('meta.content.conversation') ?? [];

        $conversation[] = $message;

        try {
            $copilot = (new CopilotApi($githubAccount))->setMessages($conversation);

            return $copilot->streamResponse();
        } catch (\Exception $e) {
            return 'Error: '.$e->getMessage();
        }
    }
}
