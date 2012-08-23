<?php
// +----------------------------------------------------------------+
// | spell.php														|
// +----------------------------------------------------------------+
// | Function: Spell check an email message							|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');
session_start();

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

$atmail->status = $auth->getuser($atmail->SessionID);

$atmail->username = $auth->username;
$atmail->pop3host = $auth->pop3host;

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 ) {
    header("Content-type: text/xml; charset: utf-8");
    echo "<Error>Athentication Error</Error>";
    $atmail->end();
}

if ( $atmail->status == 2 ) {
    header("Content-type: text/xml; charset: utf-8");
    echo "<Error>Session errror</Error>";
    $atmail->end();
}

// Load the account preferences
$atmail->loadprefs();

ob_start();
// Try to use the aspell dictionary for the user's current language
include_once('libs/Atmail/AspellLanguageCodes.php');
$dict = '';
if ($pref["aspell_{$atmail->Language}"])
    $dict = $aspellLanguageCodes[$atmail->Language];
ob_end_clean();
//$dict = 'en';

if (empty($dict))
{
    $lang = ucfirst($atmail->Language);
    header("Content-type: text/xml; charset: utf-8");
    echo "<Error>Spellcheck not available for $lang language</Error>\n";
	$atmail->end();
}

// only bother requiring if we pass authentication
require_once('spellChecker.php');

$atmail->httpheaders();

$spellChecker = new spellChecker($dict, "$atmail->username@$atmail->pop3host", $pref['use_php_pspell']);

// Add word to our personal dict file
if ( $_REQUEST['add'] )
{
    $personal = $_REQUEST['replace'] ? $_REQUEST['replace'] : $_REQUEST['wordreplace'];

	// Insert the entry into the database
    $spellChecker->addWord($atmail->Account, $personal);
}

if ( $_REQUEST['ignore'] || $_REQUEST['change'] && $_REQUEST['wordreplace'] )
{
    // Ignore the word
    $spellChecker->ignoreWord($_REQUEST['wordreplace']);
}


// spell check the email

$_REQUEST['emailmessage'] = str_replace(array('<br>', '<BR>', '<br/>', '<BR/>', '</p>', '</P>'), "\n", $_REQUEST['emailmessage']);

// Remove any html entities and tags
$_REQUEST['emailmessage'] = preg_replace('/&\w+;/', '', $_REQUEST['emailmessage']);
$_REQUEST['emailmessage'] = strip_tags($_REQUEST['emailmessage']);

// Remove punctuation such as , ; :
//$_REQUEST['emailmessage'] = preg_replace('/[^a-zA-Z\-]+/', ' ', $_REQUEST['emailmessage']);

foreach (explode("\n", $_REQUEST['emailmessage']) as $line)
{
    $words = array_unique(preg_split('/\s+/', $line));

    foreach ($words as $word)
	{
		if (preg_match('/[a-zA-Z]+/', $word))
			$spellChecker->check($word);
	}
}

if ($spellChecker->haveErrors())
{
    $result = $spellChecker->getSuggestions();

    if (is_array($result)) {
		$var['atmailstyle'] = $atmail->parse("html/$atmail->Language/simple/atmailstyle.css" );
        echo  $atmail->parse("html/$atmail->Language/$atmail->LoginType/spellcheck.html", $result, $var );
    } else {
        header("Content-type: text/xml; charset: utf-8");
        echo $result;
    }
}

$spellChecker->close();
$atmail->end();

?>
