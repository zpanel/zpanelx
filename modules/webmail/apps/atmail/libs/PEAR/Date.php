<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

// {{{ Header

/**
 * Generic date handling class for PEAR
 *
 * Handles time zones and changes from local standard to local Summer
 * time (daylight-saving time) through the Date_TimeZone class.
 * Supports several operations from Date_Calc on Date objects.
 *
 * PHP versions 4 and 5
 *
 * LICENSE:
 *
 * Copyright (c) 1997-2007 Baba Buehler, Pierre-Alain Joye, Firman
 * Wandayandi, C.A. Woodcock
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
 * @author     Baba Buehler <baba@babaz.com>
 * @author     Pierre-Alain Joye <pajoye@php.net>
 * @author     Firman Wandayandi <firman@php.net>
 * @author     C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright  1997-2007 Baba Buehler, Pierre-Alain Joye, Firman Wandayandi, C.A. Woodcock
 * @license    http://www.opensource.org/licenses/bsd-license.php
 *             BSD License
 * @version    CVS: $Id: Date.php,v 1.89 2008/03/23 18:34:16 c01234 Exp $
 * @link       http://pear.php.net/package/Date
 */


// }}}
// {{{ Error constants

define('DATE_ERROR_INVALIDDATE', 1);
define('DATE_ERROR_INVALIDTIME', 2);
define('DATE_ERROR_INVALIDTIMEZONE', 3);
define('DATE_ERROR_INVALIDDATEFORMAT', 4);
define('DATE_ERROR_INVALIDFORMATSTRING', 5);


// }}}
// {{{ Includes

require_once 'PEAR.php';

/**
 * Load Date_TimeZone
 */
require_once 'Date/TimeZone.php';

/**
 * Load Date_Calc
 */
require_once 'Date/Calc.php';

/**
 * Load Date_Span
 */
require_once 'Date/Span.php';


// }}}
// {{{ General constants

/**
 * Whether to capture the micro-time (in microseconds) by default
 * in calls to 'Date::setNow()'.  Note that this makes a call to
 * 'gettimeofday()', which may not work on all systems.
 *
 * @since    Constant available since Release 1.5.0
 */
define('DATE_CAPTURE_MICROTIME_BY_DEFAULT', false);

/**
 * Whether to correct, by adding the local Summer time offset, the
 * specified time if it falls in the 'skipped hour' (encountered
 * when the clocks go forward).
 *
 * N.B. if specified as 'false', and if a time zone that adjusts
 * for Summer time is specified, then an object of this class will
 * be set to a semi-invalid state if an invalid time is set.  That
 * is, an error will not be returned, unless the user then calls
 * a function, directly or indirectly, that accesses the time
 * part of the object.  So, for example, if the user calls:
 *
 *  <code>$date_object->format2('HH.MI.SS')</code> or:
 *  <code>$date->object->addSeconds(30)</code>,
 *
 * an error will be returned if the time is invalid.  However,
 * if the user calls:
 *
 *  <code>$date->object->addDays(1)</code>,
 *
 * for example, such that the time is no longer invalid, then the
 * object will no longer be in this invalid state.  This behaviour
 * is intended to minimize unexpected errors when a user uses the
 * class to do addition with days only, and does not intend to
 * access the time.
 *
 * Of course, this constant will be unused if the user chooses to
 * work in UTC or a time zone without Summer time, in which case
 * this situation will never arise.
 *
 * This constant is set to 'true' by default for backwards-compatibility
 * reasons, however, you are recommended to set it to 'false'.  Note that the
 * behaviour is not intended to match that of previous versions of the class
 * in terms of ignoring the Summer time offset when making calculations which
 * involve dates in both standard and Summer time - this was recognized as a
 * bug - but in terms of returning a PEAR error object when the user sets the
 * object to an invalid date (i.e. a time in the hour which is skipped when
 * the clocks go forwards, which in Europe would be a time such as 01.30).
 * Backwards compatibility here means that the behaviour is the same as it
 * used to be, less the bug.
 *
 * Note that this problem is not an issue for the user if:
 *
 *  (a) the user uses a time zone that does not observe Summer time, e.g. UTC
 *  (b) the user never accesses the time, that is, he never makes a call to
 *       Date::getHour() or Date::format("%H"), for example, even if he sets
 *       the time to something invalid
 *  (c) the user sets DATE_CORRECTINVALIDTIME_DEFAULT to true
 *
 * @since    Constant available since Release 1.5.0
 */
define('DATE_CORRECTINVALIDTIME_DEFAULT', true);

/**
 * Whether to validate dates (i.e. day-month-year, ignoring the time) by
 * disallowing invalid dates (e.g. 31st February) being set by the following
 * functions:
 *
 *  Date::setYear()
 *  Date::setMonth()
 *  Date::setDay()
 *
 * If the constant is set to 'true', then the date will be checked (by
 * default), and if invalid, an error will be returned with the Date object
 * left unmodified.
 *
 * This constant is set to 'false' by default for backwards-compatibility
 * reasons, however, you are recommended to set it to 'true'.
 *
 * Note that setHour(), setMinute(), setSecond() and setPartSecond()
 * allow an invalid date/time to be set regardless of the value of this
 * constant.
 *
 * @since    Constant available since Release 1.5.0
 */
define('DATE_VALIDATE_DATE_BY_DEFAULT', false);

/**
 * Whether, by default, to accept times including leap seconds (i.e. '23.59.60')
 * when setting the date/time, and whether to count leap seconds in the
 * following functions:
 *
 *  Date::addSeconds()
 *  Date::subtractSeconds()
 *  Date_Calc::addSeconds()
 *  Date::round()
 *  Date::roundSeconds()
 *
 * This constant is set to 'false' by default for backwards-compatibility
 * reasons, however, you are recommended to set it to 'true'.
 *
 * Note that this constant does not affect Date::addSpan() and
 * Date::subtractSpan() which will not count leap seconds in any case.
 *
 * @since    Constant available since Release 1.5.0
 */
define('DATE_COUNT_LEAP_SECONDS', false);


// }}}
// {{{ Output format constants (used in 'Date::getDate()')

/**
 * "YYYY-MM-DD HH:MM:SS"
 */
define('DATE_FORMAT_ISO', 1);

/**
 * "YYYYMMSSTHHMMSS(Z|(+/-)HHMM)?"
 */
define('DATE_FORMAT_ISO_BASIC', 2);

/**
 * "YYYY-MM-SSTHH:MM:SS(Z|(+/-)HH:MM)?"
 */
define('DATE_FORMAT_ISO_EXTENDED', 3);

/**
 * "YYYY-MM-SSTHH:MM:SS(.S*)?(Z|(+/-)HH:MM)?"
 */
define('DATE_FORMAT_ISO_EXTENDED_MICROTIME', 6);

/**
 * "YYYYMMDDHHMMSS"
 */
define('DATE_FORMAT_TIMESTAMP', 4);

/**
 * long int, seconds since the unix epoch
 */
define('DATE_FORMAT_UNIXTIME', 5);


// }}}
// {{{ Class: Date

/**
 * Generic date handling class for PEAR
 *
 * Supports time zones with the Date_TimeZone class.  Supports several
 * operations from Date_Calc on Date objects.
 *
 * Note to developers: the class stores the local time and date in the
 * local standard time.  That is, it does not store the time as the
 * local Summer time when and if the time zone is in Summer time.  It
 * is much easier to store local standard time and remember to offset
 * it when the user requests it.
 *
 * @category  Date and Time
 * @package   Date
 * @author    Baba Buehler <baba@babaz.com>
 * @author    Pierre-Alain Joye <pajoye@php.net>
 * @author    Firman Wandayandi <firman@php.net>
 * @author    C.A. Woodcock <c01234@netcomuk.co.uk>
 * @copyright 1997-2007 Baba Buehler, Pierre-Alain Joye, Firman Wandayandi, C.A. Woodcock
 * @license   http://www.opensource.org/licenses/bsd-license.php
 *            BSD License
 * @version   Release: 1.5.0a1
 * @link      http://pear.php.net/package/Date
 */
class Date
{

    // {{{ Properties

    /**
     * The year
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $year;

    /**
     * The month
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $month;

    /**
     * The day
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $day;

    /**
     * The hour
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $hour;

    /**
     * The minute
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $minute;

    /**
     * The second
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $second;

    /**
     * The parts of a second
     *
     * @var      float
     * @access   private
     * @since    Property available since Release 1.4.3
     */
    var $partsecond;

    /**
     * The year in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardyear;

    /**
     * The month in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardmonth;

    /**
     * The day in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardday;

    /**
     * The hour in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardhour;

    /**
     * The minute in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardminute;

    /**
     * The second in local standard time
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardsecond;

    /**
     * The part-second in local standard time
     *
     * @var      float
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $on_standardpartsecond;

    /**
     * Whether the object should accept and count leap seconds
     *
     * @var      bool
     * @access   private
     * @since    Property available since Release 1.5.0
     */
    var $ob_countleapseconds;

    /**
     * Whether the time is valid as a local time (an invalid time
     * is one that lies in the 'skipped hour' at the point that
     * the clocks go forward)
     *
     * @var      bool
     * @access   private
     * @see      Date::isTimeValid()
     * @since    Property available since Release 1.5.0
     */
    var $ob_invalidtime = null;

    /**
     * Date_TimeZone object for this date
     *
     * @var      object     Date_TimeZone object
     * @access   private
     * @since    Property available since Release 1.0
     */
    var $tz;

    /**
     * Defines the default weekday abbreviation length
     *
     * Formerly used by Date::format(), but now redundant - the abbreviation
     * for the current locale of the machine is used.
     *
     * @var      int
     * @access   private
     * @since    Property available since Release 1.4.4
     */
    var $getWeekdayAbbrnameLength = 3;


    // }}}
    // {{{ Constructor

    /**
     * Constructor
     *
     * Creates a new Date Object initialized to the current date/time in the
     * system-default timezone by default.  A date optionally
     * passed in may be in the ISO 8601, TIMESTAMP or UNIXTIME format,
     * or another Date object.  If no date is passed, the current date/time
     * is used.
     *
     * If a date is passed and an exception is returned by 'setDate()'
     * there is nothing that this function can do, so for this reason, it
     * is advisable to pass no parameter and to make a separate call to
     * 'setDate()'.  A date/time should only be passed if known to be a
     * valid ISO 8601 string or a valid Unix timestamp.
     *
     * @param mixed $date                optional ISO 8601 date/time to initialize;
     *                                    or, a Unix time stamp
     * @param bool  $pb_countleapseconds whether to count leap seconds
     *                                    (defaults to DATE_COUNT_LEAP_SECONDS)
     *
     * @return   void
     * @access   public
     * @see      Date::setDate()
     */
    function Date($date = null,
                  $pb_countleapseconds = DATE_COUNT_LEAP_SECONDS)
    {
        $this->ob_countleapseconds = $pb_countleapseconds;

        if (is_a($date, 'Date')) {
            $this->copy($date);
        } else {
            if (!is_null($date)) {
                // 'setDate()' expects a time zone to be already set:
                //
                $this->_setTZToDefault();
                $this->setDate($date);
            } else {
                $this->setNow();
            }
        }
    }


    // }}}
    // {{{ copy()

    /**
     * Copy values from another Date object
     *
     * Makes this Date a copy of another Date object.  This is a
     * PHP4-compatible implementation of '__clone()' in PHP5.
     *
     * @param object $date Date object to copy
     *
     * @return   void
     * @access   public
     */
    function copy($date)
    {
        $this->year       = $date->year;
        $this->month      = $date->month;
        $this->day        = $date->day;
        $this->hour       = $date->hour;
        $this->minute     = $date->minute;
        $this->second     = $date->second;
        $this->partsecond = $date->partsecond;

        $this->on_standardyear       = $date->on_standardyear;
        $this->on_standardmonth      = $date->on_standardmonth;
        $this->on_standardday        = $date->on_standardday;
        $this->on_standardhour       = $date->on_standardhour;
        $this->on_standardminute     = $date->on_standardminute;
        $this->on_standardsecond     = $date->on_standardsecond;
        $this->on_standardpartsecond = $date->on_standardpartsecond;

        $this->ob_countleapseconds = $date->ob_countleapseconds;
        $this->ob_invalidtime      = $date->ob_invalidtime;

        $this->tz = new Date_TimeZone($date->getTZID());

        $this->getWeekdayAbbrnameLength = $date->getWeekdayAbbrnameLength;
    }


    // }}}
    // {{{ __clone()

    /**
     * Copy values from another Date object
     *
     * Makes this Date a copy of another Date object.  For PHP5
     * only.
     *
     * @return   void
     * @access   public
     * @see      Date::copy()
     */
    function __clone()
    {
        // This line of code would be preferable, but will only
        // compile in PHP5:
        //
        // $this->tz = clone $this->tz;

        $this->tz = new Date_TimeZone($this->getTZID());
    }


    // }}}
    // {{{ setDate()

    /**
     * Sets the fields of a Date object based on the input date and format
     *
     * Format parameter should be one of the specified DATE_FORMAT_* constants:
     *
     *  <code>DATE_FORMAT_ISO</code>
     *                              - 'YYYY-MM-DD HH:MI:SS'
     *  <code>DATE_FORMAT_ISO_BASIC</code>
     *                              - 'YYYYMMSSTHHMMSS(Z|(+/-)HHMM)?'
     *  <code>DATE_FORMAT_ISO_EXTENDED</code>
     *                              - 'YYYY-MM-SSTHH:MM:SS(Z|(+/-)HH:MM)?'
     *  <code>DATE_FORMAT_ISO_EXTENDED_MICROTIME</code>
     *                              - 'YYYY-MM-SSTHH:MM:SS(.S*)?(Z|(+/-)HH:MM)?'
     *  <code>DATE_FORMAT_TIMESTAMP</code>
     *                              - 'YYYYMMDDHHMMSS'
     *  <code>DATE_FORMAT_UNIXTIME'</code>
     *                              - long integer of the no of seconds since
     *                                 the Unix Epoch
     *                                 (1st January 1970 00.00.00 GMT)
     *
     * @param string $date                   input date
     * @param int    $format                 optional format constant
     *                                        (DATE_FORMAT_*) of the input date.
     *                                        This parameter is not needed,
     *                                        except to force the setting of the
     *                                        date from a Unix time-stamp
     *                                        (DATE_FORMAT_UNIXTIME).
     * @param bool   $pb_repeatedhourdefault value to return if repeated
     *                                        hour is specified (defaults
     *                                        to false)
     *
     * @return   void
     * @access   public
     */
    function setDate($date,
                     $format = DATE_FORMAT_ISO,
                     $pb_repeatedhourdefault = false)
    {

        if (preg_match('/^([0-9]{4,4})-?(0[1-9]|1[0-2])-?(0[1-9]|[12][0-9]|3[01])' .
                         '([T\s]?([01][0-9]|2[0-3]):?' .             // [hh]
                         '([0-5][0-9]):?([0-5][0-9]|60)(\.\d+)?' .   // [mi]:[ss]
                         '(Z|[+\-][0-9]{2,2}(:?[0-5][0-9])?)?)?$/i', // offset
                         $date, $regs) &&
            $format != DATE_FORMAT_UNIXTIME
            ) {
            // DATE_FORMAT_ISO, ISO_BASIC, ISO_EXTENDED, and TIMESTAMP
            // These formats are extremely close to each other.  This regex
            // is very loose and accepts almost any butchered format you could
            // throw at it.  e.g. 2003-10-07 19:45:15 and 2003-10071945:15
            // are the same thing in the eyes of this regex, even though the
            // latter is not a valid ISO 8601 date.

            if (!Date_Calc::isValidDate($regs[3], $regs[2], $regs[1])) {
                return PEAR::raiseError("'" .
                                        Date_Calc::dateFormat($regs[1],
                                                              $regs[2],
                                                              $regs[3],
                                                              "%Y-%m-%d") .
                                        "' is invalid calendar date",
                                        DATE_ERROR_INVALIDDATE);
            }

            if (isset($regs[9])) {
                if ($regs[9] == "Z") {
                    $this->tz = new Date_TimeZone("UTC");
                } else {
                    $this->tz = new Date_TimeZone("UTC" . $regs[9]);
                }
            }

            $this->setLocalTime($regs[3],
                                $regs[2],
                                $regs[1],
                                isset($regs[5]) ? $regs[5] : 0,
                                isset($regs[6]) ? $regs[6] : 0,
                                isset($regs[7]) ? $regs[7] : 0,
                                isset($regs[8]) ? $regs[8] : 0.0,
                                $pb_repeatedhourdefault);

        } else if (is_numeric($date)) {
            // Unix Time; N.B. Unix Time is defined relative to GMT,
            // so it needs to be adjusted for the current time zone;
            // however we do not know if it is in Summer time until
            // we have converted it from Unix time:
            //

            // Get current time zone details:
            //
            $hs_id = $this->getTZID();

            // Input Unix time as UTC:
            //
            $this->tz = new Date_TimeZone("UTC");
            $this->setDate(gmdate("Y-m-d H:i:s", $date));

            // Convert back to correct time zone:
            //
            $this->convertTZByID($hs_id);
        } else {
            return PEAR::raiseError("Date not in ISO 8601 format",
                                    DATE_ERROR_INVALIDDATEFORMAT);
        }
    }


    // }}}
    // {{{ setNow()

