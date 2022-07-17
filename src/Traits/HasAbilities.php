<?php

namespace Jeffwhansen\Warden\Traits;

use Illuminate\Support\Collection;
use Jeffwhansen\Warden\Exceptions\GuardDoesNotMatch;
use Jeffwhansen\Warden\Guard;
use Jeffwhansen\Warden\Models\Ability;
use Jeffwhansen\Warden\WardenRegistrar;

trait HasAbilities
{
    public function getAbilityClass()
    {
        if (! isset($this->abilityClass)) {
            $this->abilityClass = app(WardenRegistrar::class)->getAbilityClass();
        }

        return $this->abilityClass;
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
     * Grant the given ability(s) to a role.
     *
     * @param string|int|array|\Jeffwhansen\Warden\Contracts\Ability|\Illuminate\Support\Collection $abilities
     *
     * @return $this
     */
    public function giveAbilityTo(...$abilities)
    {
        $abilities = collect($abilities)
            ->flatten()
            ->reduce(function ($array, $ability) {
                if (empty($ability)) {
                    return $array;
                }

                $ability = $this->getStoredAbility($ability);
                if (! $ability instanceof Ability) {
                    return $array;
                }

                $this->ensureModelSharesGuard($ability);

                $array[$ability->getKey()] = [];

                return $array;
            }, []);

        $model = $this->getModel();

        if ($model->exists) {
            $this->abilities()->sync($abilities, false);
            $model->load('abilities');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($abilities, $model) {
                    if ($model->getKey() != $object->getKey()) {
                        return;
                    }
                    $model->abilities()->sync($abilities, false);
                    $model->load('abilities');
                }
            );
        }

        return $this;
    }


    /**
     * @param string|int|array|\Jeffwhansen\Warden\Contracts\Ability|\Illuminate\Support\Collection $abilities
     *
     * @return \Jeffwhansen\Warden\Contracts\Ability|\Jeffwhansen\Warden\Contracts\Ability[]|\Illuminate\Support\Collection
     */
    protected function getStoredAbility($abilities)
    {
        $abilityClass = $this->getAbilityClass();

        if (is_numeric($abilities)) {
            return $abilityClass->findById($abilities, $this->getDefaultGuardName());
        }

        if (is_string($abilities)) {
            return $abilityClass->findByName($abilities, $this->getDefaultGuardName());
        }

        if (is_array($abilities)) {
            $abilities = array_map(function ($ability) use ($abilityClass) {
                return is_a($ability, get_class($abilityClass)) ? $ability->name : $ability;
            }, $abilities);

            return $abilityClass
                ->whereIn('name', $abilities)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $abilities;
    }

    /**
     * @param \Spatie\Permission\Contracts\Ability|\Spatie\Permission\Contracts\Role $roleOrPermission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    protected function ensureModelSharesGuard($roleOrPermission)
    {
        if (! $this->getGuardNames()->contains($roleOrPermission->guard_name)) {
            throw GuardDoesNotMatch::create($roleOrPermission->guard_name, $this->getGuardNames());
        }
    }
}
