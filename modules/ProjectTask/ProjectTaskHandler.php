<?php

class ProjectTaskHandler extends VTEventHandler {
    
    function handleEvent($eventName, $data) {
		global $adb,$log;
		// check irs a timcard we're saving.
		if (!($data->focus instanceof ProjectTask)) {
			return;
		}
		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.beforesave entered");
			$account_id = $data->focus->column_fields['account_id'];
			$projectid = $data->focus->column_fields['projectid'];
			if( empty($account_id) && ! empty($projectid) ) {
				$account_city = '';
				$account_phone = '';
				$accountid = 0;
				$query = "select 
					vtiger_project.projectid,
					vtiger_account.accountid,
					vtiger_account.accountname,
					vtiger_account.phone,
					vtiger_accountbillads.bill_city
					from  vtiger_project
					join vtiger_account on vtiger_account.accountid =  vtiger_project.linktoaccountscontacts
					join vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid	WHERE vtiger_project.projectid = ".$projectid;
				$result = $adb->query($query);
				if ($result && $adb->num_rows($result)>0) {
					$account_city = $adb->query_result($result,0,'bill_city');
					$account_phone = $adb->query_result($result,0,'phone');
					$accountid = $adb->query_result($result,0,'accountid');
					$data->focus->column_fields['account_id'] = $accountid;
					$data->focus->column_fields['account_city'] = $account_city;
					$data->focus->column_fields['account_phone'] = $account_phone;
				}
			}
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.beforesave treminated");
		}
		if($eventName == 'vtiger.entity.aftersave') {
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave entered");
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->focus;
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave this is record focus->id = ". $focus->id );
			$descr_promemoria = $focus->column_fields['cf_666'];
			$gg_promemoria = $focus->column_fields['cf_667'];
			$data_scadenza = $focus->column_fields['cf_665'];
			if(!empty($gg_promemoria) && !empty($data_scadenza)) {
				$date_start = getValidDBInsertDateValue($focus->column_fields['cf_665']);
				if(empty($descr_promemoria))	$descr_promemoria = "Promemoria pratica " . $focus->column_fields['projecttaskname'];
				// projecttask_no
				// projecttaskname Prima verifica se non esiste già la scadenza
				$sql="SELECT
						vtiger_activity.activityid,
						vtiger_activity.subject,
						vtiger_activity.date_start,
						vtiger_seactivityrel.crmid as parentid, -- projecttaskid
						vtiger_activity_reminder.reminder_time
						FROM
						vtiger_seactivityrel
						JOIN vtiger_activity on vtiger_activity.activityid = vtiger_seactivityrel.activityid
						JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid and vtiger_crmentity.deleted = 0
						LEFT JOIN vtiger_activity_reminder on vtiger_activity_reminder.activity_id = vtiger_activity.activityid
						WHERE
						vtiger_seactivityrel.crmid = ?";
				$res = $adb->pquery($sql, array($focus->id));
				$numrows = $adb->num_rows($res);
				if( $numrows > 0 ) {
					$activityid = $adb->query_result($res, 0, 'activityid');	
					$eventstatus = 'Planned';
					$reminder = 1 + intval($focus->column_fields['cf_667'])*24*60;// vtiger_activity_reminder.reminder_time minuti + 1
					$eventdescr = "Scadenza della pratica " .$focus->column_fields['projecttaskname']. ", numero " .$focus->column_fields['projecttask_no'] . ", per " .$focus->column_fields['cf_666'];
					$date_var = date('Y-m-d H:i:s');
					$sqlUpdateEvent = "UPDATE vtiger_activity
										SET date_start='".$date_start."' , eventstatus='".$eventstatus."', due_date = date_start , subject ='".$descr_promemoria."'
										WHERE vtiger_activity.activityid=".$activityid;
					$adb->query($sqlUpdateEvent); 
					$sqlUpdateEvent = "UPDATE vtiger_activity_reminder
										SET reminder_time=".$reminder."
										WHERE vtiger_activity_reminder.activity_id=".$activityid;
					$adb->query($sqlUpdateEvent); 
					$sqlUpdateCrmEntity = "UPDATE vtiger_crmentity
										SET modifiedtime=? , description ='".$eventdescr."'
										WHERE vtiger_crmentity.crmid=?";
					$adb->pquery($sqlUpdateCrmEntity,array($adb->formatDate($date_var, true),$activityid));
				} else {
					//crea un nuovo evento
					$newEvent = CRMEntity::getInstance('Events');
					vtlib_setup_modulevars('Events',$newEvent);
					$newEvent->column_fields['subject'] = $descr_promemoria;// . "-[" . $row['account_no'] . "]";
					$newEvent->column_fields['smownerid'] = $focus->column_fields["assigned_user_id"];
					$newEvent->column_fields['assigned_user_id'] = $focus->column_fields["assigned_user_id"];
					$newEvent->column_fields['createdtime'] = $focus->column_fields["modifiedtime"];
					$newEvent->column_fields['modifiedtime'] = $focus->column_fields["modifiedtime"];
					$newEvent->column_fields['parent_id'] = $focus->id;
					$newEvent->column_fields['date_start'] = $date_start;// 2013-05-27
					$newEvent->column_fields['time_start'] = '07:00:00';// 15:50
					$newEvent->column_fields['due_date'] =  $date_start; // 2013-05-27
					$newEvent->column_fields['time_end'] = '18:00:00';// 15:55
					$newEvent->column_fields['duration_hours'] = 11;// 2
					$newEvent->column_fields['duration_minutes'] = 0;// 2
					$newEvent->column_fields['sendnotification'] = 0;
					$newEvent->column_fields['visibility'] = "Public";
					$newEvent->column_fields['priority'] = "Medium";
					$newEvent->column_fields['activitytype'] = "Scadenza presentazione Pratica";
					$newEvent->column_fields['eventstatus'] = "Planned";// $insp_eventstatus 
					$newEvent->column_fields['description'] = "Scadenza della pratica " .$focus->column_fields['projecttaskname']. ", numero " .$focus->column_fields['projecttask_no'] . ", per " .$descr_promemoria;
					// $newEvent->column_fields['sendnotification'] = 1; //  	vtiger_activity.sendnotification 403
					$newEvent->column_fields['reminder_time'] = 1 + intval($focus->column_fields['cf_667'])*24*60;// vtiger_activity_reminder.reminder_time minuti + 1
					$newEvent->save($module_name='Events',$longdesc=false);
					$newEvent->activity_reminder($newEvent->id,$newEvent->column_fields['reminder_time'],0,0,'');
				}
			}
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave terminated");
		}
    }
}


?>