    /**
     * Sets to local current time and time zone
     *
     * @param bool $pb_setmicrotime whether to set micro-time (defaults to the
     *                               value of the constant
     *                               DATE_CAPTURE_MICROTIME_BY_DEFAULT)
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function setNow($pb_setmicrotime = DATE_CAPTURE_MICROTIME_BY_DEFAULT)
    {
        $this->_setTZToDefault();

        if ($pb_setmicrotime) {
            $ha_unixtime = gettimeofday();
        } else {
            $ha_unixtime = array("sec" => time());
        }

        $this->setDate(date("Y-m-d H:i:s", $ha_unixtime["sec"]) .
                       (isset($ha_unixtime["usec"]) ?
                        "." . sprintf("%06d", $ha_unixtime["usec"]) :
                        ""));
    }


    // }}}
    // {{{ round()

    /**
     * Rounds the date according to the specified precision (defaults
     * to nearest day)
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
     * N.B. the default is DATE_PRECISION_DAY
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
     *  <code>DATE_PRECISION_SECOND + 3</code> rounds the date to 3 decimal
     *                                        points of a second
     *  <code>DATE_PRECISION_SECOND - 1</code> rounds the date to the nearest 10
     *                                        seconds (thus it is equivalent to
     *                                        DATE_PRECISION_10SECONDS)
     *
     * @param int  $pn_precision          a 'DATE_PRECISION_*' constant
     * @param bool $pb_correctinvalidtime whether to correct, by adding the
     *                                     local Summer time offset, the rounded
     *                                     time if it falls in the skipped hour
     *                                     (defaults to
     *                                     DATE_CORRECTINVALIDTIME_DEFAULT)
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function round($pn_precision = DATE_PRECISION_DAY,
                   $pb_correctinvalidtime = DATE_CORRECTINVALIDTIME_DEFAULT)
    {
        if ($pn_precision <= DATE_PRECISION_DAY) {
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour,
                 $hn_minute,
                 $hn_secondraw) =
                 Date_Calc::round($pn_precision,
                                  $this->day,
                                  $this->month,
                                  $this->year,
                                  $this->hour,
                                  $this->minute,
                                  $this->partsecond == 0.0 ?
                                      $this->second :
                                      $this->second + $this->partsecond,
                                  $this->ob_countleapseconds);
            if (is_float($hn_secondraw)) {
                $hn_second     = intval($hn_secondraw);
                $hn_partsecond = $hn_secondraw - $hn_second;
            } else {
                $hn_second     = $hn_secondraw;
                $hn_partsecond = 0.0;
            }

            $this->setLocalTime($hn_day,
                                $hn_month,
                                $hn_year,
                                $hn_hour,
                                $hn_minute,
                                $hn_second,
                                $hn_partsecond,
                                true, // This is unlikely anyway, but the
                                      // day starts with the repeated hour
                                      // the first time around
                                $pb_correctinvalidtime);
            return;
        }

        // ($pn_precision >= DATE_PRECISION_HOUR)
        //
        if ($this->tz->getDSTSavings() % 3600000 == 0 ||
            ($this->tz->getDSTSavings() % 60000 == 0 &&
             $pn_precision >= DATE_PRECISION_MINUTE)
            ) {
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour,
                 $hn_minute,
                 $hn_secondraw) =
                 Date_Calc::round($pn_precision,
                                  $this->on_standardday,
                                  $this->on_standardmonth,
                                  $this->on_standardyear,
                                  $this->on_standardhour,
                                  $this->on_standardminute,
                                  $this->on_standardpartsecond == 0.0 ?
                                      $this->on_standardsecond :
                                      $this->on_standardsecond +
                                          $this->on_standardpartsecond,
                                  $this->ob_countleapseconds);
            if (is_float($hn_secondraw)) {
                $hn_second     = intval($hn_secondraw);
                $hn_partsecond = $hn_secondraw - $hn_second;
            } else {
                $hn_second     = $hn_secondraw;
                $hn_partsecond = 0.0;
            }

            $this->setStandardTime($hn_day,
                                   $hn_month,
                                   $hn_year,
                                   $hn_hour,
                                   $hn_minute,
                                   $hn_second,
                                   $hn_partsecond);
            return;
        }

        // Very unlikely anyway (as I write, the only time zone like this
        // is Lord Howe Island in Australia (offset of half an hour)):
        //
        // (This algorithm could be better)
        //
        list($hn_year,
             $hn_month,
             $hn_day,
             $hn_hour,
             $hn_minute,
             $hn_secondraw) =
             Date_Calc::round($pn_precision,
                              $this->day,
                              $this->month,
                              $this->year,
                              $this->hour,
                              $this->minute,
                              $this->partsecond == 0.0 ?
                                  $this->second :
                                  $this->second + $this->partsecond,
                              $this->ob_countleapseconds);
        if (is_float($hn_secondraw)) {
            $hn_second     = intval($hn_secondraw);
            $hn_partsecond = $hn_secondraw - $hn_second;
        } else {
            $hn_second     = $hn_secondraw;
            $hn_partsecond = 0.0;
        }

        $this->setLocalTime($hn_day,
                            $hn_month,
                            $hn_year,
                            $hn_hour,
                            $hn_minute,
                            $hn_second,
                            $hn_partsecond,
                            false, // This will be right half the time
                            $pb_correctinvalidtime);   // This will be right
                                                       // some of the time
                                                       // (depends on Summer
                                                       // time offset)
    }


    // }}}
    // {{{ roundSeconds()

    /**
     * Rounds seconds up or down to the nearest specified unit
     *
     * N.B. this function is equivalent to calling:
     *  <code>'round(DATE_PRECISION_SECOND + $pn_precision)'</code>
     *
     * @param int  $pn_precision          number of digits after the decimal point
     * @param bool $pb_correctinvalidtime whether to correct, by adding the
     *                                     local Summer time offset, the rounded
     *                                     time if it falls in the skipped hour
     *                                     (defaults to
     *                                     DATE_CORRECTINVALIDTIME_DEFAULT)
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function roundSeconds($pn_precision = 0,
                          $pb_correctinvalidtime = DATE_CORRECTINVALIDTIME_DEFAULT)
    {
        $this->round(DATE_PRECISION_SECOND + $pn_precision,
                     $pb_correctinvalidtime);
    }


    // }}}
    // {{{ trunc()

    /**
     * Truncates the date according to the specified precision (by
     * default, it truncates the time part of the date)
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
     * N.B. the default is DATE_PRECISION_DAY
     *
     * The precision can also be specified as an integral offset from
     * one of these constants, where the offset reflects a precision
     * of 10 to the power of the offset greater than the constant.
     * For example:
     *
     *  <code>DATE_PRECISION_YEAR</code> truncates the month, day and time
     *                                  part of the year
     *  <code>DATE_PRECISION_YEAR - 1</code> truncates the unit part of the
     *                                      year, e.g. 1987 becomes 1980
     *  <code>DATE_PRECISION_YEAR - 3</code> truncates the hundreds part of the
     *                                      year, e.g. 1987 becomes 1000
     *  <code>DATE_PRECISION_SECOND + 1</code> truncates the part of the second
     *                                        less than 0.1 of a second, e.g.
     *                                        3.26301 becomes 3.2 seconds
     *  <code>DATE_PRECISION_SECOND + 3</code> truncates the part of the second
     *                                        less than 0.001 of a second, e.g.
     *                                        3.26301 becomes 3.263 seconds
     *  <code>DATE_PRECISION_SECOND - 1</code> truncates the unit part of the
     *                                        seconds (thus it is equivalent to
     *                                        DATE_PRECISION_10SECONDS)
     *
     * @param int  $pn_precision          a 'DATE_PRECISION_*' constant
     * @param bool $pb_correctinvalidtime whether to correct, by adding the
     *                                     local Summer time offset, the
     *                                     truncated time if it falls in the
     *                                     skipped hour (defaults to
     *                                     DATE_CORRECTINVALIDTIME_DEFAULT)
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function trunc($pn_precision = DATE_PRECISION_DAY,
                   $pb_correctinvalidtime = DATE_CORRECTINVALIDTIME_DEFAULT)
    {
        if ($pn_precision <= DATE_PRECISION_DAY) {
            if ($pn_precision <= DATE_PRECISION_YEAR) {
                $hn_month      = 0;
                $hn_day        = 0;
                $hn_hour       = 0;
                $hn_minute     = 0;
                $hn_second     = 0;
                $hn_partsecond = 0.0;

                $hn_invprecision = DATE_PRECISION_YEAR - $pn_precision;
                if ($hn_invprecision > 0) {
                    $hn_year = intval($this->year / pow(10, $hn_invprecision)) *
                               pow(10, $hn_invprecision);
                    //
                    // (Conversion to int necessary for PHP <= 4.0.6)
                } else {
                    $hn_year = $this->year;
                }
            } else if ($pn_precision == DATE_PRECISION_MONTH) {
                $hn_year       = $this->year;
                $hn_month      = $this->month;
                $hn_day        = 0;
                $hn_hour       = 0;
                $hn_minute     = 0;
                $hn_second     = 0;
                $hn_partsecond = 0.0;
            } else if ($pn_precision == DATE_PRECISION_DAY) {
                $hn_year       = $this->year;
                $hn_month      = $this->month;
                $hn_day        = $this->day;
                $hn_hour       = 0;
                $hn_minute     = 0;
                $hn_second     = 0;
                $hn_partsecond = 0.0;
            }

            $this->setLocalTime($hn_day,
                                $hn_month,
                                $hn_year,
                                $hn_hour,
                                $hn_minute,
                                $hn_second,
                                $hn_partsecond,
                                true, // This is unlikely anyway, but the
                                      // day starts with the repeated
                                      // hour the first time around
                                $pb_correctinvalidtime);
            return;
        }

        // Precision is at least equal to DATE_PRECISION_HOUR
        //
        if ($pn_precision == DATE_PRECISION_HOUR) {
            $this->addSeconds($this->partsecond == 0.0 ?
                              -$this->second :
                              -$this->second - $this->partsecond);
            //
            // (leap seconds irrelevant)

            $this->addMinutes(-$this->minute);
        } else if ($pn_precision <= DATE_PRECISION_MINUTE) {
            if ($pn_precision == DATE_PRECISION_10MINUTES) {
                $this->addMinutes(-$this->minute % 10);
            }

            $this->addSeconds($this->partsecond == 0.0 ?
                              -$this->second :
                              -$this->second - $this->partsecond);
            //
            // (leap seconds irrelevant)

        } else if ($pn_precision == DATE_PRECISION_10SECONDS) {
            $this->addSeconds($this->partsecond == 0.0 ?
                              -$this->second % 10 :
                              (-$this->second % 10) - $this->partsecond);
            //
            // (leap seconds irrelevant)

        } else {
            // Assume Summer time offset cannot be composed of part-seconds:
            //
            $hn_precision  = $pn_precision - DATE_PRECISION_SECOND;
            $hn_partsecond = intval($this->on_standardpartsecond *
                                    pow(10, $hn_precision)) /
                                    pow(10, $hn_precision);
            $this->setStandardTime($this->on_standardday,
                                   $this->on_standardmonth,
                                   $this->on_standardyear,
                                   $this->on_standardhour,
                                   $this->on_standardminute,
                                   $this->on_standardsecond,
                                   $hn_partsecond);
        }
    }


    // }}}
    // {{{ truncSeconds()

    /**
     * Truncates seconds according to the specified precision
     *
     * N.B. this function is equivalent to calling:
     *  <code>'Date::trunc(DATE_PRECISION_SECOND + $pn_precision)'</code>
     *
     * @param int  $pn_precision          number of digits after the decimal point
     * @param bool $pb_correctinvalidtime whether to correct, by adding the
     *                                     local Summer time offset, the
     *                                     truncated time if it falls in the
     *                                     skipped hour (defaults to
     *                                     DATE_CORRECTINVALIDTIME_DEFAULT)
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function truncSeconds($pn_precision = 0,
                          $pb_correctinvalidtime = DATE_CORRECTINVALIDTIME_DEFAULT)
    {
        $this->trunc(DATE_PRECISION_SECOND + $pn_precision,
                     $pb_correctinvalidtime);
    }


    // }}}
    // {{{ getDate()

    /**
     * Gets a string (or other) representation of this date
     *
     * Returns a date in the format specified by the DATE_FORMAT_* constants.
     *
     * @param int $format format constant (DATE_FORMAT_*) of the output date
     *
     * @return   string     the date in the requested format
     * @access   public
     */
    function getDate($format = DATE_FORMAT_ISO)
    {
        switch ($format) {
        case DATE_FORMAT_ISO:
            return $this->format("%Y-%m-%d %T");
            break;
        case DATE_FORMAT_ISO_BASIC:
            $format = "%Y%m%dT%H%M%S";
            if ($this->getTZID() == 'UTC') {
                $format .= "Z";
            }
            return $this->format($format);
            break;
        case DATE_FORMAT_ISO_EXTENDED:
            $format = "%Y-%m-%dT%H:%M:%S";
            if ($this->getTZID() == 'UTC') {
                $format .= "Z";
            }
            return $this->format($format);
            break;
        case DATE_FORMAT_ISO_EXTENDED_MICROTIME:
            $format = "%Y-%m-%dT%H:%M:%s";
            if ($this->getTZID() == 'UTC') {
                $format .= "Z";
            }
            return $this->format($format);
            break;
        case DATE_FORMAT_TIMESTAMP:
            return $this->format("%Y%m%d%H%M%S");
            break;
        case DATE_FORMAT_UNIXTIME:
            // Enter a time in UTC, so use 'gmmktime()' (the alternative
            // is to offset additionally by the local time, but the object
            // is not necessarily using local time):
            //
            if ($this->ob_invalidtime)
                return $this->_getErrorInvalidTime();

            return gmmktime($this->on_standardhour,
                            $this->on_standardminute,
                            $this->on_standardsecond,
                            $this->on_standardmonth,
                            $this->on_standardday,
                            $this->on_standardyear) -
                   $this->tz->getRawOffset() / 1000; // N.B. Unix-time excludes
                                                     // leap seconds by
                                                     // definition
            break;
        }
    }


    // }}}
    // {{{ format()

