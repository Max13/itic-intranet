<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'ypareo_id',
        'name',
        'shortname',
        'fullname',
    ];

    /** @inheritdoc */
    protected $casts = [
        'ypareo_id' => 'integer',
    ];

    /**
     * Retrieve classroom's training
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function training() : BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
