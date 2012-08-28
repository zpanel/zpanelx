<?php
/////////////////////////////////////////////////////////
//	
//	source/index.php
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
	FILE: source/index.php
	PURPOSE:
		1. Provides interface for logging in.
		2. Authenticates user/password for given IMAP server
		3. Initiates session
	PRE-CONDITIONS:
		$user - IMAP account name
		$password - IMAP account password
		$host - IMAP server
	COMMENTS:
		Modify form tags for "host" as required.
		For an example, if you only want the program to be used to log into specific 
		servers, you can use "select" lists (if multiple), or "hidden" (if single) tags.
		Alternatively, simply show a text box to have the user specify the server.
********************************************************/
include("../include/super2global.inc");
include("../include/nocache.inc");

$document_head = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>IlohaMail</TITLE>
		<STYLE type="text/css">
			<!--
				body
				{
					font-family: Verdana, Arial, Helvetica, sans-serif;
				}
			//-->
		</STYLE>

</HEAD>';

include_once("../include/encryption.inc");
include_once("../include/version.inc");
include_once("../include/langs.inc");
include_once("../conf/conf.inc");
include_once("../conf/login.php");

//set content type header
if (!empty($int_lang)){
	include_once("../lang/".$int_lang."init.inc");
}else{
	include_once("../lang/".$default_lang."init.inc");
}
header("Content-type: text/html; charset=".$lang_charset);

$authenticated = false;

// session not started yet
if (!isset($session) || (empty($session))){	
    //figure out lang
    if (strlen($int_lang)>2){
        //lang selected from menu (probably)
        $lang = $int_lang;
    }else{
        //default, or non-selection
        $lang = (isset($default_lang)?$default_lang:"eng/");
    }
    include_once("../conf/defaults.inc");

	//clean up
	$user = trim(chop($user));
	$host = trim(chop($host));
	$password = trim(chop($password));


	//validate host
	if (!empty($default_host)){
		if (is_array($default_host)){
			if (empty($default_host[$host])){
				$host="";
				$error .= $loginErrors[0]."<br>\n";
			}
		}else{
			if (strcasecmp($host, $default_host)!=0){
				$host="";
				$error .= $loginErrors[0]."<br>\n";
			}
		}
	}
    
	//auto append
	if ((empty($error)) && (is_array($AUTO_APPEND)) && (!empty($AUTO_APPEND[$host]))){
		if (strpos($user, $AUTO_APPEND[$host])===false) $user .= $AUTO_APPEND[$host];
	}

	//attempt to initiate session
	if ($user && $password && $host){
		include("../include/icl.inc");
		$user_name = $user;
		
		//first, authenticate against server
		$iil_conn=iil_Connect($host, $user, $password, $AUTH_MODE);
		if ($iil_conn){
			//run custom authentication code
            include("../conf/custom_auth.php");
            
			//if successful, start session
            if (empty($error)){
				if ((!isset($port))||(empty($port))) $port = 143;
                include("../include/write_sinc.inc");
                if ($new_user){
                    include("../conf/new_user.php");
					$new_user = 1;
                }else{
					$new_user = 0;
				}
				$authenticated = true;
			}
            
			iil_Close($iil_conn);
		}else{
			$error = $iil_error."<br>";
		}
		
		//make log entry
		$log_action = "log in";
		include("../include/log.inc");
	}
}


