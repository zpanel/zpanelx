<?php
/////////////////////////////////////////////////////////
//	
//	source/folders.php
//
//	(C)Copyright 2000-2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/folders.php
	PURPOSE:
		Display a list of folders, with links to main.php (which lists the contents of the folder).
		Also provides link to edit_folders.php (for creating/deleting/renaming folders).
	PRE-CONDITIONS:
		$user - Session ID
	COMMENTS:
		This is the klunkiest hack in all of IlohaMail...it's been rewritten at least half a dozen times.
		Latest revision includes support for expanding/collapsing folders that have sub-folders.
		It's messy as all hell because some IMAP servers (*cough* Netscape *cough*) don't return
		container folders for some reason.  In other words, if you create "folder/sub/subsub", it won't
		return "folder/sub" automatically.
		
********************************************************/
include("../include/super2global.inc");
include("../include/nocache.inc");

function getFolderStates(){
	global $loginID, $host;
	
	$data = cache_read($loginID, $host, "folder_states");

	if (!$data) return array("INBOX");
	else{
		return $data;
	}
}

function saveFolderStates($folders){
	global $loginID, $host;
	
	$result = cache_write($loginID, $host, "folder_states", $folders, false);
	return $result;
}

function removeFolders($array){
	if ((!is_array($array)) || (count($array)==0)) return true;
	
	$current = getFolderStates();
	if (is_array($current)){
		$save = array();
		while ( list($k,$folder)=each($current) ){
			if (!in_array($folder, $array)) $save[]=$folder;
		}
		saveFolderStates($save);
	}
}

function addFolders($array){
	if ((!is_array($array)) || (count($array)==0)) return true;

	$current = getFolderStates();
	if (is_array($current)){
		$save = array_merge($current, $array);
		sort($save);
		saveFolderStates($save);
	}
}

function InArray($array, $item){
	if (!is_array($array)) return false;
	else if (strcasecmp($item, "inbox")==0) return false;
	else return in_array($item, $array);
}

function ChildInArray($array, $item){
	if (!is_array($array)) return false;
    reset($array);
    while (list($k,$v)=each($array)){
		$pos = strpos($v, $item);
		if (($pos!==false) && ($pos==0)) return true;
	}
    return false;
}

function IndentPath($path, $containers, $delim){
	$containers->reset();
	$pos = strrpos($path, $delim);
	if ($pos>0){
		$folder = substr($path, $pos);
		$path = substr($path, 0, $pos);
	}
	
	do{
		$container = $containers->next();
		if ($container) $path = str_replace($container, "&nbsp;&nbsp;&nbsp;", $path);
	}while($container);
	
	return $path.$folder;
}

