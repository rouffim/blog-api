<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role';

    /**
     * Get all permissions for this role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permission');
    }

    public function permissionsArray(): array {
        return $this->permissions->map(function ($permission) {
            return $permission->name;
        })->toArray();
    }
}
