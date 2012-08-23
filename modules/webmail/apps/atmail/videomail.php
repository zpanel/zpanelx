<?php
// +----------------------------------------------------------------+
// | videomail.php													|
// +----------------------------------------------------------------+
// | Function: Get a Video-Stream ID from video.atmail.com			|
// | This is a wrapper function to communicate to the Videomail 	|
// | server since Ajax calls do not allow cross domain due to 		|
// | security settings												|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Global.php');
require_once('Session.php');

session_start();


$atmail = new AtmailGlobal();
$auth =& $atmail->getAuthObj();

$atmail->httpheaders();

$atmail->status = $auth->getuser();
$atmail->username = $auth->get_username();
$atmail->pop3host = $auth->get_pop3host();

// Print the error screen if the account has auth errors, or session timeout.
if ( $atmail->status == 1 )
	$atmail->auth_error();
if ( $atmail->status == 2 )
	$atmail->session_error();

$func = $_REQUEST['func'];

// If we auth, grab the StreamID
$UniqueID = $_REQUEST['UniqueID'];
$EmailSubject = $_REQUEST['EmailSubject'];
$EmailFrom = $_REQUEST['EmailFrom'];
$DownloadID = $_REQUEST['DownloadID'];

$querydata = array('UniqueID'=>$UniqueID,
                   'EmailSubject'=>$EmailSubject,
                   'EmailFrom'=>$EmailFrom,
                   'DownloadID'=>$DownloadID);

// Some PHP < 5 implementations of http_build_query() are broken and don't warn when the function is not found  (including pear/PHP_Compat)  so we define our own if it's missing - CB 09/17/07

if (!function_exists('http_build_query')) {
    function http_build_query($querydata, $numeric_prefix = "")
    {
       $arr = array();
       foreach ($querydata as $key => $val)
         $arr[] = urlencode($numeric_prefix.$key)."=".urlencode($val);
       return implode($arr, "&");
    }
}

$httpquerystring = http_build_query($querydata);

header('Content-Type: text/xml;');

if ($func == 'getstreamid')
{
	$page = '';
	$header = 0;

	if (ini_get('allow_url_fopen')) {
		$page = file_get_contents("http://{$pref['videomail_server']}/videomail/getstreamid.php?$httpquerystring");
		// Debug to apache error-log
		//file_put_contents("php://stderr", "REQUEST = http://{$pref['videomail_server']}/videomail/getstreamid.php?$httpquerystring\n");
	} else
	{
		$fp = stream_socket_client("tcp://{$pref['videomail_server']}:80");
		if ($fp)
		{
		   fwrite($fp, "GET /videomail/getstreamid.php?$httpquerystring HTTP/1.0\r\nHost: {$pref['videomail_server']}\r\nAccept: */*\r\n\r\n");
		   while (!feof($fp)) {
			   $line = fgets($fp, 1024);
		       $page .= $line;
			}
		   	fclose($fp);

			// Extract only the response, we do not want the HTTP headers
			if(preg_match('/(<VideoMail><StreamID>.*?<\/StreamID><\/VideoMail>)/i', $page, $m))
			$page = $m[1];

		}
	}

	// Debug to apache error-log
	//file_put_contents("php://stderr", "PAGE = $page");
	
	print $page;
}
elseif ($func == 'getuniqueid')
{
	// Generate a new UniqueID
	$UniqueID = md5("$auth->username@$auth->pop3host" . session_id() . time() . mt_rand(0,999999) . $pref['downloadid']);
	print "<VideoMail><UniqueID>$UniqueID</UniqueID></VideoMail>\n";
}

$atmail->end();

?>
