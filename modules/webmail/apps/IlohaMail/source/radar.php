<?php
/////////////////////////////////////////////////////////
//	
//	source/radar.php
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
	FILE: source/radar.php
	PURPOSE:
		Periodically reload and check if there are any RECENT messages in INBOX.
		If there are messages, display and icon and stop reloading.
		Otherwise, schedule another check a couple of minutes later.
	PRE-CONDITIONS:
		$user - Session ID
	COMMENTS:
		You might want to deactivate this feature if you expect large numbers of simultaneous
		users.  This page could potentially bombard your server (both HTTP and IMAP).

********************************************************/
include("../include/super2global.inc");
include("../include/nocache.inc");

if (isset($user)){
	include("../include/session_auth.inc");
	include("../include/icl.inc");

    if ($ICL_CAPABILITY["radar"]){
        $recent=iil_CheckForRecent($host, $loginID, $password, "INBOX");
		$interval = $my_prefs["radar_interval"];
		if ($interval < $MIN_RADAR_REFRESH) $interval = $MIN_RADAR_REFRESH;
        if ($recent==0){
            $output ="<script language=\"JavaScript\">\n";
            $output.="setTimeout('location=\"radar.php?user=$user\"',".$interval."000);\n";
            $output.="</script>\n";
        }else if ($recent > 0){
            $output = "<img src=\"themes/".$my_prefs["theme"]."/images/inbox.GIF\">\n";
        }
    }
	
	$linkc=$my_colors["tool_link"];
	$bgc=$my_colors["tool_bg"];
	
	//determine email address
	if (empty($my_prefs["email_address"])){
		if (empty($init_from_address))
			$title = $loginID.( strpos($loginID, "@")>0 ? "":"@".$host );
		else
			$title = str_replace("%u", $loginID, str_replace("%h", $host, $init_from_address));
	}else{
		$title = $my_prefs["email_address"];
	}
	
	
	echo "<HTML>\n<HEAD>\n";
	echo "<script type=text/javascript>\n";
	echo "function refresh(){ location=\"radar.php?user=$user\"; }\n";
	//$title = $loginID.(strpos($loginID,"@")===false?"@".$host:"");
	if ($recent > 0) $title="(!)".$title;
	?>
		var _p = this.parent;
		while (_p != this) {
			if (_p == _p.parent) { break; }
			_p = _p.parent;
		}
		_p.document.title = "<?php echo $title?>";
	<?php
	echo "</script>\n";
	echo "</HEAD>\n";
	echo '<BODY BGCOLOR="'.$bgc.'">';
	echo "\n";
	
	echo  "<!--\n";
	echo "last check: ".date("M d Y H:i:s", time())."\n";
	echo "Result was: ".$recent."\n";
	echo "Session: $user \n";
	echo "ICL_SSL: $ICL_SSL\nICL_PORT: $ICL_PORT\n";
	echo "-->\n";
	
	echo $output;
}
?>
</BODY></HTML>