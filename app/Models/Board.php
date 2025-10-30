<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_code',
        'board_name',
        'board_type',
        'upload_folder',
        'use_notice',
        'use_file_upload',
        'max_file_count',
        'use_editor',
        'use_comment',
        'max_file_size',
        'allow_user_write',
        'memo',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'use_notice' => 'boolean',
            'use_file_upload' => 'boolean',
            'use_editor' => 'boolean',
            'use_comment' => 'boolean',
            'allow_user_write' => 'boolean',
            'is_active' => 'boolean',
            'max_file_count' => 'integer',
            'max_file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function posts(): Board|HasMany
    {
        return $this->hasMany(BoardPost::class);
    }

    public function activePosts(): Board|HasMany
    {
        return $this->hasMany(BoardPost::class)->orderBy('created_at', 'desc');
    }

    public function noticePosts(): Board|HasMany
    {
        return $this->hasMany(BoardPost::class)->where('is_notice', true)->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('board_name');
    }
}
