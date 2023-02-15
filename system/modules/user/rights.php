<?php

return [
    'index' => [
        'role' => ['*'],
        'actions' => [
            'index' => [
                'role' => ['admin', 'company_admin', 'company_user']
            ],
            'checkemail' => [
                'role' => ['*']
            ],
            'addform' => [
                'role' => ['admin', 'company_admin']
            ],
            'add' => ['role' => ['admin']],
            'edit' => ['role' => ['admin']],
            'modal' => [
                'role' => ['admin']
            ],
            'tree' => [
                'role' => ['admin', 'company_admin']
            ],
            'delete' => [
                'role' => ['admin', 'company_admin']
            ],
            'ban' => [
                'role' => ['admin', 'company_admin']
            ],
            'unban' => [
                'role' => ['admin', 'company_admin']
            ],
            'getinfo' => [
                'role' => ['admin', 'company_admin']
            ],
        ]
    ],
    'profile' => [
        'role' => ['*'],
    ],
    'permission' => [
        'role' => ['admin'],
    ],
    'role' => [
        'role' => ['admin'],
    ],
    'permissionScan' => [
        'role' => ['*']
    ],
    'properties' => [
        'role' => ['admin']
    ],
    'parameters' => [
        'role' => ['*']
    ]

];