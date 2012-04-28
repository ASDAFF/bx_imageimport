<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

if (!CModule::IncludeModule('iblock')) die();

IncludeModuleLangFile(__FILE__);

$sOperation = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);
switch ($sOperation) {
	case 'iblock':
		$iIBlockTypeID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
		print format_iblocks(get_iblocks_by_type($iIBlockTypeID));
		break;
	case 'section':
		$iIBlockID = intval(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING));
		print format_sections(get_sections_by_iblock_id($iIBlockID));
		break;
}

function get_iblocks_by_type($id) {
	$arIBlocks = array();
	$result = CIBlock::GetList(
		array('SORT' => 'ASC'),
		array('ACTIVE' => 'Y')
	);
	while ($iblock = $result->GetNext())
		if ($iblock['IBLOCK_TYPE_ID'] == $id)
			$arIBlocks[] = $iblock;
	return $arIBlocks;
}

function get_sections_by_iblock_id($id) {
	$arSections = array();
	$result = CIBlockSection::GetList(
		array('CREATED' => 'DESC'),
		array('ACTIVE' => 'Y', 'IBLOCK_ID' => $id)
	);
	while ($section = $result->GetNext())
		$arSections[] = $section;
	return $arSections;
}

function format_iblocks($iblocks) {
	$sOut = sprintf('<option value="0">%s</option>', GetMessage('II_CHOSE_IBLOCK'));
	foreach ($iblocks as $iblock)
		$sOut .= sprintf('<option value="%s">%s</option>', $iblock['ID'], $iblock['NAME']);
	return $sOut;
}
function format_sections($sections) {
	$sOut = '';
	foreach ($sections as $section)
		$sOut .= sprintf('<option value="%s">%s</option>', $section['ID'], $section['NAME']);
	$sOut .= sprintf('<option>%s</option>', GetMessage('II_ROOT_SECTION'));
	return $sOut;
}
