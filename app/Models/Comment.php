<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'parent_id',
        'content',
        'author_name',
        'author_email',
        'password',
        'is_secret',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_secret' => 'boolean',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(BoardPost::class, 'post_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany|Comment
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_secret', false);
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function canView(?User $user, BoardPost $post): bool
    {
        if (!$this->is_secret) {
            return true;
        }
        if (!$user) {
            return false;
        }
        // Comment author check (match by email preferred, fallback to name)
        if ($this->author_email && $user->email && strcasecmp($this->author_email, $user->email) === 0) {
            return true;
        }
        if ($this->author_name && $user->name && $this->author_name === $user->name) {
            return true;
        }
        // Post author check
        if ($post->author_email && $user->email && strcasecmp($post->author_email, $user->email) === 0) {
            return true;
        }
        if ($post->author_name && $user->name && $post->author_name === $user->name) {
            return true;
        }
        return false;
    }
}