    /**
     *  Date pretty printing, similar to strftime()
     *
     *  Formats the date in the given format, much like
     *  strftime().  Most strftime() options are supported.<br><br>
     *
     *  Formatting options:<br><br>
     *
     *  <code>%a  </code>  abbreviated weekday name (Sun, Mon, Tue) <br>
     *  <code>%A  </code>  full weekday name (Sunday, Monday, Tuesday) <br>
     *  <code>%b  </code>  abbreviated month name (Jan, Feb, Mar) <br>
     *  <code>%B  </code>  full month name (January, February, March) <br>
     *  <code>%C  </code>  century number (the year divided by 100 and truncated
     *                     to an integer, range 00 to 99) <br>
     *  <code>%d  </code>  day of month (range 00 to 31) <br>
     *  <code>%D  </code>  equivalent to "%m/%d/%y" <br>
     *  <code>%e  </code>  day of month without leading noughts (range 0 to 31) <br>
     *  <code>%E  </code>  Julian day - no of days since Monday, 24th November,
     *                     4714 B.C. (in the proleptic Gregorian calendar) <br>
     *  <code>%g  </code>  like %G, but without the century <br>
     *  <code>%G  </code>  the 4-digit year corresponding to the ISO week
     *                     number (see %V). This has the same format and value
     *                     as %Y, except that if the ISO week number belongs
     *                     to the previous or next year, that year is used
     *                     instead. <br>
     *  <code>%h  </code>  hour as decimal number without leading noughts (0
     *                     to 23) <br>
     *  <code>%H  </code>  hour as decimal number (00 to 23) <br>
     *  <code>%i  </code>  hour as decimal number on 12-hour clock without
     *                     leading noughts (1 to 12) <br>
     *  <code>%I  </code>  hour as decimal number on 12-hour clock (01 to 12) <br>
     *  <code>%j  </code>  day of year (range 001 to 366) <br>
     *  <code>%m  </code>  month as decimal number (range 01 to 12) <br>
     *  <code>%M  </code>  minute as a decimal number (00 to 59) <br>
     *  <code>%n  </code>  newline character ("\n") <br>
     *  <code>%o  </code>  raw timezone offset expressed as '+/-HH:MM' <br>
     *  <code>%O  </code>  dst-corrected timezone offset expressed as '+/-HH:MM' <br>
     *  <code>%p  </code>  either 'am' or 'pm' depending on the time <br>
     *  <code>%P  </code>  either 'AM' or 'PM' depending on the time <br>
     *  <code>%r  </code>  time in am/pm notation; equivalent to "%I:%M:%S %p" <br>
     *  <code>%R  </code>  time in 24-hour notation; equivalent to "%H:%M" <br>
     *  <code>%s  </code>  seconds including the micro-time (the decimal
     *                     representation less than one second to six decimal
     *                     places<br>
     *  <code>%S  </code>  seconds as a decimal number (00 to 59) <br>
     *  <code>%t  </code>  tab character ("\t") <br>
     *  <code>%T  </code>  current time; equivalent to "%H:%M:%S" <br>
     *  <code>%u  </code>  day of week as decimal (1 to 7; where 1 = Monday) <br>
     *  <code>%U  </code>  week number of the current year as a decimal
     *                     number, starting with the first Sunday as the first
     *                     day of the first week (i.e. the first full week of
     *                     the year, and the week that contains 7th January)
     *                     (00 to 53) <br>
     *  <code>%V  </code>  the ISO 8601:1988 week number of the current year
     *                     as a decimal number, range 01 to 53, where week 1
     *                     is the first week that has at least 4 days in the
     *                     current year, and with Monday as the first day of
     *                     the week.  (Use %G or %g for the year component
     *                     that corresponds to the week number for the
     *                     specified timestamp.)
     *  <code>%w  </code>  day of week as decimal (0 to 6; where 0 = Sunday) <br>
     *  <code>%W  </code>  week number of the current year as a decimal
     *                     number, starting with the first Monday as the first
     *                     day of the first week (i.e. the first full week of
     *                     the year, and the week that contains 7th January)
     *                     (00 to 53) <br>
     *  <code>%y  </code>  year as decimal (range 00 to 99) <br>
     *  <code>%Y  </code>  year as decimal including century (range 0000 to
     *                     9999) <br>
     *  <code>%Z  </code>  Abbreviated form of time zone name, e.g. 'GMT', or
     *                     the abbreviation for Summer time if the date falls
     *                     in Summer time, e.g. 'BST'. <br>
     *  <code>%%  </code>  literal '%' <br>
     * <br>
     *
     * The following codes render a different output to that of 'strftime()':
     *
     *  <code>%e</code> in 'strftime()' a single digit is preceded by a space
     *  <code>%h</code> in 'strftime()' is equivalent to '%b'
     *  <code>%U</code> '%U' and '%W' are different in 'strftime()' in that
     *                  if week 1 does not start on 1st January, '00' is
     *                  returned, whereas this function returns '53', that is,
     *                  the week is counted as the last of the previous year.
     *  <code>%W</code>
     *
     * @param string $format the format string for returned date/time
     *
     * @return   string     date/time in given format
     * @access   public
     */
    function format($format)
    {
        $output = "";

        $hn_isoyear = null;
        $hn_isoweek = null;
        $hn_isoday  = null;

        for ($strpos = 0; $strpos < strlen($format); $strpos++) {
            $char = substr($format, $strpos, 1);
            if ($char == "%") {
                $nextchar = substr($format, $strpos + 1, 1);
                switch ($nextchar) {
                case "a":
                    $output .= Date_Calc::getWeekdayAbbrname($this->day,
                                   $this->month, $this->year,
                                   $this->getWeekdayAbbrnameLength);
                    break;
                case "A":
                    $output .= Date_Calc::getWeekdayFullname($this->day,
                                   $this->month, $this->year);
                    break;
                case "b":
                    $output .= Date_Calc::getMonthAbbrname($this->month);
                    break;
                case "B":
                    $output .= Date_Calc::getMonthFullname($this->month);
                    break;
                case "C":
                    $output .= sprintf("%02d", intval($this->year / 100));
                    break;
                case "d":
                    $output .= sprintf("%02d", $this->day);
                    break;
                case "D":
                    $output .= sprintf("%02d/%02d/%02d", $this->month,
                                   $this->day, $this->year);
                    break;
                case "e":
                    $output .= $this->day;
                    break;
                case "E":
                    $output .= Date_Calc::dateToDays($this->day, $this->month,
                                   $this->year);
                    break;
                case "g":
                    if (is_null($hn_isoyear))
                        list($hn_isoyear, $hn_isoweek, $hn_isoday) =
                            Date_Calc::isoWeekDate($this->day,
                                                   $this->month,
                                                   $this->year);

                    $output .= sprintf("%02d", $hn_isoyear % 100);
                    break;
                case "G":
                    if (is_null($hn_isoyear))
                        list($hn_isoyear, $hn_isoweek, $hn_isoday) =
                            Date_Calc::isoWeekDate($this->day,
                                                   $this->month,
                                                   $this->year);

                    $output .= sprintf("%04d", $hn_isoyear);
                    break;
                case 'h':
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= sprintf("%d", $this->hour);
                    break;
                case "H":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= sprintf("%02d", $this->hour);
                    break;
                case "i":
                case "I":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $hour    = $this->hour + 1 > 12 ?
                               $this->hour - 12 :
                               $this->hour;
                    $output .= $hour == 0 ?
                               12 :
                               ($nextchar == "i" ?
                                $hour :
                                sprintf('%02d', $hour));
                    break;
                case "j":
                    $output .= sprintf("%03d",
                                       Date_Calc::dayOfYear($this->day,
                                                            $this->month,
                                                            $this->year));
                    break;
                case "m":
                    $output .= sprintf("%02d", $this->month);
                    break;
                case "M":
                    $output .= sprintf("%02d", $this->minute);
                    break;
                case "n":
                    $output .= "\n";
                    break;
                case "O":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $offms     = $this->getTZOffset();
                    $direction = $offms >= 0 ? "+" : "-";
                    $offmins   = abs($offms) / 1000 / 60;
                    $hours     = $offmins / 60;
                    $minutes   = $offmins % 60;

                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
                    break;
                case "o":
                    $offms     = $this->tz->getRawOffset($this);
                    $direction = $offms >= 0 ? "+" : "-";
                    $offmins   = abs($offms) / 1000 / 60;
                    $hours     = $offmins / 60;
                    $minutes   = $offmins % 60;

                    $output .= sprintf("%s%02d:%02d", $direction, $hours, $minutes);
                    break;
                case "p":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= $this->hour >= 12 ? "pm" : "am";
                    break;
                case "P":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= $this->hour >= 12 ? "PM" : "AM";
                    break;
                case "r":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $hour    = $this->hour + 1 > 12 ?
                               $this->hour - 12 :
                               $this->hour;
                    $output .= sprintf("%02d:%02d:%02d %s",
                                       $hour == 0 ?  12 : $hour,
                                       $this->minute,
                                       $this->second,
                                       $this->hour >= 12 ? "PM" : "AM");
                    break;
                case "R":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= sprintf("%02d:%02d", $this->hour, $this->minute);
                    break;
                case "s":
                    $output .= str_replace(',',
                                           '.',
                                           sprintf("%09f",
                                                   (float)((float) $this->second +
                                                           $this->partsecond)));
                    break;
                case "S":
                    $output .= sprintf("%02d", $this->second);
                    break;
                case "t":
                    $output .= "\t";
                    break;
                case "T":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= sprintf("%02d:%02d:%02d",
                                       $this->hour,
                                       $this->minute,
                                       $this->second);
                    break;
                case "u":
                    $hn_dayofweek = $this->getDayOfWeek();
                    $output      .= $hn_dayofweek == 0 ? 7 : $hn_dayofweek;
                    break;
                case "U":
                    $ha_week = Date_Calc::weekOfYear7th($this->day,
                                                        $this->month,
                                                        $this->year,
                                                        0);
                    $output .= sprintf("%02d", $ha_week[1]);
                    break;
                case "V":
                    if (is_null($hn_isoyear))
                        list($hn_isoyear, $hn_isoweek, $hn_isoday) =
                            Date_Calc::isoWeekDate($this->day,
                                                   $this->month,
                                                   $this->year);

                    $output .= $hn_isoweek;
                    break;
                case "w":
                    $output .= $this->getDayOfWeek();
                    break;
                case "W":
                    $ha_week = Date_Calc::weekOfYear7th($this->day,
                                                        $this->month,
                                                        $this->year,
                                                        1);
                    $output .= sprintf("%02d", $ha_week[1]);
                    break;
                case 'y':
                    $output .= sprintf('%0' .
                                       ($this->year < 0 ? '3' : '2') .
                                       'd',
                                       $this->year % 100);
                    break;
                case "Y":
                    $output .= sprintf('%0' .
                                       ($this->year < 0 ? '5' : '4') .
                                       'd',
                                       $this->year);
                    break;
                case "Z":
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $output .= $this->getTZShortName();
                    break;
                case "%":
                    $output .= "%";
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
    // {{{ _getOrdinalSuffix()

    /**
     * Returns appropriate ordinal suffix (i.e. 'th', 'st', 'nd' or 'rd')
     *
     * @param int  $pn_num       number with which to determine suffix
     * @param bool $pb_uppercase boolean specifying if the suffix should be
     *                            capitalized
     *
     * @return   string
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _getOrdinalSuffix($pn_num, $pb_uppercase = true)
    {
        switch (($pn_numabs = abs($pn_num)) % 100) {
        case 11:
        case 12:
        case 13:
            $hs_suffix = "th";
            break;
        default:
            switch ($pn_numabs % 10) {
            case 1:
                $hs_suffix = "st";
                break;
            case 2:
                $hs_suffix = "nd";
                break;
            case 3:
                $hs_suffix = "rd";
                break;
            default:
                $hs_suffix = "th";
            }
        }

        return $pb_uppercase ? strtoupper($hs_suffix) : $hs_suffix;
    }


    // }}}
    // {{{ _spellNumber()

    /**
     * Converts a number to its word representation
     *
     * Private helper function, particularly for 'format2()'.  N.B. The
     * second argument is the 'SP' code which can be specified in the
     * format string for 'format2()' and is interpreted as follows:
     *  'SP' - returns upper-case spelling, e.g. 'FOUR HUNDRED'
     *  'Sp' - returns spelling with first character of each word
     *         capitalized, e.g. 'Four Hundred'
     *  'sp' - returns lower-case spelling, e.g. 'four hundred'
     *
     * @param int    $pn_num            number to be converted to words
     * @param bool   $pb_ordinal        boolean specifying if the number should
     *                                   be ordinal
     * @param string $ps_capitalization string for specifying capitalization
     *                                   options
     * @param string $ps_locale         language name abbreviation used for
     *                                   formatting numbers as spelled-out words
     *
     * @return   string
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _spellNumber($pn_num,
                          $pb_ordinal = false,
                          $ps_capitalization = "SP",
                          $ps_locale = "en_GB")
    {
        include_once "Numbers/Words.php";
        $hs_words = Numbers_Words::toWords($pn_num, $ps_locale);
        if (Pear::isError($hs_words)) {
            return $hs_words;
        }

        if ($pb_ordinal && substr($ps_locale, 0, 2) == "en") {
            if (($pn_rem = ($pn_numabs = abs($pn_num)) % 100) == 12) {
                $hs_words = substr($hs_words, 0, -2) . "fth";
            } else if ($pn_rem >= 11 && $pn_rem <= 15) {
                $hs_words .= "th";
            } else {
                switch ($pn_numabs % 10) {
                case 1:
                    $hs_words = substr($hs_words, 0, -3) . "first";
                    break;
                case 2:
                    $hs_words = substr($hs_words, 0, -3) . "second";
                    break;
                case 3:
                    $hs_words = substr($hs_words, 0, -3) . "ird";
                    break;
                case 5:
                    $hs_words = substr($hs_words, 0, -2) . "fth";
                    break;
                default:
                    switch (substr($hs_words, -1)) {
                    case "e":
                        $hs_words = substr($hs_words, 0, -1) . "th";
                        break;
                    case "t":
                        $hs_words .= "h";
                        break;
                    case "y":
                        $hs_words = substr($hs_words, 0, -1) . "ieth";
                        break;
                    default:
                        $hs_words .= "th";
                    }
                }
            }
        }

        if (($hs_char = substr($ps_capitalization, 0, 1)) ==
            strtolower($hs_char)) {
            $hb_upper = false;
            $hs_words = strtolower($hs_words);
        } else if (($hs_char = substr($ps_capitalization, 1, 1)) ==
                   strtolower($hs_char)) {
            $hb_upper = false;
            $hs_words = ucwords($hs_words);
        } else {
            $hb_upper = true;
            $hs_words = strtoupper($hs_words);
        }

        return $hs_words;
    }


    // }}}
    // {{{ _formatNumber()

    /**
     * Formats a number according to the specified format string
     *
     * Private helper function, for 'format2()', which interprets the
     * codes 'SP' and 'TH' and the combination of the two as follows:
     *
     *  <code>TH</code> Ordinal number
     *  <code>SP</code> Spelled cardinal number
     *  <code>SPTH</code> Spelled ordinal number (combination of 'SP' and 'TH'
     *                   in any order)
     *  <code>THSP</code>
     *
     * Code 'SP' can have the following three variations (which can also be used
     * in combination with 'TH'):
     *
     *  <code>SP</code> returns upper-case spelling, e.g. 'FOUR HUNDRED'
     *  <code>Sp</code> returns spelling with first character of each word
     *                 capitalized, e.g. 'Four Hundred'
     *  <code>sp</code> returns lower-case spelling, e.g. 'four hundred'
     *
     * Code 'TH' can have the following two variations (although in combination
     * with code 'SP', the case specification of 'SP' takes precedence):
     *
     *  <code>TH</code> returns upper-case ordinal suffix, e.g. 400TH
     *  <code>th</code> returns lower-case ordinal suffix, e.g. 400th
     *
     * N.B. The format string is passed by reference, in order to pass back
     * the part of the format string that matches the valid codes 'SP' and
     * 'TH'.  If none of these are found, then it is set to an empty string;
     * If both codes are found then a string is returned with code 'SP'
     * preceding code 'TH' (i.e. 'SPTH', 'Spth' or 'spth').
     *
     * @param int    $pn_num         integer to be converted to words
     * @param string &$ps_format     string of formatting codes (max. length 4)
     * @param int    $pn_numofdigits no of digits to display if displayed as
     *                                numeral (i.e. not spelled out), not
     *                                including the sign (if negative); to
     *                                allow all digits specify 0
     * @param bool   $pb_nopad       boolean specifying whether to suppress
     *                                padding with leading noughts (if displayed
     *                                as numeral)
     * @param bool   $pb_nosign      boolean specifying whether to suppress the
     *                                display of the sign (if negative)
     * @param string $ps_locale      language name abbreviation used for
     *                                formatting
     * @param string $ps_thousandsep optional thousand-separator (e.g. a comma)
     *                                numbers as spelled-out words
     * @param int    $pn_padtype     optional integer to specify padding (if
     *                                displayed as numeral) - can be
     *                                STR_PAD_LEFT or STR_PAD_RIGHT
     *
     * @return   string
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _formatNumber($pn_num,
                           &$ps_format,
                           $pn_numofdigits,
                           $pb_nopad = false,
                           $pb_nosign = false,
                           $ps_locale = "en_GB",
                           $ps_thousandsep = null,
                           $pn_padtype = STR_PAD_LEFT)
    {
        $hs_code1 = substr($ps_format, 0, 2);
        $hs_code2 = substr($ps_format, 2, 2);

        $hs_sp = null;
        $hs_th = null;
        if (strtoupper($hs_code1) == "SP") {
            $hs_sp = $hs_code1;
            if (strtoupper($hs_code2) == "TH") {
                $hs_th = $hs_code2;
            }
        } else if (strtoupper($hs_code1) == "TH") {
            $hs_th = $hs_code1;
            if (strtoupper($hs_code2) == "SP") {
                $hs_sp = $hs_code2;
            }
        }

        $hn_absnum = abs($pn_num);
        if ($pn_numofdigits > 0 && strlen($hn_absnum) > $pn_numofdigits) {
            $hn_absnum = intval(substr($hn_absnum, -$pn_numofdigits));
        }
        $hs_num = $hn_absnum;

        if (!is_null($hs_sp)) {
            // Spell out number:
            //
            $ps_format = $hs_sp .
                         (is_null($hs_th) ? "" : ($hs_sp == "SP" ? "TH" : "th"));
            return $this->_spellNumber(!$pb_nosign && $pn_num < 0 ?
                                           $hn_absnum * -1 :
                                           $hn_absnum,
                                       !is_null($hs_th),
                                       $hs_sp,
                                       $ps_locale);
        } else {
            // Display number as Arabic numeral:
            //
            if (!$pb_nopad) {
                $hs_num = str_pad($hs_num, $pn_numofdigits, "0", $pn_padtype);
            }

            if (!is_null($ps_thousandsep)) {
                for ($i = strlen($hs_num) - 3; $i > 0; $i -= 3) {
                    $hs_num = substr($hs_num, 0, $i) .
                              $ps_thousandsep .
                              substr($hs_num, $i);
                }
            }

            if (!$pb_nosign) {
                if ($pn_num < 0)
                    $hs_num = "-" . $hs_num;
                else if (!$pb_nopad)
                    $hs_num = " " . $hs_num;
            }

            if (!is_null($hs_th)) {
                $ps_format = $hs_th;
                return $hs_num .
                       $this->_getOrdinalSuffix($pn_num,
                                                substr($hs_th, 0, 1) == "T");
            } else {
                $ps_format = "";
                return $hs_num;
            }
        }
    }


    // }}}
    // {{{ format2()

    /**
     * Extended version of 'format()' with variable-length formatting codes
     *
     * Most codes reproduce the no of digits equal to the length of the code,
     * for example, 'YYY' will return the last 3 digits of the year, and so
     * the year 2007 will produce '007', and the year 89 will produce '089',
     * unless the no-padding code is used as in 'NPYYY', which will return
     * '89'.
     *
     * For negative values, the sign will be discarded, unless the 'S' code
     * is used in combination, but note that for positive values the value
     * will be padded with a leading space unless it is suppressed with
     * the no-padding modifier, for example for 2007:
     *
     *  <code>YYYY</code> returns '2007'
     *  <code>SYYYY</code> returns ' 2007'
     *  <code>NPSYYYY</code> returns '2007'
     *
     * The no-padding modifier 'NP' can be used with numeric codes to
     * suppress leading (or trailing in the case of code 'F') noughts, and
     * with character-returning codes such as 'DAY' to suppress trailing
     * spaces, which will otherwise be padded to the maximum possible length
     * of the return-value of the code; for example, for Monday:
     *
     *  <code>Day</code> returns 'Monday   ' because the maximum length of
     *                  this code is 'Wednesday';
     *  <code>NPDay</code> returns 'Monday'
     *
     * N.B. this code affects the code immediately following only, and
     * without this code the default is always to apply padding.
     *
     * Most character-returning codes, such as 'MONTH', will
     * set the capitalization according to the code, so for example:
     *
     *  <code>MONTH</code> returns upper-case spelling, e.g. 'JANUARY'
     *  <code>Month</code> returns spelling with first character of each word
     *                    capitalized, e.g. 'January'
     *  <code>month</code> returns lower-case spelling, e.g. 'january'
     *
     * Where it makes sense, numeric codes can be combined with a following
     * 'SP' code which spells out the number, or with a 'TH' code, which
     * renders the code as an ordinal ('TH' only works in English), for
     * example, for 31st December:
     *
     *  <code>DD</code> returns '31'
     *  <code>DDTH</code> returns '31ST'
     *  <code>DDth</code> returns '31st'
     *  <code>DDSP</code> returns 'THIRTY-ONE'
     *  <code>DDSp</code> returns 'Thirty-one'
     *  <code>DDsp</code> returns 'thirty-one'
     *  <code>DDSPTH</code> returns 'THIRTY-FIRST'
     *  <code>DDSpth</code> returns 'Thirty-first'
     *  <code>DDspth</code> returns 'thirty-first'
     *
     *
     * All formatting options:
     *
     *  <code>-</code> All punctuation and white-space is reproduced unchanged
     *  <code>/</code>
     *  <code>,</code>
     *  <code>.</code>
     *  <code>;</code>
     *  <code>:</code>
     *  <code> </code>
     *  <code>"text"</code> Quoted text is reproduced unchanged (escape using
     *                     '\')
     *  <code>AD</code> AD indicator with or without full stops; N.B. if you
     *                 are using 'Astronomical' year numbering then 'A.D./B.C.'
     *                 indicators will be out for negative years
     *  <code>A.D.</code>
     *  <code>AM</code> Meridian indicator with or without full stops
     *  <code>A.M.</code>
     *  <code>BC</code> BC indicator with or without full stops
     *  <code>B.C.</code>
     *  <code>BCE</code> BCE indicator with or without full stops
     *  <code>B.C.E.</code>
     *  <code>CC</code> Century, i.e. the year divided by 100, discarding the
     *                 remainder; 'S' prefixes negative years with a minus sign
     *  <code>SCC</code>
     *  <code>CE</code> CE indicator with or without full stops
     *  <code>C.E.</code>
     *  <code>D</code> Day of week (0-6), where 0 represents Sunday
     *  <code>DAY</code> Name of day, padded with blanks to display width of the
     *                  widest name of day in the locale of the machine
     *  <code>DD</code> Day of month (1-31)
     *  <code>DDD</code> Day of year (1-366)
     *  <code>DY</code> Abbreviated name of day
     *  <code>FFF</code> Fractional seconds; no radix character is printed.  The
     *                  no of 'F's determines the no of digits of the
     *                  part-second to return; e.g. 'HH:MI:SS.FF'
     *  <code>F[integer]</code> The integer after 'F' specifies the number of
     *                         digits of the part-second to return.  This is an
     *                         alternative to using F[integer], and 'F3' is thus
     *                         equivalent to using 'FFF'.
     *  <code>HH</code> Hour of day (0-23)
     *  <code>HH12</code> Hour of day (1-12)
     *  <code>HH24</code> Hour of day (0-23)
     *  <code>ID</code> Day of week (1-7) based on the ISO standard
     *  <code>IW</code> Week of year (1-52 or 1-53) based on the ISO standard
     *  <code>IYYY</code> 4-digit year based on the ISO 8601 standard; 'S'
     *                   prefixes negative years with a minus sign
     *  <code>SIYYY</code>
     *  <code>IYY</code> Last 3, 2, or 1 digit(s) of ISO year
     *  <code>IY</code>
     *  <code>I</code>
     *  <code>J</code> Julian day - the number of days since Monday, 24th
     *                November, 4714 B.C. (proleptic Gregorian calendar)
     *  <code>MI</code> Minute (0-59)
     *  <code>MM</code> Month (01-12; January = 01)
     *  <code>MON</code> Abbreviated name of month
     *  <code>MONTH</code> Name of month, padded with blanks to display width of
     *                    the widest name of month in the date language used for
     *  <code>PM</code> Meridian indicator with or without full stops
     *  <code>P.M.</code>
     *  <code>Q</code> Quarter of year (1, 2, 3, 4; January - March = 1)
     *  <code>RM</code> Roman numeral month (I-XII; January = I); N.B. padded
     *                 with leading spaces.
     *  <code>SS</code> Second (0-59)
     *  <code>SSSSS</code> Seconds past midnight (0-86399)
     *  <code>TZC</code> Abbreviated form of time zone name, e.g. 'GMT', or the
     *                  abbreviation for Summer time if the date falls in Summer
     *                  time, e.g. 'BST'.
     *                  N.B. this is not a unique identifier - for this purpose
     *                  use the time zone region (code 'TZR').
     *  <code>TZH</code> Time zone hour; 'S' prefixes the hour with the correct
     *                  sign, (+/-), which otherwise is not displayed.  Note
     *                  that the leading nought can be suppressed with the
     *                  no-padding code 'NP').  Also note that if you combine
     *                  with the 'SP' code, the sign will not be spelled out.
     *                  (I.e. 'STZHSp' will produce '+One', for example, and
     *                  not 'Plus One'.
     *                  'TZH:TZM' will produce, for example, '+05:30'.  (Also
     *                  see 'TZM' format code)
     *  <code>STZH</code>
     *  <code>TZI</code> Whether or not the date is in Summer time (daylight
     *                  saving time).  Returns '1' if Summer time, else '0'.
     *  <code>TZM</code> Time zone minute, without any +/- sign.  (Also see
     *                  'TZH' format element)
     *  <code>TZN</code> Long form of time zone name, e.g.
     *                  'Greenwich Mean Time', or the name of the Summer time if
     *                  the date falls in Summer time, e.g.
     *                  'British Summer Time'.  N.B. this is not a unique
     *                  identifier - for this purpose use the time zone region
     *                  (code 'TZR').
     *  <code>TZO</code> Time zone offset in ISO 8601 form - that is, 'Z' if
     *                  UTC, else [+/-][hh]:[mm] (which would be equivalent
     *                  to 'STZH:TZM').  Note that this result is right padded
     *                  with spaces by default, (i.e. if 'Z').
     *  <code>TZS</code> Time zone offset in seconds; 'S' prefixes negative
     *                  sign with minus sign '-' if negative, and no sign if
     *                  positive (i.e. -43200 to 50400).
     *  <code>STZS</code>
     *  <code>TZR</code> Time zone region, that is, the name or ID of the time
     *                  zone e.g. 'Europe/London'.  This value is unique for
     *                  each time zone.
     *  <code>U</code> Seconds since the Unix Epoch -
     *                January 1 1970 00:00:00 GMT
     *  <code>W</code> 'Absolute' week of month (1-5), counting week 1 as
     *                1st-7th of the year, regardless of the day
     *  <code>W1</code> Week of year (1-54), counting week 1 as the week that
     *                 contains 1st January
     *  <code>W4</code> Week of year (1-53), counting week 1 as the week that
     *                 contains 4th January (i.e. first week with at least 4
     *                 days)
     *  <code>W7</code> Week of year (1-53), counting week 1 as the week that
     *                 contains 7th January (i.e. first full week)
     *  <code>WW</code> 'Absolute' week of year (1-53), counting week 1 as
     *                 1st-7th of the year, regardless of the day
     *  <code>YEAR</code> Year, spelled out; 'S' prefixes negative years with
     *                  'MINUS'; N.B. 'YEAR' differs from 'YYYYSP' in that the
     *                   first will render 1923, for example, as 'NINETEEN
     *                   TWENTY-THREE, and the second as 'ONE THOUSAND NINE
     *                   HUNDRED TWENTY-THREE'
     *  <code>SYEAR</code>
     *  <code>YYYY</code> 4-digit year; 'S' prefixes negative years with a minus
     *                   sign
     *  <code>SYYYY</code>
     *  <code>YYY</code> Last 3, 2, or 1 digit(s) of year
     *  <code>YY</code>
     *  <code>Y</code>
     *  <code>Y,YYY</code> Year with thousands-separator in this position; five
     *                    possible separators
     *  <code>Y.YYY</code>
     *  <code>Y�YYY</code> N.B. space-dot (mid-dot, interpunct) is valid only in
     *                    ISO 8859-1 (so take care when using UTF-8 in
     *                    particular)
     *  <code>Y'YYY</code>
     *  <code>Y YYY</code>
     *
     * In addition the following codes can be used in combination with other
     * codes;
     *  Codes that modify the next code in the format string:
     *
     *  <code>NP</code> 'No Padding' - Returns a value with no trailing blanks
     *                 and no leading or trailing noughts; N.B. that the
     *                 default is to include this padding in the return string.
     *                 N.B. affects the code immediately following only.
     *
     *  Codes that modify the previous code in the format string (can only
     *  be used with integral codes such as 'MM'):
     *
     *  <code>TH</code> Ordinal number
     *  <code>SP</code> Spelled cardinal number
     *  <code>SPTH</code> Spelled ordinal number (combination of 'SP' and 'TH'
     *                   in any order)
     *  <code>THSP</code>
     *
     * Code 'SP' can have the following three variations (which can also be used
     * in combination with 'TH'):
     *
     *  <code>SP</code> returns upper-case spelling, e.g. 'FOUR HUNDRED'
     *  <code>Sp</code> returns spelling with first character of each word
     *                 capitalized, e.g. 'Four Hundred'
     *  <code>sp</code> returns lower-case spelling, e.g. 'four hundred'
     *
     * Code 'TH' can have the following two variations (although in combination
     * with code 'SP', the case specification of 'SP' takes precedence):
     *
     *  <code>TH</code> returns upper-case ordinal suffix, e.g. 400TH
     *  <code>th</code> returns lower-case ordinal suffix, e.g. 400th
     *
     * @param string $ps_format format string for returned date/time
     * @param string $ps_locale language name abbreviation used for formatting
     *                           numbers as spelled-out words
     *
     * @return   string     date/time in given format
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function format2($ps_format, $ps_locale = "en_GB")
    {
        if (!preg_match('/^("([^"\\\\]|\\\\\\\\|\\\\")*"|(D{1,3}|S?C+|' .
                        'HH(12|24)?|I[DW]|S?IY*|J|M[IM]|Q|SS(SSS)?|S?TZ[HS]|' .
                        'TZM|U|W[W147]?|S?Y{1,3}([,.�\' ]?YYY)*)(SP(TH)?|' .
                        'TH(SP)?)?|AD|A\.D\.|AM|A\.M\.|BCE?|B\.C\.(E\.)?|CE|' .
                        'C\.E\.|DAY|DY|F(F*|[1-9][0-9]*)|MON(TH)?|NP|PM|' .
                        'P\.M\.|RM|TZ[CINOR]|S?YEAR|[^A-Z0-9"])*$/i',
                        $ps_format)) {
            return PEAR::raiseError("Invalid date format '$ps_format'",
                                    DATE_ERROR_INVALIDFORMATSTRING);
        }

        $ret = "";
        $i   = 0;

        $hb_nopadflag    = false;
        $hb_showsignflag = false;

        $hn_weekdaypad = null;
        $hn_monthpad   = null;
        $hn_isoyear    = null;
        $hn_isoweek    = null;
        $hn_isoday     = null;
        $hn_tzoffset   = null;

        while ($i < strlen($ps_format)) {
            $hb_lower = false;

            if ($hb_nopadflag) {
                $hb_nopad = true;
            } else {
                $hb_nopad = false;
            }
            if ($hb_showsignflag) {
                $hb_nosign = false;
            } else {
                $hb_nosign = true;
            }
            $hb_nopadflag    = false;
            $hb_showsignflag = false;

            switch ($hs_char = substr($ps_format, $i, 1)) {
            case "-":
            case "/":
            case ",":
            case ".":
            case ";":
            case ":":
            case " ":
                $ret .= $hs_char;
                $i   += 1;
                break;
            case "\"":
                preg_match('/(([^"\\\\]|\\\\\\\\|\\\\")*)"/',
                           $ps_format,
                           $ha_matches,
                           PREG_OFFSET_CAPTURE,
                           $i + 1);
                $ret .= str_replace(array('\\\\', '\\"'),
                                    array('\\', '"'),
                                    $ha_matches[1][0]);
                $i   += strlen($ha_matches[0][0]) + 1;
                break;
            case "a":
                $hb_lower = true;
            case "A":
                if (strtoupper(substr($ps_format, $i, 4)) == "A.D.") {
                    $ret .= $this->year >= 0 ?
                            ($hb_lower ? "a.d." : "A.D.") :
                            ($hb_lower ? "b.c." : "B.C.");
                    $i   += 4;
                } else if (strtoupper(substr($ps_format, $i, 2)) == "AD") {
                    $ret .= $this->year >= 0 ?
                            ($hb_lower ? "ad" : "AD") :
                            ($hb_lower ? "bc" : "BC");
                    $i   += 2;
                } else {
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    if (strtoupper(substr($ps_format, $i, 4)) == "A.M.") {
                        $ret .= $this->hour < 12 ?
                                ($hb_lower ? "a.m." : "A.M.") :
                                ($hb_lower ? "p.m." : "P.M.");
                        $i   += 4;
                    } else if (strtoupper(substr($ps_format, $i, 2)) == "AM") {
                        $ret .= $this->hour < 12 ?
                                ($hb_lower ? "am" : "AM") :
                                ($hb_lower ? "pm" : "PM");
                        $i   += 2;
                    }
                }

                break;
            case "b":
                $hb_lower = true;
            case "B":
                // Check for 'B.C.E.' first:
                //
                if (strtoupper(substr($ps_format, $i, 6)) == "B.C.E.") {
                    if ($this->year >= 0) {
                        $hs_era = $hb_lower ? "c.e." : "C.E.";
                        $ret   .= $hb_nopad ?
                                  $hs_era :
                                  str_pad($hs_era, 6, " ", STR_PAD_RIGHT);
                    } else {
                        $ret .= $hb_lower ? "b.c.e." : "B.C.E.";
                    }
                    $i += 6;
                } else if (strtoupper(substr($ps_format, $i, 3)) == "BCE") {
                    if ($this->year >= 0) {
                        $hs_era = $hb_lower ? "ce" : "CE";
                        $ret   .= $hb_nopad ?
                                  $hs_era :
                                  str_pad($hs_era, 3, " ", STR_PAD_RIGHT);
                    } else {
                        $ret .= $hb_lower ? "bce" : "BCE";
                    }
                    $i += 3;
                } else if (strtoupper(substr($ps_format, $i, 4)) == "B.C.") {
                    $ret .= $this->year >= 0 ?
                            ($hb_lower ? "a.d." : "A.D.") :
                            ($hb_lower ? "b.c." : "B.C.");
                    $i   += 4;
                } else if (strtoupper(substr($ps_format, $i, 2)) == "BC") {
                    $ret .= $this->year >= 0 ?
                            ($hb_lower ? "ad" : "AD") :
                            ($hb_lower ? "bc" : "BC");
                    $i   += 2;
                }

                break;
            case "c":
                $hb_lower = true;
            case "C":
                if (strtoupper(substr($ps_format, $i, 4)) == "C.E.") {
                    if ($this->year >= 0) {
                        $hs_era = $hb_lower ? "c.e." : "C.E.";
                        $ret   .= $hb_nopad ?
                                  $hs_era :
                                  str_pad($hs_era, 6, " ", STR_PAD_RIGHT);
                    } else {
                        $ret .= $hb_lower ? "b.c.e." : "B.C.E.";
                    }
                    $i += 4;
                } else if (strtoupper(substr($ps_format, $i, 2)) == "CE") {
                    if ($this->year >= 0) {
                        $hs_era = $hb_lower ? "ce" : "CE";
                        $ret   .= $hb_nopad ?
                                  $hs_era :
                                  str_pad($hs_era, 3, " ", STR_PAD_RIGHT);
                    } else {
                        $ret .= $hb_lower ? "bce" : "BCE";
                    }
                    $i += 2;
                } else {
                    // Code C(CCC...):
                    //
                    $hn_codelen = 1;
                    while (strtoupper(substr($ps_format,
                                             $i + $hn_codelen,
                                             1)) == "C")
                        ++$hn_codelen;

                    // Check next code is not 'CE' or 'C.E.'
                    //
                    if ($hn_codelen > 1 &&
                        (strtoupper(substr($ps_format,
                                           $i + $hn_codelen - 1,
                                           4)) == "C.E." ||
                         strtoupper(substr($ps_format,
                                           $i + $hn_codelen - 1,
                                           2)) == "CE"
                         ))
                        --$hn_codelen;

                    $hn_century      = intval($this->year / 100);
                    $hs_numberformat = substr($ps_format, $i + $hn_codelen, 4);
                    $hs_century      = $this->_formatNumber($hn_century,
                                                            $hs_numberformat,
                                                            $hn_codelen,
                                                            $hb_nopad,
                                                            $hb_nosign,
                                                            $ps_locale);
                    if (Pear::isError($hs_century))
                        return $hs_century;

                    $ret .= $hs_century;
                    $i   += $hn_codelen + strlen($hs_numberformat);
                }

                break;
            case "d":
                $hb_lower = true;
            case "D":
                if (strtoupper(substr($ps_format, $i, 3)) == "DAY") {
                    $hs_day = Date_Calc::getWeekdayFullname($this->day,
                                                            $this->month,
                                                            $this->year);

                    if (!$hb_nopad) {
                        if (is_null($hn_weekdaypad)) {
                            // Set week-day padding variable:
                            //
                            $hn_weekdaypad = 0;
                            foreach (Date_Calc::getWeekDays() as $hs_weekday)
                                $hn_weekdaypad = max($hn_weekdaypad,
                                                     strlen($hs_weekday));
                        }
                        $hs_day = str_pad($hs_day,
                                          $hn_weekdaypad,
                                          " ",
                                          STR_PAD_RIGHT);
                    }

                    $ret .= $hb_lower ?
                            strtolower($hs_day) :
                            (substr($ps_format, $i + 1, 1) == "A" ?
                             strtoupper($hs_day) :
                             $hs_day);
                    $i   += 3;
                } else if (strtoupper(substr($ps_format, $i, 2)) == "DY") {
                    $hs_day = Date_Calc::getWeekdayAbbrname($this->day,
                                                            $this->month,
                                                            $this->year);
                    $ret   .= $hb_lower ?
                              strtolower($hs_day) :
                              (substr($ps_format, $i + 1, 1) == "Y" ?
                               strtoupper($hs_day) :
                               $hs_day);
                    $i     += 2;
                } else if (strtoupper(substr($ps_format, $i, 3)) == "DDD" &&
                           strtoupper(substr($ps_format, $i + 2, 3)) != "DAY" &&
                           strtoupper(substr($ps_format, $i + 2, 2)) != "DY"
                           ) {
                    $hn_day = Date_Calc::dayOfYear($this->day,
                                                   $this->month,
                                                   $this->year);
                    $hs_numberformat = substr($ps_format, $i + 3, 4);
                    $hs_day = $this->_formatNumber($hn_day,
                                                   $hs_numberformat,
                                                   3,
                                                   $hb_nopad,
                                                   true,
                                                   $ps_locale);
                    if (Pear::isError($hs_day))
                        return $hs_day;

                    $ret .= $hs_day;
                    $i   += 3 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "DD" &&
                           strtoupper(substr($ps_format, $i + 1, 3)) != "DAY" &&
                           strtoupper(substr($ps_format, $i + 1, 2)) != "DY"
                           ) {
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_day = $this->_formatNumber($this->day,
                                                   $hs_numberformat,
                                                   2,
                                                   $hb_nopad,
                                                   true,
                                                   $ps_locale);
                    if (Pear::isError($hs_day))
                        return $hs_day;

                    $ret .= $hs_day;
                    $i   += 2 + strlen($hs_numberformat);
                } else {
                    // Code 'D':
                    //
                    $hn_day = Date_Calc::dayOfWeek($this->day,
                                                   $this->month,
                                                   $this->year);
                    $hs_numberformat = substr($ps_format, $i + 1, 4);
                    $hs_day = $this->_formatNumber($hn_day,
                                                   $hs_numberformat,
                                                   1,
                                                   $hb_nopad,
                                                   true,
                                                   $ps_locale);
                    if (Pear::isError($hs_day))
                        return $hs_day;

                    $ret .= $hs_day;
                    $i   += 1 + strlen($hs_numberformat);
                }

                break;
            case "f":
            case "F":
                if ($this->ob_invalidtime)
                    return $this->_getErrorInvalidTime();
                $hn_codelen = 1;
                if (is_numeric(substr($ps_format, $i + $hn_codelen, 1))) {
                    ++$hn_codelen;
                    while (is_numeric(substr($ps_format, $i + $hn_codelen, 1)))
                        ++$hn_codelen;

                    $hn_partsecdigits = substr($ps_format, $i + 1, $hn_codelen - 1);
                } else {
                    while (strtoupper(substr($ps_format,
                                             $i + $hn_codelen,
                                             1)) == "F")
                        ++$hn_codelen;

                    // Check next code is not F[numeric]:
                    //
                    if ($hn_codelen > 1 &&
                        is_numeric(substr($ps_format, $i + $hn_codelen, 1)))
                        --$hn_codelen;

                    $hn_partsecdigits = $hn_codelen;
                }

                $hs_partsec = (string) $this->partsecond;
                if (preg_match('/^([0-9]+)(\.([0-9]+))?E-([0-9]+)$/i',
                               $hs_partsec,
                               $ha_matches)) {
                    $hs_partsec =
                        str_repeat("0", $ha_matches[4] - strlen($ha_matches[1])) .
                        $ha_matches[1] .
                        $ha_matches[3];
                } else {
                    $hs_partsec = substr($hs_partsec, 2);
                }
                $hs_partsec = substr($hs_partsec, 0, $hn_partsecdigits);

                // '_formatNumber() will not work for this because the
                // part-second is an int, and we want it to behave like a float:
                //
                if ($hb_nopad) {
                    $hs_partsec = rtrim($hs_partsec, "0");
                    if ($hs_partsec == "")
                        $hs_partsec = "0";
                } else {
                    $hs_partsec = str_pad($hs_partsec,
                                          $hn_partsecdigits,
                                          "0",
                                          STR_PAD_RIGHT);
                }

                $ret .= $hs_partsec;
                $i   += $hn_codelen;
                break;
            case "h":
            case "H":
                if ($this->ob_invalidtime)
                    return $this->_getErrorInvalidTime();
                if (strtoupper(substr($ps_format, $i, 4)) == "HH12") {
                    $hn_hour = $this->hour % 12;
                    if ($hn_hour == 0)
                        $hn_hour = 12;

                    $hn_codelen = 4;
                } else {
                    // Code 'HH' or 'HH24':
                    //
                    $hn_hour    = $this->hour;
                    $hn_codelen = strtoupper(substr($ps_format,
                                                    $i,
                                                    4)) == "HH24" ? 4 : 2;
                }

                $hs_numberformat = substr($ps_format, $i + $hn_codelen, 4);
                $hs_hour = $this->_formatNumber($hn_hour,
                                                $hs_numberformat,
                                                2,
                                                $hb_nopad,
                                                true,
                                                $ps_locale);
                if (Pear::isError($hs_hour))
                    return $hs_hour;

                $ret .= $hs_hour;
                $i   += $hn_codelen + strlen($hs_numberformat);
                break;
            case "i":
            case "I":
                if (is_null($hn_isoyear))
                    list($hn_isoyear, $hn_isoweek, $hn_isoday) =
                        Date_Calc::isoWeekDate($this->day,
                                               $this->month,
                                               $this->year);

                if (strtoupper(substr($ps_format, $i, 2)) == "ID" &&
                    strtoupper(substr($ps_format, $i + 1, 3)) != "DAY"
                    ) {
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_isoday = $this->_formatNumber($hn_isoday,
                                                      $hs_numberformat,
                                                      1,
                                                      $hb_nopad,
                                                      true,
                                                      $ps_locale);
                    if (Pear::isError($hs_isoday))
                        return $hs_isoday;

                    $ret .= $hs_isoday;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "IW") {
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_isoweek = $this->_formatNumber($hn_isoweek,
                                                       $hs_numberformat,
                                                       2,
                                                       $hb_nopad,
                                                       true,
                                                       $ps_locale);
                    if (Pear::isError($hs_isoweek))
                        return $hs_isoweek;

                    $ret .= $hs_isoweek;
                    $i   += 2 + strlen($hs_numberformat);
                } else {
                    // Code I(YYY...):
                    //
                    $hn_codelen = 1;
                    while (strtoupper(substr($ps_format,
                                             $i + $hn_codelen,
                                             1)) == "Y")
                        ++$hn_codelen;

                    $hs_numberformat = substr($ps_format, $i + $hn_codelen, 4);
                    $hs_isoyear = $this->_formatNumber($hn_isoyear,
                                                       $hs_numberformat,
                                                       $hn_codelen,
                                                       $hb_nopad,
                                                       $hb_nosign,
                                                       $ps_locale);
                    if (Pear::isError($hs_isoyear))
                        return $hs_isoyear;

                    $ret .= $hs_isoyear;
                    $i   += $hn_codelen + strlen($hs_numberformat);
                }

                break;
            case "j":
            case "J":
                $hn_jd = Date_Calc::dateToDays($this->day,
                                               $this->month,
                                               $this->year);
                $hs_numberformat = substr($ps_format, $i + 1, 4);

                // Allow sign if negative; allow all digits (specify nought);
                // suppress padding:
                //
                $hs_jd = $this->_formatNumber($hn_jd,
                                              $hs_numberformat,
                                              0,
                                              true,
                                              false,
                                              $ps_locale);
                if (Pear::isError($hs_jd))
                    return $hs_jd;

                $ret .= $hs_jd;
                $i   += 1 + strlen($hs_numberformat);
                break;
            case "m":
                $hb_lower = true;
            case "M":
                if (strtoupper(substr($ps_format, $i, 2)) == "MI") {
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_minute = $this->_formatNumber($this->minute,
                                                      $hs_numberformat,
                                                      2,
                                                      $hb_nopad,
                                                      true,
                                                      $ps_locale);
                    if (Pear::isError($hs_minute))
                        return $hs_minute;

                    $ret .= $hs_minute;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "MM") {
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_month = $this->_formatNumber($this->month,
                                                     $hs_numberformat,
                                                     2,
                                                     $hb_nopad,
                                                     true,
                                                     $ps_locale);
                    if (Pear::isError($hs_month))
                        return $hs_month;

                    $ret .= $hs_month;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 5)) == "MONTH") {
                    $hs_month = Date_Calc::getMonthFullname($this->month);

                    if (!$hb_nopad) {
                        if (is_null($hn_monthpad)) {
                            // Set month padding variable:
                            //
                            $hn_monthpad = 0;
                            foreach (Date_Calc::getMonthNames() as $hs_monthofyear)
                                $hn_monthpad = max($hn_monthpad,
                                                   strlen($hs_monthofyear));
                        }
                        $hs_month = str_pad($hs_month,
                                            $hn_monthpad,
                                            " ",
                                            STR_PAD_RIGHT);
                    }

                    $ret .= $hb_lower ?
                            strtolower($hs_month) :
                            (substr($ps_format, $i + 1, 1) == "O" ?
                             strtoupper($hs_month) :
                             $hs_month);
                    $i   += 5;
                } else if (strtoupper(substr($ps_format, $i, 3)) == "MON") {
                    $hs_month = Date_Calc::getMonthAbbrname($this->month);
                    $ret     .= $hb_lower ?
                                strtolower($hs_month) :
                                (substr($ps_format, $i + 1, 1) == "O" ?
                                 strtoupper($hs_month) :
                                 $hs_month);
                    $i       += 3;
                }

                break;
            case "n":
            case "N":
                // No-Padding rule 'NP' applies to the next code (either trailing
                // spaces or leading/trailing noughts):
                //
                $hb_nopadflag = true;
                $i           += 2;
                break;
            case "p":
                $hb_lower = true;
            case "P":
                if ($this->ob_invalidtime)
                    return $this->_getErrorInvalidTime();
                if (strtoupper(substr($ps_format, $i, 4)) == "P.M.") {
                    $ret .= $this->hour < 12 ?
                            ($hb_lower ? "a.m." : "A.M.") :
                            ($hb_lower ? "p.m." : "P.M.");
                    $i   += 4;
                } else if (strtoupper(substr($ps_format, $i, 2)) == "PM") {
                    $ret .= $this->hour < 12 ?
                            ($hb_lower ? "am" : "AM") :
                            ($hb_lower ? "pm" : "PM");
                    $i   += 2;
                }

                break;
            case "q":
            case "Q":
                // N.B. Current implementation ignores the day and year, but
                // it is possible that a different implementation might be
                // desired, so pass these parameters anyway:
                //
                $hn_quarter = Date_Calc::quarterOfYear($this->day,
                                                       $this->month,
                                                       $this->year);
                $hs_numberformat = substr($ps_format, $i + 1, 4);
                $hs_quarter = $this->_formatNumber($hn_quarter,
                                                   $hs_numberformat,
                                                   1,
                                                   $hb_nopad,
                                                   true,
                                                   $ps_locale);
                if (Pear::isError($hs_quarter))
                    return $hs_quarter;

                $ret .= $hs_quarter;
                $i   += 1 + strlen($hs_numberformat);
                break;
            case "r":
                $hb_lower = true;
            case "R":
                // Code 'RM':
                //
                switch ($this->month) {
                case 1:
                    $hs_monthroman = "i";
                    break;
                case 2:
                    $hs_monthroman = "ii";
                    break;
                case 3:
                    $hs_monthroman = "iii";
                    break;
                case 4:
                    $hs_monthroman = "iv";
                    break;
                case 5:
                    $hs_monthroman = "v";
                    break;
                case 6:
                    $hs_monthroman = "vi";
                    break;
                case 7:
                    $hs_monthroman = "vii";
                    break;
                case 8:
                    $hs_monthroman = "viii";
                    break;
                case 9:
                    $hs_monthroman = "ix";
                    break;
                case 10:
                    $hs_monthroman = "x";
                    break;
                case 11:
                    $hs_monthroman = "xi";
                    break;
                case 12:
                    $hs_monthroman = "xii";
                    break;
                }

                $hs_monthroman = $hb_lower ?
                                 $hs_monthroman :
                                 strtoupper($hs_monthroman);
                $ret .= $hb_nopad ?
                        $hs_monthroman :
                        str_pad($hs_monthroman, 4, " ", STR_PAD_LEFT);
                $i   += 2;
                break;
            case "s":
            case "S":
                // Check for 'SSSSS' before 'SS':
                //
                if (strtoupper(substr($ps_format, $i, 5)) == "SSSSS") {
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $hs_numberformat = substr($ps_format, $i + 5, 4);
                    $hn_second = Date_Calc::secondsPastMidnight($this->hour,
                                                                $this->minute,
                                                                $this->second);
                    $hs_second = $this->_formatNumber($hn_second,
                                                      $hs_numberformat,
                                                      5,
                                                      $hb_nopad,
                                                      true,
                                                      $ps_locale);
                    if (Pear::isError($hs_second))
                        return $hs_second;

                    $ret .= $hs_second;
                    $i   += 5 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "SS") {
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_second = $this->_formatNumber($this->second,
                                                      $hs_numberformat,
                                                      2,
                                                      $hb_nopad,
                                                      true,
                                                      $ps_locale);
                    if (Pear::isError($hs_second))
                        return $hs_second;

                    $ret .= $hs_second;
                    $i   += 2 + strlen($hs_numberformat);
                } else {
                    // One of the following codes:
                    //  'SC(CCC...)'
                    //  'SY(YYY...)'
                    //  'SIY(YYY...)'
                    //  'STZH'
                    //  'STZS'
                    //  'SYEAR'
                    //
                    $hb_showsignflag = true;
                    if ($hb_nopad)
                        $hb_nopadflag = true;
                    ++$i;
                }

                break;
            case "t":
            case "T":
                // Code TZ[...]:
                //

                if (strtoupper(substr($ps_format, $i, 3)) == "TZR") {
                    // This time-zone-related code can be called when the time is
                    // invalid, but the others should return an error:
                    //
                    $ret .= $this->getTZID();
                    $i   += 3;
                } else {
                    if ($this->ob_invalidtime)
                        return $this->_getErrorInvalidTime();

                    if (strtoupper(substr($ps_format, $i, 3)) == "TZC") {
                        $ret .= $this->getTZShortName();
                        $i   += 3;
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZH") {
                        if (is_null($hn_tzoffset))
                            $hn_tzoffset = $this->getTZOffset();

                        $hs_numberformat = substr($ps_format, $i + 3, 4);
                        $hn_tzh = intval($hn_tzoffset / 3600000);

                        // Suppress sign here (it is added later):
                        //
                        $hs_tzh = $this->_formatNumber($hn_tzh,
                                                       $hs_numberformat,
                                                       2,
                                                       $hb_nopad,
                                                       true,
                                                       $ps_locale);
                        if (Pear::isError($hs_tzh))
                            return $hs_tzh;

                        // Display sign, even if positive:
                        //
                        $ret .= ($hb_nosign ? "" : ($hn_tzh >= 0 ? '+' : '-')) .
                                $hs_tzh;
                        $i   += 3 + strlen($hs_numberformat);
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZI") {
                        $ret .= ($this->inDaylightTime() ? '1' : '0');
                        $i   += 3;
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZM") {
                        if (is_null($hn_tzoffset))
                            $hn_tzoffset = $this->getTZOffset();

                        $hs_numberformat = substr($ps_format, $i + 3, 4);
                        $hn_tzm = intval(($hn_tzoffset % 3600000) / 60000);

                        // Suppress sign:
                        //
                        $hs_tzm = $this->_formatNumber($hn_tzm,
                                                       $hs_numberformat,
                                                       2,
                                                       $hb_nopad,
                                                       true,
                                                       $ps_locale);
                        if (Pear::isError($hs_tzm))
                            return $hs_tzm;

                        $ret .= $hs_tzm;
                        $i   += 3 + strlen($hs_numberformat);
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZN") {
                        $ret .= $this->getTZLongName();
                        $i   += 3;
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZO") {
                        if (is_null($hn_tzoffset))
                            $hn_tzoffset = $this->getTZOffset();

                        $hn_tzh = intval(abs($hn_tzoffset) / 3600000);
                        $hn_tzm = intval((abs($hn_tzoffset) % 3600000) / 60000);

                        if ($hn_tzoffset == 0) {
                            $ret .= $hb_nopad ? "Z" : "Z     ";
                        } else {
                            // Display sign, even if positive:
                            //
                            $ret .= ($hn_tzoffset >= 0 ? '+' : '-') .
                                    sprintf("%02d", $hn_tzh) .
                                    ":" .
                                    sprintf("%02d", $hn_tzm);
                        }
                        $i += 3;
                    } else if (strtoupper(substr($ps_format, $i, 3)) == "TZS") {
                        if (is_null($hn_tzoffset))
                            $hn_tzoffset = $this->getTZOffset();

                        $hs_numberformat = substr($ps_format, $i + 3, 4);
                        $hn_tzs = intval($hn_tzoffset / 1000);
                        $hs_tzs = $this->_formatNumber($hn_tzs,
                                                       $hs_numberformat,
                                                       5,
                                                       $hb_nopad,
                                                       $hb_nosign,
                                                       $ps_locale);
                        if (Pear::isError($hs_tzs))
                            return $hs_tzs;

                        $ret .= $hs_tzs;
                        $i   += 3 + strlen($hs_numberformat);
                    }
                }

                break;
            case "u":
            case "U":
                if ($this->ob_invalidtime)
                    return $this->_getErrorInvalidTime();
                $hn_unixtime     = $this->getTime();
                $hs_numberformat = substr($ps_format, $i + 1, 4);

                // Allow sign if negative; allow all digits (specify nought);
                // suppress padding:
                //
                $hs_unixtime = $this->_formatNumber($hn_unixtime,
                                                    $hs_numberformat,
                                                    0,
                                                    true,
                                                    false,
                                                    $ps_locale);
                if (Pear::isError($hs_unixtime))
                    return $hs_unixtime;

                $ret .= $hs_unixtime;
                $i   += 1 + strlen($hs_numberformat);
                break;
            case "w":
            case "W":
                // Check for 'WW' before 'W':
                //
                if (strtoupper(substr($ps_format, $i, 2)) == "WW") {
                    $hn_week = Date_Calc::weekOfYearAbsolute($this->day,
                                                             $this->month,
                                                             $this->year);
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_week = $this->_formatNumber($hn_week,
                                                    $hs_numberformat,
                                                    2,
                                                    $hb_nopad,
                                                    true,
                                                    $ps_locale);
                    if (Pear::isError($hs_week))
                        return $hs_week;

                    $ret .= $hs_week;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "W1") {
                    $hn_week = Date_Calc::weekOfYear1st($this->day,
                                                        $this->month,
                                                        $this->year);
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_week = $this->_formatNumber($hn_week,
                                                    $hs_numberformat,
                                                    2,
                                                    $hb_nopad,
                                                    true,
                                                    $ps_locale);
                    if (Pear::isError($hs_week))
                        return $hs_week;

                    $ret .= $hs_week;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "W4") {
                    $ha_week = Date_Calc::weekOfYear4th($this->day,
                                                        $this->month,
                                                        $this->year);
                    $hn_week = $ha_week[1];
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_week = $this->_formatNumber($hn_week,
                                                    $hs_numberformat,
                                                    2,
                                                    $hb_nopad,
                                                    true,
                                                    $ps_locale);
                    if (Pear::isError($hs_week))
                        return $hs_week;

                    $ret .= $hs_week;
                    $i   += 2 + strlen($hs_numberformat);
                } else if (strtoupper(substr($ps_format, $i, 2)) == "W7") {
                    $ha_week = Date_Calc::weekOfYear7th($this->day,
                                                        $this->month,
                                                        $this->year);
                    $hn_week = $ha_week[1];
                    $hs_numberformat = substr($ps_format, $i + 2, 4);
                    $hs_week = $this->_formatNumber($hn_week,
                                                    $hs_numberformat,
                                                    2,
                                                    $hb_nopad,
                                                    true,
                                                    $ps_locale);
                    if (Pear::isError($hs_week))
                        return $hs_week;

                    $ret .= $hs_week;
                    $i   += 2 + strlen($hs_numberformat);
                } else {
                    // Code 'W':
                    //
                    $hn_week = Date_Calc::weekOfMonthAbsolute($this->day,
                                                              $this->month,
                                                              $this->year);
                    $hs_numberformat = substr($ps_format, $i + 1, 4);
                    $hs_week = $this->_formatNumber($hn_week,
                                                    $hs_numberformat,
                                                    1,
                                                    $hb_nopad,
                                                    true,
                                                    $ps_locale);
                    if (Pear::isError($hs_week))
                        return $hs_week;

                    $ret .= $hs_week;
                    $i   += 1 + strlen($hs_numberformat);
                }

                break;
            case "y":
            case "Y":
                // Check for 'YEAR' first:
                //
                if (strtoupper(substr($ps_format, $i, 4)) == "YEAR") {
                    switch (substr($ps_format, $i, 2)) {
                    case "YE":
                        $hs_spformat = "SP";
                        break;
                    case "Ye":
                        $hs_spformat = "Sp";
                        break;
                    default:
                        $hs_spformat = "sp";
                    }

                    if (($hn_yearabs = abs($this->year)) < 100 ||
                        $hn_yearabs % 100 < 10) {

                        $hs_numberformat = $hs_spformat;

                        // Allow all digits (specify nought); padding irrelevant:
                        //
                        $hs_year = $this->_formatNumber($this->year,
                                                        $hs_numberformat,
                                                        0,
                                                        true,
                                                        $hb_nosign,
                                                        $ps_locale);
                        if (Pear::isError($hs_year))
                            return $hs_year;

                        $ret .= $hs_year;
                    } else {
                        // Year is spelled 'Nineteen Twelve' rather than
                        // 'One thousand Nine Hundred Twelve':
                        //
                        $hn_century = intval($this->year / 100);
                        $hs_numberformat = $hs_spformat;

                        // Allow all digits (specify nought); padding irrelevant:
                        //
                        $hs_century = $this->_formatNumber($hn_century,
                                                           $hs_numberformat,
                                                           0,
                                                           true,
                                                           $hb_nosign,
                                                           $ps_locale);
                        if (Pear::isError($hs_century))
                            return $hs_century;

                        $ret .= $hs_century . " ";

                        $hs_numberformat = $hs_spformat;

                        // Discard sign; padding irrelevant:
                        //
                        $hs_year = $this->_formatNumber($this->year,
                                                        $hs_numberformat,
                                                        2,
                                                        false,
                                                        true,
                                                        $ps_locale);
                        if (Pear::isError($hs_year))
                            return $hs_year;

                        $ret .= $hs_year;
                    }

                    $i += 4;
                } else {
                    // Code Y(YYY...):
                    //
                    $hn_codelen = 1;
                    while (strtoupper(substr($ps_format,
                                             $i + $hn_codelen,
                                             1)) == "Y")
                        ++$hn_codelen;

                    $hs_thousandsep  = null;
                    $hn_thousandseps = 0;
                    if ($hn_codelen <= 3) {
                        while (preg_match('/([,.�\' ])YYY/i',
                                          substr($ps_format,
                                                 $i + $hn_codelen,
                                                 4),
                                          $ha_matches)) {
                            $hn_codelen    += 4;
                            $hs_thousandsep = $ha_matches[1];
                            ++$hn_thousandseps;
                        }
                    }

                    // Check next code is not 'YEAR'
                    //
                    if ($hn_codelen > 1 &&
                        strtoupper(substr($ps_format,
                                          $i + $hn_codelen - 1,
                                          4)) == "YEAR")
                        --$hn_codelen;

                    $hs_numberformat = substr($ps_format, $i + $hn_codelen, 4);
                    $hs_year = $this->_formatNumber($this->year,
                                                    $hs_numberformat,
                                                    $hn_codelen -
                                                        $hn_thousandseps,
                                                    $hb_nopad,
                                                    $hb_nosign,
                                                    $ps_locale,
                                                   $hs_thousandsep);
                    if (Pear::isError($hs_year))
                        return $hs_year;

                    $ret .= $hs_year;
                    $i   += $hn_codelen + strlen($hs_numberformat);
                }

                break;
            default:
                $ret .= $hs_char;
                ++$i;
                break;
            }
        }
        return $ret;
    }


    // }}}
    // {{{ format3()

    /**
     * Formats the date in the same way as 'format()', but using the
     * formatting codes used by the PHP function 'date()'
     *
     * All 'date()' formatting options are supported except 'B'.  This
     * function also responds to the DATE_* constants, such as DATE_COOKIE,
     * which are specified at:
     *
     *  http://www.php.net/manual/en/ref.datetime.php#datetime.constants
     *
     *
     * Formatting options:
     *
     * (Day)
     *
     *  <code>d</code> Day of the month, 2 digits with leading zeros (01 to 31)
     *  <code>D</code> A textual representation of a day, three letters ('Mon'
     *                to 'Sun')
     *  <code>j</code> Day of the month without leading zeros (1 to 31)
     *  <code>l</code> [lowercase 'L'] A full textual representation of the day
     *                of the week ('Sunday' to 'Saturday')
     *  <code>N</code> ISO-8601 numeric representation of the day of the week
     *                (1 (for Monday) to 7 (for Sunday))
     *  <code>S</code> English ordinal suffix for the day of the month, 2
     *                characters ('st', 'nd', 'rd' or 'th')
     *  <code>w</code> Numeric representation of the day of the week (0 (for
     *                Sunday) to 6 (for Saturday))
     *  <code>z</code> The day of the year, starting from 0 (0 to 365)
     *
     * (Week)
     *
     *  <code>W</code> ISO-8601 week number of year, weeks starting on Monday
     *                (00 to 53)
     *
     * (Month)
     *
     *  <code>F</code> A full textual representation of a month ('January' to
     *                'December')
     *  <code>m</code> Numeric representation of a month, with leading zeros
     *                (01 to 12)
     *  <code>M</code> A short textual representation of a month, three letters
     *                ('Jan' to 'Dec')
     *  <code>n</code> Numeric representation of a month, without leading zeros
     *                (1 to 12)
     *  <code>t</code> Number of days in the given month (28 to 31)
     *
     * (Year)
     *
     *  <code>L</code> Whether it is a leap year (1 if it is a leap year, 0
     *                otherwise)
     *  <code>o</code> ISO-8601 year number. This has the same value as Y,
     *                except that if the ISO week number (W) belongs to the
     *                previous or next year, that year is used instead.
     *  <code>Y</code> A full numeric representation of a year, 4 digits (0000
     *                to 9999)
     *  <code>y</code> A two digit representation of a year (00 to 99)
     *
     * (Time)
     *
     *  <code>a</code> Lowercase Ante meridiem and Post meridiem ('am' or
     *                'pm')
     *  <code>A</code> Uppercase Ante meridiem and Post meridiem ('AM' or
     *                'PM')
     *  <code>g</code> 12-hour format of an hour without leading zeros (1 to 12)
     *  <code>G</code> 24-hour format of an hour without leading zeros (0 to 23)
     *  <code>h</code> 12-hour format of an hour with leading zeros (01 to 12)
     *  <code>H</code> 24-hour format of an hour with leading zeros (00 to 23)
     *  <code>i</code> Minutes with leading zeros (00 to 59)
     *  <code>s</code> Seconds, with leading zeros (00 to 59)
     *  <code>u</code> Milliseconds, e.g. '54321'
     *
     * (Time Zone)
     *
     *  <code>e</code> Timezone identifier, e.g. Europe/London
     *  <code>I</code> Whether or not the date is in Summer time (1 if Summer
     *                time, 0 otherwise)
     *  <code>O</code> Difference to Greenwich time (GMT) in hours, e.g. '+0200'
     *  <code>P</code> Difference to Greenwich time (GMT) with colon between
     *                hours and minutes, e.g. '+02:00'
     *  <code>T</code> Timezone abbreviation, e.g. 'GMT', 'EST'
     *  <code>Z</code> Timezone offset in seconds. The offset for timezones west
     *                of UTC is always negative, and for those east of UTC is
     *                always positive. (-43200 to 50400)
     *
     * (Full Date/Time)
     *
     *  <code>c</code> ISO 8601 date, e.g. '2004-02-12T15:19:21+00:00'
     *  <code>r</code> RFC 2822 formatted date, e.g.
     *                'Thu, 21 Dec 2000 16:01:07 +0200'
     *  <code>U</code> Seconds since the Unix Epoch
     *                (January 1 1970 00:00:00 GMT)
     *
     * @param string $ps_format the format string for returned date/time
     *
     * @return   string     date/time in given format
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function format3($ps_format)
    {
        $hs_format2str = "";

        for ($i = 0; $i < strlen($ps_format); ++$i) {
            switch ($hs_char = substr($ps_format, $i, 1)) {
            case 'd':
                $hs_format2str .= 'DD';
                break;
            case 'D':
                $hs_format2str .= 'NPDy';
                break;
            case 'j':
                $hs_format2str .= 'NPDD';
                break;
            case 'l':
                $hs_format2str .= 'NPDay';
                break;
            case 'N':
                $hs_format2str .= 'ID';
                break;
            case 'S':
                $hs_format2str .= 'th';
                break;
            case 'w':
                $hs_format2str .= 'D';
                break;
            case 'z':
                $hs_format2str .= '"' . ($this->getDayOfYear() - 1) . '"';
                break;
            case 'W':
                $hs_format2str .= 'IW';
                break;
            case 'F':
                $hs_format2str .= 'NPMonth';
                break;
            case 'm':
                $hs_format2str .= 'MM';
                break;
            case 'M':
                $hs_format2str .= 'NPMon';
                break;
            case 'n':
                $hs_format2str .= 'NPMM';
                break;
            case 't':
                $hs_format2str .= '"' . $this->getDaysInMonth() . '"';
                break;
            case 'L':
                $hs_format2str .= '"' . ($this->isLeapYear() ? 1 : 0) . '"';
                break;
            case 'o':
                $hs_format2str .= 'IYYY';
                break;
            case 'Y':
                $hs_format2str .= 'YYYY';
                break;
            case 'y':
                $hs_format2str .= 'YY';
                break;
            case 'a':
                $hs_format2str .= 'am';
                break;
            case 'A':
                $hs_format2str .= 'AM';
                break;
            case 'g':
                $hs_format2str .= 'NPHH12';
                break;
            case 'G':
                $hs_format2str .= 'NPHH24';
                break;
            case 'h':
                $hs_format2str .= 'HH12';
                break;
            case 'H':
                $hs_format2str .= 'HH24';
                break;
            case 'i':
                $hs_format2str .= 'MI';
                break;
            case 's':
                $hs_format2str .= 'SS';
                break;
            case 'u':
                $hs_format2str .= 'SSFFF';
                break;
            case 'e':
                $hs_format2str .= 'TZR';
                break;
            case 'I':
                $hs_format2str .= 'TZI';
                break;
            case 'O':
                $hs_format2str .= 'STZHTZM';
                break;
            case 'P':
                $hs_format2str .= 'STZH:TZM';
                break;
            case 'T':
                $hs_format2str .= 'TZC';
                break;
            case 'Z':
                $hs_format2str .= 'TZS';
                break;
            case 'c':
                $hs_format2str .= 'YYYY-MM-DD"T"HH24:MI:SSSTZH:TZM';
                break;
            case 'r':
                $hs_format2str .= 'Dy, DD Mon YYYY HH24:MI:SS STZHTZM';
                break;
            case 'U':
                $hs_format2str .= 'U';
                break;
            case '\\':
                $hs_char = substr($ps_format, ++$i, 1);
                $hs_format2str .= '"' . ($hs_char == '\\' ? '\\\\' : $hs_char) . '"';
                break;
            case '"':
                $hs_format2str .= '"\\""';
                break;
            default:
                $hs_format2str .= '"' . $hs_char . '"';
            }
        }

        $ret = $this->format2($hs_format2str);
        if (PEAR::isError($ret) &&
            $ret->getCode() == DATE_ERROR_INVALIDFORMATSTRING) {
            return PEAR::raiseError("Invalid date format '$ps_format'",
                                    DATE_ERROR_INVALIDFORMATSTRING);
        }

        return $ret;
    }


    // }}}
    // {{{ getTime()

    /**
     * Returns the date/time in Unix time() format
     *
     * Returns a representation of this date in Unix time() format.  This may
     * only be valid for dates from 1970 to ~2038.
     *
     * @return   int        number of seconds since the unix epoch
     * @access   public
     */
    function getTime()
    {
        return $this->getDate(DATE_FORMAT_UNIXTIME);
    }


    // }}}
    // {{{ getTZID()

    /**
     * Returns the unique ID of the time zone, e.g. 'America/Chicago'
     *
     * @return   string     the time zone ID
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getTZID()
    {
        return $this->tz->getID();
    }


    // }}}
    // {{{ _setTZToDefault()

    /**
     * sets time zone to the default time zone
     *
     * If PHP version >= 5.1.0, uses the php.ini configuration directive
     * 'date.timezone' if set and valid, else the value returned by
     * 'date("e")' if valid, else the default specified if the global
     * constant '$GLOBALS["_DATE_TIMEZONE_DEFAULT"]', which if itself
     * left unset, defaults to "UTC".
     *
     * N.B. this is a private method; to set the time zone to the
     * default publicly you should call 'setTZByID()', that is, with no
     * parameter (or a parameter of null).
     *
     * @return   void
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _setTZToDefault()
    {
        if (function_exists('version_compare') &&
            version_compare(phpversion(), "5.1.0", ">=") &&
            (Date_TimeZone::isValidID($hs_id = ini_get("date.timezone")) ||
             Date_TimeZone::isValidID($hs_id = date("e"))
             )
            ) {
            $this->tz = new Date_TimeZone($hs_id);
        } else {
            $this->tz = Date_TimeZone::getDefault();
        }
    }


    // }}}
    // {{{ setTZ()

    /**
     * Sets the time zone of this Date
     *
     * Sets the time zone of this date with the given
     * Date_TimeZone object.  Does not alter the date/time,
     * only assigns a new time zone.  For conversion, use
     * convertTZ().
     *
     * @param object $tz the Date_TimeZone object to use.  If called with a
     *                    parameter that is not a Date_TimeZone object, will
     *                    fall through to setTZByID().
     *
     * @return   void
     * @access   public
     * @see      Date::setTZByID()
     */
    function setTZ($tz)
    {
        if (is_a($tz, 'Date_Timezone')) {
            $this->setTZByID($tz->getID());
        } else {
            $res = $this->setTZByID($tz);
            if (PEAR::isError($res))
                return $res;
        }
    }


    // }}}
    // {{{ setTZByID()

    /**
     * Sets the time zone of this date with the given time zone ID
     *
     * The time zone IDs are drawn from the 'tz data-base' (see
     * http://en.wikipedia.org/wiki/Zoneinfo), which is the de facto
     * internet and IT standard.  (There is no official standard, and
     * the tz data-base is not intended to be a regulating body
     * anyway.)  Lists of valid IDs are maintained at:
     *
     *  http://en.wikipedia.org/wiki/List_of_zoneinfo_timezones
     *  http://www.php.net/manual/en/timezones.php
     *
     * If no time-zone is specified and PHP version >= 5.1.0, the time
     * zone is set automatically to the php.ini configuration directive
     * 'date.timezone' if set and valid, else the value returned by
     * 'date("e")' if valid, else the default specified if the global
     * constant '$GLOBALS["_DATE_TIMEZONE_DEFAULT"]', which if itself
     * left unset, defaults to "UTC".
     *
     * N.B. this function preserves the local date and time, that is,
     * whether in local Summer time or local standard time.  For example,
     * if the time is set to 11.00 Summer time, and the time zone is then
     * set to another time zone, using this function, in which the date
     * falls in standard time, then the time will remain set to 11.00 UTC,
     * and not 10.00.  You can convert a date to another time zone by
     * calling 'convertTZ()'.
     *
     * The ID can also be specified as a UTC offset in one of the following
     * forms, i.e. an offset with no geographical or political base:
     *
     *  UTC[+/-][h]       - e.g. UTC-1     (the preferred form)
     *  UTC[+/-][hh]      - e.g. UTC+03
     *  UTC[+/-][hh][mm]  - e.g. UTC-0530
     *  UTC[+/-][hh]:[mm] - e.g. UTC+03:00
     *
     * N.B. 'UTC' seems to be technically preferred over 'GMT'.  GMT-based
     * IDs still exist in the tz data-base, but beware of POSIX-style
     * offsets which are the opposite way round to what people normally
     * expect.
     *
     * @param string $ps_id a valid time zone id, e.g. 'Europe/London'
     *
     * @return   void
     * @access   public
     * @see      Date::convertTZByID(), Date_TimeZone::isValidID(),
     *            Date_TimeZone::Date_TimeZone()
     */
    function setTZByID($ps_id = null)
    {
        // Whether the date is in Summer time forms the default for
        // the new time zone (if needed, which is very unlikely anyway).
        // This is mainly to prevent unexpected (defaulting) behaviour
        // if the user is in the repeated hour, and switches to a time
        // zone that is also in the repeated hour (e.g. 'Europe/London'
        // and 'Europe/Lisbon').
        //
        $hb_insummertime = $this->inDaylightTime();
        if (PEAR::isError($hb_insummertime)) {
            if ($hb_insummertime->getCode() == DATE_ERROR_INVALIDTIME) {
                $hb_insummertime = false;
            } else {
                return $hb_insummertime;
            }
        }

        if (is_null($ps_id)) {
            $this->_setTZToDefault();
        } else if (Date_TimeZone::isValidID($ps_id)) {
            $this->tz = new Date_TimeZone($ps_id);
        } else {
            return PEAR::raiseError("Invalid time zone ID '$ps_id'",
                                    DATE_ERROR_INVALIDTIMEZONE);
        }

        $this->setLocalTime($this->day,
                            $this->month,
                            $this->year,
                            $this->hour,
                            $this->minute,
                            $this->second,
                            $this->partsecond,
                            $hb_insummertime);
    }


    // }}}
    // {{{ getTZLongName()

    /**
     * Returns the long name of the time zone
     *
     * Returns long form of time zone name, e.g. 'Greenwich Mean Time'.
     * N.B. if the date falls in Summer time, the Summer time name will be
     * returned instead, e.g. 'British Summer Time'.
     *
     * N.B. this is not a unique identifier for the time zone - for this
     * purpose use the time zone ID.
     *
     * @return   string     the long name of the time zone
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getTZLongName()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->tz->getLongName($this->inDaylightTime());
    }


    // }}}
    // {{{ getTZShortName()

    /**
     * Returns the short name of the time zone
     *
     * Returns abbreviated form of time zone name, e.g. 'GMT'.  N.B. if the
     * date falls in Summer time, the Summer time name will be returned
     * instead, e.g. 'BST'.
     *
     * N.B. this is not a unique identifier - for this purpose use the
     * time zone ID.
     *
     * @return   string     the short name of the time zone
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getTZShortName()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->tz->getShortName($this->inDaylightTime());
    }


    // }}}
    // {{{ getTZOffset()

    /**
     * Returns the DST-corrected offset from UTC for the given date
     *
     * Gets the offset to UTC for a given date/time, taking into
     * account daylight savings time, if the time zone observes it and if
     * it is in effect.
     *
     * N.B. that the offset is calculated historically
     * and in the future according to the current Summer time rules,
     * and so this function is proleptically correct, but not necessarily
     * historically correct.  (Although if you want to be correct about
     * times in the distant past, this class is probably not for you
     * because the whole notion of time zones does not apply, and
     * historically there are so many time zone changes, Summer time
     * rule changes, name changes, calendar changes, that calculating
     * this sort of information is beyond the scope of this package
     * altogether.)
     *
     * @return   int        the corrected offset to UTC in milliseconds
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getTZOffset()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->tz->getOffset($this->inDaylightTime());
    }


    // }}}
    // {{{ inDaylightTime()

    /**
     * Tests if this date/time is in DST
     *
     * Returns true if daylight savings time is in effect for
     * this date in this date's time zone.
     *
     * @param bool $pb_repeatedhourdefault value to return if repeated hour is
     *                                      specified (defaults to false)
     *
     * @return   boolean    true if DST is in effect for this date
     * @access   public
     */
    function inDaylightTime($pb_repeatedhourdefault = false)
    {
        if (!$this->tz->hasDaylightTime())
            return false;
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();
        // The return value is 'cached' whenever the date/time is set:
        //
        return $this->hour != $this->on_standardhour ||
               $this->minute != $this->on_standardminute ||
               $this->second != $this->on_standardsecond ||
               $this->partsecond != $this->on_standardpartsecond ||
               $this->day != $this->on_standardday ||
               $this->month != $this->on_standardmonth ||
               $this->year != $this->on_standardyear;
        //
        // (these last 3 conditions are theoretical
        // possibilities but normally will never occur)
    }


    // }}}
    // {{{ convertTZ()

    /**
     * Converts this date to a new time zone
     *
     * Previously this might not have worked correctly if your system did
     * not allow putenv() or if localtime() did not work in your
     * environment, but this implementation is no longer used.
     *
     * @param object $tz Date_TimeZone object to convert to
     *
     * @return   void
     * @access   public
     * @see      Date::convertTZByID()
     */
    function convertTZ($tz)
    {
        if ($this->getTZID() == $tz->getID())
            return;
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        $hn_rawoffset = $tz->getRawOffset() - $this->tz->getRawOffset();
        //$hn_rawoffset = $tz->getOffset($tz->inDaylightTime($this)) - $this->tz->getRawOffset();

        $this->tz     = new Date_TimeZone($tz->getID());

        list($hn_standardyear,
             $hn_standardmonth,
             $hn_standardday,
             $hn_standardhour,
             $hn_standardminute,
             $hn_standardsecond,
             $hn_standardpartsecond) =
             $this->_addOffset($hn_rawoffset,
                              $this->on_standardday,
                              $this->on_standardmonth,
                              $this->on_standardyear,
                              $this->on_standardhour,
                              $this->on_standardminute,
                              $this->on_standardsecond,
                              $this->on_standardpartsecond);

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $hn_standardminute,
                               $hn_standardsecond,
                               $hn_standardpartsecond);
    }


    // }}}
    // {{{ toUTC()

    /**
     * Converts this date to UTC and sets this date's timezone to UTC
     *
     * @return   void
     * @access   public
     */
    function toUTC()
    {
        if ($this->getTZID() == "UTC")
            return;
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        $res = $this->convertTZ(new Date_TimeZone("UTC"));
        if (PEAR::isError($res))
            return $res;
    }


    // }}}
    // {{{ convertTZByID()

    /**
     * Converts this date to a new time zone, given a valid time zone ID
     *
     * Previously this might not have worked correctly if your system did
     * not allow putenv() or if localtime() does not work in your
     * environment, but this implementation is no longer used.
     *
     * @param string $ps_id a valid time zone id, e.g. 'Europe/London'
     *
     * @return   void
     * @access   public
     * @see      Date::setTZByID(), Date_TimeZone::isValidID(),
     *            Date_TimeZone::Date_TimeZone()
     */
    function convertTZByID($ps_id)
    {
        if (!Date_TimeZone::isValidID($ps_id)) {
            return PEAR::raiseError("Invalid time zone ID '$ps_id'",
                                    DATE_ERROR_INVALIDTIMEZONE);
        }

        $res = $this->convertTZ(new Date_TimeZone($ps_id));

        if (PEAR::isError($res))
            return $res;
    }


    // }}}
    // {{{ toUTCbyOffset()

    /**
     * Converts the date/time to UTC by the offset specified
     *
     * This function is no longer called from within the Date class
     * itself because a time zone can be set using a pure offset
     * (e.g. UTC+1), i.e. not a geographical time zone.  However
     * it is retained for backwards compaibility.
     *
     * @param string $ps_offset offset of the form '[+/-][hh]:[mm]',
     *                           '[+/-][hh][mm]', or 'Z'
     *
     * @return   bool
     * @access   private
     */
    function toUTCbyOffset($ps_offset)
    {
        if ($ps_offset == "Z" ||
            preg_match('/^[+\-](00:?00|0{1,2})$/', $ps_offset)) {
            $hs_tzid = "UTC";
        } else if (preg_match('/^[+\-]([0-9]{2,2}:?[0-5][0-9]|[0-9]{1,2})$/',
                   $ps_offset)) {
            $hs_tzid = "UTC" . $ps_offset;
        } else {
            return PEAR::raiseError("Invalid offset '$ps_offset'");
        }

        // If the time is invalid, it does not matter here:
        //
        $this->setTZByID($hs_tzid);

        // Now the time will be valid because it is a time zone that
        // does not observe Summer time:
        //
        $this->toUTC();
    }


    // }}}
    // {{{ addYears()

    /**
     * Converts the date to the specified no of years from the given date
     *
     * To subtract years use a negative value for the '$pn_years'
     * parameter
     *
     * @param int $pn_years years to add
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function addYears($pn_years)
    {
        list($hs_year, $hs_month, $hs_day) =
            explode(" ", Date_Calc::addYears($pn_years,
                                             $this->day,
                                             $this->month,
                                             $this->year,
                                             "%Y %m %d"));
        $this->setLocalTime($hs_day,
                            $hs_month,
                            $hs_year,
                            $this->hour,
                            $this->minute,
                            $this->second,
                            $this->partsecond);
    }


    // }}}
    // {{{ addMonths()

    /**
     * Converts the date to the specified no of months from the given date
     *
     * To subtract months use a negative value for the '$pn_months'
     * parameter
     *
     * @param int $pn_months months to add
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function addMonths($pn_months)
    {
        list($hs_year, $hs_month, $hs_day) =
            explode(" ", Date_Calc::addMonths($pn_months,
                                              $this->day,
                                              $this->month,
                                              $this->year,
                                              "%Y %m %d"));
        $this->setLocalTime($hs_day,
                            $hs_month,
                            $hs_year,
                            $this->hour,
                            $this->minute,
                            $this->second,
                            $this->partsecond);
    }


    // }}}
    // {{{ addDays()

    /**
     * Converts the date to the specified no of days from the given date
     *
     * To subtract days use a negative value for the '$pn_days' parameter
     *
     * @param int $pn_days days to add
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function addDays($pn_days)
    {
        list($hs_year, $hs_month, $hs_day) =
            explode(" ", Date_Calc::addDays($pn_days,
                                            $this->day,
                                            $this->month,
                                            $this->year,
                                            "%Y %m %d"));
        $this->setLocalTime($hs_day,
                            $hs_month,
                            $hs_year,
                            $this->hour,
                            $this->minute,
                            $this->second,
                            $this->partsecond);
    }


    // }}}
    // {{{ addHours()

    /**
     * Converts the date to the specified no of hours from the given date
     *
     * To subtract hours use a negative value for the '$pn_hours' parameter
     *
     * @param int $pn_hours hours to add
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function addHours($pn_hours)
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        list($hn_standardyear,
             $hn_standardmonth,
             $hn_standardday,
             $hn_standardhour) =
             Date_Calc::addHours($pn_hours,
                                 $this->on_standardday,
                                 $this->on_standardmonth,
                                 $this->on_standardyear,
                                 $this->on_standardhour);

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $this->on_standardminute,
                               $this->on_standardsecond,
                               $this->on_standardpartsecond);
    }


    // }}}
    // {{{ addMinutes()

    /**
     * Converts the date to the specified no of minutes from the given date
     *
     * To subtract minutes use a negative value for the '$pn_minutes' parameter
     *
     * @param int $pn_minutes minutes to add
     *
     * @return   void
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function addMinutes($pn_minutes)
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        list($hn_standardyear,
             $hn_standardmonth,
             $hn_standardday,
             $hn_standardhour,
             $hn_standardminute) =
             Date_Calc::addMinutes($pn_minutes,
                                   $this->on_standardday,
                                   $this->on_standardmonth,
                                   $this->on_standardyear,
                                   $this->on_standardhour,
                                   $this->on_standardminute);

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $hn_standardminute,
                               $this->on_standardsecond,
                               $this->on_standardpartsecond);
    }


    // }}}
    // {{{ addSeconds()

    /**
     * Adds a given number of seconds to the date
     *
     * @param mixed $sec          the no of seconds to add as integer or float
     * @param bool  $pb_countleap whether to count leap seconds (defaults to
     *                             value of count-leap-second object property)
     *
     * @return   void
     * @access   public
     */
    function addSeconds($sec, $pb_countleap = null)
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();
        if (!is_int($sec) && !is_float($sec))
            settype($sec, 'int');
        if (!is_null($pb_countleap))
            $pb_countleap = $this->ob_countleapseconds;

        if ($pb_countleap) {
            // Convert to UTC:
            //
            list($hn_standardyear,
                 $hn_standardmonth,
                 $hn_standardday,
                 $hn_standardhour,
                 $hn_standardminute,
                 $hn_standardsecond,
                 $hn_standardpartsecond) =
                $this->_addOffset($this->tz->getRawOffset() * -1,
                                  $this->on_standardday,
                                  $this->on_standardmonth,
                                  $this->on_standardyear,
                                  $this->on_standardhour,
                                  $this->on_standardminute,
                                  $this->on_standardsecond,
                                  $this->on_standardpartsecond);
            list($hn_standardyear,
                 $hn_standardmonth,
                 $hn_standardday,
                 $hn_standardhour,
                 $hn_standardminute,
                 $hn_secondraw) =
                Date_Calc::addSeconds($sec,
                                      $hn_standardday,
                                      $hn_standardmonth,
                                      $hn_standardyear,
                                      $hn_standardhour,
                                      $hn_standardminute,
                                      $hn_standardpartsecond == 0.0 ?
                                          $hn_standardsecond :
                                          $hn_standardsecond +
                                          $hn_standardpartsecond,
                                      $pb_countleap);

            if (is_float($hn_secondraw)) {
                $hn_standardsecond     = intval($hn_secondraw);
                $hn_standardpartsecond = $hn_secondraw - $hn_standardsecond;
            } else {
                $hn_standardsecond     = $hn_secondraw;
                $hn_standardpartsecond = 0.0;
            }

            list($hn_standardyear,
                 $hn_standardmonth,
                 $hn_standardday,
                 $hn_standardhour,
                 $hn_standardminute,
                 $hn_standardsecond,
                 $hn_standardpartsecond) =
                $this->_addOffset($this->tz->getRawOffset(),
                                  $hn_standardday,
                                  $hn_standardmonth,
                                  $hn_standardyear,
                                  $hn_standardhour,
                                  $hn_standardminute,
                                  $hn_standardsecond,
                                  $hn_standardpartsecond);
        } else {
            // Use local standard time:
            //
            list($hn_standardyear,
                 $hn_standardmonth,
                 $hn_standardday,
                 $hn_standardhour,
                 $hn_standardminute,
                 $hn_secondraw) =
                Date_Calc::addSeconds($sec,
                                      $this->on_standardday,
                                      $this->on_standardmonth,
                                      $this->on_standardyear,
                                      $this->on_standardhour,
                                      $this->on_standardminute,
                                      $this->on_standardpartsecond == 0.0 ?
                                          $this->on_standardsecond :
                                          $this->on_standardsecond +
                                          $this->on_standardpartsecond,
                                      false);

            if (is_float($hn_secondraw)) {
                $hn_standardsecond     = intval($hn_secondraw);
                $hn_standardpartsecond = $hn_secondraw - $hn_standardsecond;
            } else {
                $hn_standardsecond     = $hn_secondraw;
                $hn_standardpartsecond = 0.0;
            }
        }

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $hn_standardminute,
                               $hn_standardsecond,
                               $hn_standardpartsecond);
    }


    // }}}
    // {{{ subtractSeconds()

    /**
     * Subtracts a given number of seconds from the date
     *
     * @param mixed $sec          the no of seconds to subtract as integer or
     *                             float
     * @param bool  $pb_countleap whether to count leap seconds (defaults to
     *                             value of count-leap-second object property)
     *
     * @return   void
     * @access   public
     */
    function subtractSeconds($sec, $pb_countleap = null)
    {
        if (is_null($pb_countleap))
            $pb_countleap = $this->ob_countleapseconds;

        $res = $this->addSeconds(-$sec, $pb_countleap);

        if (PEAR::isError($res))
            return $res;
    }


    // }}}
    // {{{ addSpan()

    /**
     * Adds a time span to the date
     *
     * A time span is defined as a unsigned no of days, hours, minutes
     * and seconds, where the no of minutes and seconds must be less than
     * 60, and the no of hours must be less than 24.
     *
     * A span is added (and subtracted) according to the following logic:
     *
     *  Hours, minutes and seconds are added such that if they fall over
     *   a leap second, the leap second is ignored, and not counted.
     *   For example, if a leap second occurred at 23.59.60, the
     *   following calculations:
     *
     *    23.59.59 + one second
     *    23.59.00 + one minute
     *    23.00.00 + one hour
     *
     *   would all produce 00.00.00 the next day.
     *
     *  A day is treated as equivalent to 24 hours, so if the clocks
     *   went backwards at 01.00, and one day was added to the time
     *   00.30, the result would be 23.30 the same day.
     *
     * This is the implementation which is thought to yield the behaviour
     * that the user is most likely to expect, or in another way of
     * looking at it, it is the implementation that produces the least
     * unexpected behaviour.  It basically works in hours, that is, a day
     * is treated as exactly equivalent to 24 hours, and minutes and
     * seconds are treated as equivalent to 1/60th and 1/3600th of an
     * hour.  It should be obvious that working in days is impractical;
     * working in seconds is problematic when it comes to adding days
     * that fall over leap seconds, where it would appear to most users
     * that the function adds only 23 hours, 59 minutes and 59 seconds.
     * It is also problematic to work in any kind of mixture of days,
     * hours, minutes, and seconds, because then the addition of a span
     * would sometimes depend on which order you add the constituent
     * parts, which undermines the concept of a span altogether.
     *
     * If you want alternative functionality, you must use a mixture of
     * the following functions instead:
     *
     *  addYears()
     *  addMonths()
     *  addDays()
     *  addHours()
     *  addMinutes()
     *  addSeconds()
     *
     * @param object $span the time span to add
     *
     * @return   void
     * @access   public
     */
    function addSpan($span)
    {
        if (!is_a($span, 'Date_Span')) {
            return PEAR::raiseError("Invalid argument - not 'Date_Span' object");
        } else if ($this->ob_invalidtime) {
            return $this->_getErrorInvalidTime();
        }

        $hn_days           = $span->day;
        $hn_standardhour   = $this->on_standardhour + $span->hour;
        $hn_standardminute = $this->on_standardminute + $span->minute;
        $hn_standardsecond = $this->on_standardsecond + $span->second;

        if ($hn_standardsecond >= 60) {
            ++$hn_standardminute;
            $hn_standardsecond -= 60;
        }

        if ($hn_standardminute >= 60) {
            ++$hn_standardhour;
            $hn_standardminute -= 60;
        }

        if ($hn_standardhour >= 24) {
            ++$hn_days;
            $hn_standardhour -= 24;
        }

        list($hn_standardyear, $hn_standardmonth, $hn_standardday) =
            explode(" ",
                    Date_Calc::addDays($hn_days,
                                       $this->on_standardday,
                                       $this->on_standardmonth,
                                       $this->on_standardyear,
                                       "%Y %m %d"));

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $hn_standardminute,
                               $hn_standardsecond,
                               $this->on_standardpartsecond);
    }


