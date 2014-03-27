<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

include_once('../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb;
session_start();

// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

include_once("modules/Users/Users.php");
$current_user= new Users();
$current_user->id = 1;

$mykeys = array('CHIAVE',
	'NOME',
	'LAVORO',
	'PAESE',
	'tipo_DIA',
	'PRESENTAZ',
	'n_CE',
	'prot_CE',
	'INIZIO',
	'FINE',
	'scavi',
	'legge_10',
	'PIANO_SICUREZZA',
	'ACCATAST',
	'conto',
	'abitabilita');

$sql = "SELECT 	temp_progettischwarz.* , 
				vtiger_account.accountid, 
				vtiger_account.accountname,
				vtiger_account.phone,
				vtiger_accountbillads.bill_city
				FROM temp_progettischwarz 
				JOIN vtiger_account ON vtiger_account.accountname = NOME 
				JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_account.accountid AND vtiger_crmentity.deleted = 0
				JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
				WHERE chiusa =1";
				
// PROG244 con id = 2058 è l'ultimo progetto con chiusa = 0
// PRA1154 con id = 2061 è l'ultima pratica con chiusa = 0
// PROG532 con id = 2753 è l'ultimo progetto con chiusa = 1
// PRA1561 con id = 2756 è l'ultima pratica con chiusa = 0
$res = $adb->query($sql);
$date_var = date('Y-m-d H:i:s');
$i=0;

$tmp_enti = array(
	"COREDO"=>382,
	"PRIO'"=>417,
	"SFRUZ"=>813,
	"SMARANO"=>383,
	"TASSULLO"=>814,
	"TAVON"=>382,	
	"TRES"=>815,
	"TUENETTO"=>821,
	"VERVO'"=>417,
	"AMBLAR"=>825,
	"TORRA "=>821,
	"AMBALAR"=>825,
	"BREZ"=>824,
	"CIS"=>823,
	"CLES"=>822,
	"DARDINE"=>821,
	"MALOSCO"=>820,
	"RALLO"=>814,
	"REVO'"=>819,
	"ROMALLO"=>818,
	"SARNONICO"=>817,
	"TERMON"=>816,
	"TRIESTE"=>827,
	"TUTELA"=>411,
	"VERMIGLIO"=>826,
	"VERVO"=>417);
/*
dia
legge 10,
piano sicurezza
accatastamento
abitabilita
concessione edilizia
mod. A scavi
cementi armati
domanda contributi
*/
echo "<p>".$sql."</p>";
foreach( $res as $row){
	$tmp_pratiche = array();
	$tmp_pratiche[] = "concessione edilizia";
	$projectEntity = CRMEntity::getInstance('Project');
	vtlib_setup_modulevars('Project',$projectEntity);
	$projectEntity->column_fields['projectname'] =  strtoupper($row['LAVORO'])." " .$row['NOME'];
	$projectEntity->column_fields['assigned_user_id'] = 5;	
	$publicadminid = 0;
	$publicadminid = $tmp_enti[trim($row['PAESE'])];
	if( empty($publicadminid) ) { 
		$publicadminid =  382;
	}
	$projectEntity->column_fields['publicadminid'] = $publicadminid;
	$projectEntity->column_fields['account_city'] = $row['bill_city'];
	$projectEntity->column_fields['account_phone'] = $row['phone'];
	$projectEntity->column_fields['linktoaccountscontacts']  = $row['accountid'];
	$projectEntity->column_fields['cf_641']  = $row['LAVORO'];
	$projectEntity->column_fields['projectstatus']  = 'Importato da verificare';
	$projectEntity->column_fields['projecttype']  = 'other';
	$tmp_descr = "";
	foreach($mykeys as $key) {
		$tmp_descr .= $key." = '".$row[$key]."'\n";
	}
	if( !empty($row['scavi']) &&  $row['scavi'] != "NO" && $row['scavi']!="no" && $row['scavi']!= "No" )
	{	
		$tmp_pratiche[] = "mod. A scavi";
	}
	if( !empty($row['legge_10']) &&  $row['legge_10'] != "NO" && $row['legge_10']!="no" && $row['legge_10']!= "No" )
	{	
		$tmp_pratiche[] = "legge 10";
	}
	if( !empty($row['PIANO_SICUREZZA']) &&  $row['PIANO_SICUREZZA'] != "NO" && $row['PIANO_SICUREZZA']!="no" && $row['PIANO_SICUREZZA']!= "No" )
	{	
		$tmp_pratiche[] = "piano sicurezza";
	}
	if( !empty($row['ACCATAST']) &&  $row['ACCATAST'] != "NO" && $row['ACCATAST']!="no" && $row['ACCATAST']!= "No" )
	{	
		$tmp_pratiche[] = "accatastamento";
	}
	$projectEntity->column_fields['cf_677'] = $tmp_pratiche;
	$projectEntity->column_fields['description']  = $tmp_descr;
	$projectEntity->column_fields['createdtime'] = $adb->formatDate($date_var, true);
	$projectEntity->column_fields['modifiedtime'] = $adb->formatDate($date_var, true);
	$projectEntity->save($module_name='Project');
	$i++;
	echo $i."<br/>";
}