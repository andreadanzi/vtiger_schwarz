<?php
include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb;
session_start();
$table_prefix='vtiger';
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

// Get module instance and set related list
$module = Vtiger_Module::getInstance('Documents');
$module->setRelatedList(Vtiger_Module::getInstance('ProjectTask'), 'ProjectTask', Array('SELECT'));


?>