<? 
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/imageimport/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage('II_MANAGER_TITLE'));
?>

<?
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
$importing = false;
if (isset($_POST['form_id']) and $_POST['form_id'] == 'ii_manager_form') {
	// save data
	$text_options = array(
		'search_dir' => 'search_dir',
		'rel_dir' => 'rel_dir',
		'type' => 'select-type',
		'iblock' => 'select-iblock',
		'section' => 'select-section',
	);
	foreach($text_options as $text_option => $option_id) {
		COption::SetOptionString('imageimport', $text_option, $_POST[$option_id]);
	}
	
	$checkbox_options = array(
		'clear_after' => 'clear_after',
		'field_DETAIL_PICTURE' => 'DETAIL_PICTURE',
		'field_PREVIEW_PICTURE' => 'PREVIEW_PICTURE',
	);
	foreach($checkbox_options as $checkbox_option => $option_id) {
		COption::SetOptionString('imageimport' , $checkbox_option, ($_POST[$option_id]=='on')?'Y':'N');
	}
	
	
	// additional fields
	//print_r($_POST['additional_properties']);
	if (!empty($_POST['additional_properties'])) {
		$additional_properties_save = array();
		foreach($_POST['additional_properties'] as $key => $name) {
			if (!empty($name)) {
				COption::SetOptionString('imageimport', 'property_' . $name, 'Y');
				$additional_properties_save[$name] = array(
					'id' => $name,
					'width' => $_POST['additional_properties_width'][$key],
					'height' => $_POST['additional_properties_height'][$key],
					'type' => 'property',
					'sizes' => 'option',
				);
			}
		}
		/*print '<pre>';
		print_r($additional_properties_save);
		print '</pre>';*/
		COption::SetOptionString('imageimport', 'additional_properties', serialize($additional_properties_save));
	}
	
	
	

	$importing = ($_POST['importing'] == 'Y')?true:false;
	if ($importing) {
		// check data
		$images_to_import = array();
		$import_dir_path = '';
		switch ($_POST['rel_dir']) {
			case 'document':
				$import_dir_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $_POST['search_dir']; 
				break;
			case 'server':
				$import_dir_path = $_POST['search_dir'];
		}
		if (!file_exists($import_dir_path)) {
			CAdminMessage::ShowMessage(array(
				'MESSAGE' => GetMessage('II_SEARCH_DIR_NOT_EXISTS_TITLE'),
				'DETAILS' => GetMessage('II_SEARCH_DIR_NOT_EXISTS_MSG'),
				'TYPE' => 'ERROR',
				'HTML' => false,
			));
			$importing = false;
		} else {
			if (!is_dir($import_dir_path)) {
				CAdminMessage::ShowMessage(array(
					'MESSAGE' => GetMessage('II_SEARCH_DIR_NOT_DIR_TITLE'),
					'DETAILS' => GetMessage('II_SEARCH_DIR_NOT_DIR_MSG'),
					'TYPE' => 'ERROR',
					'HTML' => false,
				));
				$importing = false;
			} else {
				$dir_handler = opendir($import_dir_path);
					while ($dh_path = readdir($dir_handler)) {
						if (is_file($import_dir_path . '/' . $dh_path)) {
							$path_info = pathinfo($import_dir_path . $dh_path);
							if (in_array($path_info['extension'], explode(',', COption::GetOptionString('imageimport', 'file_types', 'jpg,gif,png')))) {
								$images_to_import[] = $dh_path;
							}
						}
					}
				closedir($dir_handler);
				if (empty($images_to_import)) {
					CAdminMessage::ShowMessage(array(
						'MESSAGE' => GetMessage('II_SEARCH_DIR_EMPTY_TITLE'),
						'DETAILS' => GetMessage('II_SEARCH_DIR_EMPTY_MSG'),
						'TYPE' => 'ERROR',
						'HTML' => false,
					));
					$importing = false;
				}
			}
		}
	} else {
		if ($_POST['add_property'] == 'N') {
			CAdminMessage::ShowMessage(array(
				'MESSAGE' => GetMessage('II_OPT_SAVED_OK_TITLE'),
				'DETAILS' => GetMessage('II_OPT_SAVED_OK_MSG'),
				'TYPE' => 'OK',
				'HTML' => false,
			));
		}
	}
}

