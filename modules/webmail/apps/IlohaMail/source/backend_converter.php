<?php

header("Content-type: text/plain");

include("../conf/db_conf.php");
include("../include/idba.$DB_TYPE.inc");

if ($backend=="FS"){
	"File-based backend selected.  This importer is not required for file-based backends.";
	exit;
}
if (empty($DB_DS_TABLE)){
	echo "DB_DS_TABLE not set in conf/db_conf.php.\n";
	exit;
}

$db = new idba_obj;
$db->connect();

echo "!!!!!!!!   WARNING WARNING WARNING !!!!!!!!\n\n";
echo "DELETE OR OTHERWISE REMOVE THIS FILE FROM PUBLIC ACCESS ONE IMPORT IS COMPLETE.\n\n\n";

$sql = "SELECT * FROM $DB_COLORS_TABLE ORDER BY id";
$result = $db->query($sql);
if ($result){
	while($a = $db->fetch_row($result)){
		$id = $a["id"];
		$colors[$id] = $a;
	}
	echo "Read ".count($colors)." records from \"$DB_COLORS_TABLE\"\n";
}else{
	echo "Failed to read from \"$DB_COLORS_TABLE\": ".$db->error()."\n";
	exit;
}

$sql = "SELECT * FROM $DB_PREFS_TABLE ORDER BY id";
$result = $db->query($sql);
if ($result){
	while($a = $db->fetch_row($result)){
		$id = $a["id"];
		$prefs[$id] = $a;
	}
	echo "Read ".count($prefs)." records from \"$DB_PREFS_TABLE\"\n";
}else{
	echo "Failed to read from \"$DB_PREFS_TABLE\": ".$db->error()."\n";
	exit;
}

reset($prefs);
while(list($id,$pref)=each($prefs)){
	$pref_data = serialize($prefs[$id]);
	$sql = "INSERT INTO $DB_DS_TABLE (owner,ds_key,ds_data)";
	$sql.= " VALUES ('$id', 'prefs', '$pref_data')";
	$db->query($sql);
	echo "Dumped prefs";

	$color_data = serialize($colors[$id]);
	$sql = "INSERT INTO $DB_DS_TABLE (owner,ds_key,ds_data)";
	$sql.= " VALUES ('$id', 'colors', '$color_data')";
	$db->query($sql);
	echo " and colors for $id\n";
}

?>