<?php
/**
 * sqspell_config.php -- SquirrelSpell Configuration file.
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @version $Id: sqspell_config.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage squirrelspell
 */

require_once(SM_PATH . 'functions/prefs.php');

/* Just for poor wretched souls with E_ALL. :) */
global $data_dir;

sqgetGlobalVar('username', $username, SQ_SESSION);

/**
 * Example:
 *
 * $SQSPELL_APP = array( 'English' => 'ispell -a',
 *                     'Spanish' => 'ispell -d spanish -a' );
 * You can replace ispell with aspell keeping the same commandline:
 * $SQSPELL_APP = array( 'English' => 'aspell -a',
 *                     'Spanish' => 'aspell -d spanish -a' );
 */
$SQSPELL_APP = array('English' => 'ispell -a',
			'Spanish' => 'ispell -d spanish -a');
$SQSPELL_APP_DEFAULT = 'English';
$SQSPELL_WORDS_FILE = 
   getHashedFile($username, $data_dir, "$username.words");

$SQSPELL_EREG = 'ereg';

?>