<?php
/********************************************************
	conf/custom_auth.inc
	
	PURPOSE:
        Offer a place to implement any secondary custom
        authentication that may be required.
        The default system only authenticates against the
        IMAP server.
    PRE-CONDITION:
        $user - Login ID
        $password - Password
        $host - IMAP Server
        $error - Don't run if this isn't empty...
                    ...primary authentication failed anyways.
    POST-CONDITION:
        $error - Set to error message if failed, or 
                    leave blank/empty if successful
                    
********************************************************/

?>
