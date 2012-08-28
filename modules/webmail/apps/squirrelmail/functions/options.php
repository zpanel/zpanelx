<?php

/**
 * options.php
 *
 * Functions needed to display the options pages.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: options.php 14119 2011-07-12 04:36:01Z pdontthink $
 * @package squirrelmail
 * @subpackage prefs
 */

/**********************************************/
/* Define constants used in the options code. */
/**********************************************/

/* Define constants for the various option types. */
define('SMOPT_TYPE_STRING', 0);
define('SMOPT_TYPE_STRLIST', 1);
define('SMOPT_TYPE_TEXTAREA', 2);
define('SMOPT_TYPE_INTEGER', 3);
define('SMOPT_TYPE_FLOAT', 4);
define('SMOPT_TYPE_BOOLEAN', 5);
define('SMOPT_TYPE_HIDDEN', 6);
define('SMOPT_TYPE_COMMENT', 7);
define('SMOPT_TYPE_FLDRLIST', 8);
define('SMOPT_TYPE_FLDRLIST_MULTI', 9);
define('SMOPT_TYPE_EDIT_LIST', 10);
define('SMOPT_TYPE_STRLIST_MULTI', 11);
define('SMOPT_TYPE_BOOLEAN_CHECKBOX', 12);
define('SMOPT_TYPE_BOOLEAN_RADIO', 13);
define('SMOPT_TYPE_STRLIST_RADIO', 14);
define('SMOPT_TYPE_SUBMIT', 15);
define('SMOPT_TYPE_INFO', 16);
define('SMOPT_TYPE_PASSWORD', 17);

/* Define constants for the layout scheme for edit lists. */
define('SMOPT_EDIT_LIST_LAYOUT_LIST', 0);
define('SMOPT_EDIT_LIST_LAYOUT_SELECT', 1);

/* Define constants for the options refresh levels. */
define('SMOPT_REFRESH_NONE', 0);
define('SMOPT_REFRESH_FOLDERLIST', 1);
define('SMOPT_REFRESH_ALL', 2);

/* Define constants for the options size. */
define('SMOPT_SIZE_TINY', 0);
define('SMOPT_SIZE_SMALL', 1);
define('SMOPT_SIZE_MEDIUM', 2);
define('SMOPT_SIZE_LARGE', 3);
define('SMOPT_SIZE_HUGE', 4);
define('SMOPT_SIZE_NORMAL', 5);

define('SMOPT_SAVE_DEFAULT', 'save_option');
define('SMOPT_SAVE_NOOP', 'save_option_noop');

/**
 * SquirrelOption: An option for Squirrelmail.
 *
 * This class is a work in progress. When complete, it will handle
 * presentation and saving of Squirrelmail user options in a simple,
 * streamline manner. Stay tuned for more stuff.
 *
 * Also, I'd like to ask that people leave this alone (mostly :) until
 * I get it a little further along. That should only be a day or two or
 * three. I will remove this message when it is ready for primetime usage.
 * @package squirrelmail
 */
class SquirrelOption {
    /* The basic stuff. */
    var $raw_option_array;
    var $name;
    var $caption;
    var $caption_wrap;
    var $type;
    var $refresh_level;
    var $size;
    var $layout_type;
    var $comment;
    var $script;
    var $post_script;
    var $trailing_text;
    var $yes_text;
    var $no_text;
    var $use_add_widget;
    var $use_delete_widget;

    /* The name of the Save Function for this option. */
    var $save_function;

    /* The various 'values' for this options. */
    var $value;
    var $new_value;
    var $possible_values;
    var $htmlencoded=false;

    function SquirrelOption
    ($raw_option_array, $name, $caption, $type, $refresh_level, $initial_value = '', $possible_values = '', $htmlencoded = false) {
        /* Set the basic stuff. */
        $this->raw_option_array = $raw_option_array;
        $this->name = $name;
        $this->caption = $caption;
        $this->caption_wrap = TRUE;
        $this->type = $type;
        $this->refresh_level = $refresh_level;
        $this->possible_values = $possible_values;
        $this->htmlencoded = $htmlencoded;
// FIXME: why isn't this set by default to NORMAL?
        $this->size = SMOPT_SIZE_MEDIUM;
        $this->layout_type = 0;
        $this->comment = '';
        $this->script = '';
        $this->post_script = '';
        $this->trailing_text = '';
        $this->yes_text = '';
        $this->no_text = '';
        $this->use_add_widget = TRUE;
        $this->use_delete_widget = TRUE;

        /* Check for a current value. */
        if (isset($GLOBALS[$name])) {
            $this->value = $GLOBALS[$name];
        } else if (!empty($initial_value)) {
            $this->value = $initial_value;
        } else {
            $this->value = '';
        }

        /* Check for a new value. */
    if ( !sqgetGlobalVar("new_$name", $this->new_value, SQ_POST ) ) {
            $this->new_value = NULL;
        }

        /* Set the default save function. */
        if ($type != SMOPT_TYPE_HIDDEN
         && $type != SMOPT_TYPE_INFO
         && $type != SMOPT_TYPE_COMMENT) {
            $this->save_function = SMOPT_SAVE_DEFAULT;
        } else {
            $this->save_function = SMOPT_SAVE_NOOP;
        }
    }

