<?php
return [
    'version' => '1.1',
    'ModuleInfo' => [
        'name' => 'feedback',
        'version_description' => 'При выборе формы в списке, формируется html',
        'link_home' => '/feedback',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/feedback',
        'www/assets/modules/feedback',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
