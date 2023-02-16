<?php
return [
    'version' => '0.1.8',
    'ModuleInfo' => [
        'name' => 'amocrm',
        'version_description' => 'Работает на версии PHP >=7.2.24! Подключение по Api к аккаунту AmoCRM. Добавление, удаление и редактирование полей а так же отправка формы методом system/modules/amocrm/controllers/amo/Leads',
        'link_home' => '/amocrm',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/amocrm',
        'www/assets/modules/amocrm',
    ],
    'requireModules' => [ //  дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
