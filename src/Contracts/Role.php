<?php

namespace Jeffwhansen\Warden\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * A role may be given various permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role
     *
     * @throws \Jeffwhansen\Warden\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a role by its name and guard name.
     *
     * @param  int  $id
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role
     *
     * @throws \Jeffwhansen\Warden\Exceptions\RoleDoesNotExist
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Find or create a role by its name and guard name.
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role
     */
    public static function findOrCreate(string $name, $guardName): self;

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  string|\Jeffwhansen\Warden\Contracts\Ability  $ability
     * @return bool
     */
    public function hasAbilityTo($ability): bool;
}
