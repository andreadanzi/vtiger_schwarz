<?php
include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$module = Vtiger_Module::getInstance('Project');

Vtiger_Event::register($module ,'vtiger.entity.aftersave','ProjectHandler','modules/Project/ProjectHandler.php');
Vtiger_Event::register($module ,'vtiger.entity.beforesave','ProjectHandler','modules/Project/ProjectHandler.php');


?>