    /** Convenience function that identifies which types of
        widgets are stored as (serialized) array values. */
    function is_multiple_valued() {
        return ($this->type == SMOPT_TYPE_FLDRLIST_MULTI
             || $this->type == SMOPT_TYPE_STRLIST_MULTI
             || $this->type == SMOPT_TYPE_EDIT_LIST);
    }

    /* Set the value for this option. */
    function setValue($value) {
        $this->value = $value;
    }

    /* Set the new value for this option. */
    function setNewValue($new_value) {
        $this->new_value = $new_value;
    }

    /* Set whether the caption is allowed to wrap for this option. */
    function setCaptionWrap($caption_wrap) {
        $this->caption_wrap = $caption_wrap;
    }

    /* Set the size for this option. */
    function setSize($size) {
        $this->size = $size;
    }

    /* Set the trailing text for this option. */
    function setTrailingText($trailing_text) {
        $this->trailing_text = $trailing_text;
    }

    /* Set the yes text for this option. */
    function setYesText($yes_text) {
        $this->yes_text = $yes_text;
    }

    /* Set the no text for this option. */
    function setNoText($no_text) {
        $this->no_text = $no_text;
    }

    /* Set the "use add widget" value for this option. */
    function setUseAddWidget($use_add_widget) {
        $this->use_add_widget = $use_add_widget;
    }

    /* Set the "use delete widget" value for this option. */
    function setUseDeleteWidget($use_delete_widget) {
        $this->use_delete_widget = $use_delete_widget;
    }

    /* Set the layout type for this option. */
    function setLayoutType($layout_type) {
        $this->layout_type = $layout_type;
    }

    /* Set the comment for this option. */
    function setComment($comment) {
        $this->comment = $comment;
    }

    /* Set the script for this option. */
    function setScript($script) {
        $this->script = $script;
    }

    /* Set the "post script" for this option. */
    function setPostScript($post_script) {
        $this->post_script = $post_script;
    }

    /* Set the save function for this option. */
    function setSaveFunction($save_function) {
        $this->save_function = $save_function;
    }

    function createHTMLWidget() {
        global $javascript_on, $color;

        // Use new value if available
        if (!is_null($this->new_value)) {
            $tempValue = $this->value;
            $this->value = $this->new_value;
        }

        /* Get the widget for this option type. */
        switch ($this->type) {
            case SMOPT_TYPE_STRING:
                $result = $this->createWidget_String();
                break;
            case SMOPT_TYPE_PASSWORD:
                $result = $this->createWidget_String(TRUE);
                break;
            case SMOPT_TYPE_STRLIST:
                $result = $this->createWidget_StrList();
                break;
            case SMOPT_TYPE_TEXTAREA:
                $result = $this->createWidget_TextArea();
                break;
            case SMOPT_TYPE_INTEGER:
                $result = $this->createWidget_Integer();
                break;
            case SMOPT_TYPE_FLOAT:
                $result = $this->createWidget_Float();
                break;
            case SMOPT_TYPE_BOOLEAN:
                $result = $this->createWidget_Boolean();
                break;
            case SMOPT_TYPE_BOOLEAN_CHECKBOX:
                $result = $this->createWidget_Boolean(TRUE);
                break;
            case SMOPT_TYPE_BOOLEAN_RADIO:
                $result = $this->createWidget_Boolean(FALSE);
                break;
            case SMOPT_TYPE_HIDDEN:
                $result = $this->createWidget_Hidden();
                break;
            case SMOPT_TYPE_COMMENT:
                $result = $this->createWidget_Comment();
                break;
            case SMOPT_TYPE_FLDRLIST:
                $result = $this->createWidget_FolderList();
                break;
            case SMOPT_TYPE_FLDRLIST_MULTI:
                $result = $this->createWidget_FolderList(TRUE);
                break;
            case SMOPT_TYPE_EDIT_LIST:
                $result = $this->createWidget_EditList();
                break;
            case SMOPT_TYPE_STRLIST_MULTI:
                $result = $this->createWidget_StrList(TRUE);
                break;
            case SMOPT_TYPE_STRLIST_RADIO:
                $result = $this->createWidget_StrList(FALSE, TRUE);
                break;
            case SMOPT_TYPE_SUBMIT:
                $result = $this->createWidget_Submit();
                break;
            case SMOPT_TYPE_INFO:
                $result = $this->createWidget_Info();
                break;
            default:
               $result = '<font color="' . $color[2] . '">'
                       . sprintf(_("Option Type '%s' Not Found"), $this->type)
                       . '</font>';
        }

        /* Add the "post script" for this option. */
        $result .= $this->post_script;

        // put correct value back if need be
        if (!is_null($this->new_value)) {
            $this->value = $tempValue;
        }

        /* Now, return the created widget. */
        return $result;
    }

