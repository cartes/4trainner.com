<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'cover_image',
        'stream_key',
        'is_live',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($channel) {
            if (empty($channel->slug)) {
                $channel->slug = Str::slug($channel->name) . '-' . uniqid();
            }
            if (empty($channel->stream_key)) {
                // Generate a random stream key: "live_xxxxxxxxxxxxxxxxxxxxxxxx"
                $channel->stream_key = 'live_' . Str::random(24);
            }
        });
    }

    /**
     * Get the user that owns the channel.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the videos for the channel.
     */
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
