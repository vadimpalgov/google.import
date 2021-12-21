<?php
/**
 * @var object $APPLICATION;
 * @var string $DOCUMENT_ROOT;
 */

$moduleId = 'google.import';
$moduleIdUl = 'google_import';
$moduleFilePrefix = 'google_import';

$aMenu = array();

global $USER;
$bUserIsAdmin = $USER->IsAdmin();

$bHasWRight = true;

if($APPLICATION->GetGroupRight($moduleId) < "W")
{
	$bHasWRight = false;
}

if($bUserIsAdmin || $bHasWRight)
{
	$aSubMenu[] = array(
		"text" => 'Импорт из Google Cloud',
		"url" => $moduleFilePrefix.".php?lang=".LANGUAGE_ID,
		"title" => 'Импрот из Google Cloud',
		"module_id" => $moduleId,
		"items_id" => "menu_".$moduleIdUl,
		"sort" => 100,
		"section" => $moduleIdUl,
	);

	
	$aMenu[] = array(
		"parent_menu" => "global_menu_content",
        "url" => $moduleFilePrefix.".php?lang=".LANGUAGE_ID,
		"sort" => 1400,
		"text" => 'Импрот из Google Cloud',
		"title" => 'Импрот из Google Cloud',
		"icon" => $moduleIdUl."_menu_import_icon",
		"items_id" => "menu_".$moduleIdUl."_parent",
		"module_id" => $moduleId,
	);
}

return $aMenu;
