<?php
/*
 * Параметры для генерации файла
 */
$VersionOptions=[];
$VersionOptions['version'] = (isset($data['version']))?$data['version']:'1.0';
$VersionOptions['name'] = (isset($data['nameModule']))?$data['nameModule']:'nameModule';
$VersionOptions['version_description'] = (isset($data['ModuleInfo']['version_description']))?$data['ModuleInfo']['version_description']:'Описание версии';
$VersionOptions['link_home'] = (isset($data['link_home']))?$data['link_home']:'/' . $data['nameModule'];

$VersionOptions['Folders'] = [ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
    'system/modules/' . $data['nameModule'],
    'www/assets/modules/' . $data['nameModule'],
];
$VersionOptions['requireModules']=[];

$VersionOptions['requireModulesGetExample'] = "[ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет
        /*[
            'name_module' => 'slider',
            'version' => '3',
        ],*/
    ]";

$VersionOptions['FoldersGetExample'] = "[ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule
        'system/modules/".$data['nameModule']."',
        'www/assets/modules/".$data['nameModule']."',
    ]";

if(isset($data['Folders']) && is_array($data['Folders'])){
    $VersionOptions['Folders']=[];
    $VersionOptions['FoldersGetExample']="[ // папки для архивации. По умолчанию system/modules/NameModule и www/assets/modules/NameModule".PHP_EOL;
    foreach ($data['Folders'] as $Folder){
        if(!empty($Folder)){
            $VersionOptions['Folders'][]=$Folder;
            $VersionOptions['FoldersGetExample'].="        '".$Folder."',".PHP_EOL;
        }
    }
    $VersionOptions['FoldersGetExample'].="    ]";
}

if(isset($data['requireModules']) && is_array($data['requireModules'])){
    $VersionOptions['requireModules']=[];
    $VersionOptions['requireModulesGetExample']="[ // дополнительные модули, которые требует данный модуль чтобы работать. По умолчанию их нет".PHP_EOL;
    foreach ($data['requireModules'] as $requireModule){
        if(!empty($requireModule['name_module']) && !empty($requireModule['version'])){
            $VersionOptions['requireModules'][]=$requireModule;
            $VersionOptions['requireModulesGetExample'].="   [
            'name_module' => '".$requireModule['name_module']."',
            'version' => '".$requireModule['version']."',
        ],".PHP_EOL;
        }
    }
    $VersionOptions['requireModulesGetExample'].="    ]";
}

$DefaultVersion = [
    'version' => $VersionOptions['version'],
    'ModuleInfo' => [
        'name' => $VersionOptions['name'],
        'version_description' => $VersionOptions['version_description'],
        'link_home' => $VersionOptions['link_home'],
    ],
    'Folders' => $VersionOptions['Folders'],
    'requireModules' => $VersionOptions['requireModules'],
];

if(!isset($data['getExample'])){
    return $DefaultVersion;
}

/*
 * Если нужен пример для создания фала version.php
 */
if(isset($data['getExample'])){
    echo "<?php
return [
    'version' => '".$VersionOptions['version']."',
    'ModuleInfo' => [
        'name' => '".$VersionOptions['name']."',
        'version_description' => '".$VersionOptions['version_description']."',
        'link_home' => '".$VersionOptions['link_home']."',
    ],
    'Folders' => ".$VersionOptions['FoldersGetExample'].",
    'requireModules' => ".$VersionOptions['requireModulesGetExample'].",
];
";
}

