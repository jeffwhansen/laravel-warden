<?php

namespace Jeffwhansen\Warden;

class WardenRegistrar
{
    /** @var string */
    protected $abilityClass;

    /** @var string */
    protected $roleClass;

    /** @var string */
    protected $pivotRole;

    /** @var string */
    protected $pivotAbility;

    /** @var \Illuminate\Database\Eloquent\Collection */
    protected $roles;

    /** @var \Illuminate\Database\Eloquent\Collection */
    protected $abilities;

    /**
     * WardenRegistrar constructor.
     */
    public function __construct()
    {
        $this->abilityClass = config('warden.models.ability');
        $this->roleClass = config('warden.models.role');
        $this->pivotRole = config('warden.column_names.role_pivot_key');
        $this->pivotAbility = config('warden.column_names.ability_pivot_key');
    }

    public function getAbilityClass()
    {
        return app($this->abilityClass);
    }
}
