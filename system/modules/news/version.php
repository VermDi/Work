<?php
return [
    'version' => '1.1',
    'ModuleInfo' => [
        'name' => 'news',
        'version_description' => 'Исправление незначительных багов',
        'link_home' => '/news',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/news',
        'www/assets/modules/news',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
