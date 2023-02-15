<?php
return [
    'version' => '0.1.7',
    'ModuleInfo' => [
        'name' => 'tinkoff',
        'version_description' => 'Подробная Настройка кнопки Кредитования/Рассрочка. Добавлен функционал добавления кнопки в любое расположение на проекте, путем вставки кода. Пример передаваемого массива товаров для корректной работы',
        'link_home' => '/tinkoff/admin',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/tinkoff',
        'www/assets/modules/tinkoff',
    ],
    'requireModules' => [ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
    ],
];
