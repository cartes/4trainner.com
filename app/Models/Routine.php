<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'difficulty', 'trainer_id'];

    /**
     * Get the trainer that created the routine.
     */
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * The exercises that belong to the routine.
     */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'routine_exercise')
            ->withPivot('sets', 'reps', 'rest_seconds', 'notes')
            ->withTimestamps();
    }

    /**
     * The students assigned to this routine.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_routine')
            ->withPivot('assigned_by', 'is_active')
            ->withTimestamps();
    }
}
