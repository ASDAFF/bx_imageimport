<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

$file_name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);
$rel_path = COption::GetOptionString('imageimport', 'rel_path', 'document');
$search_dir = COption::GetOptionString('imageimport', 'search_dir', '');

switch ($rel_path) {
	case 'document':
		$file_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $search_dir . '/' . $file_name;
		break;
	case 'server':
		$file_path = $search_dir . '/' . $file_name;
		break;
	default:
		die('0');
}

if (!file_exists($file_path)) die('0');

$file_info = pathinfo($file_path);

CModule::IncludeModule('iblock');

$arPicture = CFile::MakeFileArray($file_path);

$arFields = array(
	'NAME' => $file_info['filename'],
	'PREVIEW_TEXT' => $file_info['filename'],
	'DETAIL_PICTURE' => $arPicture,
	'ACTIVE' => 'Y',
	'IBLOCK_TYPE_ID' => COption::GetOptionString('imageimport', 'type', '0'),
	'IBLOCK_ID' => COption::GetOptionString('imageimport', 'iblock', '0'),
	'IBLOCK_SECTION_ID' => COption::GetOptionString('imageimport', 'section', '0'),
	'MODIFIED_BY' => $USER->GetID(),
);

$iblock = new CIBlockElement();
$id = $iblock->Add($arFields);


if (COption::GetOptionString('imageimport', 'clear_after', 'N') == 'Y') {
	unlink($file_path);
}

print implode('/', array('1', sprintf("\nid: [%s]\n", $id)));
