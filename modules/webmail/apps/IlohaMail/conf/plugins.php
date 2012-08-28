<?php
define(PLUGIN_DIR, '../plugins/');

//initialize
$PLUGIN_HANDLERS=array();

//Contacts Importer Plugins
//key refers to file extension
//value is plugin folder name
$PLUGIN_HANDLERS['ctin']['vcf'] = 'vcard';
$PLUGIN_HANDLERS['ctin']['csv'] = 'csv';

//Contacts Exporter Plugins
//key refers to plugin folder name
//value is format name
$PLUGIN_HANDLERS['ctex']['csv'] = "CSV";
$PLUGIN_HANDLERS['ctex']['vcard'] = "vCard";


/*
Filter Plugins
	Description:
		Key refers to plugin folder name, values are:
			true if enabled, false if disabled
		Include file should be in PLUGIN_DIR/pluginname/pluginname.php
	NOTE:
		If you are not using any plugin filters, comment out ALL filter
		handler lines.  This will save the program from running extra
		code unnecessarily and will give you considerably better performance.
*/
$PLUGIN_HANDLERS['filters']['whitelist'] = true;


/*
Preference Pane Plugin
	Key is plugin name, value is an array with two fields:
	   'on' : show prefs pane
	   'label' : label to display on link
    Page should be in PLUGIN_DIR/pluginname/pluginname_prefs.php
*/
$PLUGIN_HANDLERS['prefs']['whitelist'] = array('on'=>true,'label'=>'Whitelist');


?>