<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// {{{ Header

/**
 * Calculates, manipulates and retrieves dates
 *
 * It does not rely on 32-bit system time stamps, so it works dates
 * before 1970 and after 2038.
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 1999-2007 Monte Ohrt, Pierre-Alain Joye, Daniel Convissor,
 * C.A. Woodcock
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted under the terms of the BSD License.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Date and Time
 * @package    Date
 * @author     Monte Ohrt <monte@ispi.net>
 * @author     Pierre-Alain Joye <pajoye@php.net>
 * @author     Daniel Convissor <danielc@php.net>
 * @author     C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright  1999-2007 Monte Ohrt, Pierre-Alain Joye, Daniel Convissor, C.A. Woodcock
 * @license    http://www.opensource.org/licenses/bsd-license.php
 *             BSD License
 * @version    CVS: $Id: Calc.php,v 1.56 2007/12/08 19:59:39 c01234 Exp $
 * @link       http://pear.php.net/package/Date
 * @since      File available since Release 1.2
 */


// }}}
// {{{ General constants:

if (!defined('DATE_CALC_BEGIN_WEEKDAY')) {
    /**
     * Defines what day starts the week
     *
     * Monday (1) is the international standard.
     * Redefine this to 0 if you want weeks to begin on Sunday.
     */
    define('DATE_CALC_BEGIN_WEEKDAY', 1);
}

if (!defined('DATE_CALC_FORMAT')) {
    /**
     * The default value for each method's $format parameter
     *
     * The default is '%Y%m%d'.  To override this default, define
     * this constant before including Calc.php.
     *
     * @since Constant available since Release 1.4.4
     */
    define('DATE_CALC_FORMAT', '%Y%m%d');
}


// {{{ Date precision constants (used in 'round()' and 'trunc()'):

define('DATE_PRECISION_YEAR', -2);
define('DATE_PRECISION_MONTH', -1);
define('DATE_PRECISION_DAY', 0);
define('DATE_PRECISION_HOUR', 1);
define('DATE_PRECISION_10MINUTES', 2);
define('DATE_PRECISION_MINUTE', 3);
define('DATE_PRECISION_10SECONDS', 4);
define('DATE_PRECISION_SECOND', 5);


// }}}
// {{{ Class: Date_Calc

/**
 * Calculates, manipulates and retrieves dates
 *
 * It does not rely on 32-bit system time stamps, so it works dates
 * before 1970 and after 2038.
 *
 * @category  Date and Time
 * @package   Date
 * @author    Monte Ohrt <monte@ispi.net>
 * @author    Daniel Convissor <danielc@php.net>
 * @author    C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright 1999-2007 Monte Ohrt, Pierre-Alain Joye, Daniel Convissor, C.A. Woodcock
 * @license   http://www.opensource.org/licenses/bsd-license.php
 *            BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Date
 * @since     Class available since Release 1.2
 */
class Date_Calc
{

    // {{{ dateFormat()

    /**
     * Formats the date in the given format, much like strfmt()
     *
     * This function is used to alleviate the problem with 32-bit numbers for
     * dates pre 1970 or post 2038, as strfmt() has on most systems.
     * Most of the formatting options are compatible.
     *
     * Formatting options:
     * <pre>
     * %a   abbreviated weekday name (Sun, Mon, Tue)
     * %A   full weekday name (Sunday, Monday, Tuesday)
     * %b   abbreviated month name (Jan, Feb, Mar)
     * %B   full month name (January, February, March)
     * %d   day of month (range 00 to 31)
     * %e   day of month, single digit (range 0 to 31)
     * %E   number of days since unspecified epoch (integer)
     *        (%E is useful for passing a date in a URL as
     *        an integer value. Then simply use
     *        daysToDate() to convert back to a date.)
     * %j   day of year (range 001 to 366)
     * %m   month as decimal number (range 1 to 12)
     * %n   newline character (\n)
     * %t   tab character (\t)
     * %w   weekday as decimal (0 = Sunday)
     * %U   week number of current year, first sunday as first week
     * %y   year as decimal (range 00 to 99)
     * %Y   year as decimal including century (range 0000 to 9999)
     * %%   literal '%'
     * </pre>
     *
     * @param int    $day    the day of the month
     * @param int    $month  the month
     * @param int    $year   the year.  Use the complete year instead of the
     *                        abbreviated version.  E.g. use 2005, not 05.
     * @param string $format the format string
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function dateFormat($day, $month, $year, $format)
    {
        if (!Date_Calc::isValidDate($day, $month, $year)) {
            $year  = Date_Calc::dateNow('%Y');
            $month = Date_Calc::dateNow('%m');
            $day   = Date_Calc::dateNow('%d');
        }

        $output = '';

        for ($strpos = 0; $strpos < strlen($format); $strpos++) {
            $char = substr($format, $strpos, 1);
            if ($char == '%') {
                $nextchar = substr($format, $strpos + 1, 1);
                switch($nextchar) {
                case 'a':
                    $output .= Date_Calc::getWeekdayAbbrname($day, $month, $year);
                    break;
                case 'A':
                    $output .= Date_Calc::getWeekdayFullname($day, $month, $year);
                    break;
                case 'b':
                    $output .= Date_Calc::getMonthAbbrname($month);
                    break;
                case 'B':
                    $output .= Date_Calc::getMonthFullname($month);
                    break;
                case 'd':
                    $output .= sprintf('%02d', $day);
                    break;
                case 'e':
                    $output .= $day;
                    break;
                case 'E':
                    $output .= Date_Calc::dateToDays($day, $month, $year);
                    break;
                case 'j':
                    $output .= Date_Calc::dayOfYear($day, $month, $year);
                    break;
                case 'm':
                    $output .= sprintf('%02d', $month);
                    break;
                case 'n':
                    $output .= "\n";
                    break;
                case 't':
                    $output .= "\t";
                    break;
                case 'w':
                    $output .= Date_Calc::dayOfWeek($day, $month, $year);
                    break;
                case 'U':
                    $output .= Date_Calc::weekOfYear($day, $month, $year);
                    break;
                case 'y':
                    $output .= sprintf('%0' .
                                       ($year < 0 ? '3' : '2') .
                                       'd',
                                       $year % 100);
                    break;
                case "Y":
                    $output .= sprintf('%0' .
                                       ($year < 0 ? '5' : '4') .
                                       'd',
                                       $year);
                    break;
                case '%':
                    $output .= '%';
                    break;
                default:
                    $output .= $char.$nextchar;
                }
                $strpos++;
            } else {
                $output .= $char;
            }
        }
        return $output;
    }


    // }}}
    // {{{ dateNow()

    /**
     * Returns the current local date
     *
     * NOTE: This function retrieves the local date using strftime(),
     * which may or may not be 32-bit safe on your system.
     *
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the current date in the specified format
     * @access   public
     * @static
     */
    function dateNow($format = DATE_CALC_FORMAT)
    {
        return strftime($format, time());
    }


    // }}}
    // {{{ getYear()

    /**
     * Returns the current local year in format CCYY
     *
     * @return   string     the current year in four digit format
     * @access   public
     * @static
     */
    function getYear()
    {
        return Date_Calc::dateNow('%Y');
    }


    // }}}
    // {{{ getMonth()

    /**
     * Returns the current local month in format MM
     *
     * @return   string     the current month in two digit format
     * @access   public
     * @static
     */
    function getMonth()
    {
        return Date_Calc::dateNow('%m');
    }


    // }}}
    // {{{ getDay()

    /**
     * Returns the current local day in format DD
     *
     * @return   string     the current day of the month in two digit format
     * @access   public
     * @static
     */
    function getDay()
    {
        return Date_Calc::dateNow('%d');
    }


    // }}}
    // {{{ defaultCentury()

    /**
     * Turns a two digit year into a four digit year
     *
     * Return value depends on current year; the century chosen
     * will be the one which forms the year that is closest
     * to the current year.  If the two possibilities are
     * equidistant to the current year (i.e. 50 years in the past
     * and 50 years in the future), then the past year is chosen.
     *
     * For example, if the current year is 2007:
     *  03 - returns 2003
     *  09 - returns 2009
     *  56 - returns 2056 (closer to 2007 than 1956)
     *  57 - returns 1957 (1957 and 2007 are equidistant, so previous century
     *        chosen)
     *  58 - returns 1958
     *
     * @param int $year the 2 digit year
     *
     * @return   int        the 4 digit year
     * @access   public
     * @static
     */
    function defaultCentury($year)
    {
        $hn_century = intval(($hn_currentyear = date("Y")) / 100);
        $hn_currentyear = $hn_currentyear % 100;

        if ($year < 0 || $year >= 100) 
            $year = $year % 100;

        if ($year - $hn_currentyear < -50)
            return ($hn_century + 1) * 100 + $year;
        else if ($year - $hn_currentyear < 50)
            return $hn_century * 100 + $year;
        else
            return ($hn_century - 1) * 100 + $year;
    }


    // }}}
    // {{{ getSecondsInYear()

    /**
     * Returns the total number of seconds in the given year
     *
     * This takes into account leap seconds.
     *
     * @param int $pn_year the year in four digit format
     *
     * @return   int
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getSecondsInYear($pn_year)
    {
        $pn_year = intval($pn_year);

        static $ha_leapseconds;
        if (!isset($ha_leapseconds)) {
            $ha_leapseconds = array(1972 => 2,
                                    1973 => 1,
                                    1974 => 1,
                                    1975 => 1,
                                    1976 => 1,
                                    1977 => 1,
                                    1978 => 1,
                                    1979 => 1,
                                    1981 => 1,
                                    1982 => 1,
                                    1983 => 1,
                                    1985 => 1,
                                    1987 => 1,
                                    1989 => 1,
                                    1990 => 1,
                                    1992 => 1,
                                    1993 => 1,
                                    1994 => 1,
                                    1995 => 1,
                                    1997 => 1,
                                    1998 => 1,
                                    2005 => 1);
        }

        $ret = Date_Calc::daysInYear($pn_year) * 86400;

        if (isset($ha_leapseconds[$pn_year])) {
            return $ret + $ha_leapseconds[$pn_year];
        } else {
            return $ret;
        }
    }


    // }}}
    // {{{ getSecondsInMonth()

    /**
     * Returns the total number of seconds in the given month
     *
     * This takes into account leap seconds.
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year in four digit format
     *
     * @return   int
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getSecondsInMonth($pn_month, $pn_year)
    {
        $pn_month = intval($pn_month);
        $pn_year  = intval($pn_year);

        static $ha_leapseconds;
        if (!isset($ha_leapseconds)) {
            $ha_leapseconds = array(1972 => array(6  => 1,
                                                  12 => 1),
                                    1973 => array(12 => 1),
                                    1974 => array(12 => 1),
                                    1975 => array(12 => 1),
                                    1976 => array(12 => 1),
                                    1977 => array(12 => 1),
                                    1978 => array(12 => 1),
                                    1979 => array(12 => 1),
                                    1981 => array(6  => 1),
                                    1982 => array(6  => 1),
                                    1983 => array(6  => 1),
                                    1985 => array(6  => 1),
                                    1987 => array(12 => 1),
                                    1989 => array(12 => 1),
                                    1990 => array(12 => 1),
                                    1992 => array(6  => 1),
                                    1993 => array(6  => 1),
                                    1994 => array(6  => 1),
                                    1995 => array(12 => 1),
                                    1997 => array(6  => 1),
                                    1998 => array(12 => 1),
                                    2005 => array(12 => 1));
        }

        $ret = Date_Calc::daysInMonth($pn_month, $pn_year) * 86400;

        if (isset($ha_leapseconds[$pn_year][$pn_month])) {
            return $ret + $ha_leapseconds[$pn_year][$pn_month];
        } else {
            return $ret;
        }
    }


    // }}}
    // {{{ getSecondsInDay()

    /**
     * Returns the total number of seconds in the day of the given date
     *
     * This takes into account leap seconds.
     *
     * @param int $pn_day   the day of the month
     * @param int $pn_month the month
     * @param int $pn_year  the year in four digit format
     *
     * @return   int
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getSecondsInDay($pn_day, $pn_month, $pn_year)
    {
        // Note to developers:
        //
        // The leap seconds listed here are a matter of historical fact,
        // that is, it is known on which exact day they occurred.
        // However, the implementation of the class as a whole depends
        // on the fact that they always occur at the end of the month
        // (although it is assumed that they could occur in any month,
        // even though practically they only occur in June or December).
        //
        // Do not define a leap second on a day of the month other than
        // the last day without altering the implementation of the 
        // functions that depend on this one.
        //
        // It is possible, though, to define an un-leap second (i.e. a skipped
        // second (I do not know what they are called), or a number of
        // consecutive leap seconds).

        $pn_day   = intval($pn_day);
        $pn_month = intval($pn_month);
        $pn_year  = intval($pn_year);

        static $ha_leapseconds;
        if (!isset($ha_leapseconds)) {
            $ha_leapseconds = array(1972 => array(6  => array(30 => 1),
                                                  12 => array(31 => 1)),
                                    1973 => array(12 => array(31 => 1)),
                                    1974 => array(12 => array(31 => 1)),
                                    1975 => array(12 => array(31 => 1)),
                                    1976 => array(12 => array(31 => 1)),
                                    1977 => array(12 => array(31 => 1)),
                                    1978 => array(12 => array(31 => 1)),
                                    1979 => array(12 => array(31 => 1)),
                                    1981 => array(6  => array(30 => 1)),
                                    1982 => array(6  => array(30 => 1)),
                                    1983 => array(6  => array(30 => 1)),
                                    1985 => array(6  => array(30 => 1)),
                                    1987 => array(12 => array(31 => 1)),
                                    1989 => array(12 => array(31 => 1)),
                                    1990 => array(12 => array(31 => 1)),
                                    1992 => array(6  => array(30 => 1)),
                                    1993 => array(6  => array(30 => 1)),
                                    1994 => array(6  => array(30 => 1)),
                                    1995 => array(12 => array(31 => 1)),
                                    1997 => array(6  => array(30 => 1)),
                                    1998 => array(12 => array(31 => 1)),
                                    2005 => array(12 => array(31 => 1)));
        }

        if (isset($ha_leapseconds[$pn_year][$pn_month][$pn_day])) {
            return 86400 + $ha_leapseconds[$pn_year][$pn_month][$pn_day];
        } else {
            return 86400;
        }
    }


    // }}}
    // {{{ getSecondsInHour()

    /**
     * Returns the total number of seconds in the hour of the given date
     *
     * This takes into account leap seconds.
     *
     * @param int $pn_day   the day of the month
     * @param int $pn_month the month
     * @param int $pn_year  the year in four digit format
     * @param int $pn_hour  the hour
     *
     * @return   int
     * @access   public
     * @static
     */
    function getSecondsInHour($pn_day, $pn_month, $pn_year, $pn_hour)
    {
        if ($pn_hour < 23)
            return 3600;
        else
            return Date_Calc::getSecondsInDay($pn_day, $pn_month, $pn_year) -
                   82800;
    }


