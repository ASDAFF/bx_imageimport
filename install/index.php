<?php
Class imageimport extends CModule {

	public $MODULE_ID = 'imageimport';
	public $MODULE_VERSION = '11.0.04';
	public $MODULE_VERSION_DATE = '2012-04-20 19:00:00';
	public $MODULE_NAME = 'Image Import';
	public $MODULE_DESCRIPTION = 'Folder image import';
	public $MODULE_GROUP_RIGHTS = 'Y';

	public function __construct() {}
	public function DoInstall() {
		RegisterModule('imageimport');
		return true;
	}
	public function DoUninstall() {
		COption::RemoveOption('imageimport');
		UnRegisterModule('imageimport');
		return true;
	}
}
?>