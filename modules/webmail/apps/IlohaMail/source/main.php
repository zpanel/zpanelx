<?php
/////////////////////////////////////////////////////////
//	
//	source/main.php
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/main.php
	PURPOSE:
		1.  List specified number of messages in specified order from given folder.
		2.  Provide interface to read messages (link subjects to source/read_message.php)
		3.  Provide interface to send messasge to senders (link "From" field to source/compose.php)
		4.  Provide interface to move or delete messages
		5.  Provide interface to view messages not currently listed.
		6.  Provide functionality to move, delete messages and expunge folders.
	PRE-CONDITIONS:
		$user - Session ID
		$folder - Folder name
		[$sort_field] - Field to sort by {"subject", "from", "to", "size", "date"}
		[$sort_order] - Order, "ASC" or "DESC"
		[$start] - Show specified number of messages starting with this index

********************************************************/

$exec_start_time = microtime();

include("../include/stopwatch.inc");
$clock = new stopwatch(true);

$clock->register("start");

include("../include/super2global.inc");
$clock->register("pre-header");
include("../include/header_main.inc");
$clock->register("post-header");
include("../include/ryosdates.inc");
include("../include/icl.inc");
include("../include/main.inc");
include("../include/cache.inc");
$clock->register("includes done");

	if (!isset($folder)){
		echo "Error: folder not specified";
		exit;
	}
	include("../lang/".$my_prefs["lang"]."defaultFolders.inc");
	include("../lang/".$my_prefs["lang"]."main.inc");
	include("../lang/".$my_prefs["lang"]."dates.inc");

	//initialize some vars
	if (!isset($hideseen)) $hideseen=0;
	if (!isset($showdeleted)) $showdeleted=0;
	if (strcmp($folder, $my_prefs["trash_name"])==0) $showdeleted=1;
	if (empty($my_prefs["main_cols"])) $my_prefs["main_cols"]="camfsdz";
	
	$clock->register("pre-connect");
	
	//connect to mail server
	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if (!$conn){
		echo "Connection failed: $iil_error <br> ";
		exit;
	}
	
	$clock->register("post-connect");
		
	echo "\n<!-- ICLMessages:\n".$conn->message."-->\n";
	
	//move columns
	//$MOVE_FIELDS = 1;
	if ($MOVE_FIELDS){
		$report = $mainErrors[8];
		if ($move_col && $move_direction){
			//echo "Moving fields <br>\n";
			$col_pos = strpos($my_prefs["main_cols"], $move_col);
			if ($col_pos !== false){
				if ($move_direction=="right") $move_direction = 1;
				else if ($move_direction=="left") $move_direction = -1;
				$partner_col = $my_prefs["main_cols"][$col_pos+$move_direction];
				//echo "Shift is $move_direction switching with $partner_col <br>\n";
				if ($partner_col){
					$my_prefs["main_cols"][$col_pos+$move_direction] = $move_col;
					$my_prefs["main_cols"][$col_pos] = $partner_col;
					include("../include/save_prefs.inc");
				}
			}
		}
	}
	

	//default names for toolbar input fields, used in main_tools.inc as well
	$main_tool_fields = array("expunge", "empty_trash", "delete_selected",
								"mark_read", "mark_unread", "moveto", "move_selected");

	//if toolbar displayed at top & bottom, bottom fields will have '_2' appened
	//at the end of field name.  we deal with that here
	reset($main_tool_fields); 
	while ( list($k,$tool_field)=each($main_tool_fields) ){
		$tool_var_name = $tool_field."_2";
		$tool_var_val = $$tool_var_name;
		if (!empty($tool_var_val)) $$tool_field = $tool_var_val;
	}										
	
	//actions (flagging, deleting, moving, etc)
	if ($move_selected) $submit = "File";
	if ($delete_selected) $submit = "Delete";
	if ($empty_trash) $submit = "Expunge";
	if ($mark_read) $submit = "Read";
	if ($mark_unread) $submit = "Unread";
	
	if (isset($submit)){
		$messages="";
		
		/* compose an IMAP message list string including all checked items */
		if ((is_array($uids)) && (implode("",$uids)!="")){
			$checkboxes = iil_C_Search($conn, $folder, "UID ".implode(",", $uids));
		}
		if (is_array($checkboxes)){
               $messages = implode(",", $checkboxes);
               $num_checked = count($checkboxes);
		}
		
		/* "Move to trash" is same as "Delete" */
		if (($submit=="File") && $moveto && (strcmp($moveto, $my_prefs["trash_name"])==0)) $submit="Delete";
           
		/*  delete all */
		if ($delete_all == 2 ){
			$messages .= "1:".$delete_all_num;
		}
					
		/* delete items */
		$delete_success = false;
		if (($submit=="Delete")||(strcmp($submit,$mainStrings[10])==0)){
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
		}
		
		/*  move items */
		if (($submit=="File")||(strcmp($submit,$mainStrings[12])==0)){
			if (strcasecmp($folder, $my_prefs["trash_name"])==0){
				iil_C_Undelete($conn, $folder, $messages);
			}
			if (iil_C_Move($conn, $messages, $folder, $moveto) >= 0){
				$report = str_replace("%n", $num_checked, $mainMessages["move"]);
				$report = str_replace("%f", $moveto, $report);
				if (strcasecmp($folder, $my_prefs["trash_name"])==0){
					iil_C_Delete($conn, $folder, $messages);
				}
			}else{
				$report = $mainErrors[4];
			}
		}
			
			
		/* empty trash  command */
		if (($submit=="Expunge") && ($expunge==1)){
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
		
		/* mark as unread */
		if ($submit=="Unread"){
			iil_C_Unseen($conn, $folder, $messages);
			$reload_folders = true;
			$selected_boxes = $checkboxes;
		}
		
		/* mark as read */
		if ($submit=="Read"){
			iil_C_Flag($conn, $folder, $messages, "SEEN");
			$reload_folders = true;
			$selected_boxes = $checkboxes;
		}
	} //end if submit
		
		
	/* If search results were moved or deleted, stop execution here. */
	if (isset($search_done)){
		echo "<p>Request completed.\n";
		echo "</body></html>";
		exit;
	}
	
	/* initialize sort field and sort order 
		(set to default prefernce values if not specified */
	
	if (empty($sort_field)) $sort_field=$my_prefs["sort_field"];
	if (empty($sort_order)) $sort_order=$my_prefs["sort_order"];

	
	/* figure out which/how many messages to fetch */
	if ((empty($start)) || (!isset($start))) $start = 0;
	$num_show=$my_prefs["view_max"];
	if ($num_show==0) $num_show=50;
	$next_start=$start+$num_show;
	$prev_start=$start-$num_show;
	if ($prev_start<0) $prev_start=0;
	//echo "<p>Start: $start";
	
	/* flush, so the browser can't start renderin and user sees some feedback */
	flush();
	
	$clock->register("pre-count");

	/* retreive message list (search, or list all in folder) */
	if ((!empty($search)) || (!empty($search_criteria))){
		include("../lang/".$my_prefs["lang"]."search_errors.inc");
		$criteria="";
		$error="";
		$date = $month."/".$day."/".$year;
		if (empty($search_criteria)){
			// check criteria
			if ($date_operand=="ignore"){
				if ($field=="-"){
					$error=$searchErrors["field"];
				}
				if (empty($string)){
					$error=$searchErrors["empty"];
				}
			}else if ((empty($date))||($date=="mm/dd/yyyy")){
				$error=$searchErrors["date"];
			}
			if (!empty($date)){
				$date_a=explode("/", $date);
				$date=iil_FormatSearchDate($date_a[0], $date_a[1], $date_a[2]);
			}
		}
		if ($error==""){
			// format search string
			if (empty($search_criteria)){
				$criteria="ALL";
				if ($field!="-") $criteria.=" $field \"$string\"";
				if ($date_operand!="ignore") $criteria.=" $date_operand $date";
				$search_criteria = $criteria;
			}else{
				$search_criteria = stripslashes($search_criteria);
				$criteria = $search_criteria;
			}
			
			echo "Searching \"$criteria\" in $folder<br>\n"; flush();
			
			// search
			$messages_a=iil_C_Search($conn, $folder, $criteria);
			if ($messages_a!==false){
				$total_num=count($messages_a);
				if (is_array($messages_a)) $messages_str=implode(",", $messages_a);
				else $messages_str="";
				echo "found: {".$messages_str."} <br>\n"; flush();
			}else{
				echo "Error: ".$conn->error."<br>\n"; flush();
			}
		}else{
			$headers=false;
		}
	}else{
		$total_num=iil_C_CountMessages($conn, $folder);
		if ($total_num > 0) $messages_str="1:".$total_num;
		else $messages_str="";
		$index_failed = false;		
	}
	
	$clock->register("post count");
	
	echo "<!-- Total num: $total_num //-->\n"; flush();
		
		
	/* if there are more messages than will be displayed,
	 		create an index array, sort, 
	 		then figure out which messages to fetch 
	*/
	if (($total_num - $num_show) > 0){
		//attempt ot read from cache
		$read_cache = false;
		if (file_exists(realpath($CACHE_DIR))){
			$cache_path = $CACHE_DIR.ereg_replace("[\\/]", "", $loginID.".".$host);
			$index_a = main_ReadCache($cache_path, $folder, $messages_str, $sort_field, $read_cache);
		}
		//if there are "recent" messages, ignore cache
	    if ($ICL_CAPABILITY["radar"]){
			$recent=iil_C_CheckForRecent($conn, $folder);
			if ($recent > 0) $read_cache = false;
		}
		
		//if not read from cache, go to server
		if (!$read_cache){
			$index_a=iil_C_FetchHeaderIndex($conn, $folder, $messages_str, $sort_field);
			$clock->register("post index: no cache");
		}else{
			$clock->register("post index: cache");
		}
		
		if ($index_a===false){
			//echo "iil_C_FetchHeaderIndex failed<br>\n";
            if (strcasecmp($sort_field,"date")==0){
                if (strcasecmp($sort_order, "ASC")==0){
                    $messages_str = $start.":".($start + $num_show);
                }else{
                    $messages_str = ($total_num - $start - $num_show).":".($total_num - $start);
                }
                //echo $messages_str; flush();
                $index_failed = false;
            }else{
                $index_failed = true;
            }
		}else{
			if ((!$read_cache) && (file_exists(realpath($CACHE_DIR))))
				main_WriteCache($cache_path, $folder, $sort_field, $index_a, $messages_str);

			if (strcasecmp($sort_order, "ASC")==0) asort($index_a);
			else if (strcasecmp($sort_order, "DESC")==0) arsort($index_a);
			
			reset($index_a);
			$i=0;
			while (list($key, $val) = each ($index_a)){
				if (($i >= $start) && ($i < $next_start)) $id_a[$i]=$key;
				$i++;
			}
			if (is_array($id_a)) $messages_str=implode(",", $id_a);

		}
		
		
		echo "<!-- Indexed: $index_a //-->"; flush();
	}
	
	$clock->register("post index");

	/* fetch headers */
	if ($messages_str!=""){
		//echo "Messages: $messages_str <br>\n";
		$headers=iil_C_FetchHeaders($conn, $folder, $messages_str);
		$headers=iil_SortHeaders($headers, $sort_field, $sort_order);  //if not from index array
	}else{
		$headers=false;
	}
	
	$clock->register("post headers");
	echo "<!-- Headers fetched: $headers //-->\n"; flush();
	
	/* if indexing failed, we need to get messages within range */
	if ($index_failed){
		$i = 0;
		$new_header_a=array();
		reset($headers);
		while ( list($k, $h) = each($headers) ){
			if (($i >= $start) && ($i < $next_start)){
				$new_header_a[$k] = $headers[$k];
				//echo "<br>Showing $i : ".$h->id;
			}
			$i++;
		}
		$headers = $new_header_a;
	}
		
	/*  start form */
	echo "\n<form name=\"messages\" method=\"POST\" action=\"main.php\">\n";			

	/* Show folder name, num messages, page selection pop-up */
	
	if ($headers==false) $headers=array();
	echo "<table width=\"100%\" cellpadding=2 cellspacing=0><tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
	echo "<td align=left valign=bottom>\n";
		$disp_folderName = $defaults[$folder];
		if (empty($disp_folderName)){
			$disp_folderName = $folder;
			if (iil_StartsWith($disp_folderName, $my_prefs["rootdir"])){
				$disp_folderName = substr($disp_folderName, strlen($my_prefs["rootdir"])+1);
			}
		}
		if (empty($search)){
			echo "<span class=\"bigTitle\">";
			echo iil_utf7_decode($disp_folderName);
			echo "</span>\n";
		}
		echo "<span class=\"mainHeadingSmall\">\n";
		if (strcasecmp("INBOX", $folder)==0)
			echo "[<a href=\"main.php?user=$user&folder=$folder\" class=\"mainHeadingSmall\">".$mainStrings[17]."</a>]";
        if (strcmp($folder,$my_prefs["trash_name"])!=0)
			echo "[<a href=\"main.php?user=$user&folder=$folder&delete_all=1\" class=\"mainHeadingSmall\">".$mainStrings[18]."</a>]";
		echo "</span>\n";
	echo "</td>\n";
	echo "<td align=\"right\" valign=\"bottom\" class=\"mainHeadingSmall\">";
		//show quota
		if ($my_prefs["show_quota"]=="m"){
			$quota = iil_C_GetQuota($conn);
			include("../lang/".$my_prefs["lang"]."quota.inc");
			if ($quota) echo $quotaStr["label"].LangInsertStringsFromAK($quotaStr["full"], $quota);
			else echo $quotaStr["label"].$quotaStr["unknown"];
		}
	echo "</td>\n";
	echo "</tr></table>";
	
	/* Confirm "delete all" request */
	if ($delete_all==1){
		echo "<p>".str_replace("%f", $folder, $mainErrors[7]);
		echo "<span class=\"small\">[<a href=\"main.php?user=$user&folder=$folder&delete_all=2&delete_all_num=$total_num&submit=Delete\">";
			echo $mainStrings[18]."</a>]</span>";
		echo "<span class=\"small\">[<a href=\"main.php?user=$user&folder=$folder\">".$mainStrings[19]."</a>]</span>";
	}
	
	
	/* Show error messages, and reports */
	if (!empty($error)) echo "<p><center><span style=\"color: red\">$error</span></center>";
	//if ((empty($error)) && (empty($report))) echo "<p>";


	$c_date["day"]=GetCurrentDay();
	$c_date["month"]=GetCurrentMonth();
	$c_date["year"]=GetCurrentYear();

	if (count($headers)>0) {
		if (!isset($start)) $start=0;
		$i=0;

		if (sizeof($headers)>0){			
			/*  show "To" field or "From" field? */
			if ($folder==$my_prefs["sent_box_name"]){
				$showto=true;
				$fromheading=$mainStrings[7];
			}else{
				$fromheading=$mainStrings[8];
			}			


			/*  show num msgs and any notices */
			echo "<table width=\"100%\"><tr>";
			echo "<td valign=bottom align=\"left\"><span class=\"mainLightSmall\">";

			echo str_replace("%p", ($num_show>$total_num?$total_num:$num_show), str_replace("%n", $total_num, $mainStrings[0]))."&nbsp;";
			
			echo "</span</td>";
			echo "<td align=center><span class=\"mainLightSmall\">";
			if (!empty($report)) echo $report;
			echo "</span></td>\n";
			echo "<td valign=bottom align=right class=\"mainLightSmall\">";
			//page controls
			$num_items=$total_num;
			if ($num_items > $num_show){
				if ($prev_start < $start){
					$args = "&sort_field=$sort_field&sort_order=$sort_order&start=$prev_start";
					if (!empty($search_criteria)) $args .= "&search_criteria=".urlencode($search_criteria);
					echo "[<a href=\"main.php?user=$sid&folder=".urlencode($folder).$args."\" class=\"mainLightSmall\">";
					echo $mainStrings[2]." $num_show".$mainStrings[3]."</a>]";
				}

				if ($next_start<$num_items){
					$num_next_str = $num_show;
					if (($num_items - $next_start) < $num_show) $num_next_str = $num_items - $next_start;
					$args = "&sort_field=$sort_field&sort_order=$sort_order&start=$next_start";
					if (!empty($search_criteria)) $args .= "&search_criteria=".urlencode($search_criteria);
					echo "[<a href=\"main.php?user=$sid&folder=".urlencode($folder).$args."\" class=\"mainLightSmall\">";
					echo $mainStrings[4]." $num_next_str".$mainStrings[5]."</a>]";
				}

				echo "<select name=start class=\"small\">\n";
					$c=0;
					while ($c < $total_num){
						$c2=($c + $num_show);
						if ($c2 > $total_num) $c2=$total_num;
						echo "<option value=".$c.($c==$start?" SELECTED":"").">".($c+1)."-".$c2."\n";
						$c = $c + $num_show;
					}
				echo "</select>";
				echo "<input type=submit value=\"".$mainStrings[16]."\">";
				
			}
			echo "</td>\n";
			echo "</tr></table>\n";

			$clock->register("pre list");

			/***
			Show tool bar
			***/
			if (strpos($my_prefs["main_toolbar"], "t")!==false){
				include("../include/main_tools.inc");
			}

			/* main list */
			$num_cols = strlen($my_prefs["main_cols"]);
			echo "\n<!-- MAIN LIST //-->\n";
			echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"".$my_colors["main_hilite"]."\">\n";
			echo "<tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
				$check_link="<SCRIPT type=\"text/javascript\" language=JavaScript1.2><!-- Make old browsers think this is a comment.\n";
				$check_link.="document.write(\"<a href=javascript:SelectAllMessages(true) class='tblheader'><b>+</b></a><span class=tblheader>|</span><a href=javascript:SelectAllMessages(false) class=tblheader><b>-</b></a>\")";
				$check_link.="\n--></SCRIPT><NOSCRIPT>";
				$check_link.="<a href=\"main.php?folder=".urlencode($folder)."&start=$start&user=$user&sort_field=$sort_field&sort_order=$sort_order&check_all=1\"><b>+</b></a>|";
 				$check_link.="<a href=\"main.php?folder=".urlencode($folder)."&start=$start&user=$user&sort_field=$sort_field&sort_order=$sort_order&uncheck_all=1\"><b>-</b></a>";
				$check_link.="</NOSCRIPT>";
				$tbl_header["c"] = "\n<td>$check_link</td>";
				$tbl_header["s"] = "\n<td>".FormFieldHeader("subject", $mainStrings[6])."</td>";
				if ($showto)
					$tbl_header["f"] = "\n<td>".FormFieldHeader("to", $fromheading)."</td>";
				else
					$tbl_header["f"] = "\n<td>".FormFieldHeader("from", $fromheading)."</td>";
				$tbl_header["d"] = "\n<td>".FormFieldHeader("date", $mainStrings[9])."</td>";
				$tbl_header["z"] = "\n<td>".FormFieldHeader("size", $mainStrings[14])."</td>";
				$tbl_header["a"] = "<td><img src=\"themes/".$my_prefs["theme"]."/images/att.gif\"></td>";
				$tbl_header["m"] = "<td><img src=\"themes/".$my_prefs["theme"]."/images/reply.gif\"></td>";
				for ($i=0;$i<$num_cols;$i++) echo $tbl_header[$my_prefs["main_cols"][$i]];
			echo "\n</tr>\n";
			if ($MOVE_FIELDS){
				echo "<tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
				$base_url = "main.php?folder=".urlencode($folder)."&start=$start&user=$user&sort_field=$sort_field&sort_order=$sort_order";
				$base_url.= "&MOVE_FIELDS=1";
				for ($i=0;$i<$num_cols;$i++) echo ShowFieldControls($my_prefs["main_cols"][$i], $base_url, $i, $num_cols);
				echo "</tr>\n";
			}
			$display_i=0;
			$prev_id = "";
			while (list ($key,$val) = each ($headers)) {
				//$next_id = $headers[key($headers)]->id;
				$header = $headers[$key];
				$id = $header->id;
				$seen = ($header->seen?"Y":"N");
				$deleted = ($header->deleted?"D":"");
				if (($id>0) && (($showdeleted==0)&&($deleted!="D")) || ($showdeleted)){
					if (($hideseen==0)||($seen=="N")){
						$display_i++;
						//echo "\n<tr ".(($i % 2)==0?"bgcolor=\"$bgc\"":"").">\n";
						
						echo "\n<tr bgcolor=\"".$my_colors["main_bg"]."\">\n";
						
						//show checkbox
						$row["c"] = "<td><input type=\"checkbox\" name=\"checkboxes[]\" value=\"$id\" ";
						$row["c"].= (isset($check_all)?"CHECKED":"");
						if (!isset($uncheck_all)) $row["c"].=(($spam) && (isSpam($header->Subject)>0) ? "CHECKED":"");
						if (is_array($selected_boxes) && in_array($id, $selected_boxes)) $row["c"].="CHECKED";
						$row["c"].= "></td>\n";
						//echo $row["c"];
						
						//show subject
						$subject=trim(chop($header->subject));
						if (empty($subject)) $subject=$mainStrings[15];
						$args = "user=$user&folder=".urlencode($folder)."&id=$id&uid=".$header->uid."&start=$start";
						$args.= "&num_msgs=$total_num&sort_field=$sort_field&sort_order=$sort_order";
						$row["s"] = "<td><a href=\"read_message.php?".$args."\" ";
						$row["s"].= ($my_prefs["view_inside"]!=1?"target=\"scr".$user.urlencode($folder).$id."\"":"").">".($seen=="N"?"<B>":"");
						$row["s"].= encodeUTFSafeHTML(LangDecodeSubject($subject, $my_prefs["charset"])).($seen=="N"?"</B>":"")."</a></td>\n";
						//echo $row["s"];
						
						//show sender||recipient
						if ($showto) $row["f"] = "<td>".LangDecodeAddressList($header->to, $my_prefs["charset"], $user)."</td>\n";						
						else $row["f"] = "<td>".LangDecodeAddressList($header->from, $my_prefs["charset"], $user)."</td>\n";
						//echo $row["f"];

						//show date/time
						$timestamp = $header->timestamp;
						$timestamp = $timestamp + ((int)$my_prefs["timezone"] * 3600);
						$row["d"] = "<td><nobr>".ShowShortDate($timestamp, $lang_datetime)."&nbsp;</nobr></td>\n";
						//echo $row["d"];

						//show size
						$row["z"] = "<td><nobr>".ShowBytes($header->size)."</nobr></td>\n";

						//attachments?
						$row["a"] = "<td>";
						if (preg_match("/multipart\/m/i", $header->ctype)==TRUE){
							$row["a"].= "<img src=\"themes/".$my_prefs["theme"]."/images/att.gif\">";
						}
						$row["a"].= "</td>\n";
						//echo $row["a"];

						//show flags
						$row["m"] = "<td>".($header->deleted?"D":"").($header->answered?"<img src=\"themes/".$my_prefs["theme"]."/images/reply.gif\">":"&nbsp;")."</td>\n";
						//echo $row["a"];
						
						for ($i=0;$i<$num_cols;$i++) echo $row[$my_prefs["main_cols"][$i]];
						
						echo "</tr>\n";
						flush();
					}
				}
				$i++;
			}
			echo "</table>";

			flush();
			
			$clock->register("post list: $i");
			
			echo "<input type=\"hidden\" name=\"user\" value=\"$user\">\n";
			echo "<input type=\"hidden\" name=\"folder\" value=\"$folder\">\n";
			echo "<input type=hidden name=\"sort_field\" value=\"".$sort_field."\">\n";
			echo "<input type=hidden name=\"sort_order\" value=\"".$sort_order."\">\n";
			if (isset($search)) echo "<input type=hidden name=search_done value=1>\n";
			echo "<input type=\"hidden\" name=\"max_messages\" value=\"".$display_i."\">\n";
			
			/***
			Show tool bar
			***/
			if (strpos($my_prefs["main_toolbar"], "b")!==false){
				$clock->register("pre tools include");
				include("../include/main_tools.inc");
				$clock->register("post tools include");
			}
			
			echo "</form>\n";
			
			if (($folder=="INBOX")&&($ICL_CAPABILITY["radar"])){
				/*** THIS JavaScript code does NOT run reliably!! ***/
				echo "\n<script language=\"JavaScript\">\n";
				echo "if (parent.radar)";
				echo "  parent.radar.location=\"radar.php?user=".$user."\";\n";
				echo "</script>\n";
			}
			if (($ICL_CAPABILITY["folders"]) && ($my_prefs["list_folders"]) && ($my_prefs["showNumUnread"]) && ($reload_folders)){
				echo "\n<script language=\"JavaScript\">\n";
				echo "parent.list1.location=\"folders.php?user=".$user."\";\n";
				echo "</script>\n";
			}
		}else{
			if (!empty($search)) echo "<p><center>".$mainErrors[0]."</center>";
			else echo "<p><center><span class=mainLight>".$mainErrors[1]."</span></center>";
		}
	}else{
		if (!empty($search)) echo "<p><center><span class=mainLight>".$mainErrors[0]."</span></center>";
		else echo "<p><center><span class=mainLight>".$mainErrors[1]."</span></center>";
	}
	
	iil_Close($conn);

$clock->register("done");
$exec_finish_time = microtime();
echo '<!-- execution time: '.$exec_start_time.' ~ '.$exec_finish_time.' -->';
echo "\n<!--\n";
$clock->dump();
echo "\n//-->\n";
?>
</BODY></HTML>