    // }}}
    // {{{ getSecondsInMinute()

    /**
     * Returns the total number of seconds in the minute of the given hour
     *
     * This takes into account leap seconds.
     *
     * @param int $pn_day    the day of the month
     * @param int $pn_month  the month
     * @param int $pn_year   the year in four digit format
     * @param int $pn_hour   the hour
     * @param int $pn_minute the minute
     *
     * @return   int
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getSecondsInMinute($pn_day,
                                $pn_month,
                                $pn_year,
                                $pn_hour,
                                $pn_minute)
    {
        if ($pn_hour < 23 || $pn_minute < 59)
            return 60;
        else
            return Date_Calc::getSecondsInDay($pn_day, $pn_month, $pn_year) -
                   86340;
    }


    // }}}
    // {{{ secondsPastMidnight()

    /**
     * Returns the no of seconds since midnight (0-86399)
     *
     * @param int   $pn_hour   the hour of the day
     * @param int   $pn_minute the minute
     * @param mixed $pn_second the second as integer or float
     *
     * @return   mixed      integer or float from 0-86399
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function secondsPastMidnight($pn_hour, $pn_minute, $pn_second)
    {
        return 3600 * $pn_hour + 60 * $pn_minute + $pn_second;
    }


    // }}}
    // {{{ secondsPastMidnightToTime()

    /**
     * Returns the time as an array (i.e. hour, minute, second)
     *
     * @param mixed $pn_seconds the no of seconds since midnight (0-86399)
     *
     * @return   mixed      array of hour, minute (both as integers), second (as
     *                       integer or float, depending on parameter)
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function secondsPastMidnightToTime($pn_seconds)
    {
        if ($pn_seconds >= 86400) {
            return array(23, 59, $pn_seconds - 86340);
        }

        $hn_hour   = intval($pn_seconds / 3600);
        $hn_minute = intval(($pn_seconds - $hn_hour * 3600) / 60);
        $hn_second = is_float($pn_seconds) ?
                     fmod($pn_seconds, 60) :
                     $pn_seconds % 60;

        return array($hn_hour, $hn_minute, $hn_second);
    }


    // }}}
    // {{{ secondsPastTheHour()

    /**
     * Returns the no of seconds since the last hour o'clock (0-3599)
     *
     * @param int   $pn_minute the minute
     * @param mixed $pn_second the second as integer or float
     *
     * @return   mixed      integer or float from 0-3599
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function secondsPastTheHour($pn_minute, $pn_second)
    {
        return 60 * $pn_minute + $pn_second;
    }


    // }}}
    // {{{ addHours()

    /**
     * Returns the date the specified no of hours from the given date
     *
     * To subtract hours use a negative value for the '$pn_hours' parameter
     *
     * @param int $pn_hours hours to add
     * @param int $pn_day   the day of the month
     * @param int $pn_month the month
     * @param int $pn_year  the year
     * @param int $pn_hour  the hour
     *
     * @return   array      array of year, month, day, hour
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addHours($pn_hours, $pn_day, $pn_month, $pn_year, $pn_hour)
    {
        if ($pn_hours == 0)
            return array((int) $pn_year,
                         (int) $pn_month,
                         (int) $pn_day,
                         (int) $pn_hour);

        $hn_days = intval($pn_hours / 24);
        $hn_hour = $pn_hour + $pn_hours % 24;

        if ($hn_hour >= 24) {
            ++$hn_days;
            $hn_hour -= 24;
        } else if ($hn_hour < 0) {
            --$hn_days;
            $hn_hour += 24;
        }

        if ($hn_days == 0) {
            $hn_year  = $pn_year;
            $hn_month = $pn_month;
            $hn_day   = $pn_day;
        } else {
            list($hn_year, $hn_month, $hn_day) =
                explode(" ",
                        Date_Calc::addDays($hn_days,
                                           $pn_day,
                                           $pn_month,
                                           $pn_year,
                                           "%Y %m %d"));
        }

        return array((int) $hn_year, (int) $hn_month, (int) $hn_day, $hn_hour);
    }


    // }}}
    // {{{ addMinutes()

    /**
     * Returns the date the specified no of minutes from the given date
     *
     * To subtract minutes use a negative value for the '$pn_minutes' parameter
     *
     * @param int $pn_minutes minutes to add
     * @param int $pn_day     the day of the month
     * @param int $pn_month   the month
     * @param int $pn_year    the year
     * @param int $pn_hour    the hour
     * @param int $pn_minute  the minute
     *
     * @return   array      array of year, month, day, hour, minute
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addMinutes($pn_minutes,
                        $pn_day,
                        $pn_month,
                        $pn_year,
                        $pn_hour,
                        $pn_minute)
    {
        if ($pn_minutes == 0)
            return array((int) $pn_year,
                         (int) $pn_month,
                         (int) $pn_day,
                         (int) $pn_hour,
                         (int) $pn_minute);

        $hn_hours  = intval($pn_minutes / 60);
        $hn_minute = $pn_minute + $pn_minutes % 60;

        if ($hn_minute >= 60) {
            ++$hn_hours;
            $hn_minute -= 60;
        } else if ($hn_minute < 0) {
            --$hn_hours;
            $hn_minute += 60;
        }

        if ($hn_hours == 0) {
            $hn_year  = $pn_year;
            $hn_month = $pn_month;
            $hn_day   = $pn_day;
            $hn_hour  = $pn_hour;
        } else {
            list($hn_year, $hn_month, $hn_day, $hn_hour) =
                Date_Calc::addHours($hn_hours,
                                    $pn_day,
                                    $pn_month,
                                    $pn_year,
                                    $pn_hour);
        }

        return array($hn_year, $hn_month, $hn_day, $hn_hour, $hn_minute);
    }


    // }}}
    // {{{ addSeconds()

    /**
     * Returns the date the specified no of seconds from the given date
     *
     * If leap seconds are specified to be counted, the passed time must be UTC.
     * To subtract seconds use a negative value for the '$pn_seconds' parameter.
     *
     * N.B. the return type of the second part of the date is float if
     * either '$pn_seconds' or '$pn_second' is a float; otherwise, it
     * is integer.
     *
     * @param mixed $pn_seconds   seconds to add as integer or float
     * @param int   $pn_day       the day of the month
     * @param int   $pn_month     the month
     * @param int   $pn_year      the year
     * @param int   $pn_hour      the hour
     * @param int   $pn_minute    the minute
     * @param mixed $pn_second    the second as integer or float
     * @param bool  $pb_countleap whether to count leap seconds (defaults to
     *                             DATE_COUNT_LEAP_SECONDS)
     *
     * @return   array      array of year, month, day, hour, minute, second
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addSeconds($pn_seconds,
                        $pn_day,
                        $pn_month,
                        $pn_year,
                        $pn_hour,
                        $pn_minute,
                        $pn_second,
                        $pb_countleap = DATE_COUNT_LEAP_SECONDS)
    {
        if ($pn_seconds == 0)
            return array((int) $pn_year,
                         (int) $pn_month,
                         (int) $pn_day,
                         (int) $pn_hour,
                         (int) $pn_minute,
                         $pn_second);

        if ($pb_countleap) {
            $hn_seconds = $pn_seconds;

            $hn_day    = (int) $pn_day;
            $hn_month  = (int) $pn_month;
            $hn_year   = (int) $pn_year;
            $hn_hour   = (int) $pn_hour;
            $hn_minute = (int) $pn_minute;
            $hn_second = $pn_second;

            $hn_days = Date_Calc::dateToDays($pn_day,
                                             $pn_month,
                                             $pn_year);
            $hn_secondsofmonth = 86400 * ($hn_days -
                                          Date_Calc::firstDayOfMonth($pn_month,
                                                                     $pn_year)) +
                                 Date_Calc::secondsPastMidnight($pn_hour,
                                                                $pn_minute,
                                                                $pn_second);

            if ($hn_seconds > 0) {
                // Advance to end of month:
                //
                if ($hn_secondsofmonth != 0 &&
                    $hn_secondsofmonth + $hn_seconds >=
                    ($hn_secondsinmonth =
                         Date_Calc::getSecondsInMonth($hn_month, $hn_year))) {

                    $hn_seconds       -= $hn_secondsinmonth - $hn_secondsofmonth;
                    $hn_secondsofmonth = 0;
                    list($hn_year, $hn_month) =
                        Date_Calc::nextMonth($hn_month, $hn_year);
                    $hn_day  = Date_Calc::getFirstDayOfMonth($hn_month,
                                                             $hn_year);
                    $hn_hour = $hn_minute = $hn_second = 0;
                }

                // Advance to end of year:
                //
                if ($hn_secondsofmonth == 0 &&
                    $hn_month != Date_Calc::getFirstMonthOfYear($hn_year)) {

                    while ($hn_year == $pn_year &&
                           $hn_seconds >= ($hn_secondsinmonth =
                               Date_Calc::getSecondsInMonth($hn_month,
                                                            $hn_year))) {
                        $hn_seconds -= $hn_secondsinmonth;
                        list($hn_year, $hn_month) =
                            Date_Calc::nextMonth($hn_month, $hn_year);
                        $hn_day = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                $hn_year);
                    }
                }

                if ($hn_secondsofmonth == 0) {
                    // Add years:
                    //
                    if ($hn_month == Date_Calc::getFirstMonthOfYear($hn_year)) {
                        while ($hn_seconds >= ($hn_secondsinyear =
                                   Date_Calc::getSecondsInYear($hn_year))) {
                            $hn_seconds -= $hn_secondsinyear;
                            $hn_month    = Date_Calc::getFirstMonthOfYear(++$hn_year);
                            $hn_day      = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                         $hn_year);
                        }
                    }

                    // Add months:
                    //
                    while ($hn_seconds >= ($hn_secondsinmonth =
                               Date_Calc::getSecondsInMonth($hn_month, $hn_year))) {
                        $hn_seconds -= $hn_secondsinmonth;
                        list($hn_year, $hn_month) =
                            Date_Calc::nextMonth($hn_month, $hn_year);
                        $hn_day = Date_Calc::getFirstDayOfMonth($hn_month, $hn_year);
                    }
                }
            } else {
                //
                // (if $hn_seconds < 0)

                // Go back to start of month:
                //
                if ($hn_secondsofmonth != 0 &&
                    -$hn_seconds >= $hn_secondsofmonth) {

                    $hn_seconds       += $hn_secondsofmonth;
                    $hn_secondsofmonth = 0;
                    $hn_day            = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                       $hn_year);
                    $hn_hour           = $hn_minute = $hn_second = 0;
                }

                // Go back to start of year:
                //
                if ($hn_secondsofmonth == 0) {
                    while ($hn_month !=
                               Date_Calc::getFirstMonthOfYear($hn_year)) {

                        list($hn_year, $hn_prevmonth) =
                            Date_Calc::prevMonth($hn_month, $hn_year);

                        if (-$hn_seconds >= ($hn_secondsinmonth =
                                Date_Calc::getSecondsInMonth($hn_prevmonth,
                                                             $hn_year))) {
                            $hn_seconds += $hn_secondsinmonth;
                            $hn_month    = $hn_prevmonth;
                            $hn_day      = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                         $hn_year);
                        } else {
                            break;
                        }
                    }
                }

                if ($hn_secondsofmonth == 0) {
                    // Subtract years:
                    //
                    if ($hn_month == Date_Calc::getFirstMonthOfYear($hn_year)) {
                        while (-$hn_seconds >= ($hn_secondsinyear =
                                   Date_Calc::getSecondsInYear($hn_year - 1))) {
                            $hn_seconds += $hn_secondsinyear;
                            $hn_month    = Date_Calc::getFirstMonthOfYear(--$hn_year);
                            $hn_day      = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                         $hn_year);
                        }
                    }

                    // Subtract months:
                    //
                    list($hn_pmyear, $hn_prevmonth) =
                        Date_Calc::prevMonth($hn_month, $hn_year);
                    while (-$hn_seconds >= ($hn_secondsinmonth =
                               Date_Calc::getSecondsInMonth($hn_prevmonth,
                                                            $hn_pmyear))) {
                        $hn_seconds += $hn_secondsinmonth;
                        $hn_year     = $hn_pmyear;
                        $hn_month    = $hn_prevmonth;
                        $hn_day      = Date_Calc::getFirstDayOfMonth($hn_month,
                                                                     $hn_year);
                        list($hn_pmyear, $hn_prevmonth) =
                            Date_Calc::prevMonth($hn_month, $hn_year);
                    }
                }
            }

            if ($hn_seconds < 0 && $hn_secondsofmonth == 0) {
                list($hn_year, $hn_month) =
                    Date_Calc::prevMonth($hn_month, $hn_year);
                $hn_day = Date_Calc::getFirstDayOfMonth($hn_month, $hn_year);
                $hn_seconds += Date_Calc::getSecondsInMonth($hn_month, $hn_year);
            }

            $hn_seconds += Date_Calc::secondsPastMidnight($hn_hour,
                                                          $hn_minute,
                                                          $hn_second);
            if ($hn_seconds < 0) {
                $hn_daysadd = intval($hn_seconds / 86400) - 1;
            } else if ($hn_seconds < 86400) {
                $hn_daysadd = 0;
            } else {
                $hn_daysadd = intval($hn_seconds / 86400) - 1;
            }

            if ($hn_daysadd != 0) {
                list($hn_year, $hn_month, $hn_day) =
                    explode(" ",
                            Date_Calc::addDays($hn_daysadd,
                                               $hn_day,
                                               $hn_month,
                                               $hn_year,
                                               "%Y %m %d"));
                $hn_seconds -= $hn_daysadd * 86400;
            }

            $hn_secondsinday = Date_Calc::getSecondsInDay($hn_day,
                                                          $hn_month,
                                                          $hn_year);
            if ($hn_seconds >= $hn_secondsinday) {
                list($hn_year, $hn_month, $hn_day) =
                    explode(" ",
                            Date_Calc::addDays(1,
                                               $hn_day,
                                               $hn_month,
                                               $hn_year,
                                               "%Y %m %d"));
                $hn_seconds -= $hn_secondsinday;
            }

            list($hn_hour, $hn_minute, $hn_second) =
                Date_Calc::secondsPastMidnightToTime($hn_seconds);

            return array((int) $hn_year,
                         (int) $hn_month,
                         (int) $hn_day,
                         $hn_hour,
                         $hn_minute,
                         $hn_second);
        } else {
            // Assume every day has 86400 seconds exactly (ignore leap seconds):
            //
            $hn_minutes = intval($pn_seconds / 60);

            if (is_float($pn_seconds)) {
                $hn_second = $pn_second + fmod($pn_seconds, 60);
            } else {
                $hn_second = $pn_second + $pn_seconds % 60;
            }

            if ($hn_second >= 60) {
                ++$hn_minutes;
                $hn_second -= 60;
            } else if ($hn_second < 0) {
                --$hn_minutes;
                $hn_second += 60;
            }

            if ($hn_minutes == 0) {
                $hn_year   = $pn_year;
                $hn_month  = $pn_month;
                $hn_day    = $pn_day;
                $hn_hour   = $pn_hour;
                $hn_minute = $pn_minute;
            } else {
                list($hn_year, $hn_month, $hn_day, $hn_hour, $hn_minute) =
                    Date_Calc::addMinutes($hn_minutes,
                                          $pn_day,
                                          $pn_month,
                                          $pn_year,
                                          $pn_hour,
                                          $pn_minute);
            }

            return array($hn_year,
                         $hn_month,
                         $hn_day,
                         $hn_hour,
                         $hn_minute,
                         $hn_second);
        }
    }


    // }}}
    // {{{ dateToDays()

    /**
     * Converts a date in the proleptic Gregorian calendar to the no of days
     * since 24th November, 4714 B.C.
     *
     * Returns the no of days since Monday, 24th November, 4714 B.C. in the
     * proleptic Gregorian calendar (which is 24th November, -4713 using
     * 'Astronomical' year numbering, and 1st January, 4713 B.C. in the
     * proleptic Julian calendar).  This is also the first day of the 'Julian
     * Period' proposed by Joseph Scaliger in 1583, and the number of days
     * since this date is known as the 'Julian Day'.  (It is not directly
     * to do with the Julian calendar, although this is where the name
     * is derived from.)
     *
     * The algorithm is valid for all years (positive and negative), and
     * also for years preceding 4714 B.C.
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year (using 'Astronomical' year numbering)
     *
     * @return   int        the number of days since 24th November, 4714 B.C.
     * @access   public
     * @static
     */
    function dateToDays($day, $month, $year)
    {
        if ($month > 2) {
            // March = 0, April = 1, ..., December = 9,
            // January = 10, February = 11
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        $hb_negativeyear = $year < 0;
        $century         = intval($year / 100);
        $year            = $year % 100;

        if ($hb_negativeyear) {
            // Subtract 1 because year 0 is a leap year;
            // And N.B. that we must treat the leap years as occurring
            // one year earlier than they do, because for the purposes
            // of calculation, the year starts on 1st March:
            //
            return intval((14609700 * $century + ($year == 0 ? 1 : 0)) / 400) +
                   intval((1461 * $year + 1) / 4) +
                   intval((153 * $month + 2) / 5) +
                   $day + 1721118;
        } else {
            return intval(146097 * $century / 4) +
                   intval(1461 * $year / 4) +
                   intval((153 * $month + 2) / 5) +
                   $day + 1721119;
        }
    }


    // }}}
    // {{{ daysToDate()

    /**
     * Converts no of days since 24th November, 4714 B.C. (in the proleptic
     * Gregorian calendar, which is year -4713 using 'Astronomical' year
     * numbering) to Gregorian calendar date
     *
     * Returned date belongs to the proleptic Gregorian calendar, using
     * 'Astronomical' year numbering.
     *
     * The algorithm is valid for all years (positive and negative), and
     * also for years preceding 4714 B.C. (i.e. for negative 'Julian Days'),
     * and so the only limitation is platform-dependent (for 32-bit systems
     * the maximum year would be something like about 1,465,190 A.D.).
     *
     * N.B. Monday, 24th November, 4714 B.C. is Julian Day '0'.
     *
     * @param int    $days   the number of days since 24th November, 4714 B.C.
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function daysToDate($days, $format = DATE_CALC_FORMAT)
    {
        $days = intval($days);

        $days   -= 1721119;
        $century = floor((4 * $days - 1) / 146097);
        $days    = floor(4 * $days - 1 - 146097 * $century);
        $day     = floor($days / 4);

        $year = floor((4 * $day +  3) / 1461);
        $day  = floor(4 * $day +  3 - 1461 * $year);
        $day  = floor(($day +  4) / 4);

        $month = floor((5 * $day - 3) / 153);
        $day   = floor(5 * $day - 3 - 153 * $month);
        $day   = floor(($day +  5) /  5);

        $year = $century * 100 + $year;
        if ($month < 10) {
            $month +=3;
        } else {
            $month -=9;
            ++$year;
        }

        return Date_Calc::dateFormat($day, $month, $year, $format);
    }


    // }}}
    // {{{ getMonths()

    /**
     * Returns array of the month numbers, in order, for the given year
     *
     * @param int $pn_year the year (using 'Astronomical' year numbering)
     *
     * @return   array      array of integer month numbers, in order
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getMonths($pn_year)
    {
        // N.B. Month numbers can be skipped but not duplicated:
        //
        return array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    }


    // }}}
    // {{{ getMonthNames()

    /**
     * Returns an array of month names
     *
     * Used to take advantage of the setlocale function to return
     * language specific month names.
     *
     * TODO: cache values to some global array to avoid performance
     * hits when called more than once.
     *
     * @param int $pb_abbreviated whether to return the abbreviated form of the
     *                             months
     *
     * @return  array       associative array of integer month numbers, in
     *                       order, to month names
     * @access  public
     * @static
     */
    function getMonthNames($pb_abbreviated = false)
    {
        $ret = array();
        foreach (Date_Calc::getMonths(2001) as $i) {
            $ret[$i] = strftime($pb_abbreviated ? '%b' : '%B',
                                mktime(0, 0, 0, $i, 1, 2001));
        }
        return $ret;
    }


    // }}}
    // {{{ prevMonth()

    /**
     * Returns month and year of previous month
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   array      array of year, month as integers
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function prevMonth($pn_month, $pn_year)
    {
        $ha_months   = Date_Calc::getMonths($pn_year);
        $hn_monthkey = array_search($pn_month, $ha_months);
        if (array_key_exists($hn_monthkey - 1, $ha_months)) {
            return array((int) $pn_year, $ha_months[$hn_monthkey - 1]);
        } else {
            $ha_months = Date_Calc::getMonths($pn_year - 1);
            return array($pn_year - 1, end($ha_months));
        }
    }


    // }}}
    // {{{ nextMonth()

    /**
     * Returns month and year of next month
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   array      array of year, month as integers
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function nextMonth($pn_month, $pn_year)
    {
        $ha_months   = Date_Calc::getMonths($pn_year);
        $hn_monthkey = array_search($pn_month, $ha_months);
        if (array_key_exists($hn_monthkey + 1, $ha_months)) {
            return array((int) $pn_year, $ha_months[$hn_monthkey + 1]);
        } else {
            $ha_months = Date_Calc::getMonths($pn_year + 1);
            return array($pn_year + 1, $ha_months[0]);
        }
    }


    // }}}
    // {{{ addMonthsToDays()

    /**
     * Returns 'Julian Day' of the date the specified no of months
     * from the given date
     *
     * To subtract months use a negative value for the '$pn_months'
     * parameter
     *
     * @param int $pn_months months to add
     * @param int $pn_days   'Julian Day', i.e. the no of days since 1st
     *                        January, 4713 B.C.
     *
     * @return   int        'Julian Day', i.e. the no of days since 1st January,
     *                       4713 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addMonthsToDays($pn_months, $pn_days)
    {
        if ($pn_months == 0)
            return (int) $pn_days;

        list($hn_year, $hn_month, $hn_day) =
            explode(" ", Date_Calc::daysToDate($pn_days, "%Y %m %d"));

        $hn_retmonth = $hn_month + $pn_months % 12;
        $hn_retyear  = $hn_year + intval($pn_months / 12);
        if ($hn_retmonth < 1) {
            $hn_retmonth += 12;
            --$hn_retyear;
        } else if ($hn_retmonth > 12) {
            $hn_retmonth -= 12;
            ++$hn_retyear;
        }

        if (Date_Calc::isValidDate($hn_day, $hn_retmonth, $hn_retyear))
            return Date_Calc::dateToDays($hn_day, $hn_retmonth, $hn_retyear);

        // Calculate days since first of month:
        //
        $hn_dayoffset = $pn_days -
                        Date_Calc::firstDayOfMonth($hn_month, $hn_year);

        $hn_retmonthfirstday = Date_Calc::firstDayOfMonth($hn_retmonth,
                                                          $hn_retyear);
        $hn_retmonthlastday  = Date_Calc::lastDayOfMonth($hn_retmonth,
                                                         $hn_retyear);

        if ($hn_dayoffset > $hn_retmonthlastday - $hn_retmonthfirstday) {
            return $hn_retmonthlastday;
        } else {
            return $hn_retmonthfirstday + $hn_dayoffset;
        }
    }


    // }}}
    // {{{ addMonths()

    /**
     * Returns the date the specified no of months from the given date
     *
     * To subtract months use a negative value for the '$pn_months'
     * parameter
     *
     * @param int    $pn_months months to add
     * @param int    $pn_day    the day of the month, default is current local
     *                           day
     * @param int    $pn_month  the month, default is current local month
     * @param int    $pn_year   the year in four digit format, default is
     *                           current local year
     * @param string $ps_format string specifying how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addMonths($pn_months,
                       $pn_day,
                       $pn_month,
                       $pn_year,
                       $ps_format = DATE_CALC_FORMAT)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        if ($pn_months == 0)
            return Date_Calc::dateFormat($pn_day,
                                         $pn_month,
                                         $pn_year,
                                         $ps_format);

        $hn_days = Date_Calc::dateToDays($pn_day, $pn_month, $pn_year);
        return Date_Calc::daysToDate(Date_Calc::addMonthsToDays($pn_months,
                                                                $hn_days),
                                     $ps_format);
    }


    // }}}
    // {{{ addYearsToDays()

    /**
     * Returns 'Julian Day' of the date the specified no of years
     * from the given date
     *
     * To subtract years use a negative value for the '$pn_years'
     * parameter
     *
     * @param int $pn_years years to add
     * @param int $pn_days  'Julian Day', i.e. the no of days since 1st January,
     *                       4713 B.C.
     *
     * @return   int        'Julian Day', i.e. the no of days since 1st January,
     *                       4713 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addYearsToDays($pn_years, $pn_days)
    {
        if ($pn_years == 0)
            return (int) $pn_days;

        list($hn_year, $hn_month, $hn_day) =
            explode(" ", Date_Calc::daysToDate($pn_days, "%Y %m %d"));

        $hn_retyear = $hn_year + $pn_years;
        if (Date_Calc::isValidDate($hn_day, $hn_month, $hn_retyear))
            return Date_Calc::dateToDays($hn_day, $hn_month, $hn_retyear);

        $ha_months = Date_Calc::getMonths($hn_retyear);
        if (in_array($hn_month, $ha_months)) {
            $hn_retmonth = $hn_month;

            // Calculate days since first of month:
            //
            $hn_dayoffset = $pn_days - Date_Calc::firstDayOfMonth($hn_month,
                                                                  $hn_year);

            $hn_retmonthfirstday = Date_Calc::firstDayOfMonth($hn_retmonth,
                                                              $hn_retyear);
            $hn_retmonthlastday  = Date_Calc::lastDayOfMonth($hn_retmonth,
                                                             $hn_retyear);

            if ($hn_dayoffset > $hn_retmonthlastday - $hn_retmonthfirstday) {
                return $hn_retmonthlastday;
            } else {
                return $hn_retmonthfirstday + $hn_dayoffset;
            }
        } else {
            // Calculate days since first of year:
            //
            $hn_dayoffset = $pn_days - Date_Calc::firstDayOfYear($hn_year);

            $hn_retyearfirstday = Date_Calc::firstDayOfYear($hn_retyear);
            $hn_retyearlastday  = Date_Calc::lastDayOfYear($hn_retyear);

            if ($hn_dayoffset > $hn_retyearlastday - $hn_retyearfirstday) {
                return $hn_retyearlastday;
            } else {
                return $hn_retyearfirstday + $hn_dayoffset;
            }
        }
    }


    // }}}
    // {{{ addYears()

    /**
     * Returns the date the specified no of years from the given date
     *
     * To subtract years use a negative value for the '$pn_years'
     * parameter
     *
     * @param int    $pn_years  years to add
     * @param int    $pn_day    the day of the month, default is current local
     *                           day
     * @param int    $pn_month  the month, default is current local month
     * @param int    $pn_year   the year in four digit format, default is
     *                           current local year
     * @param string $ps_format string specifying how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addYears($pn_years,
                      $pn_day,
                      $pn_month,
                      $pn_year,
                      $ps_format = DATE_CALC_FORMAT)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        if ($pn_years == 0)
            return Date_Calc::dateFormat($pn_day,
                                         $pn_month,
                                         $pn_year,
                                         $ps_format);

        $hn_days = Date_Calc::dateToDays($pn_day, $pn_month, $pn_year);
        return Date_Calc::daysToDate(Date_Calc::addYearsToDays($pn_years,
                                                               $hn_days),
                                     $ps_format);
    }


    // }}}
    // {{{ addDays()

    /**
     * Returns the date the specified no of days from the given date
     *
     * To subtract days use a negative value for the '$pn_days' parameter
     *
     * @param int    $pn_days   days to add
     * @param int    $pn_day    the day of the month, default is current local
     *                           day
     * @param int    $pn_month  the month, default is current local month
     * @param int    $pn_year   the year in four digit format, default is
     *                           current local year
     * @param string $ps_format string specifying how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function addDays($pn_days,
                     $pn_day,
                     $pn_month,
                     $pn_year,
                     $ps_format = DATE_CALC_FORMAT)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        if ($pn_days == 0)
            return Date_Calc::dateFormat($pn_day,
                                         $pn_month,
                                         $pn_year,
                                         $ps_format);

        return Date_Calc::daysToDate(Date_Calc::dateToDays($pn_day,
                                                           $pn_month,
                                                           $pn_year) +
                                     $pn_days,
                                     $ps_format);
    }


    // }}}
    // {{{ getFirstDayOfMonth()

    /**
     * Returns first day of the specified month of specified year as integer
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   int        number of first day of month
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getFirstDayOfMonth($pn_month, $pn_year)
    {
        return 1;
    }


    // }}}
    // {{{ getLastDayOfMonth()

    /**
     * Returns last day of the specified month of specified year as integer
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   int        number of last day of month
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getLastDayOfMonth($pn_month, $pn_year)
    {
        return Date_Calc::daysInMonth($pn_month, $pn_year);
    }


    // }}}
    // {{{ firstDayOfMonth()

    /**
     * Returns the Julian Day of the first day of the month of the specified
     * year (i.e. the no of days since 24th November, 4714 B.C.)
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   integer    the number of days since 24th November, 4714 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function firstDayOfMonth($pn_month, $pn_year)
    {
        return Date_Calc::dateToDays(Date_Calc::getFirstDayOfMonth($pn_month,
                                                                   $pn_year),
                                     $pn_month,
                                     $pn_year);
    }


    // }}}
    // {{{ lastDayOfMonth()

    /**
     * Returns the Julian Day of the last day of the month of the specified
     * year (i.e. the no of days since 24th November, 4714 B.C.)
     *
     * @param int $pn_month the month
     * @param int $pn_year  the year (using 'Astronomical' year numbering)
     *
     * @return   integer    the number of days since 24th November, 4714 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function lastDayOfMonth($pn_month, $pn_year)
    {
        list($hn_nmyear, $hn_nextmonth) = Date_Calc::nextMonth($pn_month,
                                                               $pn_year);
        return Date_Calc::firstDayOfMonth($hn_nextmonth, $hn_nmyear) - 1;
    }


    // }}}
    // {{{ getFirstMonthOfYear()

    /**
     * Returns first month of specified year as integer
     *
     * @param int $pn_year the year (using 'Astronomical' year numbering)
     *
     * @return   int        number of first month of year
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function getFirstMonthOfYear($pn_year)
    {
        $ha_months = Date_Calc::getMonths($pn_year);
        return $ha_months[0];
    }


    // }}}
    // {{{ firstDayOfYear()

    /**
     * Returns the Julian Day of the first day of the year (i.e. the no of
     * days since 24th November, 4714 B.C.)
     *
     * @param int $pn_year the year (using 'Astronomical' year numbering)
     *
     * @return   integer    the number of days since 24th November, 4714 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function firstDayOfYear($pn_year)
    {
        return Date_Calc::firstDayOfMonth(Date_Calc::getFirstMonthOfYear($pn_year),
                                          $pn_year);
    }


    // }}}
    // {{{ lastDayOfYear()

    /**
     * Returns the Julian Day of the last day of the year (i.e. the no of
     * days since 24th November, 4714 B.C.)
     *
     * @param int $pn_year the year (using 'Astronomical' year numbering)
     *
     * @return   integer    the number of days since 24th November, 4714 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function lastDayOfYear($pn_year)
    {
        return Date_Calc::firstDayOfYear($pn_year + 1) - 1;
    }


    // }}}
    // {{{ dateToDaysJulian()

    /**
     * Converts a date in the proleptic Julian calendar to the no of days
     * since 1st January, 4713 B.C.
     *
     * Returns the no of days since Monday, 1st January, 4713 B.C. in the
     * proleptic Julian calendar (which is 1st January, -4712 using
     * 'Astronomical' year numbering, and 24th November, 4713 B.C. in the
     * proleptic Gregorian calendar).  This is also the first day of the 'Julian
     * Period' proposed by Joseph Scaliger in 1583, and the number of days
     * since this date is known as the 'Julian Day'.  (It is not directly
     * to do with the Julian calendar, although this is where the name
     * is derived from.)
     *
     * The algorithm is valid for all years (positive and negative), and
     * also for years preceding 4713 B.C.
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year (using 'Astronomical' year numbering)
     *
     * @return   int        the number of days since 1st January, 4713 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function dateToDaysJulian($day, $month, $year)
    {
        if ($month > 2) {
            // March = 0, April = 1, ..., December = 9,
            // January = 10, February = 11
            $month -= 3;
        } else {
            $month += 9;
            --$year;
        }

        $hb_negativeyear = $year < 0;

        if ($hb_negativeyear) {
            // Subtract 1 because year 0 is a leap year;
            // And N.B. that we must treat the leap years as occurring
            // one year earlier than they do, because for the purposes
            // of calculation, the year starts on 1st March:
            //
            return intval((1461 * $year + 1) / 4) +
                   intval((153 * $month + 2) / 5) +
                   $day + 1721116;
        } else {
            return intval(1461 * $year / 4) +
                   floor((153 * $month + 2) / 5) +
                   $day + 1721117;
        }
    }


    // }}}
    // {{{ daysToDateJulian()

    /**
     * Converts no of days since 1st January, 4713 B.C. (in the proleptic
     * Julian calendar, which is year -4712 using 'Astronomical' year
     * numbering) to Julian calendar date
     *
     * Returned date belongs to the proleptic Julian calendar, using
     * 'Astronomical' year numbering.
     *
     * @param int    $days   the number of days since 1st January, 4713 B.C.
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function daysToDateJulian($days, $format = DATE_CALC_FORMAT)
    {
        $days = intval($days);

        $days -= 1721117;
        $days  = floor(4 * $days - 1);
        $day   = floor($days / 4);

        $year = floor((4 * $day +  3) / 1461);
        $day  = floor(4 * $day +  3 - 1461 * $year);
        $day  = floor(($day +  4) / 4);

        $month = floor((5 * $day - 3) / 153);
        $day   = floor(5 * $day - 3 - 153 * $month);
        $day   = floor(($day +  5) /  5);

        if ($month < 10) {
            $month +=3;
        } else {
            $month -=9;
            ++$year;
        }

        return Date_Calc::dateFormat($day, $month, $year, $format);
    }


    // }}}
    // {{{ isoWeekDate()

    /**
     * Returns array defining the 'ISO Week Date' as defined in ISO 8601
     *
     * Expects a date in the proleptic Gregorian calendar using 'Astronomical'
     * year numbering, that is, with a year 0.  Algorithm is valid for all
     * years (positive and negative).
     *
     * N.B. the ISO week day no for Sunday is defined as 7, whereas this
     * class and its related functions defines Sunday as 0.
     *
     * @param int $pn_day   the day of the month
     * @param int $pn_month the month
     * @param int $pn_year  the year
     *
     * @return   array      array of ISO Year, ISO Week No, ISO Day No as
     *                       integers
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function isoWeekDate($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_jd = Date_Calc::dateToDays($pn_day, $pn_month, $pn_year);
        $hn_wd = Date_Calc::daysToDayOfWeek($hn_jd);
        if ($hn_wd == 0)
            $hn_wd = 7;

        $hn_jd1 = Date_Calc::firstDayOfYear($pn_year);
        $hn_day = $hn_jd - $hn_jd1 + 1;

        if ($hn_wd <= $hn_jd - Date_Calc::lastDayOfYear($pn_year) + 3) {
            // ISO week is the first week of the next ISO year:
            //
            $hn_year    = $pn_year + 1;
            $hn_isoweek = 1;
        } else {
            switch ($hn_wd1 = Date_Calc::daysToDayOfWeek($hn_jd1)) {
            case 1:
            case 2:
            case 3:
            case 4:
                // Monday - Thursday:
                //
                $hn_year    = $pn_year;
                $hn_isoweek = floor(($hn_day + $hn_wd1 - 2) / 7) + 1;
                break;
            case 0:
                $hn_wd1 = 7;
            case 5:
            case 6:
                // Friday - Sunday:
                //
                if ($hn_day <= 8 - $hn_wd1) {
                    // ISO week is the last week of the previous ISO year:
                    //
                    list($hn_year, $hn_lastmonth, $hn_lastday) =
                        explode(" ",
                                Date_Calc::daysToDate($hn_jd1 - 1, "%Y %m %d"));
                    list($hn_year, $hn_isoweek, $hn_pisoday) =
                        Date_Calc::isoWeekDate($hn_lastday,
                                               $hn_lastmonth,
                                               $hn_year);
                } else {
                    $hn_year    = $pn_year;
                    $hn_isoweek = floor(($hn_day + $hn_wd1 - 9) / 7) + 1;
                }

                break;
            }
        }

        return array((int) $hn_year, (int) $hn_isoweek, (int) $hn_wd);
    }


    // }}}
    // {{{ gregorianToISO()

    /**
     * Converts from Gregorian Year-Month-Day to ISO Year-WeekNumber-WeekDay
     *
     * Uses ISO 8601 definitions.
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year.  Use the complete year instead of the
     *                    abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   string     the date in ISO Year-WeekNumber-WeekDay format
     * @access   public
     * @static
     */
    function gregorianToISO($day, $month, $year)
    {
        list($yearnumber, $weeknumber, $weekday) =
            Date_Calc::isoWeekDate($day, $month, $year);
        return sprintf("%04d", $yearnumber) .
                       '-' .
                       sprintf("%02d", $weeknumber) .
                       '-' .
                       $weekday;
    }


    // }}}
    // {{{ weekOfYear4th()

    /**
     * Returns week of the year counting week 1 as the week that contains 4th
     * January
     *
     * Week 1 is determined to be the week that includes the 4th January, and
     * therefore can be defined as the first week of the year that has at least
     * 4 days.  The previous week is counted as week 52 or 53 of the previous
     * year.  Note that this definition depends on which day is the first day of
     * the week, and that if this is not passed as the '$pn_firstdayofweek'
     * parameter, the default is assumed.
     *
     * Note also that the last day week of the year is likely to extend into
     * the following year, except in the case that the last day of the week
     * falls on 31st December.
     *
     * Also note that this is very similar to the ISO week returned by
     * 'isoWeekDate()', the difference being that the ISO week always has
     * 7 days, and if the 4th of January is a Friday, for example,
     * ISO week 1 would start on Monday, 31st December in the previous year,
     * whereas the week defined by this function would start on 1st January,
     * but would be only 6 days long.  Of course you can also set the day
     * of the week, whereas the ISO week starts on a Monday by definition.
     *
     * Returned week is an integer from 1 to 53.
     *
     * @param int $pn_day            the day of the month, default is current
     *                                local day
     * @param int $pn_month          the month, default is current local month
     * @param int $pn_year           the year in four digit format, default is
     *                                current local year
     * @param int $pn_firstdayofweek optional integer specifying the first day
     *                                of the week
     *
     * @return   array      array of year, week no as integers
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfYear4th($pn_day = 0,
                           $pn_month = 0,
                           $pn_year = null,
                           $pn_firstdayofweek = DATE_CALC_BEGIN_WEEKDAY)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_wd1  = Date_Calc::daysToDayOfWeek(Date_Calc::firstDayOfYear($pn_year));
        $hn_day  = Date_Calc::dayOfYear($pn_day, $pn_month, $pn_year);
        $hn_week = floor(($hn_day +
                          (10 + $hn_wd1 - $pn_firstdayofweek) % 7 +
                          3) / 7);

        if ($hn_week > 0) {
            $hn_year = $pn_year;
        } else {
            // Week number is the last week of the previous year:
            //
            list($hn_year, $hn_lastmonth, $hn_lastday) =
                explode(" ",
                        Date_Calc::daysToDate(Date_Calc::lastDayOfYear($pn_year - 1),
                                              "%Y %m %d"));
            list($hn_year, $hn_week) =
                Date_Calc::weekOfYear4th($hn_lastday,
                                         $hn_lastmonth,
                                         $hn_year,
                                         $pn_firstdayofweek);
        }

        return array((int) $hn_year, (int) $hn_week);
    }


    // }}}
    // {{{ weekOfYear7th()

    /**
     * Returns week of the year counting week 1 as the week that contains 7th
     * January
     *
     * Week 1 is determined to be the week that includes the 7th January, and
     * therefore can be defined as the first full week of the year.  The
     * previous week is counted as week 52 or 53 of the previous year.  Note
     * that this definition depends on which day is the first day of the week,
     * and that if this is not passed as the '$pn_firstdayofweek' parameter, the
     * default is assumed.
     *
     * Note also that the last day week of the year is likely to extend into
     * the following year, except in the case that the last day of the week
     * falls on 31st December.
     *
     * Returned week is an integer from 1 to 53.
     *
     * @param int $pn_day            the day of the month, default is current
     *                                local day
     * @param int $pn_month          the month, default is current local month
     * @param int $pn_year           the year in four digit format, default is
     *                                current local year
     * @param int $pn_firstdayofweek optional integer specifying the first day
     *                                of the week
     *
     * @return   array      array of year, week no as integers
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfYear7th($pn_day = 0,
                           $pn_month = 0,
                           $pn_year = null,
                           $pn_firstdayofweek = DATE_CALC_BEGIN_WEEKDAY)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_wd1  = Date_Calc::daysToDayOfWeek(Date_Calc::firstDayOfYear($pn_year));
        $hn_day  = Date_Calc::dayOfYear($pn_day, $pn_month, $pn_year);
        $hn_week = floor(($hn_day + (6 + $hn_wd1 - $pn_firstdayofweek) % 7) / 7);

        if ($hn_week > 0) {
            $hn_year = $pn_year;
        } else {
            // Week number is the last week of the previous ISO year:
            //
            list($hn_year, $hn_lastmonth, $hn_lastday) = explode(" ", Date_Calc::daysToDate(Date_Calc::lastDayOfYear($pn_year - 1), "%Y %m %d"));
            list($hn_year, $hn_week) = Date_Calc::weekOfYear7th($hn_lastday, $hn_lastmonth, $hn_year, $pn_firstdayofweek);
        }

        return array((int) $hn_year, (int) $hn_week);
    }


    // }}}
    // {{{ dateSeason()

    /**
     * Determines julian date of the given season
     *
     * Adapted from previous work in Java by James Mark Hamilton.
     *
     * @param string $season the season to get the date for: VERNALEQUINOX,
     *                        SUMMERSOLSTICE, AUTUMNALEQUINOX,
     *                        or WINTERSOLSTICE
     * @param string $year   the year in four digit format.  Must be between
     *                        -1000 B.C. and 3000 A.D.
     *
     * @return   float      the julian date the season starts on
     * @access   public
     * @static
     */
    function dateSeason($season, $year = 0)
    {
        if ($year == '') {
            $year = Date_Calc::dateNow('%Y');
        }
        if (($year >= -1000) && ($year <= 1000)) {
            $y = $year / 1000.0;
            switch ($season) {
            case 'VERNALEQUINOX':
                $juliandate = (((((((-0.00071 * $y) - 0.00111) * $y) + 0.06134) * $y) + 365242.1374) * $y) + 1721139.29189;
                break;
            case 'SUMMERSOLSTICE':
                $juliandate = (((((((0.00025 * $y) + 0.00907) * $y) - 0.05323) * $y) + 365241.72562) * $y) + 1721233.25401;
                break;
            case 'AUTUMNALEQUINOX':
                $juliandate = (((((((0.00074 * $y) - 0.00297) * $y) - 0.11677) * $y) + 365242.49558) * $y) + 1721325.70455;
                break;
            case 'WINTERSOLSTICE':
            default:
                $juliandate = (((((((-0.00006 * $y) - 0.00933) * $y) - 0.00769) * $y) + 365242.88257) * $y) + 1721414.39987;
            }
        } elseif (($year > 1000) && ($year <= 3000)) {
            $y = ($year - 2000) / 1000;
            switch ($season) {
            case 'VERNALEQUINOX':
                $juliandate = (((((((-0.00057 * $y) - 0.00411) * $y) + 0.05169) * $y) + 365242.37404) * $y) + 2451623.80984;
                break;
            case 'SUMMERSOLSTICE':
                $juliandate = (((((((-0.0003 * $y) + 0.00888) * $y) + 0.00325) * $y) + 365241.62603) * $y) + 2451716.56767;
                break;
            case 'AUTUMNALEQUINOX':
                $juliandate = (((((((0.00078 * $y) + 0.00337) * $y) - 0.11575) * $y) + 365242.01767) * $y) + 2451810.21715;
                break;
            case 'WINTERSOLSTICE':
            default:
                $juliandate = (((((((0.00032 * $y) - 0.00823) * $y) - 0.06223) * $y) + 365242.74049) * $y) + 2451900.05952;
            }
        }
        return $juliandate;
    }


    // }}}
    // {{{ dayOfYear()

    /**
     * Returns number of days since 31 December of year before given date
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     *
     * @return   int
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function dayOfYear($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_jd  = Date_Calc::dateToDays($pn_day, $pn_month, $pn_year);
        $hn_jd1 = Date_Calc::firstDayOfYear($pn_year);
        return $hn_jd - $hn_jd1 + 1;
    }


    // }}}
    // {{{ julianDate()

    /**
     * Returns number of days since 31 December of year before given date
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     *
     * @return     int
     * @access     public
     * @static
     * @deprecated Method deprecated in Release [next version]
     */
    function julianDate($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        return Date_Calc::dayOfYear($pn_day, $pn_month, $pn_year);
    }


    // }}}
    // {{{ getWeekdayFullname()

    /**
     * Returns the full weekday name for the given date
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     *
     * @return   string     the full name of the day of the week
     * @access   public
     * @static
     */
    function getWeekdayFullname($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $weekday_names = Date_Calc::getWeekDays();
        $weekday       = Date_Calc::dayOfWeek($pn_day, $pn_month, $pn_year);
        return $weekday_names[$weekday];
    }


    // }}}
    // {{{ getWeekdayAbbrname()

    /**
     * Returns the abbreviated weekday name for the given date
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     * @param int $length   the length of abbreviation
     *
     * @return   string     the abbreviated name of the day of the week
     * @access   public
     * @static
     * @see      Date_Calc::getWeekdayFullname()
     */
    function getWeekdayAbbrname($pn_day = 0,
                                $pn_month = 0,
                                $pn_year = null,
                                $length = 3)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $weekday_names = Date_Calc::getWeekDays(true);
        $weekday       = Date_Calc::dayOfWeek($pn_day, $pn_month, $pn_year);
        return $weekday_names[$weekday];
    }


