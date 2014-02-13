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

// Create module instance and save it first
$module = new Vtiger_Module();
$module->name = 'PublicAdmin';
$module->save();

// Initialize all the tables required
$module->initTables();
/**
* Creates the following table:
*/

// Add the module to the Menu (entry point from UI)
$menu = Vtiger_Menu::getInstance('Marketing');
$menu->addModule($module);

// Add the basic module block
$block1 = new Vtiger_Block();
$block1->label = 'LBL_PUBLICADMIN_INFORMATION';
$module->addBlock($block1);

// Add custom block (required to support Custom Fields)
$block2 = new Vtiger_Block();
$block2->label = 'LBL_CUSTOM_INFORMATION';
$module->addBlock($block2);

// Add description block (required to support Description)
$block3 = new Vtiger_Block();
$block3->label = 'LBL_DESCRIPTION_INFORMATION';
$module->addBlock($block3);

/** Create required fields and add to the block */
$field1 = new Vtiger_Field();
$field1->name = 'publicadminname';
$field1->table = $module->basetable;
$field1->label= 'Nome Ente';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';
$field1->quickcreate = 1;
$block1->addField($field1); 

// Set at-least one field to identifier of module record
$module->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->name = 'description';
$field2->table = $table_prefix.'_crmentity';
$field2->label = 'Description';
$field2->uitype = 19;
$field2->typeofdata = 'V~O';// Varchar~Optional
$block3->addField($field2); /** table and column are automatically set */
//$field2->setPicklistValues( Array ('Employee', 'Trainee') );

$field3 = new Vtiger_Field();
$field3->name = 'publicadmin_no';
$field3->table = $module->basetable;
$field3->label = 'Numero Ente';
$field3->uitype = 4;
$field3->columntype = 'VARCHAR(100)';
$field3->typeofdata = 'V~O'; //Varchar~Optional
$block1->addField($field3); 

$field7 = new Vtiger_Field();
$field7->name = 'publicadmin_type';//vte_installation_state
$field7->table = $module->basetable;
$field7->label = 'Tipo Ente';
$field7->uitype = 15;
$field7->columntype = 'VARCHAR(255)';
$field7->typeofdata = 'V~O';// Varchar~Optional
$block1->addField($field7); /** table and column are automatically set */
$field7->setPicklistValues( Array ('-- Nessuno -- ', 'Comune', 'Provincia', 'Regione', 'Servizio Provinciale', 'Consorzio', 'Altro') );

/** Common fields that should be in every module, linked to vtiger CRM core table */
$field8 = new Vtiger_Field();
$field8->name = 'assigned_user_id';
$field8->label = 'Assigned To';
$field8->table = $table_prefix.'_crmentity';
$field8->column = 'smownerid';
$field8->uitype = 53;
$field8->typeofdata = 'V~M';
$field8->quickcreate = 1;
$block1->addField($field8);

$field9 = new Vtiger_Field();
$field9->name = 'createdtime';
$field9->label= 'Created Time';
$field9->table = $table_prefix.'_crmentity';
$field9->column = 'createdtime';
$field9->uitype = 70;
$field9->typeofdata = 'T~O';
$field9->displaytype= 2;
$block1->addField($field9);

$field10 = new Vtiger_Field();
$field10->name = 'modifiedtime';
$field10->label= 'Modified Time';
$field10->table = $table_prefix.'_crmentity';
$field10->column = 'modifiedtime';
$field10->uitype = 70;
$field10->typeofdata = 'T~O';
$field10->displaytype= 2;
$block1->addField($field10);

/** table, column, label, set to default values */
$field5 = new Vtiger_Field();
$field5->name = 'publicadmin_phone';
$field5->label= 'Telefono';
$field5->table = $module->basetable;
$field5->columntype = 'VARCHAR(255)';
$field5->uitype = 1;
$field5->displaytype = 1;
$field5->typeofdata = 'V~O';
$field5->quickcreate = 1;
$block1->addField($field5);

/** table, column, label, set to default values */
$field15 = new Vtiger_Field();
$field15->name = 'publicadmin_email';
$field15->label= 'Email';
$field15->table = $module->basetable;
$field15->columntype = 'VARCHAR(255)';
$field15->uitype = 13;
$field15->displaytype = 1;
$field15->typeofdata = 'V~O';
$field15->quickcreate = 1;
$block1->addField($field15);


/** END */

// Create default custom filter (mandatory)
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$module->addFilter($filter1);

// Add fields to the filter created
$filter1->addField($field1,1)->addField($field3,2)->addField($field5,3)->addField($field7,4)->addField($field15,4);

/** Associate other modules to this module */
//get_dependents_list -> 1 -> N
//get_related_list -> N -> N



/** Set sharing access of this module */
$module->setDefaultSharing('Public');

/** Enable and Disable available tools */
$module->enableTools(Array('Import', 'Export'));
$module->disableTools('Merge'); 

require_once 'modules/ModComments/ModComments.php';
$detailviewblock_nc = ModComments::addWidgetTo('PublicAdmin');

// per aggiungere il supporto ai webservices
$module->initWebservice();

$project_module = Vtiger_Module::getInstance('Project');
$block_project = Vtiger_Block::getInstance('LBL_PROJECT_INFORMATION',$project_module); 

$field23 = new Vtiger_Field();
$field23->name = 'publicadminid';
$field23->table = $project_module->basetable;
$field23->label= 'Ente';
$field23->uitype = 10;
$field23->columntype = 'INT(19)';
$field23->typeofdata = 'I~O';
$field23->quickcreate = 0;
$block_project->addField($field23);
$field23->setRelatedModules(Array('PublicAdmin'));

//relazione 1 a n con Progetti
$module->setRelatedList($project_module, 'Projects', Array('ADD'),'get_dependents_list');

$projecttask_module = Vtiger_Module::getInstance('ProjectTask');
$block_projecttask = Vtiger_Block::getInstance('LBL_PROJECT_TASK_INFORMATION',$projecttask_module);

$field33 = new Vtiger_Field();
$field33->name = 'publicadminid';
$field33->table = $projecttask_module->basetable;
$field33->label= 'Ente';
$field33->uitype = 10;
$field33->columntype = 'INT(19)';
$field33->typeofdata = 'I~O';
$field33->quickcreate = 0;
$block_projecttask->addField($field33);
$field33->setRelatedModules(Array('PublicAdmin'));

//relazione 1 a n con Pratiche
$module->setRelatedList($projecttask_module, 'ProjectTasks', Array('ADD'),'get_dependents_list');

?>