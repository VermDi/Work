<?php
return [
    'version' => '2.7',
    'ModuleInfo' => [
        'name' => 'exim',
        'version_description' => 'Еще одно место с проблемой SSL исправлено',
        'link_home' => '/exim',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/exim',
        'www/assets/modules/exim',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
