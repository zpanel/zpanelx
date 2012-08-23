<?php
/********************************************************
	conf/defaults.inc
		
	PURPOSE:
		Provide central location for specifying text to be display below login form

********************************************************/

?>
<p>Powered by <a href="http://ilohamail.org">IlohaMail</a>
<?php
if (($v_stat=="Dev") || ($v_stat=="Devel")){
?>
	<p>This version is still under development.
	<br>Parts of it may break or change suddenly without warning.
	<p>File bug reports <a href="http://ilohamail.org/forum/view.php?topic_id=5">here</a> 
	or to the <a href="http://ilohamail.org/main.php?page=ML">testers mailing list</a>.
<?php
}else{
	/*
	?>
	Visit <a href="http://ilohamail.org">IlohaMail.org</a> for more information on this program.
	<?php
	*/
}

?>

