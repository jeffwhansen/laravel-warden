<?php

namespace Jeffwhansen\Warden\Traits;

use Illuminate\Support\Collection;
use Jeffwhansen\Warden\Guard;
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
}
