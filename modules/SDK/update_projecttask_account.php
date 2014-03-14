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

// Create module instance and save it first
$module = Vtiger_Module::getInstance('ProjectTask');
$block1 = Vtiger_Block::getInstance('LBL_PROJECT_TASK_INFORMATION',$module); 

/** Cliente **/
$field11 = new Vtiger_Field();
$field11->name = 'account_id';
$field11->table = $module->basetable;
$field11->label= 'Account';
$field11->columntype = 'VARCHAR(255)';
$field11->uitype = 10;
$field11->columntype = 'INT(19)';
$field11->typeofdata = 'I~O';
$field11->displaytype= 1;
$field11->quickcreate = 0;
$block1->addField($field11); 
$field11->setRelatedModules(Array('Accounts'));

/** Città Cliente **/
$field12 = new Vtiger_Field();
$field12->name = 'account_city';
$field12->table = $module->basetable;
$field12->label= 'Citt&agrave; Cliente';
$field12->columntype = 'VARCHAR(255)';
$field12->uitype = 2;
$field12->typeofdata = 'V~O';
$field12->quickcreate = 0;
$block1->addField($field12); 

/** Telefono Cliente **/
$field13 = new Vtiger_Field();
$field13->name = 'account_phone';
$field13->table = $module->basetable;
$field13->label= 'Telefono Cliente';
$field13->columntype = 'VARCHAR(255)';
$field13->uitype = 2;
$field13->typeofdata = 'V~O';
$field13->quickcreate = 0;
$block1->addField($field13); 

//relazione 1 a n Accounts
$accounts = Vtiger_Module::getInstance('Accounts');
$accounts->setRelatedList($module, 'Pratiche', Array('ADD'), 'get_dependents_list');

$module = Vtiger_Module::getInstance('Project');
$block1 = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION',$module); 

/** Città Cliente **/
$field21 = new Vtiger_Field();
$field21->name = 'account_city';
$field21->table = $module->basetable;
$field21->label= 'Citt&agrave; Cliente';
$field21->columntype = 'VARCHAR(255)';
$field21->uitype = 2;
$field21->typeofdata = 'V~O';
$field21->quickcreate = 0;
$block1->addField($field21); 

/** Telefono Cliente **/
$field22 = new Vtiger_Field();
$field22->name = 'account_phone';
$field22->table = $module->basetable;
$field22->label= 'Telefono Cliente';
$field22->columntype = 'VARCHAR(255)';
$field22->uitype = 2;
$field22->typeofdata = 'V~O';
$field22->quickcreate = 0;
$block1->addField($field22); 


?>
