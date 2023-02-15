<?php
return [
    'version' => '0.95',
    'ModuleInfo' => [
        'name' => 'core',
        'version_description' => 'Обновлено ядро и модель.',
        'link_home' => '/core',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/core',
        'system/modules/core',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
