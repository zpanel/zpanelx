<?php

/**
 * Functions to delete a event.
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: event_delete.php 14084 2011-01-06 02:44:03Z pdontthink $
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
if (! sqGetGlobalVar('month',$month,SQ_FORM) || ! is_numeric($month)) {
    unset($month);
}
if (! sqGetGlobalVar('year',$year,SQ_FORM) || ! is_numeric($year)) {
    unset($year);
}
if (! sqGetGlobalVar('day',$day,SQ_FORM) || ! is_numeric($day)) {
    unset($day);
}
if (! sqGetGlobalVar('dyear',$dyear,SQ_FORM) || ! is_numeric($dyear)) {
    unset($dyear);
}
if (! sqGetGlobalVar('dmonth',$dmonth,SQ_FORM) || ! is_numeric($dmonth)) {
    unset($dmonth);
}
if (! sqGetGlobalVar('dday',$dday,SQ_FORM) || ! is_numeric($dday)) {
    unset($dday);
}
if (! sqGetGlobalVar('dhour',$dhour,SQ_FORM) || ! is_numeric($dhour)) {
    unset($dhour);
}
if (! sqGetGlobalVar('dminute',$dminute,SQ_FORM) || ! is_numeric($dminute)) {
    unset($dminute);
}
sqGetGlobalVar('confirmed',$confirmed,SQ_POST);

/* got 'em */

/**
 * Displays confirmation form when event is deleted
 * @return void
 */
function confirm_deletion() {
    global $calself, $dyear, $dmonth, $dday, $dhour, $dminute, $calendardata, $color, $year, $month, $day;

    $tmparray = $calendardata["$dmonth$dday$dyear"]["$dhour$dminute"];

    echo html_tag( 'table',
               html_tag( 'tr',
                   html_tag( 'th', _("Do you really want to delete this event?") . '<br />', '', $color[4], 'colspan="2"' )
               ) .
               html_tag( 'tr',
                   html_tag( 'td', _("Date:"), 'right', $color[4] ) .
                   html_tag( 'td', date_intl(_("m/d/Y"),mktime(0,0,0,$dmonth,$dday,$dyear)), 'left', $color[4] )
               ) .
               html_tag( 'tr',
                   html_tag( 'td', _("Time:"), 'right', $color[4] ) .
                   html_tag( 'td', date_intl(_("H:i"),mktime($dhour,$dminute,0,$dmonth,$dday,$dyear)), 'left', $color[4] )
               ) .
               html_tag( 'tr',
                   html_tag( 'td', _("Title:"), 'right', $color[4] ) .
                   html_tag( 'td', htmlspecialchars($tmparray['title']), 'left', $color[4] )
               ) .
               html_tag( 'tr',
                   html_tag( 'td', _("Message:"), 'right', $color[4] ) .
                   html_tag( 'td', nl2br(htmlspecialchars($tmparray['message'])), 'left', $color[4] )
               ) .
               html_tag( 'tr',
                   html_tag( 'td',
                       "    <form name=\"delevent\" method=\"post\" action=\"$calself\">\n".
                       "       <input type=\"hidden\" name=\"dyear\" value=\"$dyear\" />\n".
                       "       <input type=\"hidden\" name=\"dmonth\" value=\"$dmonth\" />\n".
                       "       <input type=\"hidden\" name=\"dday\" value=\"$dday\" />\n".
                       "       <input type=\"hidden\" name=\"year\" value=\"$year\" />\n".
                       "       <input type=\"hidden\" name=\"month\" value=\"$month\" />\n".
                       "       <input type=\"hidden\" name=\"day\" value=\"$day\" />\n".
                       "       <input type=\"hidden\" name=\"dhour\" value=\"$dhour\" />\n".
                       "       <input type=\"hidden\" name=\"dminute\" value=\"$dminute\" />\n".
                       "       <input type=\"hidden\" name=\"confirmed\" value=\"yes\" />\n".
                       '       <input type="submit" value="' . _("Yes") . "\" />\n".
                       "    </form>\n" ,
                   'right', $color[4] ) .
                   html_tag( 'td',
                       "    <form name=\"nodelevent\" method=\"post\" action=\"day.php\">\n".
                       "       <input type=\"hidden\" name=\"year\" value=\"$year\" />\n".
                       "       <input type=\"hidden\" name=\"month\" value=\"$month\" />\n".
                       "       <input type=\"hidden\" name=\"day\" value=\"$day\" />\n".
                       '       <input type="submit" value="' . _("No") . "\" />\n".
                       "    </form>\n" ,
                   'left', $color[4] )
               ) ,
           '', $color[0], 'border="0" cellpadding="2" cellspacing="1"' );
}

if ($month <= 0){
    $month = date( 'm' );
}
if ($year <= 0){
    $year = date( 'Y' );
}
if ($day <= 0){
    $day = date( 'd' );
}

$calself=basename($PHP_SELF);

displayPageHeader($color, 'None');
//load calendar menu
calendar_header();

echo html_tag( 'tr', '', '', $color[0] ) .
           html_tag( 'td' ) .
               html_tag( 'table', '', '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) .
                   html_tag( 'tr' ) .
                       html_tag( 'td', '', 'left' ) .
     date_intl( _("l, F j Y"), mktime(0, 0, 0, $month, $day, $year));
if (isset($dyear) && isset($dmonth) && isset($dday) && isset($dhour) && isset($dminute)){
    if (isset($confirmed)){
        delete_event("$dmonth$dday$dyear", "$dhour$dminute");
        echo '<br /><br />' . _("Event deleted!") . "<br />\n";
        echo "<a href=\"day.php?year=$year&amp;month=$month&amp;day=$day\">" .
          _("Day View") . "</a>\n";
    } else {
        readcalendardata();
        confirm_deletion();
    }
} else {
    echo '<br />' . _("Nothing to delete!");
}

?>
</table></td></tr></table>
</body></html>