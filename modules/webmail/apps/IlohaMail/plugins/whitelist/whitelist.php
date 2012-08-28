<?php

/*
	File:  whitelist.php
	Author: Ryo Chijiiwa, ilohamail.org
	License: GPL, part of IlohaMail
	Purpose: whitelist filter plugin
*/

include_once('../include/cache.inc');

function whitelist_read(){
	global $loginID, $host;
	
	$data = cache_read($loginID, $host, 'whitelist');
	$data['dirty'] = false;
	if ($data===false){
		$data = array();
		$data['wl_enabled'] = 0;
		$data['wl_addresses'] = array();
		$data['wl_flag'] = 0;
		$data['wl_move'] = 0;
		$data['bl_enabled'] = 0;
		$data['bl_addresses'] = array();
		$data['bl_move'] = 0;
		$data['bl_delete'] = 0;
		$data['dirty'] = true;
	}
	return $data;
}

function whitelist_write(&$data){
	global $loginID, $host;
	if ($data['dirty'])
		cache_write($loginID, $host, 'whitelist', $data, false);
}

function whitelist_add(&$data, $address, $wb){
	if ($wb=='wl') $field = 'wl_addresses';
	else $field = 'bl_addresses';
	
	$pos = array_search($address, $data[$field]);
	if ($pos===false){
		$data[$field][] = $address;
		sort($data[$field]);
		$data['dirty'] = true;
	}
}

function whitelist_remove(&$data, $addresses, $wb){
	if ($wb=='wl') $field = 'wl_addresses';
	else $field = 'bl_addresses';
	if (!is_array($addresses)) return false;
	foreach($addresses as $address){
		$pos = array_search($address, $data[$field]);
		if ($pos!==false){
			unset($data[$field][$pos]);
			$data['dirty'] = true;
		}
	}
}

function whitelist_enable(&$data, $value, $wb){
	if ($wb=='wl') $field='wl_enabled';
	else $field = 'bl_enabled';
	
	if ($data[$field]!=$value){
		$data[$field]=$value;
		$data['dirty'] = true;
	}
}

function whitelist_wlactions(&$data, $wl_move, $wl_moveto, $wl_flag){
	if ($wl_move!=$data['wl_move']){
		$data['dirty'] = true;
		$data['wl_move'] = $wl_move;
	}
	if ($wl_moveto!=$data['wl_moveto']){
		$data['dirty'] = true;
		$data['wl_moveto'] = $wl_moveto;
	}
	if ($wl_flag!=$data['wl_flag']){
		$data['dirty'] = true;
		$data['wl_flag'] = $wl_flag;
	}
}


function whitelist_blactions(&$data, $bl_move, $bl_moveto, $bl_delete){
	if ($bl_move!=$data['bl_move']){
		$data['dirty'] = true;
		$data['bl_move'] = $bl_move;
	}
	if ($bl_moveto!=$data['bl_moveto']){
		$data['dirty'] = true;
		$data['bl_moveto'] = $bl_moveto;
	}
	if ($bl_delete!=$data['bl_delete']){
		$data['dirty'] = true;
		$data['bl_delete'] = $bl_delete;
	}
}

function whitelist_addressOpts(&$data, $wb){
	if ($wb=='wl') $field = 'wl_addresses';
	else $field = 'bl_addresses';	
		
	$out="";
	foreach($data[$field] as $address){
		$out.='<option>'.$address.'</option>';
	}
	return $out;
}

function whitelist_getNewestUID(&$conn, $folder, $mid){
	$a = iil_C_FetchHeaderIndex($conn, $folder, $mid, 'UID');
	if (is_array($a)) return $a[$mid];
	else return 0;
}

function whitelist_cacheNewestUID(&$conn, $folder, $uid=false){
	global $loginID, $host;
	if ($uid===false){
		$mid = iil_C_CountMessages($conn,$folder);
		$cache['uid'] = whitelist_getNewestUID($conn, $folder, $mid);
	}else{
		$cache['uid'] = $uid;
	}
	$wrote = cache_write($loginID, $host, 'wbl_cache', $cache, false);
	echo 'New UID is '.$cache['uid'].' wrote:'.$wrote."\n";
}

function whitelist_getSet(&$conn, $folder, $top_mid, $top_uid){
	global $loginID, $host;
	
	//$top_mid = iil_C_CountMessages($conn, $folder);
	//$top_uid = whitelist_getNewestUID($conn, $folder, $top_mid);

	//get last top UID from cache
	$cache = cache_read($loginID, $host, 'wbl_cache');
	if (!$cache['uid']){
		//if no UID, running for first time.
		//store current top UID, and return empty set
		whitelist_cacheNewestUID($conn, $folder,$top_uid);
		return '';
	}else{
		echo 'Old UID was:'.$cache['uid']."\n";
	}
	
	//get all messages after last top UID
	$last_uid = $cache['uid'];
	
	//if no new messages, return
	if ($last_uid==$top_uid) return '';
	
	$set = array();
	$done=false;
	do{
		$bottom_mid = max(($top_mid-50), 0);
		echo 'fetching UIDs for '.$top_mid.':'.$bottom_mid."\n";
		$uids=iil_C_FetchHeaderIndex($conn, $folder, $bottom_mid.':'.$top_mid, 'UID');
		arsort($uids);
		foreach($uids as $mid=>$uid)
			if ($uid>$last_uid) $set[] = $mid;
			else{
				$done=true;
				break;
			}
		$top_mid = $bottom_mid;
	}while( $top_mid>0 && !$done );
	
	return implode(',',$set);
}

