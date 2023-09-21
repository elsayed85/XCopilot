<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class GithubController extends Controller
{
    public function accounts()
    {
        $accounts = auth()->user()->githubAccounts()->select(['id', 'account_name'])->get();

        return response()->json($accounts);
    }

    public function sharedAccounts()
    {
        $sharedAccounts = auth()->user()->sharedGithubAccounts()
            ->select(['github_accounts.id', 'account_name'])
            ->get();

        return response()->json($sharedAccounts);
    }
}