    // }}}
    // {{{ getMonthFullname()

    /**
     * Returns the full month name for the given month
     *
     * @param int $month the month
     *
     * @return   string     the full name of the month
     * @access   public
     * @static
     */
    function getMonthFullname($month)
    {
        $month = (int)$month;
        if (empty($month)) {
            $month = (int)Date_Calc::dateNow('%m');
        }

        $month_names = Date_Calc::getMonthNames();
        return $month_names[$month];
    }


    // }}}
    // {{{ getMonthAbbrname()

    /**
     * Returns the abbreviated month name for the given month
     *
     * @param int $month  the month
     * @param int $length the length of abbreviation
     *
     * @return   string     the abbreviated name of the month
     * @access   public
     * @static
     * @see      Date_Calc::getMonthFullname
     */
    function getMonthAbbrname($month, $length = 3)
    {
        $month = (int)$month;
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        $month_names = Date_Calc::getMonthNames(true);
        return $month_names[$month];
    }


    // }}}
    // {{{ getMonthFromFullname()

    /**
     * Returns the numeric month from the month name or an abreviation
     *
     * Both August and Aug would return 8.
     *
     * @param string $month the name of the month to examine.
     *                       Case insensitive.
     *
     * @return   int        the month's number
     * @access   public
     * @static
     */
    function getMonthFromFullName($month)
    {
        $month  = strtolower($month);
        $months = Date_Calc::getMonthNames();
        while (list($id, $name) = each($months)) {
            if (ereg($month, strtolower($name))) {
                return $id;
            }
        }
        return 0;
    }


