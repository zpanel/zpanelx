<?php
/*
	File:		main.xml.php
	Purpose:	Equivalent to main.php, except output XML data
	Author:		Ryo Chijiiwa, ilohamail.org
	License:	GPL (part of IlohaMail)
*/

include('../include/stopwatch.inc');
$clock = new stopwatch(true);
include_once("../include/super2global.inc");
include_once("../include/nocache.inc");
include_once("../include/session_auth.inc");
include_once("../lang/".$my_prefs["charset"].".inc");
include_once("../include/ryosimap.inc");
include_once('../include/ryosdates.inc');
include_once('../lang/'.$my_prefs['lang'].'dates.inc');


/////////////////
//	FUNCTIONS
/////////////////

function headers2tree(&$headers, &$t_num_kids){
	
}

function array2xml($a){
	$out='';
	foreach($a as $label=>$data){
		if (is_numeric($label)) $label='i'.$label;
		if (is_array($data)) $out.='<'.$label.'>'.array2xml($data).'</'.$label.'>';
		else $out.='<'.$label.'>'.htmlspecialchars($data).'</'.$label.'>';
	}
	return $out;
}

function header2xml(&$header, &$t_num_kids, $print_r=false){
	global $folder, $showto, $selected_boxes;

	$a = main_packageHeader($folder, $header, $t_num_kids, $showto, $selected_boxes);

	if ($print_r){
		print_r($a);
		return;
	}

	echo '<msg>';
	echo array2xml($a);
	echo '</msg>'."\r\n";
}

function headers2xml(&$headers,&$t_num_kids){
	global $my_prefs;
	global $MAIN_CONTEXT;
	global $total_num, $start, $next_start, $prev_start, $num_show;
	global $user,$folder,$submit,$checkboxes,$selected_boxes,$report;
	
	header('Content-type: text/xml');
	echo '<?xml version="1.0" encoding="'.$my_prefs['charset'].'"?>'."\r\n";
	echo '<data>';

	$rows = count($headers);
	$fuid = ($headers[0]->uid?$headers[0]->uid:$headers[0]->id);
	$luid = ($headers[$rows-1]->uid?$headers[$rows-1]->uid:$headers[$rows-1]->id);

	if ($fuid==$_GET['fuid']&&$luid==$_GET['luid']){
		//no change
		echo '<type></type></data>';
		return;
	}
	
	echo '<type>m</type>';
	echo '<ctx>';
	echo '<folder>'.$folder.'</folder>';
	echo '<tnum>'.$total_num.'</tnum>';
	echo '<user>'.$user.'</user>';
	echo '<start>'.$start.'</start>';
	echo '<next>'.$next_start.'</next>';
	echo '<prev>'.$prev_start.'</prev>';
	echo '<show>'.$num_show.'</show>';
	echo '<rows>'.$rows.'</rows>';
	echo '<submit>'.$submit.'</submit>';
	echo '<report>'.$report.'</report>';
	echo '<cbs>'.(is_array($selected_boxes)?implode(',',$selected_boxes):'').'</cbs>';
	if ($headers[0]->uid) echo '<uid>1</uid>';
	else echo '<uid>0</uid>';
	echo '<fuid>'.$fuid.'</fuid>';
	echo '<luid>'.$luid.'</luid>';
	echo '<raw>'.array2xml($MAIN_CONTEXT).'</raw>';
	echo '</ctx>';
	echo '<messages>';
	foreach($headers as $header){
		header2xml($header, $t_num_kids);
	}
	echo '</messages>';
	echo '</data>'."\r\n";
}

function printheaders(&$headers, &$t_num_kids){
	foreach($headers as $header){
		header2xml($header, $t_num_kids, true);
	}
}



/////////////////
//	LOAD CORE
//		The main_core file processes actions, 
//		creates message indices, and fetches headers
/////////////////

include('../include/main_core.inc');

/////////////////
//	OUTPUT XML
/////////////////
$clock->purge();
headers2xml($headers, $t_num_kids);


/////////////////
//	SAVE CONTEXT
/////////////////
main_contextSave($MAIN_CONTEXT, $OLD_CONTEXT);

iil_Close($conn);
?>