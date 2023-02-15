<?php
return [
    'version' => '1.1',
    'ModuleInfo' => [
        'name' => 'admin',
        'version_description' => 'Виджеты, ошибки и многое другое',
        'link_home' => '/admin',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/admin',
        'www/assets/modules/admin',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
