<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/prolog.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_js.php');

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

$arFields = array(
		'NAME' => $file_info['filename'],
		'PREVIEW_TEXT' => $file_info['filename'],
		'ACTIVE' => 'Y',
		'IBLOCK_TYPE_ID' => COption::GetOptionString('imageimport', 'type', '0'),
		'IBLOCK_ID' => $iblockID,
		'IBLOCK_SECTION_ID' => COption::GetOptionString('imageimport', 'section', '0'),
		'MODIFIED_BY' => $USER->GetID(),
);

$pictures = array(
	'DETAIL_PICTURE' => array(
		'type' => 'field',
		'sizes' => 'iblock',
	), 
	'PREVIEW_PICTURE' => array(
		'type' => 'field',
		'sizes' => 'iblock',
	),
);

$unlink_queue = array();

foreach($pictures as $title => $options) {
	if (COption::GetOptionString('imageimport', $title, 'N') == 'Y') {
		$picture_sizes = array('width' => 0, 'height' => 0);
		switch ($options['sizes']) {
			case 'iblock':
				$picture_sizes = array(
					'width' => $iblockFields[$title]['DEFAULT_VALUE']['WIDTH'],
					'height' => $iblockFields[$title]['DEFAULT_VALUE']['HEIGHT'],
				);
				break;
			case 'option':
				$picture_sizes = array(
					'width' => $options['width'],
					'height' => $options['height'],
				);
				break;
		}
		if (!empty($picture_sizes['width']) or !empty($picture_sizes['height'])) {
			do {
				$picture_source = sprintf('%s/_%s_%s_%s', $file_info['dirname'], $title, uniqid(), $file_name);
			} while (file_exists($picture_source));
			CFile::ResizeImageFile($file_path, $picture_source, $picture_sizes);
			$unlink_queue[] = $picture_source;
		} else {
			$picture_source = $file_path;
		}
		$picture_array = CFile::MakeFileArray($picture_source);
		switch ($options['type']) {
			case 'field':
				$arFields[$title] = $picture_array;
				break;
			case 'property':
				$arFields['PROPERTIES'][$options['id']] = $picture_array();
				break;
		}
	}
}

$iblockElement = new CIBlockElement();
$id = $iblockElement->Add($arFields);

if (COption::GetOptionString('imageimport', 'clear_after', 'N') == 'Y')
	$unlink_queue[] = $file_path;

foreach($unlink_queue as $file_to_unlink)
	unlink($file_to_unlink);

print implode('/', array('1', sprintf("\nid: [%s]\n", $id)));