$options_save = array(
	'rel_dir' => COption::GetOptionString('imageimport', 'rel_dir', 'document'),
	'iblock_type' => COption::GetOptionString('imageimport', 'type', '0'),
	'iblock' => COption::GetOptionString('imageimport', 'iblock', '0'),
);
?>

<? if ($importing):?>
<div id="ii-visual">
	<p><?=GetMessage('II_VIS_IMPORTED')?> <span id="ii-imported">0</span> / <?=count($images_to_import)?> (<?=GetMessage('II_VIS_NOT_IMPORTED')?> <span id="ii-not-imported">0</span>)</p>
	<div style="border:2px solid #ccc;width:400px;">
		<div id="ii-line" style="background:#ccc;width:0%;">
			&nbsp;
		</div>
	</div>
	<p id="ii-more-import" style="display: none;"><a href="ii_manager.php"><?php print GetMessage('II_NEW_IMPORT'); ?></a></p>
</div>
<? endif; ?>


<?if (!$importing):?>
<form method="POST" action="<?= $APPLICATION->GetCurPage()?>?lang=<?= LANGUAGE_ID?>" name="ii_manager_form">
	<input type="hidden" name="form_id" value="ii_manager_form" />
	<input type="hidden" name="importing" id="importing" value="Y" />
	<input type="hidden" name="add_property" id="add_property" value="N" />
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
		<label><input type="radio" name ="rel_dir" value="document" <? if ($options_save['rel_dir'] == 'document'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_REL_DIR_DOCUMENT')?></label><br />
		<label><input type="radio" name ="rel_dir" value="server" <? if ($options_save['rel_dir'] == 'server'):?>checked="checked" <? endif; ?>/><?=GetMessage('II_OPT_REL_DIR_SERVER')?></label><br />
	</td>
</tr>

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

<tr>
	<td>
		<label for='select-type'><?=GetMessage('II_OPT_LABEL_TYPE')?></label>
	</td>
	<td>
		<select name='select-type' id='select-type' style="width:300px;">
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
		<select name="select-iblock" id="select-iblock" style="width:300px;">
		</select>
	</td>
</tr>

<tr>
	<td>
		<label for="select-section"><?=GetMessage('II_OPT_LABEL_SECTION')?></label>
	</td>
	<td>
		<select name="select-section" id="select-section" style="width:300px;">
		</select>
	</td>
</tr>

<tr class="heading">
	<td colspan="2" align="center">
		<?=GetMessage('II_OPT_HEADING_COMMON_PICTURES')?>
	</td>
</tr>

<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="checkbox" name="DETAIL_PICTURE" <? if (COption::GetOptionString('imageimport', 'DETAIL_PICTURE', 'Y') == 'Y'):?>checked="checked"<? endif; ?>/><?=GetMessage('II_OPT_DETAIL_PICTURE')?></label>
	</td>
</tr>

<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><input type="checkbox" name="PREVIEW_PICTURE" <? if (COption::GetOptionString('imageimport', 'PREVIEW_PICTURE', 'Y') == 'Y'):?>checked="checked"<? endif; ?>/><?=GetMessage('II_OPT_PREVIEW_PICTURE')?></label>
	</td>
</tr>

<tr class="heading">
	<td colspan="2" align="center">
		<?=GetMessage('II_OPT_HEADING_PROPERTIES')?>
	</td>
</tr>

<?php 
$additional_properties = COption::GetOptionString('imageimport', 'additional_properties', '');
if (!empty($additional_properties))	
	$additional_properties = unserialize($additional_properties);
else $additional_properties = array();
if ($_POST['add_property'] == 'Y')
	$additional_properties[] = array(
		'id' => '',
		'width' => '',
		'height' => '',
	);
foreach($additional_properties as $additional_property):
?>
<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><?php print GetMessage('II_MANAGER_ADDITIONAL_PROPERTY_ID'); ?></label><br />
		<input type="text" name="additional_properties[]" value="<?=$additional_property['id'];?>" style="width:300px;" /><br />
	</td>
</tr>
<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<label><?php print GetMessage('II_MANAGER_WIDTH'); ?></label>
		<input type="text" name="additional_properties_width[]" value="<?=$additional_property['width'];?>" style="width:90px;" /> &nbsp;
		<label><?php print GetMessage('II_MANAGER_HEIGHT'); ?></label>
		<input type="text" name="additional_properties_height[]" value="<?=$additional_property['height'];?>" style="width:90px;" />
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<hr />
	</td>
</tr>
<?php endforeach; ?>

<tr>
	<td>
		&nbsp;
	</td>
	<td>
		<input type="button" id="add_property_button" value="<?=GetMessage('II_MANAGER_ADD_PROPERTY_BUTTON_VALUE');?>" />
	</td>
</tr>

<script type="text/javascript">
;(function(){

	var select_type = document.getElementById('select-type');
	var select_iblock = document.getElementById('select-iblock');
	var select_section = document.getElementById('select-section');

	var iblock_type = '<?=$options_save['iblock_type']?>';
	var iblock = '<?=$options_save['iblock']?>';
	
	function get_place_iblocks() {
		CHttpRequest.Action = function(result) {
			select_iblock.innerHTML = result;
			select_iblock.value = iblock;
			get_place_sections();
		}
		CHttpRequest.Send('ii_async_ops.php?op=iblock&id=' + select_type.value);
	}

	function get_place_sections() {
		CHttpRequest.Action = function(result) {
			select_section.innerHTML = result;
		}
		CHttpRequest.Send('ii_async_ops.php?op=section&id=' + select_iblock.value);
	}

	select_type.onchange = function() {
		get_place_iblocks();
	}

	select_iblock.onchange = function() {
		get_place_sections();
	}

	if(iblock_type != '0') {
		select_type.value = iblock_type;
		get_place_iblocks();
	}

})();
</script>



<?$tabControl->EndTab();?>

<?$tabControl->Buttons();?>
<input type="submit" id="submit_button" value="<?=GetMessage('II_MANAGER_SUBMIT')?>" />
<input type="button" id="save_button" value="<?=GetMessage('II_MANAGER_SAVE_SETTINGS')?>" />
<script type="text/javascript">
;(function(){
	var save_button = document.getElementById('save_button');
	var submit_button = document.getElementById('submit_button');
	var importing = document.getElementById('importing');
	var add_property_button = document.getElementById('add_property_button');
	var add_property = document.getElementById('add_property');
	save_button.onclick = function() {
		importing.value = 'N';
		submit_button.click();
	}
	add_property_button.onclick = function() {
		importing.value = 'N',
		add_property.value = 'Y',
		submit_button.click();
	}
})();
</script>

<?$tabControl->End();?>

</form>
<?php endif;?>

<?if ($importing):?>
<script type="text/javascript">
;(function(){
	var images_to_import = [];
	<?php foreach($images_to_import as $image):?>
	images_to_import.push('<?php print $image; ?>');
	<?php endforeach;?>

	var count_images = images_to_import.length;

	var ii_interval = <?php print COption::GetOptionInt('imageimport', 'worker_interval', 1000);?>;
	var ii_line = document.getElementById('ii-line');
	var ii_imported = document.getElementById('ii-imported');
	var ii_not_imported = document.getElementById('ii-not-imported');
	var ii_more_import = document.getElementById('ii-more-import');

	var ii_success = 0;
	var ii_error = 0;

	var ii_count = 0;

	CHttpRequest.Action = function(result) {
		if (result.split('/')[0] == '1') {
			ii_success++;
			ii_imported.innerHTML = ii_success.toString();
		} else {
			ii_error++;
			ii_not_imported.innerHTML = ii_error.toString();
		}
		ii_line.style.width = Math.round((ii_error + ii_success)/images_to_import.length*100).toString() + '%';
	}

	var ii_controller = setInterval(function(){
		if(ii_count < count_images) {
			CHttpRequest.Send('ii_async_iblock.php?name=' + images_to_import[ii_count]);
			ii_count++;
		} else {
			ii_more_import.style.display = 'block';
			clearInterval(ii_controller);
		}
	}, ii_interval);
})();
</script>
<?endif;?>


<?
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_admin.php');
?>