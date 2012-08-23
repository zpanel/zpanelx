<?php

/**
 * help.php
 *
 * Displays help for the user
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: help.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the help page */
define('PAGE_NAME', 'help');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/display_messages.php');

displayPageHeader($color, 'None' );

$helpdir[0] = 'basic.hlp';
$helpdir[1] = 'main_folder.hlp';
$helpdir[2] = 'read_mail.hlp';
$helpdir[3] = 'compose.hlp';
$helpdir[4] = 'addresses.hlp';
$helpdir[5] = 'folders.hlp';
$helpdir[6] = 'options.hlp';
$helpdir[7] = 'search.hlp';
$helpdir[8] = 'FAQ.hlp';

/****************[ HELP FUNCTIONS ]********************/

/**
 * parses through and gets the information from the different documents.
 * this returns one section at a time.  You must keep track of the position
 * so that it knows where to start to look for the next section.
 */

function get_info($doc, $pos) {
    $ary = array(0,0,0);

    $cntdoc = count($doc);

    for ($n=$pos; $n < $cntdoc; $n++) {
        if (trim(strtolower($doc[$n])) == '<chapter>'
            || trim(strtolower($doc[$n])) == '<section>') {
            for ($n++; $n < $cntdoc
                 && (trim(strtolower($doc[$n])) != '</section>')
                 && (trim(strtolower($doc[$n])) != '</chapter>'); $n++) {
                if (trim(strtolower($doc[$n])) == '<title>') {
                    $n++;
                    $ary[0] = trim($doc[$n]);
                }
                if (trim(strtolower($doc[$n])) == '<description>') {
                    $ary[1] = '';
                    for ($n++;$n < $cntdoc
                         && (trim(strtolower($doc[$n])) != '</description>');
                         $n++) {
                        $ary[1] .= $doc[$n];
                    }
                }
                if (trim(strtolower($doc[$n])) == '<summary>') {
                    $ary[2] = '';
                    for ($n++; $n < $cntdoc
                         && (trim(strtolower($doc[$n])) != '</summary>');
                         $n++) {
                        $ary[2] .= $doc[$n];
                    }
                }
            }
            if (isset($ary)) {
                $ary[3] = $n;
            } else {
                $ary[0] = _("ERROR: Help files are not in the right format!");
                $ary[1] = $ary[0];
                $ary[2] = $ary[0];
            }
            return( $ary );
        } else if (!trim(strtolower($doc[$n]))) {
            $ary[0] = '';
            $ary[1] = '';
            $ary[2] = '';
            $ary[3] = $n;
        }
    }
    $ary[0] = _("ERROR: Help files are not in the right format!");
    $ary[1] = $ary[0];
    $ary[2] = $ary[0];
    $ary[3] = $n;
    return( $ary );
}

/**************[ END HELP FUNCTIONS ]******************/

echo html_tag( 'table',
        html_tag( 'tr',
            html_tag( 'td','<div style="text-align: center;"><b>' . _("Help") .'</b></div>', 'center', $color[0] )
        ) ,
    'center', '', 'width="95%" cellpadding="1" cellspacing="2" border="0"' );

do_hook('help_top');

echo html_tag( 'table', '', 'center', '', 'width="90%" cellpadding="0" cellspacing="10" border="0"' ) .
        html_tag( 'tr' ) .
            html_tag( 'td' );

if (!isset($squirrelmail_language)) {
    $squirrelmail_language = 'en_US';
}

if (file_exists("../help/$squirrelmail_language")) {
    $user_language = $squirrelmail_language;
} else if (file_exists('../help/en_US')) {
    error_box(_("Help is not available in the selected language. It will be displayed in English instead."), $color);
    echo '<br />';
    $user_language = 'en_US';
} else {
    error_box( _("Help is not available. Please contact your system administrator for assistance."), $color );
    echo '</td></tr></table>';
    exit;
}


/* take the chapternumber from the GET-vars,
 * else see if we can get a relevant chapter from the referer */
$chapter = 0;

if ( sqgetGlobalVar('chapter', $temp, SQ_GET) ) {
    $chapter = (int) $temp;
} elseif ( sqgetGlobalVar('HTTP_REFERER', $temp, SQ_SERVER) ) {
    $ref = strtolower($temp);

    $contexts = array ( 'src/compose' => 4, 'src/addr' => 5,
        'src/folders' => 6, 'src/options' => 7, 'src/right_main' => 2,
        'src/read_body' => 3, 'src/search' => 8 );

    foreach($contexts as $path => $chap) {
        if(strpos($ref, $path)) {
            $chapter = $chap;
            break;
        }
    }
}

