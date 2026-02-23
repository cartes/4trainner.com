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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\UserMeta;
use App\Models\StudentProgress;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user');
    }

    /**
     * Get the students associated with the user (as a professor).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'professor_student', 'professor_id', 'student_id')
            ->withTimestamps();
    }


    /**
     * Get the progress records for the user (as a student).
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class, 'student_id');
    }

    /**
     * Get the routines created by this trainer/professor.
     */
    public function routines()
    {
        return $this->hasMany(Routine::class, 'trainer_id');
    }

    /**
     * Compatibility: Get the trainers assigned to this student (Legacy).
     */
    public function trainers()
    {
        return $this->belongsToMany(User::class, 'professor_student', 'student_id', 'professor_id')
            ->withTimestamps();
    }

    /**
     * Get the routines assigned to this student.
     */
    public function assignedRoutines()
    {
        return $this->belongsToMany(Routine::class, 'user_routine', 'user_id', 'routine_id')
            ->withPivot('assigned_by', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get the channels owned by the user (trainer).
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }
}
