<?php
// +----------------------------------------------------------------+
// | mime.php														|
// +----------------------------------------------------------------+
// | Function: Sent an attachment to the users browser : Prompt to	|
// | download or view . The attachments are stored in the 			|
// | $pref[user_dir]/tmp/											|
// | directory - Which is outside of the webserver root.			|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Session.php');
require_once('Global.php');

session_cache_limiter ('private, must-revalidate');
session_start();

// We need this to run as long as possible, otherwise a user might be downloading
// a large attachment on a slow link, we do not want it to time out
ini_set('max_execution_time', 1200);

$var = $mime = array();

$mime['hqx']     = "application/mac-binhex40";
$mime['cpt']     = "application/mac-compactpro";
$mime['doc']     = "application/msword";
$mime['bin']     = "application/octet-stream";
$mime['dms']     = "application/octet-stream";
$mime['lha']     = "application/octet-stream";
$mime['lzh']     = "application/octet-stream";
$mime['exe']     = "application/octet-stream";
$mime['class']   = "application/octet-stream";
$mime['oda']     = "application/oda";
$mime['pdf']     = "application/pdf";
$mime['ai']      = "application/postscript";
$mime['eps']     = "application/postscript";
$mime['ps']      = "application/postscript";
$mime['ppt']     = "application/powerpoint";
$mime['rtf']     = "application/rtf";
$mime['bcpio']   = "application/x-bcpio";
$mime['vcd']     = "application/x-cdlink";
$mime['']        = "application/x-compress";
$mime['cpio']    = "application/x-cpio";
$mime['csh']     = "application/x-csh";
$mime['dcr']     = "application/x-director";
$mime['dir']     = "application/x-director";
$mime['dxr']     = "application/x-director";
$mime['dvi']     = "application/x-dvi";
$mime['gtar']    = "application/x-gtar";
$mime['tgz']     = "application/x-tgz";
$mime['hdf']     = "application/x-hdf";
$mime['skp']     = "application/x-koan";
$mime['skd']     = "application/x-koan";
$mime['skt']     = "application/x-koan";
$mime['skm']     = "application/x-koan";
$mime['latex']   = "application/x-latex";
$mime['mif']     = "application/x-mif";
$mime['nc']      = "application/x-netcdf";
$mime['cdf']     = "application/x-netcdf";
$mime['sh']      = "application/x-sh";
$mime['shar']    = "application/x-shar";
$mime['sit']     = "application/x-stuffit";
$mime['sv4cpio'] = "application/x-sv4cpio";
$mime['sv4crc']  = "application/x-sv4crc";
$mime['tar']     = "application/x-tar";
$mime['tcl']     = "application/x-tcl";
$mime['tex']     = "application/x-tex";
$mime['texinfo'] = "application/x-texinfo";
$mime['texi']    = "application/x-texinfo";
$mime['t']       = "application/x-troff";
$mime['tr']      = "application/x-troff";
$mime['roff']    = "application/x-troff";
$mime['man']     = "application/x-troff-man";
$mime['me']      = "application/x-troff-me";
$mime['ms']      = "application/x-troff-ms";
$mime['ustar']   = "application/x-ustar";
$mime['src']     = "application/x-wais-source";
$mime['zip']     = "application/zip";
$mime['au']      = "audio/basic";
$mime['snd']     = "audio/basic";
$mime['mid']     = "audio/midi";
$mime['midi']    = "audio/midi";
$mime['kar']     = "audio/midi";
$mime['mpga']    = "audio/mpeg";
$mime['mp2']     = "audio/mpeg";
$mime['mp3']     = "audio/mpeg";
$mime['aif']     = "audio/x-aiff";
$mime['aiff']    = "audio/x-aiff";
$mime['aifc']    = "audio/x-aiff";
$mime['ram']     = "audio/x-pn-realaudio";
$mime['rpm']     = "audio/x-pn-realaudio-plugin";
$mime['ra']      = "audio/x-realaudio";
$mime['wav']     = "audio/x-wav";
$mime['pdb']     = "chemical/x-pdb";
$mime['xyz']     = "chemical/x-pdb";
$mime['gif']     = "image/gif";
$mime['ief']     = "image/ief";
$mime['jpeg']    = "image/jpeg";
$mime['jpg']     = "image/jpeg";
$mime['jpe']     = "image/jpeg";
$mime['png']     = "image/png";
$mime['tiff']    = "image/tiff";
$mime['tif']     = "image/tiff";
$mime['ras']     = "image/x-cmu-raster";
$mime['pnm']     = "image/x-portable-anymap";
$mime['pbm']     = "image/x-portable-bitmap";
$mime['pgm']     = "image/x-portable-graymap";
$mime['ppm']     = "image/x-portable-pixmap";
$mime['rgb']     = "image/x-rgb";
$mime['xbm']     = "image/x-xbitmap";
$mime['xpm']     = "image/x-xpixmap";
$mime['xwd']     = "image/x-xwindowdump";
$mime['css']     = "text/css";
$mime['html']    = "text/html";
$mime['htm']     = "text/html";
$mime['txt']     = "text/plain";
$mime['rtx']     = "text/richtext";
$mime['tsv']     = "text/tab-separated-values";
$mime['etx']     = "text/x-setext";
$mime['sgml']    = "text/x-sgml";
$mime['sgm']     = "text/x-sgml";
$mime['mpeg']    = "video/mpeg";
$mime['mpg']     = "video/mpeg";
$mime['mpe']     = "video/mpeg";
$mime['qt']      = "video/quicktime";
$mime['mov']     = "video/quicktime";
$mime['avi']     = "video/x-msvideo";
$mime['movie']   = "video/x-sgi-movie";
$mime['ice']     = "x-conference/x-cooltalk";
$mime['wrl']     = "x-world/x-vrml";
$mime['vrml']    = "x-world/x-vrml";

