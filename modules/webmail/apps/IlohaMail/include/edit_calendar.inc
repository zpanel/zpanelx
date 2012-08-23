<?php
/********************************************************
	include/edit_calendar.inc
	
	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Handle calendar item edits and deletes

********************************************************/

include_once("../conf/db_conf.php");
include_once("../include/idba.$DB_TYPE.inc");
include_once("../include/array2sql.inc");

$db = new idba_obj;
if (!$db->connect()){
	echo "DB connection failed.";
	exit;
}

if (isset($edit_cal)){
	$error = "";
	
	$beginDate = formatCalDate($start_month, $start_day, $start_year);
	if (!$beginDate) $error .= "Invalid beginning date\n";
	$endDate = formatCalDate($end_month, $end_day, $end_year);
	if (!$endDate) $error .= "Invalid ending date\n";
	
	$beginTime = ($start_hour * 100) + $start_minute;
	$endTime = ($end_hour * 100) + $end_minute;
	
	$pattern = "";
	if (count($repeat_d)>0){
		$pattern = "d:";
		while (list($k,$d) = each($repeat_d)) $pattern .= "$k,";
	}
	if (count($repeat_w)>0){
		$pattern .= " ";
		while (list($k,$d) = each($repeat_w)) $pattern .= "w$k,";		
	}else if (count($repeat_d)>0){
		$pattern .= " w:all";
	}
	if ($repeat_monthly) $pattern .= " m:".substr($beginDate, 6);
	if ($repeat_yearly) $pattern .= " y:".substr($beginDate, 4);
	
	$data["userID"] = $session_dataID;
	$data["title"] = $title;
	$data["place"] = $place;
	$data["description"] = $description;
	$data["participants"] = $participants;
	$data["beginTime"] = $beginTime;
	$data["endTime"] = $endTime;
	$data["beginDate"] = $beginDate;
	$data["endDate"] = $endDate;
	$data["pattern"] = $pattern;
	$data["color"] = $color;
	
	//echo implode(",",$data);
	
	$sql = Array2SQL($DB_CALENDAR_TABLE, $data, ($edit > 0 ? "UPDATE":"INSERT"));
	if ($edit>0) $sql.= " WHERE id=$edit and userID=$session_dataID";
	
	$backend_result = $db->query($sql);
	
	$date = $beginDate;
}else if (isset($delete_cal)){
    $backend_query = "DELETE FROM $DB_CALENDAR_TABLE WHERE userID='$session_dataID' and id='$edit'";;
	$backend_result = $db->query($backend_query);	
}

?>