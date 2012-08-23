<?php

/**
 * Functions to edit an event.
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: event_edit.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage calendar
 */

/** @ignore */
define('SM_PATH','../../');

/* SquirrelMail required files. */
include_once(SM_PATH . 'include/validate.php');
/* date_intl() */
include_once(SM_PATH . 'functions/date.php');
/* form functions */
include_once(SM_PATH . 'functions/forms.php');

/* Calendar plugin required files. */
include_once(SM_PATH . 'plugins/calendar/calendar_data.php');
include_once(SM_PATH . 'plugins/calendar/functions.php');

/* get globals */

sqGetGlobalVar('updated',$updated,SQ_POST);

/* get date values and make sure that they are numeric */
if (! sqGetGlobalVar('event_year',$event_year,SQ_POST) || ! is_numeric($event_year)) {
    unset($event_year);
}
if (! sqGetGlobalVar('event_month',$event_month,SQ_POST) || ! is_numeric($event_month)) {
    unset($event_month);
}
if (! sqGetGlobalVar('event_day',$event_day,SQ_POST) || ! is_numeric($event_day)) {
    unset($event_day);
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
sqGetGlobalVar('event_title',$event_title,SQ_POST);
sqGetGlobalVar('event_text',$event_text,SQ_POST);
sqGetGlobalVar('send',$send,SQ_POST);

if (! sqGetGlobalVar('event_priority',$event_priority,SQ_POST) || ! is_numeric($event_priority)) {
    unset($event_priority);
}

sqGetGlobalVar('confirmed',$confirmed,SQ_POST);

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
if (! sqGetGlobalVar('minute',$minute,SQ_FORM) || ! is_numeric($minute)) {
    unset($minute);
}
/* got 'em */

/**
 * update event info
 * @return void
 * @access private
 */
function update_event_form() {
    global $color, $editor_size, $year, $day, $month, $hour, $minute, $calendardata;

    $tmparray = $calendardata["$month$day$year"]["$hour$minute"];
    $tab = '    ';
    echo "\n<form name=\"eventupdate\" action=\"event_edit.php\" method=\"post\">\n".
         $tab . addHidden('year',$year).
         $tab . addHidden('month',$month).
         $tab . addHidden('day',$day).
         $tab . addHidden('hour',$hour).
         $tab . addHidden('minute',$minute).
         $tab . addHidden('updated','yes').
         html_tag( 'tr' ) .
         html_tag( 'td', _("Date:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) .
         "      <select name=\"event_year\">\n";
    select_option_year($year);
    echo "      </select>\n" .
         "      &nbsp;&nbsp;\n" .
         "      <select name=\"event_month\">\n";
    select_option_month($month);
    echo "      </select>\n".
         "      &nbsp;&nbsp;\n".
         "      <select name=\"event_day\">\n";
    select_option_day($day);
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Time:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) .
         "      <select name=\"event_hour\">\n";
    select_option_hour($hour);
    echo "      </select>\n".
         "      &nbsp;:&nbsp;\n".
         "      <select name=\"event_minute\">\n";
    select_option_minute($minute);
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Length:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) .
         "      <select name=\"event_length\">\n";
    select_option_length($tmparray['length']);
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Priority:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', '', 'left', $color[4] ) .
         "      <select name=\"event_priority\">\n";
    select_option_priority($tmparray['priority']);
    echo "      </select>\n".
         "      </td></tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', _("Title:"), 'right', $color[4] ) . "\n" .
         html_tag( 'td', addInput('event_title',$tmparray['title'],30,50), 'left', $color[4]) .
             "\n</tr>\n".
         html_tag( 'tr' ) .
         html_tag( 'td', addTextArea('event_text',$tmparray['message'],$editor_size,5),
                   'left', $color[4], 'colspan="2"' ) .
         '</tr>' . html_tag( 'tr' ) .
         html_tag( 'td', addSubmit(_("Update Event"),'send'), 'left', $color[4], 'colspan="2"' ) .
         "</tr></form>\n";
}

/**
 * Confirms event update
 * @return void
 * @access private
 */
function confirm_update() {
    global $calself, $year, $month, $day, $hour, $minute, $calendardata,
        $color, $event_year, $event_month, $event_day, $event_hour, 
        $event_minute, $event_length, $event_priority, $event_title, $event_text;

    $tmparray = $calendardata["$month$day$year"]["$hour$minute"];
    $tab = '    ';

    echo html_tag( 'table',
                html_tag( 'tr',
                    html_tag( 'th', _("Do you really want to change this event from:") . "<br />\n", '', $color[4], 'colspan="2"' ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Date:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', date_intl(_("m/d/Y"),mktime(0,0,0,$month,$day,$year)), 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Time:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', date_intl(_("H:i"),mktime($hour,$minute,0,$month,$day,$year)) , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Priority:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', $tmparray['priority'] , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Title:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', htmlspecialchars($tmparray['title']) , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Message:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', nl2br(htmlspecialchars($tmparray['message'])) , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'th', _("to:") . "<br />\n", '', $color[4], 'colspan="2"' ) ."\n"
                ) .

                html_tag( 'tr',
                    html_tag( 'td', _("Date:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', date_intl(_("m/d/Y"),mktime(0,0,0,$event_month,$event_day,$event_year)), 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Time:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', date_intl(_("H:i"),mktime($event_hour,$event_minute,0,$event_month,$event_day,$event_year)), 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Priority:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', $event_priority , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Title:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', htmlspecialchars($event_title) , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td', _("Message:") , 'right', $color[4] ) ."\n" .
                    html_tag( 'td', nl2br(htmlspecialchars($event_text)) , 'left', $color[4] ) ."\n"
                ) .
                html_tag( 'tr',
                    html_tag( 'td',
                        "<form name=\"updateevent\" method=\"post\" action=\"$calself\">\n".
                        $tab . addHidden('year',$year).
                        $tab . addHidden('month',$month).
                        $tab . addHidden('day',$day).
                        $tab . addHidden('hour',$hour).
                        $tab . addHidden('minute',$minute).
                        $tab . addHidden('event_year',$event_year).
                        $tab . addHidden('event_month',$event_month).
                        $tab . addHidden('event_day',$event_day).
                        $tab . addHidden('event_hour',$event_hour).
                        $tab . addHidden('event_minute',$event_minute).
                        $tab . addHidden('event_priority',$event_priority).
                        $tab . addHidden('event_length',$event_length).
                        $tab . addHidden('event_title',$event_title).
                        $tab . addHidden('event_text',$event_text).
                        $tab . addHidden('updated','yes').
                        $tab . addHidden('confirmed','yes').
                        $tab . addSubmit(_("Yes")).
                        "</form>\n" ,
                    'right', $color[4] ) ."\n" .
                    html_tag( 'td',
                        "<form name=\"nodelevent\" method=\"post\" action=\"day.php\">\n".
                        $tab . addHidden('year',$year).
                        $tab . addHidden('month',$month).
                        $tab . addHidden('day',$day).
                        $tab . addSubmit(_("No")).
                        "</form>\n" ,
                    'left', $color[4] ) ."\n"
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
if ($hour <= 0){
    $hour = '08';
}

$calself=basename($PHP_SELF);

displayPageHeader($color, 'None');
//load calendar menu
calendar_header();

echo html_tag( 'tr', '', '', $color[0] ) .
            html_tag( 'td', '', 'left' ) .
                html_tag( 'table', '', '', $color[0], 'width="100%" border="0" cellpadding="2" cellspacing="1"' ) .
                    html_tag( 'tr' ) .
                        html_tag( 'td',
                            date_intl( _("l, F j Y"), mktime(0, 0, 0, $month, $day, $year)) ,
                        'left', '', 'colspan="2"' );
if (!isset($updated)){
    //get changes to event
    readcalendardata();
    update_event_form();
} else {
    if (!isset($confirmed)){
        //confirm changes
        readcalendardata();
        confirm_update();
    } else {
        update_event("$month$day$year", "$hour$minute");
        echo html_tag( 'tr',
                   html_tag( 'td', _("Event updated!"), 'left' )
                ) . "\n";
        echo html_tag( 'tr',
                   html_tag( 'td',
                       "<a href=\"day.php?year=$year&amp;month=$month&amp;day=$day\">" .
                       _("Day View") ."</a>",
                   'left' )
                ) . "\n";

        $fixdate = date( 'mdY', mktime(0, 0, 0, $event_month, $event_day, $event_year));
        //if event has been moved to different year then act accordingly
        if ($year==$event_year){
            $calendardata["$fixdate"]["$event_hour$event_minute"] = array('length'   => $event_length,
                                                                          'priority' => $event_priority,
                                                                          'title'    => $event_title,
                                                                          'message'  => $event_text);
            writecalendardata();
        } else {
            writecalendardata();
            $year=$event_year;
            $calendardata = array();
            readcalendardata();
            $calendardata["$fixdate"]["$event_hour$event_minute"] = array('length'   => $event_length,
                                                                          'priority' => $event_priority,
                                                                          'title'    => $event_title,
                                                                          'message'  => $event_text);
            writecalendardata();
        }
    }
}

?>
</table></td></tr></table>
</body></html>