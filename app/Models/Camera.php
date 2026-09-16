<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'stream_url',
        'max_width',
        'quality',
        'order',
    ];

    /**
     * The camera's IP address, parsed out of its stream URL.
     */
    public function getIpAddressAttribute(): ?string
    {
        return parse_url($this->stream_url, PHP_URL_HOST) ?: null;
    }
}
