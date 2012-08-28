<?php

/**
 * Plugin to help support mutli-server setups
 * 
 * 1. Catches edits to Config.php and instead
 *    updates the DB so that all Config data
 *    is centrally located in the DB
 * 
 * 2. Extracts Config data from DB so that all the 
 *    Config vars usually defined in Config.php
 *    are set as normal.
 * 
 * 3. Handles the necesary differences in the license
 *    checking for multi-server setups
 * 
 * NOTE: Database details must still be contained
 *       in Config.php and edited on each server
 *       manually when necessary.
 *
 */
class MultiServerPlugin extends Plugin
{
	/**
	 * Transparently extract and set up the variables
	 * normally found in Config.php
	 *
	 * @param array $args
	 */
	function onCreate(&$args)
	{
		$this->db = new SQL;
		
		// Grab Config from the database
		$rows = $this->db->sqlmultihash('select * from Config');
		
		foreach ($rows as $row) {
			$array = $row['array'];
			
			if ($row['type'] == 1) {
				$row['value'] = unserialize($row['value']);
			}
			
			global $$array;
			
			if ($array == 'pref') {
				$pref[$row['arraykey']] = $row['value'];
			} elseif ($array == 'brand') {
				$brand[$row['arraykey']] = $row['value'];
			} elseif ($array == 'settings') {
				$settings[$row['arraykey']] = $row['value'];
			} elseif ($array == 'domains') {
				$domains[$row['arraykey']] = $row['value'];
			} elseif ($array == 'groups') {
				$groups[$row['arraykey']] = $row['value'];
			} elseif ($array == 'language') {
				$language[$row['arraykey']] = $row['value'];
			} elseif ($array == 'reserved') {
				$reserved[$row['arraykey']] = $row['value'];
			} elseif ($array == 'reg') {
				$reg[$row['arraykey']] = $row['value'];
			}
		}

	}
	
	
	/**
	 * Handle the differences for multi-server
	 * license checking
	 *
	 * @param array $args
	 */
	function onCHS(&$args)
	{
		global $kdkl, $reg;

		$hosts = $this->db->sqlgetfield('select value from Config where array = "reg" and arraykey = "hostnames"');

		$hosts = explode(',', $hosts);
		preg_match('/.{4}(\d)/', $reg['serial'], $m);
		if (count($hosts) > intval($m[1])) {
			$kdkl = '';
		}
		
		$reg['serial'] = substr($reg['serial'], 0, 4) . substr($reg['serial'], 5);
		
		if (in_array(gethostbyaddr($_SERVER['HTTP_HOST']), $hosts)) {
			$kdkl = $this->db->sqlgetfield('select value from Config where array = "reg" and arraykey = "master"');
		}
	}
	
	/**
	 * Handle the writing of Config data to the
	 * database rather than Config.php
	 *
	 * @param array $args
	 */
	function onWriteConf(&$args)
	{
		global $pref, $settings, $brand, $domains, $groups, $language, $reserved, $reg, $return;
		
		// Write the config into the DB
		foreach (array('pref', 'settings', 'brand', 'domains', 'groups', 'language', 'reserved', 'reg') as $name) {
			
			if (is_array($$name)) {
				foreach ($$name as $k => $v) {
					if (is_array($v)) {
						$v = serialize($v);
						$type = 1;
					} else {
						$type = 0;
					}
					
					if ($this->db->sqlgetfield('select count(*) from Config where array = ? and arraykey = ?', array($name, $k))) {
						$this->db->sqldo('update Config set value = ?, type = ? where array = ? and arraykey = ?', array($v, $type, $name, $k));
					} else {
						$this->db->sqldo('insert into Config values (?, ?, ?, ?)', array($name, $k, $v, $type));
					}
				}
			}
		}
		
		$args[0] = true;
	}
}
