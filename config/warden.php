<?php

// config for Jeffwhansen/Warden
return [
    'models' => [
        'ability' => \Jeffwhansen\Warden\Models\Ability::class,
        'role' => \Jeffwhansen\Warden\Models\Role::class,
    ],

    'table_names' => [

        /*
         * Storage for the roles of our application
         */
        'roles' => 'roles',

        /*
         * Storage for the abilities that will be available in our application
         * They will be assigned to the roles via the ability_role table
         */
        'abilities' => 'abilities',

        /*
         * Storage for the assignment of roles to abilities
         */
        'ability_role' => 'ability_role',

        /*
         * Storage for the assignment of abilities to models
         */
        'ability_model' => 'ability_model',

        /*
         * Storage for the assignment of the roles to the targets (Users)
         */
        'assigned_roles' => 'assigned_roles',

    ],

    'column_names' => [
        'role_pivot_key' => 'role_id',
        'ability_pivot_key' => 'ability_id',
        'assignee_morphs' => 'assignee',
        'roleable_morphs' => 'roleable',
    ],
];
