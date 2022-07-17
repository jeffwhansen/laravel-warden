<?php

namespace Jeffwhansen\Warden\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Jeffwhansen\Warden\Exceptions\GuardDoesNotMatch;
use Jeffwhansen\Warden\Guard;
use Jeffwhansen\Warden\Models\Role;
use Jeffwhansen\Warden\WardenRegistrar;

trait HasRoles
{

    use HasAbilities;

    public function getRoleClass()
    {
        if (! isset($this->roleClass)) {
            $this->roleClass = app(WardenRegistrar::class)->getRoleClass();
        }

        return $this->roleClass;
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->morphToMany(
            config('warden.models.role'),
            'assignee',
            config('warden.table_names.assigned_roles'),
            config('warden.column_names.assignee_morphs'.'_id'),
            config('warden.column_names.role_pivot_key'),
        );
    }

    protected function getGuardNames(): Collection
    {
        return Guard::getNames($this);
    }

    protected function getDefaultGuardName(): string
    {
        return Guard::getDefaultName($this);
    }


    /**
     * Assign the given role to the model.
     *
     * @param array|string|int|\Jeffwhansen\Warden\Contracts\Role|\Illuminate\Support\Collection ...$roles
     *
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->reduce(function ($array, $role) {
                if (empty($role)) {
                    return $array;
                }

                $role = $this->getStoredRole($role);
                $this->ensureModelSharesGuard($role);
                $array[$role->getKey()] = [];
                return $array;
            }, []);

        $model = $this->getModel();

        if ($model->exists) {
            $this->roles()->sync($roles, false);
            $model->load('roles');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($roles, $model) {
                    if ($model->getKey() != $object->getKey()) {
                        return;
                    }
                    $model->roles()->sync($roles, false);
                    $model->load('roles');
                }
            );
        }
        return $this;
    }


    protected function getStoredRole($role): Role
    {
        $roleClass = $this->getRoleClass();

        if (is_numeric($role)) {
            return $roleClass->findById($role, $this->getDefaultGuardName());
        }

        if (is_string($role)) {
            return $roleClass->findByName($role, $this->getDefaultGuardName());
        }

        return $role;
    }


    /**
     * @param \Jeffwhansen\Warden\Contracts\Ability|\Jeffwhansen\Warden\Contracts\Role $roleOrPermission
     *
     * @throws \Jeffwhansen\Warden\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }

}