    function createWidget_Info() {
        $result = htmlspecialchars($this->value) . "\n";
        return $result;
    }

    /**
     * Create text box
     *
     * @param boolean $password When TRUE, the text in the input
     *                          widget will be obscured (OPTIONAL;
     *                          default = FALSE).
     *
     * @return string html formated text input
     *
     */
    function createWidget_String($password=FALSE) {
        switch ($this->size) {
            case SMOPT_SIZE_TINY:
                $width = 5;
                break;
            case SMOPT_SIZE_SMALL:
                $width = 12;
                break;
            case SMOPT_SIZE_LARGE:
                $width = 38;
                break;
            case SMOPT_SIZE_HUGE:
                $width = 50;
                break;
            case SMOPT_SIZE_NORMAL:
            default:
                $width = 25;
        }

        $result = "<input type=\"" 
                . ($password ? 'password' : 'text') 
                . "\" name=\"new_$this->name\" value=\""
                . htmlspecialchars($this->value)
                . "\" size=\"$width\" $this->script /> " 
                . htmlspecialchars($this->trailing_text) . "\n";
        return $result;
    }

    /**
     * Create selection box or radio group
     *
     * When $this->htmlencoded is TRUE, the keys and values in
     * $this->possible_values are assumed to be display-safe.
     * Use with care!
     *
     * Note that when building radio buttons instead of a select
     * widget, if the "size" attribute is SMOPT_SIZE_TINY, the
     * radio buttons will be output one after another without
     * linebreaks between them.  Otherwise, each radio button
     * goes on a line of its own.
     *
     * @param boolean $multiple_select When TRUE, the select widget
     *                                 will allow multiple selections
     *                                 (OPTIONAL; default is FALSE
     *                                 (single select list))
     * @param boolean $radio_buttons   When TRUE, the widget will
     *                                 instead be built as a group
     *                                 of radio buttons (and
     *                                 $multiple_select will be
     *                                 forced to FALSE) (OPTIONAL;
     *                                 default is FALSE (select widget))
     *
     * @return string html formated selection box or radio buttons
     *
     */
    function createWidget_StrList($multiple_select=FALSE, $radio_buttons=FALSE) {

        // radio buttons instead of select widget?
        //
        if ($radio_buttons) {

            $result = '';
            foreach ($this->possible_values as $real_value => $disp_value) {
                $result .= "\n" . '<input type="radio" name="new_' . $this->name 
                         . '" id="new_' . $this->name . '_' 
                         . ($this->htmlencoded ? $real_value : htmlspecialchars($real_value))
                         . '" value="'
                         . ($this->htmlencoded ? $real_value : htmlspecialchars($real_value))
                         . '"' . ($real_value == $this->value ? ' checked="checked"' : '')
                         . ' /> <label for="new_' . $this->name . '_'
                         . ($this->htmlencoded ? $real_value : htmlspecialchars($real_value))
                         . '">'
                         . ($this->htmlencoded ? $disp_value : htmlspecialchars($disp_value))
                         . '</label>';
                if ($this->size != SMOPT_SIZE_TINY)
                    $result .= '<br />';
            }

            return $result;
        }


        // everything below applies to select widgets
        //
        switch ($this->size) {
//FIXME: not sure about these sizes... seems like we could add another on the "large" side...
            case SMOPT_SIZE_TINY:
                $height = 3;
                break;
            case SMOPT_SIZE_SMALL:
                $height = 8;
                break;
            case SMOPT_SIZE_LARGE:
                $height = 15;
                break;
            case SMOPT_SIZE_HUGE:
                $height = 25;
                break;
            case SMOPT_SIZE_NORMAL:
            default:
                $height = 5;
        }

        // multiple select lists should already have array values
        if (is_array($this->value))
            $selected = $this->value;
        else
            $selected = array(strtolower($this->value));

        /* Begin the select tag. */
        $result = '<select name="new_' . $this->name
            . ($multiple_select ? '[]" multiple="multiple" size="' . $height . '" ' : '" ')
            . $this->script . ">\n";

        /* Add each possible value to the select list. */
        foreach ($this->possible_values as $real_value => $disp_value) {
            /* Start the next new option string. */
            $new_option = '<option value="' .
                ($this->htmlencoded ? $real_value : htmlspecialchars($real_value)) . '"';

            // multiple select lists have possibly more than one default selection
            if ($multiple_select) {
                foreach ($selected as $default) {
                    if ((string)$default == (string)$real_value) {
                        $new_option .= ' selected="selected"';
                        break;
                    }
                }
            }

            /* If this value is the current value, select it. */
            else if ($real_value == $this->value) {
               $new_option .= ' selected="selected"';
            }

            /* Add the display value to our option string. */
            $new_option .= '>' . ($this->htmlencoded ? $disp_value : htmlspecialchars($disp_value)) . "</option>\n";
            /* And add the new option string to our select tag. */
            $result .= $new_option;
        }

        /* Close the select tag and return our happy result. */
        $result .= '</select>' . htmlspecialchars($this->trailing_text) . "\n";
        return $result;
    }

