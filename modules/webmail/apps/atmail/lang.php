<?php

/**
 * Builds the templates for atmail in a supported language
 */

ini_set("max_execution_time", 900);
require_once('header.php');
require_once('Config.php');

if (isset($_SERVER['GATEWAY_INTERFACE']) && $_SERVER['GATEWAY_INTERFACE'])
{
    print <<<EOF
<h1>Installation Script Error</h1>
<p>The lang.php script can only be run via the command-line.</p>
EOF;
    exit;
}

if ($_SERVER['argc'] == 1) {
	$langname = 'english';
	$langFile = 'lang/languages/english/english.lang';
} else {
	$langname = strtolower($_SERVER['argv'][1]);
}

$lang = array();

// build all available languages
if ($langname == 'all' || $langname == 'alls')
{
    if (!$dir = opendir("lang/languages/"))
    die("Cannot read lang/languages/ dir");

    while (false !== $file = readdir($dir))
    {
        if ( $file == '.' || $file == '..' || preg_match('/^\./', $file))
        continue;

        if ( $file == 'english' && $langname == 'alls' )
        continue;

        print "Translating $file .... \n";

        // system("php lang.php $file lang/languages/$file/$file.lang");

        $language[$file] = ucfirst($file);
        writeconf();
        newlang( $file, "lang/languages/$file/$file.lang" );
    }
}

// build selected language
else
{
    // get lang file location
    if (!isset($langFile)) $langFile = $_SERVER['argv'][2];

    if ( !$langname )
    {
        print
        "Please specify the language name: e.g php lang.php english lang/languages/english/english.lang\n";
        exit;
    }

    if ( !$langFile )
    {
        print
        "Please specify the language file: e.g php lang.php english lang/language/english/english.lang";
        exit;
    }

    $language[$langname] = ucfirst($langname);

    //Atmail::Config->
    //writeconf();

    newlang( $langname, $langFile );
}

exit;


