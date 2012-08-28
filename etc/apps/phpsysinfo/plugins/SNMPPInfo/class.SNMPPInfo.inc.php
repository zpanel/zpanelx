<?php 
/**
 * SNMPPInfo Plugin
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Plugin_SNMPPInfo
 * @author    Mieczyslaw Nalewaj <namiltd@users.sourceforge.net>
 * @copyright 2011 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.SNMPPInfo.inc.php 493 2011-08-21 16:58:32Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * SNMPPInfo Plugin, which displays battery state
 *
 * @category  PHP
 * @package   PSI_Plugin_SNMPPInfo
 * @author    Mieczyslaw Nalewaj <namiltd@users.sourceforge.net>
 * @copyright 2011 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   $Id: class.SNMPPInfo.inc.php 493 2011-08-21 16:58:32Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
class SNMPPInfo extends PSI_Plugin
{
    /**
     * variable, which holds the content of the command
     * @var array
     */
    private $_filecontent = array();
    
    /**
     * variable, which holds the result before the xml is generated out of this array
     * @var array
     */
    private $_result = array();
    
    /**
     * read the data into an internal array and also call the parent constructor
     *
     * @param String $enc encoding
     */
    public function __construct($enc)
    {
        parent::__construct(__CLASS__, $enc);
        switch (PSI_PLUGIN_SNMPPINFO_ACCESS) {
        case 'command':
                $printers = preg_split('/([\s]+)?,([\s]+)?/', PSI_PLUGIN_SNMPPINFO_DEVICES, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($printers as $printer) {
                    CommonFunctions::executeProgram("snmpwalk", "-On -c public -v 1 ".$printer." 1.3.6.1.2.1.1.5", $buffer, PSI_DEBUG);
                    if (strlen(trim($buffer)) > 0) {
                        $this->_filecontent[$printer] = $buffer;
            		CommonFunctions::executeProgram("snmpwalk", "-On -c public -v 1 ".$printer." 1.3.6.1.2.1.43.11.1.1", $buffer2, PSI_DEBUG);
                	if (strlen(trim($buffer2)) > 0) {
                    	    $this->_filecontent[$printer] = $buffer."\n".$buffer2;
			}else{
			    $this->_filecontent[$printer] = $buffer;
			}
		    }
                }
            break;
        case 'php-snmp':
            	snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
		snmp_set_oid_output_format(SNMP_OID_OUTPUT_NUMERIC);
                $printers = preg_split('/([\s]+)?,([\s]+)?/', PSI_PLUGIN_SNMPPINFO_DEVICES, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($printers as $printer) {
            	    if (! PSI_DEBUG) restore_error_handler(); 
            	    $bufferarr=snmprealwalk($printer, "public", "1.3.6.1.2.1.1.5");
		     if (! PSI_DEBUG) set_error_handler('errorHandlerPsi');
            	    if (! empty($bufferarr)) {
            		$buffer="";
            		foreach ($bufferarr as $id=>$string) {
            	    		$buffer=$buffer.$id." = ".$string."\n";
            		}
            		if (! PSI_DEBUG) restore_error_handler(); 
                	$bufferarr2=snmprealwalk($printer, "public", "1.3.6.1.2.1.43.11.1.1");    
			if (! PSI_DEBUG) set_error_handler('errorHandlerPsi');
            		if (! empty($bufferarr2)) {
            		    foreach ($bufferarr2 as $id=>$string) {
            	    		$buffer=$buffer.$id." = ".$string."\n";
            		    }
			}            		    
                	if (strlen(trim($buffer)) > 0) {
                    		$this->_filecontent[$printer] = $buffer;
			}
            	    }
                }    
            break;
        case 'data':
                $printers = preg_split('/([\s]+)?,([\s]+)?/', PSI_PLUGIN_SNMPPINFO_DEVICES, -1, PREG_SPLIT_NO_EMPTY);
                $pn=0;
                foreach ($printers as $printer) {
                    $buffer="";
                    if ((CommonFunctions::rfts(APP_ROOT."/data/SNMPPInfo{$pn}.txt", $buffer))&&(!empty($buffer))){
                        $this->_filecontent[$printer] = $buffer;
                    }
                    $pn++;
                }
                break;
            default:
                $this->global_error->addError("switch(PSI_PLUGIN_SNMPPINFO_ACCESS)", "Bad SNMPPInfo configuration in SNMPPInfo.config.php");
                break;
        }
    }
    
    /**
     * doing all tasks to get the required informations that the plugin needs
     * result is stored in an internal array
     *
     * @return void
     */
    public function execute()
    {
        if ( empty($this->_filecontent)) {
            return;
        }
	foreach ($this->_filecontent as $printer=>$result) {
	    $lines = preg_split('/\n/', $result);
	    foreach ($lines as $line) {
    		if (preg_match('/^.1.3.6.1.2.1.43.11.1.1.6.1.(.*) = STRING:\s(.*)/', $line, $data)) {
    		    $this->_result[$printer][$data[1]]['prtMarkerSuppliesDescription']=trim($data[2],"\"");
    		}
    		if (preg_match('/^.1.3.6.1.2.1.43.11.1.1.7.1.(.*) = INTEGER:\s(.*)/', $line, $data)) {
    		    $this->_result[$printer][$data[1]]['prtMarkerSuppliesSupplyUnit']=$data[2];
    		}
    		if (preg_match('/^.1.3.6.1.2.1.43.11.1.1.8.1.(.*) = INTEGER:\s(.*)/', $line, $data)) {
    		    $this->_result[$printer][$data[1]]['prtMarkerSuppliesMaxCapacity']=$data[2];
    		}
    		if (preg_match('/^.1.3.6.1.2.1.43.11.1.1.9.1.(.*) = INTEGER:\s(.*)/', $line, $data)) {
    		    $this->_result[$printer][$data[1]]['prtMarkerSuppliesLevel']=$data[2];
    		}
    		if (preg_match('/^.1.3.6.1.2.1.1.5.0 = STRING:\s(.*)/', $line, $data)) {
    		    $this->_result[$printer][0]['prtMarkerSuppliesDescription']=$data[1];
    		}
	    }
	}
    }
    
    /**
     * generates the XML content for the plugin
     *
     * @return SimpleXMLElement entire XML content for the plugin
     */
    public function xml()
    {
        foreach ($this->_result as $printer=>$markersupplies_item) {
    	    $xmlsnmppinfo_printer = $this->xml->addChild("Printer");
    	    $xmlsnmppinfo_printer->addAttribute("Device", $printer);
    	    foreach ($markersupplies_item as $marker=>$snmppinfo_item) {
    	        if ($marker==0) {
    	    	    $xmlsnmppinfo_printer->addAttribute("Name", $snmppinfo_item['prtMarkerSuppliesDescription']);
    	    	} else {
        	    $xmlsnmppinfo = $xmlsnmppinfo_printer->addChild("MarkerSupplies");
        	    if (isset($snmppinfo_item['prtMarkerSuppliesDescription'])) 
                	$xmlsnmppinfo->addAttribute("Description", $snmppinfo_item['prtMarkerSuppliesDescription']);
                    else 
                	$xmlsnmppinfo->addAttribute("Description",""); /* empty on some devices */
        	    $xmlsnmppinfo->addAttribute("SupplyUnit", $snmppinfo_item['prtMarkerSuppliesSupplyUnit']);
        	    $xmlsnmppinfo->addAttribute("MaxCapacity", $snmppinfo_item['prtMarkerSuppliesMaxCapacity']);
        	    $xmlsnmppinfo->addAttribute("Level", $snmppinfo_item['prtMarkerSuppliesLevel']);
               }            	    
	    }
        }
        return $this->xml->getSimpleXmlElement();
    }
}
?>
