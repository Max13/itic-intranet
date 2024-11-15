<?php

namespace App\Models;

use App\Models\Marking\Criterion;
use App\Models\Marking\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_staff',
        'is_student',
        'is_trainer',
        'ypareo_id',
        'ypareo_login',
        'firstname',
        'lastname',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_staff' => 'boolean',
        'is_student' => 'boolean',
        'is_trainer' => 'boolean',
        'ypareo_id' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Query user's criteria
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markingCriteria() : HasMany
    {
        return $this->hasMany(Criterion::class);
    }

    /**
     * Query user's points
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function points() : HasMany
    {
        return $this->hasMany(Point::class, 'student_id');
    }
}
