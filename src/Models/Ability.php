<?php

namespace Jeffwhansen\Warden\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Jeffwhansen\Warden\Contracts\Ability as AbilityContract;
use Jeffwhansen\Warden\Exceptions\AbilityAlreadyExists;
use Jeffwhansen\Warden\Exceptions\AbilityDoesNotExist;
use Jeffwhansen\Warden\Guard;
use Jeffwhansen\Warden\Traits\HasRoles;

class Ability extends Model implements AbilityContract
{
    use HasRoles;

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');
        parent::__construct($attributes);
        $this->guarded[] = $this->primaryKey;
    }

    public function getTable()
    {
        return config('warden.table_names.abilities', parent::getTable());
    }

    /**
     * A role may be given various abilitys.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('warden.models.role'),
            config('warden.table_names.ability_role'),
            config('warden.column_name.ability_pivot_key'),
            config('warden.column_name.role_pivot_key'),
        );
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);
        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];

        if (static::findByParam($params)) {
            throw AbilityAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * Find or create ability by its name (and optionally guardName).
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Ability|\Jeffwhansen\Warden\Models\Ability
     */
    public static function findOrCreate(string $name, $guardName = null): AbilityContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $ability = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $ability) {
            return static::query()->create(['name' => $name, 'guard_name' => $guardName]);
        }

        return $ability;
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param  string  $name
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Ability|\Jeffwhansen\Warden\Models\Ability
     *
     * @throws \Jeffwhansen\Warden\Exceptions\AbilityDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): AbilityContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $ability = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $ability) {
            throw AbilityDoesNotExist::named($name);
        }

        return $ability;
    }

    /**
     * Find a ability by its id (and optionally guardName).
     *
     * @param  int  $id
     * @param  string|null  $guardName
     * @return \Jeffwhansen\Warden\Contracts\Ability|\Jeffwhansen\Warden\Models\Ability
     */
    public static function findById(int $id, $guardName = null): AbilityContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $ability = static::findByParam([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (! $ability) {
            throw AbilityDoesNotExist::withId($id);
        }

        return $ability;
    }

    protected static function findByParam(array $params = [])
    {
        $query = static::query();

        foreach ($params as $key => $value) {
            $query->where($key, trim($value));
        }

        return $query->first();
    }
}