$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();
$atmail->status = $auth->getuser($atmail->SessionID);

// Print the error screen if the account has auth errors, or session timeout.
if ($atmail->status == 1)
	$atmail->auth_error();

if (($atmail->status == 2) && (!preg_match('/xhtml/i', $_REQUEST['whoscalling'])))
	$atmail->session_error();

// use basename() to remove any path an attacker may add
$var['src'] = basename(rawurldecode($_REQUEST['file']));
$tmpfile = $pref['user_dir'] . "/tmp/" . $atmail->auth->get_account() . "/" . $var['src'];

// Exit if no pathname is defined
if (!$var['src']) die("Not implemented");

$size = filesize($tmpfile);
$name = rawurldecode($_REQUEST['name']);

if (!$name)
	$name = $var['src'];

if ( preg_match('/.*\.(.*)$/', $name, $match))
	$var['ext'] = $match[1] ;

$var['type'] = $mime[$var['ext']];
if ( !$var['type'] )
	$mime[$var['ext']] = "unknown/data";

// Strip the .safe extension
$name = preg_replace('/\.safe$/', '', $name);

// If the filename is too long, cut it to a size the HTTP header can read!
if (strlen($name) > 182)
{
	$name = substr($name, 0, 171);
	$name = $name . ".{$var['ext']}";
}

$name = str_replace(array("\r", "\n"), '', $name);

if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
    $encName = rawurlencode($name);
} else {
    $encName = $name;    
}


if ($var['ext'] == "html")
	$atmail->httpheaders();
else
{
	header("Content-Type: ".$mime[$var['ext']] . ";");
	header("Content-Length: $size");
	header("Content-Disposition: attachment; filename=\"$encName\"; charset=\"UTF-8\"");
	header("Pragma: ");
}

$fh = fopen($tmpfile, 'r');
if (!is_resource($fh)) {
	die("Error with $tmpfile - Please check the directory exists and the session has not timed out");
}

$i = 0;
while (!feof($fh))
{
    $i++;
    $buf = @fread($fh, 4096);
    if  (empty($buf))
    	break;

    echo $buf;

    if  ($i % 50 == 0)
    	ob_flush();
}

ob_flush();
fclose($fh);
$atmail->end();
