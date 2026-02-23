<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'title',
        'description',
        'status',
        'file_path',
        'thumbnail_path',
        'duration',
    ];

    /**
     * Get the channel that owns the video.
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
