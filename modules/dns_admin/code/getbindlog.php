<body bgcolor="#000000">
<font color="#009900">
<?php
if (isset($_POST['inBindLog'])){
	$bindlog = $_POST['inBindLog'];
	$logerror = array();
	$logwarning = array();
	$getlog = array();
	if (file_exists($bindlog)){
		$handle = @fopen($bindlog, "r");
		$getlog = array();
		if ($handle) { 
	    	while (!feof($handle)) {
	       	$buffer = fgets($handle, 4096);
			$getlog[] = $buffer;
				if (strstr($buffer,'error:') || strstr($buffer,'error ')){
	       			$logerror[] = $buffer;
				}
				if (strstr($buffer,'warning:') || strstr($buffer,'warning ')){
	       			$logwarning[] = $buffer;
				}
	    	}fclose($handle);
		}
	}


if (isset($_POST['inViewErrors'])){
	echo "<font color=\"#FFF\"><h2>BIND Errors:</h2></font>";
	foreach ($logerror as $logline){
		$logline = str_replace("error", "<font color=\"#CC0000\">error</font>", $logline);
		echo $logline . "<br>";		
	}
}

if (isset($_POST['inViewWarnings'])){
	echo "<font color=\"#FFF\"><h2>BIND Warnings:</h2></font>";
	foreach ($logwarning as $logline){
		$logline = str_replace("warning", "<font color=\"#FFFF99\">warning</font>", $logline);
		echo $logline . "<br>";		
	}
}

if (isset($_POST['inViewLogs'])){
	echo "<font color=\"#FFF\"><h2>BIND Full Logs:</h2></font>";
	foreach ($getlog as $logline){
		if (strstr($logline, "succeeded") || strstr($logline, "SIGHUP")){
			$logline = "<font color=\"#00FF00\">" . $logline. "</font>";
		}
		if (strstr($logline, "error")){
			$logline = "<font color=\"#CC0000\">" . $logline. "</font>";
		}
		if (strstr($logline, "Failed")){
			$logline = "<font color=\"#AAAAAA\">" . $logline. "</font>";
		}
		if (strstr($logline, "warning")){
			$logline = "<font color=\"#FFFF99\">" . $logline. "</font>";
		}
		echo $logline . "<br>";	
	}
}

} else {
	
}
?>
</font>
</body>