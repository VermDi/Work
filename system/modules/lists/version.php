<?php
return [
    'version' => '1.2',
    'ModuleInfo' => [
        'name' => 'lists',
        'version_description' => 'UPDATE FOR MYSQL 5.7 +',
        'link_home' => '/lists',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/lists',
        'www/assets/modules/lists',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
