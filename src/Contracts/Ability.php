<?php

namespace Jeffwhansen\Warden\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Ability
{
    /**
     * A ty can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany;

    /**
     * Find a ty by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Jeffwhansen\Warden\Exceptions\AbilityDoesNotExist
     *
     * @return \Jeffwhansen\Warden\Contracts\Ability;
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a ty by its id.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Jeffwhansen\Warden\Exceptions\AbilityDoesNotExist
     *
     * @return \Jeffwhansen\Warden\Contracts\Ability;
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Find or Create a ty by its name and guard name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @return \Jeffwhansen\Warden\Contracts\Ability;
     */
    public static function findOrCreate(string $name, $guardName): self;
}
