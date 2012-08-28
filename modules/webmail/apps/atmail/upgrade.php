<?php

// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
require_once('header.php');

header('Content-type: text/html; charset=utf-8');

require_once('Config.php');
require_once('Global_Base.php'); // Backwards compatability, e.g FC2, no file_put_contents if sqldo statement errors
require_once('SQL.php');

$db = new SQL();

$newversion = '1.03';

if (preg_match('/(\d+\.\d+)/', $pref['version'], $m)) {
    $version = $m[1];
    if ($version == $newversion) {
        die("Already at version $version");
    }
} else {
    die('Could not determine current version');
}

$alpha = range('a', 'z');
$alpha[] = 'other';

if ($version == '1.0') {
    $version = '1.01';
}

if ($version == '1.01') {

    // Add UseSSL column
    foreach ($alpha as $a) {
        $db->sqldo("alter table UserSettings_$a add UseSSL tinyint(1) default 0");
    }

    $db->sqldo("alter table Accounts add UseSSL tinyint(1) default 0");

    if (extension_loaded('openssl')) {
        $pref['mail_type_ssl'] = 'allow';
    } else {
        $pref['mail_type_ssl'] = 'deny';
    }

    $version = '1.02';
}

if ($version == '1.02') {

    if (empty($pref['aspell_path']) || !is_executable($pref['aspell_path'])) {
        if (is_executable('/usr/bin/aspell')) {
            $pref['aspell_path'] = '/usr/bin/aspell';
        } elseif (is_executable('/usr/local/bin/aspell')) {
            $pref['aspell_path'] = '/usr/local/bin/aspell';
        }
    }

    $pref['addressbook_ldap_entries'] = '0';
    $pref['autocomplete_ldap_entries'] = '0';
    $pref['imap_sort_extension'] = '1';
    $pref['imap_sort_charset'] = 'us-ascii';
    $pref['quota_bar'] = '1';
    $pref['quota_alert'] = '1';
    $pref['quota_alert_over'] = '90';
    $pref['quota_alert_html'] = '<p style="font-weight:bold;text-align:center;font-size:24px;">
    YOUR QUOTA IS NEARLY EXHAUSTED - PLEASE DELETE UNNECESSARY ITEMS</p>
    <p style="text-align:center;font-size:18px;">
    You will be unable to receive or send any messages once you have exhausted your quota.</p>';

    $version = '1.03';
}

if ($version == '1.03') {
	$pref['logo_small_alt'] = 'Atmail Open';
	$pref['footer_msg'] = '<hr />Message sent via Atmail Open - http://atmail.org/';
	
	$version = '1.04';
}

$pref['version'] = "Atmail Open $version";
writeconf();

$msg = <<<_EOF
<h1>Upgrade to Atmail Open $newversion - Complete</h1>
<p>Reload the <a href='index.php'>index page</a> of Atmail Open to continue using the latest release.</p>
<p>View the <a href="http://support.atmail.org/changelog.html">changelog</a> for details on the new Atmail Open $newversion release.</p>
<p>This script (upgrade.php) will be deleted from your server. A new version will be supplied with the next release.</p>
_EOF;

unlink(__FILE__);
?>

<HTML>
<BODY>
<HEAD>
<STYLE>
BODY { background: #ffffff; width: 600px;}
H1 { font-family: Verdana, arial; font-size: 16px;}
P { font-family: Verdana, arial; font-size: 12px;}
pre {
    overflow: auto;
    padding: 1em;
    border: 1px solid #c0d8c0;
    background-color: #f0f0f0;
}
</STYLE>
</HEAD>

<img src="../../imgs/about.gif">
<?php echo $msg; ?>
</BODY>
</HTML>