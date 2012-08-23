<?php

function ldap_mailhost_on_login($tools) {

    // load the settings for this module
    require($tools->include_path.'settings.php');

    // we're looking to find the user's imap server setting
    $imap_server = false;

    // user posted to login form
    if (isset($_POST['user'])) {
        $user = $_POST['user'];

        // check to see if the user specified their domain (user@domain)
        $domain = false;
        if (strstr($_POST['user'], '@') && strpos($_POST['user'], '@') < strlen($_POST['user'])) {
            $user   = substr($_POST['user'], 0, (strpos($_POST['user'], '@')));
            $domain = substr($_POST['user'], (strpos($_POST['user'], '@') + 1));
        }

        // make sure we have safe input
        if ( ! preg_match( $uid_pattern, $user ) ) {
            return;
        }
        if ( $domain && ! preg_match( $domain_pattern, $domain ) ) {
            return;
        }

        // connect and bind to ldap
        if ( ! ($connect = @ldap_connect($ldap_server)) ) {
            return;
        }
        if ( ! ($bind = @ldap_bind($connect, $ldap_user, $ldap_pass)) ) {
            return;
        }

        // search for the user in the appropriate ldap base dn
        if ( ! ( $search = @ldap_search(
                               $connect, 
                               $domain ? sprintf( 
                                             $domain_base_format, 
                                             $domain
                                         ) 
                                       : $default_base, 
                               sprintf(
                                   $uid_filter_format, 
                                   $user
                               )
                           )
        ) ) {
            return;
        }

        // make sure we found one and only one user
        if ( ldap_count_entries($connect,$search) != 1 ) {
            return;
        }

        // get the mailhost attribute from the user entry
        $info = ldap_get_entries($connect, $search);
        if ( $info[0] && $info[0][$mailhost_attr] && $info[0][$mailhost_attr][0] ) {
            $mailhost = $info[0][$mailhost_attr][0];

            // look up the mailhost value in the settings map to find
            // the corresponding imap_alt server
            foreach ($server_map as $alt_imap => $vals) {
                if ( $mailhost == $vals ) {
                                    
                    // if all went well
                    // we found the correct imap server to connect to
                    $_POST['imap_server'] = $alt_imap;
                    return;
                                
                }
            }
        }

        // if something went wrong, the imap_server will remain whatever
        // the default imap_server setting is in hastymail.conf
        // depending on what that is set to, it will probably result in 
        // and error to the user indicating that that imap server is down
        
    }
}
?>