function newlang($langname, $file)
{
    global $lang;

    print "Writing new Language $langname using $file\n\n";

    if (!$fh = fopen($file, 'r'))
    {
        die("Cannot read $file");
    }

    while (false !== $line = fgets($fh))
    {

        // get content from lang file
        if (preg_match('/(.*?)=(.*)/', $line, $result))
        {
            //list($name, $value) = $m;

            $name  = trim($result[1]);
            $value = trim($result[2]);

            $lang[$name] = $value;
        }

    }

    // Include our language name
    $lang['lang'] = $langname;

    // delete previous version of lang templates
    if (is_dir("html/".$langname)) {
        remove_directory("html/".$langname);
    }

    remove_directory("./lang/$langname");
    copy_directory("./lang/html", "./lang/$langname");


    /*### TAKE OUT FOR PRODUCTION!!!
    if ($pref['sql_table'] == "atmail404new")
    system("find /usr/local/atmail/webmail/lang/$lang -name \".svn\" | sed \"s/^/rm -rf /g\" | sh");
    ### TAKE OUT FOR PRODUCTION!!!
    */

    html_search("./lang/$langname/");
    //html_search("./lang/$langname/calendar/");
    html_search("./lang/$langname/help/");
    html_search("./lang/$langname/javascript/");
    //html_search("./lang/$langname/javascript/htmleditor/");
    html_search("./lang/$langname/msg/");
    html_search("./lang/$langname/simple/");
    //html_search("./lang/$langname/simple/heading/");
    

    if(is_dir("./lang/$langname/xhtml/"))
    html_search("./lang/$langname/xhtml/");
    
    html_search("./lang/$langname/xp/");
    //html_search("./lang/$langname/xp/heading/");

    if(file_exists("./lang/$langname/xp/calendar/readpost.html"))
    html_search("./lang/$langname/xp/calendar/");

	if(file_exists("./lang/$langname/xp/css"))
	html_search("./lang/$langname/xp/css/");

	if(file_exists("./lang/$langname/calendar"))
	html_search("./lang/$langname/calendar");

    //html_search("./lang/$langname/xul/");
    //html_search("./lang/$langname/xul/css/");

    sleep(1);

    copy_directory("./lang/$langname", "./html/$langname");
    remove_directory("./lang/$langname");

    print "New language installed to ./html/$langname/\n";

    if ( !is_dir("imgs/menubar-$langname") && !is_dir("lang/languages/$langname/menubar-$langname") )
    {
        print "
Image Translation
-----------------

 - The imgs/menubar-$langname directory does not exist. To complete the translation of @Mail
 you are required to modify the text for the menubar images.
  ";
        $value = enterinfo( "
Icons for $langname could not be found.
Copy English template to imgs/menubar-$langname ?", "Y" );

        if ( strtolower($value) == 'y')
        {
            copy_directory("imgs/menubar-english", "imgs/menubar-$langname");
        }
    }

    if ( is_dir("lang/languages/$langname/menubar-$langname") )
    {
        $value = enterinfo("
Move image template from:
lang/languages/$langname/menubar-$langname to imgs/menubar-$langname ?", "Y" );

        if ( strtolower($value) == 'y' )
        {
            $time = time();
            print "Creating images ...\n\n";
            copy_directory("imgs/menubar-$langname", "/tmp/menubar-$langname-$time");
            remove_directory("imgs/menubar-$langname");
            copy_directory("lang/languages/$langname/menubar-$langname", "imgs/menubar-$langname");
            remove_directory("lang/languages/$langname/menubar-$langname");
        }
    }
}


# Add the new Language into the Config file
# This will craete the language popup in the login page
$language[$lang] = ucfirst($lang);

# Write the new conf
//Atmail::Config->
writeconf();

/*
if ($files)
print "Copying existing templates";

foreach ( keys %files ) {
print "$_ = $files{$_}\n";
}
*/

function html_search($dir)
{
    print "Searching $dir";
    if (!$dh = opendir($dir))
    die("could not open $dir\n\n");


    while (false !== $folder = readdir($dh))
    {
        if ( $folder == '.' || $folder == '..' || !preg_match('/\.html$|\.js$|\.xhtml|\.css$/', $folder)
        || $folder == 'change-perl.pl' )
        continue;

        change_path( $dir, $folder );
    }

    print " done\n";
}


function change_path($dir, $folder)
{
    global $lang;

    print ".";
    $fh = fopen("$dir/$folder", 'r');
    $fh2 = fopen("$dir/$folder.new", 'w');

    while (false !== $line = fgets($fh))
    {
        # Substituite the new var names
        $stetement = '/\$lang\[\'(.*?)\'\]/';
        while (preg_match($stetement, $line, $match)) {

            if (array_key_exists($match[1], $lang)) {
                $line = str_replace($match[0], $lang[$match[1]], $line);
            } else {
                //echo "\nno lang var exists for $match[0]!\n";
                break;
            }
        }

        # Take out those annoying ^M characters
        $line = preg_replace('/\cM/', '', $line);

        # Change the Include files
        $line = preg_replace('/<!--Include="html\/(.*?)\//', "<!--Include=\"html/".$lang['lang']."/", $line);

        fwrite($fh2, $line);
    }

    fclose($fh);
    fclose($fh2);

    // Crappy winblows complains about renaming a file to
    // a filename that already exists...

    if ( strtolower( substr( PHP_OS, 0, 3 ) ) == 'win' ) {
        unlink("$dir/$folder");
    }

    rename( "$dir/$folder.new", "$dir/$folder" )|| die("could not rename $dir/$folder.new to $dir/$folder");
    chmod($dir."/".$folder, 0744);
}


function enterinfo($txt, $var)
{
    print "\033[1;32m $txt [$var]: \033[0;39m";

    if(!defined("STDIN")) {
        define("STDIN", "fopen('php://stdin','r')");
    }

    $value = trim(fgets(STDIN));
    if ( !$value )
    $value = $var;
    print "\n";
    return $value;
}


/**
 * Remove selected directory
 *
 * @param string $directory
 * @param boolean $empty
 */
function remove_directory($directory, $empty = false)
{

    if(substr($directory, -1) == '/') {
        $directory = substr($directory,0,-1);
    }
    if (!file_exists($directory) || !is_dir($directory))  {
        return false;
    } elseif (!is_readable($directory)) {
        return false;
    } else {
        $handle = opendir($directory);

        while (false !== ($item = readdir($handle))) {
            if ($item != '.' && $item != '..') {
                $path = $directory.'/'.$item;

                if (is_dir($path)) {
                    remove_directory($path);
                } else {
                    unlink($path);
                }
            }
        }

        closedir($handle);

        if($empty == false) {
            if(!rmdir($directory)) {
                return false;
            }
        }

        return true;
    }
}

/**
 * Copy selected directory
 *
 * @param string $source
 * @param string $destination
 */
function copy_directory($source, $destination)
{
    $file_array = array();

    if (is_file($source)) {
        $perm = fileperms($source);
        copy($source, $destination);
        chmod($destination, 0744);
    }

    if (@is_dir($source)) {
        @mkdir($destination, 0777);
        $dir_handle = opendir($source);

        while ($files = readdir($dir_handle)) {
            if ($files != "." && $files != "..") {
                $file_array[] = $files;
            }
        }
        closedir($dir_handle);
    }

    for($i = 0; $i < count($file_array); $i++) {
        $file = $file_array[$i];
        if ($destination != "$source/$file") {
            copy_directory("$source/$file", "$destination/$file");
        }
    }
}
?>
