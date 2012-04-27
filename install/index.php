<?php
Class imageimport extends CModule {

	public $MODULE_ID = 'imageimport';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME = 'Image Import';
	public $MODULE_DESCRIPTION = 'Folder image import';
	public $MODULE_GROUP_RIGHTS = 'Y';

	public function __construct() {
		include(dirname(__FILE__) . '/version.php');
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
	}
	public function DoInstall() {
		RegisterModule('imageimport');
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/imageimport/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		return true;
	}
	public function DoUninstall() {
		COption::RemoveOption('imageimport');
		DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/imageimport/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');
		UnRegisterModule('imageimport');
		return true;
	}
}
?>