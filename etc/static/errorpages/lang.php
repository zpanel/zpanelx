<?php
if (!isset($Langue)) {
$Langue = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
$Langue = strtolower(substr(chop($Langue[0]),0,2));
}
if (file_exists("../lang/".$Langue.".php")) { 
include("../lang/".$Langue.".php");
} else { 
include("../lang/en.php");
} 
?>