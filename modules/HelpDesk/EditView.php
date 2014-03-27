<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

// danzi.tn@20140324 parent_id 
if(isset($_REQUEST['return_module']) && $_REQUEST['return_module'] =='Project' && isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] > 0) {
	global $adb;
	$parent_id = vtlib_purify($_REQUEST['parent_id']);
	$sql="SELECT projectname, project_no, linktoaccountscontacts FROM vtiger_project WHERE vtiger_project.projectid = ?";
	$result = $adb->pquery($sql,array($parent_id ));
	$account_id = $adb->query_result($result,0,"linktoaccountscontacts");
	$_REQUEST['account_id'] = $account_id;
	$_REQUEST['parent_id'] = $account_id;
}
// danzi.tn@20140324e parent_id 

require_once 'modules/Vtiger/EditView.php';

if($focus->mode == 'edit') {
	$smarty->assign("OLDSMOWNERID", $focus->column_fields['assigned_user_id']);
}

if(isset($_REQUEST['product_id'])) {
	$smarty->assign("PRODUCTID", vtlib_purify($_REQUEST['product_id']));
}

if($_REQUEST['record'] != '') {
	//Added to display the ticket comments information
	$smarty->assign("COMMENT_BLOCK",$focus->getCommentInformation($_REQUEST['record']));
}

$smarty->display("salesEditView.tpl");

?>