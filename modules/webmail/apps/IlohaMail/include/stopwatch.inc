<?php

class stopwatch{
	var $entries;
	
	function stopwatch(){
		$this->entries = array();
	}
	
	function register($message){
		$entry_a["time"] = microtime();
		$entry_a["message"] = $message;
		array_push($this->entries, $entry_a);
	}
	
	function dump(){
		reset($this->entries);
		while ( list($k, $blah) = each($this->entries) ){
			$a = $this->entries[$k];
			$str = $a["time"];
			$space_pos = strpos($str, " ");
			$seconds = substr($str, $space_pos+1);
			$microsec = substr($str, 1, $space_pos - 2);
			echo $seconds.$microsec.":".$a["message"]."\n";
		}
	}
}

?>