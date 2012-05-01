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

$iblockID = COption::GetOptionString('imageimport', 'iblock', '0');
$iblockFields = CIBlock::GetArrayByID($iblockID, 'FIELDS');

$detailPicture_sizes = array(
	'width' => $iblockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['WIDTH'],
	'height' => $iblockFields['DETAIL_PICTURE']['DEFAULT_VALUE']['HEIGHT'],
);
$previewPicture_sizes = array(
	'width' => $iblockFields['PREVIEW_PICTURE']['DEFAULT_VALUE']['WIDTH'],
	'height' => $iblockFields['PREVIEW_PICTURE']['DEFAULT_VALUE']['HEIGHT'], 
);

$detailPicture_source = $file_info['dirname'] . '/_detail_' . $file_name;
$previewPicture_source = $file_info['dirname'] . '/_preview_' . $file_name;

CFile::ResizeImageFile($file_path, $detailPicture_source ,$detailPicture_sizes);
CFile::ResizeImageFile($file_path, $previewPicture_source, $previewPicture_sizes);

$arDetailPicture = CFile::MakeFileArray($detailPicture_source);
$arPreviewPicture = CFile::MakeFileArray($previewPicture_source);

$arFields = array(
	'NAME' => $file_info['filename'],
	'PREVIEW_TEXT' => $file_info['filename'],
	'DETAIL_PICTURE' => $arDetailPicture,
	'PREVIEW_PICTURE' => $arPreviewPicture,
	'ACTIVE' => 'Y',
	'IBLOCK_TYPE_ID' => COption::GetOptionString('imageimport', 'type', '0'),
	'IBLOCK_ID' => $iblockID,
	'IBLOCK_SECTION_ID' => COption::GetOptionString('imageimport', 'section', '0'),
	'MODIFIED_BY' => $USER->GetID(),
);

$iblockElement = new CIBlockElement();
$id = $iblockElement->Add($arFields);

unlink($detailPicture_source);
unlink($previewPicture_source);

if (COption::GetOptionString('imageimport', 'clear_after', 'N') == 'Y') {
	unlink($file_path);
}

print implode('/', array('1', sprintf("\nid: [%s]\n", $id)));
