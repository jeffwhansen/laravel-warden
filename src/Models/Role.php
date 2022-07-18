<?php

namespace Jeffwhansen\Warden\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Jeffwhansen\Warden\Contracts\Role as RoleContract;
use Jeffwhansen\Warden\Exceptions\GuardDoesNotMatch;
use Jeffwhansen\Warden\Exceptions\RoleAlreadyExists;
use Jeffwhansen\Warden\Exceptions\RoleDoesNotExist;
use Jeffwhansen\Warden\Guard;
use Jeffwhansen\Warden\Traits\HasAbilities;

class Role extends Model implements RoleContract
{
    use HasAbilities;


    protected $guarded = [];

    public function getRoleableAttribute()
    {

    }


    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->guarded[] = $this->primaryKey;
    }

    public function getTable()
    {
        return config('warden.table_names.roles', parent::getTable());
    }

    /**
     * A role may be given various abilitys.
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

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];

        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * Find or create role by its name (and optionally guardName).
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role|\Jeffwhansen\Warden\Models\Role
     */
    public static function findOrCreate(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $role;
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function assignees(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'assignee',
            config('warden.table_names.assigned_roles'),
            config('warden.column_names.role_pivot_key'),
            config('warden.column_names.assignee_morphs'.'_id')
        );
    }

    /**
     * A role belongs to some roleables of the model associated with its guard.
     */
    public function roleables(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'roleable',
            config('warden.table_names.assigned_roles'),
            config('warden.column_names.role_pivot_key'),
            config('warden.column_names.roleable_morphs'.'_id')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role|\Jeffwhansen\Warden\Models\Role
     *
     * @throws \Jeffwhansen\Warden\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    /**
     * Find a role by its id (and optionally guardName).
     *
     * @param  int  $id
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Role|\Jeffwhansen\Warden\Models\Role
     */
    public static function findById(int $id, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }

    protected static function findByParam(array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, trim($value));
        }

        return $query->first();
    }

    /**
     * Determine if the user may perform the given ability.
     *
     * @param  string|Ability  $ability
     * @return bool
     *
     * @throws \Jeffwhansen\Warden\Exceptions\GuardDoesNotMatch
     */
    public function hasAbilityTo($ability): bool
    {
        $abilityClass = $this->getAbilityClass();

        if (is_string($ability)) {
            $ability = $abilityClass->findByName($ability, $this->getDefaultGuardName());
        }

        if (is_int($ability)) {
            $ability = $abilityClass->findById($ability, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($ability->guard_name)) {
            throw GuardDoesNotMatch::create($ability->guard_name, $this->getGuardNames());
        }

        return $this->abilities->contains($ability->getKeyName(), $ability->getKey());
    }
}
