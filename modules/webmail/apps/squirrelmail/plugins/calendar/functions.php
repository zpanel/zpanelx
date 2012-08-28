<?php

/**
 * Other calendar plugin functions.
 *
 * @copyright 2002-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: functions.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage calendar
 */

/**
 * Adds second layer of calendar links to upper menu
 * @return void
 */
function calendar_header() {
    global $color,$year,$day,$month;

    echo html_tag( 'table', '', '', $color[0], 'border="0" width="100%" cellspacing="0" cellpadding="2"' ) .
         html_tag( 'tr' ) .
         html_tag( 'td', '', 'left', '', 'width="100%"' );

    displayInternalLink("plugins/calendar/calendar.php?year=$year&amp;month=$month",_("Month View"),"right");
    echo "&nbsp;&nbsp;\n";
    displayInternalLink("plugins/calendar/day.php?year=$year&amp;month=$month&amp;day=$day",_("Day View"),"right");
    echo "&nbsp;&nbsp;\n";
    // displayInternalLink("plugins/calendar/event_create.php?year=$year&amp;month=$month&amp;day=$day",_("Add Event"),"right");
    // echo "&nbsp;&nbsp;\n";
    echo '</td></tr>';

}

/**
 * Generates html option tags with length values
 * 
 * Hardcoded values from 0 minutes to 6 hours
 * @param integer $selected selected option length
 * @return void
 */
function select_option_length($selected) {
    $eventlength = array(
        '0' => _("0 min."),
        '15' => _("15 min."),
        '30' => _("30 min."),
        '45' => _("45 min."),
        '60' => _("1 hr."),
        '90' => _("1.5 hr."),
        '120' => _("2 hr."),
        '150' => _("2.5 hr."),
        '180' => _("3 hr."),
        '210' => _("3.5 hr."),
        '240' => _("4 hr."),
        '300' => _("5 hr."),
        '360' => _("6 hr.")
    );

    while( $bar = each($eventlength)) {
        if($bar['key']==$selected){
            echo '        <option value="'.$bar['key'].'" selected="selected">'.$bar['value']."</option>\n";
        } else {
            echo '        <option value="'.$bar['key'].'">'.$bar['value']."</option>\n";
        }
    }
}

/**
 * Generates html option tags with minute values
 *
 * Hardcoded values in 5 minute intervals
 * @param integer $selected selected value
 * @return void
 */
function select_option_minute($selected) {
    $eventminute = array(
        '00'=>'00',
        '05'=>'05',
        '10'=>'10',
        '15'=>'15',
        '20'=>'20',
        '25'=>'25',
        '30'=>'30',
        '35'=>'35',
        '40'=>'40',
        '45'=>'45',
        '50'=>'50',
        '55'=>'55'
    );

    while ( $bar = each($eventminute)) {
        if ($bar['key']==$selected){
            echo '        <option value="'.$bar['key'].'" selected="selected">'.$bar['value']."</option>\n";
        } else {
            echo '        <option value="'.$bar['key'].'">'.$bar['value']."</option>\n";
        }
    }
}

/**
 * Generates html option tags with hour values
 * @param integer $selected selected value
 * @return void
 * @todo 12/24 hour format
 */
function select_option_hour($selected) {

    for ($i=0;$i<24;$i++){
        ($i<10)? $ih = "0" . $i : $ih = $i;
        if ($ih==$selected){
            echo '            <option value="'.$ih.'" selected="selected">'.$i."</option>\n";
        } else {
            echo '            <option value="'.$ih.'">'.$i."</option>\n";
        }
    }
}

/**
 * Generates html option tags with priority values
 * @param integer $selected selected value
 * @return void
 */
function select_option_priority($selected) {
    $eventpriority = array(
        '0' => _("Normal"),
        '1' => _("High"),
    );

    while( $bar = each($eventpriority)) {
        if($bar['key']==$selected){
            echo '        <option value="'.$bar['key'].'" selected="selected">'.$bar['value']."</option>\n";
        } else {
            echo '        <option value="'.$bar['key'].'">'.$bar['value']."</option>\n";
        }
    }
}

/**
 * Generates html option tags with year values
 * 
 * Hardcoded values from 1902 to 2037
 * @param integer $selected selected value
 * @return void
 */
function select_option_year($selected) {

    for ($i=1902;$i<2038;$i++){
        if ($i==$selected){
            echo '            <option value="'.$i.'" selected="selected">'.$i."</option>\n";
        } else {
            echo '            <option value="'.$i.'">'.$i."</option>\n";
        }
    }
}

/**
 * Generates html option tags with month values
 * @param integer $selected selected value
 * @return void
 */
function select_option_month($selected) {

    for ($i=1;$i<13;$i++){
        $im=date('m',mktime(0,0,0,$i,1,1));
        $is = getMonthAbrv( date('m',mktime(0,0,0,$i,1,1)) );
        if ($im==$selected){
            echo '            <option value="'.$im.'" selected="selected">'.$is."</option>\n";
        } else {
            echo '            <option value="'.$im.'">'.$is."</option>\n";
        }
    }
}

/**
 * Generates html option tags with day of month values
 * 
 * Hardcoded values from 1 to 31
 * @param integer $selected selected value
 * @return void
 */
function select_option_day($selected) {

    for ($i=1;$i<32;$i++){
        ($i<10)? $ih="0".$i : $ih=$i;
        if ($i==$selected){
            echo '            <option value="'.$ih.'" selected="selected">'.$i."</option>\n";
        } else {
            echo '            <option value="'.$ih.'">'.$i."</option>\n";
        }
    }
}

?>