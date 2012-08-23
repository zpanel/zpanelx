<?php
/********************************************************
	conf/defaults.inc
		
	PURPOSE:
		Configuration options for login screen

********************************************************/

/***************** HOSTS *******************************
	The $host variable is used to determine how the user
	specifies the email server to log into.
		Format of host:
			[protocol/]host[:port]
		e.g.:
			mail.domain.com
			IMAP/mail.domain.com:145
			mail.domain.com:110
		note:
			You can have multiple mail server that use
			different protocols.  If protocol or port
			is specified in host string, the "protocol"
			field in the login screen will be ignored.
			
			Protocol is optional only if the port is a
			standard port (110=pop, 143=imap, 993=imap-ssl)
			If neither protocol or port is specified, 
			it will default to IMAP.
********************************************************/

// default.  index.php will display an empty text box
$default_host = "localhost";


//	Use the following to hard code the server.  If the
//	following line is used, index.php will not allow
//	users to specify any other server
//
//$default_host = "imap.example.com";


//	Use the following if you have multiple supported
//	servers, from which users can choose from.
//
//$default_host = array(
//	"red.example.com"=>"Red", 
//	"green.example.com"=>"Green", 
//	"imap.example.com"=>"imap.example.com"
//	);



/***************** ADVANCED ****************************
	The "advanced" login screen allows the users to specify
	the protocol (POP/IMAP) as well as the root directory,
	in addition to the standard parameters.
	Set the value to 0 if you do not want to give users 
	the ability to specify the protocol or rootdir.
********************************************************/

$adv_mode = 1;



/***************** Default Port *************************
	Specify the default port.  If "advanced" mode is enabled,
	the value specified here will be the default value, but
	the user will be able to change it.
	IlohaMail currently does not support nonstandard ports.
	Ports:
		143 for IMAP
		110 for POP3
********************************************************/

$default_port = 143;



/***************** Default Root Direcotry ***************
	Specify the default rootdir.  If "advanced" mode is 
	enabled, the value specified here will be the default 
	value, but the user will be able to change it.

	Rootdirs are only used by IMAP accounts, and in only
	rare cases.  Some IMAP servers will return all folders
	within a user's home directory, and not only folders
	used to store email.  If that happens, rootdir can
	be used to make sure only related folders are returned.
********************************************************/

$default_rootdir = "";



/***************** Default Root Direcotry ***************
    Default language to use if user doesn't specify one
    when first logging in.  See include/langs.inc for
    available languages.
********************************************************/

$default_lang = "eng/";




/******************* Show / Hide Fields *****************
	The following directives allow you to hide some of
	the fields in the login screen.
	A default value should be specified  above, if the
	fields are hidden.
	Values:
		1 = Hide
		0 = Show
	
	For $hide_host to have affect, a $default_host must 
	be specified.
********************************************************/

$hide_host = 0;

$hide_protocol = 0;

$hide_rootdir = 1;

$hide_lang = 0;


/******************* Auto-Append ************************
	Automatically appends string (usually host) to end of
	user id.  Leave blank/undeclared if you do not need
	to auto-append.
	String will not be appended if already present in 
	user id.
	
	e.g.
		This will append "@domain.com" to users logging
		into mail.domain.com (so it'll be something like
		"user@domain.com"):

		$AUTO_APPEND["mail.domain.com"] = "@domain.com";
		
********************************************************/

//$AUTO_APPEND["mail.domain.com"] = "@domain.com";




/******************* Log out redirect *******************
    URL to be redirected to after user logs out.  Default
    is "index.php" which is the login screen.
********************************************************/

$logout_url = "logout.php";



/******************* Use Cookies ************************
	When cookies are used, the session encryption key is
	stored in a cookie, instead of using an encryption key
	generated from the user's IP address.  Unlike IP-based
	encryption keys, keys stored in cookies will carry over
	even if the user's IP address changes.  This will also
	enhance security, especially when used with SSL.
	
	If cookies aren't available in the user's browser, 
	IlohaMail will automatically revert to a IP-based key.
********************************************************/

$USE_COOKIES = 1;


/******************* VDOMAIN_DETECT ************************
	When allowing access to multiple hosts through one installation
	of IlohaMail, this feature can be used to auto-detect the right
	host depending on the vhost used to access the interface.
	
	Format:
	$VDOMAIN_DETECT["HTTP vhost"] = "mail host";
	
********************************************************/

//	When accessed from http://domain1.com/mail,
//		use mail.domain1.com:
$VDOMAIN_DETECT["domain1.com"] = "mail.domain1.com";

//	When accessed from http://domain2.net/mail, 
//		use mail.domain2.net
$VDOMAIN_DETECT["domain2.net"] = "mail.domain2.net";


/******************* SSL_ENABLED ************************
	If set to 'true', the protocol selection menu will 
	display 'imap-ssl', for IMAP over SSL.
	
	NOTE: Enabled ONLY IF your build of PHP supports 
	SSL (i.e. was compiled with the --with-openssl flag).
********************************************************/
$SSL_ENABLED = false;

?>