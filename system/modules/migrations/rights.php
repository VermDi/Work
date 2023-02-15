<?php
return [

    'Index' => [
        'role'    => ['*'],
        'actions' => [
            'index' => [
                'role' => ['admin']
            ],
            'scan'=>[
                'role'=>['admin']
            ],
            'down'=>[
                'role'=>['admin']
            ],
            'createForm'=>[
                'role'=>['admin']
            ],
            'create'=>[
                'role'=>['admin']
            ],
            'downForm'=>[
                'role'=>['admin']
            ],
        ]
    ],
    'Up'    => [
        'role' => ['*']
    ]
];
