<?php
/********************************************************
	conf/defaults.inc
		
	PRE-CONDITION:
		$lang = Language to use (e.g. "eng/" or "jp/")
	PURPOSE:
		Provide central location for setting default
        values for user options.
********************************************************/
if ((!isset($lang))||(strlen($lang)<3)) $lang="eng/";
include("../lang/".$lang."init.inc");
if (!isset($lang_charset)) $lang_charset="ISO-8859-1";
if (!isset($rootdir)) $rootdir="";

if (isset($host)){
	$defaults_path="../conf/defaults.".$host.".inc";
	if (file_exists(realpath($defaults_path))){
		include($defaults_path);
		$included=true;
	}
}

if (!$included){
	include("../conf/defaults.generic.inc");
}
?>