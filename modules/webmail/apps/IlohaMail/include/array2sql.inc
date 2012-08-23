<?php
/*
    array2sql.inc - Convert associative array into sql request
    
    (C)Copyright 2002 Ryo Chijiiwa
    This file is part of IlohaMail, released under GPL
    
    Params:
        $table - Table name
        $array - Array, key=field name, val=data
        $action - String: "INSERT" or "UPDATE"
*/

function Array2SQL($table, $array, $action){
    $fields="";
    $vals="";
    $sql="";
    
    if (strcasecmp($action,"INSERT")==0){
        reset($array);
        while ( list($field, $val) = each($array) ){
            $fields .= (!empty($fields)?",":"").$field;
            $vals .= (!empty($vals)?",":"")."'".$val."'";
        }
        
        $sql = "INSERT INTO $table ($fields) VALUES ($vals)";
   
     }else if (strcasecmp($action, "UPDATE")==0){
        reset($array);
        while ( list($field, $val) = each($array) )
            $sql .= (!empty($sql)?",":"")."$field='$val'";
        
        $sql = "UPDATE $table SET ".$sql;
    }
    
    return $sql;
}


?>