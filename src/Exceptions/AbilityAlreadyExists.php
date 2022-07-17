<?php

namespace Jeffwhansen\Warden\Exceptions;

use InvalidArgumentException;

class AbilityAlreadyExists extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName)
    {
        return new static("A `{$permissionName}` ability already exists for guard `{$guardName}`.");
    }
}
