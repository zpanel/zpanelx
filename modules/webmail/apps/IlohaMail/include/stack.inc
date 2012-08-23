<?php
/********************************************************
	include/stack.inc
	
	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html
	
	PURPOSE:
		A simple array based stack class.

********************************************************/

class stack{
	var $a;
	var $index;
	
	function stack(){
		$this->a = array();
		$this->index = 0;
	}
	
	function push($val){
		array_unshift($this->a, $val);
	}

	function pop(){
		$val = $this->a[0];
		array_shift($this->a);
		return $val;
	}
	
	function top(){
		return $this->a[0];
	}
	
	function reset(){
		$this->index = 0;
	}
	
	function end(){
		return (count($a) - 1);
	}
	
	function next(){
		if ($this->index == count($this->a)) return false;
		else{
			$val = $this->a[$this->index];
			$this->index++;
			return $val;
		}
	}
	
	function clean(){
		$this->a = array();
	}
	
	function dump(){
		return implode(",", $this->a);
	}
}
?>