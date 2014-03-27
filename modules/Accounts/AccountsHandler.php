<?php
// danzi.tn@20140314 - Generazione automatica delle pratiche
class AccountsHandler extends VTEventHandler {
    
    function handleEvent($eventName, $data) {
		global $adb,$log;
		// check irs a timcard we're saving.
		if (!($data->focus instanceof Accounts)) {
			return;
		}
		
		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
			$log->debug("handleEvent AccountsHandler vtiger.entity.beforesave entered");
			$data->focus->column_fields['cf_705'] = 'Project';
			$data->focus->column_fields['cf_707'] = 'ProjectTask';
			$log->debug("handleEvent AccountsHandler vtiger.entity.beforesave treminated");
		}
		if($eventName == 'vtiger.entity.aftersave') {
			$log->debug("handleEvent AccountsHandler vtiger.entity.aftersave entered");
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->focus;
			$log->debug("handleEvent AccountsHandler vtiger.entity.aftersave accountname = ".$focus->column_fields['accountname']);
			if( !$data->isNew() )
			{
				$log->debug("handleEvent AccountsHandler vtiger.entity.aftersave this is an update");
				$query = "UPDATE
							 vtiger_projecttask
							JOIN  vtiger_project ON  vtiger_projecttask.projectid =  vtiger_project.projectid 
							JOIN vtiger_account ON vtiger_account.accountid =  vtiger_project.linktoaccountscontacts
							JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
							JOIN vtiger_crmentity ON  vtiger_crmentity.crmid =  vtiger_accountbillads.accountaddressid AND  vtiger_crmentity.deleted = 0
							SET 
							 vtiger_projecttask.account_id =  vtiger_account.accountid,
							 vtiger_projecttask.account_city = vtiger_accountbillads.bill_city,
							 vtiger_projecttask.account_phone = vtiger_account.phone WHERE  vtiger_account.accountid = ".$id ;
				$result = $adb->query($query);
				$query = "UPDATE
							 vtiger_project
							JOIN vtiger_account ON vtiger_account.accountid =  vtiger_project.linktoaccountscontacts
							JOIN vtiger_accountbillads ON vtiger_accountbillads.accountaddressid = vtiger_account.accountid
							JOIN vtiger_crmentity ON  vtiger_crmentity.crmid =  vtiger_accountbillads.accountaddressid AND  vtiger_crmentity.deleted = 0
							SET 
							 vtiger_project.account_city = vtiger_accountbillads.bill_city,
							 vtiger_project.account_phone = vtiger_account.phone WHERE  vtiger_account.accountid = ".$id ;
				$result = $adb->query($query);
			} elseif( $data->isNew()) {
				$log->debug("handleEvent AccountsHandler vtiger.entity.aftersave this is an insert");
			}
			$log->debug("handleEvent AccountsHandler vtiger.entity.aftersave terminated");
		}
    }
}


?>
