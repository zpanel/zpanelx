<?php
/*	
	File: include/do_post_login.inc

	Copyright:		2004 Ryo Chijiiwa <Ryo@IlohaMail.org>
	License: 		see http://www.fsf.org/copyleft/gpl.html
	Purpose:  		Post login stuff, like showing frameset
	Precondition: 	Include from index.php
*/

	$user=$session;
	
    //load session data
	include("../include/session_auth.inc");
	include("../conf/defaults.inc");
	
	//authenticate
	if (!$authenticated){
		include_once("../include/icl.inc");
		$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
		if (!$conn){
			header('Location: index.php?user='.$loginID.'&host='.$host.'&port='.$port.'&error='.urlencode('Authentication failed.'));
			exit;
		}
	}else{
		$conn = $iil_conn;
	}

	//do prefs (posted from "Prefs" pane so that changes apply to all frames)
	if ($do_prefs){
		$js_mode = $my_prefs['js_usage'];
		include("../include/do_prefs.inc");
	}
    
	//do pref_colors (posted from "Prefs:Colors" pane so that changes apply to all frames)
	if ($do_pref_colors){
		include("../include/do_color_prefs.inc");
	}
	
	//initialize default folders...
	if ($ICL_CAPABILITY["folders"] && $CREATE_DEF_DIRS){
		include_once("../lang/".$my_prefs["lang"]."defaultFolders.inc");
		reset($defaults);
		while (list($df_name,$blah)=each($defaults)){
			if ($df_name && $df_name!="INBOX" && !iil_C_Select($conn, $df_name)){
				echo "<!-- Folder ".$df_name." does not exist: ";
				echo (iil_C_CreateFolder($conn, $df_name)?"created":"not created");
				echo "//-->\n";
			}
		}
	}

    //overwrite lang prefs if different
    if ((strlen($int_lang)>2) && (strcmp($int_lang, $my_prefs["lang"])!=0)){
        $my_prefs["lang"] = $int_lang;
        include("../lang/".$lang."init.inc");
        if ($supported_charsets[$my_prefs["charset"]]!=1) $my_prefs["charset"] = $lang_charset;
        $DS->write("prefs", $my_prefs);
    }
    
    //figure out which page to load in main frame
	if (($new_user&&!$GOTO_MAIN)||($show_page=="prefs")) $main_page = "prefs.php?user=".$session;
	else if ($show_page == "pref_colors") $main_page = "pref_colors.php?user=".$session;
	else $main_page = "main.php?folder=INBOX&user=".$session."&cmc=1";
	
	//show document head
	include("../conf/HTML_head.inc");
	if ($js_mode=="h"||$my_prefs['js_usage']=='h'){
		include("../include/js_index.inc");
	}
	
	//load theme info
	include('../source/themes/'.$my_prefs['theme'].'/info.inc');
	$toolbar_height = $THEME_VARS['toolbar_height'];
	if (!$toolbar_height) $toolbar_height = 30;
	
	//show frames
    if (($my_prefs["list_folders"]==1) && ($ICL_PROTOCOL!='POP3')){
		//...with folder list
		$login_success = true;
		?>
		<FRAMESET ROWS="<?php echo $toolbar_height?>,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
			<FRAMESET COLS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="radar.php?user=<?php echo $session?>" NAME="radar" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
				<FRAME SRC="tool.php?user=<?php echo $session?>" NAME="tool" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
			</FRAMESET>
			<FRAMESET COLS="<?php echo $my_prefs["folderlistWidth"]?>,*" frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="folders.php?user=<?php echo $session?>&amp;dofilter=no" NAME="list1"  MARGINWIDTH=5 MARGINHEIGHT=5 NORESIZE frameborder=no border=0 framespacing=0>
				<FRAME SRC="<?php echo $main_page?>" NAME="list2" MARGINWIDTH=10 MARGINHEIGHT=10 FRAMEBORDER=no border=0 framespacing=0>
			</FRAMESET>
		</FRAMESET>
		<?php
	}else if (empty($error)){
		//...without folder list
		$login_success = true;
		?>
		<FRAMESET ROWS="<?php echo $toolbar_height?>,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
			<FRAMESET COLS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="radar.php?user=<?php echo $session?>" NAME="radar" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
				<FRAME SRC="tool.php?user=<?php echo $session?>" NAME="tool" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
			</FRAMESET>
			<FRAME SRC="<?php echo $main_page?>" NAME="list2" MARGINWIDTH=10 MARGINHEIGHT=10 FRAMEBORDER=no border=0 framespacing=0>
		</FRAMESET>
		<?php
	}

?>