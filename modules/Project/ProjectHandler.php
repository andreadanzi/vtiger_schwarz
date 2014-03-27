<?php
// danzi.tn@20140314 - Generazione automatica delle pratiche
// danzi.tn@20140314 - Copia della Città e del Telefono del Cliente
class ProjectHandler extends VTEventHandler {
    
    function handleEvent($eventName, $data) {
		global $adb,$log;
		// check irs a timcard we're saving.
		if (!($data->focus instanceof Project)) {
			return;
		}
		
		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
			$log->debug("handleEvent ProjectHandler vtiger.entity.beforesave entered");
			/* danzi.tn@20140314 vediamo se vale la pena forzare o lasciare libero l'utente di inserire altra città
			if( $data->isNew() )
			{				
				$query = "select 
					vtiger_account.accountid,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from vtiger_account join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_account.accountid = ?";
				$account_id = $data->focus->column_fields["linktoaccountscontacts"];
				$result = $adb->pquery($query,array($account_id));
				if ($result && $adb->num_rows($result)>0) {
					$account_city = $adb->query_result($result,0,'bill_city');
					$account_phone = $adb->query_result($result,0,'phone');
					$data->focus->column_fields["account_city"] = $account_city;
					$data->focus->column_fields["account_phone"] = $account_phone;
				}	
			}
			*/
			$log->debug("handleEvent ProjectHandler vtiger.entity.beforesave treminated");
		}
		if($eventName == 'vtiger.entity.aftersave') {
			$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave entered");
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->focus;
			$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave projectname = ".$focus->column_fields['projectname']);
			if( !$data->isNew() )
			{
				$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave this is an update");
			} elseif( $data->isNew()) {
				$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave this is an insert");
				$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave this is an insert focus->id = ". $focus->id );
				// se non è un projecttype collaudo allora genera Pratiche di default
				$projecttype = $focus->column_fields['projecttype'];
				$pratiche_obbligatorie = $focus->column_fields["cf_677"];
				if( !empty($projecttype) && $projecttype!="collaudo" ) {
					$tipo_procedura = array('dia'=>'DIA',
											'legge 10'=>'LEGGE_10',
											'piano sicurezza'=>'PS',
											'accatastamento'=>'ACCATASTAMENTO',
											'abitabilita'=>"ABITABILITA'",
											'concessione edilizia'=>'CE',
											'mod. A scavi'=>'MOD_A_SCAVI',
											'cementi armati'=>'CA',
											'domanda contributi'=>'DOM_CONTRIB');
					foreach ($pratiche_obbligatorie as $key) {
						$projectTask = CRMEntity::getInstance('ProjectTask');
						vtlib_setup_modulevars('ProjectTask',$projectTask);
						$projectTask->column_fields['projecttaskname'] = $tipo_procedura[$key] . " - " .$focus->column_fields['projectname'];
						$projectTask->column_fields['projecttasktype'] = 'amministrativa';
						$projectTask->column_fields['account_id'] = $focus->column_fields["linktoaccountscontacts"];
						$projectTask->column_fields['assigned_user_id'] = $focus->column_fields["assigned_user_id"];
						$projectTask->column_fields['createdtime'] = $focus->column_fields["createdtime"];
						$projectTask->column_fields['modifiedtime'] = $focus->column_fields["modifiedtime"];
						$projectTask->column_fields['startdate'] = $focus->column_fields["startdate"];
						$projectTask->column_fields['enddate'] = $focus->column_fields["targetenddate"];
						// $projectTask->column_fields['xxx'] = $focus->column_fields["actualenddate"]; // data fine effettiva -> ???
						// $projectTask->column_fields['xxx'] = $focus->column_fields["cf_668"]; // data autorizzazione -> ???
						// $projectTask->column_fields['cf_657'] = $focus->column_fields["cf_642"]; // data progetto -> Data Presentazione
						// $projectTask->column_fields['cf_658'] = $focus->column_fields["cf_642"]; // data progetto -> Data Protocollo
						$projectTask->column_fields['projectid'] = $focus->id;
						$projectTask->column_fields['publicadminid'] = $focus->column_fields["publicadminid"];
						$projectTask->column_fields['projecttaskpriority'] = 'normal';
						$projectTask->column_fields['cf_660'] = $key;
						$projectTask->column_fields['account_city'] = $focus->column_fields['account_city'];
						$projectTask->column_fields['account_phone'] = $focus->column_fields['account_phone'];
						$projectTask->save($module_name='ProjectTask',$longdesc=false);
					}
				}
			}
			$log->debug("handleEvent ProjectHandler vtiger.entity.aftersave terminated");
		}
    }
}


?>
