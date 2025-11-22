<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Music extends Model
{
    protected $table = 'musics';
    protected $fillable = [
        'name','artist_id','thumbnail_url','music_url','source','lyrics',
    ];

    /**
     * Relasi ke artist
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Relasi ke likes
     */
    public function likes(): HasMany
    {
        return $this->hasMany(MusicLike::class);
    }

    /**
     * Cek apakah user tertentu sudah like lagu ini
     */
    public function isLikedBy(?int $userId): bool
    {
        if (!$userId) return false;

        return $this->likes()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Total likes untuk lagu ini
     */
    public function likesCount(): int
    {
        return $this->likes()->count();
    }
}
