<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Path (on the 'local' disk) of this camera's latest captured snapshot.
     */
    public function snapshotPath(): string
    {
        return "camera-snapshots/{$this->id}.jpg";
    }

    /**
     * When this camera's snapshot was last captured, or null if none yet.
     */
    public function getSnapshotUpdatedAtAttribute(): ?Carbon
    {
        $path = $this->snapshotPath();

        return Storage::disk('local')->exists($path)
            ? Carbon::createFromTimestamp(Storage::disk('local')->lastModified($path))
            : null;
    }

    /**
     * Whether a snapshot has been captured for this camera yet.
     */
    public function getHasSnapshotAttribute(): bool
    {
        return $this->snapshot_updated_at !== null;
    }
}
