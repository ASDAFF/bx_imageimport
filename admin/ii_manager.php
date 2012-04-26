<? 
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/imageimport/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');

IncludeModuleLangFile(__FILE__);

$APPLICATION->SetTitle(GetMessage('II_MANAGER_TITLE'));
?>

<?

$iblock_saved = COption::GetOptionString('imageimport', 'iblock', '0');
$rel_dir = COption::GetOptionString('imageimport', 'rel_dir', 'document');
$target = COption::GetOptionString('imageimport', 'target', 'iblock');


if (!CModule::IncludeModule('iblock')) return;

$rIBlockTypeList = CIBlockType::GetList(
	array('SORT' => 'ASC'),
	array()
);
$arIBlockTypeList = array();
while ($iblock_type = $rIBlockTypeList->GetNext()) $arIBlockTypeList[] = $iblock_type;
foreach($arIBlockTypeList as $i => $iblock_type) {
	$iblock_lang_settings = CIBlockType::GetByIDLang($iblock_type['ID'], LANGUAGE_ID, true);
	$arIBlockTypeList[$i]['NAME'] = $iblock_lang_settings['NAME'];
}


$rIBlockList = CIBlock::GetList(
	array('SORT' => 'ASC'),
	array(),
	false
);
$arIBlockList = array();
while ($iblock = $rIBlockList->GetNext()) $arIBlockList[] = $iblock; 


?>
<form method="POST" action="<?= $APPLICATION->GetCurPage()?>?lang=<?= LANGUAGE_ID?>" name="ii_manager_form">

<?
$aTabs = array(
	array(
		'DIV' => 'ii_common',
		'TAB' => GetMessage('II_MANAGER_TAB_COMMON_NAME'),
		'ICON' => '',
		'TITLE' => GetMessage('II_MANAGER_TAB_COMMON_TITLE'),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<tr class="heading">
	<td colspan="2" align="center">
		<?=GetMessage('II_OPT_HEADING_COMMON_IMPORT')?>
	</td>
</tr>

<tr>
	<td valign='top'>
		<label for="search_dir"><?=GetMessage('II_OPT_LABEL_DIR')?>: </label>
	</td>
	<td>
		<input type="text" name="search_dir" value="<?=COption::GetOptionString('imageimport', 'search_dir', '')?>" /><br />
	</td>
</tr>

<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="radio" name ="rel_dir" value="document" <? if ($rel_dir == 'document'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_REL_DIR_DOCUMENT')?></label><br />
		<label><input type="radio" name ="rel_dir" value="server" <? if ($rel_dir == 'server'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_REL_DIR_SERVER')?></label><br />
	</td>
</tr>

<!-- <tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="checkbox" name="recoursive" <? if (COption::GetOptionString('imageimport', 'recoursive', 'N') == 'Y'):?>checked="checked"<? endif; ?>/><?=GetMessage('II_OPT_RECOURSIVE')?></label>
	</td>
</tr> -->

<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="checkbox" name="clear_after" <? if (COption::GetOptionString('imageimport', 'clear_after', 'Y') == 'Y'):?>checked="checked"<? endif; ?>/><?=GetMessage('II_OPT_CLEAR')?></label>
	</td>
</tr>

<tr class="heading">
	<td colspan="2" align="center">
		<?=GetMessage('II_OPT_HEADING_COMMON_TARGET')?>
	</td>
</tr>

<!-- <tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="radio" name ="target" value="iblock" <? if ($target == 'iblock'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_TARGER_IBLOCK')?></label><br />
		<label><input type="radio" name ="target" value="medialib" <? if ($target == 'medialib'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_REL_TARGET_MEDIALIB')?></label><br />
	</td>
</tr> -->


<tr>
	<td>
		<label for='iblock_type_id'><?=GetMessage('II_OPT_LABEL_TYPE')?></label>
	</td>
	<td>
		<select name='iblock_type_id' id='iblock_type_id'>
				<option value="notype"></option>
			<? foreach($arIBlockTypeList as $iblock_type): ?>
				<option value="<?=$iblock_type['ID']?>"><?=$iblock_type['NAME']?></option>
			<? endforeach; ?>
		</select>
	</td>
</tr>

<tr>
	<td>
		<label for="iblock_id"><?=GetMessage('II_OPT_LABEL_IBLOCK')?></label>
	</td>
	<td>
		<select name='select-notype' id="select-notype" style="font-style:italic;">
				<option value="0"><?=GetMessage('II_OPT_SELECT_IBLOCK_TYPE_FIRST')?></option>
		</select>
		<? foreach($arIBlockTypeList as $iblock_type): ?>
		<select name="select-<?=$iblock_type['ID']?>" id="select-<?=$iblock_type['ID']?>">
			<? foreach ($arIBlockList as $iblock): ?>
				<? if ($iblock_type['ID'] == $iblock['IBLOCK_TYPE_ID']): ?>
				<option value="<?=$iblock['ID']?>"<? if ($iblock_saved == $iblock['ID']): ?> selected="selected"<? endif; ?>><?=$iblock['NAME']?></option>
				<? endif; ?>
			<? endforeach; ?>
		</select>
		<? endforeach; ?>
	</td>
</tr>

<script type="text/javascript">
var ii_select_classes = [];
ii_select_classes.push('select-notype');
<? foreach($arIBlockTypeList as $iblock_type): ?>
ii_select_classes.push('select-' + '<?=$iblock_type['ID']?>');
<? endforeach; ?>

var ii_type_select = document.getElementById('iblock_type_id');
ii_type_select.value = '<?=COption::GetOptionString('imageimport', 'iblock_type', 'notype')?>';

function iiSwitchSelect() {
	for(var i = 0; i < ii_select_classes.length; i++) {
		var current_select = document.getElementById(ii_select_classes[i]);
		current_select.style.display = 'none';
	}
	var current_select = document.getElementById('select-' + ii_type_select.value);
	current_select.style.display = 'block';
}

iiSwitchSelect();
ii_type_select.onchange = iiSwitchSelect;
</script>












<?$tabControl->EndTab();?>

<?$tabControl->Buttons();?>
<input type="submit" value="<?=GetMessage('II_MANAGER_SUBMIT')?>" />

<?$tabControl->End();?>

</form>

<?
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');
?>