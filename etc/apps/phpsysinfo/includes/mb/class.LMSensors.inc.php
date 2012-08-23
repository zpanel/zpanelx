<?php 
/**
 * lmsensor sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.LMSensors.inc.php 316 2009-09-03 08:09:18Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from lmsensor
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class LMSensors extends Sensors
{
    /**
     * content to parse
     *
     * @var array
     */
    private $_lines = array();
    
    /**
     * fill the private content var through tcp or file access
     */
    function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            if (CommonFunctions::executeProgram("sensors", "", $lines)) {
                // Martijn Stolk: Dirty fix for misinterpreted output of sensors,
                // where info could come on next line when the label is too long.
                $lines = str_replace(":\n", ":", $lines);
                $lines = str_replace("\n\n", "\n", $lines);
                $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            }
            break;
        case 'file':
            if (CommonFunctions::rfts(APP_ROOT.'/data/lmsensors.txt', $lines)) {
                $lines = str_replace(":\n", ":", $lines);
                $lines = str_replace("\n\n", "\n", $lines);
                $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
            }
            break;
        default:
            $this->error->addConfigError('__construct()', 'PSI_SENSOR_ACCESS');
            break;
        }
    }
    
    /**
     * get temperature information
     *
     * @return void
     */
    private function _temperature()
    {
        $ar_buf = array();
        foreach ($this->_lines as $line) {
            $data = array();
            if (preg_match("/(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*)\((.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } else {
                (preg_match("/(.*):(.*)/", $line, $data));
            }
            if (count($data) > 1) {
                $temp = substr(trim($data[2]), -1);
                switch ($temp) {
                case "C":
                case "F":
                    array_push($ar_buf, $line);
                }
            }
        }
        foreach ($ar_buf as $line) {
            $data = array();
            if (preg_match("/(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)\)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*).C[ ]*\((.*)=(.*).C,(.*)=(.*).C\)(.*)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*).C[ ]*\((.*)=(.*).C\)(.*)/", $line, $data)) {
                ;
            } else {
                (preg_match("/(.*):(.*).C/", $line, $data));
            }
            foreach ($data as $key=>$value) {
                if (preg_match("/^\+?([0-9\.]+).?$/", trim($value), $newvalue)) {
                    $data[$key] = trim($newvalue[1]);
                } else {
                    $data[$key] = trim($value);
                }
            }
            $dev = new SensorDevice();
            $dev->setName($data[1]);
            $dev->setValue($data[2]);
            $limit = 0;
            $perce = 0;
            if (isset($data[6]) && $data[2] > $data[6]) {
                $limit = 75;
                $perce = 75;
            } else {
                $limit = isset($data[4]) ? $data[4] : 75;
                $perce = isset($data[6]) ? $data[6] : 75;
            }
            if ($limit < $perce) {
                $dev->setMax($perce);
            } else {
                $dev->setMax($limit);
            }
            $this->mbinfo->setMbTemp($dev);
        }
    }
    
    /**
     * get fan information
     *
     * @return void
     */
    private function _fans()
    {
        $ar_buf = array();
        foreach ($this->_lines as $line) {
            $data = array();
            if (preg_match("/(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*)\((.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } else {
                preg_match("/(.*):(.*)/", $line, $data);
            }
            if (count($data) > 1) {
                $temp = preg_split("/ /", trim($data[2]));
                if (count($temp) == 1) {
                    $temp = preg_split("/\xb0/", trim($data[2]));
                }
                if (isset($temp[1])) {
                    switch ($temp[1]) {
                    case "RPM":
                        array_push($ar_buf, $line);
                    }
                }
            }
        }
        foreach ($ar_buf as $line) {
            $data = array();
            if (preg_match("/(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)\)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*) RPM  \((.*)=(.*) RPM,(.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*) RPM  \((.*)=(.*) RPM\)(.*)/", $line, $data)) {
                ;
            } else {
                preg_match("/(.*):(.*) RPM/", $line, $data);
            }
            $dev = new SensorDevice();
            $dev->setName(trim($data[1]));
            $dev->setValue(trim($data[2]));
            if (isset($data[4])) {
                $dev->setMin(trim($data[4]));
            }
            $this->mbinfo->setMbFan($dev);
        }
    }
    
    /**
     * get voltage information
     *
     * @return void
     */
    private function _voltage()
    {
        $ar_buf = array();
        foreach ($this->_lines as $line) {
            $data = array();
            if (preg_match("/(.*):(.*)\((.*)=(.*),(.*)=(.*)\)(.*)/", $line, $data)) {
                ;
            } else {
                preg_match("/(.*):(.*)/", $line, $data);
            }
            if (count($data) > 1) {
                $temp = preg_split("/ /", trim($data[2]));
                if (count($temp) == 1) {
                    $temp = preg_split("/\xb0/", trim($data[2]));
                }
                if (isset($temp[1])) {
                    switch ($temp[1]) {
                    case "V":
                        array_push($ar_buf, $line);
                    }
                }
            }
        }
        foreach ($ar_buf as $line) {
            $data = array();
            if (preg_match("/(.*)\:(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)\)/", $line, $data)) {
                ;
            } elseif (preg_match("/(.*):(.*) V  \((.*)=(.*) V,(.*)=(.*) V\)(.*)/", $line, $data)) {
                ;
            } else {
                preg_match("/(.*):(.*) V$/", $line, $data);
            }
            foreach ($data as $key=>$value) {
                if (preg_match("/^\+?([0-9\.]+)$/", trim($value), $newvalue)) {
                    $data[$key] = trim($newvalue[1]);
                } else {
                    $data[$key] = trim($value);
                }
            }
            if (isset($data[1])) {
                $dev = new SensorDevice();
                $dev->setName($data[1]);
                $dev->setValue($data[2]);
                if (isset($data[4])) {
                    $dev->setMin($data[4]);
                }
                if (isset($data[6])) {
                    $dev->setMax($data[6]);
                }
                $this->mbinfo->setMbVolt($dev);
            }
        }
    }
    
    /**
     * get the information
     *
     * @see PSI_Interface_Sensor::build()
     *
     * @return Void
     */
    function build()
    {
        $this->_temperature();
        $this->_voltage();
        $this->_fans();
    }
}
?>
