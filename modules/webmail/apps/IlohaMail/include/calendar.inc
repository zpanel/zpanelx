<?php
/********************************************************
	include/calendar.inc
	
	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		Some common code for calendar feature

********************************************************/

class scheduleItem{
	var $id;
	var $title;
	var $place;
	var $description;
	var $participants;
	var $beginTime;
	var $endTime;
	var $color;
}

function formatCalDate($month, $day, $year){
	$error = 0;
	$result = false;

	if (checkdate($month, $day, $year)){
		if (($day < 10) && (strlen($day)==1)) $day = "0".$day;
		if (($month < 10) && (strlen($month)==1)) $month = "0".$month;
		$result = $year.$month.$day;
	}
	
	return $result;
}

?>