<?php

/**
 * Displays the main calendar page (month view).
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: calendar.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage calendar
 */

/** @ignore */
define('SM_PATH','../../');

/* SquirrelMail required files. */
include_once(SM_PATH . 'include/validate.php');
/* load date_intl() */
include_once(SM_PATH . 'functions/date.php');

/* Calendar plugin required files. */
include_once(SM_PATH . 'plugins/calendar/calendar_data.php');
include_once(SM_PATH . 'plugins/calendar/functions.php');

/* get globals */
if (! sqgetGlobalVar('month',$month,SQ_FORM) || ! is_numeric($month)) {
    unset($month);
}
if (! sqgetGlobalVar('year',$year,SQ_FORM) || ! is_numeric($year)) {
    unset($year);
}
/* got 'em */

/**
 * display upper part of month calendar view
 * @return void
 * @access private
 */
function startcalendar() {
    global $year, $month, $color;

    $prev_date = mktime(0, 0, 0, $month - 1, 1, $year);
    $act_date  = mktime(0, 0, 0, $month, 1, $year);
    $next_date = mktime(0, 0, 0, $month + 1, 1, $year);
    $prev_month = date( 'm', $prev_date );
    $next_month = date( 'm', $next_date);
    $prev_year = date( 'Y', $prev_date);
    $next_year = date( 'Y', $next_date );
    $self = 'calendar.php';

    echo html_tag( 'tr', "\n".
               html_tag( 'td', "\n".
                   html_tag( 'table', '', '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) .
                       html_tag( 'tr', "\n".
                            html_tag( 'th',
                                "<a href=\"$self?year=".($year-1)."&amp;month=$month\">&lt;&lt;&nbsp;".($year-1)."</a>"
                            ) . "\n".
                            html_tag( 'th',
                                "<a href=\"$self?year=$prev_year&amp;month=$prev_month\">&lt;&nbsp;" .
                                date_intl( 'M', $prev_date). "</a>"
                            ) . "\n".
                            html_tag( 'th', date_intl( 'F Y', $act_date ), '', $color[0], 'colspan="3"') .
                            html_tag( 'th',
                                "<a href=\"$self?year=$next_year&amp;month=$next_month\">" .
                                date_intl( 'M', $next_date) . "&nbsp;&gt;</a>"
                            ) . "\n".
                            html_tag( 'th',
                                "<a href=\"$self?year=".($year+1)."&amp;month=$month\">".($year+1)."&nbsp;&gt;&gt;</a>"
                            )
                       ) . "\n".
                       html_tag( 'tr',
                           html_tag( 'th', _("Sunday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Monday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Tuesday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Wednesday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Thursday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Friday"), '', $color[5], 'width="14%"' ) ."\n" .
                           html_tag( 'th', _("Saturday"), '', $color[5], 'width="14%"' ) ."\n"
                       )
               ) ,
           '', $color[0] ) ."\n";
}

/**
 * main logic for month view of calendar
 * @return void
 * @access private
 */
function drawmonthview() {
    global $year, $month, $color, $calendardata, $todayis;

    $aday = 1 - date('w', mktime(0, 0, 0, $month, 1, $year));
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    while ($aday <= $days_in_month) {
        echo html_tag( 'tr' );
        for ($j=1; $j<=7; $j++) {
            $cdate="$month";
            ($aday<10)?$cdate=$cdate."0$aday":$cdate=$cdate."$aday";
            $cdate=$cdate."$year";
            if ( $aday <= $days_in_month && $aday > 0){
                echo html_tag( 'td', '', 'left', $color[4], 'height="50" valign="top"' ) ."\n".
                     html_tag( 'div', '', 'right' );
                echo(($cdate==$todayis) ? '<font size="-1" color="'.$color[1].'">[ ' . _("TODAY") . " ] " : '<font size="-1">');
                echo "<a href=\"day.php?year=$year&amp;month=$month&amp;day=";
                echo(($aday<10) ? "0" : "");
                echo "$aday\">$aday</a></font></div>";
            } else {
                echo html_tag( 'td', '', 'left', $color[0]) ."\n".
                     "&nbsp;";
            }
            if (isset($calendardata[$cdate])){
                $i=0;
                while ($calfoo = each($calendardata[$cdate])) {
                    $calbar = $calendardata[$cdate][$calfoo['key']];
                    // FIXME: how to display multiline task
                    $title = '['. $calfoo['key']. '] ' .
                        str_replace(array("\r","\n"),array(' ',' '),htmlspecialchars($calbar['message']));
                    // FIXME: link to nowhere
                    echo "<a href=\"#\" style=\"text-decoration:none; color: "
                        .($calbar['priority']==1 ? $color[1] : $color[6])
                        ."\" title=\"$title\">".htmlspecialchars($calbar['title'])."</a><br />\n";
                    $i=$i+1;
                    if($i==2){
                        break;
                    }
                }
            }
            echo "\n</td>\n";
            $aday++;
        }
        echo '</tr>';
    }
}

/**
 * end of monthly view and form to jump to any month and year
 * @return void
 * @access private
 */
function endcalendar() {
    global $year, $month, $day, $color;

    echo html_tag( 'tr' ) ."\n" .
           html_tag( 'td', '', 'left', '', 'colspan="7"' ) ."\n" .
         "          <form name=\"caljump\" action=\"calendar.php\" method=\"post\">\n".
         "          <select name=\"year\">\n";
    select_option_year($year);
    echo "          </select>\n".
         "          <select name=\"month\">\n";
    select_option_month($month);
    echo "          </select>\n".
         '          <input type="submit" value="' . _("Go") . "\" />\n".
         "          </form>\n".
         "          </td></tr>\n".
         "</table></td></tr></table>\n";
}


if( !isset( $month ) || $month <= 0){
    $month = date( 'm' );
}
if( !isset($year) || $year <= 0){
    $year = date( 'Y' );
}
if( !isset($day) || $day <= 0){
    $day = date( 'd' );
}

$todayis = date( 'mdY' );
$calself=basename($PHP_SELF);

displayPageHeader($color, 'None');
calendar_header();
readcalendardata();
startcalendar();
drawmonthview();
endcalendar();

?>
</body></html>