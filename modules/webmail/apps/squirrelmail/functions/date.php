<?php

/**
 * date.php
 *
 * Takes a date and parses it into a usable format.  The form that a
 * date SHOULD arrive in is:
 *       <Tue,> 29 Jun 1999 09:52:11 -0500 (EDT)
 * (as specified in RFC 822) -- 'Tue' is optional
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: date.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage date
 */

/** Load up some useful constants */
require_once(SM_PATH . 'functions/constants.php');

/**
 * Corrects a time stamp to be the local time.
 *
 * @param int stamp the timestamp to adjust
 * @param string tzc the timezone correction
 * @return int the corrected timestamp
 */
function getGMTSeconds($stamp, $tzc) {
    /* date couldn't be parsed */
    if ($stamp == -1) {
        return -1;
    }
    /* timezone correction, expressed as `shhmm' */
    switch($tzc)
    {
        case 'Pacific':
        case 'PST':
            $tzc = '-0800';
            break;
        case 'Mountain':
        case 'MST':
        case 'PDT':
            $tzc = '-0700';
            break;
        case 'Central':
        case 'CST':
        case 'MDT':
            $tzc = '-0600';
            break;
        case 'Eastern':
        case 'EST':
        case 'CDT':
            $tzc = '-0500';
            break;
        case 'EDT':
            $tzc = '-0400';
            break;
        case 'GMT':
            $tzc = '+0000';
            break;
        case 'BST':
        case 'MET':
        case 'CET':
            $tzc = '+0100';
            break;
        case 'EET':
        case 'IST':
        case 'MET DST':
        case 'METDST':
	case 'CEST':
	case 'MEST':
            $tzc = '+0200';
            break;
        case 'HKT':
            $tzc = '+0800';
            break;
        case 'JST':
        case 'KST':
            $tzc = '+0900';
            break;
    }
    $neg = false;
    if (substr($tzc, 0, 1) == '-') {
        $neg = true;
    } else if (substr($tzc, 0, 1) != '+') {
        $tzc = '+'.$tzc;
    }
    $hh = substr($tzc,1,2);
    $mm = substr($tzc,3,2);
    $iTzc = ($hh * 60 + $mm) * 60;
    if ($neg) $iTzc = -1 * (int) $iTzc;
    /* stamp in gmt */
    $stamp -= $iTzc;
    /** now find what the server is at **/
    $current = date('Z', time());
    /* stamp in local timezone */
    $stamp += $current;

    return $stamp;
}

/**
 * Returns the (localized) string for a given day number.
 * Switch system has been intentionaly chosen for the
 * internationalization of month and day names. The reason
 * is to make sure that _("") strings will go into the
 * main po.
 *
 * @param int day_number the day number
 * @return string the day in human readable form
 */
function getDayName( $day_number ) {

    switch( $day_number ) {
    case 0:
        $ret = _("Sunday");
        break;
    case 1:
        $ret = _("Monday");
        break;
    case 2:
        $ret = _("Tuesday");
        break;
    case 3:
        $ret = _("Wednesday");
        break;
    case 4:
        $ret = _("Thursday");
        break;
    case 5:
        $ret = _("Friday");
        break;
    case 6:
        $ret = _("Saturday");
        break;
    default:
        $ret = '';
    }
    return( $ret );
}

/**
 * Like getDayName, but returns the short form
 * @param int day_number the day number
 * @return string the day in short human readable form
 */
function getDayAbrv( $day_number ) {

    switch( $day_number ) {
    case 0:
        $ret = _("Sun");
        break;
    case 1:
        $ret = _("Mon");
        break;
    case 2:
        $ret = _("Tue");
        break;
    case 3:
        $ret = _("Wed");
        break;
    case 4:
        $ret = _("Thu");
        break;
    case 5:
        $ret = _("Fri");
        break;
    case 6:
        $ret = _("Sat");
        break;
    default:
        $ret = '';
    }
    return( $ret );
}


