<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'muscle_group', 'video_url'];

    /**
     * The routines that contain the exercise.
     */
    public function routines()
    {
        return $this->belongsToMany(Routine::class)
            ->withPivot('sets', 'reps', 'rest_seconds', 'notes')
            ->withTimestamps();
    }
}