    // }}}
    // {{{ getWeekDays()

    /**
     * Returns an array of week day names
     *
     * Used to take advantage of the setlocale function to return language
     * specific week days.
     *
     * @param int $pb_abbreviated whether to return the abbreviated form of the
     *                             days
     *
     * @return   array      an array of week-day names
     * @access   public
     * @static
     */
    function getWeekDays($pb_abbreviated = false)
    {
        for ($i = 0; $i < 7; $i++) {
            $weekdays[$i] = strftime($pb_abbreviated ? '%a' : '%A',
                                     mktime(0, 0, 0, 1, $i, 2001));
        }
        return $weekdays;
    }


    // }}}
    // {{{ daysToDayOfWeek()

    /**
     * Returns day of week for specified 'Julian Day'
     * 
     * The algorithm is valid for all years (positive and negative), and
     * also for years preceding 4714 B.C. (i.e. for negative 'Julian Days'),
     * and so the only limitation is platform-dependent (for 32-bit systems
     * the maximum year would be something like about 1,465,190 A.D.).
     *
     * N.B. Monday, 24th November, 4714 B.C. is Julian Day '0'.
     *
     * @param int $pn_days the number of days since 24th November, 4714 B.C.
     *
     * @return   int        integer from 0 to 7 where 0 represents Sunday
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function daysToDayOfWeek($pn_days)
    {
        // On Julian day 0 the day is Monday (PHP day 1):
        //
        $ret = ($pn_days + 1) % 7;
        return $ret < 0 ? $ret + 7 : $ret;
    }


    // }}}
    // {{{ dayOfWeek()

    /**
     * Returns day of week for given date (0 = Sunday)
     *
     * The algorithm is valid for all years (positive and negative).
     *
     * @param int $day   the day of the month, default is current local day
     * @param int $month the month, default is current local month
     * @param int $year  the year in four digit format, default is current
     *                    local year
     *
     * @return   int        the number of the day in the week
     * @access   public
     * @static
     */
    function dayOfWeek($day = null, $month = null, $year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        // if ($month <= 2) {
        //     $month += 12;
        //     --$year;
        // }

        // $wd = ($day +
        //        intval((13 * $month + 3) / 5) +
        //        $year +
        //        floor($year / 4) -
        //        floor($year / 100) +
        //        floor($year / 400) +
        //        1) % 7;

        // return (int) ($wd < 0 ? $wd + 7 : $wd);

        return Date_Calc::daysToDayOfWeek(Date_Calc::dateToDays($day,
                                                                $month,
                                                                $year));
    }