/**
 * Returns the (localized) string for a given month number.
 *
 * @param string month_number the month number (01..12)
 * @return string the month name in human readable form
 */
function getMonthName( $month_number ) {
    switch( $month_number ) {
     case '01':
        $ret = _("January");
        break;
     case '02':
        $ret = _("February");
        break;
     case '03':
        $ret = _("March");
        break;
     case '04':
        $ret = _("April");
        break;
     case '05':
        $ret = _("May");
        break;
     case '06':
        $ret = _("June");
        break;
     case '07':
        $ret = _("July");
        break;
     case '08':
        $ret = _("August");
        break;
     case '09':
        $ret = _("September");
        break;
     case '10':
        $ret = _("October");
        break;
     case '11':
        $ret = _("November");
        break;
     case '12':
        $ret = _("December");
        break;
     default:
        $ret = '';
    }
    return( $ret );
}

/**
 * Returns the (localized) string for a given month number,
 * short representation.
 *
 * @param string month_number the month number (01..12)
 * @return string the shortened month in human readable form
 */
function getMonthAbrv( $month_number ) {
    switch( $month_number ) {
     case '01':
        $ret = _("Jan");
        break;
     case '02':
        $ret = _("Feb");
        break;
     case '03':
        $ret = _("Mar");
        break;
     case '04':
        $ret = _("Apr");
        break;
     case '05':
        $ret = _("Ma&#121;");
        break;
     case '06':
        $ret = _("Jun");
        break;
     case '07':
        $ret = _("Jul");
        break;
     case '08':
        $ret = _("Aug");
        break;
     case '09':
        $ret = _("Sep");
        break;
     case '10':
        $ret = _("Oct");
        break;
     case '11':
        $ret = _("Nov");
        break;
     case '12':
        $ret = _("Dec");
        break;
     default:
        $ret = '';
    }
    return( $ret );
}

/**
 * Returns the localized representation of the date/time.
 *
 * @param string date_format The format for the date, like the input for the PHP date() function.
 * @param int stamp the timestamp to convert
 * @return string a full date representation
 */
function date_intl( $date_format, $stamp ) {
    $ret = str_replace( array('D','F','l','M'), array('$1','$2','$3','$4'), $date_format );
    // to reduce the date calls we retrieve m and w in the same call
    $ret = date('w#m#'. $ret, $stamp );
    // extract day and month in order to replace later by intl day and month
    $aParts = explode('#',$ret);
    $ret = str_replace(array('$1','$4','$2','$3',), array(getDayAbrv($aParts[0]),
                                                          getMonthAbrv($aParts[1]),
                   				          getMonthName($aParts[1]),
						          getDayName($aParts[0])),
						          $aParts[2]);
    return( $ret );
}

/**
 * This returns a date of the format "Wed, Oct 29, 2003 9:52 am",
 * or the same in 24H format (depending on the user's settings),
 * and taking localization into accout.
 *
 * @param int stamp the timestamp
 * @param string fallback string to use when stamp not valid
 * @return string the long date string
 */
function getLongDateString( $stamp, $fallback = '' ) {

    global $hour_format;

    if ($stamp == -1) {
        return $fallback;
    }

    if ( $hour_format == SMPREF_TIME_12HR ) {
        $date_format = _("D, F j, Y g:i a");
    } else {
        $date_format = _("D, F j, Y H:i");
    }

    return( date_intl( $date_format, $stamp ) );

}

/**
 * Returns a short representation of the date,
 * taking timezones and localization into account.
 * Depending on user's settings, this string can be
 * of the form: "14:23" or "Jun 14, 2003" depending
 * on whether the stamp is "today" or not.
 *
 * @param int stamp the timestamp
 * @return string the date string
 */
