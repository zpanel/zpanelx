<?php

/**
 * addrbook_popup.php
 *
 * Frameset for the JavaScript version of the address book.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: addrbook_popup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage addressbook
 */

/** This is the addrbook_popup page */
define('PAGE_NAME', 'addrbook_popup');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/** SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/addressbook.php');
   
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">

<html>
    <head>
        <meta name="robots" content="noindex,nofollow">
        <title><?php echo "$org_title: " . _("Address Book"); ?></title>
    </head>
    <frameset rows="60,*" border="0">
        <frame name="abookmain"
               marginwidth="0"
               scrolling="no"
               border="0"
               src="addrbook_search.php?show=form" />
        <frame name="abookres"
               marginwidth="0"
               border="0"
               src="addrbook_search.php?show=blank" />
    </frameset>
</html>