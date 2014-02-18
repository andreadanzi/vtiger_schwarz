<?php

class ProjectTaskHandler extends VTEventHandler {
    
    function handleEvent($eventName, $data) {
		global $adb, $current_user,$log;
		global $table_prefix;
		// check irs a timcard we're saving.
		if (!($data->focus instanceof ProjectTask)) {
			return;
		}
		
		if($eventName == 'vtiger.entity.beforesave') {
			// Entity is about to be saved, take required action
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.beforesave entered");
		
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.beforesave treminated");
		}
		if($eventName == 'vtiger.entity.aftersave') {
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave entered");
			$id = $data->getId();
			$module = $data->getModuleName();
			$focus = $data->focus;
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave this is record focus->id = ". $focus->id );
			$gg_promemoria = $focus->column_fields['cf_667'];
			// projecttask_no
			// projecttaskname Prima verifica se non esiste già la scadenza
			$newEvent = CRMEntity::getInstance('Events');
			vtlib_setup_modulevars('Events',$newEvent);
			$newEvent->column_fields['subject'] = $focus->column_fields['cf_666'];// . "-[" . $row['account_no'] . "]";
			$newEvent->column_fields['smownerid'] = $focus->column_fields["assigned_user_id"];
			$newEvent->column_fields['assigned_user_id'] = $focus->column_fields["assigned_user_id"];
			$newEvent->column_fields['createdtime'] = $focus->column_fields["modifiedtime"];
			$newEvent->column_fields['modifiedtime'] = $focus->column_fields["modifiedtime"];
			$newEvent->column_fields['parent_id'] = $focus->id;
			$newEvent->column_fields['date_start'] = $focus->column_fields['cf_665'];// 2013-05-27
			$newEvent->column_fields['time_start'] = '01:00:00';// 15:50
			$newEvent->column_fields['due_date'] =  $newEvent->column_fields['cf_665']; // 2013-05-27
			$newEvent->column_fields['time_end'] = '23:59:55';// 15:55
			$newEvent->column_fields['duration_hours'] = 23;// 2
			$newEvent->column_fields['duration_minutes'] = 59;// 2
			$newEvent->column_fields['visibility'] = "Public";
			$newEvent->column_fields['priority'] = "Medium";
			$newEvent->column_fields['activitytype'] = "Scadenza presentazione Pratica";
			$newEvent->column_fields['is_all_day_event'] = 1;
			$newEvent->column_fields['eventstatus'] = "Planned";// $insp_eventstatus 
			$newEvent->column_fields['description'] = "Scadenza della pratica " .$focus->column_fields['projecttaskname']. ", numero " .$focus->column_fields['projecttask_no'] . ", per " .$focus->column_fields['cf_666'];
			// $newEvent->column_fields['sendnotification'] = 1; //  	vtiger_activity.sendnotification 403
			$newEvent->column_fields['reminder_time'] = 1 + intval($focus->column_fields['cf_667'])*24*60;// vtiger_activity_reminder.reminder_time minuti + 1
			$newEvent->save($module_name='Events',$longdesc=false);
			$newEvent->activity_reminder($newEvent->id,$newEvent->column_fields['reminder_time'],0,0,'');
			$log->debug("handleEvent ProjectTaskHandler vtiger.entity.aftersave terminated");
		}
    }
}


?>
