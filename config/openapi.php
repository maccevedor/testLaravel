<?php

return [
    'title' => 'Laravel API',
    'version' => '1.0.0',
    'description' => 'API documentation for Laravel application',
    'servers' => [
        [
            'url' => env('APP_URL', 'http://localhost'),
            'description' => 'Local server',
        ],
    ],
    'paths' => [
        'api/clients' => [
            'get' => [
                'summary' => 'List all clients',
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                    ],
                ],
            ],
            'post' => [
                'summary' => 'Create a new client',
                'responses' => [
                    '201' => [
                        'description' => 'Client created successfully',
                    ],
                ],
            ],
        ],
        'api/clients/{id}' => [
            'get' => [
                'summary' => 'Get a specific client',
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                    ],
                ],
            ],
            'put' => [
                'summary' => 'Update a client',
                'responses' => [
                    '200' => [
                        'description' => 'Client updated successfully',
                    ],
                ],
            ],
            'delete' => [
                'summary' => 'Delete a client',
                'responses' => [
                    '200' => [
                        'description' => 'Client deleted successfully',
                    ],
                ],
            ],
        ],
    ],
];