    // }}}
    // {{{ weekOfYearAbsolute()

    /**
     * Returns week of the year counting week 1 as 1st-7th January,
     * regardless of what day 1st January falls on
     *
     * Returned value is an integer from 1 to 53.  Week 53 will start on
     * 31st December and have only one day, except in a leap year, in
     * which it will start a day earlier and contain two days.
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     *
     * @return   int        integer from 1 to 53
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfYearAbsolute($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_day = Date_Calc::dayOfYear($pn_day, $pn_month, $pn_year);
        return intval(($hn_day + 6) / 7);
    }


    // }}}
    // {{{ weekOfYear1st()

    /**
     * Returns week of the year counting week 1 as the week that contains 1st
     * January
     *
     * Week 1 is determined to be the week that includes the 1st January, even
     * if this week extends into the previous year, in which case the week will
     * only contain between 1 and 6 days of the current year.  Note that this
     * definition depends on which day is the first day of the week, and that if
     * this is not passed as the '$pn_firstdayofweek' parameter, the default is
     * assumed.
     *
     * Note also that the last day week of the year is also likely to contain
     * less than seven days, except in the case that the last day of the week
     * falls on 31st December.
     *
     * Returned value is an integer from 1 to 54.  The year will only contain
     * 54 weeks in the case of a leap year in which 1st January is the last day
     * of the week, and 31st December is the first day of the week.  In this
     * case, both weeks 1 and 54 will contain one day only.
     *
     * @param int $pn_day            the day of the month, default is current
     *                                local day
     * @param int $pn_month          the month, default is current local month
     * @param int $pn_year           the year in four digit format, default is
     *                                current local year
     * @param int $pn_firstdayofweek optional integer specifying the first day
     *                                of the week
     *
     * @return   int        integer from 1 to 54
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfYear1st($pn_day = 0,
                           $pn_month = 0,
                           $pn_year = null,
                           $pn_firstdayofweek = DATE_CALC_BEGIN_WEEKDAY)
    {
        if (is_null($pn_year)) {
            $pn_year = Date_Calc::dateNow('%Y');
        }
        if (empty($pn_month)) {
            $pn_month = Date_Calc::dateNow('%m');
        }
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }

        $hn_wd1 = Date_Calc::daysToDayOfWeek(Date_Calc::firstDayOfYear($pn_year));
        $hn_day = Date_Calc::dayOfYear($pn_day, $pn_month, $pn_year);
        return floor(($hn_day + (7 + $hn_wd1 - $pn_firstdayofweek) % 7 + 6) / 7);
    }


    // }}}
    // {{{ weekOfYear()

    /**
     * Returns week of the year, where first Sunday is first day of first week
     *
     * N.B. this function is equivalent to calling:
     *
     *  <code>Date_Calc::weekOfYear7th($day, $month, $year, 0)</code>
     *
     * Returned week is an integer from 1 to 53.
     *
     * @param int $pn_day   the day of the month, default is current local day
     * @param int $pn_month the month, default is current local month
     * @param int $pn_year  the year in four digit format, default is current
     *                       local year
     *
     * @return     int        integer from 1 to 53
     * @access     public
     * @static
     * @see        Date_Calc::weekOfYear7th
     * @deprecated Method deprecated in Release [next version]
     */
    function weekOfYear($pn_day = 0, $pn_month = 0, $pn_year = null)
    {
        $ha_week = Date_Calc::weekOfYear7th($pn_day, $pn_month, $pn_year, 0);
        return $ha_week[1];
    }


