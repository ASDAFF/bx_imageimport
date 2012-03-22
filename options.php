<?
if (!$USER->IsAdmin()) return;


$aTab = array(
	array("DIV" => "edit1", "TAB" => 'Настройки импорта изображений', "ICON" => "perfmon_settings", "TITLE" => 'Настройки импорта изображений',),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

if ($_SERVER['REQUIEST_METHOD'] == 'POST' && check_bitrix_sessid()) {
	//hadle post
}

$tabControl->Begin(); ?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<? $tabControl->BeginNextTab(); ?>

	<label for="search_dir">Папка, из которой будут загружаться изображения: </label>
	<input type="text" name="" value="<? COption::GetOptionString('iblock', 'search_dir', 'image_import'); ?>" />

	<label for="iblock_id">Информационный блок для привязки изображений</label>
	<select name='iblock_id'>
		<? foreach(array('1' => 'Первый инфоблок', '2' => 'Второй инфоблок',) as $key => $text): ?>
		<option value="<?=$key?>"><?=$text?></option>
		<? endforeach; ?>
	</select>

	<label for="section_id">Раздел для привязки изоражений</label>
	<select name="section_id">
		<? foreach (array('1' => 'Первый раздел', '2' => 'Второй раздел',) as $key => $text): ?>
		<option value="<?=$key?>"><?=$text?></option>
		<? endforeach; ?>
	</select>

	<input type="submit" name="submit" value="Сохранить" />
	<input type="button" name="defaults" value="По умолчанию" />

<? $tabControl->Buttons(); ?>
<? $tabControl->End(); ?>
</form>