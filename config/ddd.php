<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain-Driven Design Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings specific to the DDD architecture
    | implementation, including paths, namespaces, and other domain-specific
    | configurations.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Domain Namespaces
    |--------------------------------------------------------------------------
    |
    | Define the base namespaces for your domain layers.
    |
    */
    'namespaces' => [
        'domain' => 'App\\Domain',
        'application' => 'App\\Application',
        'infrastructure' => 'App\\Infrastructure',
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Paths
    |--------------------------------------------------------------------------
    |
    | Define the base paths for your domain layers.
    |
    */
    'paths' => [
        'domain' => app_path('Domain'),
        'application' => app_path('Application'),
        'infrastructure' => app_path('Infrastructure'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Components
    |--------------------------------------------------------------------------
    |
    | Define the components that make up each domain.
    |
    */
    'components' => [
        'domain' => [
            'entities' => 'Entities',
            'value_objects' => 'ValueObjects',
            'repositories' => 'Repositories',
            'exceptions' => 'Exceptions',
            'events' => 'Events',
        ],
        'application' => [
            'use_cases' => 'UseCases',
            'dto' => 'DTOs',
            'interfaces' => 'Interfaces',
            'services' => 'Services',
        ],
        'infrastructure' => [
            'persistence' => 'Persistence',
            'http' => 'Http',
            'services' => 'Services',
            'providers' => 'Providers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository Configuration
    |--------------------------------------------------------------------------
    |
    | Configure repository-specific settings.
    |
    */
    'repositories' => [
        'default' => 'eloquent',
        'implementations' => [
            'eloquent' => 'App\\Infrastructure\\Persistence\\Eloquent',
            'doctrine' => 'App\\Infrastructure\\Persistence\\Doctrine',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Provider Configuration
    |--------------------------------------------------------------------------
    |
    | Configure service provider-specific settings.
    |
    */
    'providers' => [
        'domain' => [
            'App\\Infrastructure\\Providers\\DomainServiceProvider',
        ],
        'application' => [
            'App\\Infrastructure\\Providers\\ApplicationServiceProvider',
        ],
        'infrastructure' => [
            'App\\Infrastructure\\Providers\\InfrastructureServiceProvider',
        ],
    ],
];