if (isset($user)){
	include("../include/session_auth.inc");
	include("../include/ryosimap.inc");
	include("../include/icl.inc");
	include("../include/stack.inc");
	include("../include/cache.inc");

	$linkc = $my_colors["folder_link"];
	$bgc = $my_colors["folder_bg"];
	$textc = $my_colors["folder_link"];
	$font_size = $my_colors["font_size"];
	$bodyString= '<BODY BGCOLOR="'.$bgc.'" TEXT="'.$linkc.'" LINK="'.$linkc.'" ALINK="'.$linkc.'" VLINK="'.$linkc.'">';	
?>
<HTML>
<HEAD>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=<?php echo $my_prefs["charset"]; ?>">
	<?php
	if ($my_prefs["refresh_folderlist"]){
		$interval = $my_prefs["folderlist_interval"];
		if ($MIN_FOLDERLIST_REFRESH > $my_prefs["folderlist_interval"]) $interval = $MIN_FOLDERLIST_REFRESH;
		echo '<META HTTP-EQUIV="refresh"  CONTENT="'.$interval.';URL=folders.php?user='.$user.'">';
		echo "\n";
	}
	include("../include/css.inc");
	?>
</HEAD>
<?php
	echo $bodyString;
	
	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if (!$conn)
		echo "failed";
	else{
	
	//handle emptry_trash request
	if ($empty_trash && !empty($my_prefs["trash_name"])){
		iil_C_ClearFolder($conn, $my_prefs["trash_name"]);
	}

	//show heading
	include("../lang/".$my_prefs["lang"]."folders.inc");
	echo "<p><a href=\"folders.php?user=$user\"><b>".$fl_str["folders"]."</b></a>\n";
	echo "<br>[<a href=\"edit_folders.php?user=".$user."\" target=\"list2\">".$fl_str["manage"]."</a>]";
	echo "<br><br>";
	
	//show quota
	if ($my_prefs["show_quota"]=="f"){
		$quota = iil_C_GetQuota($conn);
		include("../lang/".$my_prefs["lang"]."quota.inc");
		include_once("../lang/".$my_prefs["charset"].".inc");
		echo "<span class=\"small\">\n";
		if ($quota) echo "<b>".$quotaStr["label"]."</b><br>".LangInsertStringsFromAK($quotaStr["full"], $quota);
		else echo "<b>".$quotaStr["label"]."</b>".$quotaStr["unknown"];
		echo "</span>\n";
		echo "<p>\n";
	}
	
	
	//get list of mailboxes
	$cleared = cache_clear($loginID, $host, "folders");
	if ($my_prefs["hideUnsubscribed"]){
		$folders = iil_C_ListSubscribed($conn, $my_prefs["rootdir"], "*");
	}else{
		$folders = iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
	}
	$wrote = cache_write($loginID, $host, "folders", $folders);
	
	
	if (!is_array($folders)){
    	echo "<b>Failed:</b> ".$conn->error."<br>\n";
	} else {	
		echo "\n<!-- cache cleared: $cleared written: $wrote $CACHE_ERROR //-->\n";
		echo "<!--\n".implode(",\n",$folders)."\n//-->\n";
	
		//get hierarchy delimiter, usually '/' or '.'
        $delim = iil_C_GetHierarchyDelimiter($conn);
		
		echo "<!-- delim: $delim //-->\n";
	
		//get list of container folders, because some IMAP server won't return them
		//e.g.  container of "folder/sub" is "folder"
		$folder_container = array();
        $containers = array();
        reset($folders);
        while ( list($k, $path) = each($folders) ){
			while (false !== ($pos = strrpos($path, $delim))){
				echo "<!-- $pos : $path : $delim //-->\n";
                $container = substr($path, 0, $pos);
                if ($containers[$container]!=1) $containers[$container]=1;
				$folder_container[$path] = $container;
				//echo "<!-- container of $path is ".$folder_container[$path]." //-->\n";
				$path = substr($path, 0, $pos);
			}
        }

		//make sure containers are in folder list
        reset($containers);
        while ( list($container, $v) = each($containers) ){
            if (!InArray($folders,$container)) array_push($folders, $container);
        }
        asort($folders);

		//handle subscribe (expand) command
		if ($subscribe){
			//subscribe folder...
			$add_list = array();
			//iil_C_Subscribe($conn, $folder);
			$v_sub[]=$folder;
			$add_list[] = $folder;
			
			//and immediate sub-folders
			$folder.=$delim;
    		reset($folders);
    		while (list($k,$v)=each($folders)){
				$pos = strpos($v, $folder);
				if (($pos!==false) && ($pos==0)){
					$pos = strrpos($v, $delim);
					if ($pos <= strlen($folder)){
						//iil_C_Subscribe($conn, $v);
						$v_sub[]=$v;
						$add_list[] = $v;
					}
				}
			}
			if (count($add_list)>0) addFolders($add_list);
		}
		
		//handle unsubscribe (collapse) command
		if ($unsubscribe){
			//subscribe folder...
			$remove_list = array();
			//iil_C_UnSubscribe($conn, $folder);
			$remove_list[] = $folder;
			
			//and all sub-folders
			$folder.=$delim;
    		reset($folders);
    		while (list($k,$v)=each($folders)){
				$pos = strpos($v, $folder);
				if (($pos!==false) && ($pos==0)){
					//$r = iil_C_UnSubscribe($conn, $v);
					$remove_list[] = $v;
				}
			}
			if (count($remove_list)>0) removeFolders($remove_list);
		}
				
		//get list of subscribed (expanded) folders
		$subscribed = getFolderStates();
		
		//make sure they exist (might've been deleted)
		$temp_subs = array();
		reset($subscribed);
		while( list($k,$path)=each($subscribed) ){
			if (in_array($path, $folders)) $temp_subs[] = $path;
		}
		$subscribed = $temp_subs;
		echo "<!-- AFTER UNSUB:\n".implode("\n", $subscribed)."\n//-->\n";
		
		//with some servers, only container folders are ignored, so we need to
		//do it the inefficient way...
		if (is_array($subscribed)){
			//make sure the container of every subscribed folder is also in list
			//curse Netscape!
			reset($subscribed);
			while( list($k,$path)=each($subscribed) ){
				//make sure every folder in path to subscribed folder is also subscribed.
				$original_path = $path;
				while(false !== ($pos = strrpos($path, $delim))){
   	            	$container = substr($path, 0, $pos);
					if (!in_array($container, $subscribed)){
						$v_sub[]=$container;
					}
					$path = substr($path, 0, $pos);
				}
				
				//make sure all folder at same level as subscribed folders are subscribed
				$path = $original_path;
				if (false !== ($pos = strrpos($path, $delim))){
					$container = substr($path, 0, $pos);
					if (!$checked_container[$container]){
						//echo "<!-- Adding all in: $container //-->\n";
						reset($folders);
						while ( list($k2, $folder)=each($folders) ){
							//is "folder" inside "container"?
							$pos = strpos($folder, $container);
							if (($pos!==false) && ($pos==0)){
								//is $folder immediately inside $container, or further down?
								$pos = strrpos($folder, $delim);
								if ($pos <= strlen($container.$delim)){
									if (!InArray($subscribed, $folder)){
										//*gasp*!  $folder is not subscribed!
										$v_sub[]=$folder;
									}
								}
							}
						}
						$checked_container[$container] = 1;
					}
				}
			}
		}
		if (is_array($v_sub)){
			while ( list($k,$v)=each($v_sub) ) if (!in_array($v, $subscribed)) $subscribed[]=$v;
		}
		if (is_array($subscribed)){
			sort($subscribed);
			reset($subscribed);
			//while ( list($k,$v)=each($subscribed) ) echo "<!-- subscribed: $v //-->\n";
		}
		
		natcasesort($folders);
		$c=sizeof($folders);
		echo "<NOBR>";

		//show default folders (i.e. Inbox, Sent, Trash)
		$unseen_str = "";
		reset ($defaults);
 		while (list($key, $value) = each ($defaults)) {
			if (($value!=".")&&(!empty($key))){
				if ($my_prefs["showNumUnread"]){
					$num_unseen = iil_C_CountUnseen($conn, $key);
					if ( $num_unseen > 0 ) $unseen_str = "&nbsp;(".$num_unseen.")";
					else $unseen_str = "";
				}
				echo "<a href=\"main.php?folder=$key&user=".$user."\" target=\"list2\">$value</a>";
				echo $unseen_str;
				if ($key==$my_prefs["trash_name"]){
					echo "&nbsp;[<a href=\"folders.php?user=".$user."&empty_trash=1\">".$fstr["expunge"]."</a>]";
				}
				echo "<br>";
			}
 		}

		echo "<br>\n";

		//indent according to depth
		$result = array();
        reset($folders);
        while ( list($k, $path) = each($folders) ){
			//we're only going to display folders that are in...
			//root level, subscribed, or in "INBOX"
			if (($folder_container[$path]==$my_prefs["rootdir"]) 
				|| (InArray($subscribed, $path))){
			//	|| ($folder_container[$path]=="INBOX")){
				
            	$a = explode($delim, $path);
            	$c = count($a);
            	$folder = $a[$c-1];
				if (strcmp($a[0], $my_prefs["rootdir"])==0) $c--;
            	if (($path[0]!=".") && ($folder[0]!=".")){
                	for($i=0;$i<($c-1);$i++) $indent[$path].="&nbsp;&nbsp;";
					$result[$path] = $folder;
            	}
			}
        }

		flush();

		//display folders
        reset($result);
        while ( list($path, $display) = each($result) ){
            if ((!empty($display)) && (($containers[$path])||(empty($defaults[$path])))){
				$key = $path;
				if ($containers[$path]){
					echo "<!-- begins with ".$path.$delim."? //-->\n";
					$is_sub = ChildInArray($subscribed, $path.$delim);
					$button = "<a href=\"folders.php?user=$user&".($is_sub?"unsubscribe":"subscribe")."=1&folder=".urlencode($path)."\" target=\"list1\">";
					$button .= "<tt>".($is_sub?"-":"+")."</tt></a>";
				}else{
					$button = "<tt>&nbsp;</tt>";
				}
				echo "<span style=\"font-size: ".$my_colors["font_size"]."; color: ".$my_colors["folder_bg"]."\"><tt>".$indent[$key]."</tt></span>";
                echo $button;
				
				$unseen_str="";
				if ($my_prefs["showNumUnread"]){
					$num_unseen = iil_C_CountUnseen($conn, $path);
					if ( $num_unseen > 0 ) $unseen_str = "&nbsp;(".$num_unseen.")";
				}
				
                $path = urlencode($path);				
				echo "<a href=\"main.php?folder=$path&user=".$user."\" target=\"list2\">".iil_utf7_decode($display).$unseen_str."</a><BR>\n";
				flush();
            }
        }
	}
	iil_Close($conn);
	}
}else{
	echo "User unspecified.";
	exit;
}
?>
</BODY></HTML>