    // }}}
    // {{{ subtractSpan()

    /**
     * Subtracts a time span from the date
     *
     * N.B. it is impossible for this function to count leap seconds,
     * because the result would be dependent on which order the consituent
     * parts of the span are subtracted from the date.  Therefore, leap
     * seconds are ignored by this function.  If you want to count leap
     * seconds, use 'subtractSeconds()'.
     *
     * @param object $span the time span to subtract
     *
     * @return   void
     * @access   public
     */
    function subtractSpan($span)
    {
        if (!is_a($span, 'Date_Span')) {
            return PEAR::raiseError("Invalid argument - not 'Date_Span' object");
        } else if ($this->ob_invalidtime) {
            return $this->_getErrorInvalidTime();
        }

        $hn_days           = -$span->day;
        $hn_standardhour   = $this->on_standardhour - $span->hour;
        $hn_standardminute = $this->on_standardminute - $span->minute;
        $hn_standardsecond = $this->on_standardsecond - $span->second;

        if ($hn_standardsecond < 0) {
            --$hn_standardminute;
            $hn_standardsecond += 60;
        }

        if ($hn_standardminute < 0) {
            --$hn_standardhour;
            $hn_standardminute += 60;
        }

        if ($hn_standardhour < 0) {
            --$hn_days;
            $hn_standardhour += 24;
        }

        list($hn_standardyear, $hn_standardmonth, $hn_standardday) =
            explode(" ",
                    Date_Calc::addDays($hn_days,
                                       $this->on_standardday,
                                       $this->on_standardmonth,
                                       $this->on_standardyear,
                                       "%Y %m %d"));

        $this->setStandardTime($hn_standardday,
                               $hn_standardmonth,
                               $hn_standardyear,
                               $hn_standardhour,
                               $hn_standardminute,
                               $hn_standardsecond,
                               $this->on_standardpartsecond);
    }