    /**
     * Create folder selection box
     *
     * @param boolean $multiple_select When TRUE, the select widget
     *                                 will allow multiple selections
     *                                 (OPTIONAL; default is FALSE
     *                                 (single select list))
     *
     * @return string html formated selection box
     *
     */
    function createWidget_FolderList($multiple_select=FALSE) {

        switch ($this->size) {
//FIXME: not sure about these sizes... seems like we could add another on the "large" side...
            case SMOPT_SIZE_TINY:
                $height = 3;
                break;
            case SMOPT_SIZE_SMALL:
                $height = 8;
                break;
            case SMOPT_SIZE_LARGE:
                $height = 15;
                break;
            case SMOPT_SIZE_HUGE:
                $height = 25;
                break;
            case SMOPT_SIZE_NORMAL:
            default:
                $height = 5;
        }

        // multiple select lists should already have array values
        if (is_array($this->value))
            $selected = $this->value;
        else
            $selected = array(strtolower($this->value));

        /* Begin the select tag. */
        $result = '<select name="new_' . $this->name
                . ($multiple_select ? '[]" multiple="multiple" size="' . $height . '"' : '"')
                . " $this->script>\n";

        /* Add each possible value to the select list. */
        foreach ($this->possible_values as $real_value => $disp_value) {

            if ( is_array($disp_value) ) {
                /* For folder list, we passed in the array of boxes.. */
                $selected_lowercase = array();
                foreach ($selected as $i => $box) 
                    $selected_lowercase[$i] = strtolower($box);
                $new_option = sqimap_mailbox_option_list(0, $selected_lowercase, 0, $disp_value);

            } else {
                /* Start the next new option string. */
                $new_option = '<option value="' . htmlspecialchars($real_value) . '"';

                // multiple select lists have possibly more than one default selection
                if ($multiple_select) {
                    foreach ($selected as $default) {
                        if ((string)$default == (string)$real_value) {
                            $new_option .= ' selected="selected"';
                            break;
                        }
                    }
                }

                /* If this value is the current value, select it. */
                else if ($real_value == $this->value) {
                   $new_option .= ' selected="selected"';
                }

                /* Add the display value to our option string. */
                $new_option .= '>' . htmlspecialchars($disp_value) . "</option>\n";
            }
            /* And add the new option string to our select tag. */
            $result .= $new_option;
        }
        /* Close the select tag and return our happy result. */
        $result .= '</select>' . htmlspecialchars($this->trailing_text) . "\n";
        return $result;
    }


    function createWidget_TextArea() {
        switch ($this->size) {
            case SMOPT_SIZE_TINY:  $rows = 3; $cols =  10; break;
            case SMOPT_SIZE_SMALL: $rows = 4; $cols =  30; break;
            case SMOPT_SIZE_LARGE: $rows = 10; $cols =  60; break;
            case SMOPT_SIZE_HUGE:  $rows = 20; $cols =  80; break;
            case SMOPT_SIZE_NORMAL:
            default: $rows = 5; $cols =  50;
        }
        $result = "<textarea name=\"new_$this->name\" rows=\"$rows\" "
                . "cols=\"$cols\" $this->script>"
                . htmlspecialchars($this->value) . "</textarea>\n";
        return ($result);
    }