if ( $chapter == 0 || !isset( $helpdir[$chapter-1] ) ) {
    // Initialise the needed variables.
    $toc = array();

    // Get the chapter numbers, title and decriptions.
    for ($i=0, $cnt = count($helpdir); $i < $cnt; $i++) {
        if (file_exists("../help/$user_language/$helpdir[$i]")) {
            // First try the selected language.
            $doc = file("../help/$user_language/$helpdir[$i]");
            $help_info = get_info($doc, 0);
            $toc[] = array($i+1, $help_info[0], $help_info[2]);
        } elseif (file_exists("../help/en_US/$helpdir[$i]")) {
            // If the selected language can't be found, try English.
            $doc = file("../help/en_US/$helpdir[$i]");
            $help_info = get_info($doc, 0);
            $toc[] = array($i+1, $help_info[0],
                    _("This chapter is not available in the selected language. It will be displayed in English instead.") .
                    '<br />' . $help_info[2]);
        } else {
            // If English can't be found, the chapter went MIA.
            $toc[] = array($i+1, _("This chapter is missing"),
                    sprintf(_("For some reason, chapter %s is not available."), $i+1));
        }
    }

    // Write the TOC header
    echo html_tag( 'table', '', 'center', '', 'cellpadding="0" cellspacing="0" border="0"' ) .
         html_tag( 'tr' ) .
         html_tag( 'td' ) .
         '<div style="text-align: center;"><b>' . _("Table of Contents") . '</b></div><br />';
    echo html_tag( 'ol' );

    // Write the TOC chapters.
    // FIXME: HTML code is not compliant.
    for ($i=0, $cnt = count($toc); $i < $cnt; $i++) {
        echo '<li><a href="../src/help.php?chapter=' . $toc[$i][0]. '">' .
            $toc[$i][1] . '</a>' . html_tag( 'ul', $toc[$i][2] );
    }

    // Provide hook for external help scripts.
    do_hook('help_chapter');

    // Write the TOC footer.
    echo '</ol></td></tr></table>';
} else {
    // Initialise the needed variables.
    $display_chapter = TRUE;

    // Get the chapter.
    if (file_exists("../help/$user_language/" . $helpdir[$chapter-1])) {
        // First try the selected language.
        $doc = file("../help/$user_language/" . $helpdir[$chapter-1]);
    } elseif (file_exists("../help/en_US/" . $helpdir[$chapter-1])) {
        // If the selected language can't be found, try English.
        $doc = file("../help/en_US/" . $helpdir[$chapter-1]);
        error_box(_("This chapter is not available in the selected language. It will be displayed in English instead."), $color);
        echo '<br />';
    } else {
        // If English can't be found, the chapter went MIA.
        $display_chapter = FALSE;
    }

    // Write the chpater header.
    echo '<div style="text-align: center;"><small>';
    if ($chapter <= 1){
        echo '<font color="' . $color[9] . '">' . _("Previous")
             . '</font> | ';
    } else {
        echo '<a href="../src/help.php?chapter=' . ($chapter-1)
             . '">' . _("Previous") . '</a> | ';
    }
    echo '<a href="../src/help.php">' . _("Table of Contents") . '</a>';
    if ($chapter >= count($helpdir)){
        echo ' | <font color="' . $color[9] . '">' . _("Next") . '</font>';
    } else {
        echo ' | <a href="../src/help.php?chapter=' . ($chapter+1)
             . '">' . _("Next") . '</a>';
    }
    echo '</small></div><br />';

    // Write the chapter.
    if ($display_chapter) {
        // If there is a valid chapter, display it.
        $help_info = get_info($doc, 0);
        echo '<font size="5"><b>' . $chapter . ' - ' . $help_info[0]
            . '</b></font><br /><br />';

        if (isset($help_info[1]) && $help_info[1]) {
            echo $help_info[1];
        } else {
            echo html_tag( 'p', $help_info[2], 'left' );
        }

        $section = 0;
        for ($n = $help_info[3], $cnt = count($doc); $n < $cnt; $n++) {
            $section++;
            $help_info = get_info($doc, $n);
            echo "<b>$chapter.$section - $help_info[0]</b>" .
                $help_info[1];
            $n = $help_info[3];
        }

        echo '<br /><div style="text-align: center;"><a href="#pagetop">' . _("Top") . '</a></div>';
    } else {
        // If the help file went MIA, display an error message.
        error_box(sprintf(_("For some reason, chapter %s is not available."), $chapter), $color);
    }
}

do_hook('help_bottom');

echo html_tag( 'tr',
            html_tag( 'td', '&nbsp;', 'left', $color[0] )
        );

?>
</table></body></html>