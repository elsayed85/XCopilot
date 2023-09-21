<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountInvite extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
        'max_usages' => 'integer',
        'usages' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($invite) {
            $invite->token = str_random(32);
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(GithubAccount::class, 'account_id');
    }

    public function isExpired()
    {
        if (is_null($this->expires_at)) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function isMaxedOut()
    {
        if (is_null($this->max_usages)) {
            return false;
        }

        return $this->usages >= $this->max_usages;
    }

    public function redeem(): self
    {
        $this->account->members()->attach(auth()->user());

        $this->increment('usages');

        //        if ($this->isMaxedOut()) {
        //            $this->delete();
        //        }

        return $this;
    }
}