function getDateString( $stamp ) {

    global $invert_time, $hour_format, $show_full_date;

    if ( $stamp == -1 ) {
       return '';
    }

    $now = time();

    $dateZ = date('Z', $now );

    // FIXME: isn't this obsolete and introduced as a terrible workaround
    // for bugs at other places which are fixed a long time ago?
    if ($invert_time) {
        $dateZ = - $dateZ;
    }

    // calculate when it was midnight and when it will be,
    // in order to display dates differently if they're 'today'
    $midnight = $now - ($now % 86400) - $dateZ;
    // this is to correct if after calculations midnight is more than
    // one whole day away.
    if ($now - $midnight > 86400) {
        $midnight += 86400;
    }
    $nextmid = $midnight + 86400;

    if (($show_full_date == 1) || ($nextmid < $stamp)) {
        $date_format = _("M j, Y");
    } else if ($midnight < $stamp) {
        /* Today */
        if ( $hour_format == SMPREF_TIME_12HR ) {
            $date_format = _("g:i a");
        } else {
            $date_format = _("H:i");
        }
    } else if ($midnight - 518400 < $stamp) {
        /* This week */
        if ( $hour_format == SMPREF_TIME_12HR ) {
            $date_format = _("D, g:i a");
        } else {
            $date_format = _("D, H:i");
        }
    } else {
        /* before this week */
        $date_format = _("M j, Y");
    }

    return( date_intl( $date_format, $stamp ) );
}

/**
 * Decodes a RFC 822 Date-header into a timestamp
 *
 * @param array dateParts the Date-header split by whitespace
 * @return int the timestamp calculated from the header
 */
function getTimeStamp($dateParts) {
    /** $dateParts[0] == <day of week>   Mon, Tue, Wed
    ** $dateParts[1] == <day of month>  23
    ** $dateParts[2] == <month>         Jan, Feb, Mar
    ** $dateParts[3] == <year>          1999
    ** $dateParts[4] == <time>          18:54:23 (HH:MM:SS)
    ** $dateParts[5] == <from GMT>      +0100
    ** $dateParts[6] == <zone>          (EDT)
    **
    ** NOTE:  In RFC 822, it states that <day of week> is optional.
    **        In that case, dateParts[0] would be the <day of month>
    **        and everything would be bumped up one.
    **/
    if (count($dateParts) <2) {
        return -1;
    } else if (count($dateParts) ==3) {
        if (substr_count($dateParts[0],'-') == 2 &&
            substr_count($dateParts[1],':') == 2) {
            //  dd-Month-yyyy 23:19:05 +0200
            //  redefine the date
            $aDate = explode('-',$dateParts[0]);
            $newDate = array($aDate[0],$aDate[1],$aDate[2],$dateParts[1],$dateParts[2]);
            $dateParts = $newDate;
        }
    }

    /*
     * Simply check to see if the first element in the dateParts
     * array is an integer or not.
     * Since the day of week is optional, this check is needed.
     */
    if (!is_numeric(trim($dateParts[0]))) {
        /* cope with broken mailers that send "Tue,23" without space */
        if ( preg_match ('/^\w+,(\d{1,2})$/', $dateParts[0], $match) ) {
            /* replace Tue,23 with 23 */
            $dateParts[0] = $match[1];
        } else {
            /* just drop the day of the week */
            array_shift($dateParts);
        }
    }
    /* calculate timestamp separated from the zone and obs-zone */
    $stamp = strtotime(implode (' ', array_splice ($dateParts,0,4)));
    if (!isset($dateParts[0])) {
        $dateParts[0] = '+0000';
    }

    if (!preg_match('/^[+-]{1}[0-9]{4}$/',$dateParts[0])) {
        /* zone in obs-zone format */
        if (preg_match('/\((.+)\)/',$dateParts[0],$regs)) {
            $obs_zone = $regs[1];
        } else {
            $obs_zone = $dateParts[0];
        }
        return getGMTSeconds($stamp, $obs_zone);
    } else {
        return getGMTSeconds($stamp, $dateParts[0]);
    }
}

/* I use this function for profiling. Should never be called in
   actual versions of squirrelmail released to public. */
/*
   function getmicrotime() {
      $mtime = microtime();
      $mtime = explode(' ',$mtime);
      $mtime = $mtime[1] + $mtime[0];
      return ($mtime);
   }
*/
