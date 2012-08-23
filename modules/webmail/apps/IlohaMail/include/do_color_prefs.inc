<?php
/*	
	File: include/do_color_prefs.inc

	Copyright: 		2004 Ryo Chijiiwa <Ryo@IlohaMail.org>
	License: 		see http://www.fsf.org/copyleft/gpl.html
	Purpose:  		Save color prefs
	Precondition: 	Include from index.php, data POST'd from pref_colors.php
*/

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
		$DS->write("colors", $my_colors);
		
		//display prefs page again
        $show_page="pref_colors";
			
		//show error...
		if (!empty($error)){
			echo "<body>ERROR: $error</body></html>";
			exit;
		}	
	}

?>