    function createWidget_Integer() {

        global $javascript_on;

        // add onChange javascript handler to a regular string widget
        // which will strip out all non-numeric chars
        if ($javascript_on)
           return preg_replace('/\/>/', ' onChange="origVal=this.value; newVal=\'\'; '
                    . 'for (i=0;i<origVal.length;i++) { if (origVal.charAt(i)>=\'0\' '
                    . '&& origVal.charAt(i)<=\'9\') newVal += origVal.charAt(i); } '
                    . 'this.value=newVal;" />', $this->createWidget_String());
        else
           return $this->createWidget_String();
    }

    function createWidget_Float() {

        global $javascript_on;

        // add onChange javascript handler to a regular string widget
        // which will strip out all non-numeric (period also OK) chars
        if ($javascript_on)
           return preg_replace('/\/>/', ' onChange="origVal=this.value; newVal=\'\'; '
                    . 'for (i=0;i<origVal.length;i++) { if ((origVal.charAt(i)>=\'0\' '
                    . '&& origVal.charAt(i)<=\'9\') || origVal.charAt(i)==\'.\') '
                    . 'newVal += origVal.charAt(i); } this.value=newVal;" />'
                , $this->createWidget_String());
        else
           return $this->createWidget_String();
    }

    /**
     * Create boolean widget
     *
     * When creating Yes/No radio buttons, the "yes_text"
     * and "no_text" option attributes are used to override
     * the typical "Yes" and "No" text.
     *
     * @param boolean $checkbox When TRUE, the widget will be
     *                          constructed as a checkbox,
     *                          otherwise it will be a set of
     *                          Yes/No radio buttons (OPTIONAL;
     *                          default is TRUE (checkbox)).
     *
     * @return string html formated boolean widget
     *
     */
    function createWidget_Boolean($checkbox=TRUE) {
        /* Do the whole current value thing. */
        if ($this->value != SMPREF_NO) {
            $yes_chk = ' checked="checked"';
            $no_chk = '';
        } else {
            $yes_chk = '';
            $no_chk = ' checked="checked"';
        }

        // checkbox...
        //
        if ($checkbox) {
            $result = '<input type="checkbox" name="new_' . $this->name
                    . '" id="new_' . $this->name . '" value="' . SMPREF_YES
                    . "\" $yes_chk " . $this->script . ' />&nbsp;'
                    . '<label for="new_' . $this->name . '">' 
                    . htmlspecialchars($this->trailing_text) . '</label>';
        }

        // radio buttons...
        //
        else {

            /* Build the yes choice. */
            $yes_option = '<input type="radio" name="new_' . $this->name 
                        . '" id="new_' . $this->name . '_yes"'
                        . ' value="' . SMPREF_YES . "\"$yes_chk $this->script />&nbsp;"
                        . '<label for="new_' . $this->name . '_yes">' . (!empty($this->yes_text) ? htmlspecialchars($this->yes_text) : _("Yes")) . '</label>';

            /* Build the no choice. */
            $no_option = '<input type="radio" name="new_' . $this->name
                       . '" id="new_' . $this->name . '_no"'
                       . ' value="' . SMPREF_NO . "\"$no_chk $this->script />&nbsp;"
                       . '<label for="new_' . $this->name . '_no">' . (!empty($this->no_text) ? htmlspecialchars($this->no_text) : _("No")) . '</label>';
    
            /* Build the combined "boolean widget". */
            $result = "$yes_option&nbsp;&nbsp;&nbsp;&nbsp;$no_option";

        }

        return ($result);
    }

    function createWidget_Hidden() {
        $result = '<input type="hidden" name="new_' . $this->name
                . '" value="' . htmlspecialchars($this->value)
                . '" ' . $this->script . ' />';
        return ($result);
    }

    function createWidget_Comment() {
        $result = $this->comment;
        return ($result);
    }

