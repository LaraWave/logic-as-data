<?php

return [

    'analytics' => [
        /*
         * Enable or disable execution tracing. 
         * Set to false in high-throughput environments to save database space.
         */
        'enabled' => env('LOGIC_AS_DATA_ANALYTICS_ENABLED', true),
    ],

    'route' => [
        /*
        |--------------------------------------------------------------------------
        | The URL prefix for the admin interface.
        |--------------------------------------------------------------------------
        |
        | Default: logic-as-data
        |
        */
        'prefix' => env('LOGIC_AS_DATA_PREFIX', 'logic-as-data'),

        /**
         * 
         */
        /*
        |--------------------------------------------------------------------------
        | The middleware applied to the Logic as Data routes.
        |--------------------------------------------------------------------------
        |
        | Default: web, auth
        |
        */
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Name
    |--------------------------------------------------------------------------
    |
    | The name of the table that will be created by the migration and used
    | by the LogicRule model to store your JSON conditions and actions.
    | Change this if "logic_rules" conflicts with an existing app table.
    |
    */
    'tables' => [
        /*
         * The table names used by the package. Change these if they conflict
         * with existing tables in your database.
         */
        'rules'  => env('LOGIC_AS_DATA_RULES_TABLE', 'logic_rules'),
        'telemetry' => env('LOGIC_AS_DATA_TELEMETRY_TABLE', 'logic_telemetry'),
        'traces' => env('LOGIC_AS_DATA_TRACES_TABLE', 'logic_traces'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rule Caching (Performance)
    |--------------------------------------------------------------------------
    |
    | Since rules are evaluated frequently, querying the database for every 
    | hook can slow down the application. It is highly recommended to keep 
    | caching enabled. The package will automatically clear this cache 
    | whenever a LogicRule is created, updated, or deleted.
    |
    */

    'cache' => [
        'enabled' => env('LOGIC_AS_DATA_CACHE_ENABLED', true),

        // The cache key prefix used by the package
        'key' => env('LOGIC_AS_DATA_CACHE_KEY', 'logic_as_data_rules'),

        // Time-to-live in seconds (Default: 24 hours)
        'ttl' => env('LOGIC_AS_DATA_CACHE_TTL', 86400), 
    ],

];
