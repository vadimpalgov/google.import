<?php
$moduleId = 'google.import';

require __DIR__ . '/vendor/autoload.php';

CModule::AddAutoloadClasses(
    $moduleId,
    [
        '\Bitrix\GoogleImport\Client' => 'lib/Client.php',
        '\Bitrix\GoogleImport\Sheets' => 'lib/Sheets.php',
        '\Bitrix\GoogleImport\Profile' => 'lib/Profile.php',
        '\Bitrix\GoogleImport\ProfileManager' => 'lib/ProfileManager.php',
        '\Bitrix\GoogleImport\Profiles\ProfileInterface' => 'lib/profiles/ProfileInterface.php',
        '\Bitrix\GoogleImport\Profiles\CartridgeOld' => 'lib/profiles/Cartridge.php',
        '\Bitrix\GoogleImport\Profiles\Review' => 'lib/profiles/Review.php',
        '\Bitrix\GoogleImport\Profiles\Printer' => 'lib/profiles/Printer.php',
        '\Bitrix\GoogleImport\Profiles\Cartridge' => 'lib/profiles/Cartridge.php',
    ]
);

$moduleJsId = str_replace('.', '_', $moduleId);
$pathJS = '/bitrix/js/'.$moduleId;
$pathCSS = '/bitrix/panel/'.$moduleId;

$arJSKdaIBlockConfig = array(
    $moduleJsId => array(
        'js' => $pathJS.'/script.js',
        'css' => $pathCSS.'/styles.css',
        'rel' => array('jquery', $moduleJsId.'_chosen'),
    ),
);

foreach ($arJSKdaIBlockConfig as $ext => $arExt) {
    CJSCore::RegisterExt($ext, $arExt);
}