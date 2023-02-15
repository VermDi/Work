<?php
return [
    'version' => '1.1',
    'ModuleInfo' => [
        'name' => 'faq',
        'version_description' => 'Небольшие правки в модуле',
        'link_home' => '/faq',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/faq',
        'www/assets/modules/faq',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