    // }}}
    // {{{ dateDiff()

    /**
     * Subtract supplied date and return answer in days
     *
     * If the second parameter '$pb_ignoretime' is specified as false, the time
     * parts of the two dates will be ignored, and the integral no of days
     * between the day-month-year parts of the two dates will be returned.  If
     * either of the two dates have an invalid time, the integral no of days
     * will also be returned, else the returned value will be the no of days as
     * a float, with each hour being treated as 1/24th of a day and so on.
     *
     * For example,
     *  21/11/2007 13.00 minus 21/11/2007 01.00
     * returns 0.5
     *
     * Note that if the passed date is in the past, a positive value will be
     * returned, and if it is in the future, a negative value will be returned.
     *
     * @param object $po_date       date to subtract
     * @param bool   $pb_ignoretime whether to ignore the time values of the two
     *                               dates in subtraction (defaults to false)
     *
     * @return   mixed      days between two dates as int or float
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function dateDiff($po_date, $pb_ignoretime = false)
    {
        if ($pb_ignoretime || $this->ob_invalidtime) {
            return Date_Calc::dateToDays($this->day,
                                         $this->month,
                                         $this->year) -
                   Date_Calc::dateToDays($po_date->getDay(),
                                         $po_date->getMonth(),
                                         $po_date->getYear());
        }

        $hn_secondscompare = $po_date->getStandardSecondsPastMidnight();
        if (PEAR::isError($hn_secondscompare)) {
            if ($hn_secondscompare->getCode() != DATE_ERROR_INVALIDTIME) {
                return $hn_secondscompare;
            }

            return Date_Calc::dateToDays($this->day,
                                         $this->month,
                                         $this->year) -
                   Date_Calc::dateToDays($po_date->getDay(),
                                         $po_date->getMonth(),
                                         $po_date->getYear());
        }

        $hn_seconds = $this->getStandardSecondsPastMidnight();

        // If time parts are equal, return int, else return float:
        //
        return Date_Calc::dateToDays($this->on_standardday,
                                     $this->on_standardmonth,
                                     $this->on_standardyear) -
               Date_Calc::dateToDays($po_date->getStandardDay(),
                                     $po_date->getStandardMonth(),
                                     $po_date->getStandardYear()) +
               ($hn_seconds == $hn_secondscompare ? 0 :
                ($hn_seconds - $hn_secondscompare) / 86400);
    }


    // }}}
    // {{{ inEquivalentTimeZones()

    /**
     * Tests whether two dates are in equivalent time zones
     *
     * Equivalence in this context consists in the time zones of the two dates
     * having:
     *
     *  an equal offset from UTC in both standard and Summer time (if
     *   the time zones observe Summer time)
     *  the same Summer time start and end rules, that is, the two time zones
     *   must switch from standard time to Summer time, and vice versa, on the
     *   same day and at the same time
     *
     * An example of two equivalent time zones is 'Europe/London' and
     * 'Europe/Lisbon', which in London is known as GMT/BST, and in Lisbon as
     * WET/WEST.
     *
     * @param object $po_date1 the first Date object to compare
     * @param object $po_date2 the second Date object to compare
     *
     * @return   bool       true if the time zones are equivalent
     * @access   public
     * @static
     * @since    Method available since Release 1.5.0
     */
    function inEquivalentTimeZones($po_date1, $po_date2)
    {
        return $po_date1->tz->isEquivalent($po_date2->getTZID());
    }


