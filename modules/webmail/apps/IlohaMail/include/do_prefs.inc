<?php
/*	
	File: include/do_prefs.inc

	Copyright: 		2004 Ryo Chijiiwa <Ryo@IlohaMail.org>
	License: 		see http://www.fsf.org/copyleft/gpl.html
	Purpose:  		Save prefs to backend
	Precondition: 	Include from index.php, data POST'd from prefs.php
*/
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
		if (!$DS->write("prefs", $my_prefs)) $error.= $DS->error;
		
   	    //display prefs page again
       	$show_page="prefs";
			
		//show error if any
		if (!empty($error)){
			echo "<body>ERROR: $error</body></html>";
			exit;
		}	
	}
?>