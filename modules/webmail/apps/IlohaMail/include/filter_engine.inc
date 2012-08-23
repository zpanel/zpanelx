<?php
/////////////////////////////////////////////////////////
//	
//	include/filter_engine.inc
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
		Applies filter.  To be included from source/main.php

********************************************************/

	include_once('../include/filters.inc');
	include_once('../include/data_manager.inc');
	include_once('../conf/plugins.php');
	
	if ($clock) $clock->register('included filter includes');
	
	//get filters
	$filters_a = fltrFetchFilters($loginID, $host);
	$auto_apply_list = fltrGetAutoApply($folder, $filters_a, $apply_filter);
	$pifilters = $PLUGIN_HANDLERS['filters'];	
	echo '<!-- filters_a: "'.$filters_a.'" auto_apply: "'.$auto_apply_list.'" apply_filter: "'.$apply_filter.'" //-->'."\n";
	if ($clock) $clock->register('got filters');
	
	//initialize filter stuff
	if ($apply_filter || (is_array($pifilters)&&count($pifilters)>0)){
		$lastRun_a = cache_read($loginID, $host, "filter");
		$top_mid = iil_C_CountMessages($conn, $folder);
		$top_uid = fltr_getNewestUID($conn, $folder, $top_mid);
		if ($clock) $clock->register('initialized filter stuff');
	}
	
	
	//handle plugins
	if (is_array($pifilters) && count($pifilters)>0){
		//load filter status info
		echo '<!-- PLUGIN FILTERS'."\n";
		//run through filters
		foreach($pifilters as $piname=>$on){
			if (!$on) continue;
			include(PLUGIN_DIR.$piname.'/'.$piname.'.php');
			$func = $piname.'_main';
			echo 'Calling '.$func."\n";
			$func($conn, $folder, $top_mid, $top_uid);
		}
		echo '//-->'."\n";
	}
	
	if ($clock) $clock->register('plugin filters done');
	
	if (is_array($filters_a) && $apply_filter){
		echo "\n\n<!-- FILTER\n";

		if ($apply_filter=="all"){
			//apply all filters
			fltrApplyAll($conn, $folder, $filters_a, $lastRun_a, $top_mid, $top_uid);
		}else if ($apply_filter && is_numeric($apply_filter)){
			//apply specified filter
			$filter_id = $apply_filter;
			$filter_a = $filters_a[$filter_id];
			$set = fltr_getSet($conn, $folder, $filter_id, $lastRun_a,$top_mid,$top_uid);
			fltrDoFilter($filter_a, $set, $last_run);
		}else if ($apply_filter=="auto"){
			//apply auto_apply filters
			reset($auto_apply_list);
			//while ( list($n, $filter_id) = each($auto_apply_list) ){
			foreach($auto_apply_list as $filter_id){
				$v = $filters_a[$filter_id];
				echo 'apply filter '.$v['name']." \n";
				$top_mid = iil_C_CountMessages($conn, $folder);
				$set = fltr_getSet($conn, $folder, $filter_id, $lastRun_a,$top_mid,$top_uid);
				fltrDoFilter($v, $set, $last_run);
			}
		}

		if ($clock) $clock->register('done applying');
		//save state
		cache_write($loginID, $host, "filter", $lastRun_a, false);

		echo "\n// END FILTER -->\n\n";
	}

/*
get internal date of most recent message, save as "curRun"
get list of messages since last internal date ("lastRun") as "messList"
sort filter by sort order
for each filter
	for each condition
		find matches
		perform boolean on results
	for each result
		if no entry in queue for mid
			add filter ID
		remove mid from messList
go through result queue
store curRun as lastRun
*/
?>