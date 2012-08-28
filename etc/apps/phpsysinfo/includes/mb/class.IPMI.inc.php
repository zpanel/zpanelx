<?php 
/**
 * ipmi sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.IPMI.inc.php 287 2009-06-26 12:11:59Z bigmichi1 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting information from ipmitool
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class IPMI extends Sensors
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
    public function __construct()
    {
        parent::__construct();
        switch (strtolower(PSI_SENSOR_ACCESS)) {
        case 'command':
            $lines = "";
            CommonFunctions::executeProgram('ipmitool', 'sensor', $lines);
            $this->_lines = preg_split("/\n/", $lines, -1, PREG_SPLIT_NO_EMPTY);
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
    private function _temp()
    {
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/[ ]+\|[ ]+/", $line);
            if ($buffer[2] == "degrees C" && $buffer[5] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                $dev->setMax($buffer[8]);
                $this->mbinfo->setMbTemp($dev);
            }
        }
    }
    
    /**
     * get voltage information
     *
     * @return void
     */
    private function _voltage()
    {
        foreach ($this->_lines as $line) {
            $buffer = preg_split("/[ ]+\|[ ]+/", $line);
            if ($buffer[2] == "Volts" && $buffer[5] != "na") {
                $dev = new SensorDevice();
                $dev->setName($buffer[0]);
                $dev->setValue($buffer[1]);
                $dev->setMin($buffer[5]);
                $dev->setMax($buffer[8]);
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
    public function build()
    {
        $this->_temp();
        $this->_voltage();
    }
}
?>
