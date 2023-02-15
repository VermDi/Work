<?php
return [
    'version' => '3.0.3',
    'ModuleInfo' => [
        'name' => 'pages',
        'version_description' => 'Внедрение нового билдера! Обратная совместимость почти полная! Но попытка соблюсти сделана!',
        'link_home' => '/pages',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/pages',
        'www/assets/modules/pages',
        'www/assets/vendors/e-mindhelpers',
        'www/assets/vendors/ckeditor',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
