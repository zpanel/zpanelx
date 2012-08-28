<?php

/**
 * functions to operate on calendar data files.
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: calendar_data.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage calendar
 */

/**
 * this is array that contains all events
 * it is three dimensional array with fallowing structure
 * $calendardata[date][time] = array(length,priority,title,message,reminder);
 */
$calendardata = array();

/**
 * Reads multilined calendar data
 * 
 * Plugin stores multiline texts converted to single line with PHP nl2br().
 * Function undoes nl2br() conversion and html encoding of ASCII vertical bar.
 *
 * Older plugin versions sanitized data with htmlspecialchars. Since 1.5.1 calendar 
 * data is not sanitized. Output functions must make sure that data is correctly 
 * encoded and sanitized.
 * @param string $string calendar string
 * @return string calendar string converted to multiline text
 * @access private
 * @since 1.5.1 and 1.4.7
 */
function calendar_readmultiline($string) {
    /**
     * replace html line breaks with ASCII line feeds
     * replace htmlencoded | with ASCII vertical bar
     */
    $string = str_replace(array('<br />','<br>','&#124;'),array("\n","\n",'|'),$string);
    return $string;
}

/**
 * Callback function used to sanitize calendar data before saving it to file
 * @param string $sValue array value 
 * @param string $sKey array key
 * @access private
 * @since 1.5.1 and 1.4.7
 */
function calendar_encodedata(&$sValue, $sKey) {
    /**
     * add html line breaks
     * remove original ASCII line feeds and carriage returns
     * replace ASCII vertical bar with html code in order to sanitize field delimiter
     */
    $sValue = str_replace(array("\n","\r",'|'),array('','','&#124;'),nl2br($sValue));
}

/**
 * read events into array
 *
 * data is | delimited, just like addressbook
 * files are structured like this:
 * date|time|length|priority|title|message
 * files are divided by year for performance increase
 */
function readcalendardata() {
    global $calendardata, $username, $data_dir, $year;

    $filename = getHashedFile($username, $data_dir, "$username.$year.cal");

    if (file_exists($filename)){
        $fp = fopen ($filename,'r');

        if ($fp){
            while ($fdata = fgetcsv ($fp, 4096, '|')) {
                $calendardata[$fdata[0]][$fdata[1]] = array( 'length'   => $fdata[2],
                                                             'priority' => $fdata[3],
                                                             'title'    => str_replace("\n",' ',calendar_readmultiline($fdata[4])),
                                                             'message'  => calendar_readmultiline($fdata[5]),
                                                             'reminder' => $fdata[6] );
            }
            fclose ($fp);
            // this is to sort the events within a day on starttime
            $new_calendardata = array();
            foreach($calendardata as $day => $data) {
                ksort($data, SORT_NUMERIC);
                $new_calendardata[$day] = $data;
            }
            $calendardata = $new_calendardata;
        }
    }
}

/**
 * Saves calendar data
 * @return void
 * @access private
 */
function writecalendardata() {
    global $calendardata, $username, $data_dir, $year, $color;

    $filetmp = getHashedFile($username, $data_dir, "$username.$year.cal.tmp");
    $filename = getHashedFile($username, $data_dir, "$username.$year.cal");
    $fp = fopen ($filetmp,"w");
    if ($fp) {
        while ( $calfoo = each ($calendardata)) {
            while ( $calbar = each ($calfoo['value'])) {
                $calfoobar = $calendardata[$calfoo['key']][$calbar['key']];
                array_walk($calfoobar,'calendar_encodedata');
                /**
                 * Make sure that reminder field is set. Calendar forms don't implement it, 
                 * but it is still used for calendar data. Backwards compatibility.
                 */ 
                if (!isset($calfoobar['reminder'])) $calfoobar['reminder']='';

                $calstr = "$calfoo[key]|$calbar[key]|$calfoobar[length]|$calfoobar[priority]|$calfoobar[title]|$calfoobar[message]|$calfoobar[reminder]\n";
                if(sq_fwrite($fp, $calstr, 4096) === FALSE) {
                    error_box(_("Could not write calendar file %s", "$username.$year.cal.tmp"), $color);
                }
            }

        }
        fclose ($fp);
        @unlink($filename);
        rename($filetmp,$filename);
    }
}

/**
 * deletes event from file
 * @return void
 * @access private
 */
function delete_event($date, $time) {
    global $calendardata, $username, $data_dir, $year;

    $filename = getHashedFile($username, $data_dir, "$username.$year.cal");
    $fp = fopen ($filename,'r');
    if ($fp){
        while ($fdata = fgetcsv ($fp, 4096, "|")) {
            if (($fdata[0]==$date) && ($fdata[1]==$time)){
                // do nothing
            } else {
                $calendardata[$fdata[0]][$fdata[1]] = array( 'length'   => $fdata[2],
                                                             'priority' => $fdata[3],
                                                             'title'    => $fdata[4],
                                                             'message'  => $fdata[5],
                                                             'reminder' => $fdata[6] );
            }
        }
        fclose ($fp);
    }
    writecalendardata();
}

/**
 * same as delete but does not save calendar
 * saving is done inside event_edit.php
 * @return void
 * @access private
 * @todo code reuse
 */
function update_event($date, $time) {
    global $calendardata, $username, $data_dir, $year;

    $filename = getHashedFile($username, $data_dir, "$username.$year.cal");
    $fp = fopen ($filename,'r');
    if ($fp){
        while ($fdata = fgetcsv ($fp, 4096, '|')) {
            if (($fdata[0]==$date) && ($fdata[1]==$time)){
                // do nothing
            } else {
                $calendardata[$fdata[0]][$fdata[1]] = array( 'length'   => $fdata[2],
                                                             'priority' => $fdata[3],
                                                             'title'    => $fdata[4],
                                                             'message'  => $fdata[5],
                                                             'reminder' => $fdata[6] );
            }
        }
        fclose ($fp);
    }
}

?>