// valid session
$login_success = false;
if ((isset($session)) && ($session != "")){
	$user=$session;
	
    //load session data
	include("../include/session_auth.inc");
	include("../conf/defaults.inc");
	
	//authenticate
	if (!$authenticated){
		include_once("../include/icl.inc");
		$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
		if ($conn){
			iil_Close($conn);
		}else{
			echo "Authentication failed.";
			echo "</html>\n";
			exit;
		}
	}

	//do prefs (posted from "Prefs" pane so that changes apply to all frames)
	if ($do_prefs){
		//check charset (change to default if unsupported)
		include_once("../lang/".$lang."init.inc");
		if (!empty($charset)){ 
			if (!$supported_charsets[$charset]) $charset = $lang_charset;
		}else{
			$charset = $lang_charset;
		}

		//apply changes...
		if (isset($apply)) $update=true;
		if ((isset($update))||(isset($revert))){
			//check rootdir
			if ($rootdir=="-") $rootdir = $rootdir_other;
		
			//initialize $my_prefs
			$my_prefs=$init["my_prefs"];
			
			//if updating, over-write values
			if (isset($update)){
				reset($my_prefs);
 				while (list($key, $value) = each ($my_prefs)) {
					 $my_prefs[$key]=$$key;
					echo "<!-- $key ".$$key." -->\n";
				}
			}
		
			//save prefs to backend
			include("../include/save_prefs.inc");
		
    	    //display prefs page again
        	$show_page="prefs";
			
			//show error if any
			if (!empty($error)){
				echo "<body>ERROR: $error</body></html>";
				exit;
			}	
		}
	}
    
	//do pref_colors (posted from "Prefs:Colors" pane so that changes apply to all frames)
	if ($do_pref_colors){
		//apply changes...
		if (isset($apply)) $update=true;
		if ((isset($update))||(isset($revert))){
			//check rootdir
			if ($font_family=="other") $font_family = $font_family_other;
		
			//initialize $my_prefs
			$my_colors=$init["my_colors"];
			
			//if updating, over-write values
			if (isset($update)){
				reset($my_colors);
 				while (list($key, $value) = each ($my_colors)) {
				 	$my_colors[$key]=$$key;
					echo "<!-- $key ".$$key." -->\n";
				}
			}
		
			//save prefs to backend
			include("../include/save_colors.inc");
		
    	    //display prefs page again
        	$show_page="pref_colors";
			
			//show error...
			if (!empty($error)){
				echo "<body>ERROR: $error</body></html>";
				exit;
			}	
		}
	}

    //overwrite lang prefs if different
    if ((strlen($int_lang)>2) && (strcmp($int_lang, $my_prefs["lang"])!=0)){
        $my_prefs["lang"] = $int_lang;
        include("../lang/".$lang."init.inc");
        if ($supported_charsets[$my_prefs["charset"]]!=1) $my_prefs["charset"] = $lang_charset;
        include("../include/save_prefs.inc");
    }
    
    //figure out which page to load in main frame
	if (($new_user)||($show_page=="prefs")) $main_page = "prefs.php?user=".$session;
	else if ($show_page == "pref_colors") $main_page = "pref_colors.php?user=".$session;
	else $main_page = "main.php?folder=INBOX&user=".$session;
	
	//show document head
	echo $document_head;
	
	//show frames
    if (($my_prefs["list_folders"]==1) && ($port!=110)){
		//...with folder list
		$login_success = true;
		?>
		<FRAMESET ROWS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
			<FRAMESET COLS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="radar.php?user=<?php echo $session?>" NAME="radar" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
				<FRAME SRC="tool.php?user=<?php echo $session?>" NAME="tool" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
			</FRAMESET>
			<FRAMESET COLS="<?php echo $my_prefs["folderlistWidth"]?>,*" frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="folders.php?user=<?php echo $session?>" NAME="list1"  MARGINWIDTH=5 MARGINHEIGHT=5 NORESIZE frameborder=no border=0 framespacing=0>
				<FRAME SRC="<?php echo $main_page?>" NAME="list2" MARGINWIDTH=10 MARGINHEIGHT=10 FRAMEBORDER=no border=0 framespacing=0>
			</FRAMESET>
		</FRAMESET>
		<?php
	}else if (empty($error)){
		//...without folder list
		$login_success = true;
		?>
		<FRAMESET ROWS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
			<FRAMESET COLS="30,*"  frameborder=no border=0 framespacing=0 MARGINWIDTH="0" MARGINHEIGHT="0">
				<FRAME SRC="radar.php?user=<?php echo $session?>" NAME="radar" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
				<FRAME SRC="tool.php?user=<?php echo $session?>" NAME="tool" SCROLLING="NO" MARGINWIDTH="0" MARGINHEIGHT="0"  frameborder=no border=0 framespacing=0>
			</FRAMESET>
			<FRAME SRC="<?php echo $main_page?>" NAME="list2" MARGINWIDTH=10 MARGINHEIGHT=10 FRAMEBORDER=no border=0 framespacing=0>
		</FRAMESET>
		<?php
	}
}

