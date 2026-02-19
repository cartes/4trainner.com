<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Importar el trait HasRoles
use App\Models\UserMeta;
use App\Models\StudentProgress;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * User Meta data relationship
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * Get the students associated with the user (as a professor).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professor_student', 'professor_id', 'student_id');
    }

    /**
     * Get the professors associated with the user (as a student).
     */
    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professor_student', 'student_id', 'professor_id');
    }

    /**
     * Get the progress records for the user (as a student).
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }
}
