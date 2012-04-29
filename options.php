<?
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/imageimport/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_admin_after.php');

if (!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);

if ($REQUEST_METHOD == 'POST') {
	COption::SetOptionString('imageimport', 'file_types', $_POST['file_types']);
	COption::SetOptionInt('imageimport', 'worker_interval', $_POST['worker_interval']);
	CAdminMessage::ShowMessage(array(
		'MESSAGE' => GetMessage('II_OPT_SAVED_OK_TITLE'),
		'DETAILS' => GetMessage('II_OPT_SAVED_OK_MSG'),
		'TYPE' => 'OK',
		'HTML' => false,
	));
}

if (!CModule::IncludeModule('iblock')) return;

$aTabs = array(
	array(
		'DIV' => 'edit-options-common',
		'TAB' => GetMessage('II_OPT_COMMON_NAME'),
		'ICON' => '',
		'TITLE' => GetMessage('II_OPT_COMMON_TITLE'),
	),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin(); ?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<? $tabControl->BeginNextTab(); ?>

<tr class="heading">
	<td colspan="2" align="center">
		<?=GetMessage('II_OPT_HEADING_COMMON_IMPORT')?>
	</td>
</tr>

<tr>
	<td valign='top'>
		<label for="file_types"><?=GetMessage('II_OPT_LABEL_FILE_TYPES')?>: </label>
	</td>
	<td>
		<input type="text" name="file_types" value="<?=COption::GetOptionString('imageimport', 'file_types', 'jpg,gif,png')?>" /><br />
	</td>
</tr>

<tr>
	<td valign='top'>
		<label for="worker_interval"><?=GetMessage('II_OPT_LABEL_WORKER_INTERVAL')?>: </label>
	</td>
	<td>
		<input type="text" name="worker_interval" value="<?=COption::GetOptionInt('imageimport', 'worker_interval', 1000)?>" /><br />
	</td>
</tr>

<? $tabControl->Buttons(); ?>
	<input type="submit" name="submit" value="<?=GetMessage('II_OPT_SAVE')?>" />
</form>
<? $tabControl->End(); ?>
