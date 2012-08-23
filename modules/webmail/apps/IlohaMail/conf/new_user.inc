<?php
/********************************************************
	conf/new_user.inc
	
	PURPOSE:
        Do whatever needs to be done for a new user, that
        isn't done by the default program.  This might include
        something like sending them a welcoming email, or
        upgrading external databases.
        It can be anything as long as IT HAS NOT OUTPUT.
        
        This shouldn't require authentication or any sort of
        validation that could prevent the user from logging in.
        Any such processing must be done in conf/custom_auth.inc
        
    PRE-CONDITION:
        $user_name - Login ID
        $password - Password
        $host - IMAP Server
        $error - Don't run if this isn't empty...
                    ...primary authentication failed.
    POST-CONDITION:
        Whatever that needs to be done...

********************************************************/

    /***
        Example:
        The code below will send a short email to the new user.
    ***/
    
    // $email_address = $user_name."@".$host;
    // $subject = "Welcome to IlohaMail";
    // $message = "Hi!\n\n";
    // $message .= "Welcome to IlohaMail, the best webmail on the net.\n";
    // $message .= "This program was brought to you by the folks at\n";
    // $message .= "IlohaMail.org\n";
    // mail($email_address, $subject, $message);
?>
