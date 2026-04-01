<?php

return [

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

    'table_name' => env('LOGIC_AS_DATA_TABLE_NAME', 'logic_rules'),

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
