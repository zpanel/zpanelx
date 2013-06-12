<?php

/**
 * Bandwidth generation class.
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class sys_bandwidth {

    /**
     * Generate the toal amount of bandwidth based on an Apache Access Log (common format).
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $logfile The path to the log file of which to parse.
     * @return int Total amount of bandwidth used (bytes)
     */
    static function CalculateFromApacheLog($logfile) {
        $lines = file($logfile);
        $total = 0;
        foreach($lines as $line) {
            preg_match('>.+ .+\[.+\] ".+ .* HTTP/.*" [0-9]{3} ([0-9]+\b)>', $line, $match);
            if (isset($match[1])) {
                $total +=  $match[1];
            }
        }
        return $total;
    }
}

?>