    // }}}
    // {{{ compare()

    /**
     * Compares two dates
     *
     * Suitable for use in sorting functions.
     *
     * @param object $od1 the first Date object to compare
     * @param object $od2 the second Date object to compare
     *
     * @return   int        0 if the dates are equal, -1 if '$od1' is
     *                       before '$od2', 1 if '$od1' is after '$od2'
     * @access   public
     * @static
     */
    function compare($od1, $od2)
    {
        $d1 = new Date($od1);
        $d2 = new Date($od2);

        // If the time zones are equivalent, do nothing:
        //
        if (!Date::inEquivalentTimeZones($d1, $d2)) {
            // Only a time zone with a valid time can be converted:
            //
            if ($d2->isTimeValid()) {
                $d2->convertTZByID($d1->getTZID());
            } else if ($d1->isTimeValid()) {
                $d1->convertTZByID($d2->getTZID());
            } else {
                // No comparison can be made without guessing the time:
                //
                return PEAR::raiseError("Both dates have invalid time",
                                        DATE_ERROR_INVALIDTIME);
            }
        }

        $days1 = Date_Calc::dateToDays($d1->getDay(),
                                       $d1->getMonth(),
                                       $d1->getYear());
        $days2 = Date_Calc::dateToDays($d2->getDay(),
                                       $d2->getMonth(),
                                       $d2->getYear());
        if ($days1 < $days2)
            return -1;
        if ($days1 > $days2)
            return 1;

        $hn_hour1 = $d1->getStandardHour();
        if (PEAR::isError($hn_hour1))
            return $hn_hour1;
        $hn_hour2 = $d2->getStandardHour();
        if (PEAR::isError($hn_hour2))
            return $hn_hour2;

        if ($hn_hour1 < $hn_hour2) return -1;
        if ($hn_hour1 > $hn_hour2) return 1;
        if ($d1->getStandardMinute() < $d2->getStandardMinute()) return -1;
        if ($d1->getStandardMinute() > $d2->getStandardMinute()) return 1;
        if ($d1->getStandardSecond() < $d2->getStandardSecond()) return -1;
        if ($d1->getStandardSecond() > $d2->getStandardSecond()) return 1;
        if ($d1->getStandardPartSecond() < $d2->getStandardPartSecond()) return -1;
        if ($d1->getStandardPartSecond() > $d2->getStandardPartSecond()) return 1;
        return 0;
    }


