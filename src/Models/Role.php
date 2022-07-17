<?php

namespace Jeffwhansen\Warden\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Jeffwhansen\Warden\Contracts\Role as RoleContract;

class Role extends Model implements RoleContract
{
    /**
     * A role may be given various permissions.
     */
    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(
            config('warden.models.ability'),
            config('warden.table_names.ability_role'),
            config('warden.column_name.role_pivot_key'),
            config('warden.column_name.ability_pivot_key'),
        );
    }
}
