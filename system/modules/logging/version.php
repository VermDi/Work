<?
return [
    'version' => '1.0',
    'ModuleInfo' => [
        'name' => 'logging - модуль для Логирования изменений',
        'version_description' => 'Модуль не логирует все подряд, он лишь позволяет сохранять сообщения, и выводить их по фильтру',
        'link_home' => '/logging',
    ],
    'Folders' => [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/logging',
        'www/assets/modules/logging'
    ],
];