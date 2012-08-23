<?php
/////////////////////////////////////////////////////////
//	
//	include/filters.inc
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Common filter related code

********************************************************/

	$fltr["from"] = $composeHStrings[1];
	$fltr["to"] = $composeHStrings[2];
	$fltr["cc"] = $composeHStrings[3];
	$fltr["subject"] = $composeHStrings[0];
	$fltr["body"] = $composeStrings[7];
	//$fltr["other"] = "Other";
	
	$fltr_g["operands"] = array("&", "|");
	//$fltr_g["conditions"] = array("^", "$", "<");
	$fltr_g["conditions"] = array("<");
	$fltr_g["bools"] = array("=", "!");
	$fltr_g["fields"] = array("from", "to", "cc", "subject", "body", "other");
	

	function fltrShowConditionForm($i, $cond, $show){
		global $fltr_g, $fltr;
		
		if (is_array($cond)) extract($cond);
		
		echo '<table width="100%"><tr>'."\n";
		
		//show bool (and|or)
		if ($i>0){
			echo "\n<hr>\n";
			echo '<td align="middle" valign="center">';
			echo '<select name="conditions['.$i.'][operand]">'."\n";
			echo LangDefaultOptions($fltr_g["operands"], $fltr, $operand);
			echo '</td>';
		}else{
			echo '<input type="hidden" name="conditions['.$i.'][operand]" value="|">';
		}

		echo '<td align="left" valign="top">'."\n";		
		//show field
		echo $fltr["field"].":";
		if (!empty($field) && !in_array($field, $fltr_g["fields"])){
			$field_other = $field;
			$field = "other";
		}
		echo LangDefaultMenu($fltr_g["fields"], $fltr, $field, "conditions[$i][field]");
		echo $fltr["other"].': <input type="text" name="conditions['.$i.'][field_other]" value="'.$field_other.'">'."\n";
		
		//show condition
		echo '<br>';
		echo $fltr["condition"].":";
		$a["b"] = LangDefaultMenu($fltr_g["bools"], $fltr, $bool, "conditions[$i][bool]");
		$a["c"] = LangDefaultMenu($fltr_g["conditions"], $fltr, $condition, "conditions[$i][condition]");
		$a["t"] = '<input type="text" name="conditions['.$i.'][subject]" value="'.$subject.'">'."\n";
		echo LangInsertStringsFromAK($fltr["condition_str"], $a);
		
		echo '</td>';
		
		//show buttons
		if ($show){
			echo '<td>';
			echo '<input type="submit" name="add_cond" value="'.$fltr["add_cond"].'"><br>';
			echo '<input type="submit" name="rem_cond" value="'.$fltr["remove_cond"].'"><br>';			
			echo '</td>';
		}
		
		echo '</tr></table>'."\n";
	}


	function fltrFetchFilters($loginID, $host){
		global $DB_FILTER_TABLE, $DB_TYPE;

		//open DM connection
		$dmf = new DataManager_obj;
		if ($dmf->initialize($loginID, $host, $DB_FILTER_TABLE, $DB_TYPE)){
		}else{
			echo "Data Manager initialization failed:<br>\n";
			$dmf->showError();
			return;
		}
	
		return $dmf->sort("sort_order,name", "DESC");
	}


	function fltrGetAutoApply($folder, $filters_a, &$apply_filter){
		if (is_array($filters_a) && strcasecmp($folder,"INBOX")==0){
			reset($filters_a);
			while ( list($filter_id, $v) = each($filters_a) ){
				if (strpos($v['flags'],'a')!==false) $auto_apply_list[]=$filter_id;
			}
		}
		if (is_array($auto_apply_list)){
			if (is_numeric($apply_filter)) $auto_apply_list[] = $apply_filter;
			$apply_filter = "auto";
		}
		return $auto_apply_list;
	}

    function fltrCompileConditions($conditions){
		while (list($i,$cond)=each($conditions)){
			//condition 	-> operand:field:bool:condition:text
			if (!empty($cond["subject"])){
				extract($cond);
				if ($field=="other") $field=$field_other;
				$field = ereg_replace("[:;>]", "", $field);
				$str = $operand.":".$field.":".$bool.":".$condition.":".base64_encode($subject).";";
				$out.=$str;
			}
		}
		return $out;
	}
	
	
	function fltrCompileActions($actions, $params){
		$out = "";
		while (list($act_code, $foobar)=each($actions)){
			$out .= $act_code.":".base64_encode($params[$act_code]).";";
		}
		return $out;
	}
	
	
	function fltrCompileFilter($conditions, $actions, $params){
		$conditions = fltrCompileConditions($conditions);
		$actions = fltrCompileActions($actions, $params);
		if (!empty($conditions) && !empty($actions)) return $conditions." > ".$actions;
	}
	
	function fltrParseConditions($cstr){
		$conds = explode(";", $cstr);  //split multiple conditions
		$i = 0;
		while (list($k,$str)=each($conds)){
			if (!empty($str)){
				$raw = explode(":", $str); //split condition params
				$ca["operand"] = $raw[0];
				$ca["field"] = $raw[1];
				$ca["bool"] = $raw[2];
				$ca["condition"] = $raw[3];
				$ca["subject"] = base64_decode($raw[4]);
				$outa[$i] = $ca;
				$i++;
			}
		}
		return $outa;
	}
	
	
	function fltrParseActions($astr){
		$acts = explode(";", $astr);
		while (list($i,$act)=each($acts)){
			list($act_code, $param_enc)=explode(":", $act);
			if (!empty($act_code)){
				if (!empty($param_enc)) $param = base64_decode($param_enc);
				$actions[$act_code]=($param?$param:1);
				$params[$act_code] = $param;
			}
		}
		$out["actions"] = $actions;
		$out["params"] = $params;
		return $out;
	}
	
	function fltrParseFilter($fstr){
		list($cstr,$astr) = explode(" > ", $fstr);
		if ($cstr && $astr){
			$out = fltrParseActions($astr);
			$out["conditions"] = fltrParseConditions($cstr);
		}
		return $out;
	}
	
	function fltrCheckInput($name, $conditions, $actions, $params, $sort_order){
		global $f_err;
		
		$error = "";
		
		if (empty($name)) $error .= $f_err["no_name"];
		if (!array($actions) || count($actions)==0) $error .= $f_err["no_act"];
		if (!array($conditions) || count($conditions)==0) $error .= $f_err["no_cond"];
		if ($actions['c'] && empty($params['c'])) $error .= $f_err["inv_param"];
		if ($actions['m'] && empty($params['m'])) $error .= $f_err["inv_param"];
		if (ereg("[^0-9]", $sort_order)) $error .= $f_err["inv_sort"];
		if (strlen($sort_order)==0) $sort_order = 0;
		return $error;
	}
	
	function fltrCompileSearch($condition){
		global $fltr_g;
		/*
Array
(
    [operand] => &
    [field] => to
    [bool] => =
    [condition] => <
    [subject] => ryo@iloha.net
)		*/
		extract($condition);
		$str = ($bool=="="?"":"NOT ");
		if (!in_array($field, $fltr_g["fields"])) $str.= "HEADER ";
		$str.= strtoupper($field)." ";
		$str.= "\"".$subject."\"";
		return $str;
	}
	
	function fltrSearch($conditions, $set, $last_run){
		global $conn, $folder;
		
		//frepeat for each condition
		$result_set = array();
		while(list($ck,$condition)=each($conditions)){
			//print_r($condition);
			
			//form search query
			$sc_str = $set." ".fltrCompileSearch($condition);
			//$sc_str = $set." SINCE $last_run ".fltrCompileSearch($condition);
			echo "search str: ".$sc_str."\n";
			$new_results = iil_C_Search($conn, $folder, $sc_str);
			
			if (is_array($new_results)){
				//process results
				if ($condition["operand"]=="&"){
					//intersect
					$result_set = array_intersect($result_set, $new_results);
				}else if ($condition["operand"]=="|"){
					//union
					$result_set = array_merge($result_set, $new_results);
				}
				echo "results: ".implode(",", $new_results)."\n";
			}else{
				echo "error: ".$conn->error."\n";
			}
		}
		
		for($i=count($result_set)-1;$i>=0;$i--) 
			if (empty($result_set[$i])) unset($result_set[$i]);
			
		return $result_set;
	}
	
	function fltrHandleResults($actions, $result_set){
		global $conn, $folder, $my_prefs;
		//global $main_refresh_cache;

		rsort($result_set);
		$msg_count = count($result_set);
		$msg_set = implode(",", $result_set);
		$msg_set = str_replace(",[,]+", ",", $msg_set);
		$msg_set = ereg_replace("[,]$", "", $msg_set);
		echo "end results:".$msg_set."\n";
				
		if (is_array($actions) && $msg_set){
			reset($actions);
			while(list($code, $param)=each($actions)){
				switch ($code){
					case "d": //delete
						if ($my_prefs["trash_name"]){
							iil_C_Move($conn, $msg_set, $folder, $my_prefs["trash_name"]);
						}else{
							iil_C_Delete($conn, $folder, $msg_set);
						}
						iil_C_Expunge($conn, $folder);
						break;
					case "m": //move
						if (iil_C_Move($conn, $msg_set, $folder, $param)){
							echo "Moved $msg_count to $param\n";
							iil_C_Expunge($conn, $folder);
						}
						break;
					case "c": //copy
						iil_C_Copy(&$conn, $msg_set, $folder, $param);
						break;
					case 'f': //flag
						if ($param=='Read') iil_C_Flag($conn, $folder, $msg_set, 'SEEN');
						else if ($param=='Unread') iil_C_Unseen($conn, $folder, $msg_set);
						else if ($param=='Flagged') iil_C_Flag($conn, $folder, $msg_set, "FLAGGED");
						else if ($param=='Unflagged') iil_C_Unflag($conn, $folder, $msg_set, "FLAGGED");
						echo 'Flagged '.$msg_count.' as '.$param."\n";
						break;
				}
			}
			$main_refresh_cache = true;
		}
		
		if ($main_refresh_cache && function_exists('main_firstmod')) main_firstmod($result_set);

		//print_r($actions);
	}
	
	function fltrDoFilter($filter_a, $set, $last_run){
		if (empty($set)) return;
		
		$v = $filter_a;
		echo "Running filter ".$v["name"].": ".$v["rule"]."\n";
		$rule_a = fltrParseFilter($v["rule"]);
		extract($rule_a);
			
		$result_set = fltrSearch($conditions, $set, $last_run);
		
		//process resulting set
		if (is_array($result_set) && count($result_set)>0){
			fltrHandleResults($actions, $result_set);
		}
	}
	
	function fltrGetLastRun(&$conn, $folder, &$lastRun_a, $filter_id, $cur_run){
		$last_run = $lastRun_a[$filter_id][$folder];
		if (!$last_run){
			$first_header = iil_C_FetchHeader($conn, $folder, 1);
			$last_run = firstWord($first_header->internaldate);
			echo "fetching first header: $last_run\n";
		}else{
			echo "got cached last run: ".$last_run."\n";
		}

		$lastRun_a[$filter_id][$folder] = $cur_run;
		//$lastRun_a[$filter_id]["uid"] = $cur_uid;
		
		return $last_run;
	}
	
	function fltrGetSet(&$conn, $folder, $filter_id, $cur_run, $last_run){

		//find message-id set to search in
		$search_set = iil_C_Search($conn, $folder, "ALL SINCE $last_run");
		if (!is_array($search_set) || count($search_set)==0) return "";
		echo "last run: ".$last_run."\n";
		echo "cur run: ".$cur_run."\n";
		echo '"'.implode(",", $search_set)."\"\n";
		
		if (count($search_set)==1 && empty($search_set[0])) return "";
		
		$last = count($search_set)-1;
		$set = $search_set[0].":".$search_set[$last];
		echo "set: ".$set."\n";
		
		return $set;
	}

	function fltr_getNewestUID(&$conn, $folder, $mid){
		//check MID, get if necessary
		if ($mid===false || $mid=='') 
			$mid = iil_C_CountMessages($conn, $folder);
			
		//get UID for mid
		$a = iil_C_FetchHeaderIndex($conn, $folder, $mid, 'UID');
		if (is_array($a)) return $a[$mid];
		else return 0;
	}

	function fltr_getSet(&$conn, $folder, $filter_id, &$lastRun_a, $top_mid,$top_uid){
		global $loginID, $host;
	
		//get last top UID from cache
		$cache = $lastRun_a[$filter_id];
		if (!$cache[$folder.'_uid']){
			//if no UID, running for first time.
			//store current top UID, and return empty set
			$lastRun_a[$filter_id][$folder.'_uid'] = $top_uid;
			return '1:'.$top_mid;
		}else{
			echo 'Old UID was:'.$cache[$folder.'_uid']."\n";
			echo 'New UID is: '.$top_uid."\n";
		}
	
		//get all messages after last top UID
		$last_uid = $cache[$folder.'_uid'];
		
		//if no new messages, return
		if ($last_uid==$top_uid) return '';
	
		// fetch UID index in chunks of 50, find ragen of mid of 
		// UIDs that are higher than previous top UID
		$set = array();
		$done=false;
		$min_mid = $top_mid;
		$max_mid = 1;
		do{
			$bottom_mid = max(($top_mid-50), 1);
			echo 'fetching UIDs for '.$top_mid.':'.$bottom_mid."\n";
			$uids=iil_C_FetchHeaderIndex($conn, $folder, $bottom_mid.':'.$top_mid, 'UID');
			arsort($uids);
			foreach($uids as $mid=>$uid)
				if ($uid>$last_uid){
					if ($mid<$min_mid) $min_mid = $mid;
					if ($mid>$max_mid) $max_mid = $mid;
				}else{
					$done=true;
					break;
				}
			$top_mid = $bottom_mid;
		}while( $top_mid>0 && !$done );
		
		//update last run
		$lastRun_a[$filter_id][$folder.'_uid'] = $top_uid;

		//return message ID, or range of MIDs			
		if ($min_mid==$max_mid) return $min_mid;
		else if ($min_mid>$max_mid) return $bottom_mid.':'.$top_mid;
		else return $min_mid.':'.$max_mid;		
	}
	
	function fltrApplyAll(&$conn, $folder, $filters_a, &$lastRun_a, &$top_mid, $top_uid){
		reset($filters_a);
		foreach($filters_a as $filter_id=>$v){
			if (ereg("[d]", $v["flags"])) continue;
			
			$top_mid = iil_C_CountMessages($conn, $folder);
			$set = fltr_getSet($conn, $folder, $filter_id, $lastRun_a,$top_mid,$top_uid);
			fltrDoFilter($v, $set, $last_run);
		}
	}
?>
