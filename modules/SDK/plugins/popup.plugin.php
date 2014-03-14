<?php


add_action('add_return_popup_js','return_popup_js');
function return_popup_js($smarty,$srcmodule,$forfield) {
	if($srcmodule == 'Project' && $forfield=='linktoaccountscontacts')
	{	
		$smarty->assign("CUSTOM_JS","modules/SDK/js/AccountLookup.js");
	}
	if($srcmodule == 'ProjectTask' && $forfield=='projectid')
	{
		$smarty->assign("CUSTOM_JS","modules/SDK/js/ProjectLookup.js");
	}
	if($srcmodule == 'ProjectTask' && $forfield=='account_id')
	{
		$smarty->assign("CUSTOM_JS","modules/SDK/js/AccountLookup.js");
	}
}

add_action('add_select_popup_query','select_popup_query');
function select_popup_query($count,$value,$entity_id,$forfield,$value1) {
	global $adb,$log;
	$ret_value = "";
	if($_REQUEST['srcmodule'] == 'Project' && $_REQUEST['forfield']=='linktoaccountscontacts') 
	{
		$account_city = '';
		$account_phone = '';
		$query = "select 
					vtiger_account.accountid,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from vtiger_account join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_account.accountid = ".$entity_id;
		$log->debug("AccountLookup customquery ".$query);
		$result = $adb->query($query);
		if ($result && $adb->num_rows($result)>0) {
			$account_city = $adb->query_result($result,0,'bill_city');
			$account_phone = $adb->query_result($result,0,'phone');
		}	
		$ret_value = "<a href='javascript:window.close();' onclick='return return_account_from_popup($entity_id, \"$value\", \"$forfield\", \"$account_city\", \"$account_phone\")' id =$count >$value1</a>";		
	}
	if($_REQUEST['srcmodule'] == 'ProjectTask' && $_REQUEST['forfield']=='account_id') 
	{
		$account_city = '';
		$account_phone = '';
		$query = "select 
					vtiger_account.accountid,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from vtiger_account join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_account.accountid = ".$entity_id;
		$log->debug("AccountLookup customquery ".$query);
		$result = $adb->query($query);
		if ($result && $adb->num_rows($result)>0) {
			$account_city = $adb->query_result($result,0,'bill_city');
			$account_phone = $adb->query_result($result,0,'phone');
		}	
		$ret_value = "<a href='javascript:window.close();' onclick='return return_account_from_popup($entity_id, \"$value\", \"$forfield\", \"$account_city\", \"$account_phone\")' id =$count >$value1</a>";		
	}
	if ( $_REQUEST['srcmodule'] == 'ProjectTask' && $_REQUEST['forfield']=='projectid') {
		$account_id = 0;
		$account_name = '';
		$account_city = '';
		$account_phone = '';
		$query = "select 
					vtiger_project.projectid,
					vtiger_account.accountid,
					vtiger_account.accountname,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from  vtiger_project
					join vtiger_account on vtiger_account.accountid =  vtiger_project.linktoaccountscontacts
					join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_project.projectid = ".$entity_id;
		$log->debug("ProjectLookup customquery ".$query);
		$result = $adb->query($query);
		if ($result && $adb->num_rows($result)>0) {
			$account_id = $adb->query_result($result,0,'accountid');
			$account_name = $adb->query_result($result,0,'accountname');
			$account_city = $adb->query_result($result,0,'bill_city');
			$account_phone = $adb->query_result($result,0,'phone');
		}	
		$ret_value = "<a href='javascript:window.close();' onclick='return return_project_from_popup($entity_id, \"$value\", \"$forfield\", \"$account_id\", \"$account_name\", \"$account_city\", \"$account_phone\")' id =$count >$value1</a>";
	}
	return $ret_value;
}


?>