<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoardPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'title',
        'content',
        'author_name',
        'author_email',
        'password',
        'is_notice',
        'is_secret',
        'view_count',
        'like_count',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'is_notice' => 'boolean',
            'is_secret' => 'boolean',
            'view_count' => 'integer',
            'like_count' => 'integer',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function files(): BoardPost|HasMany
    {
        return $this->hasMany(BoardFile::class, 'post_id');
    }

    public function comments(): BoardPost|HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function parentComments(): BoardPost|HasMany
    {
        return $this->hasMany(Comment::class, 'post_id')->whereNull('parent_id');
    }

    public function likes(): BoardPost|HasMany
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }

    public function hasLiked(?User $user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function scopeNotice($query)
    {
        return $query->where('is_notice', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_notice', false);
    }

    public function scopePublic($query)
    {
        return $query->where('is_secret', false);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementLikeCount(): void
    {
        $this->increment('like_count');
    }
}