    // }}}
    // {{{ before()

    /**
     * Test if this date/time is before a certain date/time
     *
     * @param object $when the Date object to test against
     *
     * @return   boolean    true if this date is before $when
     * @access   public
     */
    function before($when)
    {
        $hn_compare = Date::compare($this, $when);
        if (PEAR::isError($hn_compare))
            return $hn_compare;

        if ($hn_compare == -1) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ after()

    /**
     * Test if this date/time is after a certain date/time
     *
     * @param object $when the Date object to test against
     *
     * @return   boolean    true if this date is after $when
     * @access   public
     */
    function after($when)
    {
        $hn_compare = Date::compare($this, $when);
        if (PEAR::isError($hn_compare))
            return $hn_compare;

        if ($hn_compare == 1) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ equals()

    /**
     * Test if this date/time is exactly equal to a certain date/time
     *
     * @param object $when the Date object to test against
     *
     * @return   boolean    true if this date is exactly equal to $when
     * @access   public
     */
    function equals($when)
    {
        $hn_compare = Date::compare($this, $when);
        if (PEAR::isError($hn_compare))
            return $hn_compare;

        if ($hn_compare == 0) {
            return true;
        } else {
            return false;
        }
    }


    // }}}
    // {{{ isFuture()

    /**
     * Determine if this date is in the future
     *
     * @return   boolean    true if this date is in the future
     * @access   public
     */
    function isFuture()
    {
        $now = new Date();
        return $this->after($now);
    }


    // }}}
    // {{{ isPast()

    /**
     * Determine if this date is in the past
     *
     * @return   boolean    true if this date is in the past
     * @access   public
     */
    function isPast()
    {
        $now = new Date();
        return $this->before($now);
    }


    // }}}
    // {{{ isLeapYear()

    /**
     * Determine if the year in this date is a leap year
     *
     * @return   boolean    true if this year is a leap year
     * @access   public
     */
    function isLeapYear()
    {
        return Date_Calc::isLeapYear($this->year);
    }


    // }}}
    // {{{ getJulianDate()

    /**
     * Returns the no of days (1-366) since 31st December of the previous year
     *
     * N.B. this function does not return (and never has returned) the 'Julian
     * Date', as described, for example, at:
     *
     *  http://en.wikipedia.org/wiki/Julian_day
     *
     * If you want the day of the year (0-366), use 'getDayOfYear()' instead.
     * If you want the true Julian Day, call one of the following:
     *
     *  <code>format("%E")</code>
     *  <code>format2("J")</code>
     *
     * There currently is no function that calls the Julian Date (as opposed
     * to the 'Julian Day'), although the Julian Day is an approximation.
     *
     * @return     int        the Julian date
     * @access     public
     * @see        Date::getDayOfYear()
     * @deprecated Method deprecated in Release 1.5.0
     */
    function getJulianDate()
    {
        return Date_Calc::julianDate($this->day, $this->month, $this->year);
    }


    // }}}
    // {{{ getDayOfYear()

    /**
     * Returns the no of days (1-366) since 31st December of the previous year
     *
     * @return   int        an integer between 1 and 366
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getDayOfYear()
    {
        return Date_Calc::dayOfYear($this->day, $this->month, $this->year);
    }


    // }}}
    // {{{ getDayOfWeek()

    /**
     * Gets the day of the week for this date (0 = Sunday)
     *
     * @return   int        the day of the week (0 = Sunday)
     * @access   public
     */
    function getDayOfWeek()
    {
        return Date_Calc::dayOfWeek($this->day, $this->month, $this->year);
    }


    // }}}
    // {{{ getWeekOfYear()

    /**
     * Gets the week of the year for this date
     *
     * @return   int        the week of the year
     * @access   public
     */
    function getWeekOfYear()
    {
        return Date_Calc::weekOfYear($this->day, $this->month, $this->year);
    }


    // }}}
    // {{{ getQuarterOfYear()

    /**
     * Gets the quarter of the year for this date
     *
     * @return   int        the quarter of the year (1-4)
     * @access   public
     */
    function getQuarterOfYear()
    {
        return Date_Calc::quarterOfYear($this->day, $this->month, $this->year);
    }


    // }}}
    // {{{ getDaysInMonth()

    /**
     * Gets number of days in the month for this date
     *
     * @return   int        number of days in this month
     * @access   public
     */
    function getDaysInMonth()
    {
        return Date_Calc::daysInMonth($this->month, $this->year);
    }


    // }}}
    // {{{ getWeeksInMonth()

    /**
     * Gets the number of weeks in the month for this date
     *
     * @return   int        number of weeks in this month
     * @access   public
     */
    function getWeeksInMonth()
    {
        return Date_Calc::weeksInMonth($this->month, $this->year);
    }


    // }}}
    // {{{ getDayName()

    /**
     * Gets the full name or abbreviated name of this weekday
     *
     * @param bool $abbr   abbreviate the name
     * @param int  $length length of abbreviation
     *
     * @return   string     name of this day
     * @access   public
     */
    function getDayName($abbr = false, $length = 3)
    {
        if ($abbr) {
            return Date_Calc::getWeekdayAbbrname($this->day,
                                                 $this->month,
                                                 $this->year,
                                                 $length);
        } else {
            return Date_Calc::getWeekdayFullname($this->day,
                                                 $this->month,
                                                 $this->year);
        }
    }


    // }}}
    // {{{ getMonthName()

    /**
     * Gets the full name or abbreviated name of this month
     *
     * @param boolean $abbr abbreviate the name
     *
     * @return   string     name of this month
     * @access   public
     */
    function getMonthName($abbr = false)
    {
        if ($abbr) {
            return Date_Calc::getMonthAbbrname($this->month);
        } else {
            return Date_Calc::getMonthFullname($this->month);
        }
    }


    // }}}
    // {{{ getNextDay()

    /**
     * Get a Date object for the day after this one
     *
     * The time of the returned Date object is the same as this time.
     *
     * @return   object     Date object representing the next day
     * @access   public
     */
    function getNextDay()
    {
        $ret = new Date($this);
        $ret->addDays(1);
        return $ret;
    }


    // }}}
    // {{{ getPrevDay()

    /**
     * Get a Date object for the day before this one
     *
     * The time of the returned Date object is the same as this time.
     *
     * @return   object     Date object representing the previous day
     * @access   public
     */
    function getPrevDay()
    {
        $ret = new Date($this);
        $ret->addDays(-1);
        return $ret;
    }


    // }}}
    // {{{ getNextWeekday()

    /**
     * Get a Date object for the weekday after this one
     *
     * The time of the returned Date object is the same as this time.
     *
     * @return   object     Date object representing the next week-day
     * @access   public
     */
    function getNextWeekday()
    {
        $ret = new Date($this);
        list($hs_year, $hs_month, $hs_day) =
            explode(" ", Date_Calc::nextWeekday($this->day,
                                                $this->month,
                                                $this->year,
                                                "%Y %m %d"));
        $ret->setDayMonthYear($hs_day, $hs_month, $hs_year);
        return $ret;
    }


    // }}}
    // {{{ getPrevWeekday()

    /**
     * Get a Date object for the weekday before this one
     *
     * The time of the returned Date object is the same as this time.
     *
     * @return   object     Date object representing the previous week-day
     * @access   public
     */
    function getPrevWeekday()
    {
        $ret = new Date($this);
        list($hs_year, $hs_month, $hs_day) =
            explode(" ", Date_Calc::prevWeekday($this->day,
                                                $this->month,
                                                $this->year,
                                                "%Y %m %d"));
        $ret->setDayMonthYear($hs_day, $hs_month, $hs_year);
        return $ret;
    }


    // }}}
    // {{{ getYear()

    /**
     * Returns the year field of the date object
     *
     * @return   int        the year
     * @access   public
     */
    function getYear()
    {
        return $this->year;
    }


    // }}}
    // {{{ getMonth()

    /**
     * Returns the month field of the date object
     *
     * @return   int        the minute
     * @access   public
     */
    function getMonth()
    {
        return $this->month;
    }


    // }}}
    // {{{ getDay()

    /**
     * Returns the day field of the date object
     *
     * @return   int        the day
     * @access   public
     */
    function getDay()
    {
        return $this->day;
    }


    // }}}
    // {{{ _getErrorInvalidTime()

    /**
     * Returns invalid time PEAR Error
     *
     * @return   object
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _getErrorInvalidTime()
    {
        return PEAR::raiseError("Invalid time '" .
                                sprintf("%02d.%02d.%02d",
                                        $this->hour,
                                        $this->minute,
                                        $this->second) .
                                "' specified for date '" .
                                Date_Calc::dateFormat($this->day,
                                                      $this->month,
                                                      $this->year,
                                                      "%Y-%m-%d") .
                                "' and in this timezone",
                                DATE_ERROR_INVALIDTIME);
    }


    // }}}
    // {{{ _secondsInDayIsValid()

    /**
     * If leap seconds are observed, checks if the seconds in the day is valid
     *
     * Note that only the local standard time is accessed.
     *
     * @return   bool
     * @access   private
     * @since    Method available since Release 1.5.0
     */
    function _secondsInDayIsValid()
    {
        if ($this->ob_countleapseconds) {
            // Convert to UTC:
            //
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour,
                 $hn_minute,
                 $hn_second,
                 $hn_partsecond) =
                $this->_addOffset($this->tz->getRawOffset() * -1,
                                  $this->on_standardday,
                                  $this->on_standardmonth,
                                  $this->on_standardyear,
                                  $this->on_standardhour,
                                  $this->on_standardminute,
                                  $this->on_standardsecond,
                                  $this->on_standardpartsecond);
            return Date_Calc::secondsPastMidnight($hn_hour,
                                                  $hn_minute,
                                                  $hn_second +
                                                      $hn_partsecond) <
                   Date_Calc::getSecondsInDay($hn_day, $hn_month, $hn_year);
        } else {
            return $this->getStandardSecondsPastMidnight() < 86400;
        }
    }


    // }}}
    // {{{ isTimeValid()

    /**
     * Whether the stored time is valid as a local time
     *
     * An invalid time is one that lies in the 'skipped hour' at the point
     * that the clocks go forward.  Note that the stored date (i.e.
     * the day/month/year, is always valid).
     *
     * The object is able to store an invalid time because a user might
     * unwittingly and correctly store a valid time, and then add one day so
     * as to put the object in the 'skipped' hour (when the clocks go forward).
     * This could be corrected by a conversion to Summer time (by adding one
     * hour); however, if the user then added another day, and had no need for
     * or interest in the time anyway, the behaviour may be rather unexpected.
     * And anyway in this situation, the time originally specified would now,
     * two days on, be valid again.
     *
     * So this class allows an invalid time like this so long as the user does
     * not in any way make use of or request the time while it is in this
     * semi-invalid state, in order to allow for for the fact that he might be
     * only interested in the date, and not the time, and in order not to behave
     * in an unexpected way, especially without throwing an exception to tell
     * the user about it.
     *
     * @return   bool
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function isTimeValid()
    {
        return !$this->ob_invalidtime;
    }


    // }}}
    // {{{ getHour()

    /**
     * Returns the hour field of the date object
     *
     * @return   int        the hour
     * @access   public
     */
    function getHour()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->hour;
    }


    // }}}
    // {{{ getMinute()

