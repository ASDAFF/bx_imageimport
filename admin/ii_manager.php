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


if (!CModule::IncludeModule('iblock')) die();

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
?>

<?
if (isset($_POST['form_id']) and $_POST['form_id'] == 'ii_manager_form') {
	// save data
	// check data
	// make import
} 

?>

<form method="POST" action="<?= $APPLICATION->GetCurPage()?>?lang=<?= LANGUAGE_ID?>" name="ii_manager_form">
	<input type="hidden" name="form_id" value="ii_manager_form" />
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
		<label for='select-type'><?=GetMessage('II_OPT_LABEL_TYPE')?></label>
	</td>
	<td>
		<select name='select-type' id='select-type'>
				<option value="notype"></option>
			<? foreach($arIBlockTypeList as $iblock_type): ?>
				<option value="<?=$iblock_type['ID']?>"><?=$iblock_type['NAME']?></option>
			<? endforeach; ?>
		</select>
	</td>
</tr>

<tr>
	<td>
		<label for="select-iblock"><?=GetMessage('II_OPT_LABEL_IBLOCK')?></label>
	</td>
	<td>
		<select name="select-iblock" id="select-iblock">
		</select>
	</td>
</tr>

<tr>
	<td>
		<label for="select-section"><?=GetMessage('II_OPT_LABEL_SECTION')?></label>
	</td>
	<td>
		<select name="select-section" id="select-section">
		</select>
	</td>
</tr>

<script type="text/javascript">
;(function(){
	
	function get_place_iblocks(select_type, select_iblock, select_section) {
		CHttpRequest.Action = function(result) {
			select_iblock.innerHTML = result;
			select_section.innerHTML = '';
		}
		CHttpRequest.Send('ii_async_ops.php?op=iblock&id=' + select_type.value);
	}

	function get_place_sections(select_iblock, select_section) {
		CHttpRequest.Action = function(result) {
			select_section.innerHTML = result;
		}
		CHttpRequest.Send('ii_async_ops.php?op=section&id=' + select_iblock.value);
	}

	var select_type = document.getElementById('select-type');
	var select_iblock = document.getElementById('select-iblock');
	var select_section = document.getElementById('select-section');

	select_type.onchange = function() {
		get_place_iblocks(select_type, select_iblock, select_section);
	}

	select_iblock.onchange = function() {
		get_place_sections(select_iblock, select_section);
	}

})();
</script>



<?$tabControl->EndTab();?>

<?$tabControl->Buttons();?>
<input type="submit" value="<?=GetMessage('II_MANAGER_SUBMIT')?>" />

<?$tabControl->End();?>

</form>

<?
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');
?>