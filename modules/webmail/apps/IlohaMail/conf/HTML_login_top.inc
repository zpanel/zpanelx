<?php
/********************************************************
	conf/login_title.inc
	
	PURPOSE:
		Provide central location for setting title displayed in login page (source/index.php)

********************************************************/
?>

<BODY BGCOLOR="<?php echo $bgcolor?>" TEXT="<?php echo $textColorOut?>" LINK="<?php echo $textColorOut?>" ALINK="<?php echo $textColorOut?>" VLINK="<?php echo $textColorOut?>" onLoad="document.forms[0].user.focus();">
	<p><BR><BR>
	<center>
	<br>&nbsp;<h2>Welcome to webmail!</h2>
	&nbsp;<b> Version <?php echo $version."-".$v_stat; ?></b>
	<br><br>
