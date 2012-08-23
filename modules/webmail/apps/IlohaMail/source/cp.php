<?php
/////////////////////////////////////////////////////////
//	
//	source/cp.php
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
	PURPOSE:
		Display color grid and corresponding color codes
	PRE-CONDITIONS:
		$user - Session ID
	COMMENTS:
********************************************************/

	include("../include/super2global.inc");

	include("../include/header_main.inc");
	include("../lang/".$my_prefs["lang"]."cp.inc");

	$r=array("00", "33", "66", "99", "CC", "FF");
	$g=array("00", "33", "66", "99", "CC", "FF");
	$b=array("00", "33", "66", "99", "CC", "FF");

	echo "<p><table>";
	while (list($rkey, $r_code) = each ($r)){
		reset($g);
		while (list($gkey, $g_code) = each ($g)){
			echo "<tr>";
			reset($b);
			while (list($bkey, $b_code) = each ($b)){
				$code="#".$r_code.$g_code.$b_code;
				echo "<td>$code</td><td width=60 bgcolor=\"$code\"></td>";
			}
			echo "</tr>\n";
		}
	}
	echo "</table>";
	
?>
</body></html>