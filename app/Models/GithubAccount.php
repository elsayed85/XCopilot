<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GithubAccount extends Model
{
    use softDeletes;

    protected $guarded = [];

    protected $casts = [
        'copilot_token_expires_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'copilot_token',
        'github_token',
        'pivot',
    ];

    public function getGithubToken(): ?string
    {
        return $this->github_token;
    }

    public function getCopilotToken(): ?string
    {
        return $this->copilot_token;
    }

    public function getCopilotTokenExpiresAt()
    {
        return $this->copilot_token_expires_at;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, (new GithubAccountMember())->getTable())->withPivot('id')->withTimestamps();
    }

    public function invites(): HasMany
    {
        return $this->hasMany(AccountInvite::class, 'account_id');
    }

    public function isOwner(User $user): bool
    {
        return $this->owner->is($user);
    }

    public function isMember(User $user): bool
    {
        return $this->members->contains($user);
    }
}
