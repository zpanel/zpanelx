<?php

require_once(PLUGIN_DIR.'/vcard/Contact_Vcard_Parse.php');
$VCARD_FIELDS=array(
				"name" => 3,
				"email" => 4,
				"email2" => 12,
				"grp" => 6,
				"aim" => 0,
				"icq" => 0,
				"yahoo" => 0,
				"msn" => 0,
				"jabber" => 0,
				"phone" => 8,
				"work" => 9,
				"cell" => 10,
				"url" => 5,
				"comments" => 7,
				"firstname" => 15,
				"lastname" => 16,
				"street" => 17,
				"extended" => 0,
				"city" => 18,
				"region" => 19,
				"postalcode" => 20,
				"country" => 21
		);
		
function ctin_vcard_init(){
}

function split_quoted_str($delim, $line){
	$len = strlen($line);
	$in_quotes = false;
	$out = array(0=>"");
	$num_tokens = 0;
	for ($i=0;$i<$len;$i++){
		$c = (string)$line[$i];
		if ($c=='"'){
			$out[$num_tokens].=$c;
			$in_quotes = !$in_quotes;
		}else if ($c=="\\"){
			$i++;
			$out[$num_tokens].=$c.$line[$i];
		}else if (!$in_quotes && $c==$delim){
			$num_tokens++;
			$out[$num_tokens]="";
		}else $out[$num_tokens].=$c;
	}
	return $out;
}

function csv_parse($data){
	$lines = split_quoted_str("\n", $data);
	
	if (!is_array($lines)) return array();
	
	//print_r($lines);
	
	foreach($lines as $line){
		$out[] = split_quoted_str(",", chop($line));
	}
	return $out;
}

function csv_row_to_options($row){
	echo '<option value="0">--'."\n";
	foreach($row as $i=>$col){
		echo '<option value="'.($i+1).'">'.$col."\n";
	}
}

function ctin_csv_confirm($file, $filename, &$dm){
	global $user, $VCARD_FIELDS;	//session ID
	global $ecStrings;
	
	$fp = fopen($file, "r");
	if (!$fp) return true;
	$csv = fread($fp, filesize($file));
	fclose($fp);
	
	$data = csv_parse($csv);
	//print_r($data);
	$num = count($data);
	if ($num==0){
		echo '<span class="mainLight">';
		echo '<p>No valid CSV data found in file.';
		echo '</span>';
		exit;
	}
	echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="hidden" name="user" value="'.$user.'">';
	echo '<input type="hidden" name="file" value="'.$filename.'">';
	echo '<input type="hidden" name="func_name" value="import">';
	echo '<span class="mainLight">';
	echo 'Import '.$num.' records?';
	echo '<p>Start on row: <input type="text" name="start_row" value="2">';
	echo '<table>';
	foreach($VCARD_FIELDS as $field_name=>$str_num){
		if ($str_num>0) $label = $ecStrings[$str_num];
		else $label = $field_name;
		
		echo '<tr><td class="mainLight">'.$label.':</td>';
		echo '<td><select name="field_map['.$field_name.']">'."\n";
		csv_row_to_options($data[0]);
		echo '</select></td></tr>'."\n";
	}
	echo '</table>';
	echo '</span>';
	echo '<input type="submit" name="submit" value="Import">';
	echo '</form>';
	
	return false; //not done
	//print_r($data);
}



function ctin_csv_import($file, $filename, &$dm){
	global $session_dataID;
	global $VCARD_FIELDS;
	global $start_row;
	global $field_map;
	
	$fp = fopen($file, "r");
	if (!$fp) return true;
	$csv = fread($fp, filesize($file));
	fclose($fp);
	
	$data = csv_parse($csv);
	$num_rows = count($data);
	$start_row--;
	
	for ($i=$start_row;$i<$num_rows;$i++){
		$a = array();
		$a["owner"] = $session_dataID;
		foreach($VCARD_FIELDS as $field_name=>$blah){
			$col_num = $field_map[$field_name];
			if ($col_num==0) continue;
			$col_num--;
			$cell = $data[$i][$col_num];
			if (ereg("^\"[^\"]*\"$", $cell)) $cell = substr($cell, 1, -1);
			$a[$field_name] = addslashes($cell);
		}
		if ($dm->insert($a)) $imported++;
		//print_r($a);
	}
	
	echo '<p><span class="mainLight">Successfully imported '.$imported.' items.</span>';
	
	return true; //done
}

?>