<?php
return [
    'version' => '1.4',
    'ModuleInfo' => [
        'name' => 'block',
        'version_description' => 'Учлись изменения из редактора на стороне клиента',
        'link_home' => '/block',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/block',
        'www/assets/modules/block',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
