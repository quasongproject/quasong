<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    protected $fillable = ['name'];

    /**
     * Relasi ke musics
     */
    public function musics(): HasMany
    {
        return $this->hasMany(Music::class);
    }
}
