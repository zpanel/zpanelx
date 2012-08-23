<?php

/**
 * functions to create a event for calendar.
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: event_create.php 14084 2011-01-06 02:44:03Z pdontthink $
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
if (! sqGetGlobalVar('hour',$hour,SQ_FORM) || ! is_numeric($hour)) {
    unset($hour);
}
if (! sqGetGlobalVar('event_hour',$event_hour,SQ_POST) || ! is_numeric($event_hour)) {
    unset($event_hour);
}
if (! sqGetGlobalVar('event_minute',$event_minute,SQ_POST) || ! is_numeric($event_minute)) {
    unset($event_minute);
}
if (! sqGetGlobalVar('event_length',$event_length,SQ_POST) || ! is_numeric($event_length)) {
    unset($event_length);
}
if (! sqGetGlobalVar('event_priority',$event_priority,SQ_POST) || ! is_numeric($event_priority)) {
    unset($event_priority);
}

sqGetGlobalVar('event_title',$event_title,SQ_POST);
sqGetGlobalVar('event_text',$event_text,SQ_POST);
sqGetGlobalVar('send',$send,SQ_POST);

/* got 'em */

//main form to gather event info
function show_event_form() {
    global $color, $editor_size, $year, $day, $month, $hour;

    echo "\n<form name=\"eventscreate\" action=\"event_create.php\" method=\"post\">\n".
         "      <input type=\"hidden\" name=\"year\" value=\"$year\" />\n".
         "      <input type=\"hidden\" name=\"month\" value=\"$month\" />\n".
         "      <input type=\"hidden\" name=\"day\" value=\"$day\" />\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Start time:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) . "\n" .
         "      <select name=\"event_hour\">\n";
    select_option_hour($hour);
    echo "      </select>\n" .
         "      &nbsp;:&nbsp;\n" .
         "      <select name=\"event_minute\">\n";
    select_option_minute("00");
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Length:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) . "\n" .
         "      <select name=\"event_length\">\n";
    select_option_length("0");
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Priority:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) . "\n" .
         "      <select name=\"event_priority\">\n";
    select_option_priority("0");
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Title:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) . "\n" .
         "      <input type=\"text\" name=\"event_title\" value=\"\" size=\"30\" maxlength=\"50\" /><br />\n".
         "      </td></tr>\n".
         html_tag( 'tr',
             html_tag( 'td',
                 "<textarea name=\"event_text\" rows=\"5\" cols=\"$editor_size\" wrap=\"hard\"></textarea>" ,
             'left', $color[4], 'colspan="2"' )
         ) ."\n" .
         html_tag( 'tr',
             html_tag( 'td',
                 '<input type="submit" name="send" value="' .
                 _("Set Event") . '" />' ,
             'left', $color[4], 'colspan="2"' )
         ) ."\n";
    echo "</form>\n";
}


if ( !isset($month) || $month <= 0){
    $month = date( 'm' );
}
if ( !isset($year) || $year <= 0){
    $year = date( 'Y' );
}
if (!isset($day) || $day <= 0){
    $day = date( 'd' );
}
if (!isset($hour) || $hour <= 0){
    $hour = '08';
}

$calself=basename($PHP_SELF);


displayPageHeader($color, 'None');
//load calendar menu
calendar_header();

echo html_tag( 'tr', '', '', $color[0] ) .
           html_tag( 'td', '', 'left' ) .
               html_tag( 'table', '', '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) .
                   html_tag( 'tr',
                       html_tag( 'td', date_intl( _("l, F j Y"), mktime(0, 0, 0, $month, $day, $year)), 'left', '', 'colspan="2"' )
                   );
//if form has not been filled in
if(!isset($event_text)){
    show_event_form();
} else {
    readcalendardata();
    $calendardata["$month$day$year"]["$event_hour$event_minute"] =
    array( 'length'   => $event_length,
           'priority' => $event_priority,
           'title'    => $event_title,
           'message'  => $event_text,
           'reminder' => '' );
    //save
    writecalendardata();
    echo html_tag( 'table',
                html_tag( 'tr',
                    html_tag( 'th', _("Event Has been added!") . "<br />\n", '', $color[4], 'colspan="2"' )
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Date:"), 'right', $color[4] ) . "\n" .
                    html_tag( 'td', date_intl(_("m/d/Y"),mktime(0,0,0,$month,$day,$year)), 'left', $color[4] ) . "\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Time:"), 'right', $color[4] ) . "\n" .
                    html_tag( 'td', date_intl(_("H:i"),mktime($event_hour,$event_minute,0,$month,$day,$year)), 'left', $color[4] ) . "\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Title:"), 'right', $color[4] ) . "\n" .
                    html_tag( 'td', htmlspecialchars($event_title,ENT_NOQUOTES), 'left', $color[4] ) . "\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Message:"), 'right', $color[4] ) . "\n" .
                    html_tag( 'td', nl2br(htmlspecialchars($event_text,ENT_NOQUOTES)), 'left', $color[4] ) . "\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td',
                        "<a href=\"day.php?year=$year&amp;month=$month&amp;day=$day\">" . _("Day View") . "</a>\n" ,
                    'left', $color[4], 'colspan="2"' ) . "\n"
                ) ,
            '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) ."\n";
}

?>
</table></td></tr></table>
</body></html>