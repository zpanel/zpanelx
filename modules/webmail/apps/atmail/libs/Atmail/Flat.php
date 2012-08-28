<?php

require_once('header.php');

require_once('Global.php');


class Flat
{

	var $VERSION = '1.1';

	function Flat()
	{
	    $this->debug = 0;
	    require_once('Config.php');
	}

	function gethash($path)
	{
		$path = $this->escape_pathname($path);
	    $txt = file_get_contents($path);

	    eval($txt) || print "$@";

	    return $db;
	}

	function updatehash($path, $db)
	{
		$path = $this->escape_pathname($path);

	    $fh = fopen("$path.new", 'w');

	    fwrite($fh, "\$db = (");

	    foreach ( $db as $k => $v)
	    {
	        if ($k == "0" ) continue;

	        $db[$k] = addslashes($v);
	        fwrite($fh, "'$k' => '$v',\n");
	    }

	    fwrite($fh, ");");
	    close($fh);

	    rename("$path.new", $path);

	    return 1;
	}

	# Save a message into the users mailbox
	function savemsg($args)
	{
	    # Get a new message number to append too
	    $id = $this->get_spare_msg($args['Account'], $args['EmailBox'] );

		$filename = $this->escape_pathname("{$pref['user_dir']}/users/{$args['Account']}/mbox/{$args['EmailBox']}/$id.msg");

	    # Create a new file and print the message
	    $fh = fopen("$filename", 'w')
	      || Config::catcherror( "Error cannot open $filename");

	    fwrite($fh, "From atmail " . localtime() . "\n");

		if($args['EmailFile'])
		{
			$msq = file_get_contents($args['EmailFile']);
			fwrite($fh, $msq);

		}
		else
	    	fwrite($fh, $args['EmailMessage']);

	    fclose($fh);

	    return;
	}


	# Find a spare msg number from our users folder
	function get_spare_msg($user, $mbox)
	{
	    $folder = array();

	    $num = 0;

		$filename = $this->escape_pathname("{$pref['user_dir']}/users/$user/mbox/$mbox/");

	    # Open the users mailbox, search for the most recent msg ID
	    $dh = opendir($filename);

	    while(false !== $f = readdir($dh))
	    {
	        if ( $f == "." || $f == ".." || strpos($f, '.msg') === false)
	        	continue;
	        $f = str_replace('.msg', '', $f);
	        if ($f > $num)
	        	$num = $f;
	    }

	    # Return the last msg id + 1 . This is used for the new message
	    return $num + 1;
	}

	# Find the size of a mailbox
	function getsize($user, $mbox)
	{
		$filename = $this->escape_pathname("{$pref['user_dir']}/users/$user/mbox/$mbox");
	    return filesize($filename);

	}


	# Write a more complex hash to disk
	function hashdump($name, $file, $db)
	{
	    # Just in case ...
	    $file = str_replace(array('..', '|'), '', $file);

		$filename = $this->escape_pathname("{$pref['user_dir']}/users/$file");

	    # Save the changes to the users $file in their directory.
	    $fh = fopen($filename, 'w') || die ("cannot open $filename");

	    fwrite($fh, var_export($db, true));
	    close($fh);

	    return;
	}


	function quote($txt)
	{
	    return $txt;
	}

}

?>