//couldn't log in, show login form
if (!$login_success){
	//check for cookie...
	if ($_COOKIE["ILOHAMAIL_SESSION"]){
		$user = "";
		setcookie("ILOHAMAIL_SESSION", "");
	}

	//put together lang options
	$langOptions="<option value=\"--\">--";
	while (list($key, $val)=each($languages)) 
		$langOptions.="<option value=\"$key\" ".(strcmp($key,$int_lang)==0?"SELECTED":"").">$val\n";

	//colors...
	$bgcolor = $default_colors["folder_bg"];
	$textColorOut = $default_colors["folder_link"];
	$bgcolorIn = $default_colors["tool_bg"];
	$textColorIn = $default_colors["tool_link"];
	
	//load lang file
	if (!empty($int_lang)){
		include("../lang/".$int_lang."login.inc");
	}else{
		include("../lang/".$default_lang."login.inc");
	}
	
	//set a test cookie
	if ($USE_COOKIES){
		setcookie ("IMAIL_TEST_COOKIE", "test", time()+$MAX_SESSION_TIME, "/", $_SERVER[SERVER_NAME]);
	}
	
	//print document head
	echo $document_head;
	
	echo "\n<!-- \nSESS_KEY: $IMAIL_SESS_KEY $MAX_SESSION_TIME ".$_SERVER[SERVER_NAME]."\nOLD: $OLD_SESS_KEY\n //-->\n";
	?>
	<BODY BGCOLOR="<?php echo $bgcolor?>" TEXT="<?php echo $textColorOut?>" LINK="<?php echo $textColorOut?>" ALINK="<?php echo $textColorOut?>" VLINK="<?php echo $textColorOut?>" onLoad="document.forms[0].user.focus();">
	<p><BR><BR>
	<center>
	<form method="POST" action="index.php">
	<input type="hidden" name="logout" value=0>
	<table width="280" border="0" cellspacing="0" cellpadding="0" bgcolor="<?php echo $bgcolorIn?>">
	<tr><td align="center" colspan=2>
	<?php
		include("../conf/login_title.inc");
        if (!empty($error)) echo "<font color=\"#FFAAAA\">".$error."</font><br>";
	?>
	</td>
	</tr>
	<tr><td align=right><?php echo $loginStrings[0] ?>:</td><td><input type="text" name="user" value="<?php echo $user; ?>" size=15></td></tr>
	<tr><td align=right><?php echo $loginStrings[1] ?>: </td><td><input type="password" name="password" value="" size=15 AUTOCOMPLETE="off"></td></tr>
	<?php
		$HTTP_HOST = strtolower($_SERVER["HTTP_HOST"]);
		if (is_array($VDOMAIN_DETECT) && empty($host)){
			$host = $VDOMAIN_DETECT[$HTTP_HOST];
		}
		//empty default host
			//show text box
		//default host is array
			//show list (select $host)
		//default host is string
			//show host
			//don't show host
		if (empty($default_host)){
			echo "<tr><td align=right>".$loginStrings[2].": </td><td><input type=text name=\"host\" value=\"$host\">&nbsp;&nbsp;</td></tr>";			
		}else if (is_array($default_host)){
			echo  "<tr><td align=right>".$loginStrings[2].":</td><td><select name=\"host\">\n";
			reset($default_host);
			while ( list($server, $name) = each($default_host) ){
				echo "<option value=\"$server\" ".($server==$host?"SELECTED":"").">$name\n";
			}
			echo "</select></td></tr>";			
		}else{
			echo "<input type=hidden name=\"host\" value=\"$default_host\">";
			if (!$hide_host){
				echo  "".$loginStrings[2]." <b>$host</b>&nbsp;&nbsp;";
				echo "</td></tr>";
			}
		}
			
		//initialize default rootdir and port 
		if ((!isset($rootdir))||(empty($rootdir))) $rootdir = $default_rootdir;
		if ((!isset($port))||(empty($port))) $port = $default_port;
		
		//show (or hide) protocol selection
		if (!$hide_protocol){
			echo "<tr>";
			echo "".$loginStrings[3]." ";
            echo "<select style='visibility:hidden' name=\"port\"\n";
            echo "<option value=\"143\" ".($port==143?"SELECTED":"").">IMAP\n";
            if ($SSL_ENABLED) echo "<option value=\"993\" ".($port==993?"SELECTED":"").">IMAP-SSL\n";
            echo "<option value=\"110\" ".($port==110?"SELECTED":"").">POP3\n";
            echo "</select>\n";
			//echo "<td><input type=\"text\" name=\"port\" value=\"$port\" size=\"4\"></td>";
			echo "</td></tr>\n";
		}else{
			echo "<input type=\"hidden\" name=\"port\" value=\"$default_port\">\n";
		}
		
		//show (or hide) root dir box
		if (!$hide_rootdir){
			echo "<tr>";
			echo "<td align=\"right\">".$loginStrings[4].":</td>";
			echo "<td><input type=\"text\" name=\"rootdir\" value=\"$rootdir\" size=\"12\"></td>";
			echo "</tr>\n";
		}else{
			echo "<input type=\"hidden\" name=\"rootdir\" value=\"$default_rootdir\">\n";
		}
		
		if (!$hide_lang){
			echo "<tr><td align=right>".$loginStrings[5].": </td><td>\n";
   		 	echo "<select name=\"int_lang\">\n";
			echo $langOptions;
			echo "</select></td></tr>\n";
		}
	?>
	<tr><td align="right" colspan="2"><input type=submit value="<?php echo $loginStrings[6] ?>">&nbsp;&nbsp;<p> </td></tr>
	</table>
	</form>
	<?php
		include("../conf/login_blurb.inc");
	?>
	</center>
	</body>
	<?php
}
	?>
</HTML>