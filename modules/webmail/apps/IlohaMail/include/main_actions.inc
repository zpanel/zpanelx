<?php
/////////////////////////////////////////////////////////
//	
//	include/main_actions.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////
	if ($clock) $clock->register('action: '.$submit);
	if (isset($submit)){
		$messages="";
		
		/* if move entire thread from read_message, swap uid with t_uid */
		if ($move_thread && is_array($t_uids)) $uids = $t_uids;
		
		if ($uidl) $uids = explode(',', $uidl);
		if ($midl) $checkboxes = explode(',', $midl);

		/* compose an IMAP message list string including all checked items */
		if ((is_array($uids)) && (implode("",$uids)!="")){
			//$checkboxes = iil_C_Search($conn, $folder, "UID ".implode(",", $uids));
			$checkboxes = iil_C_UIDsToMIDs($conn, $folder, $uids);
			echo '<!-- UIDs:'.implode(',',$uids).' mids:'.implode($checkboxes).' //-->';
		}
		if (is_array($checkboxes)){
               $messages = implode(",", $checkboxes);
               $num_checked = count($checkboxes);
			   main_firstmod($checkboxes);
		}
		
		/* "Move to trash" is same as "Delete" */
		if (($submit=="File") && $moveto && (strcmp($moveto, $my_prefs["trash_name"])==0)) $submit="Delete";
           
		/*  delete all */
		if ($delete_all == 2 ){
			$messages .= "1:".$delete_all_num;
		}
					
		$delete_success = false;
		if (($submit=="Delete")||(strcmp($submit,$mainStrings[10])==0)){
		/* delete items */
			//if folders and trash specified, move to trash
			if ($ICL_CAPABILITY["folders"]){
				if (strcmp($folder, $my_prefs["trash_name"])!=0){
					if (!empty($my_prefs["trash_name"])){
						if (iil_C_Move($conn, $messages, $folder, $my_prefs["trash_name"]) >= 0){
							$delete_success = true;
						}else{
							$report = $mainErrors[2].":".$messages;
						}
					}
				}else{
					$report = $mainErrors[3].":".$messages;
				}
			}

			//otherwise, just mark as deleted
			if (!$delete_success){
				if (iil_C_Delete($conn, $folder, $messages) > 0) $delete_success = true;
			}
			
			//if deleted, format success report
			if ($delete_success){
				$report =  str_replace("%n", $num_checked, $mainMessages["delete"]);
			}
		}else if (($submit=="File")||(strcmp($submit,$mainStrings[12])==0)){
		/*  move items */
			if (strcasecmp($folder, $my_prefs["trash_name"])==0){
				iil_C_Undelete($conn, $folder, $messages);
			}
			if (iil_C_Move($conn, $messages, $folder, $moveto) >= 0){
				$report = str_replace("%n", $num_checked, $mainMessages["move"]);
				$report = str_replace("%f", $moveto, $report);
				if (strcasecmp($folder, $my_prefs["trash_name"])==0){
					//iil_C_Delete($conn, $folder, $messages);
				}
			}else{
				$report = $mainErrors[4];
			}
		}else if ($submit=="Unread"){			
		/* mark as unread */
			iil_C_Unseen($conn, $folder, $messages);
			$reload_folders = true;
			$selected_boxes = $checkboxes;
		}else if ($submit=="Read"){
		/* mark as read */
			iil_C_Flag($conn, $folder, $messages, "SEEN");
			$reload_folders = true;
			$selected_boxes = $checkboxes;
		}else if ($submit=="Flagged"){
		/* mark as important */
			iil_C_Flag($conn, $folder, $messages, "FLAGGED");
			$report="Flagged $messages in $folder as important";
			$selected_boxes = $checkboxes;
		}else if ($submit=="Unflagged"){
		/* mark as important */
			iil_C_Unflag($conn, $folder, $messages, "FLAGGED");
			$report="Flagged $messages in $folder as normal";
			$selected_boxes = $checkboxes;
		}else if (($submit=="Expunge") && ($expunge==1)){
		/* empty trash  command */
			if ($folder==$my_prefs["trash_name"]){
				if (!iil_C_ClearFolder($conn, $folder)){
					echo $mainErrors[6]." (".$conn->error.")<br>\n";
				}
			}else{
				$error .= "Folder $folder is not trash (trash is ".$my_prefs["trash_name"].")<br>\n";
			}
		}
		
		/* expunge non-trash folders automatically */
		if (strcasecmp($folder,$my_prefs["trash_name"])!=0){
			iil_C_Expunge($conn, $folder);
		}
		
	} //end if submit

?>