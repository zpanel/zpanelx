<?php
/*

File: 		main_core.inc
Purpose: 	Core of main.php and the likes that does things and fetches data
Author: 	Ryo Chijiiwa
License:  	This file is part of IlohaMail, released under the GPL

*/

include_once('../include/icl.inc');
include_once('../include/main.inc');
include_once('../include/cache.inc');

/////////////////
//	INITIALIZATION
/////////////////

	//initialize some vars
	if (!isset($hideseen)) $hideseen=0;
	if (!isset($showdeleted)) $showdeleted=0;
	if (strcmp($folder, $my_prefs["trash_name"])==0) $showdeleted=1;
	if (empty($my_prefs["main_cols"])) $my_prefs["main_cols"]="camfsdz";
	
	//initialize main context
	$MAIN_CONTEXT = main_contextInit($OLD_CONTEXT);
	echo '<!--';
	print_r($MAIN_CONTEXT);
	echo '//-->';
	
	//more initializations!
	if (!isset($folder) || empty($folder)) $folder="INBOX";
	$main_firstmod_id = false;
	$thread_view = ($my_prefs['thread_view'] && $ICL_CAPABILITY['threads'] && stristr($sort_field,"date")!==false && !$search_criteria && !$search && !$do_quick_search);
	$is_draft_box = ($folder==$my_prefs['draft_box_name']);

	$clock->register("pre-connect");
	
	//connect to mail server
	echo "<!--\nCookie:".$_COOKIE["IMAIL_TEST_COOKIE"]."\nHost: $host\nPort:$port\n//-->\n";
	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if (!$conn){
		echo "Connection failed: $iil_error <br> ";
		exit;
	}
	echo "<!-- ".date('Z')." use internal date: $IMAP_USE_INTERNAL_DATE sort_field: ".$my_prefs['sort_field']." //-->\n";
	$clock->register("post-connect");
		
	echo "\n<!-- ICLMessages:\n".$conn->message."-->\n";
	echo '<!--'."\n".$conn->rootdir."\n".$conn->delimiter."\n".'//-->'."\n";
	
	//default names for toolbar input fields, used in main_tools.inc as well
	$main_tool_fields = array("expunge", "empty_trash", "delete_selected",
								"mark", "mark_as", "moveto", "move_selected");

	//if toolbar displayed at top & bottom, bottom fields will have '_2' appened
	//at the end of field name.  we deal with that here
	reset($main_tool_fields); 
	while ( list($k,$tool_field)=each($main_tool_fields) ){
		$tool_var_name = $tool_field."_2";
		$tool_var_val = $$tool_var_name;
		if (!empty($tool_var_val)) $$tool_field = $tool_var_val;
	}										
	
	/*  show "To" field or "From" field? */
	if ($folder==$my_prefs["sent_box_name"] || $folder==$my_prefs['draft_box_name']){
		$showto=true;
		$fromheading=$mainStrings[7];
	}else{
		$fromheading=$mainStrings[8];
	}

	
/////////////////
//	ACTIONS AND FILTERS
/////////////////

	//apply filter
	if ($ICL_CAPABILITY["filters"]){
		include("../include/filter_engine.inc");
		$clock->register("post-filter");
	}
		
	//actions (flagging, deleting, moving, etc)
	$submit = main_get_submit();
	
	//handle actions
	if ($submit){
		include('../include/main_actions.inc');
	}	
		
	/* If search results were moved or deleted, need to re-search */
	if (isset($search_done)) $MAIN_CONTEXT['i'] = false;
	
	
//////////////////
//	FETCH INDEX & HEADERS
//////////////////		

	/* figure out which/how many messages to fetch */
	if ((empty($start)) || (!isset($start))) $start = 0;
	$num_show=$my_prefs["view_max"];
	if ($show_all || $do_quick_search) $num_show = $post_total_num;
	if ($num_show==0) $num_show=50;
	$next_start=$start+$num_show;
	$prev_start=$start-$num_show;
	if ($prev_start<0) $prev_start=0;
	
	$clock->register("pre-count");

	//retreive message list (search, or list all in folder)
	$messages_str = "";
	if ((!empty($search)) || (!empty($search_criteria))){
		//do search
		list($total_num, $messages_str, $error) = main_handle_search($conn, $folder, $search_criteria, $srchp);
	}else if ($do_quick_search && $quick_search_str){
		//quick search
		list($total_num, $messages_str, $error) = main_doQuickSearch($conn, $folder, $showto, $quick_search_str);
	}else if ($MAIN_CONTEXT["c"]=="q" && $MAIN_CONTEXT["i"]){
		//cached message list
		$messages_str = $MAIN_CONTEXT["i"];
		$total_num = $MAIN_CONTEXT['n'];
	}else{
		//select from all in folder
		$total_num=iil_C_CountMessages($conn, $folder);
		if ($total_num > 0) $messages_str="1:".$total_num;
		else $messages_str="";
		$index_failed = false;	
	}
	$total_num_msgs = $total_num;
	
	$clock->register("post count");
	echo "<!-- Total num: $total_num Sort: $sort_field //-->\n"; flush();
	
	// store commonly used parameters in an array 
	// so we can pass it to functions easily
	$index_params = array('tv'=>$thread_view, 'sf'=>$sort_field, 'st'=>$start, 
								'ns'=>$num_show, 'tn'=>$total_num, 'so'=>$sort_order,'nxs'=>$next_start);
		
	//if there are more messages than will be displayed,
	// 		create an index array, sort, 
	// 		then figure out which messages to fetch 
	if ((($total_num - $num_show) > 0) || $thread_view){
		$index_a = main_fetch_index($conn, $folder, $messages_str, $index_params);
		if (!$index_a) $index_failed = $index_params['if'] = true;		
	}
	
	$clock->register("post index (".count($index_a).')');

	/* fetch headers */
	if ($thread_view && $messages_str!=""){
		//fetch threaded headers
		echo "\n<!--   T TREE \n"; flush();
		$thread_data = main_fetchThreads($folder, $index_a, $total_num, $sort_field, $sort_order);
		$t_tree = $thread_data['t'];
				
		$th_data = main_fetchThreadHeaders($conn, $folder, $t_tree, $start, $next_start);
		if ($th_data){
			$headers = $th_data['h'];
			$t_num_kids = $th_data['k'];
		}
		if ($headers) $total_num = count($t_tree);
		echo '//-->';

		$clock->register("fetched threaded headers");
	}
	
	/* if we haven't gotten headers yet, get them */
	if (!is_array($headers) && $messages_str!=""){
		$headers = main_fetchHeaders($conn, $folder, $messages_str, $index_params);
	}


?>