    /**
     * Creates an edit list
     *
     * Note that multiple layout types are supported for this widget.
     * $this->layout_type must be one of the SMOPT_EDIT_LIST_LAYOUT_*
     * constants.
     *
     * @return string html formated list of edit fields and
     *                their associated controls
     */
    function createWidget_EditList() {

        switch ($this->size) {
            case SMOPT_SIZE_TINY:
                $height = 3;
                break;
            case SMOPT_SIZE_SMALL:
                $height = 8;
                break;
            case SMOPT_SIZE_MEDIUM:
                $height = 15;
                break;
            case SMOPT_SIZE_LARGE:
                $height = 25;
                break;
            case SMOPT_SIZE_HUGE:
                $height = 40;
                break;
            case SMOPT_SIZE_NORMAL:
            default:
                $height = 5;
        }


        // ensure correct format of current value(s)
        //
        if (empty($this->possible_values)) $this->possible_values = array();
        if (!is_array($this->possible_values)) $this->possible_values = array($this->possible_values);


        global $javascript_on, $color;

        switch ($this->layout_type) {
            case SMOPT_EDIT_LIST_LAYOUT_SELECT:
                $result = '';
                if ($this->use_add_widget)
                    $result .= _("Add") . '&nbsp;<input name="add_' . $this->name 
                             . '" size="38" /><br />';
                $result .= '<select name="new_' . $this->name
                    . '[]" multiple="multiple" size="' . $height . '"'
                    . ($javascript_on ? ' onchange="if (typeof(window.addinput_' . $this->name . ') == \'undefined\') { var f = document.forms.length; var i = 0; var pos = -1; while( pos == -1 && i < f ) { var e = document.forms[i].elements.length; var j = 0; while( pos == -1 && j < e ) { if ( document.forms[i].elements[j].type == \'text\' && document.forms[i].elements[j].name == \'add_' . $this->name . '\' ) { pos = j; } j++; } i++; } if( pos >= 0 ) { window.addinput_' . $this->name . ' = document.forms[i-1].elements[pos]; } } for (x = 0; x < this.length; x++) { if (this.options[x].selected) { window.addinput_' . $this->name . '.value = this.options[x].text; break; } }"' : '')
                    . ' ' . $this->script . ">\n";


                if (is_array($this->value))
                    $selected = $this->value;
                else
                    $selected = array($this->value);


                // Add each possible value to the select list.
                //
                foreach ($this->possible_values as $value) {

                    // Start the next new option string.
                    //
                    $result .= '<option value="' . htmlspecialchars($value) . '"';

                    // having a selected item in the edit list doesn't have
                    // any meaning, but maybe someone will think of a way to
                    // use it, so we might as well put the code in
                    //
                    foreach ($selected as $default) {
                        if ((string)$default == (string)$value) {
                            $result .= ' selected="selected"';
                            break;
                        }
                    }

                    // Add the display value to our option string.
                    //
                    $result .= '>' . htmlspecialchars($value) . "</option>\n";

                }

                $result .= '</select>';
                if (!empty($this->possible_values) && $this->use_delete_widget)
                    $result .= '<br /><input type="checkbox" name="delete_' 
                             . $this->name . '" id="delete_' . $this->name 
                             . '" value="1" />&nbsp;<label for="delete_'
                             . $this->name . '">' . _("Delete Selected")
                             . '</label>';

                break;



            case SMOPT_EDIT_LIST_LAYOUT_LIST:
                $result = '<table width="80%" cellpadding="1" cellspacing="0" border="0" bgcolor="'
                        . $color[0] . '"><tr><td>';
                if ($this->use_add_widget)
                    $result .= _("Add") . '&nbsp;<input name="add_' . $this->name 
                             . '" size="38" /><br />';
                $result .= '<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="' . $color[5] . '">';

                $bgcolor = 4;
                if (!isset($color[12]))
                    $color[12] = '#EAEAEA';
                $index = 0;

                foreach ($this->possible_values as $key => $value) {

                    if ($bgcolor == 4) $bgcolor = 12;
                    else $bgcolor = 4;

                    $result .= '<tr bgcolor="' . $color[$bgcolor] . '">'
                             . '<td width="1%"><input type="checkbox" name="new_' . $this->name . '[' . ($index++) . ']" id="' . $this->name . '_list_item_' . $key . '" value="' . htmlspecialchars($value) . '"></td>'
                             . '<td><label for="' . $this->name . '_list_item_' . $key . '">' . htmlspecialchars($value) . '</label></td>'
                             . "</tr>\n";

                }

                $result .= '</table>';

                if (!empty($this->possible_values) && $this->use_delete_widget)
                    $result .= '<input type="checkbox" name="delete_' 
                        . $this->name . '" id="delete_' . $this->name 
                        . '" value="1" />&nbsp;<label for="delete_' . $this->name . '">'
                        . _("Delete Selected") . '</label>';

                $result .= '</td></tr></table>';

                break;


            default:
                $result = '<font color="' . $color[2] . '">'
                        . sprintf(_("Edit List Layout Type '%s' Not Found"), $this->layout_type)
                        . '</font>';
        }

        return $result;

    }

    /**
     * Creates a submit button
     *
     * @return string html formated submit button widget
     *
     */
    function createWidget_Submit() {

        $result = "<input type=\"submit\" name=\"$this->name\" value=\""
                . htmlspecialchars($this->comment)
                . "\" $this->script />" 
                . htmlspecialchars($this->trailing_text) . "\n";

        return $result;

    }

    function save() {
        $function = $this->save_function;
        $function($this);
    }

    function changed() {

        // edit lists have a lot going on, so we'll always process them
        //
        if ($this->type == SMOPT_TYPE_EDIT_LIST) return TRUE;

        return ($this->value != $this->new_value);
    }
}

