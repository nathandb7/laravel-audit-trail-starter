<?php

return [
    'enabled' => env('AUDIT_TRAIL_ENABLED', true),

    'route_prefix' => 'audit-trail',

    'middleware' => ['web', 'auth'],

    'table_name' => 'audit_logs',

    'user_model' => App\Models\User::class,

    'track_ip' => true,

    'track_user_agent' => true,

    'track_url' => true,

    'track_auth_events' => true,

    'track_model_events' => true,

    'ignored_fields' => [
        'password',
        'remember_token',
        'updated_at',
    ],

    'ignored_models' => [
        //
    ],

    'modules' => [
        'auth',
        'users',
        'sales',
        'inventory',
        'finance',
        'settings',
    ],
];
