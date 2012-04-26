<?php
Class imageimport extends CModule {

	public $MODULE_ID = 'imageimport';
	public $MODULE_VERSION = '11.0.06';
	public $MODULE_VERSION_DATE = '2012-04-26 16:00:00';
	public $MODULE_NAME = 'Image Import';
	public $MODULE_DESCRIPTION = 'Folder image import';
	public $MODULE_GROUP_RIGHTS = 'Y';

	public function __construct() {}
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