    // }}}
    // {{{ weekOfMonthAbsolute()

    /**
     * Returns week of the month counting week 1 as 1st-7th of the month,
     * regardless of what day the 1st falls on
     *
     * Returned value is an integer from 1 to 5.  Week 5 will start on
     * the 29th of the month and have between 1 and 3 days, except
     * in February in a non-leap year, when there will be 4 weeks only.
     *
     * @param int $pn_day the day of the month, default is current local day
     *
     * @return   int        integer from 1 to 5
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfMonthAbsolute($pn_day = 0)
    {
        if (empty($pn_day)) {
            $pn_day = Date_Calc::dateNow('%d');
        }
        return intval(($pn_day + 6) / 7);
    }


    // }}}
    // {{{ weekOfMonth()

    /**
     * Alias for 'weekOfMonthAbsolute()'
     *
     * @param int $pn_day the day of the month, default is current local day
     *
     * @return   int        integer from 1 to 5
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function weekOfMonth($pn_day = 0)
    {
        return Date_Calc::weekOfMonthAbsolute($pn_day);
    }


    // }}}
    // {{{ quarterOfYear()

    /**
     * Returns quarter of the year for given date
     *
     * @param int $day   the day of the month, default is current local day
     * @param int $month the month, default is current local month
     * @param int $year  the year in four digit format, default is current
     *                    local year
     *
     * @return   int        the number of the quarter in the year
     * @access   public
     * @static
     */
    function quarterOfYear($day = 0, $month = 0, $year = null)
    {
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        return intval(($month - 1) / 3 + 1);
    }


    // }}}
    // {{{ daysInMonth()

    /**
     * Returns the number of days in the given month
     *
     * @param int $month the month, default is current local month
     * @param int $year  the year in four digit format, default is current
     *                    local year
     *
     * @return   int        the number of days the month has
     * @access   public
     * @static
     */
    function daysInMonth($month = 0, $year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        return Date_Calc::lastDayOfMonth($month, $year) -
               Date_Calc::firstDayOfMonth($month, $year) +
               1;
    }


    // }}}
    // {{{ daysInYear()

    /**
     * Returns the number of days in the given year
     *
     * @param int $year the year in four digit format, default is current local
     *                   year
     *
     * @return   int        the number of days the year has
     * @access   public
     * @static
     */
    function daysInYear($year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }

