<?php 
/**
 * Linux System Class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.Linux.inc.php 422 2011-01-21 12:42:25Z jacky672 $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * Linux sysinfo class
 * get all the required information from Linux system
 *
 * @category  PHP
 * @package   PSI_OS
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class Linux extends OS
{
    /**
     * call parent constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Hostname
     *
     * @return void
     */
    private function _hostname()
    {
        if (PSI_USE_VHOST === true) {
            $this->sys->setHostname(getenv('SERVER_NAME'));
        } else {
            if (CommonFunctions::rfts('/proc/sys/kernel/hostname', $result, 1)) {
                $result = trim($result);
                $ip = gethostbyname($result);
                if ($ip != $result) {
                    $this->sys->setHostname(gethostbyaddr($ip));
                }
            }
        }
    }
    /**
     * IP
     *
     * @return void
     */
    private function _ip()
    {
        if (PSI_USE_VHOST === true) {
            $this->sys->setIp(gethostbyname($this->_hostname()));
        } else {
            if (!($result = $_SERVER['SERVER_ADDR'])) {
                $this->sys->setIp(gethostbyname($this->_hostname()));
            } else {
                $this->sys->setIp($result);
            }
        }
    }
    /**
     * Kernel Version
     *
     * @return void
     */
    private function _kernel()
    {
        if (CommonFunctions::executeProgram('uname', '-r', $strBuf, PSI_DEBUG)) {
            $result = trim($strBuf);
            if (CommonFunctions::executeProgram('uname', '-v', $strBuf, PSI_DEBUG)) {
                if (preg_match('/SMP/', $strBuf)) {
                    $result .= ' (SMP)';
                }
            }
            if (CommonFunctions::executeProgram('uname', '-m', $strBuf, PSI_DEBUG)) {
                $result .= ' '.trim($strBuf);
            }
            $this->sys->setKernel($result);
        } else {
            if (CommonFunctions::rfts('/proc/version', $strBuf, 1)) {
                if (preg_match('/version (.*?) /', $strBuf, $ar_buf)) {
                    $result = $ar_buf[1];
                    if (preg_match('/SMP/', $strBuf)) {
                        $result .= ' (SMP)';
                    }
                    $this->sys->setKernel($result);
                }
            }
        }
    }
    /**
     * UpTime
     * time the system is running
     *
     * @return void
     */
    private function _uptime()
    {
        CommonFunctions::rfts('/proc/uptime', $buf, 1);
        $ar_buf = preg_split('/ /', $buf);
        $this->sys->setUptime(trim($ar_buf[0]));
    }
    /**
     * Number of Users
     *
     * @return void
     */
    private function _users()
    {
        if (CommonFunctions::executeProgram('who', '-q', $strBuf, PSI_DEBUG)) {
            $arrWho = preg_split('/=/', $strBuf);
            $this->sys->setUsers($arrWho[1]);
        }
    }
    /**
     * Processor Load
     * optionally create a loadbar
     *
     * @return void
     */
    private function _loadavg()
    {
        if (CommonFunctions::rfts('/proc/loadavg', $buf)) {
            $result = preg_split("/\s/", $buf, 4);
            // don't need the extra values, only first three
            unset($result[3]);
            $this->sys->setLoad(implode(' ', $result));
        }
        if (PSI_LOAD_BAR) {
            $this->sys->setLoadPercent($this->_parseProcStat('cpu'));
        }
    }
    /**
     * fill the load for a individual cpu, through parsing /proc/stat for the specified cpu
     *
     * @param String $cpuline cpu for which load should be meassured
     *
     * @return Integer
     */
    private function _parseProcStat($cpuline)
    {
        $load = 0;
        $load2 = 0;
        $total = 0;
        $total2 = 0;
        if (CommonFunctions::rfts('/proc/stat', $buf)) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^'.$cpuline.' (.*)/', $line, $matches)) {
                    $ab = 0;
                    $ac = 0;
                    $ad = 0;
                    $ae = 0;
                    sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                    $load = $ab + $ac + $ad; // cpu.user + cpu.sys
                    $total = $ab + $ac + $ad + $ae; // cpu.total
                    break;
                }
            }
        }
        // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
        if(PSI_LOAD_BAR) {
            sleep(1);
        }
        if (CommonFunctions::rfts('/proc/stat', $buf)) {
            $lines = preg_split("/\n/", $buf, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($lines as $line) {
                if (preg_match('/^'.$cpuline.' (.*)/', $line, $matches)) {
                    $ab = 0;
                    $ac = 0;
                    $ad = 0;
                    $ae = 0;
                    sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
                    $load2 = $ab + $ac + $ad;
                    $total2 = $ab + $ac + $ad + $ae;
                    break;
                }
            }
        }
        if ($total > 0 && $total2 > 0 && $load > 0 && $load2 > 0 && $total2 != $total && $load2 != $load) {
            return (100 * ($load2 - $load)) / ($total2 - $total);
        }
        return 0;
    }
    /**
     * CPU information
     * All of the tags here are highly architecture dependant.
     *
     * @return void
     */
    private function _cpuinfo()
    {
        if (CommonFunctions::rfts('/proc/cpuinfo', $bufr)) {
            $processors = preg_split('/\s?\n\s?\n/', trim($bufr));
            foreach ($processors as $processor) {
                $dev = new CpuDevice();
                $details = preg_split("/\n/", $processor, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($details as $detail) {
                    $arrBuff = preg_split('/\s+:\s+/', trim($detail));
                    if (count($arrBuff) == 2) {
                        switch (strtolower($arrBuff[0])) {
                        case 'processor':
                            if(PSI_LOAD_BAR) {
                                $dev->setLoad($this->_parseProcStat('cpu'.trim($arrBuff[1])));
                            }
                            break;
                        case 'model name':
                        case 'cpu':
                            $dev->setModel($arrBuff[1]);
                            break;
                        case 'cpu mhz':
                        case 'clock':
                            $dev->setCpuSpeed($arrBuff[1]);
                            break;
                        case 'cycle frequency [hz]':
                            $dev->setCpuSpeed($arrBuff[1] / 1000000);
                            break;
                        case 'cpu0clktck':
                            $dev->setCpuSpeed(hexdec($arrBuff[1]) / 1000000); // Linux sparc64
                            break;
                        case 'l2 cache':
                        case 'cache size':
                            $dev->setCache(preg_replace("/[a-zA-Z]/", "", $arrBuff[1]) * 1024);
                            break;
                        case 'bogomips':
                        case 'cpu0bogo':
                            $dev->setBogomips($arrBuff[1]);
                            break;
                        case 'flags':
                            if(preg_match("/vmx/",$arrBuff[1])) {
                                $dev->setVirt("vmx");
                            }
                            else if(preg_match("/smv/",$arrBuff[1])) {
                                $dev->setVirt("smv");
                            }
                            break;
                        }
                    }
                }
                // sparc64 specific code follows
                // This adds the ability to display the cache that a CPU has
                // Originally made by Sven Blumenstein <bazik@gentoo.org> in 2004
                // Modified by Tom Weustink <freshy98@gmx.net> in 2004
                $sparclist = array('SUNW,UltraSPARC@0,0', 'SUNW,UltraSPARC-II@0,0', 'SUNW,UltraSPARC@1c,0', 'SUNW,UltraSPARC-IIi@1c,0', 'SUNW,UltraSPARC-II@1c,0', 'SUNW,UltraSPARC-IIe@0,0');
                foreach ($sparclist as $name) {
                    if (CommonFunctions::rfts('/proc/openprom/'.$name.'/ecache-size', $buf, 1, 32, false)) {
                        $dev->setCache(base_convert($buf, 16, 10));
                    }
                }
                // sparc64 specific code ends
                
                // XScale detection code
                if ($dev->getModel() === "") {
                    foreach ($details as $detail) {
                        $arrBuff = preg_split('/\s+:\s+/', trim($detail));
                        if (count($arrBuff) == 2) {
                            switch (strtolower($arrBuff[0])) {
                            case 'processor':
                                $dev->setModel($arrBuff[1]);
                                break;
                            case 'bogomips':
                                $dev->setCpuSpeed($arrBuff[1]); //BogoMIPS are not BogoMIPS on this CPU, it's the speed
                                $dev->setBogomips(null); // no BogoMIPS available, unset previously set BogoMIPS 
                                break;
                            case 'i size':
                            case 'd size':
                                if ($dev->getCache() === null) {
                                    $dev->setCache($arrBuff[1] * 1024);
                                } else {
                                    $dev->setCache($dev->getCache() + ($arrBuff[1] * 1024));
                                }
                                break;
                            }
                        }
                    }
                }
                if (CommonFunctions::rfts('/proc/acpi/thermal_zone/THRM/temperature', $buf, 1, 4096, false)) {
                    $dev->setTemp(substr($buf, 25, 2));
                }

                if ($dev->getModel() === "") {
                    $dev->setModel("unknown");
                }
                $this->sys->setCpus($dev);
            }
        }
    }
    /**
     * PCI devices
     *
     * @return void
     */
    private function _pci()
    {
        if (!$arrResults = Parser::lspci()) {
            if (CommonFunctions::rfts('/proc/pci', $strBuf, 0, 4096, false)) {
                $booDevice = false;
                $arrBuf = preg_split("/\n/", $strBuf, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($arrBuf as $strLine) {
                    if (preg_match('/Bus/', $strLine)) {
                        $booDevice = true;
                        continue;
                    }
                    if ($booDevice) {
                        list($strKey, $strValue) = preg_split('/: /', $strLine, 2);
                        if (!preg_match('/bridge/i', $strKey) && !preg_match('/USB/i ', $strKey)) {
                            $dev = new HWDevice();
                            $dev->setName(preg_replace('/\([^\)]+\)\.$/', '', trim($strValue)));
                            $this->sys->setPciDevices($dev);
                        }
                        $booDevice = false;
                    }
                }
            }
        } else {
            foreach ($arrResults as $dev) {
                $this->sys->setPciDevices($dev);
            }
        }
    }
    /**
     * IDE devices
     *
     * @return void
     */
    private function _ide()
    {
        $bufd = CommonFunctions::gdc('/proc/ide', false);
        foreach ($bufd as $file) {
            if (preg_match('/^hd/', $file)) {
                $dev = new HWDevice();
                $dev->setName(trim($file));
                if (CommonFunctions::rfts("/proc/ide/".$file."/media", $buf, 1)) {
                    if (trim($buf) == 'disk') {
                        if (CommonFunctions::rfts("/proc/ide/".$file."/capacity", $buf, 1, 4096, false) || CommonFunctions::rfts("/sys/block/".$file."/size", $buf, 1, 4096, false)) {
                            $dev->setCapacity(trim($buf) * 512 / 1024);
                        }
                    }
                }
                if (CommonFunctions::rfts("/proc/ide/".$file."/model", $buf, 1)) {
                    $dev->setName($dev->getName().": ".trim($buf));
                }
                $this->sys->setIdeDevices($dev);
            }
        }
    }
    /**
     * SCSI devices
     *
     * @return void
     */
    private function _scsi()
    {
        $get_type = false;
        $device = null;
        if (CommonFunctions::executeProgram('lsscsi', '-c', $bufr, PSI_DEBUG) || CommonFunctions::rfts('/proc/scsi/scsi', $bufr, 0, 4096, PSI_DEBUG)) {
            $bufe = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($bufe as $buf) {
                if (preg_match('/Vendor: (.*) Model: (.*) Rev: (.*)/i', $buf, $devices)) {
                    $get_type = true;
                    $device = $devices;
                    continue;
                }
                if ($get_type) {
                    preg_match('/Type:\s+(\S+)/i', $buf, $dev_type);
                    $dev = new HWDevice();
                    $dev->setName($device[1].' '.$device[2].' ('.$dev_type[1].')');
                    $this->sys->setScsiDevices($dev);
                    $get_type = false;
                }
            }
        }
    }
    /**
     * USB devices
     *
     * @return array
     */
    private function _usb()
    {
        $devnum = -1;
        if (!CommonFunctions::executeProgram('lsusb', '', $bufr, PSI_DEBUG)) {
            if (CommonFunctions::rfts('/proc/bus/usb/devices', $bufr, 0, 4096, false)) {
                $bufe = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($bufe as $buf) {
                    if (preg_match('/^T/', $buf)) {
                        $devnum += 1;
                        $results[$devnum] = "";
                    } elseif (preg_match('/^S:/', $buf)) {
                        list($key, $value) = preg_split('/: /', $buf, 2);
                        list($key, $value2) = preg_split('/=/', $value, 2);
                        if (trim($key) != "SerialNumber") {
                            $results[$devnum] .= " ".trim($value2);
                        }
                    }
                }
                foreach ($results as $var) {
                    $dev = new HWDevice();
                    $dev->setName($var);
                    $this->sys->setUsbDevices($dev);
                }
            }
        } else {
            $bufe = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($bufe as $buf) {
                $device = preg_split("/ /", $buf, 7);
                if (isset($device[6]) && trim($device[6]) != "") {
                    $dev = new HWDevice();
                    $dev->setName(trim($device[6]));
                    $this->sys->setUsbDevices($dev);
                }
            }
        }
    }
    /**
     * Network devices
     * includes also rx/tx bytes
     *
     * @return void
     */
    private function _network()
    {
        if (CommonFunctions::rfts('/proc/net/dev', $bufr)) {
            $bufe = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($bufe as $buf) {
                if (preg_match('/:/', $buf)) {
                    list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                    $stats = preg_split('/\s+/', trim($stats_list));
                    $dev = new NetDevice();
                    $dev->setName(trim($dev_name));
                    $dev->setRxBytes($stats[0]);
                    $dev->setTxBytes($stats[8]);
                    $dev->setErrors($stats[2] + $stats[10]);
                    $dev->setDrops($stats[3] + $stats[11]);
                    $this->sys->setNetDevices($dev);
                }
            }
        }
    }
    /**
     * Physical memory information and Swap Space information
     *
     * @return void
     */
    private function _memory()
    {
        if (CommonFunctions::rfts('/proc/meminfo', $bufr)) {
            $bufe = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($bufe as $buf) {
                if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $this->sys->setMemTotal($ar_buf[1] * 1024);
                } elseif (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $this->sys->setMemFree($ar_buf[1] * 1024);
                } elseif (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $this->sys->setMemCache($ar_buf[1] * 1024);
                } elseif (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $this->sys->setMemBuffer($ar_buf[1] * 1024);
                }
            }
            $this->sys->setMemUsed($this->sys->getMemTotal() - $this->sys->getMemFree());
            // values for splitting memory usage
            if ($this->sys->getMemCache() !== null && $this->sys->getMemBuffer() !== null) {
                $this->sys->setMemApplication($this->sys->getMemUsed() - $this->sys->getMemCache() - $this->sys->getMemBuffer());
            }
            if (CommonFunctions::rfts('/proc/swaps', $bufr)) {
                $swaps = preg_split("/\n/", $bufr, -1, PREG_SPLIT_NO_EMPTY);
                unset($swaps[0]);
                foreach ($swaps as $swap) {
                    $ar_buf = preg_split('/\s+/', $swap, 5);
                    $dev = new DiskDevice();
                    $dev->setMountPoint($ar_buf[0]);
                    $dev->setName("SWAP");
                    $dev->setTotal($ar_buf[2] * 1024);
                    $dev->setUsed($ar_buf[3] * 1024);
                    $dev->setFree($dev->getTotal() - $dev->getUsed());
                    $this->sys->setSwapDevices($dev);
                }
            }
        }
    }
    /**
     * filesystem information
     *
     * @return void
     */
    private function _filesystems()
    {
        $arrResult = Parser::df("-P");
        foreach ($arrResult as $dev) {
            $this->sys->setDiskDevices($dev);
        }
    }
    /**
     * Distribution
     *
     * @return void
     */
    private function _distro()
    {
        $list = @parse_ini_file(APP_ROOT."/data/distros.ini", true);
        if (!$list) {
            return;
        }
        // We have the '2> /dev/null' because Ubuntu gives an error on this command which causes the distro to be unknown
        if (CommonFunctions::executeProgram('lsb_release', '-a 2> /dev/null', $distro_info, PSI_DEBUG)) {
            $distro_tmp = preg_split("/\n/", $distro_info, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($distro_tmp as $info) {
                $info_tmp = preg_split('/:/', $info, 2);
                $distro[$info_tmp[0]] = trim($info_tmp[1]);
                if (isset($distro['Distributor ID']) && isset($list[$distro['Distributor ID']]['Image'])) {
                    $this->sys->setDistributionIcon($list[$distro['Distributor ID']]['Image']);
                }
                if (isset($distro['Description'])) {
                    $this->sys->setDistribution($distro['Description']);
                }
            }
        } else {
            // Fall back in case 'lsb_release' does not exist ;)
            foreach ($list as $section=>$distribution) {
                if (!isset($distribution["Files"])) {
                    continue;
                } else {
                    foreach (preg_split("/;/", $distribution["Files"], -1, PREG_SPLIT_NO_EMPTY) as $filename) {
                        if (file_exists($filename)) {
                            CommonFunctions::rfts($filename, $buf);
                            if (isset($distribution["Image"])) {
                                $this->sys->setDistributionIcon($distribution["Image"]);
                            }
                            if (isset($distribution["Name"])) {
                                if ($distribution["Name"] == 'Synology') {
                                    $this->sys->setDistribution($distribution["Name"]);
                                } else {
                                    $this->sys->setDistribution($distribution["Name"]." ".trim($buf));
                                }
                            } else {
                                $this->sys->setDistribution(trim($buf));
                            }
                            return;
                        }
                    }
                }
            }
        }
    }
    /**
     * get the information
     *
     * @see PSI_Interface_OS::build()
     *
     * @return Void
     */
    function build()
    {
        $this->_distro();
        $this->_ip();
        $this->_hostname();
        $this->_kernel();
        $this->_uptime();
        $this->_users();
        $this->_cpuinfo();
        $this->_pci();
        $this->_ide();
        $this->_scsi();
        $this->_usb();
        $this->_network();
        $this->_memory();
        $this->_filesystems();
        $this->_loadavg();
    }
}
?>