function save_option($option) {

    // Can't save the pref if we don't have the username
    //
    if ( !sqgetGlobalVar('username', $username, SQ_SESSION ) ) {
        return;
    }

    // if the widget is a selection list, make sure the new
    // value is actually in the selection list and is not an
    // injection attack
    //
    if ($option->type == SMOPT_TYPE_STRLIST
     && !array_key_exists($option->new_value, $option->possible_values))
        return;


    // all other widgets except TEXTAREAs should never be allowed to have newlines
    //
    else if ($option->type != SMOPT_TYPE_TEXTAREA)
        $option->new_value = str_replace(array("\r", "\n"), '', $option->new_value);


    global $data_dir;

    // edit lists: first add new elements to list, then
    // remove any selected ones (note that we must add
    // before deleting because the javascript that populates
    // the "add" textbox when selecting items in the list
    // (for deletion))
    //
    if ($option->type == SMOPT_TYPE_EDIT_LIST) {

        if (empty($option->possible_values)) $option->possible_values = array();
        if (!is_array($option->possible_values)) $option->possible_values = array($option->possible_values);

        // add element if given
        //
        if ((isset($option->use_add_widget) && $option->use_add_widget)
         && sqGetGlobalVar('add_' . $option->name, $new_element, SQ_POST)) {
            $new_element = trim($new_element);
            if (!empty($new_element)
             && !in_array($new_element, $option->possible_values))
                $option->possible_values[] = $new_element;
        }

        // delete selected elements if needed
        //
        if ((isset($option->use_delete_widget) && $option->use_delete_widget)
         && is_array($option->new_value)
         && sqGetGlobalVar('delete_' . $option->name, $ignore, SQ_POST))
            $option->possible_values = array_diff($option->possible_values, $option->new_value);

        // save full list (stored in "possible_values")
        //
        setPref($data_dir, $username, $option->name, serialize($option->possible_values));

    // Certain option types need to be serialized because
    // they are not scalar
    //
    } else if ($option->is_multiple_valued())
        setPref($data_dir, $username, $option->name, serialize($option->new_value));

    // Checkboxes, when unchecked, don't submit anything in
    // the POST, so set to SMPREF_OFF if not found
    //
    else if (($option->type == SMOPT_TYPE_BOOLEAN
           || $option->type == SMOPT_TYPE_BOOLEAN_CHECKBOX)
          && empty($option->new_value)) 
        setPref($data_dir, $username, $option->name, SMPREF_OFF);

    else
        setPref($data_dir, $username, $option->name, $option->new_value);


    // if a checkbox or multi select is zeroed/cleared out, it
    // needs to have an empty value pushed into its "new_value" slot
    //
    if (($option->type == SMOPT_TYPE_STRLIST_MULTI
      || $option->type == SMOPT_TYPE_BOOLEAN_CHECKBOX)
     && is_null($option->new_value))
        $option->new_value = '';

}

function save_option_noop($option) {
    /* Do nothing here... */
}

function create_optpage_element($optpage) {
    return create_hidden_element('optpage', $optpage);
}

function create_optmode_element($optmode) {
    return create_hidden_element('optmode', $optmode);
}

function create_hidden_element($name, $value) {
    $result = '<input type="hidden" '
            . 'name="' . $name . '" '
            . 'value="' . htmlspecialchars($value) . '" />';
    return ($result);
}

