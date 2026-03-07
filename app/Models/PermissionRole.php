<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRole extends Pivot
{
    use SoftDeletes;

    protected $table = 'permission_role';

    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id',
        'created_at',
        'created_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