function whitelist_getContacts(){
	global $loginID, $host, $DB_CONTACTS_TABLE, $DB_TYPE;
	
	//initialize source name
	$source_name = $DB_CONTACTS_TABLE;
	if (empty($source_name)) $source_name = "contacts";
	
	
	//open data manager connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $source_name, $backend)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}
	
	$records = $dm->fetch_fields('email','email','');
	if (is_array($records) && count($records)>0){
		$emails=array();
		foreach($records as $rec) $emails[]=$rec['email'];
		return $emails;
	}else return array();
}

function whitelist_getCorpus(&$wbldata, $wb){
	$data = "";
	if ($wb=='wl'){
		$contacts = whitelist_getContacts();
		echo 'Contacts: '.$contacts."\n";
		if (is_array($contacts)) $data = ','.implode(',',$contacts);
		$field = 'wl_addresses';
	}else{
		$field = 'bl_addresses';
	}
	if (count($wbldata[$field])>0)
		$data.=','.implode(',',$wbldata[$field]).',';
	$data = strtoupper($data);
	return $data;
}

function whitelist_match(&$conn, $folder, &$wbldata, $set, $mode){
	global $my_prefs;
	
	echo 'whitelist_apply_wl called'."\n";
	
	// get addresses in whitelist as CSV
	$data = whitelist_getCorpus($wbldata, $mode);
	//echo "Data:\n".$data."\n\n";
	if (empty($data)) return false;
	
	// get From header lines for message set 
	$from_a = iil_C_FetchHeaderIndex($conn, $folder, $set, 'FROM');
	if (!is_array($from_a)) return false;
	
	// loop through header lines
	$matches = array();
	foreach($from_a as $mid=>$addr){
		// .... parse out addresses
		$addr_a = LangParseAddressList($addr, $my_prefs["charset"]);
		if (!is_array($addr_a)) continue;
		
		// ... loop through addresses (usually only 1)
		foreach($addr_a as $address){
			//search for address in corpus
			echo 'Checking '.$address['address'];
			if (strpos($data, ','.$address['address'].',')!==false){
				echo '... matched'."\n";
				$matches[]=$mid;
			}else{
				echo '... no match'."\n";
			}
		}
	}
	
	return $matches;
}

function whitelist_apply_wl(&$conn, $folder, &$wbldata, $set){
	
	//get matches in message set
	$matches = whitelist_match($conn, $folder, $wbldata, $set, 'wl');
	if (!is_array($matches) || count($matches)==0) return false;
	$match_set = implode(',',$matches);
	echo 'Matches: '.$match_set;
	
	$acted = false;
	
	//perform actions
	if ($wbldata['wl_flag']){
		echo 'Flagged '.$match_set.' as important.'."\n";
		iil_C_Flag($conn, $folder, $match_set, 'FLAGGED');
		$acted = true;
	}
	if ($wbldata['wl_move'] && $wbldata['wl_moveto']){
		iil_C_Move($conn, $match_set, $folder, $wbldata['wl_moveto']);
		echo 'Moved '.$match_set.' to '.$wbldata['wl_moveto']."\n";
		$acted = true;
	}
	return $acted;
}


function whitelist_apply_bl(&$conn, $folder, &$wbldata, $set){
	
	//get matches in message set
	$matches = whitelist_match($conn, $folder, $wbldata, $set, 'bl');
	if (!is_array($matches) || count($matches)==0) return false;
	$match_set = implode(',',$matches);
	echo 'Matches: '.$match_set;
	
	//perform actions
	if ($wbldata['bl_delete']){
		echo 'Deleting '.$match_set."\n";
		iil_C_Delete($conn, $folder, $match_set);
		$acted = true;
	}
	if ($wbldata['bl_move'] && $wbldata['bl_moveto']){
		iil_C_Move($conn, $match_set, $folder, $wbldata['bl_moveto']);
		echo 'Moved '.$match_set.' to '.$wbldata['bl_moveto']."\n";
		$acted = true;
	}
	return $acted;
}


function whitelist_main(&$conn, $folder, $top_mid,$top_uid){
	if (strcasecmp($folder,'inbox')!==0) return;
	
	//read in settings and configuration
	$wbldata = whitelist_read();
	if (!$wbldata['wl_enabled'] && !$wbldata['bl_enabled']) return true;
	
	//get set of messages to apply wbl on
	$set=whitelist_getSet($conn, $folder,$top_mid,$top_uid);
	
	echo 'last run: '.$last_run.' set:'.$set."\n";
	
	//if no new messages, return
	if (empty($set)) return false;
	
	//apply wbl rules on new messages
	if ($wbldata['wl_enabled']) $acted = whitelist_apply_wl($conn, $folder, $wbldata, $set);
	if ($wbldata['bl_enabled']) $acted = whitelist_apply_bl($conn, $folder, $wbldata, $set);
	
	if ($acted) iil_C_Expunge($conn, $folder);
	
	//cache latest UID so we know which messages are new next time
	whitelist_cacheNewestUID($conn, $folder);
	//$stat = whitelist_getFilterStat();
	//$newdata = whitelist_getNewData($conn, $folder, $stat);
}

?>