function create_option_groups($optgrps, $optvals) {
    /* Build a simple array with which to start. */
    $result = array();

    /* Create option group for each option group name. */
    foreach ($optgrps as $grpkey => $grpname) {
        $result[$grpkey] = array();
        $result[$grpkey]['name'] = $grpname;
        $result[$grpkey]['options'] = array();
    }

     /* Create a new SquirrelOption for each set of option values. */
    foreach ($optvals as $grpkey => $grpopts) {
        foreach ($grpopts as $optset) {
            /* Create a new option with all values given. */
            $next_option = new SquirrelOption(
                $optset,
                $optset['name'],
                $optset['caption'],
                $optset['type'],
                (isset($optset['refresh']) ? $optset['refresh'] : SMOPT_REFRESH_NONE),
                (isset($optset['initial_value']) ? $optset['initial_value'] : ''),
                (isset($optset['posvals']) ? $optset['posvals'] : ''),
                (isset($optset['htmlencoded']) ? $optset['htmlencoded'] : false)
                );

            /* If provided, set if the caption is allowed to wrap for this option. */
            if (isset($optset['caption_wrap'])) {
                $next_option->setCaptionWrap($optset['caption_wrap']);
            }

            /* If provided, set the size for this option. */
            if (isset($optset['size'])) {
                $next_option->setSize($optset['size']);
            }

            /* If provided, set the trailing_text for this option. */
            if (isset($optset['trailing_text'])) {
                $next_option->setTrailingText($optset['trailing_text']);
            }

            /* If provided, set the yes_text for this option. */
            if (isset($optset['yes_text'])) {
                $next_option->setYesText($optset['yes_text']);
            }

            /* If provided, set the no_text for this option. */
            if (isset($optset['no_text'])) {
                $next_option->setNoText($optset['no_text']);
            }

            /* If provided, set the use_add_widget value for this option. */
            if (isset($optset['use_add_widget'])) {
                $next_option->setUseAddWidget($optset['use_add_widget']);
            }

            /* If provided, set the use_delete_widget value for this option. */
            if (isset($optset['use_delete_widget'])) {
                $next_option->setUseDeleteWidget($optset['use_delete_widget']);
            }

            /* If provided, set the layout type for this option. */
            if (isset($optset['layout_type'])) {
                $next_option->setLayoutType($optset['layout_type']);
            }

            /* If provided, set the comment for this option. */
            if (isset($optset['comment'])) {
                $next_option->setComment($optset['comment']);
            }

            /* If provided, set the save function for this option. */
            if (isset($optset['save'])) {
                $next_option->setSaveFunction($optset['save']);
            }

            /* If provided, set the script for this option. */
            if (isset($optset['script'])) {
                $next_option->setScript($optset['script']);
            }

            /* If provided, set the "post script" for this option. */
            if (isset($optset['post_script'])) {
                $next_option->setPostScript($optset['post_script']);
            }

            /* Add this option to the option array. */
            $result[$grpkey]['options'][] = $next_option;
        }
    }

    /* Return our resulting array. */
    return ($result);
}

function print_option_groups($option_groups) {
    /* Print each option group. */
    foreach ($option_groups as $next_optgrp) {
        /* If it is not blank, print the name for this option group. */
        if ($next_optgrp['name'] != '') {
            echo html_tag( 'tr', "\n".
                        html_tag( 'td',
                            '<b>' . $next_optgrp['name'] . '</b>' ,
                        'center' ,'', 'valign="middle" colspan="2" nowrap' )
                    ) ."\n";
        }

        /* Print each option in this option group. */
        $hidden_options = '';
        foreach ($next_optgrp['options'] as $option) {
            if ($option->type != SMOPT_TYPE_HIDDEN) {

                // although trailing_text will be a label for the checkbox,
                // make the caption a label too - some widgets won't have
                // trailing_text and having both as labels is perfectly fine
                //
                if ($option->type == SMOPT_TYPE_BOOLEAN_CHECKBOX
                 || $option->type == SMOPT_TYPE_BOOLEAN)
                    $option->caption = '<label for="new_' . $option->name . '">'
                                     . $option->caption . '</label>';

                // text area trailing text just goes under the caption
                //
                if ($option->type == SMOPT_TYPE_TEXTAREA && !empty($option->trailing_text))
                    $option->caption .= '<br /><small>' . $option->trailing_text . '</small>';

                global $color;
                //$info_bgcolor = 0;
                $info_bgcolor = 4;
                $info_width = 80;
                if ($option->type == SMOPT_TYPE_INFO)
                    echo html_tag('tr', "\n" . html_tag('td', "\n" . html_tag('table', "\n" . html_tag('tr', "\n" . html_tag('td', "\n" . $option->createHTMLWidget())), '', $color[$info_bgcolor], 'width="' . $info_width . '%"'), 'center' ,'', 'colspan="2" valign="middle"')) ."\n";
                else
                    echo html_tag( 'tr', "\n".
                               html_tag( 'td', $option->caption . (!empty($option->caption) ? ':' : ''), 'right' ,'', 'valign="middle"' . ($option->caption_wrap ? '' : ' style="white-space:nowrap"') ) .
                               html_tag( 'td', $option->createHTMLWidget(), 'left' )
                           ) ."\n";
            } else {
                $hidden_options .= $option->createHTMLWidget();
            }
        }

        /* Print an empty row after this option group. */
        echo html_tag( 'tr',
                   html_tag( 'td', '&nbsp;' . $hidden_options, 'left', '', 'colspan="2"' )
                ) . "\n";
    }
}

function OptionSubmit( $name ) {
        echo html_tag( 'tr',
                   html_tag( 'td', '<input type="submit" value="' . _("Submit") . '" name="' . $name . '" />&nbsp;&nbsp;&nbsp;&nbsp;', 'right', '', 'colspan="2"' )
                ) . "\n";
}

