<?php
return [
    'version' => '1.5',
    'ModuleInfo' => [
        'name' => 'gkg',
        'version_description' => 'Добавлено создание миграции на создание меню',
        'link_home' => '/gkg',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/gkg',
        'www/assets/modules/gkg',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