        return Date_Calc::firstDayOfYear($year + 1) - 
               Date_Calc::firstDayOfYear($year);
    }


    // }}}
    // {{{ weeksInMonth()

    /**
     * Returns the number of rows on a calendar month
     *
     * Useful for determining the number of rows when displaying a typical
     * month calendar.
     *
     * @param int $month the month, default is current local month
     * @param int $year  the year in four digit format, default is current
     *                    local year
     *
     * @return   int        the number of weeks the month has
     * @access   public
     * @static
     */
    function weeksInMonth($month = 0, $year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        $FDOM = Date_Calc::firstOfMonthWeekday($month, $year);
        if (DATE_CALC_BEGIN_WEEKDAY==1 && $FDOM==0) {
            $first_week_days = 7 - $FDOM + DATE_CALC_BEGIN_WEEKDAY;
            $weeks           = 1;
        } elseif (DATE_CALC_BEGIN_WEEKDAY==0 && $FDOM == 6) {
            $first_week_days = 7 - $FDOM + DATE_CALC_BEGIN_WEEKDAY;
            $weeks           = 1;
        } else {
            $first_week_days = DATE_CALC_BEGIN_WEEKDAY - $FDOM;
            $weeks           = 0;
        }
        $first_week_days %= 7;
        return ceil((Date_Calc::daysInMonth($month, $year)
                     - $first_week_days) / 7) + $weeks;
    }


    // }}}
    // {{{ getCalendarWeek()

    /**
     * Return an array with days in week
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   array      $week[$weekday]
     * @access   public
     * @static
     */
    function getCalendarWeek($day = 0, $month = 0, $year = null,
                             $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $week_array = array();

        // date for the column of week

        $curr_day = Date_Calc::beginOfWeek($day, $month, $year, '%E');

        for ($counter = 0; $counter <= 6; $counter++) {
            $week_array[$counter] = Date_Calc::daysToDate($curr_day, $format);
            $curr_day++;
        }
        return $week_array;
    }


    // }}}
    // {{{ getCalendarMonth()

    /**
     * Return a set of arrays to construct a calendar month for the given date
     *
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   array      $month[$row][$col]
     * @access   public
     * @static
     */
    function getCalendarMonth($month = 0, $year = null,
                              $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        $month_array = array();

        // date for the first row, first column of calendar month
        if (DATE_CALC_BEGIN_WEEKDAY == 1) {
            if (Date_Calc::firstOfMonthWeekday($month, $year) == 0) {
                $curr_day = Date_Calc::firstDayOfMonth($month, $year) - 6;
            } else {
                $curr_day = Date_Calc::firstDayOfMonth($month, $year)
                    - Date_Calc::firstOfMonthWeekday($month, $year) + 1;
            }
        } else {
            $curr_day = (Date_Calc::firstDayOfMonth($month, $year)
                - Date_Calc::firstOfMonthWeekday($month, $year));
        }

        // number of days in this month
        $daysInMonth = Date_Calc::daysInMonth($month, $year);

        $weeksInMonth = Date_Calc::weeksInMonth($month, $year);
        for ($row_counter = 0; $row_counter < $weeksInMonth; $row_counter++) {
            for ($column_counter = 0; $column_counter <= 6; $column_counter++) {
                $month_array[$row_counter][$column_counter] =
                        Date_Calc::daysToDate($curr_day, $format);
                $curr_day++;
            }
        }

        return $month_array;
    }


    // }}}
    // {{{ getCalendarYear()

    /**
     * Return a set of arrays to construct a calendar year for the given date
     *
     * @param int    $year   the year in four digit format, default current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   array      $year[$month][$row][$col]
     * @access   public
     * @static
     */
    function getCalendarYear($year = null, $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }

        $year_array = array();

        for ($curr_month = 0; $curr_month <= 11; $curr_month++) {
            $year_array[$curr_month] =
                    Date_Calc::getCalendarMonth($curr_month + 1,
                                                $year, $format);
        }

        return $year_array;
    }


    // }}}
    // {{{ prevDay()

    /**
     * Returns date of day before given date
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function prevDay($day = 0, $month = 0, $year = null,
                     $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        return Date_Calc::addDays(-1, $day, $month, $year, $format);
    }


    // }}}
    // {{{ nextDay()

    /**
     * Returns date of day after given date
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function nextDay($day = 0,
                     $month = 0,
                     $year = null,
                     $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        return Date_Calc::addDays(1, $day, $month, $year, $format);
    }


    // }}}
    // {{{ prevWeekday()

    /**
     * Returns date of the previous weekday, skipping from Monday to Friday
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function prevWeekday($day = 0, $month = 0, $year = null,
                         $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $days = Date_Calc::dateToDays($day, $month, $year);
        if (Date_Calc::dayOfWeek($day, $month, $year) == 1) {
            $days -= 3;
        } elseif (Date_Calc::dayOfWeek($day, $month, $year) == 0) {
            $days -= 2;
        } else {
            $days -= 1;
        }

        return Date_Calc::daysToDate($days, $format);
    }


    // }}}
    // {{{ nextWeekday()

    /**
     * Returns date of the next weekday of given date, skipping from
     * Friday to Monday
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function nextWeekday($day = 0, $month = 0, $year = null,
                         $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $days = Date_Calc::dateToDays($day, $month, $year);
        if (Date_Calc::dayOfWeek($day, $month, $year) == 5) {
            $days += 3;
        } elseif (Date_Calc::dayOfWeek($day, $month, $year) == 6) {
            $days += 2;
        } else {
            $days += 1;
        }

        return Date_Calc::daysToDate($days, $format);
    }


    // }}}
    // {{{ daysToPrevDayOfWeek()

    /**
     * Returns 'Julian Day' of the previous specific day of the week
     * from the given date.
     *
     * @param int  $dow        the day of the week (0 = Sunday)
     * @param int  $days       'Julian Day', i.e. the no of days since 1st
     *                          January, 4713 B.C.
     * @param bool $onorbefore if true and days are same, returns current day
     *
     * @return   int        'Julian Day', i.e. the no of days since 1st January,
     *                       4713 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function daysToPrevDayOfWeek($dow, $days, $onorbefore = false)
    {
        $curr_weekday = Date_Calc::daysToDayOfWeek($days);
        if ($curr_weekday == $dow) {
            if ($onorbefore) {
                return $days;
            } else {
                return $days - 7;
            }
        } else if ($curr_weekday < $dow) {
            return $days - 7 + $dow - $curr_weekday;
        } else {
            return $days - $curr_weekday + $dow;
        }
    }


    // }}}
    // {{{ prevDayOfWeek()

    /**
     * Returns date of the previous specific day of the week
     * from the given date
     *
     * @param int    $dow        the day of the week (0 = Sunday)
     * @param int    $day        the day of the month, default is current local
     *                            day
     * @param int    $month      the month, default is current local month
     * @param int    $year       the year in four digit format, default is
     *                            current local year
     * @param string $format     the string indicating how to format the output
     * @param bool   $onorbefore if true and days are same, returns current day
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function prevDayOfWeek($dow,
                           $day = 0,
                           $month = 0,
                           $year = null,
                           $format = DATE_CALC_FORMAT,
                           $onorbefore = false)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $days = Date_Calc::dateToDays($day, $month, $year);
        $days = Date_Calc::daysToPrevDayOfWeek($dow, $days, $onorbefore);
        return Date_Calc::daysToDate($days, $format);
    }


    // }}}
    // {{{ daysToNextDayOfWeek()

    /**
     * Returns 'Julian Day' of the next specific day of the week
     * from the given date.
     *
     * @param int  $dow       the day of the week (0 = Sunday)
     * @param int  $days      'Julian Day', i.e. the no of days since 1st
     *                         January, 4713 B.C.
     * @param bool $onorafter if true and days are same, returns current day
     *
     * @return   int        'Julian Day', i.e. the no of days since 1st January,
     *                       4713 B.C.
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function daysToNextDayOfWeek($dow, $days, $onorafter = false)
    {
        $curr_weekday = Date_Calc::daysToDayOfWeek($days);
        if ($curr_weekday == $dow) {
            if ($onorafter) {
                return $days;
            } else {
                return $days + 7;
            }
        } else if ($curr_weekday > $dow) {
            return $days + 7 - $curr_weekday + $dow;
        } else {
            return $days + $dow - $curr_weekday;
        }
    }


    // }}}
    // {{{ nextDayOfWeek()

    /**
     * Returns date of the next specific day of the week
     * from the given date
     *
     * @param int    $dow       the day of the week (0 = Sunday)
     * @param int    $day       the day of the month, default is current local
     *                           day
     * @param int    $month     the month, default is current local month
     * @param int    $year      the year in four digit format, default is
     *                           current local year
     * @param string $format    the string indicating how to format the output
     * @param bool   $onorafter if true and days are same, returns current day
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function nextDayOfWeek($dow,
                           $day = 0,
                           $month = 0,
                           $year = null,
                           $format = DATE_CALC_FORMAT,
                           $onorafter = false)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $days = Date_Calc::dateToDays($day, $month, $year);
        $days = Date_Calc::daysToNextDayOfWeek($dow, $days, $onorafter);
        return Date_Calc::daysToDate($days, $format);
    }


    // }}}
    // {{{ prevDayOfWeekOnOrBefore()

    /**
     * Returns date of the previous specific day of the week
     * on or before the given date
     *
     * @param int    $dow    the day of the week (0 = Sunday)
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function prevDayOfWeekOnOrBefore($dow,
                                     $day = 0,
                                     $month = 0,
                                     $year = null,
                                     $format = DATE_CALC_FORMAT)
    {
        return Date_Calc::prevDayOfWeek($dow,
                                        $day,
                                        $month,
                                        $year,
                                        $format,
                                        true);
    }


    // }}}
    // {{{ nextDayOfWeekOnOrAfter()

    /**
     * Returns date of the next specific day of the week
     * on or after the given date
     *
     * @param int    $dow    the day of the week (0 = Sunday)
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function nextDayOfWeekOnOrAfter($dow,
                                    $day = 0,
                                    $month = 0,
                                    $year = null,
                                    $format = DATE_CALC_FORMAT)
    {
        return Date_Calc::nextDayOfWeek($dow,
                                        $day,
                                        $month,
                                        $year,
                                        $format,
                                        true);
    }


    // }}}
    // {{{ beginOfWeek()

    /**
     * Find the month day of the beginning of week for given date,
     * using DATE_CALC_BEGIN_WEEKDAY
     *
     * Can return weekday of prev month.
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function beginOfWeek($day = 0, $month = 0, $year = null,
                         $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $hn_days      = Date_Calc::dateToDays($day, $month, $year);
        $this_weekday = Date_Calc::daysToDayOfWeek($hn_days);
        $interval     = (7 - DATE_CALC_BEGIN_WEEKDAY + $this_weekday) % 7;
        return Date_Calc::daysToDate($hn_days - $interval, $format);
    }


    // }}}
    // {{{ endOfWeek()

    /**
     * Find the month day of the end of week for given date,
     * using DATE_CALC_BEGIN_WEEKDAY
     *
     * Can return weekday of following month.
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function endOfWeek($day = 0, $month = 0, $year = null,
                       $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        $hn_days      = Date_Calc::dateToDays($day, $month, $year);
        $this_weekday = Date_Calc::daysToDayOfWeek($hn_days);
        $interval     = (6 + DATE_CALC_BEGIN_WEEKDAY - $this_weekday) % 7;
        return Date_Calc::daysToDate($hn_days + $interval, $format);
    }


    // }}}
    // {{{ beginOfPrevWeek()

    /**
     * Find the month day of the beginning of week before given date,
     * using DATE_CALC_BEGIN_WEEKDAY
     *
     * Can return weekday of prev month.
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function beginOfPrevWeek($day = 0, $month = 0, $year = null,
                             $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        list($hn_pwyear, $hn_pwmonth, $hn_pwday) =
            explode(" ", Date_Calc::daysToDate(Date_Calc::dateToDays($day,
                                                                     $month,
                                                                     $year) - 7,
                                               '%Y %m %d'));
        return Date_Calc::beginOfWeek($hn_pwday,
                                      $hn_pwmonth,
                                      $hn_pwyear,
                                      $format);
    }


    // }}}
    // {{{ beginOfNextWeek()

    /**
     * Find the month day of the beginning of week after given date,
     * using DATE_CALC_BEGIN_WEEKDAY
     *
     * Can return weekday of prev month.
     *
     * @param int    $day    the day of the month, default is current local day
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function beginOfNextWeek($day = 0, $month = 0, $year = null,
                             $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        if (empty($day)) {
            $day = Date_Calc::dateNow('%d');
        }

        list($hn_pwyear, $hn_pwmonth, $hn_pwday) =
            explode(" ",
                    Date_Calc::daysToDate(Date_Calc::dateToDays($day,
                                                                $month,
                                                                $year) + 7,
                                          '%Y %m %d'));
        return Date_Calc::beginOfWeek($hn_pwday,
                                      $hn_pwmonth,
                                      $hn_pwyear,
                                      $format);
    }


    // }}}
    // {{{ beginOfMonth()

    /**
     * Return date of first day of month of given date
     *
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string     the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::beginOfMonthBySpan()
     * @deprecated Method deprecated in Release 1.4.4
     */
    function beginOfMonth($month = 0, $year = null, $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        return Date_Calc::dateFormat(Date_Calc::getFirstDayOfMonth($month,
                                                                   $year),
                                     $month,
                                     $year,
                                     $format);
    }


    // }}}
    // {{{ endOfMonth()

    /**
     * Return date of last day of month of given date
     *
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string  the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::beginOfMonthBySpan()
     * @since      Method available since Release [next version]
     * @deprecated Method deprecated in Release [next version]
     */
    function endOfMonth($month = 0, $year = null, $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        return Date_Calc::daysToDate(Date_Calc::lastDayOfMonth($month, $year),
                                     $format);
    }


    // }}}
    // {{{ beginOfPrevMonth()

    /**
     * Returns date of the first day of previous month of given date
     *
     * @param mixed  $dummy  irrelevant parameter
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string     the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::beginOfMonthBySpan()
     * @deprecated Method deprecated in Release 1.4.4
     */
    function beginOfPrevMonth($dummy = null,
                              $month = 0,
                              $year = null,
                              $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        list($hn_pmyear, $hn_prevmonth) = Date_Calc::prevMonth($month, $year);
        return Date_Calc::dateFormat(Date_Calc::getFirstDayOfMonth($hn_prevmonth,
                                                                   $hn_pmyear),
                                     $hn_prevmonth,
                                     $hn_pmyear,
                                     $format);
    }


    // }}}
    // {{{ endOfPrevMonth()

    /**
     * Returns date of the last day of previous month for given date
     *
     * @param mixed  $dummy  irrelevant parameter
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string     the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::endOfMonthBySpan()
     * @deprecated Method deprecated in Release 1.4.4
     */
    function endOfPrevMonth($dummy = null,
                            $month = 0,
                            $year = null,
                            $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        return Date_Calc::daysToDate(Date_Calc::firstDayOfMonth($month,
                                                                $year) - 1,
                                     $format);
    }


    // }}}
    // {{{ beginOfNextMonth()

    /**
     * Returns date of begin of next month of given date
     *
     * @param mixed  $dummy  irrelevant parameter
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string     the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::beginOfMonthBySpan()
     * @deprecated Method deprecated in Release 1.4.4
     */
    function beginOfNextMonth($dummy = null,
                              $month = 0,
                              $year = null,
                              $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        list($hn_nmyear, $hn_nextmonth) = Date_Calc::nextMonth($month, $year);
        return Date_Calc::dateFormat(Date_Calc::getFirstDayOfMonth($hn_nextmonth,
                                                                   $hn_nmyear),
                                     $hn_nextmonth,
                                     $hn_nmyear,
                                     $format);
    }


    // }}}
    // {{{ endOfNextMonth()

    /**
     * Returns date of the last day of next month of given date
     *
     * @param mixed  $dummy  irrelevant parameter
     * @param int    $month  the month, default is current local month
     * @param int    $year   the year in four digit format, default is current
     *                        local year
     * @param string $format the string indicating how to format the output
     *
     * @return     string     the date in the desired format
     * @access     public
     * @static
     * @see        Date_Calc::endOfMonthBySpan()
     * @deprecated Method deprecated in Release 1.4.4
     */
    function endOfNextMonth($dummy = null,
                            $month = 0,
                            $year = null,
                            $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        list($hn_nmyear, $hn_nextmonth) = Date_Calc::nextMonth($month, $year);
        return Date_Calc::daysToDate(Date_Calc::lastDayOfMonth($hn_nextmonth,
                                                               $hn_nmyear),
                                     $format);
    }


    // }}}
    // {{{ beginOfMonthBySpan()

    /**
     * Returns date of the first day of the month in the number of months
     * from the given date
     *
     * @param int    $months the number of months from the date provided.
     *                        Positive numbers go into the future.
     *                        Negative numbers go into the past.
     *                        0 is the month presented in $month.
     * @param string $month  the month, default is current local month
     * @param string $year   the year in four digit format, default is the
     *                        current local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release 1.4.4
     */
    function beginOfMonthBySpan($months = 0,
                                $month = 0,
                                $year = null,
                                $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        return Date_Calc::addMonths($months, 
                                    Date_Calc::getFirstDayOfMonth($month, $year),
                                    $month,
                                    $year,
                                    $format);
    }


    // }}}
    // {{{ endOfMonthBySpan()

    /**
     * Returns date of the last day of the month in the number of months
     * from the given date
     *
     * @param int    $months the number of months from the date provided.
     *                        Positive numbers go into the future.
     *                        Negative numbers go into the past.
     *                        0 is the month presented in $month.
     * @param string $month  the month, default is current local month
     * @param string $year   the year in four digit format, default is the
     *                        current local year
     * @param string $format the string indicating how to format the output
     *
     * @return   string  the date in the desired format
     * @access   public
     * @static
     * @since    Method available since Release 1.4.4
     */
    function endOfMonthBySpan($months = 0,
                              $month = 0,
                              $year = null,
                              $format = DATE_CALC_FORMAT)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }

        $hn_days = Date_Calc::addMonthsToDays($months + 1,
            Date_Calc::firstDayOfMonth($month, $year)) - 1;
        return Date_Calc::daysToDate($hn_days, $format);
    }


    // }}}
    // {{{ firstOfMonthWeekday()

    /**
     * Find the day of the week for the first of the month of given date
     *
     * @param int $month the month, default is current local month
     * @param int $year  the year in four digit format, default is current
     *                    local year
     *
     * @return   int        number of weekday for the first day, 0=Sunday
     * @access   public
     * @static
     */
    function firstOfMonthWeekday($month = 0, $year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if (empty($month)) {
            $month = Date_Calc::dateNow('%m');
        }
        return Date_Calc::daysToDayOfWeek(Date_Calc::firstDayOfMonth($month,
                                                                     $year));
    }


    // }}}
    // {{{ nWeekdayOfMonth()

    /**
     * Calculates the date of the Nth weekday of the month,
     * such as the second Saturday of January 2000
     *
     * @param int    $week   the number of the week to get
     *                        (1 = first, etc.  Also can be 'last'.)
     * @param int    $dow    the day of the week (0 = Sunday)
     * @param int    $month  the month
     * @param int    $year   the year.  Use the complete year instead of the
     *                        abbreviated version.  E.g. use 2005, not 05.
     * @param string $format the string indicating how to format the output
     *
     * @return   string     the date in the desired format
     * @access   public
     * @static
     */
    function nWeekdayOfMonth($week, $dow, $month, $year,
                             $format = DATE_CALC_FORMAT)
    {
        if (is_numeric($week)) {
            $DOW1day = ($week - 1) * 7 + 1;
            $DOW1    = Date_Calc::dayOfWeek($DOW1day, $month, $year);
            $wdate   = ($week - 1) * 7 + 1 + (7 + $dow - $DOW1) % 7;
            if ($wdate > Date_Calc::daysInMonth($month, $year)) {
                return -1;
            } else {
                return Date_Calc::dateFormat($wdate, $month, $year, $format);
            }
        } elseif ($week == 'last' && $dow < 7) {
            $lastday = Date_Calc::daysInMonth($month, $year);
            $lastdow = Date_Calc::dayOfWeek($lastday, $month, $year);
            $diff    = $dow - $lastdow;
            if ($diff > 0) {
                return Date_Calc::dateFormat($lastday - (7 - $diff), $month,
                                             $year, $format);
            } else {
                return Date_Calc::dateFormat($lastday + $diff, $month,
                                             $year, $format);
            }
        } else {
            return -1;
        }
    }


    // }}}
    // {{{ isValidDate()

    /**
     * Returns true for valid date, false for invalid date
     *
     * Uses the proleptic Gregorian calendar, with the year 0 (1 B.C.)
     * assumed to be valid and also assumed to be a leap year.
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year.  Use the complete year instead of the
     *                    abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   bool
     * @access   public
     * @static
     */
    function isValidDate($day, $month, $year)
    {
        if ($day < 1 || $month < 1 || $month > 12)
            return false;
        if ($month == 2) {
            if (Date_Calc::isLeapYearGregorian($year)) {
                return $day <= 29;
            } else {
                return $day <= 28;
            }
        } elseif ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
            return $day <= 30;
        } else {
            return $day <= 31;
        }
    }


    // }}}
    // {{{ isLeapYearGregorian()

    /**
     * Returns true for a leap year, else false
     *
     * Uses the proleptic Gregorian calendar.  The year 0 (1 B.C.) is
     * assumed in this algorithm to be a leap year.  The function is
     * valid for all years, positive and negative.
     *
     * @param int $year the year.  Use the complete year instead of the
     *                   abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   bool
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function isLeapYearGregorian($year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        return (($year % 4 == 0) &&
                ($year % 100 != 0)) ||
               ($year % 400 == 0);
    }


    // }}}
    // {{{ isLeapYearJulian()

    /**
     * Returns true for a leap year, else false
     *
     * Uses the proleptic Julian calendar.  The year 0 (1 B.C.) is
     * assumed in this algorithm to be a leap year.  The function is
     * valid for all years, positive and negative.
     *
     * @param int $year the year.  Use the complete year instead of the
     *                   abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   boolean
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function isLeapYearJulian($year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        return $year % 4 == 0;
    }


    // }}}
    // {{{ isLeapYear()

    /**
     * Returns true for a leap year, else false
     *
     * @param int $year the year.  Use the complete year instead of the
     *                   abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   boolean
     * @access   public
     * @static
     */
    function isLeapYear($year = null)
    {
        if (is_null($year)) {
            $year = Date_Calc::dateNow('%Y');
        }
        if ($year < 1582) {
            // pre Gregorio XIII - 1582
            return Date_Calc::isLeapYearJulian($year);
        } else {
            // post Gregorio XIII - 1582
            return Date_Calc::isLeapYearGregorian($year);
        }
    }


    // }}}
    // {{{ isFutureDate()

    /**
     * Determines if given date is a future date from now
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year.  Use the complete year instead of the
     *                    abbreviated version.  E.g. use 2005, not 05.
     *
     * @return   bool
     * @access   public
     * @static
     */
    function isFutureDate($day, $month, $year)
    {
        $this_year  = Date_Calc::dateNow('%Y');
        $this_month = Date_Calc::dateNow('%m');
        $this_day   = Date_Calc::dateNow('%d');

        if ($year > $this_year) {
            return true;
        } elseif ($year == $this_year) {
            if ($month > $this_month) {
                return true;
            } elseif ($month == $this_month) {
                if ($day > $this_day) {
                    return true;
                }
            }
        }
        return false;
    }


    // }}}
    // {{{ isPastDate()

    /**
     * Determines if given date is a past date from now
     *
     * @param int $day   the day of the month
     * @param int $month the month
     * @param int $year  the year.  Use the complete year instead of the
     *                    abbreviated version.  E.g. use 2005, not 05.
     *            
     * @return   boolean
     * @access   public
     * @static
     */
    function isPastDate($day, $month, $year)
    {
        $this_year  = Date_Calc::dateNow('%Y');
        $this_month = Date_Calc::dateNow('%m');
        $this_day   = Date_Calc::dateNow('%d');

        if ($year < $this_year) {
            return true;
        } elseif ($year == $this_year) {
            if ($month < $this_month) {
                return true;
            } elseif ($month == $this_month) {
                if ($day < $this_day) {
                    return true;
                }
            }
        }
        return false;
    }


    // }}}
    // {{{ dateDiff()

    /**
     * Returns number of days between two given dates
     *
     * @param int $day1   the day of the month
     * @param int $month1 the month
     * @param int $year1  the year.  Use the complete year instead of the
     *                     abbreviated version.  E.g. use 2005, not 05.
     * @param int $day2   the day of the month
     * @param int $month2 the month
     * @param int $year2  the year.  Use the complete year instead of the
     *                     abbreviated version.  E.g. use 2005, not 05.
     *            
     * @return   int        the absolute number of days between the two dates.
     *                       If an error occurs, -1 is returned.
     * @access   public
     * @static
     */
    function dateDiff($day1, $month1, $year1, $day2, $month2, $year2)
    {
        if (!Date_Calc::isValidDate($day1, $month1, $year1)) {
            return -1;
        }
        if (!Date_Calc::isValidDate($day2, $month2, $year2)) {
            return -1;
        }
        return abs(Date_Calc::dateToDays($day1, $month1, $year1)
                   - Date_Calc::dateToDays($day2, $month2, $year2));
    }


    // }}}
    // {{{ compareDates()

    /**
     * Compares two dates
     *
     * @param int $day1   the day of the month
     * @param int $month1 the month
     * @param int $year1  the year.  Use the complete year instead of the
     *                     abbreviated version.  E.g. use 2005, not 05.
     * @param int $day2   the day of the month
     * @param int $month2 the month
     * @param int $year2  the year.  Use the complete year instead of the
     *                     abbreviated version.  E.g. use 2005, not 05.
     *            
     * @return   int        0 if the dates are equal. 1 if date 1 is later, -1
     *                       if date 1 is earlier.
     * @access   public
     * @static
     */
    function compareDates($day1, $month1, $year1, $day2, $month2, $year2)
    {
        $ndays1 = Date_Calc::dateToDays($day1, $month1, $year1);
        $ndays2 = Date_Calc::dateToDays($day2, $month2, $year2);
        if ($ndays1 == $ndays2) {
            return 0;
        }
        return ($ndays1 > $ndays2) ? 1 : -1;
    }


    // }}}
    // {{{ round()

    /**
     * Rounds the date according to the specified precision
     *
     * The precision parameter must be one of the following constants:
     *
     *  <code>DATE_PRECISION_YEAR</code>
     *  <code>DATE_PRECISION_MONTH</code>
     *  <code>DATE_PRECISION_DAY</code>
     *  <code>DATE_PRECISION_HOUR</code>
     *  <code>DATE_PRECISION_10MINUTES</code>
     *  <code>DATE_PRECISION_MINUTE</code>
     *  <code>DATE_PRECISION_10SECONDS</code>
     *  <code>DATE_PRECISION_SECOND</code>
     *
     * The precision can also be specified as an integral offset from
     * one of these constants, where the offset reflects a precision
     * of 10 to the power of the offset greater than the constant.
     * For example:
     *
     *  <code>DATE_PRECISION_YEAR - 1</code> rounds the date to the nearest 10
     *                                      years
     *  <code>DATE_PRECISION_YEAR - 3</code> rounds the date to the nearest 1000
     *                                      years
     *  <code>DATE_PRECISION_SECOND + 1</code> rounds the date to 1 decimal
     *                                        point of a second
     *  <code>DATE_PRECISION_SECOND + 1</code> rounds the date to 3 decimal
     *                                        points of a second
     *  <code>DATE_PRECISION_SECOND + 1</code> rounds the date to the nearest 10
     *                                        seconds (thus it is equivalent to
     *                                        DATE_PRECISION_10SECONDS)
     *
     * N.B. This function requires a time in UTC if both the precision is at
     * least DATE_PRECISION_SECOND and leap seconds are being counted, otherwise
     * any local time is acceptable.
     *
     * @param int   $pn_precision a 'DATE_PRECISION_*' constant
     * @param int   $pn_day       the day of the month
     * @param int   $pn_month     the month
     * @param int   $pn_year      the year
     * @param int   $pn_hour      the hour
     * @param int   $pn_minute    the minute
     * @param mixed $pn_second    the second as integer or float
     * @param bool  $pb_countleap whether to count leap seconds (defaults to
     *                             DATE_COUNT_LEAP_SECONDS)
     *
     * @return   array      array of year, month, day, hour, minute, second
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function round($pn_precision,
                   $pn_day,
                   $pn_month,
                   $pn_year,
                   $pn_hour = 0,
                   $pn_minute = 0,
                   $pn_second = 0,
                   $pb_countleap = DATE_COUNT_LEAP_SECONDS)
    {
        if ($pn_precision <= DATE_PRECISION_YEAR) {
            $hn_month  = 0;
            $hn_day    = 0;
            $hn_hour   = 0;
            $hn_minute = 0;
            $hn_second = 0;

            if ($pn_precision < DATE_PRECISION_YEAR) {
                $hn_year = round($pn_year, $pn_precision - DATE_PRECISION_YEAR);
            } else {
                // Check part-year:
                //
                $hn_midyear = (Date_Calc::firstDayOfYear($pn_year + 1) -
                               Date_Calc::firstDayOfYear($pn_year)) / 2;
                if (($hn_days = Date_Calc::dayOfYear($pn_day,
                                                     $pn_month,
                                                     $pn_year)) <=
                    $hn_midyear - 1) {
                    $hn_year = $pn_year;
                } else if ($hn_days >= $hn_midyear) {
                    // Round up:
                    //
                    $hn_year = $pn_year + 1;
                } else {
                    // Take time into account:
                    //
                    $hn_partday = Date_Calc::secondsPastMidnight($pn_hour,
                                                                 $pn_minute,
                                                                 $pn_second) /
                                  86400;
                    if ($hn_partday >= $hn_midyear - $hn_days) {
                        // Round up:
                        //
                        $hn_year = $pn_year + 1;
                    } else {
                        $hn_year = $pn_year;
                    }
                }
            }
        } else if ($pn_precision == DATE_PRECISION_MONTH) {
            $hn_year   = $pn_year;
            $hn_day    = 0;
            $hn_hour   = 0;
            $hn_minute = 0;
            $hn_second = 0;

            $hn_firstofmonth = Date_Calc::firstDayOfMonth($pn_month, $pn_year);
            $hn_midmonth     = (Date_Calc::lastDayOfMonth($pn_month, $pn_year) +
                                1 -
                                $hn_firstofmonth) / 2;
            if (($hn_days = Date_Calc::dateToDays($pn_day,
                                                  $pn_month,
                                                  $pn_year) -
                            $hn_firstofmonth) <= $hn_midmonth - 1) {
                $hn_month = $pn_month;
            } else if ($hn_days >= $hn_midmonth) {
                // Round up:
                //
                list($hn_year, $hn_month) = Date_Calc::nextMonth($pn_month,
                                                                 $pn_year);
            } else {
                // Take time into account:
                //
                $hn_partday = Date_Calc::secondsPastMidnight($pn_hour,
                                                              $pn_minute,
                                                              $pn_second) /
                              86400;
                if ($hn_partday >= $hn_midmonth - $hn_days) {
                    // Round up:
                    //
                    list($hn_year, $hn_month) = Date_Calc::nextMonth($pn_month,
                                                                     $pn_year);
                } else {
                    $hn_month = $pn_month;
                }
            }
        } else if ($pn_precision == DATE_PRECISION_DAY) {
            $hn_year   = $pn_year;
            $hn_month  = $pn_month;
            $hn_hour   = 0;
            $hn_minute = 0;
            $hn_second = 0;

            if (Date_Calc::secondsPastMidnight($pn_hour,
                                               $pn_minute,
                                               $pn_second) >= 43200) {
                // Round up:
                //
                list($hn_year, $hn_month, $hn_day) =
                    explode(" ", Date_Calc::nextDay($pn_day,
                                                    $pn_month,
                                                    $pn_year,
                                                    "%Y %m %d"));
            } else {
                $hn_day = $pn_day;
            }
        } else if ($pn_precision == DATE_PRECISION_HOUR) {
            $hn_year   = $pn_year;
            $hn_month  = $pn_month;
            $hn_day    = $pn_day;
            $hn_minute = 0;
            $hn_second = 0;

            if (Date_Calc::secondsPastTheHour($pn_minute, $pn_second) >= 1800) {
                // Round up:
                //
                list($hn_year, $hn_month, $hn_day, $hn_hour) =
                    Date_Calc::addHours(1,
                                        $pn_day,
                                        $pn_month,
                                        $pn_year,
                                        $pn_hour);
            } else {
                $hn_hour = $pn_hour;
            }
        } else if ($pn_precision <= DATE_PRECISION_MINUTE) {
            $hn_year   = $pn_year;
            $hn_month  = $pn_month;
            $hn_day    = $pn_day;
            $hn_hour   = $pn_hour;
            $hn_second = 0;

            if ($pn_precision < DATE_PRECISION_MINUTE) {
                $hn_minute = round($pn_minute,
                                   $pn_precision - DATE_PRECISION_MINUTE);
            } else {
                // Check seconds:
                //
                if ($pn_second >= 30) {
                    // Round up:
                    //
                    list($hn_year,
                         $hn_month,
                         $hn_day,
                         $hn_hour,
                         $hn_minute) =
                        Date_Calc::addMinutes(1,
                                              $pn_day,
                                              $pn_month,
                                              $pn_year,
                                              $pn_hour,
                                              $pn_minute);
                } else {
                    $hn_minute = $pn_minute;
                }
            }
        } else {
            // Precision is at least (DATE_PRECISION_SECOND - 1):
            //
            $hn_year   = $pn_year;
            $hn_month  = $pn_month;
            $hn_day    = $pn_day;
            $hn_hour   = $pn_hour;
            $hn_minute = $pn_minute;

            $hn_second = round($pn_second,
                               $pn_precision - DATE_PRECISION_SECOND);

            if (fmod($hn_second, 1) == 0.0) {
                $hn_second = (int) $hn_second;

                if ($hn_second != intval($pn_second)) {
                    list($hn_year,
                         $hn_month,
                         $hn_day,
                         $hn_hour,
                         $hn_minute,
                         $hn_second) =
                        Date_Calc::addSeconds($hn_second - intval($pn_second),
                                              $pn_day,
                                              $pn_month,
                                              $pn_year,
                                              $pn_hour,
                                              $pn_minute,
                                              intval($pn_second),
                                              $pn_precision >=
                                                  DATE_PRECISION_SECOND &&
                                              $pb_countleap);
                        //
                        // (N.B. if rounded to nearest 10 seconds,
                        // user does not expect seconds to be '60')
                }
            }
        }

        return array((int) $hn_year,
                     (int) $hn_month,
                     (int) $hn_day,
                     (int) $hn_hour,
                     (int) $hn_minute,
                     $hn_second);
    }


    // }}}
    // {{{ roundSeconds()

    /**
     * Rounds seconds up or down to the nearest specified unit
     *
     * @param int   $pn_precision number of digits after the decimal point
     * @param int   $pn_day       the day of the month
     * @param int   $pn_month     the month
     * @param int   $pn_year      the year
     * @param int   $pn_hour      the hour
     * @param int   $pn_minute    the minute
     * @param mixed $pn_second    the second as integer or float
     * @param bool  $pb_countleap whether to count leap seconds (defaults to
     *                             DATE_COUNT_LEAP_SECONDS)
     *
     * @return   array      array of year, month, day, hour, minute, second
     * @access   public
     * @static
     * @since    Method available since Release [next version]
     */
    function roundSeconds($pn_precision,
                          $pn_day,
                          $pn_month,
                          $pn_year,
                          $pn_hour,
                          $pn_minute,
                          $pn_second,
                          $pb_countleap = DATE_COUNT_LEAP_SECONDS)
    {
        return Date_Calc::round(DATE_PRECISION_SECOND + $pn_precision,
                                $pn_day,
                                $pn_month,
                                $pn_year,
                                $pn_hour,
                                $pn_minute,
                                $pn_second);
    }


    // }}}

}

// }}}


/*
 * Local variables:
 * mode: php
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
?>