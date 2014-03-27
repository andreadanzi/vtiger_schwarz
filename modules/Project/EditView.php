<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

// danzi.tn@20140324 copia dei valori di città e telefono del cliente di provenienza 
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] =='Accounts' && isset($_REQUEST['account_id']) && $_REQUEST['account_id'] > 0) {
	global $adb;
	$query = "select 
					vtiger_account.accountid,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from vtiger_account join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_account.accountid = ?";
	$account_id =  vtlib_purify($_REQUEST['account_id']);
	$result = $adb->pquery($query,array($account_id));
	if ($result && $adb->num_rows($result)>0) {
		$account_city = $adb->query_result($result,0,'bill_city');
		$account_phone = $adb->query_result($result,0,'phone');
		$_REQUEST['account_city'] = $account_city;
		$_REQUEST['account_phone'] = $account_phone;
	}		
}
// danzi.tn@20140324e
 
require_once 'modules/Vtiger/EditView.php';

$smarty->display('salesEditView.tpl');

?>