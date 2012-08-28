<?php

/**
 * addrbook_search.php
 *
 * Handle addressbook searching in the popup window.
 *
 * NOTE: A lot of this code is similar to the code in
 *       addrbook_search_html.html -- If you change one,
 *       change the other one too!
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: addrbook_search.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage addressbook
 */

/** This is the addrbook_search page */
define('PAGE_NAME', 'addrbook_search');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/** SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/forms.php');
require_once(SM_PATH . 'functions/addressbook.php');

/** lets get the global vars we may need */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('base_uri',  $base_uri,      SQ_SESSION);

sqgetGlobalVar('show' ,   $show);
sqgetGlobalVar('query',   $query,   SQ_POST);
sqgetGlobalVar('listall', $listall, SQ_POST);
sqgetGlobalVar('backend', $backend, SQ_POST);

/**
 * Function to include JavaScript code
 * @return void
 */
function insert_javascript() {
    ?>
    <script language="Javascript"><!--

    function to_and_close($addr) {
        to_address($addr);
        parent.close();
    }

    function to_address($addr) {
        var prefix    = "";
        var pwintype = typeof parent.opener.document.compose;

        $addr = $addr.replace(/ {1,35}$/, "");

        if (pwintype != "undefined") {
            if (parent.opener.document.compose.send_to.value) {
                prefix = ", ";
                parent.opener.document.compose.send_to.value =
                    parent.opener.document.compose.send_to.value + ", " + $addr;
            } else {
                parent.opener.document.compose.send_to.value = $addr;
            }
        }
    }

    function cc_address($addr) {
        var prefix    = "";
        var pwintype = typeof parent.opener.document.compose;

        $addr = $addr.replace(/ {1,35}$/, "");

        if (pwintype != "undefined") {
            if (parent.opener.document.compose.send_to_cc.value) {
                prefix = ", ";
                parent.opener.document.compose.send_to_cc.value =
                    parent.opener.document.compose.send_to_cc.value + ", " + $addr;
            } else {
                parent.opener.document.compose.send_to_cc.value = $addr;
            }
        }
    }

    function bcc_address($addr) {
        var prefix    = "";
        var pwintype = typeof parent.opener.document.compose;

        $addr = $addr.replace(/ {1,35}$/, "");

        if (pwintype != "undefined") {
            if (parent.opener.document.compose.send_to_bcc.value) {
                prefix = ", ";
                parent.opener.document.compose.send_to_bcc.value =
                    parent.opener.document.compose.send_to_bcc.value + ", " + $addr;
            } else {
                parent.opener.document.compose.send_to_bcc.value = $addr;
            }
        }
    }

// --></script>
<?php
} /* End of included JavaScript */


/**
 * List search results
 * @param array $res Array of search results
 * @param bool $includesource [Default=true]
 * @return void
 */
function display_result($res, $includesource = true) {
    global $color;
        
    if(sizeof($res) <= 0) return;
        
    insert_javascript();
        
    $line = 0;
    echo html_tag( 'table', '', 'center', '', 'border="0" width="98%"' ) .
    html_tag( 'tr', '', '', $color[9] ) .
    html_tag( 'th', '&nbsp;', 'left' ) .
    html_tag( 'th', '&nbsp;' . _("Name"), 'left' ) .
    html_tag( 'th', '&nbsp;' . _("E-mail"), 'left' ) .
    html_tag( 'th', '&nbsp;' . _("Info"), 'left' );

    if ($includesource) {
        echo html_tag( 'th', '&nbsp;' . _("Source"), 'left', '', 'width="10%"' );
    }    
    echo "</tr>\n";
    
    while (list($undef, $row) = each($res)) {
        $email = htmlspecialchars(addcslashes(AddressBook::full_address($row), "'"), ENT_QUOTES);
        if ($line % 2) { 
            $tr_bgcolor = $color[12];
        } else {
            $tr_bgcolor = $color[4];
        }
        echo html_tag( 'tr', '', '', $tr_bgcolor, 'nowrap' ) .
        html_tag( 'td',
             '<small><a href="javascript:to_address(' . 
                                       "'" . $email . "');\">"._("To")."</a> | " .
             '<a href="javascript:cc_address(' . 
                                       "'" . $email . "');\">"._("Cc")."</a> | " .
             '<a href="javascript:bcc_address(' . 
                                 "'" . $email . "');\">"._("Bcc")."</a></small>",
        'center', '', 'valign="top" width="5%" nowrap' ) .
        html_tag( 'td', '&nbsp;' . htmlspecialchars($row['name']), 'left', '', 'valign="top" nowrap' ) .
        html_tag( 'td', '&nbsp;' .
             '<a href="javascript:to_and_close(' .
                 "'" . $email . "');\">" . htmlspecialchars($row['email']) . '</a>'
        , 'left', '', 'valign="top"' ) .
        html_tag( 'td', htmlspecialchars($row['label']), 'left', '', 'valign="top" nowrap' );
        if ($includesource) {
            echo html_tag( 'td', '&nbsp;' . $row['source'], 'left', '', 'valign="top" nowrap' );
        }

        echo "</tr>\n";
        $line++;
    }
    echo '</table>';
}

