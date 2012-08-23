<?php
include('../conf/plugins.php');
$plugin = $_GET['plugin'];
$plugin = eregi_replace('[^a-zA-Z0-9_-]','',$plugin);
$path = PLUGIN_DIR.'/'.$plugin.'/'.$plugin.'_prefs.php';
if (file_exists($path)){
	include($path);
}else{
	echo "Failed to load: ".$path;
}
?>