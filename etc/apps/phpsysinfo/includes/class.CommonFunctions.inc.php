<?php
/**
 * common Functions class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.CommonFunctions.inc.php 699 2012-09-15 11:57:13Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * class with common functions used in all places
 *
 * @category  PHP
 * @package   PSI
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class CommonFunctions
{
    private static function _parse_log_file($string)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $log_file = substr(PSI_LOG, 1);
            if (file_exists($log_file)) {
                $contents = @file_get_contents($log_file);
                if ($contents && preg_match("/^\-\-\-[^-\n]+\-\-\- ".preg_quote($string, '/')."\n/m", $contents, $matches, PREG_OFFSET_CAPTURE)) {
                    $findIndex = $matches[0][1];
                    if (preg_match("/\n/m", $contents, $matches, PREG_OFFSET_CAPTURE, $findIndex)) {
                        $startIndex = $matches[0][1]+1;
                        if (preg_match("/^\-\-\-[^-\n]+\-\-\- /m", $contents, $matches, PREG_OFFSET_CAPTURE, $startIndex)) {
                            $stopIndex = $matches[0][1];

                            return substr($contents, $startIndex, $stopIndex-$startIndex );
                        } else {
                            return substr($contents, $startIndex );
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Find a system program, do also path checking when not running on WINNT
     * on WINNT we simply return the name with the exe extension to the program name
     *
     * @param string $strProgram name of the program
     *
     * @return string complete path and name of the program
     */
    private static function _findProgram($strProgram)
    {
        $arrPath = array();
        if (PSI_OS == 'WINNT') {
            $strProgram .= '.exe';
            $arrPath = preg_split('/;/', getenv("Path"), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $arrPath = preg_split('/:/', getenv("PATH"), -1, PREG_SPLIT_NO_EMPTY);
        }
        if ( defined('PSI_ADD_PATHS') && is_string(PSI_ADD_PATHS) ) {
            if (preg_match(ARRAY_EXP, PSI_ADD_PATHS)) {
                $arrPath = array_merge(eval(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
            } else {
                $arrPath = array_merge(array(PSI_ADD_PATHS), $arrPath); // In this order so $addpaths is before $arrPath when looking for a program
            }
        }
        //add some default paths if we still have no paths here
        if (empty($arrPath) && PSI_OS != 'WINNT') {
            if (PSI_OS == 'Android') {
                array_push($arrPath, '/system/bin');
            } else {
                array_push($arrPath, '/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');
            }
        }
        // If open_basedir defined, fill the $open_basedir array with authorized paths,. (Not tested when no open_basedir restriction)
        if ((bool) ini_get('open_basedir')) {
            $open_basedir = preg_split('/:/', ini_get('open_basedir'), -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach ($arrPath as $strPath) {
            // To avoid "open_basedir restriction in effect" error when testing paths if restriction is enabled
            if ((isset($open_basedir) && !in_array($strPath, $open_basedir)) ||
             !(((PSI_OS == 'Android') && ($strPath=='/system/bin')) || is_dir($strPath))) { //is_dir('/system/bin') Android patch
                continue;
            }
            if (PSI_OS == 'WINNT') {
                $strProgrammpath = rtrim($strPath,'\\').'\\'.$strProgram;
            } else {
                $strProgrammpath = rtrim($strPath,"/")."/".$strProgram;
            }
            if (is_executable($strProgrammpath)) {
                return $strProgrammpath;
            }
        }
    }

    /**
     * Execute a system program. return a trim()'d result.
     * does very crude pipe checking.  you need ' | ' for it to work
     * ie $program = CommonFunctions::executeProgram('netstat', '-anp | grep LIST');
     * NOT $program = CommonFunctions::executeProgram('netstat', '-anp|grep LIST');
     *
     * @param string  $strProgramname name of the program
     * @param string  $strArgs        arguments to the program
     * @param string  &$strBuffer     output of the command
     * @param boolean $booErrorRep    en- or disables the reporting of errors which should be logged
     *
     * @return boolean command successfull or not
     */
    public static function executeProgram($strProgramname, $strArgs, &$strBuffer, $booErrorRep = true)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $out = self::_parse_log_file("Executing: ".$strProgramname.' '.$strArgs);
            if ($out == false) {
                if (substr(PSI_LOG, 0, 1)=="-") {
                    $strBuffer = '';

                    return false;
                }
            } else {
                $strBuffer = $out;

                return true;
            }
        }
        
        $strBuffer = '';
        $strError = '';
        $pipes = array();
        $strProgram = self::_findProgram($strProgramname);
        $error = Error::singleton();
        if (!$strProgram) {
            if ($booErrorRep) {
                $error->addError('find_program('.$strProgramname.')', 'program not found on the machine');
            }

            return false;
        }
        // see if we've gotten a |, if we have we need to do path checking on the cmd
        if ($strArgs) {
            $arrArgs = preg_split('/ /', $strArgs, -1, PREG_SPLIT_NO_EMPTY);
            for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                if ($arrArgs[$i] == '|') {
                    $strCmd = $arrArgs[$i + 1];
                    $strNewcmd = self::_findProgram($strCmd);
                    $strArgs = preg_replace("/\| ".$strCmd.'/', "| ".$strNewcmd, $strArgs);
                }
            }
        }
        $descriptorspec = array(0=>array("pipe", "r"), 1=>array("pipe", "w"), 2=>array("pipe", "w"));
        if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
            $process = $pipes[1] = popen($strProgram." ".$strArgs." 2>/dev/null", "r");
        } else {
            $process = proc_open($strProgram." ".$strArgs, $descriptorspec, $pipes);
        }
        if (is_resource($process)) {
            if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
                $pipes[0] = null;
                $pipes[2] = fopen("/dev/null", "r");
            }
            self::_timeoutfgets($pipes, $strBuffer, $strError);
            if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
                fclose($pipes[2]);
                $return_value = pclose($pipes[1]);
            } else {
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                // It is important that you close any pipes before calling
                // proc_close in order to avoid a deadlock
                $return_value = proc_close($process);
            }
        } else {
            if ($booErrorRep) {
                $error->addError($strProgram, "\nOpen process error");
            }

            return false;
        }
        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && (substr(PSI_LOG, 0, 1)!="-") && (substr(PSI_LOG, 0, 1)!="+")) {
            error_log("---".gmdate('r T')."--- Executing: ".$strProgramname.' '.$strArgs."\n".$strBuffer."\n", 3, PSI_LOG);
        }
        if (! empty($strError)) {
            if ($booErrorRep) {
                $error->addError($strProgram, $strError."\nReturn value: ".$return_value);
            }

            return $return_value == 0;
        }

        return true;
    }

    /**
     * read a file and return the content as a string
     *
     * @param string  $strFileName name of the file which should be read
     * @param string  &$strRet     content of the file (reference)
     * @param integer $intLines    control how many lines should be read
     * @param integer $intBytes    control how many bytes of each line should be read
     * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return boolean command successfull or not
     */
    public static function rfts($strFileName, &$strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true)
    {
        if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && ((substr(PSI_LOG, 0, 1)=="-") || (substr(PSI_LOG, 0, 1)=="+"))) {
            $out = self::_parse_log_file("Reading: ".$strFileName);
            if ($out == false) {
                if (substr(PSI_LOG, 0, 1)=="-") {
                    $strRet = '';

                    return false;
                }
            } else {
                $strRet = $out;

                return true;
            }
        }
        
        $strFile = "";
        $intCurLine = 1;
        $error = Error::singleton();
        if (file_exists($strFileName)) {
            if (is_readable($strFileName)) {
                if ($fd = fopen($strFileName, 'r')) {
                    while (!feof($fd)) {
                        $strFile .= fgets($fd, $intBytes);
                        if ($intLines <= $intCurLine && $intLines != 0) {
                            break;
                        } else {
                            $intCurLine++;
                        }
                    }
                    fclose($fd);
                    $strRet = $strFile;
                    if (defined('PSI_LOG') && is_string(PSI_LOG) && (strlen(PSI_LOG)>0) && (substr(PSI_LOG, 0, 1)!="-") && (substr(PSI_LOG, 0, 1)!="+")) {
                        error_log("---".gmdate('r T')."--- Reading: ".$strFileName."\n".$strRet, 3, PSI_LOG);
                    }
                } else {
                    if ($booErrorRep) {
                         $error->addError('fopen('.$strFileName.')', 'file can not read by phpsysinfo');
                    }

                    return false;
                }
            } else {
                    if ($booErrorRep) {
                         $error->addError('fopen('.$strFileName.')', 'file permission error');
                    }

                    return false;
            }
        } else {
            if ($booErrorRep) {
                $error->addError('file_exists('.$strFileName.')', 'the file does not exist on your machine');
            }

            return false;
        }

        return true;
    }

    /**
     * reads a directory and return the name of the files and directorys in it
     *
     * @param string  $strPath     path of the directory which should be read
     * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return array content of the directory excluding . and ..
     */
    public static function gdc($strPath, $booErrorRep = true)
    {
        $arrDirectoryContent = array();
        $error = Error::singleton();
        if (is_dir($strPath)) {
            if ($handle = opendir($strPath)) {
                while (($strFile = readdir($handle)) !== false) {
                    if ($strFile != "." && $strFile != "..") {
                        $arrDirectoryContent[] = $strFile;
                    }
                }
                closedir($handle);
            } else {
                if ($booErrorRep) {
                    $error->addError('opendir('.$strPath.')', 'directory can not be read by phpsysinfo');
                }
            }
        } else {
            if ($booErrorRep) {
                $error->addError('is_dir('.$strPath.')', 'directory does not exist on your machine');
            }
        }

        return $arrDirectoryContent;
    }

    /**
     * Check for needed php extensions
     *
     * We need that extensions for almost everything
     * This function will return a hard coded
     * XML string (with headers) if the SimpleXML extension isn't loaded.
     * Then it will terminate the script.
     * See bug #1787137
     *
     * @param array $arrExt additional extensions for which a check should run
     *
     * @return void
     */
    public static function checkForExtensions($arrExt = array())
    {
        if ((PSI_OS == "Minix") || (PSI_SYSTEM_CODEPAGE == "UTF-8"))
            $arrReq = array('simplexml', 'pcre', 'xml', 'dom');
        elseif (PSI_OS == "WINNT")
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom', 'com_dotnet');
        else
            $arrReq = array('simplexml', 'pcre', 'xml', 'mbstring', 'dom');
        $extensions = array_merge($arrExt, $arrReq);
        $text = "";
        $error = false;
        $text .= "<?xml version='1.0'?>\n";
        $text .= "<phpsysinfo>\n";
        $text .= "  <Error>\n";
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $text .= "    <Function>checkForExtensions</Function>\n";
                $text .= "    <Message>phpSysInfo requires the ".$extension." extension to php in order to work properly.</Message>\n";
                $error = true;
            }
        }
        $text .= "  </Error>\n";
        $text .= "</phpsysinfo>";
        if ($error) {
            header("Content-Type: text/xml\n\n");
            echo $text;
            die();
        }
    }

    /**
     * get the content of stdout/stderr with the option to set a timeout for reading
     *
     * @param array   $pipes array of file pointers for stdin, stdout, stderr (proc_open())
     * @param string  &$out  target string for the output message (reference)
     * @param string  &$err  target string for the error message (reference)
     * @param integer $sek   timeout value in seconds
     *
     * @return void
     */
    private static function _timeoutfgets($pipes, &$out, &$err, $sek = 30)
    {
        // fill output string
        $time = $sek;
        $w = null;
        $e = null;

        $read = array($pipes[1],$pipes[2]);
        while (!(feof($pipes[1]) && feof($pipes[2])) && ($n = stream_select($read, $w, $e, $time)) !== false && $n > 0) {
                $out .= fread($pipes[1], 4096);
                $err .= fread($pipes[2], 4096);
        }
    }

    /**
     * get all configured plugins from config.php (file must be included before calling this function)
     *
     * @return array
     */
    public static function getPlugins()
    {
        if ( defined('PSI_PLUGINS') && is_string(PSI_PLUGINS) ) {
            if (preg_match(ARRAY_EXP, PSI_PLUGINS)) {
                return eval(strtolower(PSI_PLUGINS));
            } else {
                return array(strtolower(PSI_PLUGINS));
            }
        } else {
            return array();
        }
    }
}