/* ================= End of functions ================= */
    
    
displayHtmlHeader();
    
/* Initialize vars */
if (!isset($query)) { $query = ''; }
if (!isset($show))  { $show  = ''; }
if (!isset($backend)) { $backend = ''; }

/* Choose correct colors for top and bottom frame */
if ($show == 'form' && !isset($listall)) {
    echo '<body text="' . $color[6] . '" bgcolor="' . $color[3] . '" ' .
               'link="' . $color[6] . '" vlink="'   . $color[6] . '" ' .
                                        'alink="'   . $color[6] . '" ' .
         'OnLoad="document.sform.query.focus();">';
} else {
    echo '<body text="' . $color[8] . '" bgcolor="' . $color[4] . '" ' .
               'link="' . $color[7] . '" vlink="'   . $color[7] . '" ' .
                                        'alink="'   . $color[7] . "\">\n";
}

/* Empty search */
if (empty($query) && empty($show) && empty($listall)) {
    echo html_tag( 'p', '<br />' .
                      _("No persons matching your search were found"),
            'center' ) .
          "\n</body></html>\n";
    exit;
}

/* Initialize addressbook */
$abook = addressbook_init();

/* Create search form */
if ($show == 'form' && empty($listall)) {
    echo '<form name="sform" target="abookres" action="addrbook_search.php'. 
            '" method="post">' . "\n" .
         html_tag( 'table', '', '', '', 'border="0" width="100%" height="100%"' ) .
         html_tag( 'tr' ) .
         html_tag( 'td', '  <strong>' . _("Search for") . "</strong>\n", 'left', '', 'nowrap valign="middle" width="10%"' ) .
         html_tag( 'td', '', 'left', '', '' ) .
         addInput('query', $query, 28);

    /* List all backends to allow the user to choose where to search */
    if ($abook->numbackends > 1) {
        echo '<strong>' . _("in") . '</strong>&nbsp;'."\n";
        $selopts = array();
        $selopts['-1'] = _("All address books");

        $ret = $abook->get_backend_list();
        while (list($undef,$v) = each($ret)) {
            $selopts[$v->bnum] = $v->sname;
        }
        echo addSelect('backend', $selopts, '-1', TRUE);
    } else {
        echo addHidden('backend', '-1');
    }
        
    echo '</td></tr>' .
    html_tag( 'tr',
                    html_tag( 'td', '', 'left' ) .
                    html_tag( 'td',
                            '<input type="submit" value="' . _("Search") . '" name="show" />' .
                            '&nbsp;|&nbsp;<input type="submit" value="' . _("List all") .
                            '" name="listall" />' . "\n" .
                            '&nbsp;|&nbsp;<input type="button" value="' . _("Close") .
                            '" onclick="parent.close();" />' . "\n" ,
                    'left' )
            ) .
         '</table></form>' . "\n";
} else {

    /* Show personal addressbook */
    if ($show == 'blank' && empty($listall)) {

        if($backend != -1 || $show == 'blank') {
            if ($show == 'blank') {
                $backend = $abook->localbackend;
            }
            $res = $abook->list_addr($backend);

            if(is_array($res)) {
                usort($res,'alistcmp');
                display_result($res, false);
            } else {
                echo html_tag( 'p', '<strong>' .
                                 sprintf(_("Unable to list addresses from %s"),
                                     $abook->backends[$backend]->sname) . '</strong>' ,
                       'center' ) . "\n";
            }
        } else {
            $res = $abook->list_addr();
            usort($res,'alistcmp');
            display_result($res, true);
        }

    } else {
        if( !empty( $listall ) ){
          $query = '*';
        }

        /* Do the search */
        if (!empty($query)) {
    
            if($backend == -1) {
                $res = $abook->s_search($query);
            } else {
                $res = $abook->s_search($query, $backend);
            }
        
            if (!is_array($res)) {
                echo html_tag( 'p', '<b><br />' .
                                 _("Your search failed with the following error(s)") .
                                 ':<br />' . $abook->error . "</b>\n" ,
                       'center' ) .
                "\n</body></html>\n";
                exit;
            }
        
            if (sizeof($res) == 0) {
                echo html_tag( 'p', '<br /><b>' .
                                 _("No persons matching your search were found") . "</b>\n" ,
                       'center' ) .
                "\n</body></html>\n";
                exit;
            }
        
            display_result($res);
        }
    }
   
}
?>
</body></html>