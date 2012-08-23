<?php

/**
 * Builds the templates for atmail in a supported language
 */

ini_set("max_execution_time", 900);

require('../libs/Atmail/Config.php');


if (isset($_SERVER['GATEWAY_INTERFACE']) && $_SERVER['GATEWAY_INTERFACE'])
{
    print <<<EOF
<h1>Installation Script Error</h1>
<p>The mergenew.php script can only be run via the command-line.</p>
EOF;
    exit;
}

if (!$dir = opendir("{$pref['install_dir']}/lang/languages/"))
die("Cannot read {$pref['install_dir']}/lang/languages/ dir");

while (false !== $file = readdir($dir))
{

if ( $file == '.' || $file == '..' || preg_match('/^\./', $file))
continue;

print "Updating $file with any new strings . . . \n";
system("perl {$pref['install_dir']}/lang/compare.pl {$pref['install_dir']}/lang/languages/english/english.lang {$pref['install_dir']}/lang/languages/$file/$file.lang > /tmp/newlang.lang ; cat /tmp/newlang.lang >> {$pref['install_dir']}/lang/languages/$file/$file.lang");


}

system("cd {$pref['install_dir']}; php lang.php all");

?>