    /**
     * Returns the minute field of the date object
     *
     * @return   int        the minute
     * @access   public
     */
    function getMinute()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->minute;
    }


    // }}}
    // {{{ getSecond()

    /**
     * Returns the second field of the date object
     *
     * @return   int        the second
     * @access   public
     */
    function getSecond()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->second;
    }


    // }}}
    // {{{ getSecondsPastMidnight()

    /**
     * Returns the no of seconds since midnight (0-86400) as float
     *
     * @return   float      float which is at least 0 and less than 86400
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getSecondsPastMidnight()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return Date_Calc::secondsPastMidnight($this->hour,
                                              $this->minute,
                                              $this->second) +
               $this->partsecond;
    }


    // }}}
    // {{{ getPartSecond()

    /**
     * Returns the part-second field of the date object
     *
     * @return   float      the part-second
     * @access   protected
     * @since    Method available since Release 1.5.0
     */
    function getPartSecond()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->partsecond;
    }


    // }}}
    // {{{ getStandardYear()

    /**
     * Returns the year field of the local standard time
     *
     * @return   int        the year
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardYear()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardyear;
    }


    // }}}
    // {{{ getStandardMonth()

    /**
     * Returns the month field of the local standard time
     *
     * @return   int        the minute
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardMonth()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardmonth;
    }


    // }}}
    // {{{ getStandardDay()

    /**
     * Returns the day field of the local standard time
     *
     * @return   int        the day
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardDay()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardday;
    }


    // }}}
    // {{{ getStandardHour()

    /**
     * Returns the hour field of the local standard time
     *
     * @return   int        the hour
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardHour()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardhour;
    }


    // }}}
    // {{{ getStandardMinute()

    /**
     * Returns the minute field of the local standard time
     *
     * @return   int        the minute
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardMinute()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardminute;
    }


    // }}}
    // {{{ getStandardSecond()

    /**
     * Returns the second field of the local standard time
     *
     * @return   int        the second
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardSecond()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardsecond;
    }


    // }}}
    // {{{ getStandardSecondsPastMidnight()

    /**
     * Returns the no of seconds since midnight (0-86400) of the
     * local standard time as float
     *
     * @return   float      float which is at least 0 and less than 86400
     * @access   public
     * @since    Method available since Release 1.5.0
     */
    function getStandardSecondsPastMidnight()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return Date_Calc::secondsPastMidnight($this->on_standardhour,
                                              $this->on_standardminute,
                                              $this->on_standardsecond) +
               $this->on_standardpartsecond;
    }


    // }}}
    // {{{ getStandardPartSecond()

    /**
     * Returns the part-second field of the local standard time
     *
     * @return   float      the part-second
     * @access   protected
     * @since    Method available since Release 1.5.0
     */
    function getStandardPartSecond()
    {
        if ($this->ob_invalidtime)
            return $this->_getErrorInvalidTime();

        return $this->on_standardpartsecond;
    }


    // }}}
    // {{{ _addOffset()

    /**
     * Add a time zone offset to the passed date/time
     *
     * @param int   $pn_offset     the offset to add in milliseconds
     * @param int   $pn_day        the day
     * @param int   $pn_month      the month
     * @param int   $pn_year       the year
     * @param int   $pn_hour       the hour
     * @param int   $pn_minute     the minute
     * @param int   $pn_second     the second
     * @param float $pn_partsecond the part-second
     *
     * @return   array      array of year, month, day, hour, minute, second,
     *                       and part-second
     * @access   private
     * @static
     * @since    Method available since Release 1.5.0
     */
    function _addOffset($pn_offset,
                        $pn_day,
                        $pn_month,
                        $pn_year,
                        $pn_hour,
                        $pn_minute,
                        $pn_second,
                        $pn_partsecond)
    {
        if ($pn_offset == 0) {
            return array((int) $pn_year,
                         (int) $pn_month,
                         (int) $pn_day,
                         (int) $pn_hour,
                         (int) $pn_minute,
                         (int) $pn_second,
                         (float) $pn_partsecond);
        }

        if ($pn_offset % 3600000 == 0) {
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour) =
                 Date_Calc::addHours($pn_offset / 3600000,
                                     $pn_day,
                                     $pn_month,
                                     $pn_year,
                                     $pn_hour);

            $hn_minute     = (int) $pn_minute;
            $hn_second     = (int) $pn_second;
            $hn_partsecond = (float) $pn_partsecond;
        } else if ($pn_offset % 60000 == 0) {
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour,
                 $hn_minute) =
                 Date_Calc::addMinutes($pn_offset / 60000,
                                       $pn_day,
                                       $pn_month,
                                       $pn_year,
                                       $pn_hour,
                                       $pn_minute);

            $hn_second     = (int) $pn_second;
            $hn_partsecond = (float) $pn_partsecond;
        } else {
            list($hn_year,
                 $hn_month,
                 $hn_day,
                 $hn_hour,
                 $hn_minute,
                 $hn_secondraw) =
                 Date_Calc::addSeconds($pn_offset / 1000,
                                       $pn_day,
                                       $pn_month,
                                       $pn_year,
                                       $pn_hour,
                                       $pn_partsecond == 0.0 ?
                                           $pn_second :
                                           $pn_second + $pn_partsecond,
                                       false);  // N.B. do not count
                                                // leap seconds

            if (is_float($hn_secondraw)) {
                $hn_second     = intval($hn_secondraw);
                $hn_partsecond = $hn_secondraw - $hn_second;
            } else {
                $hn_second     = $hn_secondraw;
                $hn_partsecond = 0.0;
            }
        }

        return array($hn_year,
                     $hn_month,
                     $hn_day,
                     $hn_hour,
                     $hn_minute,
                     $hn_second,
                     $hn_partsecond);
    }


    // }}}
    // {{{ setLocalTime()

    /**
     * Sets local time (Summer-time-adjusted) and then calculates local
     * standard time
     *
     * @param int   $pn_day                 the day
     * @param int   $pn_month               the month
     * @param int   $pn_year                the year
     * @param int   $pn_hour                the hour
     * @param int   $pn_minute              the minute
     * @param int   $pn_second              the second
     * @param float $pn_partsecond          the part-second
     * @param bool  $pb_repeatedhourdefault whether to assume Summer time if a
     *                                       repeated hour is specified (defaults
     *                                       to false)
     * @param bool  $pb_correctinvalidtime  whether to correct, by adding the
     *                                       local Summer time offset, the
     *                                       specified time if it falls in the
     *                                       skipped hour (defaults to
     *                                       DATE_CORRECTINVALIDTIME_DEFAULT)
     *
     * @return   void
     * @access   protected
     * @see      Date::setStandardTime()
     * @since    Method available since Release 1.5.0
     */
    function setLocalTime($pn_day,
                          $pn_month,
                          $pn_year,
                          $pn_hour,
                          $pn_minute,
                          $pn_second,
                          $pn_partsecond,
                          $pb_repeatedhourdefault = false,
                          $pb_correctinvalidtime = DATE_CORRECTINVALIDTIME_DEFAULT)
    {
        settype($pn_day, "int");
        settype($pn_month, "int");
        settype($pn_year, "int");
        settype($pn_hour, "int");
        settype($pn_minute, "int");
        settype($pn_second, "int");
        settype($pn_partsecond, "float");

        $hb_insummertime =
            $this->tz->inDaylightTime(array($pn_day,
                $pn_month, $pn_year, Date_Calc::secondsPastMidnight($pn_hour,
                $pn_minute, $pn_second) + $pn_partsecond),
                $pb_repeatedhourdefault);
        if (PEAR::isError($hb_insummertime)) {
            if ($hb_insummertime->getCode() != DATE_ERROR_INVALIDTIME) {
                return $hb_insummertime;
            } else if ($pb_correctinvalidtime) {
                // Store passed time as local standard time:
                //
                $this->on_standardday        = $pn_day;
                $this->on_standardmonth      = $pn_month;
                $this->on_standardyear       = $pn_year;
                $this->on_standardhour       = $pn_hour;
                $this->on_standardminute     = $pn_minute;
                $this->on_standardsecond     = $pn_second;
                $this->on_standardpartsecond = $pn_partsecond;

                // Add Summer time offset to passed time:
                //
                list($this->year,
                     $this->month,
                     $this->day,
                     $this->hour,
                     $this->minute,
                     $this->second,
                     $this->partsecond) =
                     $this->_addOffset($this->tz->getDSTSavings(),
                                       $pn_day,
                                       $pn_month,
                                       $pn_year,
                                       $pn_hour,
                                       $pn_minute,
                                       $pn_second,
                                       $pn_partsecond);

                $this->ob_invalidtime = !$this->_secondsInDayIsValid();
            } else {
                // Hedge bets - if the user adds/subtracts a day, then the time
                // will be uncorrupted, and if the user does
                // addition/subtraction with the time, or requests the time,
                // then return an error at that point:
                //
                $this->day        = $pn_day;
                $this->month      = $pn_month;
                $this->year       = $pn_year;
                $this->hour       = $pn_hour;
                $this->minute     = $pn_minute;
                $this->second     = $pn_second;
                $this->partsecond = $pn_partsecond;

                $this->ob_invalidtime = true;
            }

            return;
        } else {
            // Passed time is valid as local time:
            //
            $this->day        = $pn_day;
            $this->month      = $pn_month;
            $this->year       = $pn_year;
            $this->hour       = $pn_hour;
            $this->minute     = $pn_minute;
            $this->second     = $pn_second;
            $this->partsecond = $pn_partsecond;
        }

        $this->ob_invalidtime = !$this->_secondsInDayIsValid();

        if ($hb_insummertime) {
            // Calculate local standard time:
            //
            list($this->on_standardyear,
                 $this->on_standardmonth,
                 $this->on_standardday,
                 $this->on_standardhour,
                 $this->on_standardminute,
                 $this->on_standardsecond,
                 $this->on_standardpartsecond) =
                 $this->_addOffset($this->tz->getDSTSavings() * -1,
                                   $pn_day,
                                   $pn_month,
                                   $pn_year,
                                   $pn_hour,
                                   $pn_minute,
                                   $pn_second,
                                   $pn_partsecond);
        } else {
            // Time is already local standard time:
            //
            $this->on_standardday        = $pn_day;
            $this->on_standardmonth      = $pn_month;
            $this->on_standardyear       = $pn_year;
            $this->on_standardhour       = $pn_hour;
            $this->on_standardminute     = $pn_minute;
            $this->on_standardsecond     = $pn_second;
            $this->on_standardpartsecond = $pn_partsecond;
        }
    }


    // }}}
    // {{{ setStandardTime()

    /**
     * Sets local standard time and then calculates local time (i.e.
     * Summer-time-adjusted)
     *
     * @param int   $pn_day        the day
     * @param int   $pn_month      the month
     * @param int   $pn_year       the year
     * @param int   $pn_hour       the hour
     * @param int   $pn_minute     the minute
     * @param int   $pn_second     the second
     * @param float $pn_partsecond the part-second
     *
     * @return   void
     * @access   protected
     * @see      Date::setLocalTime()
     * @since    Method available since Release 1.5.0
     */
    function setStandardTime($pn_day,
                             $pn_month,
                             $pn_year,
                             $pn_hour,
                             $pn_minute,
                             $pn_second,
                             $pn_partsecond)
    {
        settype($pn_day, "int");
        settype($pn_month, "int");
        settype($pn_year, "int");
        settype($pn_hour, "int");
        settype($pn_minute, "int");
        settype($pn_second, "int");
        settype($pn_partsecond, "float");

        $this->on_standardday        = $pn_day;
        $this->on_standardmonth      = $pn_month;
        $this->on_standardyear       = $pn_year;
        $this->on_standardhour       = $pn_hour;
        $this->on_standardminute     = $pn_minute;
        $this->on_standardsecond     = $pn_second;
        $this->on_standardpartsecond = $pn_partsecond;

        $this->ob_invalidtime = !$this->_secondsInDayIsValid();

        if ($this->tz->inDaylightTimeStandard(array($pn_day, $pn_month,
            $pn_year, Date_Calc::secondsPastMidnight($pn_hour, $pn_minute,
            $pn_second) + $pn_partsecond))) {

            // Calculate local time:
            //
            list($this->year,
                 $this->month,
                 $this->day,
                 $this->hour,
                 $this->minute,
                 $this->second,
                 $this->partsecond) =
                 $this->_addOffset($this->tz->getDSTSavings(),
                                   $pn_day,
                                   $pn_month,
                                   $pn_year,
                                   $pn_hour,
                                   $pn_minute,
                                   $pn_second,
                                   $pn_partsecond);
        } else {
            // Time is already local time:
            //
            $this->day        = $pn_day;
            $this->month      = $pn_month;
            $this->year       = $pn_year;
            $this->hour       = $pn_hour;
            $this->minute     = $pn_minute;
            $this->second     = $pn_second;
            $this->partsecond = $pn_partsecond;
        }
    }


    // }}}
    // {{{ setYear()

    /**
     * Sets the year field of the date object
     *
     * If specified year forms an invalid date, then PEAR error will be
     * returned, unless the validation is over-ridden using the second
     * parameter.
     *
     * @param int  $y           the year
     * @param bool $pb_validate whether to check that the new date is valid
     *
     * @return   void
     * @access   public
     * @see      Date::setDayMonthYear(), Date::setDateTime()
     */
    function setYear($y, $pb_validate = DATE_VALIDATE_DATE_BY_DEFAULT)
    {
        if ($pb_validate && !Date_Calc::isValidDate($this->day, $this->month, $y)) {
            return PEAR::raiseError("'" .
                                    Date_Calc::dateFormat($this->day,
                                                          $this->month,
                                                          $y,
                                                          "%Y-%m-%d") .
                                    "' is invalid calendar date",
                                    DATE_ERROR_INVALIDDATE);
        } else {
            $this->setLocalTime($this->day,
                                $this->month,
                                $y,
                                $this->hour,
                                $this->minute,
                                $this->second,
                                $this->partsecond);
        }
    }


    // }}}
    // {{{ setMonth()

    /**
     * Sets the month field of the date object
     *
     * If specified year forms an invalid date, then PEAR error will be
     * returned, unless the validation is over-ridden using the second
     * parameter.
     *
     * @param int  $m           the month
     * @param bool $pb_validate whether to check that the new date is valid
     *
     * @return   void
     * @access   public
     * @see      Date::setDayMonthYear(), Date::setDateTime()
     */
    function setMonth($m, $pb_validate = DATE_VALIDATE_DATE_BY_DEFAULT)
    {
        if ($pb_validate && !Date_Calc::isValidDate($this->day, $m, $this->year)) {
            return PEAR::raiseError("'" .
                                    Date_Calc::dateFormat($this->day,
                                                          $m,
                                                          $this->year,
                                                          "%Y-%m-%d") .
                                    "' is invalid calendar date",
                                    DATE_ERROR_INVALIDDATE);
        } else {
            $this->setLocalTime($this->day,
                                $m,
                                $this->year,
                                $this->hour,
                                $this->minute,
                                $this->second,
                                $this->partsecond);
        }
    }


    // }}}
    // {{{ setDay()

    /**
     * Sets the day field of the date object
     *
     * If specified year forms an invalid date, then PEAR error will be
     * returned, unless the validation is over-ridden using the second
     * parameter.
     *
     * @param int  $d           the day
     * @param bool $pb_validate whether to check that the new date is valid
     *
     * @return   void
     * @access   public
     * @see      Date::setDayMonthYear(), Date::setDateTime()
     */
    function setDay($d, $pb_validate = DATE_VALIDATE_DATE_BY_DEFAULT)
    {
        if ($pb_validate && !Date_Calc::isValidDate($d, $this->month, $this->year)) {
            return PEAR::raiseError("'" .
                                    Date_Calc::dateFormat($d,
                                                          $this->month,
                                                          $this->year,
                                                          "%Y-%m-%d") .
                                    "' is invalid calendar date",
                                    DATE_ERROR_INVALIDDATE);
        } else {
            $this->setLocalTime($d,
                                $this->month,
                                $this->year,
                                $this->hour,
                                $this->minute,
                                $this->second,
                                $this->partsecond);
        }
    }


    // }}}
    // {{{ setDayMonthYear()

    /**
     * Sets the day, month and year fields of the date object
     *
     * If specified year forms an invalid date, then PEAR error will be
     * returned.  Note that setting each of these fields separately
     * may unintentionally return a PEAR error if a transitory date is
     * invalid between setting these fields.
     *
     * @param int $d the day
     * @param int $m the month
     * @param int $y the year
     *
     * @return   void
     * @access   public
     * @see      Date::setDateTime()
     * @since    Method available since Release 1.5.0
     */
    function setDayMonthYear($d, $m, $y)
    {
        if (!Date_Calc::isValidDate($d, $m, $y)) {
            return PEAR::raiseError("'" .
                                    Date_Calc::dateFormat($d,
                                                          $m,
                                                          $y,
                                                          "%Y-%m-%d") .
                                    "' is invalid calendar date",
                                    DATE_ERROR_INVALIDDATE);
        } else {
            $this->setLocalTime($d,
                                $m,
                                $y,
                                $this->hour,
                                $this->minute,
                                $this->second,
                                $this->partsecond);
        }
    }


    // }}}
    // {{{ setHour()

    /**
     * Sets the hour field of the date object
     *
     * Expects an hour in 24-hour format.
     *
     * @param int  $h                      the hour
     * @param bool $pb_repeatedhourdefault whether to assume Summer time if a
     *                                      repeated hour is specified (defaults
     *                                      to false)
     *
     * @return   void
     * @access   public
     * @see      Date::setHourMinuteSecond(), Date::setDateTime()
     */
    function setHour($h, $pb_repeatedhourdefault = false)
    {
        if ($h > 23 || $h < 0) {
            return PEAR::raiseError("Invalid hour value '$h'");
        } else {
            $ret = $this->setHourMinuteSecond($h,
                                              $this->minute,
                                              $this->partsecond == 0.0 ?
                                                  $this->second :
                                                  $this->second + $this->partsecond,
                                              $pb_repeatedhourdefault);

            if (PEAR::isError($ret))
                return $ret;
        }
    }


    // }}}
    // {{{ setMinute()

    /**
     * Sets the minute field of the date object
     *
     * @param int  $m                      the minute
     * @param bool $pb_repeatedhourdefault whether to assume Summer time if a
     *                                      repeated hour is specified (defaults
     *                                      to false)
     *
     * @return   void
     * @access   public
     * @see      Date::setHourMinuteSecond(), Date::setDateTime()
     */
    function setMinute($m, $pb_repeatedhourdefault = false)
    {
        if ($m > 59 || $m < 0) {
            return PEAR::raiseError("Invalid minute value '$m'");
        } else {
            $ret = $this->setHourMinuteSecond($this->hour,
                                              $m,
                                              $this->partsecond == 0.0 ?
                                                  $this->second :
                                                  $this->second + $this->partsecond,
                                              $pb_repeatedhourdefault);

            if (PEAR::isError($ret))
                return $ret;
        }
    }


    // }}}
    // {{{ setSecond()

    /**
     * Sets the second field of the date object
     *
     * @param mixed $s                      the second as integer or float
     * @param bool  $pb_repeatedhourdefault whether to assume Summer time if a
     *                                       repeated hour is specified
     *                                       (defaults to false)
     *
     * @return   void
     * @access   public
     * @see      Date::setHourMinuteSecond(), Date::setDateTime()
     */
    function setSecond($s, $pb_repeatedhourdefault = false)
    {
        if ($s > 60 || // Leap seconds possible
            $s < 0) {
            return PEAR::raiseError("Invalid second value '$s'");
        } else {
            $ret = $this->setHourMinuteSecond($this->hour,
                                              $this->minute,
                                              $s,
                                              $pb_repeatedhourdefault);

            if (PEAR::isError($ret))
                return $ret;
        }
    }


    // }}}
    // {{{ setPartSecond()

    /**
     * Sets the part-second field of the date object
     *
     * @param float $pn_ps                  the part-second
     * @param bool  $pb_repeatedhourdefault whether to assume Summer time if a
     *                                      repeated hour is specified (defaults
     *                                      to false)
     *
     * @return   void
     * @access   protected
     * @see      Date::setHourMinuteSecond(), Date::setDateTime()
     * @since    Method available since Release 1.5.0
     */
    function setPartSecond($pn_ps, $pb_repeatedhourdefault = false)
    {
        if ($pn_ps >= 1 || $pn_ps < 0) {
            return PEAR::raiseError("Invalid part-second value '$pn_ps'");
        } else {
            $ret = $this->setHourMinuteSecond($this->hour,
                                              $this->minute,
                                              $this->second + $pn_ps,
                                              $pb_repeatedhourdefault);

            if (PEAR::isError($ret))
                return $ret;
        }
    }


    // }}}
    // {{{ setHourMinuteSecond()

    /**
     * Sets the hour, minute, second and part-second fields of the date object
     *
     * N.B. if the repeated hour, due to the clocks going back, is specified,
     * the default is to assume local standard time.
     *
     * @param int   $h                      the hour
     * @param int   $m                      the minute
     * @param mixed $s                      the second as integer or float
     * @param bool  $pb_repeatedhourdefault whether to assume Summer time if a
     *                                       repeated hour is specified
     *                                       (defaults to false)
     *
     * @return   void
     * @access   public
     * @see      Date::setDateTime()
     * @since    Method available since Release 1.5.0
     */
    function setHourMinuteSecond($h, $m, $s, $pb_repeatedhourdefault = false)
    {
        // Split second into integer and part-second:
        //
        if (is_float($s)) {
            $hn_second     = intval($s);
            $hn_partsecond = $s - $hn_second;
        } else {
            $hn_second     = (int) $s;
            $hn_partsecond = 0.0;
        }

        $this->setLocalTime($this->day,
                            $this->month,
                            $this->year,
                            $h,
                            $m,
                            $hn_second,
                            $hn_partsecond,
                            $pb_repeatedhourdefault);
    }


    // }}}
    // {{{ setDateTime()

    /**
     * Sets all the fields of the date object (day, month, year, hour, minute
     * and second)
     *
     * If specified year forms an invalid date, then PEAR error will be
     * returned.  Note that setting each of these fields separately
     * may unintentionally return a PEAR error if a transitory date is
     * invalid between setting these fields.
     *
     * N.B. if the repeated hour, due to the clocks going back, is specified,
     * the default is to assume local standard time.
     *
     * @param int   $pn_day                 the day
     * @param int   $pn_month               the month
     * @param int   $pn_year                the year
     * @param int   $pn_hour                the hour
     * @param int   $pn_minute              the minute
     * @param mixed $pm_second              the second as integer or float
     * @param bool  $pb_repeatedhourdefault whether to assume Summer time if a
     *                                       repeated hour is specified
     *                                       (defaults to false)
     *
     * @return   void
     * @access   public
     * @see      Date::setDayMonthYear(), Date::setHourMinuteSecond()
     * @since    Method available since Release 1.5.0
     */
    function setDateTime($pn_day,
                         $pn_month,
                         $pn_year,
                         $pn_hour,
                         $pn_minute,
                         $pm_second,
                         $pb_repeatedhourdefault = false)
    {
        if (!Date_Calc::isValidDate($d, $m, $y)) {
            return PEAR::raiseError("'" .
                                    Date_Calc::dateFormat($d,
                                                          $m,
                                                          $y,
                                                          "%Y-%m-%d") .
                                    "' is invalid calendar date",
                                    DATE_ERROR_INVALIDDATE);
        } else {
            // Split second into integer and part-second:
            //
            if (is_float($pm_second)) {
                $hn_second     = intval($pm_second);
                $hn_partsecond = $pm_second - $hn_second;
            } else {
                $hn_second     = (int) $pm_second;
                $hn_partsecond = 0.0;
            }

            $this->setLocalTime($d,
                                $m,
                                $y,
                                $h,
                                $m,
                                $hn_second,
                                $hn_partsecond,
                                $pb_repeatedhourdefault);
        }
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
