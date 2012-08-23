<?php

/**
 * Displays the day page (day view).
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: day.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage calendar
 */

/** @ignore */
define('SM_PATH','../../');

/* SquirrelMail required files. */
include_once(SM_PATH . 'include/validate.php');
/* date_intl() */
include_once(SM_PATH . 'functions/date.php');

/* Calendar plugin required files. */
include_once(SM_PATH . 'plugins/calendar/calendar_data.php');
include_once(SM_PATH . 'plugins/calendar/functions.php');

/* get globals */
if (! sqGetGlobalVar('year',$year,SQ_FORM) || ! is_numeric($year)) {
    unset($year);
}
if (! sqGetGlobalVar('month',$month,SQ_FORM) || ! is_numeric($month)) {
    unset($month);
}
if (! sqGetGlobalVar('day',$day,SQ_FORM) || ! is_numeric($day)) {
    unset($day);
}
/* got 'em */

/**
 * displays head of day calendar view
 * @return void
 * @access private
 */
function day_header() {
    global $color, $month, $day, $year, $prev_year, $prev_month, $prev_day,
           $prev_date, $next_month, $next_day, $next_year, $next_date;

    echo html_tag( 'tr', '', '', $color[0] ) . "\n".
                html_tag( 'td', '', 'left' ) .
                    html_tag( 'table', '', '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) ."\n" .
                        html_tag( 'tr',
                            html_tag( 'th',
                                "<a href=\"day.php?year=$prev_year&amp;month=$prev_month&amp;day=$prev_day\">&lt;&nbsp;".
                                date_intl('D',$prev_date)."</a>",
                            'left' ) .
                            html_tag( 'th', date_intl( _("l, F j Y"), mktime(0, 0, 0, $month, $day, $year)) ,
                                '', '', 'width="75%"' ) .
                            html_tag( 'th',
                                "<a href=\"day.php?year=$next_year&amp;month=$next_month&amp;day=$next_day\">".
                                date_intl('D',$next_date)."&nbsp;&gt;</a>" ,
                            'right' )
                        );
}

/**
 * events for specific day  are inserted into "daily" array
 * @return void
 * @access private
 */
function initialize_events() {
    global $daily_events, $calendardata, $month, $day, $year;

    for ($i=7;$i<23;$i++){
        if ($i<10){
            $evntime = '0' . $i . '00';
        } else {
            $evntime = $i . '00';
            }
        $daily_events[$evntime] = 'empty';
    }

    $cdate = $month . $day . $year;

    if (isset($calendardata[$cdate])){
        while ( $calfoo = each($calendardata[$cdate])){
            $daily_events["$calfoo[key]"] = $calendardata[$cdate][$calfoo['key']];
        }
    }
}

/**
 * main loop for displaying daily events
 * @return void
 * @access private
 */
function display_events() {
    global $daily_events, $month, $day, $year, $color;

    ksort($daily_events,SORT_STRING);
    $eo=0;
    while ($calfoo = each($daily_events)){
        if ($eo==0){
            $eo=4;
        } else {
            $eo=0;
        }

        $ehour = substr($calfoo['key'],0,2);
        $eminute = substr($calfoo['key'],2,2);
        if (!is_array($calfoo['value'])){
            echo html_tag( 'tr',
                       html_tag( 'td', $ehour . ':' . $eminute, 'left' ) .
                       html_tag( 'td', '&nbsp;', 'left' ) .
                       html_tag( 'td',
                           "<font size=\"-1\"><a href=\"event_create.php?year=$year&amp;month=$month&amp;day=$day&amp;hour="
                           .substr($calfoo['key'],0,2)."\">".
                           _("ADD") . "</a></font>" ,
                       'center' ) ,
                   '', $color[$eo]);

        } else {
            $calbar=$calfoo['value'];
            if ($calbar['length']!=0){
                $elength = '-'.date_intl(_("H:i"),mktime($ehour,$eminute+$calbar['length'],0,1,1,0));
            } else {
                $elength='';
            }
            echo html_tag( 'tr', '', '', $color[$eo] ) .
                        html_tag( 'td', date_intl(_("H:i"),mktime($ehour,$eminute,0,1,1,0)) . $elength, 'left' ) .
                        html_tag( 'td', '', 'left' ) . '[';
                            echo ($calbar['priority']==1) ? 
                                "<font color=\"$color[1]\">".htmlspecialchars($calbar['title']).'</font>' : 
                                htmlspecialchars($calbar['title']);
                            echo'] <div style="margin-left:10px">'.nl2br(htmlspecialchars($calbar['message'])).'</div>' .
                        html_tag( 'td',
                            "<font size=\"-1\"><nobr>\n" .
                            "<a href=\"event_edit.php?year=$year&amp;month=$month&amp;day=$day&amp;hour=".
                            substr($calfoo['key'],0,2)."&amp;minute=".substr($calfoo['key'],2,2)."\">".
                            _("EDIT") . "</a>&nbsp;|&nbsp;\n" .
                            "<a href=\"event_delete.php?dyear=$year&amp;dmonth=$month&amp;dday=$day&amp;dhour=".
                            substr($calfoo['key'],0,2)."&amp;dminute=".substr($calfoo['key'],2,2).
                            "&amp;year=$year&amp;month=$month&amp;day=$day\">" .
                            _("DEL") . '</a>' .
                            "</nobr></font>\n" ,
                        'center' );
        }
    }
}
/* end of day functions */

if ($month <= 0){
    $month = date( 'm');
}
if ($year <= 0){
    $year = date( 'Y');
}
if ($day <= 0){
    $day = date( 'd');
}

$prev_date = mktime(0, 0, 0, $month , $day - 1, $year);
$next_date = mktime(0, 0, 0, $month , $day + 1, $year);
$prev_day = date ('d',$prev_date);
$prev_month = date ('m',$prev_date);
$prev_year = date ('Y',$prev_date);
$next_day = date ('d',$next_date);
$next_month = date ('m',$next_date);
$next_year = date ('Y',$next_date);

$calself=basename($PHP_SELF);

$daily_events = array();

displayPageHeader($color, 'None');
calendar_header();
readcalendardata();
day_header();
initialize_events();
display_events();
?>
</table></td></tr></table>
</body></html>