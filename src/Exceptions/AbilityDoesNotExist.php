<?php

namespace Jeffwhansen\Warden\Exceptions;

use InvalidArgumentException;

class AbilityDoesNotExist extends InvalidArgumentException
{
    public static function create(string $abilityName, string $guardName = '')
    {
        return new static("There is no ability named `{$abilityName}` for guard `{$guardName}`.");
    }

    public static function withId(int $abilityId, string $guardName = '')
    {
        return new static("There is no [ability] with id `{$abilityId}` for guard `{$guardName}`.");
    }
}
