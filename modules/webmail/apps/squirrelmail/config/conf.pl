#!/usr/bin/env perl
# conf.pl
#
# Copyright (c) 1999-2011 The SquirrelMail Project Team
# Licensed under the GNU GPL. For full terms see COPYING.
#
# A simple configure script to configure SquirrelMail
#
# $Id: conf.pl 14111 2011-05-03 07:46:46Z pdontthink $
############################################################              
$conf_pl_version = "1.4.0";

############################################################
# Check what directory we're supposed to be running in, and
# change there if necessary.  File::Basename has been in
# Perl since at least 5.003_7, and nobody sane runs anything
# before that, but just in case.
############################################################
my $dir;
if ( eval q{require "File/Basename.pm"} ) {
    $dir = File::Basename::dirname($0);
    chdir($dir);
}

############################################################              
# Some people try to run this as a CGI. That's wrong!
############################################################              
if ( defined( $ENV{'PATH_INFO'} )
    || defined( $ENV{'QUERY_STRING'} )
    || defined( $ENV{'REQUEST_METHOD'} ) ) {
    print "Content-Type: text/html\n\n";
    print "You must run this script from the command line.";
    exit;
    }

############################################################
# If we got here, use Cwd to get the full directory path
# (the Basename stuff above will sometimes return '.' as
# the base directory, which is not helpful here). 
############################################################
use Cwd;
$dir = cwd();
  

############################################################              
# First, lets read in the data already in there...
############################################################              
if ( -e "config.php" ) {
    # Make sure that file is readable
    if (! -r "config.php") {
        clear_screen();
        print "WARNING:\n";
        print "The file \"config/config.php\" was found, but you don't\n";
        print "have rights to read it.\n";
        print "\n";
        print "Press enter to continue";
        $ctu = <STDIN>;
        exit;
    }
    open( FILE, "config.php" );
    while ( $line = <FILE> ) {
        $line =~ s/^\s+//;
        $line =~ s/^\$//;
        $var = $line;

        $var =~ s/=/EQUALS/;
        if ( $var =~ /^([a-z]|[A-Z])/ ) {
            @o = split ( /\s*EQUALS\s*/, $var );
            if ( $o[0] eq "config_version" ) {
                $o[1] =~ s/[\n|\r]//g;
                $o[1] =~ s/[\'|\"];\s*$//;
                $o[1] =~ s/;$//;
                $o[1] =~ s/^[\'|\"]//;

                $config_version = $o[1];
                close(FILE);
            }
        }
    }
    close(FILE);

    if ( $config_version ne $conf_pl_version ) {
        clear_screen();
        print "WARNING:\n";
        print "  The file \"config/config.php\" was found, but it is for\n";
        print "  an older version of SquirrelMail. It is possible to still\n";
        print "  read the defaults from this file but be warned that many\n";
        print "  preferences change between versions. It is recommended that\n";
        print "  you start with a clean config.php for each upgrade that you\n";
        print "  do. To do this, just move config/config.php out of the way.\n";
        print "\n";
        print "Continue loading with the old config.php [y/N]? ";
        $ctu = <STDIN>;

        if ( ( $ctu !~ /^y\n/i ) || ( $ctu =~ /^\n/ ) ) {
            exit;
        }

        print "\nDo you want me to stop warning you [y/N]? ";
        $ctu = <STDIN>;
        if ( $ctu =~ /^y\n/i ) {
            $print_config_version = $conf_pl_version;
        } else {
            $print_config_version = $config_version;
        }
    } else {
        $print_config_version = $config_version;
    }

    $config = 1;
    open( FILE, "config.php" );
} elsif ( -e "config_default.php" ) {
    open( FILE, "config_default.php" );
    while ( $line = <FILE> ) {
        $line =~ s/^\s+//;
        $line =~ s/^\$//;
        $var = $line;

        $var =~ s/=/EQUALS/;
        if ( $var =~ /^([a-z]|[A-Z])/ ) {
            @o = split ( /\s*EQUALS\s*/, $var );
            if ( $o[0] eq "config_version" ) {
                $o[1] =~ s/[\n|\r]//g;
                $o[1] =~ s/[\'|\"];\s*$//;
                $o[1] =~ s/;$//;
                $o[1] =~ s/^[\'|\"]//;

                $config_version = $o[1];
                close(FILE);
            }
        }
    }
    close(FILE);

    if ( $config_version ne $conf_pl_version ) {
        clear_screen();
        print "WARNING:\n";
        print "  You are trying to use a 'config_default.php' from an older\n";
        print "  version of SquirrelMail. This is HIGHLY unrecommended. You\n";
        print "  should get the 'config_default.php' that matches the version\n";
        print "  of SquirrelMail that you are running. You can get this from\n";
        print "  the SquirrelMail web page by going to the following URL:\n";
        print "      http://squirrelmail.org.\n";
        print "\n";
        print "Continue loading with old config_default.php (a bad idea) [y/N]? ";
        $ctu = <STDIN>;

        if ( ( $ctu !~ /^y\n/i ) || ( $ctu =~ /^\n/ ) ) {
            exit;
        }

        print "\nDo you want me to stop warning you [y/N]? ";
        $ctu = <STDIN>;
        if ( $ctu =~ /^y\n/i ) {
            $print_config_version = $conf_pl_version;
        } else {
            $print_config_version = $config_version;
        }
    } else {
        $print_config_version = $config_version;
    }
    $config = 2;
    open( FILE, "config_default.php" );
} else {
    print "No configuration file found. Please get config_default.php\n";
    print "or config.php before running this again. This program needs\n";
    print "a default config file to get default values.\n";
    exit;
}

# Read and parse the current configuration file
# (either config.php or config_default.php).
while ( $line = <FILE> ) {
    $line =~ s/^\s+//; 
    $line =~ s/^\$//;
    $var = $line;

    $var =~ s/=/EQUALS/;
    if ( $var =~ /^([a-z]|[A-Z])/ ) {
        @options = split ( /\s*EQUALS\s*/, $var );
        $options[1] =~ s/[\n|\r]//g;
        $options[1] =~ s/[\'|\"];\s*$//;
        $options[1] =~ s/;$//;
        $options[1] =~ s/^[\'|\"]//;
        # de-escape escaped strings
        $options[1] =~ s/\\'/'/g;
        $options[1] =~ s/\\\\/\\/g;

        if ( $options[0] =~ /^theme\[[0-9]+\]\[['|"]PATH['|"]\]/ ) {
            $sub = $options[0];
            $sub =~ s/\]\[['|"]PATH['|"]\]//;
            $sub =~ s/.*\[//;
            if ( -e "../themes" ) {
                $options[1] =~ s/^\.\.\/config/\.\.\/themes/;
            }
            $theme_path[$sub] = &change_to_rel_path($options[1]);
        } elsif ( $options[0] =~ /^theme\[[0-9]+\]\[['|"]NAME['|"]\]/ ) {
            $sub = $options[0];
            $sub =~ s/\]\[['|"]NAME['|"]\]//;
            $sub =~ s/.*\[//;
            $theme_name[$sub] = $options[1];
        } elsif ( $options[0] =~ /^plugins\[[0-9]+\]/ ) {
            $sub = $options[0];
            $sub =~ s/\]//;
            $sub =~ s/^plugins\[//;
            $plugins[$sub] = $options[1];
        } elsif ( $options[0] =~ /^ldap_server\[[0-9]+\]/ ) {
            $sub = $options[0];
            $sub =~ s/\]//;
            $sub =~ s/^ldap_server\[//;
            $continue = 0;
            while ( ( $tmp = <FILE> ) && ( $continue != 1 ) ) {
                if ( $tmp =~ /\);\s*$/ ) {
                    $continue = 1;
                }

                if ( $tmp =~ /^\s*[\'|\"]host[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]host[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $host = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]base[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]base[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $base = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]charset[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]charset[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $charset = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]port[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]port[\'|\"]\s*=>\s*[\'|\"]?//i;
                    $tmp =~ s/[\'|\"]?,?\s*$//;
                    $tmp =~ s/[\'|\"]?\);\s*$//;
                    $port = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]maxrows[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]maxrows[\'|\"]\s*=>\s*[\'|\"]?//i;
                    $tmp =~ s/[\'|\"]?,?\s*$//;
                    $tmp =~ s/[\'|\"]?\);\s*$//;
                    $maxrows = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]name[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]name[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $name = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]binddn[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]binddn[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $binddn = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]bindpw[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]bindpw[\'|\"]\s*=>\s*[\'|\"]//i;
                    $tmp =~ s/[\'|\"],?\s*$//;
                    $tmp =~ s/[\'|\"]\);\s*$//;
                    $bindpw = $tmp;
                } elsif ( $tmp =~ /^\s*[\'|\"]protocol[\'|\"]/i ) {
                    $tmp =~ s/^\s*[\'|\"]protocol[\'|\"]\s*=>\s*[\'|\"]?//i;
                    $tmp =~ s/[\'|\"]?,?\s*$//;
                    $tmp =~ s/[\'|\"]?\);\s*$//;
                    $protocol = $tmp;
                }
            }
            $ldap_host[$sub]    = $host;
            $ldap_base[$sub]    = $base;
            $ldap_name[$sub]    = $name;
            $ldap_port[$sub]    = $port;
            $ldap_maxrows[$sub] = $maxrows;
            $ldap_charset[$sub] = $charset;
            $ldap_binddn[$sub]  = $binddn;
            $ldap_bindpw[$sub]  = $bindpw;
            $ldap_protocol[$sub] = $protocol;
        } elsif ( $options[0] =~ /^(data_dir|attachment_dir|theme_css|org_logo|signout_page)$/ ) {
            ${ $options[0] } = &change_to_rel_path($options[1]);
        } else {
            ${ $options[0] } = $options[1];
        }
    }
}
close FILE;

# Default values used when option is missing
$useSendmail = "false"                 if ( lc($useSendmail) ne "true" );
$sendmail_path = "/usr/sbin/sendmail"  if ( !$sendmail_path );
$pop_before_smtp = "false"             if ( !$pop_before_smtp ) ;
$pop_before_smtp_host = ''             if ( !$pop_before_smtp_host ) ;
$default_unseen_notify = 2             if ( !$default_unseen_notify );
$default_unseen_type = 1               if ( !$default_unseen_type );
$config_use_color = 0                  if ( !$config_use_color );
$invert_time = "false"                 if ( !$invert_time );
$force_username_lowercase = "false"    if ( !$force_username_lowercase );
$optional_delimiter = "detect"         if ( !$optional_delimiter );
$auto_create_special = "false"         if ( !$auto_create_special );
$default_use_priority = "true"         if ( !$default_use_priority );
$hide_sm_attributions = "false"        if ( !$hide_sm_attributions );
$default_use_mdn = "true"              if ( !$default_use_mdn );
$delete_folder = "false"               if ( !$delete_folder );
$noselect_fix_enable = "false"         if ( !$noselect_fix_enable );
$frame_top = "_top"                    if ( !$frame_top );

$provider_uri = "http://squirrelmail.org/" if ( !$provider_uri );
$provider_name = "SquirrelMail"        if ( !$provider_name );

$edit_identity = "true"                if ( !$edit_identity );
$edit_name = "true"                    if ( !$edit_name );
$allow_thread_sort = 'false'           if ( !$allow_thread_sort ) ;
$allow_server_sort = 'false'           if ( !$allow_server_sort );
$uid_support = 'true'                  if ( !$uid_support );
$no_list_for_subscribe = 'false'       if ( !$no_list_for_subscribe );
$allow_charset_search = 'true'         if ( !$allow_charset_search );

$prefs_user_field = 'user'             if ( !$prefs_user_field );
$prefs_key_field = 'prefkey'           if ( !$prefs_key_field );
$prefs_val_field = 'prefval'           if ( !$prefs_val_field );

$use_smtp_tls= 'false'                 if ( !$use_smtp_tls);
$smtp_auth_mech = 'none'               if ( !$smtp_auth_mech );

$use_imap_tls = 'false'                if ( !$use_imap_tls );
$imap_auth_mech = 'login'              if ( !$imap_auth_mech );

$session_name = 'SQMSESSID'            if (!$session_name );

$default_use_javascript_addr_book = 'false' if (! $default_use_javascript_addr_book);

# Added in 1.4.4
$lossy_encoding = 'false'               if ( !$lossy_encoding );
$addrbook_global_dsn = ''               if ( !$addrbook_global_dsn );
$addrbook_global_table = 'global_abook' if ( !$addrbook_global_table );
$addrbook_global_writeable = 'false'    if ( !$addrbook_global_writeable );
$addrbook_global_listing = 'false'      if ( !$addrbook_global_listing );
$abook_global_file = ''                 if ( !$abook_global_file);
$abook_global_file_writeable = 'false'  if ( !$abook_global_file_writeable);

# Added in 1.4.5
$hide_auth_header = "false"             if ( !$hide_auth_header );
$encode_header_key = ''                 if ( !$encode_header_key );

# Added in 1.4.8
$config_location_base = ''              if ( !$config_location_base );
# add qmail-inject test here for backwards compatibility
if ( !$sendmail_args && $sendmail_path =~ /qmail-inject/ ) {
    $sendmail_args = '';
} elsif ( !$sendmail_args ) {
    $sendmail_args = '-i -t';
}

# Added in 1.4.11
$smtp_sitewide_user = ''                if ( !$smtp_sitewide_user );
$smtp_sitewide_pass = ''                if ( !$smtp_sitewide_pass );

# Added in 1.4.9
$abook_global_file_listing = 'true'     if ( !$abook_global_file_listing );
$abook_file_line_length = 2048          if ( !$abook_file_line_length );

# Added in 1.4.16
$only_secure_cookies = 'true'           if ( !$only_secure_cookies );

# Added in 1.4.20RC1
$disable_security_tokens = 'false'      if ( !$disable_security_tokens );
$check_referrer = ''                    if ( !$check_referrer );

if ( $ARGV[0] eq '--install-plugin' ) {
    print "Activating plugin " . $ARGV[1] . "\n";
    if ( -d "../plugins/" . $ARGV[1]) {
        push @plugins, $ARGV[1];
        save_data();
        exit(0);
    } else {
        print "No such plugin.\n";
        exit(1);
    }
} elsif ( $ARGV[0] eq '--remove-plugin' ) {
    print "Removing plugin " . $ARGV[1] . "\n";
    foreach $plugin (@plugins) {
        if ( $plugin ne $ARGV[1] ) {
            push @newplugins, $plugin;
        }
    }
    @plugins = @newplugins;
    save_data();
    exit(0);
}



####################################################################################

# used in multiple places, define once
$list_supported_imap_servers = 
    "    bincimap    = Binc IMAP server\n" .
    "    courier     = Courier IMAP server\n" .
    "    cyrus       = Cyrus IMAP server\n" .
    "    dovecot     = Dovecot Secure IMAP server\n" .
    "    exchange    = Microsoft Exchange IMAP server\n" .
    "    hmailserver = hMailServer\n" .
    "    macosx      = Mac OS X Mailserver\n" .
    "    mercury32   = Mercury/32\n" .
    "    uw          = University of Washington's IMAP server\n" .
    "    gmail       = IMAP access to Google mail (Gmail) accounts\n";

#####################################################################################
if ( $config_use_color == 1 ) {
    $WHT = "\x1B[1m";
    $NRM = "\x1B[0m";
} else {
    $WHT              = "";
    $NRM              = "";
    $config_use_color = 2;
}

while ( ( $command ne "q" ) && ( $command ne "Q" ) ) {
    clear_screen();
    print $WHT. "SquirrelMail Configuration : " . $NRM;
    if    ( $config == 1 ) { print "Read: config.php"; }
    elsif ( $config == 2 ) { print "Read: config_default.php"; }
    print " ($print_config_version)\n";
    print "---------------------------------------------------------\n";

    if ( $menu == 0 ) {
        print $WHT. "Main Menu --\n" . $NRM;
        print "1.  Organization Preferences\n";
        print "2.  Server Settings\n";
        print "3.  Folder Defaults\n";
        print "4.  General Options\n";
        print "5.  Themes\n";
        print "6.  Address Books\n";
        print "7.  Message of the Day (MOTD)\n";
        print "8.  Plugins\n";
        print "9.  Database\n";
        print "10. Languages\n";
        print "\n";
        print "D.  Set pre-defined settings for specific IMAP servers\n";
        print "\n";
    } elsif ( $menu == 1 ) {
        print $WHT. "Organization Preferences\n" . $NRM;
        print "1.  Organization Name      : $WHT$org_name$NRM\n";
        print "2.  Organization Logo      : $WHT$org_logo$NRM\n";
        print "3.  Org. Logo Width/Height : $WHT($org_logo_width/$org_logo_height)$NRM\n";
        print "4.  Organization Title     : $WHT$org_title$NRM\n";
        print "5.  Signout Page           : $WHT$signout_page$NRM\n";
        print "6.  Top Frame              : $WHT$frame_top$NRM\n";
        print "7.  Provider link          : $WHT$provider_uri$NRM\n";
        print "8.  Provider name          : $WHT$provider_name$NRM\n";

        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 2 ) {
        print $WHT. "Server Settings\n\n" . $NRM;
        print $WHT . "General" . $NRM . "\n";
        print "-------\n";
        print "1.  Domain                 : $WHT$domain$NRM\n";
        print "2.  Invert Time            : $WHT$invert_time$NRM\n";
        print "3.  Sendmail or SMTP       : $WHT";
        if ( lc($useSendmail) eq "true" ) {
            print "Sendmail";
        } else {
            print "SMTP";
        }
        print "$NRM\n";
        print "\n";
        
        if ( $show_imap_settings ) {
          print $WHT . "IMAP Settings". $NRM . "\n--------------\n";
          print "4.  IMAP Server            : $WHT$imapServerAddress$NRM\n";
          print "5.  IMAP Port              : $WHT$imapPort$NRM\n";
          print "6.  Authentication type    : $WHT$imap_auth_mech$NRM\n";
          print "7.  Secure IMAP (TLS)      : $WHT$use_imap_tls$NRM\n";
          print "8.  Server software        : $WHT$imap_server_type$NRM\n";
          print "9.  Delimiter              : $WHT$optional_delimiter$NRM\n";
          print "\n";
        } elsif ( $show_smtp_settings ) {
          if ( lc($useSendmail) eq "true" ) {
            print $WHT . "Sendmail" . $NRM . "\n--------\n";
            print "4.   Sendmail Path         : $WHT$sendmail_path$NRM\n";
            print "5.   Sendmail arguments    : $WHT$sendmail_args$NRM\n";
            print "6.   Header encryption key : $WHT$encode_header_key$NRM\n";
            print "\n";
          } else {
            print $WHT . "SMTP Settings" . $NRM . "\n-------------\n";
            print "4.   SMTP Server           : $WHT$smtpServerAddress$NRM\n";
            print "5.   SMTP Port             : $WHT$smtpPort$NRM\n";
            print "6.   POP before SMTP       : $WHT$pop_before_smtp$NRM\n";
            print "7.   SMTP Authentication   : $WHT$smtp_auth_mech" . display_smtp_sitewide_userpass() . "$NRM\n";
            print "8.   Secure SMTP (TLS)     : $WHT$use_smtp_tls$NRM\n";
            print "9.   Header encryption key : $WHT$encode_header_key$NRM\n";
            print "\n";
          }
        }

        if ($show_imap_settings == 0) {
          print "A.  Update IMAP Settings   : ";
          print "$WHT$imapServerAddress$NRM:";
          print "$WHT$imapPort$NRM ";
          print "($WHT$imap_server_type$NRM)\n";
        } 
        if ($show_smtp_settings == 0) {
          if ( lc($useSendmail) eq "true" ) {
            print "B.  Change Sendmail Config : $WHT$sendmail_path$NRM\n";
          } else {
            print "B.  Update SMTP Settings   : ";
            print "$WHT$smtpServerAddress$NRM:";
            print "$WHT$smtpPort$NRM\n";
          }
        }
        if ( $show_smtp_settings || $show_imap_settings )
        {
          print "H.  Hide " . 
                ($show_imap_settings ? "IMAP Server" : 
                  (lc($useSendmail) eq "true") ? "Sendmail" : "SMTP") . " Settings\n";
        }
        
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 3 ) {
        print $WHT. "Folder Defaults\n" . $NRM;
        print "1.  Default Folder Prefix          : $WHT$default_folder_prefix$NRM\n";
        print "2.  Show Folder Prefix Option      : $WHT$show_prefix_option$NRM\n";
        print "3.  Trash Folder                   : $WHT$trash_folder$NRM\n";
        print "4.  Sent Folder                    : $WHT$sent_folder$NRM\n";
        print "5.  Drafts Folder                  : $WHT$draft_folder$NRM\n";
        print "6.  By default, move to trash      : $WHT$default_move_to_trash$NRM\n";
        print "7.  By default, save sent messages : $WHT$default_move_to_sent$NRM\n";
        print "8.  By default, save as draft      : $WHT$default_save_as_draft$NRM\n";
        print "9.  List Special Folders First     : $WHT$list_special_folders_first$NRM\n";
        print "10. Show Special Folders Color     : $WHT$use_special_folder_color$NRM\n";
        print "11. Auto Expunge                   : $WHT$auto_expunge$NRM\n";
        print "12. Default Sub. of INBOX          : $WHT$default_sub_of_inbox$NRM\n";
        print "13. Show 'Contain Sub.' Option     : $WHT$show_contain_subfolders_option$NRM\n";
        print "14. Default Unseen Notify          : $WHT$default_unseen_notify$NRM\n";
        print "15. Default Unseen Type            : $WHT$default_unseen_type$NRM\n";
        print "16. Auto Create Special Folders    : $WHT$auto_create_special$NRM\n";
        print "17. Folder Delete Bypasses Trash   : $WHT$delete_folder$NRM\n";
        print "18. Enable /NoSelect folder fix    : $WHT$noselect_fix_enable$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 4 ) {
        print $WHT. "General Options\n" . $NRM;
        print "1.  Data Directory               : $WHT$data_dir$NRM\n";
        print "2.  Attachment Directory         : $WHT$attachment_dir$NRM\n";
        print "3.  Directory Hash Level         : $WHT$dir_hash_level$NRM\n";
        print "4.  Default Left Size            : $WHT$default_left_size$NRM\n";
        print "5.  Usernames in Lowercase       : $WHT$force_username_lowercase$NRM\n";
        print "6.  Allow use of priority        : $WHT$default_use_priority$NRM\n";
        print "7.  Hide SM attributions         : $WHT$hide_sm_attributions$NRM\n";
        print "8.  Allow use of receipts        : $WHT$default_use_mdn$NRM\n";
        print "9.  Allow editing of identity    : $WHT$edit_identity$NRM\n";
        print "    Allow editing of name        : $WHT$edit_name$NRM\n";
        print "    Remove username from header  : $WHT$hide_auth_header$NRM\n";
        print "10. Allow server thread sort     : $WHT$allow_thread_sort$NRM\n";
        print "11. Allow server-side sorting    : $WHT$allow_server_sort$NRM\n";
        print "12. Allow server charset search  : $WHT$allow_charset_search$NRM\n";
        print "13. Enable UID support           : $WHT$uid_support$NRM\n";
        print "14. PHP session name             : $WHT$session_name$NRM\n";
        print "15. Location base                : $WHT$config_location_base$NRM\n";
        print "16. Only secure cookies if poss. : $WHT$only_secure_cookies$NRM\n";
        print "17. Disable secure forms         : $WHT$disable_security_tokens$NRM\n";
        print "18. Page referal requirement     : $WHT$check_referrer$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 5 ) {
        print $WHT. "Themes\n" . $NRM;
        print "1.  Change Themes\n";
        for ( $count = 0 ; $count <= $#theme_name/2 ; $count++ ) {
            $temp_name = $theme_name[$count*2];
            printf "     %s%*s    %s\n", $temp_name, 
                   40 - length($temp_name), " ",
                   $theme_name[($count*2)+1];
        }
        print "2.  CSS File : $WHT$theme_css$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 6 ) {
        print $WHT. "Address Books\n" . $NRM;
        print "1.  Change LDAP Servers\n";
        for ( $count = 0 ; $count <= $#ldap_host ; $count++ ) {
            print "    >  $ldap_host[$count]\n";
        }
        print "2.  Use Javascript Address Book Search          : $WHT$default_use_javascript_addr_book$NRM\n";
        print "3.  Global file address book                    : $WHT$abook_global_file$NRM\n";
        print "4.  Allow writing into global file address book : $WHT$abook_global_file_writeable$NRM\n";
        print "5.  Allow listing of global file address book   : $WHT$abook_global_file_listing$NRM\n";
        print "6.  Allowed address book line length            : $WHT$abook_file_line_length$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 7 ) {
        print $WHT. "Message of the Day (MOTD)\n" . $NRM;
        print "\n$motd\n";
        print "\n";
        print "1   Edit the MOTD\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 8 ) {
        print $WHT. "Plugins\n" . $NRM;
        print "  Installed Plugins\n";
        $num = 0;
        for ( $count = 0 ; $count <= $#plugins ; $count++ ) {
            $num = $count + 1;
            print "    $num. $plugins[$count]\n";
        }
        print "\n  Available Plugins:\n";
        opendir( DIR, "../plugins" );
        @files          = sort(readdir(DIR));
        $pos            = 0;
        @unused_plugins = ();
        for ( $i = 0 ; $i <= $#files ; $i++ ) {
            if ( -d "../plugins/" . $files[$i] && $files[$i] !~ /^\./ && $files[$i] ne "CVS" ) {
                $match = 0;
                for ( $k = 0 ; $k <= $#plugins ; $k++ ) {
                    if ( $plugins[$k] eq $files[$i] ) {
                        $match = 1;
                    }
                }
                if ( $match == 0 ) {
                    $unused_plugins[$pos] = $files[$i];
                    $pos++;
                }
            }
        }

        for ( $i = 0 ; $i <= $#unused_plugins ; $i++ ) {
            $num = $num + 1;
            print "    $num. $unused_plugins[$i]\n";
        }
        closedir DIR;

        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 9 ) {
        print $WHT. "Database\n" . $NRM;
        print "1.  DSN for Address Book   : $WHT$addrbook_dsn$NRM\n";
        print "2.  Table for Address Book : $WHT$addrbook_table$NRM\n";
        print "\n";
        print "3.  DSN for Preferences    : $WHT$prefs_dsn$NRM\n";
        print "4.  Table for Preferences  : $WHT$prefs_table$NRM\n";
        print "5.  Field for username     : $WHT$prefs_user_field$NRM\n";
        print "6.  Field for prefs key    : $WHT$prefs_key_field$NRM\n";
        print "7.  Field for prefs value  : $WHT$prefs_val_field$NRM\n";
        print "\n";
        print "8.  DSN for Global Address Book            : $WHT$addrbook_global_dsn$NRM\n";
        print "9.  Table for Global Address Book          : $WHT$addrbook_global_table$NRM\n";
        print "10. Allow writing into Global Address Book : $WHT$addrbook_global_writeable$NRM\n";
        print "11. Allow listing of Global Address Book   : $WHT$addrbook_global_listing$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    } elsif ( $menu == 10 ) {
        print $WHT. "Language preferences\n" . $NRM;
        print "1.  Default Language       : $WHT$squirrelmail_default_language$NRM\n";
        print "2.  Default Charset        : $WHT$default_charset$NRM\n";
        print "3.  Enable lossy encoding  : $WHT$lossy_encoding$NRM\n";
        print "\n";
        print "R   Return to Main Menu\n";
    }
    if ( $config_use_color == 1 ) {
        print "C   Turn color off\n";
    } else {
        print "C   Turn color on\n";
    }
    print "S   Save data\n";
    print "Q   Quit\n";

    print "\n";
    print "Command >> " . $WHT;
    $command = <STDIN>;
    $command =~ s/[\n|\r]//g;
    $command =~ tr/A-Z/a-z/;
    print "$NRM\n";

    # Read the commands they entered.
    if ( $command eq "r" ) {
        $menu = 0;
    } elsif ( $command eq "s" ) {
        save_data();
        print "Press enter to continue...";
        $tmp   = <STDIN>;
        $saved = 1;
    } elsif ( ( $command eq "q" ) && ( $saved == 0 ) ) {
        print "You have not saved your data.\n";
        print "Save?  [" . $WHT . "Y" . $NRM . "/n]: ";
        $save = <STDIN>;
        if ( ( $save =~ /^y/i ) || ( $save =~ /^\s*$/ ) ) {
            save_data();
        }
    } elsif ( $command eq "c" ) {
        if ( $config_use_color == 1 ) {
            $config_use_color = 2;
            $WHT              = "";
            $NRM              = "";
        } else {
            $config_use_color = 1;
            $WHT              = "\x1B[1m";
            $NRM              = "\x1B[0m";
        }
    } elsif ( $command eq "d" && $menu == 0 ) {
        set_defaults();
    } else {
        $saved = 0;
        if ( $menu == 0 ) {
            if ( ( $command > 0 ) && ( $command < 11 ) ) {
                $menu = $command;
            }
        } elsif ( $menu == 1 ) {
            if    ( $command == 1 ) { $org_name                      = command1(); }
            elsif ( $command == 2 ) { $org_logo                      = command2(); }
            elsif ( $command == 3 ) { ($org_logo_width,$org_logo_height)  = command3(); }
            elsif ( $command == 4 ) { $org_title                     = command4(); }
            elsif ( $command == 5 ) { $signout_page                  = command5(); }
            elsif ( $command == 6 ) { $frame_top                     = command6(); }
            elsif ( $command == 7 ) { $provider_uri                  = command7(); }
            elsif ( $command == 8 ) { $provider_name                 = command8(); }

        } elsif ( $menu == 2 ) {
            if ( $command eq "a" )    { $show_imap_settings = 1; $show_smtp_settings = 0; }
            elsif ( $command eq "b" ) { $show_imap_settings = 0; $show_smtp_settings = 1; }
            elsif ( $command eq "h" ) { $show_imap_settings = 0; $show_smtp_settings = 0; }
            elsif ( $command <= 3 ) {
              if    ( $command == 1 )  { $domain                 = command11(); }
              elsif ( $command == 2 )  { $invert_time            = command110(); }
              elsif ( $command == 3 )  { $useSendmail            = command14(); }
              $show_imap_settings = 0; $show_smtp_settings = 0;
            } elsif ( $show_imap_settings ) {
              if    ( $command == 4 )  { $imapServerAddress      = command12(); }
              elsif ( $command == 5 )  { $imapPort               = command13(); }
              elsif ( $command == 6 )  { $imap_auth_mech     = command112a(); }
              elsif ( $command == 7 )  { $use_imap_tls       = command113("IMAP",$use_imap_tls); }
              elsif ( $command == 8 )  { $imap_server_type       = command19(); }
              elsif ( $command == 9 )  { $optional_delimiter     = command111(); }
            } elsif ( $show_smtp_settings && lc($useSendmail) eq "true" ) {
              if ( $command == 4 )  { $sendmail_path          = command15(); }
              elsif ( $command == 5 )  { $sendmail_args          = command_sendmail_args(); }
              elsif ( $command == 6 )  { $encode_header_key      = command114(); }
            } elsif ( $show_smtp_settings ) {
              if    ( $command == 4 )  { $smtpServerAddress      = command16(); }
              elsif ( $command == 5 )  { $smtpPort               = command17(); }
              elsif ( $command == 6 )  { $pop_before_smtp        = command18a(); }
              elsif ( $command == 7 )  { $smtp_auth_mech    = command112b(); }
              elsif ( $command == 8 )  { $use_smtp_tls      = command113("SMTP",$use_smtp_tls); }
              elsif ( $command == 9 )  { $encode_header_key      = command114(); }
            }
        } elsif ( $menu == 3 ) {
            if    ( $command == 1 )  { $default_folder_prefix          = command21(); }
            elsif ( $command == 2 )  { $show_prefix_option             = command22(); }
            elsif ( $command == 3 )  { $trash_folder                   = command23a(); }
            elsif ( $command == 4 )  { $sent_folder                    = command23b(); }
            elsif ( $command == 5 )  { $draft_folder                   = command23c(); }
            elsif ( $command == 6 )  { $default_move_to_trash          = command24a(); }
            elsif ( $command == 7 )  { $default_move_to_sent           = command24b(); }
            elsif ( $command == 8 )  { $default_save_as_draft          = command24c(); }
            elsif ( $command == 9 )  { $list_special_folders_first     = command27(); }
            elsif ( $command == 10 ) { $use_special_folder_color       = command28(); }
            elsif ( $command == 11 ) { $auto_expunge                   = command29(); }
            elsif ( $command == 12 ) { $default_sub_of_inbox           = command210(); }
            elsif ( $command == 13 ) { $show_contain_subfolders_option = command211(); }
            elsif ( $command == 14 ) { $default_unseen_notify          = command212(); }
            elsif ( $command == 15 ) { $default_unseen_type            = command213(); }
            elsif ( $command == 16 ) { $auto_create_special            = command214(); }
            elsif ( $command == 17 ) { $delete_folder                  = command215(); }
            elsif ( $command == 18 ) { $noselect_fix_enable            = command216(); }
        } elsif ( $menu == 4 ) {
            if    ( $command == 1 )  { $data_dir                 = command31(); }
            elsif ( $command == 2 )  { $attachment_dir           = command32(); }
            elsif ( $command == 3 )  { $dir_hash_level           = command33(); }
            elsif ( $command == 4 )  { $default_left_size        = command34(); }
            elsif ( $command == 5 )  { $force_username_lowercase = command35(); }
            elsif ( $command == 6 )  { $default_use_priority     = command36(); }
            elsif ( $command == 7 )  { $hide_sm_attributions     = command37(); }
            elsif ( $command == 8 )  { $default_use_mdn          = command38(); }
            elsif ( $command == 9 )  { $edit_identity            = command39(); }
            elsif ( $command == 10 ) { $allow_thread_sort        = command310(); }
            elsif ( $command == 11 ) { $allow_server_sort        = command311(); }
            elsif ( $command == 12 ) { $allow_charset_search     = command312(); }
            elsif ( $command == 13 ) { $uid_support              = command313(); }
            elsif ( $command == 14 ) { $session_name             = command314(); }
            elsif ( $command == 15 ) { $config_location_base     = command_config_location_base(); }
            elsif ( $command == 16 ) { $only_secure_cookies      = command316(); }
            elsif ( $command == 17 ) { $disable_security_tokens  = command317(); }
            elsif ( $command == 18 ) { $check_referrer           = command318(); }
        } elsif ( $menu == 5 ) {
            if ( $command == 1 ) { command41(); }
            elsif ( $command == 2 ) { $theme_css = command42(); }
        } elsif ( $menu == 6 ) {
            if    ( $command == 1 ) { command61(); }
            elsif ( $command == 2 ) { command62(); }
            elsif ( $command == 3 ) { $abook_global_file=command63(); }
            elsif ( $command == 4 ) { command64(); }
            elsif ( $command == 5 ) { command_abook_global_file_listing(); }
            elsif ( $command == 6 ) { command_abook_file_line_length(); }
        } elsif ( $menu == 7 ) {
            if ( $command == 1 ) { $motd = command71(); }
        } elsif ( $menu == 8 ) {
            if ( $command =~ /^[0-9]+/ ) { @plugins = command81(); }
        } elsif ( $menu == 9 ) {
            if    ( $command == 1 ) { $addrbook_dsn     = command91(); }
            elsif ( $command == 2 ) { $addrbook_table   = command92(); }
            elsif ( $command == 3 ) { $prefs_dsn        = command93(); }
            elsif ( $command == 4 ) { $prefs_table      = command94(); }
            elsif ( $command == 5 ) { $prefs_user_field = command95(); }
            elsif ( $command == 6 ) { $prefs_key_field  = command96(); }
            elsif ( $command == 7 ) { $prefs_val_field  = command97(); }
            elsif ( $command == 8 ) { $addrbook_global_dsn       = command98(); }
            elsif ( $command == 9 ) { $addrbook_global_table     = command99(); }
            elsif ( $command == 10 ) { $addrbook_global_writeable = command910(); }
            elsif ( $command == 11 ) { $addrbook_global_listing  = command911(); }
        } elsif ( $menu == 10 ) {
            if    ( $command == 1 ) { $squirrelmail_default_language = commandA1(); }
            elsif ( $command == 2 ) { $default_charset  = commandA2(); }
            elsif ( $command == 3 ) { $lossy_encoding   = commandA3(); }
        }
    }
}

# we exit here
print "\nExiting conf.pl.\n".
    "You might want to test your configuration by browsing to\n".
    "http://your-squirrelmail-location/src/configtest.php\n".
    "Happy SquirrelMailing!\n\n";


####################################################################################
#### Organization preferences ####
# org_name
sub command1 {
    print "We have tried to make the name SquirrelMail as transparent as\n";
    print "possible.  If you set up an organization name, most places where\n";
    print "SquirrelMail would take credit will be credited to your organization.\n";
    print "\n";
    print "If your Organization Name includes a '\$', please precede it with a \\. \n";
    print "Other '\$' will be considered the beginning of a variable that\n";
    print "must be defined before the \$org_name is printed.\n";
    print "\$version, for example, is included by default, and will print the\n";
    print "string representing the current SquirrelMail version.\n";
    print "\n";
    print "[$WHT$org_name$NRM]: $WHT";
    $new_org_name = <STDIN>;
    if ( $new_org_name eq "\n" ) {
        $new_org_name = $org_name;
    } else {
        $new_org_name =~ s/[\r|\n]//g;
        $new_org_name =~ s/\"/&quot;/g;
    }
    return $new_org_name;
}

# org_logo
sub command2 {
    print "Your organization's logo is an image that will be displayed at\n";
    print "different times throughout SquirrelMail. ";
    print "\n";
    print "Please be aware of the following: \n";
    print "  - Relative URLs are relative to the config dir\n";
    print "    to use the default logo, use ../images/sm_logo.png\n";
    print "  - To specify a logo defined outside the SquirrelMail source tree\n";
    print "    use the absolute URL the webserver would use to include the file\n";
    print "    e.g. http://example.com/images/mylogo.gif or /images/mylogo.jpg\n";
    print "\n";
    print "[$WHT$org_logo$NRM]: $WHT";
    $new_org_logo = <STDIN>;
    if ( $new_org_logo eq "\n" ) {
        $new_org_logo = $org_logo;
    } else {
        $new_org_logo =~ s/[\r|\n]//g;
    }
    return $new_org_logo;
}

# org_logo_width
sub command3 {
    print "Your organization's logo is an image that will be displayed at\n";
    print "different times throughout SquirrelMail.  Width\n";
    print "and Height of your logo image.  Use '0' to disable.\n";
    print "\n";
    print "Width: [$WHT$org_logo_width$NRM]: $WHT";
    $new_org_logo_width = <STDIN>;
    $new_org_logo_width =~ tr/0-9//cd;  # only want digits!
    if ( $new_org_logo_width eq '' ) {
        $new_org_logo_width = $org_logo_width;
    }
    if ( $new_org_logo_width > 0 ) {
        print "Height: [$WHT$org_logo_height$NRM]: $WHT";
        $new_org_logo_height = <STDIN>;
        $new_org_logo_height =~ tr/0-9//cd;  # only want digits!
        if( $new_org_logo_height eq '' ) {
            $new_org_logo_height = $org_logo_height;
        }
    } else {
        $new_org_logo_height = 0;
    }
    return ($new_org_logo_width, $new_org_logo_height);
}

# org_title
sub command4 {
    print "A title is what is displayed at the top of the browser window in\n";
    print "the titlebar.  Usually this will end up looking something like:\n";
    print "\"Netscape: $org_title\"\n";
    print "\n";
    print "If your Organization Title includes a '\$', please precede it with a \\. \n";
    print "Other '\$' will be considered the beginning of a variable that\n";
    print "must be defined before the \$org_title is printed.\n";
    print "\$version, for example, is included by default, and will print the\n";
    print "string representing the current SquirrelMail version.\n";
    print "\n";
    print "[$WHT$org_title$NRM]: $WHT";
    $new_org_title = <STDIN>;
    if ( $new_org_title eq "\n" ) {
        $new_org_title = $org_title;
    } else {
        $new_org_title =~ s/[\r|\n]//g;
        $new_org_title =~ s/\"/\'/g;
    }
    return $new_org_title;
}

# signout_page
sub command5 {
    print "When users click the Sign Out button they will be logged out and\n";
    print "then sent to signout_page.  If signout_page is left empty,\n";
    print "(hit space and then return) they will be taken, as normal,\n";
    print "to the default and rather sparse SquirrelMail signout page.\n";
    print "\n";
    print "[$WHT$signout_page$NRM]: $WHT";
    $new_signout_page = <STDIN>;
    if ( $new_signout_page eq "\n" ) {
        $new_signout_page = $signout_page;
    } else {
        $new_signout_page =~ s/[\r|\n]//g;
        $new_signout_page =~ s/^\s+$//g;
    }
    return $new_signout_page;
}

# Default top frame
sub command6 {
    print "SquirrelMail defaults to using the whole of the browser window.\n";
    print "This allows you to keep it within a specified frame. The default\n";
    print "is '_top'\n";
    print "\n";
    print "[$WHT$frame_top$NRM]: $WHT";
    $new_frame_top = <STDIN>;
    if ( $new_frame_top eq "\n" ) {
        $new_frame_top = '_top';
    } else {
        $new_frame_top =~ s/[\r|\n]//g;
        $new_frame_top =~ s/^\s+$//g;
    }
    return $new_frame_top;
}

# Default link to provider
sub command7 {
    print "Here you can set the link on the right of the page.\n";
    print "The default is 'http://squirrelmail.org/'\n";
    print "\n";
    print "[$WHT$provider_uri$NRM]: $WHT";
    $new_provider_uri = <STDIN>;
    if ( $new_provider_uri eq "\n" ) {
        $new_provider_uri = 'http://squirrelmail.org/';
    } else {
        $new_provider_uri =~ s/[\r|\n]//g;
        $new_provider_uri =~ s/^\s+$//g;
    }
    return $new_provider_uri;
}

sub command8 {
    print "Here you can set the name of the link on the right of the page.\n";
    print "The default is 'SquirrelMail'\n";
    print "\n";
    print "[$WHT$provider_name$NRM]: $WHT";
    $new_provider_name = <STDIN>;
    if ( $new_provider_name eq "\n" ) {
        $new_provider_name = 'SquirrelMail';
    } else {
        $new_provider_name =~ s/[\r|\n]//g;
        $new_provider_name =~ s/^\s+$//g;
        $new_provider_name =~ s/\'/\\'/g;
    }
    return $new_provider_name;
}

####################################################################################
#### Server settings ####
# domain
sub command11 {
    print "The domain name is the suffix at the end of all email addresses.  If\n";
    print "for example, your email address is jdoe\@example.com, then your domain\n";
    print "would be example.com.\n";
    print "\n";
    print "[$WHT$domain$NRM]: $WHT";
    $new_domain = <STDIN>;
    if ( $new_domain eq "\n" ) {
        $new_domain = $domain;
    } else {
        $new_domain =~ s/\s//g;
    }
    return $new_domain;
}

# imapServerAddress
sub command12 {
    print "This is the hostname where your IMAP server can be contacted.\n";
    print "[$WHT$imapServerAddress$NRM]: $WHT";
    $new_imapServerAddress = <STDIN>;
    if ( $new_imapServerAddress eq "\n" ) {
        $new_imapServerAddress = $imapServerAddress;
    } else {
        $new_imapServerAddress =~ s/[\r|\n]//g;
    }
    return $new_imapServerAddress;
}

# imapPort
sub command13 {
    print "This is the port that your IMAP server is on.  Usually this is 143.\n";
    print "[$WHT$imapPort$NRM]: $WHT";
    $new_imapPort = <STDIN>;
    if ( $new_imapPort eq "\n" ) {
        $new_imapPort = $imapPort;
    } else {
        $new_imapPort =~ s/[\r|\n]//g;
    }
    return $new_imapPort;
}

# useSendmail
sub command14 {
    print "You now need to choose the method that you will use for sending\n";
    print "messages in SquirrelMail.  You can either connect to an SMTP server\n";
    print "or use sendmail directly.\n";
    if ( lc($useSendmail) eq "true" ) {
        $default_value = "1";
    } else {
        $default_value = "2";
    }
    print "\n";
    print "  1.  Sendmail\n";
    print "  2.  SMTP\n";
    print "Your choice [1/2] [$WHT$default_value$NRM]: $WHT";
    $use_sendmail = <STDIN>;
    if ( ( $use_sendmail =~ /^1\n/i )
        || ( ( $use_sendmail =~ /^\n/ ) && ( $default_value eq "1" ) ) ) {
        $useSendmail = "true";
        } else {
        $useSendmail = "false";
        }
    return $useSendmail;
}

# sendmail_path
sub command15 {
    print "Specify where the sendmail executable is located.  Usually /usr/sbin/sendmail\n";
    print "[$WHT$sendmail_path$NRM]: $WHT";
    $new_sendmail_path = <STDIN>;
    if ( $new_sendmail_path eq "\n" ) {
        $new_sendmail_path = $sendmail_path;
    } else {
        $new_sendmail_path =~ s/[\r|\n]//g;
    }
    return $new_sendmail_path;
}

# Extra sendmail arguments
sub command_sendmail_args {
    print "Specify additional sendmail program arguments.\n";
    print "\n";
    print "Make sure that arguments are supported by your sendmail program. -f argument \n";
    print "is added automatically by SquirrelMail scripts. Variable defaults to standard\n";
    print "/usr/sbin/sendmail arguments. If you use qmail-inject, nbsmtp or any other \n";
    print "sendmail wrapper, which does not support -i and -t arguments, set variable to\n";
    print "empty string or use arguments suitable for your mailer.\n";
    print "\n";
    print "[$WHT$sendmail_args$NRM]: $WHT";
    $new_sendmail_args = <STDIN>;
    if ( $new_sendmail_args eq "\n" ) {
        $new_sendmail_args = $sendmail_args;
    } else {
        # strip linefeeds and crs.
        $new_sendmail_args =~ s/[\r\n]//g;
    }
    return trim($new_sendmail_args);
}

# smtpServerAddress
sub command16 {
    print "This is the hostname of your SMTP server.\n";
    print "[$WHT$smtpServerAddress$NRM]: $WHT";
    $new_smtpServerAddress = <STDIN>;
    if ( $new_smtpServerAddress eq "\n" ) {
        $new_smtpServerAddress = $smtpServerAddress;
    } else {
        $new_smtpServerAddress =~ s/[\r|\n]//g;
    }
    return $new_smtpServerAddress;
}

# smtpPort
sub command17 {
    print "This is the port to connect to for SMTP.  Usually 25.\n";
    print "[$WHT$smtpPort$NRM]: $WHT";
    $new_smtpPort = <STDIN>;
    if ( $new_smtpPort eq "\n" ) {
        $new_smtpPort = $smtpPort;
    } else {
        $new_smtpPort =~ s/[\r|\n]//g;
    }
    return $new_smtpPort;
}

# authenticated server 
sub command18 {
    return;
    # This sub disabled by tassium - it has been replaced with smtp_auth_mech
    print "Do you wish to use an authenticated SMTP server?  Your server must\n";
    print "support this in order for SquirrelMail to work with it.  We implemented\n";
    print "it according to RFC 2554.\n";

    $YesNo = 'n';
    $YesNo = 'y' if ( lc($use_authenticated_smtp) eq "true" );

    print "Use authenticated SMTP server (y/n) [$WHT$YesNo$NRM]: $WHT";

    $new_use_authenticated_smtp = <STDIN>;
    $new_use_authenticated_smtp =~ tr/yn//cd;
    return "true"  if ( $new_use_authenticated_smtp eq "y" );
    return "false" if ( $new_use_authenticated_smtp eq "n" );
    return $use_authenticated_smtp;
}

# pop before SMTP
sub command18a {
    print "Do you wish to use POP3 before SMTP?  Your server must\n";
    print "support this in order for SquirrelMail to work with it.\n";

    $YesNo = 'n';
    $YesNo = 'y' if ( lc($pop_before_smtp) eq "true" );

    print "Use pop before SMTP (y/n) [$WHT$YesNo$NRM]: $WHT";

    $new_pop_before_smtp = <STDIN>;
    $new_pop_before_smtp =~ tr/yn//cd;
    if ( $new_pop_before_smtp eq "y" ) {
        $new_pop_before_smtp = "true";
    } elsif ( $new_pop_before_smtp eq "n" ) {
        $new_pop_before_smtp = "false";
    } else {
        $new_pop_before_smtp = $pop_before_smtp;
    }

    # if using POP before SMTP, allow setting of custom POP server address
    if ($new_pop_before_smtp eq "true") {
        print "$NRM\nIf the address of the POP server is not the same as\n";
        print "your SMTP server, you may specify it here. Leave blank (to\n";
        print "clear this, enter only spaces) to use the same address as\n";
        print "your SMTP server.\n";
        print "POP before SMTP server address [$WHT$pop_before_smtp_host$NRM]: $WHT";

        $new_pop_before_smtp_host = <STDIN>;
        if ( $new_pop_before_smtp_host eq "\n" ) {
            $new_pop_before_smtp_host = $pop_before_smtp_host;
        } elsif ($new_pop_before_smtp_host =~ /^\s+$/) {
            $new_pop_before_smtp_host = '';
        } else {
            $new_pop_before_smtp_host =~ s/[\r|\n]//g;
        }
        $pop_before_smtp_host = $new_pop_before_smtp_host;
    }

    return $new_pop_before_smtp;
}

# imap_server_type 
sub command19 {
    print "Each IMAP server has its own quirks.  As much as we tried to stick\n";
    print "to standards, it doesn't help much if the IMAP server doesn't follow\n";
    print "the same principles.  We have made some work-arounds for some of\n";
    print "these servers.  If you would like to use them, please select your\n";
    print "IMAP server.  If you do not wish to use these work-arounds, you can\n";
    print "set this to \"other\", and none will be used.\n";
    print $list_supported_imap_servers;
    print "    other       = Not one of the above servers\n";
    print "[$WHT$imap_server_type$NRM]: $WHT";
    $new_imap_server_type = <STDIN>;

    if ( $new_imap_server_type eq "\n" ) {
        $new_imap_server_type = $imap_server_type;
    } else {
        $new_imap_server_type =~ s/[\r|\n]//g;
    }
    return $new_imap_server_type;
}

# invert_time
sub command110 {
    print "Sometimes the date of messages sent is messed up (off by a few hours\n";
    print "on some machines).  Typically this happens if the system doesn't support\n";
    print "tm_gmtoff.  It will happen only if your time zone is \"negative\".\n";
    print "This most often occurs on Solaris 7 machines in the United States.\n";
    print "By default, this is off.  It should be kept off unless problems surface\n";
    print "about the time that messages are sent.\n";
    print "    no  = Do NOT fix time -- almost always correct\n";
    print "    yes = Fix the time for this system\n";

    $YesNo = 'n';
    $YesNo = 'y' if ( lc($invert_time) eq "true" );

    print "Fix the time for this system (y/n) [$WHT$YesNo$NRM]: $WHT";

    $new_invert_time = <STDIN>;
    $new_invert_time =~ tr/yn//cd;
    return "true"  if ( $new_invert_time eq "y" );
    return "false" if ( $new_invert_time eq "n" );
    return $invert_time;
}

sub command111 {
    print "This is the delimiter that your IMAP server uses to distinguish between\n";
    print "folders.  For example, Cyrus uses '.' as the delimiter and a complete\n";
    print "folder would look like 'INBOX.Friends.Bob', while UW uses '/' and would\n";
    print "look like 'INBOX/Friends/Bob'.  Normally this should be left at 'detect'\n";
    print "but if you are sure you know what delimiter your server uses, you can\n";
    print "specify it here.\n";
    print "\nTo have it autodetect the delimiter, set it to 'detect'.\n\n";
    print "[$WHT$optional_delimiter$NRM]: $WHT";
    $new_optional_delimiter = <STDIN>;

    if ( $new_optional_delimiter eq "\n" ) {
        $new_optional_delimiter = $optional_delimiter;
    } else {
        $new_optional_delimiter =~ s/[\r|\n]//g;
    }
    return $new_optional_delimiter;
}
# IMAP authentication type
# Possible values: login, cram-md5, digest-md5
# Now offers to detect supported mechs, assuming server & port are set correctly

sub command112a {
    if ($use_imap_tls =~ /^true\b/i) {
        print "Auto-detection of login methods is unavailable when using TLS.\n";
    } else {
        print "If you have already set the hostname and port number, I can try to\n";
        print "detect the mechanisms your IMAP server supports.\n";
        print "I will try to detect CRAM-MD5 and DIGEST-MD5 support.  I can't test\n";
        print "for \"login\" without knowing a username and password.\n";
        print "Auto-detecting is optional - you can safely say \"n\" here.\n";
        print "\nTry to detect supported mechanisms? [y/N]: ";
        $inval=<STDIN>;
        chomp($inval);
        if ($inval =~ /^y\b/i) {
          # Yes, let's try to detect.
          print "Trying to detect IMAP capabilities...\n";
          my $host = $imapServerAddress . ':'. $imapPort;
          print "CRAM-MD5:\t";
          my $tmp = detect_auth_support('IMAP',$host,'CRAM-MD5');
          if (defined($tmp)) {
              if ($tmp eq 'YES') {
                  print "$WHT SUPPORTED$NRM\n";
              } else {
                print "$WHT NOT SUPPORTED$NRM\n";
              }
          } else {
            print $WHT . " ERROR DETECTING$NRM\n";
          }

          print "DIGEST-MD5:\t";
          $tmp = detect_auth_support('IMAP',$host,'DIGEST-MD5');
          if (defined($tmp)) {
              if ($tmp eq 'YES') {
                print "$WHT SUPPORTED$NRM\n";
            } else {
                print "$WHT NOT SUPPORTED$NRM\n";
            }
          } else {
            print $WHT . " ERROR DETECTING$NRM\n";
          }
      
        } 
    }
      print "\nWhat authentication mechanism do you want to use for IMAP connections?\n\n";
      print $WHT . "login" . $NRM . " - Plaintext. If you can do better, you probably should.\n";
      print $WHT . "cram-md5" . $NRM . " - Slightly better than plaintext methods.\n";
      print $WHT . "digest-md5" . $NRM . " - Privacy protection - better than cram-md5.\n";
      print "\n*** YOUR IMAP SERVER MUST SUPPORT THE MECHANISM YOU CHOOSE HERE ***\n";
      print "If you don't understand or are unsure, you probably want \"login\"\n\n";
      print "login, cram-md5, or digest-md5 [$WHT$imap_auth_mech$NRM]: $WHT";
      $inval=<STDIN>;
      chomp($inval);
      if ( ($inval =~ /^cram-md5\b/i) || ($inval =~ /^digest-md5\b/i) || ($inval =~ /^login\b/i)) {
        return lc($inval);
      } else {
        # user entered garbage or default value so nothing needs to be set
        return $imap_auth_mech;
      }
}

    
# SMTP authentication type
# Possible choices: none, login, plain, cram-md5, digest-md5
sub command112b {
    if ($use_smtp_tls =~ /^true\b/i) {
        print "Auto-detection of login methods is unavailable when using TLS.\n";
    } elsif (eval ("use IO::Socket; 1")) {
        print "If you have already set the hostname and port number, I can try to\n";
        print "automatically detect the mechanisms your SMTP server supports.\n";
        print "Auto-detection is *optional* - you can safely say \"n\" here.\n";
        print "\nTry to detect auth mechanisms? [y/N]: ";
        $inval=<STDIN>;
        chomp($inval);
        if ($inval =~ /^y\b/i) {
            # Yes, let's try to detect.
            print "Trying to detect supported methods (SMTP)...\n";
        
            # Special case!
            # Check none by trying to relay to junk@microsoft.com
            $host = $smtpServerAddress . ':' . $smtpPort;
            my $sock = IO::Socket::INET->new($host);
            print "Testing none:\t\t$WHT";
            if (!defined($sock)) {
                print " ERROR TESTING\n";
                close $sock;
            } else {
                $got = <$sock>;  # Discard greeting
                print $sock "HELO $domain\r\n";
                $got = <$sock>;  # Discard
                print $sock "MAIL FROM:<tester\@squirrelmail.org>\r\n";
                $got = <$sock>;  # Discard
                print $sock "RCPT TO:<junk\@microsoft.com>\r\n";
                $got = <$sock>;  # This is the important line
                if ($got =~ /^250\b/) {  # SMTP will relay without auth
                    print "SUPPORTED$NRM\n";
                } else {
                  print "NOT SUPPORTED$NRM\n";
                }
                print $sock "RSET\r\n";
                print $sock "QUIT\r\n";
                close $sock;
            }
            # Try login (SquirrelMail default)
            print "Testing login:\t\t";
            $tmp=detect_auth_support('SMTP',$host,'LOGIN');
            if (defined($tmp)) {
                if ($tmp eq 'YES') {
                    print $WHT . "SUPPORTED$NRM\n";
                } else {
                    print $WHT . "NOT SUPPORTED$NRM\n";
                }
              } else {
                  print $WHT . "ERROR DETECTING$NRM\n";
              }

            # Try plain
            print "Testing plain:\t\t";
            $tmp=detect_auth_support('SMTP',$host,'PLAIN');
            if (defined($tmp)) {
                if ($tmp eq 'YES') {
                    print $WHT . "SUPPORTED$NRM\n";
                } else {
                    print $WHT . "NOT SUPPORTED$NRM\n";
                }
              } else {
                  print $WHT . "ERROR DETECTING$NRM\n";
              }
    
            # Try CRAM-MD5
            print "Testing CRAM-MD5:\t";
            $tmp=detect_auth_support('SMTP',$host,'CRAM-MD5');
            if (defined($tmp)) {
                if ($tmp eq 'YES') {
                    print $WHT . "SUPPORTED$NRM\n";
                } else {
                    print $WHT . "NOT SUPPORTED$NRM\n";
                }
              } else {
                  print $WHT . "ERROR DETECTING$NRM\n";
            }
    

            print "Testing DIGEST-MD5:\t";
            $tmp=detect_auth_support('SMTP',$host,'DIGEST-MD5');
            if (defined($tmp)) {
                if ($tmp eq 'YES') {
                    print $WHT . "SUPPORTED$NRM\n";
                } else {
                    print $WHT . "NOT SUPPORTED$NRM\n";
                }
              } else {
                  print $WHT . "ERROR DETECTING$NRM\n";
            }
        } 
    }
    print "\nWhat authentication mechanism do you want to use for SMTP connections?\n";
    print $WHT . "none" . $NRM . " - Your SMTP server does not require authorization.\n";
    print $WHT . "login" . $NRM . " - Plaintext. If you can do better, you probably should.\n";
    print $WHT . "plain" . $NRM . " - Plaintext. If you can do better, you probably should.\n";
    print $WHT . "cram-md5" . $NRM . " - Slightly better than plaintext.\n";
    print $WHT . "digest-md5" . $NRM . " - Privacy protection - better than cram-md5.\n";
    print $WHT . "\n*** YOUR SMTP SERVER MUST SUPPORT THE MECHANISM YOU CHOOSE HERE ***\n" . $NRM;
    print "If you don't understand or are unsure, you probably want \"none\"\n\n";
    print "none, login, plain, cram-md5, or digest-md5 [$WHT$smtp_auth_mech$NRM]: $WHT";
    $inval=<STDIN>;
    chomp($inval);
    if ($inval =~ /^none\b/i) {
      # SMTP doesn't necessarily require logins
      $smtp_sitewide_user = '';
      $smtp_sitewide_pass = '';
      return "none";
    }
    if ( ($inval =~ /^cram-md5\b/i) || ($inval =~ /^digest-md5\b/i) || 
    ($inval =~ /^login\b/i) || ($inval =~ /^plain\b/i)) {
      command_smtp_sitewide_userpass($inval);
      return lc($inval);
    } elsif (trim($inval) eq '') {
      command_smtp_sitewide_userpass($smtp_auth_mech);
      return $smtp_auth_mech;
    } else {
      # user entered garbage
      return $smtp_auth_mech;
    }
}

sub command_smtp_sitewide_userpass($) {
    # get first function argument
    my $auth_mech = shift(@_);
    my $default, $tmp;
    $auth_mech = lc(trim($auth_mech));
    if ($auth_mech eq 'none') {
        return;
    }
    print "SMTP authentication uses IMAP username and password by default.\n";
    print "\n";
    print "Would you like to use other login and password for all SquirrelMail \n";
    print "SMTP connections?";
    if ($smtp_sitewide_user ne '') {
        $default = 'y';
        print " [Y/n]:";
    } else {
        $default = 'n';
        print " [y/N]:";
    }
    $tmp=<STDIN>;
    $tmp = trim($tmp);
    
    if ($tmp eq '') {
        $tmp = $default;
    } else {
        $tmp = lc($tmp);
    }

    if ($tmp eq 'n') {
        $smtp_sitewide_user = '';
        $smtp_sitewide_pass = '';
    } elsif ($tmp eq 'y') {
        print "Enter username [$smtp_sitewide_user]:";
        my $new_user = <STDIN>;
        $new_user = trim($new_user);
        if ($new_user ne '') {
            $smtp_sitewide_user = $new_user;
        }
        if ($smtp_sitewide_user ne '') {
            print "If you don't enter any password, current sitewide password will be used.\n";
            print "If you enter space, password will be set to empty string.\n";
            print "Enter password:";
            my $new_pass = <STDIN>;
            if ($new_pass ne "\n") {
                $smtp_sitewide_pass = trim($new_pass);
            }
        } else {
            print "Invalid input. You must set username used for SMTP authentication.\n";
            print "Press enter to continue\n";
            $tmp = <STDIN>;
        }
    } else {
        print "Invalid input\n";
        print "Press enter to continue\n";
        $tmp = <STDIN>;
    }
}

# Sub adds information about SMTP authentication type to menu
sub display_smtp_sitewide_userpass() {
    my $ret = '';
    if ($smtp_auth_mech ne 'none') {
        if ($smtp_sitewide_user ne '') {
            $ret = ' (with custom username and password)';
        } else {
            $ret = ' (with IMAP username and password)';
        }
    }
    return $ret;
}


# TLS
# This sub is reused for IMAP and SMTP
# Args: service name, default value
sub command113 {
    my($default_val,$service,$inval);
    $service=$_[0];
    $default_val=$_[1];
    print "TLS (Transport Layer Security) encrypts the traffic between server and client.\n";
    print "If you're familiar with SSL, you get the idea.\n";
    print "To use this feature, your " . $service . " server must offer TLS\n";
    print "capability, plus PHP 4.3.x with OpenSSL support.\n";
    print "Note that the 'STARTTLS' command is not supported; the server must\n";
    print "have a dedicated port listening for TLS connections.\n";
    print "\nIf your " . $service . " server is localhost, you can safely disable this.\n";
    print "If it is remote, you may wish to seriously consider enabling this.\n";
    print "Enable TLS (y/n) [$WHT";
    if ($default_val eq "true") {
      print "y";
    } else {
      print "n";
    }
    print "$NRM]: $WHT"; 
    $inval=<STDIN>;
    $inval =~ tr/yn//cd;
    return "true"  if ( $inval eq "y" );
    return "false" if ( $inval eq "n" );
    return $default_val;
}

# $encode_header_key
sub command114{
    print "This encryption key allows the hiding of SquirrelMail Received:\n";
    print "headers in outbound messages.  SquirrelMail uses the encryption\n";
    print "key to encode the username, remote address, and proxied address\n";
    print "and then stores that encoded information in X-Squirrel-* headers.\n";
    print "\n";
    print "Warning: the encryption function used to accomplish this is not\n";
    print "bulletproof. When used with a static encryption key as it is here,\n";
    print "it provides only minimal security and the encoded user information\n";
    print "in the X-Squirrel-* headers can be decoded quickly by a skilled\n";
    print "attacker.\n";
    print "\n";
    print "When you need to inspect an email sent from your system with the\n";
    print "X-Squirrel-* headers, you can decode the user information therein\n";
    print "by using the decrypt_headers.php script found in the SquirrelMail\n";
    print "contrib/ directory. You'll need the encryption key that you\n";
    print "defined here when doing so.\n";
    print "\n";
    print "Enter encryption key: ";
    $new_encode_header_key = <STDIN>;
    if ( $new_encode_header_key eq "\n" ) {
        $new_encode_header_key = $encode_header_key;
    } else {
        $new_encode_header_key =~ s/[\r\n]//g;
    }
    return $new_encode_header_key;
}

####################################################################################
#### MOTD ####
sub command71 {
    print "\nYou can now create the welcome message that is displayed\n";
    print "every time a user logs on.  You can use HTML or just plain\n";
    print
"text. If you do not wish to have one, just make it blank.\n\n(Type @ on a blank line to exit)\n";

    $new_motd = "";
    do {
        print "] ";
        $line = <STDIN>;
        $line =~ s/[\r|\n]//g;
        if ( $line ne "@" ) {
            $line =~ s/  /\&nbsp;\&nbsp;/g;
            $line =~ s/\t/\&nbsp;\&nbsp;\&nbsp;\&nbsp;/g;
            $line =~ s/$/ /;
            $line =~ s/\"/\\\"/g;

            $new_motd = $new_motd . $line;
        }
    } while ( $line ne "@" );
    return $new_motd;
}

####################################################################################
#### PLUGINS ####
sub command81 {
    $command =~ s/[\s|\n|\r]*//g;
    if ( $command > 0 ) {
        $command = $command - 1;
        if ( $command <= $#plugins ) {
            @newplugins = ();
            $ct         = 0;
            while ( $ct <= $#plugins ) {
                if ( $ct != $command ) {
                    @newplugins = ( @newplugins, $plugins[$ct] );
                }
                $ct++;
            }
            @plugins = @newplugins;
        } elsif ( $command <= $#plugins + $#unused_plugins + 1 ) {
            $num        = $command - $#plugins - 1;
            @newplugins = @plugins;
            $ct         = 0;
            while ( $ct <= $#unused_plugins ) {
                if ( $ct == $num ) {
                    @newplugins = ( @newplugins, $unused_plugins[$ct] );
                }
                $ct++;
            }
            @plugins = @newplugins;
        }
    }
    return @plugins;
}

####################################################################################
#### FOLDERS #####
# default_folder_prefix
sub command21 {
    print "Some IMAP servers (UW, for example) store mail and folders in\n";
    print "your user space in a separate subdirectory.  This is where you\n";
    print "specify what that directory is.\n";
    print "\n";
    print "EXAMPLE:  mail/";
    print "\n";
    print "NOTE:  If you use Cyrus, or some server that would not use this\n";
    print "       option, you must set this to 'none'.\n";
    print "\n";
    print "[$WHT$default_folder_prefix$NRM]: $WHT";
    $new_default_folder_prefix = <STDIN>;

    if ( $new_default_folder_prefix eq "\n" ) {
        $new_default_folder_prefix = $default_folder_prefix;
    } else {
        $new_default_folder_prefix =~ s/[\r\n]//g;
    }
    if ( ( $new_default_folder_prefix =~ /^\s*$/ ) || ( $new_default_folder_prefix =~ m/^none$/i ) ) {
        $new_default_folder_prefix = "";
    } else {
        # add the trailing delimiter only if we know what the server is.
        if (($imap_server_type eq 'cyrus' and
                  $optional_delimiter eq 'detect') or
                 ($imap_server_type eq 'courier' and
                  $optional_delimiter eq 'detect')) {
           $new_default_folder_prefix =~ s/\.*$/\./;
        } elsif ($imap_server_type eq 'uw' and
                 $optional_delimiter eq 'detect') {
           $new_default_folder_prefix =~ s/\/*$/\//;
        }
    }
    return $new_default_folder_prefix;
}

# Show Folder Prefix
sub command22 {
    print "It is possible to set up the default folder prefix as a user\n";
    print "specific option, where each user can specify what their mail\n";
    print "folder is.  If you set this to false, they will never see the\n";
    print "option, but if it is true, this option will appear in the\n";
    print "'options' section.\n";
    print "\n";
    print "NOTE:  You set the default folder prefix in option '1' of this\n";
    print "       section.  That will be the default if the user doesn't\n";
    print "       specify anything different.\n";
    print "\n";

    if ( lc($show_prefix_option) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "\n";
    print "Show option (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $show_prefix_option = "true";
    } else {
        $show_prefix_option = "false";
    }
    return $show_prefix_option;
}

# Trash Folder 
sub command23a {
    print "You can now specify where the default trash folder is located.\n";
    print "On servers where you do not want this, you can set it to anything\n";
    print "and set option 6 to false.\n";
    print "\n";
    print "This is relative to where the rest of your email is kept.  You do\n";
    print "not need to worry about their mail directory.  If this folder\n";
    print "would be ~/mail/trash on the filesystem, you only need to specify\n";
    print "that this is 'trash', and be sure to put 'mail/' in option 1.\n";
    print "\n";

    print "[$WHT$trash_folder$NRM]: $WHT";
    $new_trash_folder = <STDIN>;
    if ( $new_trash_folder eq "\n" ) {
        $new_trash_folder = $trash_folder;
    } else {
        if (check_imap_folder($new_trash_folder)) {
            $new_trash_folder =~ s/[\r|\n]//g;
        } else {
            $new_trash_folder = $trash_folder;
        }
    }
    return $new_trash_folder;
}

# Sent Folder 
sub command23b {
    print "This is where messages that are sent will be stored.  SquirrelMail\n";
    print "by default puts a copy of all outgoing messages in this folder.\n";
    print "\n";
    print "This is relative to where the rest of your email is kept.  You do\n";
    print "not need to worry about their mail directory.  If this folder\n";
    print "would be ~/mail/sent on the filesystem, you only need to specify\n";
    print "that this is 'sent', and be sure to put 'mail/' in option 1.\n";
    print "\n";

    print "[$WHT$sent_folder$NRM]: $WHT";
    $new_sent_folder = <STDIN>;
    if ( $new_sent_folder eq "\n" ) {
        $new_sent_folder = $sent_folder;
    } else {
        if (check_imap_folder($new_sent_folder)) {
            $new_sent_folder =~ s/[\r|\n]//g;
        } else {
            $new_sent_folder = $sent_folder;
        }
    }
    return $new_sent_folder;
}

# Draft Folder 
sub command23c {
    print "You can now specify where the default draft folder is located.\n";
    print "On servers where you do not want this, you can set it to anything\n";
    print "and set option 9 to false.\n";
    print "\n";
    print "This is relative to where the rest of your email is kept.  You do\n";
    print "not need to worry about their mail directory.  If this folder\n";
    print "would be ~/mail/drafts on the filesystem, you only need to specify\n";
    print "that this is 'drafts', and be sure to put 'mail/' in option 1.\n";
    print "\n";

    print "[$WHT$draft_folder$NRM]: $WHT";
    $new_draft_folder = <STDIN>;
    if ( $new_draft_folder eq "\n" ) {
        $new_draft_folder = $draft_folder;
    } else {
        if (check_imap_folder($new_draft_folder)) {
            $new_draft_folder =~ s/[\r|\n]//g;
        } else {
            $new_draft_folder = $draft_folder;
        }
    }
    return $new_draft_folder;
}

# default move to trash 
sub command24a {
    print "By default, should messages get moved to the trash folder?  You\n";
    print "can specify the default trash folder in option 3.  If this is set\n";
    print "to false, messages will get deleted immediately without moving\n";
    print "to the trash folder.\n";
    print "\n";
    print "Trash folder is currently: $trash_folder\n";
    print "\n";

    if ( lc($default_move_to_trash) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "By default, move to trash (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $default_move_to_trash = "true";
    } else {
        $default_move_to_trash = "false";
    }
    return $default_move_to_trash;
}

# default move to sent (save sent messages)
sub command24b {
    print "By default, should copies of outgoing messages get saved in the\n";
    print "sent folder?  You can specify the default sent folder in option 4.\n";
    print "If this is set to false, messages will get sent and no copy will\n";
    print "be made.\n";
    print "\n";
    print "Sent folder is currently: $sent_folder\n";
    print "\n";

    if ( lc($default_move_to_sent) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "By default, save sent messages (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $default_move_to_sent = "true";
    } else {
        $default_move_to_sent = "false";
    }
    return $default_move_to_sent;
}

# default save as draft
sub command24c {
    print "By default, should the save to draft option be shown? You can\n";
    print "specify the default drafts folder in option 5. If this is set\n";
    print "to false, users will not be shown the save to draft option.\n";
    print "\n";
    print "Drafts folder is currently: $draft_folder\n";
    print "\n";

    if ( lc($default_save_as_draft) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "By default, save as draft (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $default_save_as_draft = "true";
    } else {
        $default_save_as_draft = "false";
    }
    return $default_save_as_draft;
}

# List special folders first 
sub command27 {
    print "SquirrelMail has what we call 'special folders' that are not\n";
    print "manipulated and viewed like normal folders.  Some examples of\n";
    print "these folders would be INBOX, Trash, Sent, etc.  This option\n";
    print "Simply asks if you want these folders listed first in the folder\n";
    print "listing.\n";
    print "\n";

    if ( lc($list_special_folders_first) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "\n";
    print "List first (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $list_special_folders_first = "true";
    } else {
        $list_special_folders_first = "false";
    }
    return $list_special_folders_first;
}

# Show special folders color 
sub command28 {
    print "SquirrelMail has what we call 'special folders' that are not\n";
    print "manipulated and viewed like normal folders.  Some examples of\n";
    print "these folders would be INBOX, Trash, Sent, etc.  This option\n";
    print "wants to know if we should display special folders in a\n";
    print "color than the other folders.\n";
    print "\n";

    if ( lc($use_special_folder_color) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "\n";
    print "Show color (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $use_special_folder_color = "true";
    } else {
        $use_special_folder_color = "false";
    }
    return $use_special_folder_color;
}

# Auto expunge 
sub command29 {
    print "The way that IMAP handles deleting messages is as follows.  You\n";
    print "mark the message as deleted, and then to 'really' delete it, you\n";
    print "expunge it.  This option asks if you want to just have messages\n";
    print "marked as deleted, or if you want SquirrelMail to expunge the \n";
    print "messages too.\n";
    print "\n";

    if ( lc($auto_expunge) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Auto expunge (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $auto_expunge = "true";
    } else {
        $auto_expunge = "false";
    }
    return $auto_expunge;
}

# Default sub of inbox 
sub command210 {
    print "Some IMAP servers have all folders as subfolders of INBOX.\n";
    print "This can cause some confusion in folder creation for users when\n";
    print "they try to create folders and don't put it as a subfolder of INBOX\n";
    print "and get permission errors.  This option asks if you want folders\n";
    print "to be subfolders of INBOX by default.\n";
    print "\n";

    if ( lc($default_sub_of_inbox) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Default sub of INBOX (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $default_sub_of_inbox = "true";
    } else {
        $default_sub_of_inbox = "false";
    }
    return $default_sub_of_inbox;
}

# Show contain subfolder option 
sub command211 {
    print "Some IMAP servers (UW) make it so that there are two types of\n";
    print "folders.  Those that contain messages, and those that contain\n";
    print "subfolders.  If this is the case for your server, set this to\n";
    print "true, and it will ask the user whether the folder they are\n";
    print "creating contains subfolders or messages.\n";
    print "\n";

    if ( lc($show_contain_subfolders_option) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Show option (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $show_contain_subfolders_option = "true";
    } else {
        $show_contain_subfolders_option = "false";
    }
    return $show_contain_subfolders_option;
}

# Default Unseen Notify 
sub command212 {
    print "This option specifies where the users will receive notification\n";
    print "about unseen messages by default.  This is of course an option that\n";
    print "can be changed on a user level.\n";
    print "  1 = No notification\n";
    print "  2 = Only on the INBOX\n";
    print "  3 = On all folders\n";
    print "\n";

    print "Which one should be default (1,2,3)? [$WHT$default_unseen_notify$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( $new_show =~ /^[1|2|3]\n/i ) {
        $default_unseen_notify = $new_show;
    }
    $default_unseen_notify =~ s/[\r|\n]//g;
    return $default_unseen_notify;
}

# Default Unseen Type 
sub command213 {
    print "Here you can define the default way that unseen messages will be displayed\n";
    print "to the user in the folder listing on the left side.\n";
    print "  1 = Only unseen messages   (4)\n";
    print "  2 = Unseen and Total messages  (4/27)\n";
    print "\n";

    print "Which one should be default (1,2)? [$WHT$default_unseen_type$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( $new_show =~ /^[1|2]\n/i ) {
        $default_unseen_type = $new_show;
    }
    $default_unseen_type =~ s/[\r|\n]//g;
    return $default_unseen_type;
}

# Auto create special folders
sub command214 {
    print "Would you like the Sent, Trash, and Drafts folders to be created\n";
    print "automatically print for you when a user logs in?  If the user\n";
    print "accidentally deletes their special folders, this option will\n";
    print "automatically create it again for them.\n";
    print "\n";

    if ( lc($auto_create_special) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Auto create special folders? (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $auto_create_special = "true";
    } else {
        $auto_create_special = "false";
    }
    return $auto_create_special;
}

# Automatically delete folders 
sub command215 {
    if ( $imap_server_type eq "uw" ) {
        print "UW IMAP servers will not allow folders containing";
        print "mail to also contain folders.\n";
        print "Deleting folders will bypass the trash folder and";
        print "be immediately deleted\n\n";
        print "If this is not the correct value for your server,\n";
        print "please use option D on the Main Menu to configure your server correctly.\n\n";
        print "Press enter to continue...\n";
        $new_delete = <STDIN>;
        $delete_folder = "true";
    } else { 
        print "Should folders selected for deletion bypass the Trash folder?\n\n";
        if ( lc($delete_folder) eq "true" ) {
            $default_value = "y";
        } else {
            $default_value = "n";
        }
        print "Auto delete folders? (y/n) [$WHT$default_value$NRM]: $WHT";
        $new_delete = <STDIN>;
        if ( ( $new_delete =~ /^y\n/i ) || ( ( $new_delete =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
            $delete_folder = "true";
        } else {
            $delete_folder = "false";
        }
    }
    return $delete_folder;
}

#noselect fix
sub command216 {
    print "Some IMAP servers allow subfolders to exist even if the parent\n";
    print "folders do not. This fixes some problems with the folder list\n";
    print "when this is the case, causing the /NoSelect folders to be displayed\n";
    print "\n";

    if ( lc($noselect_fix_enable) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "enable noselect fix? (y/n) [$WHT$noselect_fix_enable$NRM]: $WHT";
    $noselect_fix_enable = <STDIN>;
    if ( ( $noselect_fix_enable =~ /^y\n/i ) || ( ( $noselect_fix_enable =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $noselect_fix_enable = "true";
    } else {
        $noselect_fix_enable = "false";
    }
    return $noselect_fix_enable;
}

####################################################################################
#### GENERAL OPTIONS ####
# Data directory
sub command31 {
    print "Specify the location for your data directory.\n";
    print "The path name can be absolute or relative (to the config directory).\n";
    print "You probably need to create this directory yourself. Here are two examples:\n";
    print "  Absolute:    /var/local/squirrelmail/data/\n";
    print "  Relative:    ../data/\n";     
    print "Relative paths to directories outside of the SquirrelMail distribution\n";
    print "will be converted to their absolute path equivalents in config.php.\n\n";
    print "Note: There are potential security risks with having a writable directory\n";
    print "under the web server's root directory (ex: /home/httpd/html).\n";
    print "For this reason, it is recommended to put the data directory\n";
    print "in an alternate location of your choice. \n";
    print "\n";

    print "[$WHT$data_dir$NRM]: $WHT";
    $new_data_dir = <STDIN>;
    if ( $new_data_dir eq "\n" ) {
        $new_data_dir = $data_dir;
    } else {
        $new_data_dir =~ s/[\r|\n]//g;
    }
    if ( $new_data_dir =~ /^\s*$/ ) {
        $new_data_dir = "";
    } else {
        $new_data_dir =~ s/\/*$//g;
        $new_data_dir =~ s/$/\//g;
    }
    return $new_data_dir;
}

# Attachment directory
sub command32 {
    print "Path to directory used for storing attachments while a mail is\n";
    print "being sent. The path name can be absolute or relative (to the config directory).\n";
    print "It doesn't matter.  Here are two examples:\n";
    print "  Absolute:    /var/spool/squirrelmail/attach/\n";
    print "  Relative:    ../attach/\n";
    print "Relative paths to directories outside of the SquirrelMail distribution\n";
    print "will be converted to their absolute path equivalents in config.php.\n\n";
    print "Note:  There are a few security considerations regarding this\n";
    print "directory:\n";
    print "  1.  It should have the permission 733 (rwx-wx-wx) to make it\n";
    print "      impossible for a random person with access to the webserver\n";
    print "      to list files in this directory.  Confidential data might\n";
    print "      be laying around in there.\n";
    print "      Depending on your user:group assignments, 730 (rwx-wx---)\n";
    print "      may be possible, and more secure (e.g. root:apache)\n";
    print "  2.  Since the webserver is not able to list the files in the\n";
    print "      content is also impossible for the webserver to delete files\n";
    print "      lying around there for too long.\n";
    print "  3.  It should probably be another directory than the data\n";
    print "      directory specified in option 3.\n";
    print "\n";

    print "[$WHT$attachment_dir$NRM]: $WHT";
    $new_attachment_dir = <STDIN>;
    if ( $new_attachment_dir eq "\n" ) {
        $new_attachment_dir = $attachment_dir;
    } else {
        $new_attachment_dir =~ s/[\r|\n]//g;
    }
    if ( $new_attachment_dir =~ /^\s*$/ ) {
        $new_attachment_dir = "";
    } else {
        $new_attachment_dir =~ s/\/*$//g;
        $new_attachment_dir =~ s/$/\//g;
    }
    return $new_attachment_dir;
}

sub command33 {
    print "The directory hash level setting allows you to configure the level\n";
    print "of hashing that Squirremail employs in your data and attachment\n";
    print "directories. This value must be an integer ranging from 0 to 4.\n";
    print "When this value is set to 0, Squirrelmail will simply store all\n";
    print "files as normal in the data and attachment directories. However,\n";
    print "when set to a value from 1 to 4, a simple hashing scheme will be\n";
    print "used to organize the files in this directory. In short, the crc32\n";
    print "value for a username will be computed. Then, up to the first 4\n";
    print "digits of the hash, as set by this configuration value, will be\n";
    print "used to directory hash the files for that user in the data and\n";
    print "attachment directory. This allows for better performance on\n";
    print "servers with larger numbers of users.\n";
    print "\n";

    print "[$WHT$dir_hash_level$NRM]: $WHT";
    $new_dir_hash_level = <STDIN>;
    if ( $new_dir_hash_level eq "\n" ) {
        $new_dir_hash_level = $dir_hash_level;
    } else {
        $new_dir_hash_level =~ s/[\r|\n]//g;
    }
    if ( ( int($new_dir_hash_level) < 0 )
        || ( int($new_dir_hash_level) > 4 )
        || !( int($new_dir_hash_level) eq $new_dir_hash_level ) ) {
        print "Invalid Directory Hash Level.\n";
        print "Value must be an integer ranging from 0 to 4\n";
        print "Hit enter to continue.\n";
        $enter_key = <STDIN>;

        $new_dir_hash_level = $dir_hash_level;
        }

    return $new_dir_hash_level;
}

sub command34 {
    print "This is the default size (in pixels) of the left folder list.\n";
    print "Default is 150, but you can set it to whatever you wish.  This\n";
    print "is a user preference, so this will only show up as their default.\n";
    print "\n";
    print "[$WHT$default_left_size$NRM]: $WHT";
    $new_default_left_size = <STDIN>;
    if ( $new_default_left_size eq "\n" ) {
        $new_default_left_size = $default_left_size;
    } else {
        $new_default_left_size =~ s/[\r|\n]//g;
    }
    return $new_default_left_size;
}

sub command35 {
    print "Some IMAP servers only have lowercase letters in the usernames\n";
    print "but they still allow people with uppercase to log in.  This\n";
    print "causes a problem with the user's preference files.  This option\n";
    print "transparently changes all usernames to lowercase.";
    print "\n";

    if ( lc($force_username_lowercase) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Convert usernames to lowercase (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        return "true";
    }
    return "false";
}

sub command36 {
    print "";
    print "\n";

    if ( lc($default_use_priority) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }

    print "Allow users to specify priority of outgoing mail (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        return "true";
    }
    return "false";
}

sub command37 {
    print "";
    print "\n";

    if ( lc($default_hide_attribution) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }

    print "Hide SM attributions (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        return "true";
    }
    return "false";
}

sub command38 {
    print "";
    print "\n";

    if ( lc($default_use_mdn) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }

    print "Enable support for read/delivery receipt support (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        return "true";
    }
    return "false";
}

sub command39 {
    print "In loosely managed environments, you may want to allow users\n";
    print "to edit their full name and email address. In strictly managed\n";
    print "environments, you may want to force users to use the name\n";
    print "and email address assigned to them.\n";
    print "\n";
    print "'y' - allow a user to edit their full name and email address,\n";
    print "'n' - users must use the assigned values.\n";
    print "\n";

    if ( lc($edit_identity) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow editing of user's identity? (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_edit = <STDIN>;
    if ( ( $new_edit =~ /^y\n/i ) || ( ( $new_edit =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $edit_identity = "true";
        $edit_name = "true";
        $hide_auth_header = "false";
    } else {
        $edit_identity = "false";
        $edit_name = command39a();
        $hide_auth_header = command39b();
    }
    return $edit_identity;
}

sub command39a {
    print $NRM;
    print "\nAs a follow-up, this option allows you to choose if the user\n";
    print "can edit their full name even when you don't want them to\n";
    print "change their username\n";
    print "\n";

    if ( lc($edit_name) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow editing of the users full name? (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_edit = <STDIN>;
    if ( ( $new_edit =~ /^y\n/i ) || ( ( $new_edit =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $edit_name = "true";
    } else {
        $edit_name = "false";
    }
    return $edit_name;
}

sub command39b {
    print $NRM;
    print "\nSquirrelMail adds username information to every outgoing\n";
    print "email in order to prevent possible sender forging when\n";
    print "users are allowed to change their email and/or full name.\n";
    print "\n";
    print "You can remove user information from this header (y) if you\n";
    print "think that it violates privacy or security.\n";
    print "\n";
    print "Note: SquirrelMail will refuse to remove that information\n";
    print "from the email headers if users are allowed to change their\n";
    print "identities, regardless of what you have set here.\n";
    print "\n";
    print "Note: If you have defined a header encryption key in your SMTP\n";
    print "or Sendmail settings (see the \"Server Settings\" option page),\n";
    print "this setting is ignored because all user information in outgoing\n";
    print "messages is encoded.\n";
    print "\n";

    if ( lc($hide_auth_header) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Remove username from email headers? (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_header = <STDIN>;
    if ( ( $new_header =~ /^y\n/i ) || ( ( $new_header =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $hide_auth_header = "true";
    } else {
        $hide_auth_header = "false";
    }
    return $edit_name;
}

sub command310 {
    print "This option allows you to choose if users can use thread sorting.\n";
    print "Your IMAP server must support the THREAD command for this to work.\n";
    print "\n";

    if ( lc($allow_thread_sort) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow server side thread sorting? (y/n) [$WHT$default_value$NRM]: $WHT";
    $allow_thread_sort = <STDIN>;
    if ( ( $allow_thread_sort =~ /^y\n/i ) || ( ( $allow_thread_sort =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $allow_thread_sort = "true";
    } else {
        $allow_thread_sort = "false";
    }
    return $allow_thread_sort;
}

sub command311 {
    print "This option allows you to choose if SM uses server-side sorting\n";
    print "Your IMAP server must support the SORT  command for this to work\n";
    print "\n";

    if ( lc($allow_server_sort) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow server-side sorting? (y/n) [$WHT$default_value$NRM]: $WHT";
    $allow_server_sort = <STDIN>;
    if ( ( $allow_server_sort =~ /^y\n/i ) || ( ( $allow_server_sort =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $allow_server_sort = "true";
    } else {
        $allow_server_sort = "false";
    }
    return $allow_server_sort;
}

sub command312 {
    print "This option allows you to choose if SM uses charset search\n";
    print "Your IMAP server must support the SEARCH CHARSET command for this to work\n";
    print "\n";

    if ( lc($allow_charset_search) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow charset searching? (y/n) [$WHT$default_value$NRM]: $WHT";
    $allow_charset_search = <STDIN>;
    if ( ( $allow_charset_search =~ /^y\n/i ) || ( ( $allow_charset_search =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $allow_charset_search = "true";
    } else {
        $allow_charset_search = "false";
    }
    return $allow_charset_search;
}

sub command313 {
    print "This option allows you to enable unique identifier (UID) support.\n";
    print "\n";

    if ( lc($uid_support) eq "true" ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Enable Unique identifier (UID) support? (y/n) [$WHT$default_value$NRM]: $WHT";
    $uid_support = <STDIN>;
    if ( ( $uid_support =~ /^y\n/i ) || ( ( $uid_support =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $uid_support = "true";
    } else {
        $uid_support = "false";
    }
    return $uid_support;
}

sub command314 {
    print "This option allows you to change the name of the PHP session used\n";
    print "by SquirrelMail.  Unless you know what you are doing, you probably\n";
    print "don't need or want to change this from the default of SQMSESSID.\n";
    print "[$WHT$session_name$NRM]: $WHT";
    $new_session_name = <STDIN>;
    chomp($new_session_name);
    if ( $new_session_name eq "" ) {
        $new_session_name = $session_name;
    }
    return $new_session_name;
}

# set the location base for redirects (since 1.4.8)
sub command_config_location_base {
    print "Here you can set the base part of the SquirrelMail URL.\n";
    print "It is normally autodetected but if that fails, use this\n";
    print "option to override.\n";
    print "It should contain only the protocol and hostname/port parts\n";
    print "of the URL; the full path will be appended automatically.\n\n";
    print "Examples:\nhttp://webmail.example.org\nhttp://webmail.example.com:8080\nhttps://webmail.example.com:6691\n\n";
    print "Do not add any path elements.\n";

    print "URL base? [" .$WHT."autodetect$NRM]: $WHT";
    $new_config_location_base = <STDIN>;
    chomp($new_config_location_base);
    $config_location_base = $new_config_location_base;
    
    return $config_location_base;
}


# only_secure_cookies (since 1.4.16)
sub command316 {
    print "This option allows you to specify that if a user session is initiated\n";
    print "under a secure (HTTPS, SSL-encrypted) connection, the cookies given to\n";
    print "the browser will ONLY be transmitted via a secure connection henceforth.\n\n";
    print "Generally this is a Good Thing, and should NOT be disabled.  However,\n";
    print "if you intend to use the Secure Login or Show SSL Link plugins to\n";
    print "encrypt the user login, but not the rest of the SquirrelMail session,\n";
    print "this can be turned off.  Think twice before doing so.\n";
    print "\n";

    if ( lc($only_secure_cookies) eq 'true' ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Transmit cookies only on secure connection when available? (y/n) [$WHT$default_value$NRM]: $WHT";
    $only_secure_cookies = <STDIN>;
    if ( ( $only_secure_cookies =~ /^y\n/i ) || ( ( $only_secure_cookies =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $only_secure_cookies = 'true';
    } else {
        $only_secure_cookies = 'false';
    }
    return $only_secure_cookies;
}



# disable_security_tokens (since 1.4.20RC1)
sub command317 {
    print "This option allows you to turn off the security checks in the forms\n";
    print "that SquirrelMail generates.  It is NOT RECOMMENDED that you disable\n";
    print "this feature - otherwise, your users may be exposed to phishing and\n";
    print "other attacks.\n";
    print "Unless you know what you are doing, you should leave this set to \"NO\".\n";
    print "\n";

    if ( lc($disable_security_tokens) eq 'true' ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Disable secure forms? (y/n) [$WHT$default_value$NRM]: $WHT";
    $disable_security_tokens = <STDIN>;
    if ( ( $disable_security_tokens =~ /^y\n/i ) || ( ( $disable_security_tokens =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $disable_security_tokens = 'true';
    } else {
        $disable_security_tokens = 'false';
    }
    return $disable_security_tokens;
}



# check_referrer (since 1.4.20RC1)
sub command318 {
    print "This option allows you to enable referal checks for all page requests\n";
    print "made to SquirrelMail.  This can help ensure that page requests came\n";
    print "from the same server and not from an attacker's site (usually the\n";
    print "result of a XSS or phishing attack).  To enable referal checking,\n";
    print "this setting can be set to the domain where your SquirrelMail is\n";
    print "being hosted (usually the same as the Domain setting under Server\n";
    print "Settings).  For example, it could be \"example.com\", or if you\n";
    print "use a plugin (such as Login Manager) to host SquirrelMail on more\n";
    print "than one domain, you can set this to \"###DOMAIN###\" to tell it\n";
    print "to use the current domain.\n";
    print "\n";
    print "However, in some cases (where proxy servers are in use, etc.), the\n";
    print "domain might be different.\n";
    print "\n";
    print "NOTE that referal checks are not foolproof - they can be spoofed by\n";
    print "browsers, and some browsers intentionally don't send referal\n";
    print "information (in which case, the check is silently bypassed)\n";
    print "\n";

    print "Referal requirement? [$WHT$check_referrer$NRM]: $WHT";
    $new_check_referrer = <STDIN>;
    chomp($new_check_referrer);
    $check_referrer = $new_check_referrer;

    return $check_referrer;
}



####################################################################################
#### THEMES ####
sub command41 {
    print "\nDefine the themes that you wish to use.  If you have added ";
    print "a theme of your own, just follow the instructions (?) about how to add ";
    print "them.  You can also change the default theme.\n";
    print "[theme] command (?=help) > ";
    $input = <STDIN>;
    $input =~ s/[\r|\n]//g;
    while ( $input ne "d" ) {
        if ( $input =~ /^\s*l\s*/i ) {
            $count = 0;
            while ( $count <= $#theme_name ) {
                if ( $count == $theme_default ) {
                    print " *";
                } else {
                    print "  ";
                }
                if ( $count < 10 ) {
                    print " ";
                }
                $name       = $theme_name[$count];
                $num_spaces = 35 - length($name);
                for ( $i = 0 ; $i < $num_spaces ; $i++ ) {
                    $name = $name . " ";
                }

                print " $count.  $name";
                print "($theme_path[$count])\n";

                $count++;
            }
        } elsif ( $input =~ /^\s*m\s*[0-9]+/i ) {
            $old_def       = $theme_default;
            $theme_default = $input;
            $theme_default =~ s/^\s*m\s*//;
            if ( ( $theme_default > $#theme_name ) || ( $theme_default < 0 ) ) {
                print "Cannot set default theme to $theme_default.  That theme does not exist.\n";
                $theme_default = $old_def;
            }
        } elsif ( $input =~ /^\s*\+/ ) {
            print "What is the name of this theme: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $theme_name[ $#theme_name + 1 ] = $name;
            print "Be sure to put ../themes/ before the filename.\n";
            print "What file is this stored in (ex: ../themes/default_theme.php): ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $theme_path[ $#theme_path + 1 ] = $name;
        } elsif ( $input =~ /^\s*-\s*[0-9]?/ ) {
            if ( $input =~ /[0-9]+\s*$/ ) {
                $rem_num = $input;
                $rem_num =~ s/^\s*-\s*//g;
                $rem_num =~ s/\s*$//;
            } else {
                $rem_num = $#theme_name;
            }
            if ( $rem_num == $theme_default ) {
                print "You cannot remove the default theme!\n";
            } else {
                $count          = 0;
                @new_theme_name = ();
                @new_theme_path = ();
                while ( $count <= $#theme_name ) {
                    if ( $count != $rem_num ) {
                        @new_theme_name = ( @new_theme_name, $theme_name[$count] );
                        @new_theme_path = ( @new_theme_path, $theme_path[$count] );
                    }
                    $count++;
                }
                @theme_name = @new_theme_name;
                @theme_path = @new_theme_path;
                if ( $theme_default > $rem_num ) {
                    $theme_default--;
                }
            }
        } elsif ( $input =~ /^\s*t\s*/i ) {
            print "\nStarting detection...\n\n";

            opendir( DIR, "../themes" );
            @files = grep { /\.php$/i } sort(readdir(DIR));
            $cnt = 0;
            while ( $cnt <= $#files ) {
                $filename = "../themes/" . $files[$cnt];
                if ( $filename ne "../themes/index.php" ) {
                    $found = 0;
                    for ( $x = 0 ; $x <= $#theme_path ; $x++ ) {
                        if ( $theme_path[$x] eq $filename ) {
                            $found = 1;
                        }
                    }
                    if ( $found != 1 ) {
                        print "** Found theme: $filename\n";
                        $def = $files[$cnt];
                        $def =~ s/_/ /g;
                        $def =~ s/\.php//g;
                        $def = lc($def);
                        #$def =~ s/(^\w+)/ucfirst $1/eg;
                        #$def =~ s/(\s+)(\w+)/$1 . ucfirst $2/eg;
                        $def =~ s/(^\w+)|(\s+)(\w+)/ucfirst $1 . $2 . ucfirst $3/eg;
                        print "   What is its name? [$def]: ";
                        $nm = <STDIN>;
                        $nm =~ s/^\s+|\s+$|[\n|\r]//g;
                        if ( $nm eq '' ) { $nm = $def; }
                        $theme_name[ $#theme_name + 1 ] = $nm;
                        $theme_path[ $#theme_path + 1 ] = $filename;
                    }
                }
                $cnt++;
            }
            print "\n";
            for ( $cnt = 0 ; $cnt <= $#theme_path ; $cnt++ ) {
                $filename = $theme_path[$cnt];
                if ( !( -e $filename ) ) {
                    print "  Removing $filename (file not found)\n";
                    $offset         = 0;
                    @new_theme_name = ();
                    @new_theme_path = ();
                    for ( $x = 0 ; $x < $#theme_path ; $x++ ) {
                        if ( $theme_path[$x] eq $filename ) {
                            $offset = 1;
                        }
                        if ( $offset == 1 ) {
                            $new_theme_name[$x] = $theme_name[ $x + 1 ];
                            $new_theme_path[$x] = $theme_path[ $x + 1 ];
                        } else {
                            $new_theme_name[$x] = $theme_name[$x];
                            $new_theme_path[$x] = $theme_path[$x];
                        }
                    }
                    @theme_name = @new_theme_name;
                    @theme_path = @new_theme_path;
                }
            }
            print "\nDetection complete!\n\n";

            closedir DIR;
        } elsif ( $input =~ /^\s*\?\s*/ ) {
            print ".-------------------------.\n";
            print "| t       (detect themes) |\n";
            print "| +           (add theme) |\n";
            print "| - N      (remove theme) |\n";
            print "| m N      (mark default) |\n";
            print "| l         (list themes) |\n";
            print "| d                (done) |\n";
            print "`-------------------------'\n";
        }
        print "[theme] command (?=help) > ";
        $input = <STDIN>;
        $input =~ s/[\r|\n]//g;
    }
}

# Theme - CSS file
sub command42 {
    print "You may specify a cascading style-sheet (CSS) file to be included\n";
    print "on each html page generated by SquirrelMail. The CSS file is useful\n";
    print "for specifying a site-wide font. If you're not familiar with CSS\n";
    print "files, leave this blank.\n";
    print "\n";
    print "To clear out an existing value, just type a space for the input.\n";
    print "\n";
    print "Please be aware of the following: \n";
    print "  - Relative URLs are relative to the config dir\n";
    print "    to use the themes directory, use ../themes/css/newdefault.css\n";
    print "  - To specify a css file defined outside the SquirrelMail source tree\n";
    print "    use the absolute URL the webserver would use to include the file\n";
    print "    e.g. http://example.com/css/mystyle.css or /css/mystyle.css\n";
    print "\n";
    print "[$WHT$theme_css$NRM]: $WHT";
    $new_theme_css = <STDIN>;

    if ( $new_theme_css eq "\n" ) {
        $new_theme_css = $theme_css;
    } else {
        $new_theme_css =~ s/[\r|\n]//g;
    }
    $new_theme_css =~ s/^\s*//;
    return $new_theme_css;
}
####################################################################################
#### Address books ####
# LDAP
sub command61 {
    print "You can now define different LDAP servers.\n";
    print "Please ensure proper permissions for config.php when including\n";
    print "sensitive passwords.\n\n";
    print "[ldap] command (?=help) > ";
    $input = <STDIN>;
    $input =~ s/[\r|\n]//g;
    while ( $input ne "d" ) {
        if ( $input =~ /^\s*l\s*/i ) {
            $count = 0;
            while ( $count <= $#ldap_host ) {
                print "$count. $ldap_host[$count]\n";
                print "        base: $ldap_base[$count]\n";
                if ( $ldap_charset[$count] ) {
                    print "     charset: $ldap_charset[$count]\n";
                }
                if ( $ldap_port[$count] ) {
                    print "        port: $ldap_port[$count]\n";
                }
                if ( $ldap_name[$count] ) {
                    print "        name: $ldap_name[$count]\n";
                }
                if ( $ldap_maxrows[$count] ) {
                    print "     maxrows: $ldap_maxrows[$count]\n";
                }
                if ( $ldap_binddn[$count] ) {
                    print "      binddn: $ldap_binddn[$count]\n";
                    if ( $ldap_bindpw[$count] ) {
                        print "      bindpw: $ldap_bindpw[$count]\n";
                    }
                }
        if ( $ldap_protocol[$count] ) {
                    print "    protocol: $ldap_protocol[$count]\n";
                }

                print "\n";
                $count++;
            }
        } elsif ( $input =~ /^\s*\+/ ) {
            $sub = $#ldap_host + 1;

            print "First, we need to have the hostname or the IP address where\n";
            print "this LDAP server resides.  Example: ldap.bigfoot.com\n";
            print "\n";
            print "You can use any URI compatible with your LDAP library. ldaps\n";
            print "and ldapi URIs can be used to create secure LDAP connections.\n";
            print "hostname: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_host[$sub] = $name;

            print "\n";

            print "Next, we need the server root (base dn).  For this, an empty\n";
            print "string is allowed.\n";
            print "Example: ou=member_directory,o=netcenter.com\n";
            print "base: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_base[$sub] = $name;

            print "\n";

            print "This is the TCP/IP port number for the LDAP server.  Default\n";
            print "port is 389.  This is optional.  Press ENTER for default.\n";
            print "port: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_port[$sub] = $name;

            print "\n";

            print "This is the charset for the server.  Default is utf-8.  This\n";
            print "is also optional.  Press ENTER for default.\n";
            print "charset: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_charset[$sub] = $name;

            print "\n";

            print "This is the name for the server, used to tag the results of\n";
            print "the search.  Default it \"LDAP: hostname\".  Press ENTER for default\n";
            print "name: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_name[$sub] = $name;

            print "\n";

            print "You can specify the maximum number of rows in the search result.\n";
            print "Default is unlimited.  Press ENTER for default.\n";
            print "maxrows: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_maxrows[$sub] = $name;

            print "\n";

            print "If your LDAP server does not like anonymous logins, you can specify bind DN.\n";
            print "Default is none, anonymous bind.  Press ENTER for default.\n";
            print "binddn: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_binddn[$sub] = $name;

            print "\n";

            if ( $ldap_binddn[$sub] ne '' ) {

                print "Now, please specify password for that DN.\n";
                print "bindpw: ";
                $name = <STDIN>;
                $name =~ s/[\r|\n]//g;
                $ldap_bindpw[$sub] = $name;

                print "\n";
            }

        print "You can specify bind protocol version here.\n";
            print "Default protocol version depends on your php ldap settings.\n";
        print "Press ENTER for default.\n";
            print "protocol: ";
            $name = <STDIN>;
            $name =~ s/[\r|\n]//g;
            $ldap_protocol[$sub] = $name;

            print "\n";

        } elsif ( $input =~ /^\s*-\s*[0-9]?/ ) {
            if ( $input =~ /[0-9]+\s*$/ ) {
                $rem_num = $input;
                $rem_num =~ s/^\s*-\s*//g;
                $rem_num =~ s/\s*$//;
            } else {
                $rem_num = $#ldap_host;
            }
            $count            = 0;
            @new_ldap_host    = ();
            @new_ldap_base    = ();
            @new_ldap_port    = ();
            @new_ldap_name    = ();
            @new_ldap_charset = ();
            @new_ldap_maxrows = ();
            @new_ldap_bindpw  = ();
            @new_ldap_binddn  = ();
            @new_ldap_protocol = ();

            while ( $count <= $#ldap_host ) {
                if ( $count != $rem_num ) {
                    @new_ldap_host    = ( @new_ldap_host,    $ldap_host[$count] );
                    @new_ldap_base    = ( @new_ldap_base,    $ldap_base[$count] );
                    @new_ldap_port    = ( @new_ldap_port,    $ldap_port[$count] );
                    @new_ldap_name    = ( @new_ldap_name,    $ldap_name[$count] );
                    @new_ldap_charset = ( @new_ldap_charset, $ldap_charset[$count] );
                    @new_ldap_maxrows = ( @new_ldap_maxrows, $ldap_maxrows[$count] );
                    @new_ldap_binddn  = ( @new_ldap_binddn,  $ldap_binddn[$count] );
                    @new_ldap_bindpw  = ( @new_ldap_bindpw,  $ldap_bindpw[$count] );
                    @new_ldap_protocol  = ( @new_ldap_protocol,  $ldap_protocol[$count] );
                }
                $count++;
            }
            @ldap_host    = @new_ldap_host;
            @ldap_base    = @new_ldap_base;
            @ldap_port    = @new_ldap_port;
            @ldap_name    = @new_ldap_name;
            @ldap_charset = @new_ldap_charset;
            @ldap_maxrows = @new_ldap_maxrows;
            @ldap_binddn  = @new_ldap_binddn;
            @ldap_bindpw  = @new_ldap_bindpw;
            @ldap_protocol = @new_ldap_protocol;
        } elsif ( $input =~ /^\s*\?\s*/ ) {
            print ".-------------------------.\n";
            print "| +            (add host) |\n";
            print "| - N       (remove host) |\n";
            print "| l          (list hosts) |\n";
            print "| d                (done) |\n";
            print "`-------------------------'\n";
        }
        print "[ldap] command (?=help) > ";
        $input = <STDIN>;
        $input =~ s/[\r|\n]//g;
    }
}

# Javascript or html address book
sub command62 {
    print "Some of our developers have come up with very good javascript interface\n";
    print "for searching through address books, however, our original goals said\n";
    print "that we would be 100% HTML.  In order to make it possible to use their\n";
    print "interface, and yet stick with our goals, we have also written a plain\n";
    print "HTML version of the search.  Here, you can choose which version to use.\n";
    print "\n";
    print "This is just the default value.  It is also a user option that each\n";
    print "user can configure individually\n";
    print "\n";

    if ( lc($default_use_javascript_addr_book) eq "true" ) {
        $default_value = "y";
    } else {
        $default_use_javascript_addr_book = "false";
        $default_value                    = "n";
    }
    print "Use javascript version by default (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $default_use_javascript_addr_book = "true";
    } else {
        $default_use_javascript_addr_book = "false";
    }
    return $default_use_javascript_addr_book;
}

# global filebased address book
sub command63 {
    print "If you want to use global file address book, then you\n";
    print "must set this option to a valid value. If option does\n";
    print "not have path elements, system assumes that file is\n";
    print "stored in data directory. If relative path is set, it is\n";
    print "relative to main squirrelmail directory. If value is empty,\n";
    print "address book is not enabled.\n";
    print "\n";

    print "[$WHT$abook_global_file$NRM]: $WHT";
    $new_abook_global_file = <STDIN>;
    if ( $new_abook_global_file eq "\n" ) {
        $new_abook_global_file = $abook_global_file;
    } else {
        $new_abook_global_file =~ s/[\r\n]//g;
    }
    return $new_abook_global_file;
}

# writing into global filebased abook control
sub command64 {
    print "This setting controls writing into global file address\n";
    print "book options. Address book file must be writeable by\n";
    print "webserver's user, if you want to enable this option.\n";
    print "\n";

    if ( lc($abook_global_file_writeable) eq 'true' ) {
        $default_value = "y";
    } else {
        $abook_global_file_writeable = 'false';
        $default_value               = "n";
    }
    print "Allow writing into global file address book (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $abook_global_file_writeable = 'true';
    } else {
        $abook_global_file_writeable = 'false';
    }
    return $abook_global_file_writeable;
}

# listing of global filebased abook control
sub command_abook_global_file_listing {
    print "This setting controls listing of global file address book.\n";
    print "\n";

    if ( lc($abook_global_file_listing) eq 'true' ) {
        $default_value = "y";
    } else {
        $abook_global_file_listing = 'false';
        $default_value = "n";
    }
    print "Allow listing of global file address book (y/n) [$WHT$default_value$NRM]: $WHT";
    $new_show = <STDIN>;
    if ( ( $new_show =~ /^y\n/i ) || ( ( $new_show =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $abook_global_file_listing = 'true';
    } else {
        $abook_global_file_listing = 'false';
    }
    return $abook_global_file_listing;
}

# controls $abook_file_line_length setting
sub command_abook_file_line_length {
    print "This setting controls space allocated to file based address book records.\n";
    print "End users will be unable to save address book entry, if total entry size \n";
    print "(quoted address book fields + 4 delimiters + linefeed) exceeds allowed \n";
    print "address book length size.\n";
    print "\n";
    print "Same setting is applied to personal and global file based address books.\n";
    print "\n";
    print "It is strongly recommended to keep default setting value. Change it only\n";
    print "if you really want to store address book entries that are bigger than two\n";
    print "kilobytes (2048).\n";
    print "\n";

    print "Enter allowed address book line length [$abook_file_line_length]: ";
    my $tmp = <STDIN>;
    $tmp = trim($tmp);
    # value is not modified, if user hits Enter or enters space
    if ($tmp ne '') {
        # make sure that input is numeric
        if ($tmp =~ /^\d+$/) {
            $abook_file_line_length = $tmp;
        } else {
            print "If you want to change this setting, you must enter number.\n";
            print "If you want to keep original setting - enter space.\n\n";
            print "Press Enter to continue...";
            $tmp = <STDIN>;
        }
    }
}

####################################################################################
#### Database ####
sub command91 {
    print "If you want to store your users address book details in a database then\n";
    print "you need to set this DSN to a valid value. The format for this is:\n";
    print "mysql://user:pass\@hostname/dbname\n";
    print "Where mysql can be one of the databases PHP supports, the most common\n";
    print "of these are mysql, msql and pgsql.\n";
    print "Please ensure proper permissions for config.php when including\n";
    print "sensitive passwords.\n\n";
    print "If the DSN is left empty (hit space and then return) the database\n";
    print "related code for address books will not be used.\n";
    print "\n";

    if ( $addrbook_dsn eq "" ) {
        $default_value = "Disabled";
    } else {
        $default_value = $addrbook_dsn;
    }
    print "[$WHT$addrbook_dsn$NRM]: $WHT";
    $new_dsn = <STDIN>;
    if ( $new_dsn eq "\n" ) {
        $new_dsn = "";
    } else {
        $new_dsn =~ s/[\r|\n]//g;
        $new_dsn =~ s/^\s+$//g;
    }
    return $new_dsn;
}

sub command92 {
    print "This is the name of the table you want to store the address book\n";
    print "data in, it defaults to 'address'\n";
    print "\n";
    print "[$WHT$addrbook_table$NRM]: $WHT";
    $new_table = <STDIN>;
    if ( $new_table eq "\n" ) {
        $new_table = $addrbook_table;
    } else {
        $new_table =~ s/[\r|\n]//g;
    }
    return $new_table;
}

sub command93 {
    print "If you want to store your users preferences in a database then\n";
    print "you need to set this DSN to a valid value. The format for this is:\n";
    print "mysql://user:pass\@hostname/dbname\n";
    print "Where mysql can be one of the databases PHP supports, the most common\n";
    print "of these are mysql, msql and pgsql.\n";
    print "Please ensure proper permissions for config.php when including\n";
    print "sensitive passwords.\n\n";
    print "If the DSN is left empty (hit space and then return) the database\n";
    print "related code for address books will not be used.\n";
    print "\n";

    if ( $prefs_dsn eq "" ) {
        $default_value = "Disabled";
    } else {
        $default_value = $prefs_dsn;
    }
    print "[$WHT$prefs_dsn$NRM]: $WHT";
    $new_dsn = <STDIN>;
    if ( $new_dsn eq "\n" ) {
        $new_dsn = "";
    } else {
        $new_dsn =~ s/[\r|\n]//g;
        $new_dsn =~ s/^\s+$//g;
    }
    return $new_dsn;
}

sub command94 {
    print "This is the name of the table you want to store the preferences\n";
    print "data in, it defaults to 'userprefs'\n";
    print "\n";
    print "[$WHT$prefs_table$NRM]: $WHT";
    $new_table = <STDIN>;
    if ( $new_table eq "\n" ) {
        $new_table = $prefs_table;
    } else {
        $new_table =~ s/[\r|\n]//g;
    }
    return $new_table;
}

sub command95 {
    print "This is the name of the field in which you want to store the\n";
    print "username of the person the prefs are for. It defaults to 'user'\n";
    print "\n";
    print "[$WHT$prefs_user_field$NRM]: $WHT";
    $new_field = <STDIN>;
    if ( $new_field eq "\n" ) {
        $new_field = $prefs_user_field;
    } else {
        $new_field =~ s/[\r|\n]//g;
    }
    return $new_field;
}

sub command96 {
    print "This is the name of the field in which you want to store the\n";
    print "preferences keyword. It defaults to 'prefkey'\n";
    print "\n";
    print "[$WHT$prefs_key_field$NRM]: $WHT";
    $new_field = <STDIN>;
    if ( $new_field eq "\n" ) {
        $new_field = $prefs_key_field;
    } else {
        $new_field =~ s/[\r|\n]//g;
    }
    return $new_field;
}

sub command97 {
    print "This is the name of the field in which you want to store the\n";
    print "preferences value. It defaults to 'prefval'\n";
    print "\n";
    print "[$WHT$prefs_val_field$NRM]: $WHT";
    $new_field = <STDIN>;
    if ( $new_field eq "\n" ) {
        $new_field = $prefs_val_field;
    } else {
        $new_field =~ s/[\r|\n]//g;
    }
    return $new_field;
}

sub command98 {
    print "If you want to store your global address book in a database then\n";
    print "you need to set this DSN to a valid value. The format for this is:\n";
    print "mysql://user:pass\@hostname/dbname\n";
    print "Where mysql can be one of the databases PHP supports, the most common\n";
    print "of these are mysql, msql and pgsql.\n";
    print "Please ensure proper permissions for config.php when including\n";
    print "sensitive passwords.\n\n";
    print "If the DSN is left empty (hit space and then return) the database\n";
    print "related code for global SQL address book will not be used.\n";
    print "\n";

    if ( $addrbook_global_dsn eq "" ) {
        $default_value = "Disabled";
    } else {
        $default_value = $addrbook_global_dsn;
    }
    print "[$WHT$addrbook_global_dsn$NRM]: $WHT";
    $new_dsn = <STDIN>;
    if ( $new_dsn eq "\n" ) {
        $new_dsn = "";
    } else {
        $new_dsn =~ s/[\r\n]//g;
        $new_dsn =~ s/^\s+$//g;
    }
    return $new_dsn;
}

sub command99 {
    print "This is the name of the table you want to store the global address book\n";
    print "data in. Default table name is 'global_abook'. Address book uses same\n";
    print "database format as personal address book.\n";
    print "\n";
    print "[$WHT$addrbook_global_table$NRM]: $WHT";
    $new_table = <STDIN>;
    if ( $new_table eq "\n" ) {
        $new_table = $addrbook_global_table;
    } else {
        $new_table =~ s/[\r\n]//g;
    }
    return $new_table;
}

sub command910 {
    print "This option controls users\' ability to add or modify records stored \n";
    print "in global address book\n";

    if ( lc($addrbook_global_writeable) eq 'true' ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow writing into global address book? (y/n) [$WHT$default_value$NRM]: $WHT";
    $addrbook_global_writeable = <STDIN>;
    if ( ( $addrbook_global_writeable =~ /^y\n/i ) || ( ( $addrbook_global_writeable =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $addrbook_global_writeable = 'true';
    } else {
        $addrbook_global_writeable = 'false';
    }
    return $addrbook_global_writeable;
}

sub command911 {
    print "Enable this option if you want to see listing of addresses stored \n";
    print "in global address book\n";

    if ( lc($addrbook_global_listing) eq 'true' ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Allow listing of global address book? (y/n) [$WHT$default_value$NRM]: $WHT";
    $addrbook_global_listing = <STDIN>;
    if ( ( $addrbook_global_listing =~ /^y\n/i ) || ( ( $addrbook_global_listing =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $addrbook_global_listing = 'true';
    } else {
        $addrbook_global_listing = 'false';
    }
    return $addrbook_global_listing;
}


####################################################################################
#### Languages ####
# Default language
sub commandA1 {
    print "SquirrelMail attempts to set the language in many ways.  If it\n";
    print "can not figure it out in another way, it will default to this\n";
    print "language.  Please use the code for the desired language.\n";
    print "\n";
    print "[$WHT$squirrelmail_default_language$NRM]: $WHT";
    $new_language = <STDIN>;
    if ( $new_language eq "\n" ) {
        $new_language = $squirrelmail_default_language;
    } else {
        $new_language =~ s/[\r|\n]//g;
        $new_language =~ s/^\s+$//g;
    }
    return $new_language;
}

# Default Charset
sub commandA2 {
    print "This option controls what character set is used when sending\n";
    print "mail and when sending HTML to the browser.\n";
    print "\n";
    print "This option is used only when default language is 'en_US'.\n";
    print "\n";

    print "[$WHT$default_charset$NRM]: $WHT";
    $new_default_charset = <STDIN>;
    if ( $new_default_charset eq "\n" ) {
        $new_default_charset = $default_charset;
    } else {
        $new_default_charset =~ s/[\r|\n]//g;
    }
    return $new_default_charset;
}

# Lossy encoding
sub commandA3 {
    print "Enable this option if you want to allow lossy charset encoding in message\n";
    print "composition pages. This option allows charset conversions when output\n";
    print "charset does not support all symbols used in original charset. Symbols\n";
    print "unsupported by output charset will be replaced with question marks.\n";
    print "\n";

    if ( lc($lossy_encoding) eq 'true' ) {
        $default_value = "y";
    } else {
        $default_value = "n";
    }
    print "Enable lossy encoding? (y/n) [$WHT$default_value$NRM]: $WHT";
    $lossy_encoding = <STDIN>;
    if ( ( $lossy_encoding =~ /^y\n/i ) || ( ( $lossy_encoding =~ /^\n/ ) && ( $default_value eq "y" ) ) ) {
        $lossy_encoding = 'true';
    } else {
        $lossy_encoding = 'false';
    }
    return $lossy_encoding;
}

#### End of menu functions
####################################################################################

sub save_data {
    $tab = "    ";
    if ( open( CF, ">config.php" ) ) {
        print CF "<?php\n";
        print CF "\n";

        print CF "/**\n";
        print CF " * SquirrelMail Configuration File\n";
        print CF " * Created using the configure script, conf.pl\n";
        print CF " */\n";
        print CF "\n";
        print CF "global \$version;\n";
    
        if ($print_config_version) {
            print CF "\$config_version = '$print_config_version';\n";
        }
    # integer
        print CF "\$config_use_color = $config_use_color;\n";
        print CF "\n";
    
    # string
        print CF "\$org_name      = \"$org_name\";\n";
        # string
    print CF "\$org_logo      = " . &change_to_SM_path($org_logo) . ";\n";
        $org_logo_width |= 0;
        $org_logo_height |= 0;
    # string
        print CF "\$org_logo_width  = '$org_logo_width';\n";
        # string
    print CF "\$org_logo_height = '$org_logo_height';\n";
    # string that can contain variables.
        print CF "\$org_title     = \"$org_title\";\n";
    # string
        print CF "\$signout_page  = " . &change_to_SM_path($signout_page) . ";\n";
    # string
        print CF "\$frame_top     = '$frame_top';\n";
        print CF "\n";

        print CF "\$provider_uri     = '$provider_uri';\n";
        print CF "\n";

        print CF "\$provider_name     = '$provider_name';\n";
        print CF "\n";

    # string that can contain variables
        print CF "\$motd = \"$motd\";\n";
        print CF "\n";

    # Language preferences    
    # string
        print CF "\$squirrelmail_default_language = '$squirrelmail_default_language';\n";
    # string
        print CF "\$default_charset       = '$default_charset';\n";
    # boolean
        print CF "\$lossy_encoding        = $lossy_encoding;\n";
        print CF "\n";

    # Server settings
    # string
        print CF "\$domain                 = '$domain';\n";
    # string
        print CF "\$imapServerAddress      = '$imapServerAddress';\n";
    # integer
        print CF "\$imapPort               = $imapPort;\n";
    # boolean
        print CF "\$useSendmail            = $useSendmail;\n";
    # string
        print CF "\$smtpServerAddress      = '$smtpServerAddress';\n";
    # integer
        print CF "\$smtpPort               = $smtpPort;\n";
        # string
        print CF "\$sendmail_path          = '$sendmail_path';\n";
        # string
        print CF "\$sendmail_args          = '$sendmail_args';\n";
    # boolean
#        print CF "\$use_authenticated_smtp = $use_authenticated_smtp;\n";
    # boolean
        print CF "\$pop_before_smtp        = $pop_before_smtp;\n";
    # string
        print CF "\$pop_before_smtp_host   = '$pop_before_smtp_host';\n";
    # string
        print CF "\$imap_server_type       = '$imap_server_type';\n";
    # boolean
        print CF "\$invert_time            = $invert_time;\n";
    # string
        print CF "\$optional_delimiter     = '$optional_delimiter';\n";
    # string
        print CF "\$encode_header_key      = '$encode_header_key';\n";
        print CF "\n";

    # string
        print CF "\$default_folder_prefix          = '$default_folder_prefix';\n";
    # string
        print CF "\$trash_folder                   = '$trash_folder';\n";
    # string
        print CF "\$sent_folder                    = '$sent_folder';\n";
    # string
        print CF "\$draft_folder                   = '$draft_folder';\n";
    # boolean
        print CF "\$default_move_to_trash          = $default_move_to_trash;\n";
    # boolean
        print CF "\$default_move_to_sent           = $default_move_to_sent;\n";
    # boolean
        print CF "\$default_save_as_draft          = $default_save_as_draft;\n";
    # boolean
        print CF "\$show_prefix_option             = $show_prefix_option;\n";
    # boolean
        print CF "\$list_special_folders_first     = $list_special_folders_first;\n";
    # boolean
        print CF "\$use_special_folder_color       = $use_special_folder_color;\n";
    # boolean
        print CF "\$auto_expunge                   = $auto_expunge;\n";
    # boolean
        print CF "\$default_sub_of_inbox           = $default_sub_of_inbox;\n";
    # boolean
        print CF "\$show_contain_subfolders_option = $show_contain_subfolders_option;\n";
    # integer
        print CF "\$default_unseen_notify          = $default_unseen_notify;\n";
    # integer
        print CF "\$default_unseen_type            = $default_unseen_type;\n";
    # boolean
        print CF "\$auto_create_special            = $auto_create_special;\n";
    # boolean
        print CF "\$delete_folder                  = $delete_folder;\n";
    # boolean
        print CF "\$noselect_fix_enable            = $noselect_fix_enable;\n";

        print CF "\n";

    # General options
    # string
        print CF "\$data_dir                 = " . &change_to_SM_path($data_dir) . ";\n";
    # string that can contain a variable
        print CF "\$attachment_dir           = " . &change_to_SM_path($attachment_dir) . ";\n";
    # integer
        print CF "\$dir_hash_level           = $dir_hash_level;\n";
    # string
        print CF "\$default_left_size        = '$default_left_size';\n";
    # boolean
        print CF "\$force_username_lowercase = $force_username_lowercase;\n";
    # boolean
        print CF "\$default_use_priority     = $default_use_priority;\n";
    # boolean
        print CF "\$hide_sm_attributions     = $hide_sm_attributions;\n";
    # boolean
        print CF "\$default_use_mdn          = $default_use_mdn;\n";
    # boolean
        print CF "\$edit_identity            = $edit_identity;\n";
    # boolean
        print CF "\$edit_name                = $edit_name;\n";
    # boolean
        print CF "\$hide_auth_header         = $hide_auth_header;\n";
    # boolean
        print CF "\$allow_thread_sort        = $allow_thread_sort;\n";
    # boolean
        print CF "\$allow_server_sort        = $allow_server_sort;\n";
        # boolean
        print CF "\$allow_charset_search     = $allow_charset_search;\n";
        # boolean
        print CF "\$uid_support              = $uid_support;\n";
        print CF "\n";
    
    # all plugins are strings
        for ( $ct = 0 ; $ct <= $#plugins ; $ct++ ) {
            print CF "\$plugins[$ct] = '$plugins[$ct]';\n";
        }
        print CF "\n";

    # strings
        print CF "\$theme_css = " . &change_to_SM_path($theme_css) . ";\n";
    if ( $theme_default eq '' ) { $theme_default = '0'; }
        print CF "\$theme_default = $theme_default;\n";

        for ( $count = 0 ; $count <= $#theme_name ; $count++ ) {
            print CF "\$theme[$count]['PATH'] = " . &change_to_SM_path($theme_path[$count]) . ";\n";
            # escape theme name so it can contain single quotes.
            $esc_name =  $theme_name[$count];
            $esc_name =~ s/\\/\\\\/g;
            $esc_name =~ s/'/\\'/g;
            print CF "\$theme[$count]['NAME'] = '$esc_name';\n";
        }
        print CF "\n";

        ## Address books
        # boolean
        print CF "\$default_use_javascript_addr_book = $default_use_javascript_addr_book;\n";
        for ( $count = 0 ; $count <= $#ldap_host ; $count++ ) {
            print CF "\$ldap_server[$count] = array(\n";
        # string
            print CF "    'host' => '$ldap_host[$count]',\n";
        # string
            print CF "    'base' => '$ldap_base[$count]'";
            if ( $ldap_name[$count] ) {
                print CF ",\n";
        # string
                print CF "    'name' => '$ldap_name[$count]'";
            }
            if ( $ldap_port[$count] ) {
                print CF ",\n";
        # integer
                print CF "    'port' => $ldap_port[$count]";
            }
            if ( $ldap_charset[$count] ) {
                print CF ",\n";
        # string
                print CF "    'charset' => '$ldap_charset[$count]'";
            }
            if ( $ldap_maxrows[$count] ) {
                print CF ",\n";
        # integer
                print CF "    'maxrows' => $ldap_maxrows[$count]";
            }
            if ( $ldap_binddn[$count] ) {
                print CF ",\n";
                # string
                print CF "    'binddn' => '$ldap_binddn[$count]'";
                if ( $ldap_bindpw[$count] ) {
                    print CF ",\n";
                    # string
                    print CF "    'bindpw' => '$ldap_bindpw[$count]'";
                }
            }
            if ( $ldap_protocol[$count] ) {
                print CF ",\n";
        # integer
                print CF "    'protocol' => $ldap_protocol[$count]";
            }
            print CF "\n";
            print CF ");\n";
            print CF "\n";
        }

        # Global file based address book
        # string
        print CF "\$abook_global_file = '$abook_global_file';\n";
        # boolean
        print CF "\$abook_global_file_writeable = $abook_global_file_writeable;\n";
        # boolean
        print CF "\$abook_global_file_listing = $abook_global_file_listing;\n";
        # integer
        print CF "\$abook_file_line_length = $abook_file_line_length;\n\n";

    ## Database
    # string
        print CF "\$addrbook_dsn = '$addrbook_dsn';\n";
    # string
        print CF "\$addrbook_table = '$addrbook_table';\n\n";
    # string
        print CF "\$prefs_dsn = '$prefs_dsn';\n";
    # string
        print CF "\$prefs_table = '$prefs_table';\n";
    # string
        print CF "\$prefs_user_field = '$prefs_user_field';\n";
    # string
        print CF "\$prefs_key_field = '$prefs_key_field';\n";
    # string
        print CF "\$prefs_val_field = '$prefs_val_field';\n";
    # string
        print CF "\$addrbook_global_dsn = '$addrbook_global_dsn';\n";
    # string
        print CF "\$addrbook_global_table = '$addrbook_global_table';\n";
    # boolean
        print CF "\$addrbook_global_writeable = $addrbook_global_writeable;\n";
    # boolean
        print CF "\$addrbook_global_listing = $addrbook_global_listing;\n\n";
    # boolean
        print CF "\$no_list_for_subscribe = $no_list_for_subscribe;\n";

    # string
        print CF "\$smtp_auth_mech = '$smtp_auth_mech';\n";
        print CF "\$imap_auth_mech = '$imap_auth_mech';\n";
        print CF "\$smtp_sitewide_user = '" . quote_single($smtp_sitewide_user) . "';\n";
        print CF "\$smtp_sitewide_pass = '" . quote_single($smtp_sitewide_pass) . "';\n";
    # boolean
        print CF "\$use_imap_tls = $use_imap_tls;\n";
        print CF "\$use_smtp_tls = $use_smtp_tls;\n";

        print CF "\$session_name = '$session_name';\n";

    # boolean
        print CF "\$only_secure_cookies     = $only_secure_cookies;\n";
        print CF "\$disable_security_tokens = $disable_security_tokens;\n";

    # string
        print CF "\$check_referrer          = '$check_referrer';\n";

        print CF "\n";
        print CF "\$config_location_base    = '$config_location_base';\n";

        print CF "\n";
        print CF "\@include SM_PATH . 'config/config_local.php';\n";
    
        print CF "\n";
        close CF;

        print "Data saved in config.php\n";
    } else {
        print "Error saving config.php: $!\n";
    }
}

sub set_defaults {
    clear_screen();
    print $WHT. "SquirrelMail Configuration : " . $NRM;
    if    ( $config == 1 ) { print "Read: config.php"; }
    elsif ( $config == 2 ) { print "Read: config_default.php"; }
    print "\n";
    print "---------------------------------------------------------\n";

    print "While we have been building SquirrelMail, we have discovered some\n";
    print "preferences that work better with some servers that don't work so\n";
    print "well with others.  If you select your IMAP server, this option will\n";
    print "set some pre-defined settings for that server.\n";
    print "\n";
    print "Please note that you will still need to go through and make sure\n";
    print "everything is correct.  This does not change everything.  There are\n";
    print "only a few settings that this will change.\n";
    print "\n";

    $continue = 0;
    while ( $continue != 1 ) {
        print "Please select your IMAP server:\n";
	print $list_supported_imap_servers;
        print "\n";
        print "    quit        = Do not change anything\n";
        print "Command >> ";
        $server = <STDIN>;
        $server =~ s/[\r|\n]//g;

        # variable is used to display additional messages.
        $message = "";

        print "\n";
        if ( $server eq "cyrus" ) {
            $imap_server_type               = "cyrus";
            $default_folder_prefix          = "";
            $trash_folder                   = "INBOX.Trash";
            $sent_folder                    = "INBOX.Sent";
            $draft_folder                   = "INBOX.Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = true;
            $show_contain_subfolders_option = false;
            $optional_delimiter             = ".";
            $disp_default_folder_prefix     = "<none>";

            $continue = 1;
        } elsif ( $server eq "uw" ) {
            $imap_server_type               = "uw";
            $default_folder_prefix          = "mail/";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = true;
            $default_sub_of_inbox           = false;
            $show_contain_subfolders_option = true;
            $optional_delimiter             = "/";
            $disp_default_folder_prefix     = $default_folder_prefix;
            $delete_folder                  = true;
            
            $continue = 1;
        } elsif ( $server eq "exchange" ) {
            $imap_server_type               = "exchange";
            $default_folder_prefix          = "";
            $default_sub_of_inbox           = true;
            $trash_folder                   = "INBOX/Deleted Items";
            $sent_folder                    = "INBOX/Sent Items";
            $drafts_folder                  = "INBOX/Drafts";
            $show_prefix_option             = false;
            $show_contain_subfolders_option = false;
            $optional_delimiter             = "detect";
            $disp_default_folder_prefix     = "<none>";

            $continue = 1;
        } elsif ( $server eq "courier" ) {
            $imap_server_type               = "courier";
            $default_folder_prefix          = "INBOX.";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = true;
            $show_contain_subfolders_option = false;
            $optional_delimiter             = ".";
            $disp_default_folder_prefix     = $default_folder_prefix;
            $delete_folder                  = true;
            
            $continue = 1;
        } elsif ( $server eq "macosx" ) {
            $imap_server_type               = "macosx";
            $default_folder_prefix          = "INBOX/";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = true;
            $show_contain_subfolders_option = false;
            $optional_delimiter             = "detect";
            $allow_charset_search           = false;
            $disp_default_folder_prefix     = $default_folder_prefix;

            $continue = 1;
        } elsif ( $server eq "hmailserver" ) {
            $imap_server_type               = "hmailserver";
            $default_folder_prefix          = "";
            $trash_folder                   = "INBOX.Trash";
            $sent_folder                    = "INBOX.Sent";
            $draft_folder                   = "INBOX.Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = true;
            $show_contain_subfolders_option = false;
            $optional_delimiter             = "detect";
            $allow_charset_search           = false;
            $disp_default_folder_prefix     = $default_folder_prefix;
            $delete_folder                  = false;
            $force_username_lowercase       = false;

            $continue = 1;
        } elsif ( $server eq "mercury32" ) {
            $imap_server_type               = "mercury32";
            $default_folder_prefix          = "";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = true;
            $show_contain_subfolders_option = true;
            $optional_delimiter             = "detect";
            $delete_folder                  = true;
            $force_username_lowercase       = true;

            $continue = 1;
        } elsif ( $server eq "dovecot" ) {
            $imap_server_type               = "dovecot";
            $default_folder_prefix          = "";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = false;
            $show_contain_subfolders_option = false;
            $delete_folder                  = false;
            $force_username_lowercase       = true;
            $optional_delimiter             = "detect";
            $disp_default_folder_prefix     = "<none>";

            $continue = 1;
        } elsif ( $server eq "bincimap" ) {
            $imap_server_type               = "bincimap";
            $default_folder_prefix          = "INBOX/";
            $trash_folder                   = "Trash";
            $sent_folder                    = "Sent";
            $draft_folder                   = "Drafts";
            $show_prefix_option             = false;
            $default_sub_of_inbox           = false;
            $show_contain_subfolders_option = false;
            $delete_folder                  = true;
            $force_username_lowercase       = false;
            $optional_delimiter             = "detect";
            $disp_default_folder_prefix     = $default_folder_prefix;

            # Default folder prefix depends on used depot.
            $message = "\nIf you use IMAPdir depot, you must set default folder prefix to empty string.\n";

            $continue = 1;
        } elsif ( $server eq "gmail" ) {
            $imap_server_type               = "gmail";
            $default_folder_prefix          = "";
            $trash_folder                   = "[Gmail]/Trash";
            $default_move_to_trash          = true;
            $sent_folder                    = "[Gmail]/Sent Mail";
            $draft_folder                   = "[Gmail]/Drafts";
            $auto_create_special            = false;
            $show_prefix_option             = false;
            $default_sub_of_inbox           = false;
            $show_contain_subfolders_option = false;
            $delete_folder                  = true;
            $force_username_lowercase       = false;
            $optional_delimiter             = "/";
            $disp_default_folder_prefix     = "<none>";
            $domain                         = "gmail.com";
            $imapServerAddress              = "imap.gmail.com";
            $imapPort                       = 993;
            $use_imap_tls                   = true;
            $imap_auth_mech                 = "login";
            $smtpServerAddress              = "smtp.gmail.com";
            $smtpPort                       = 465;
            $pop_before_smtp                = false;
            $useSendmail                    = false;
            $use_smtp_tls                   = true;
            $smtp_auth_mech                 = "login";
            $continue = 1;

            # Gmail changes system folder names (Drafts, Sent, Trash) out
            # from under you when the user changes language settings
            $message = "\nNOTE!  When a user changes languages in Gmail's interface, the\n"
                     . "Drafts, Sent and Trash folder names are changed to localized\n"
                     . "versions thereof.  To see those folders correctly in SquirrelMail,\n"
                     . "the user should change the SquirrelMail language to match.\n"
                     . "Moreover, SquirrelMail then needs to be told what folders to use\n"
                     . "for Drafts, Sent and Trash in Options --> Folder Preferences.\n"
                     . "These default settings will only correctly find the Sent, Trash\n"
                     . "and Drafts folders if both Gmail and SquirrelMail languages are\n"
                     . "set to English.\n\n"
                     . "Also note that in some regions (Europe?), the default folder\n"
                     . "names (see main menu selection 3. Folder Defaults) are different\n"
                     . "(they may need to have the prefix \"[Google Mail]\" instead of\n" 
                     . "\"[Gmail]\") and \"Trash\" may be called \"Bin\" instead.\n";

        } elsif ( $server eq "quit" ) {
            $continue = 1;
        } else {
            $disp_default_folder_prefix = $default_folder_prefix;
            print "Unrecognized server: $server\n";
            print "\n";
        }

        print "              imap_server_type = $imap_server_type\n";
        print "         default_folder_prefix = $disp_default_folder_prefix\n";
        print "                  trash_folder = $trash_folder\n";
        print "                   sent_folder = $sent_folder\n";
        print "                  draft_folder = $draft_folder\n";
        print "            show_prefix_option = $show_prefix_option\n";
        print "          default_sub_of_inbox = $default_sub_of_inbox\n";
        print "show_contain_subfolders_option = $show_contain_subfolders_option\n";
        print "            optional_delimiter = $optional_delimiter\n";
        print "                 delete_folder = $delete_folder\n";

        print "$message";
    }
    print "\nPress enter to continue...";
    $tmp = <STDIN>;
}

# This subroutine corrects relative paths to ensure they
# will work within the SM space. If the path falls within
# the SM directory tree, the SM_PATH variable will be 
# prepended to the path, if not, then the path will be
# converted to an absolute path, e.g.
#   '../images/logo.gif'        --> SM_PATH . 'images/logo.gif'
#   '../../someplace/data'      --> '/absolute/path/someplace/data'
#   'images/logo.gif'           --> SM_PATH . 'config/images/logo.gif'
#   '/absolute/path/logo.gif'   --> '/absolute/path/logo.gif'
#   'C:\absolute\path\logo.gif' --> 'C:\absolute\path\logo.gif'
#   'http://whatever/'          --> 'http://whatever'
#   $some_var/path              --> "$some_var/path"
sub change_to_SM_path() {
    my ($old_path) = @_;
    my $new_path = '';
    my @rel_path;
    my @abs_path;
    my $subdir;

    # If the path is absolute, don't bother.
    return "\'" . $old_path . "\'"  if ( $old_path eq '');
    return "\'" . $old_path . "\'"  if ( $old_path =~ /^(\/|http)/ );
    return "\'" . $old_path . "\'"  if ( $old_path =~ /^\w:(\\|\/)/ );
    return $old_path                if ( $old_path =~ /^\'(\/|http)/ );
    return $old_path                if ( $old_path =~ /^\'\w:\// );
    return $old_path                if ( $old_path =~ /^SM_PATH/);
   
    if ( $old_path =~ /^\$/ ) {
        # check if it's a single var, or a $var/path combination
        # if it's $var/path, enclose in ""
        if ( $old_path =~ /\// ) {
            return '"'.$old_path.'"';
        }
        return $old_path;
    }
    
    # Remove remaining '
    $old_path =~ s/\'//g;
    
    # For relative paths, split on '../'
    @rel_path = split(/\.\.\//, $old_path);

    if ( $#rel_path > 1 ) {
        # more than two levels away. Make it absolute.
        @abs_path = split(/\//, $dir);
        
        # Lop off the relative pieces of the absolute path..
        for ( $i = 0; $i <= $#rel_path; $i++ ) {
            pop @abs_path;
            shift @rel_path;
        }
        push @abs_path, @rel_path;
        $new_path = "\'" . join('/', @abs_path) . "\'";
    } elsif ( $#rel_path > 0 ) {
        # it's within the SM tree, prepend SM_PATH
        $new_path = $old_path;
        $new_path =~ s/^\.\.\//SM_PATH . \'/;
        $new_path .= "\'";
    } else {
        # Last, it's a relative path without any leading '.'
    # Prepend SM_PATH and config, since the paths are 
    # relative to the config directory
        $new_path = "SM_PATH . \'config/" . $old_path . "\'";
    }
  return $new_path;
}


# Change SM_PATH to admin-friendly version, e.g.:
#  SM_PATH . 'images/logo.gif' --> '../images/logo.gif'
#  SM_PATH . 'config/some.php' --> 'some.php'
#  '/absolute/path/logo.gif'   --> '/absolute/path/logo.gif'
#  'http://whatever/'          --> 'http://whatever'
sub change_to_rel_path() {
    my ($old_path) = @_;
    my $new_path = $old_path;

    if ( $old_path =~ /^SM_PATH/ ) {
        $new_path =~ s/^SM_PATH . \'/\.\.\//;
        $new_path =~ s/\.\.\/config\///;
    }

    return $new_path;
}

# Attempts to auto-detect if a specific auth mechanism is supported.
# Called by 'command112a' and 'command112b'
# ARGS: service-name (IMAP or SMTP), host:port, mech-name (ie. CRAM-MD5)
sub detect_auth_support {
    # Try loading IO::Socket
    unless (eval("use IO::Socket; 1")) {
        print "Perl IO::Socket module is not available.";
        return undef;
    }

    # Misc setup
    my $service = shift;
    my $host = shift;
    my $mech = shift;
    # Sanity checks
    if ((!defined($service)) or (!defined($host)) or (!defined($mech))) {
      # Error - wrong # of args
      print "BAD ARGS!\n";
      return undef;
    }
    
    if ($service eq 'SMTP') {
        $cmd = "AUTH $mech\r\n";
        $logout = "QUIT\r\n";
    } elsif ($service eq 'IMAP') {
        $cmd = "A01 AUTHENTICATE $mech\n";
        $logout = "C01 LOGOUT\n";
    } else {
        # unknown service - whoops.
        return undef;
    }

    # Get this show on the road
    my $sock=IO::Socket::INET->new($host);
    if (!defined($sock)) {
        # Connect failed
        return undef;
    }
    my $discard = <$sock>; # Server greeting/banner - who cares..

    if ($service eq 'SMTP') {
        # Say hello first..
        print $sock "HELO $domain\r\n";
        $discard = <$sock>; # Yeah yeah, you're happy to see me..
    }
    print $sock $cmd;

    my $response = <$sock>;
    chomp($response);
    if (!defined($response)) {
        return undef;
    }

    # So at this point, we have a response, and it is (hopefully) valid.
    if ($service eq 'SMTP') {
        if (!($response =~ /^334/)) {
            # Not supported
            print $sock $logout;
            close $sock;
            return 'NO';
	}
    } elsif ($service eq 'IMAP') {
        if ($response =~ /^A01/) {
            # Not supported
            close $sock;
            return 'NO';
        }
    } else {
        # Unknown service - this shouldn't be able to happen.
        close $sock;
        return undef;
    }

    # If it gets here, the mech is supported
    print $sock "*\n";  # Attempt to cancel authentication
    print $sock $logout; # Try to log out, but we don't really care if this fails
    close $sock;
    return 'YES';
}

sub clear_screen() {
    if ( $^O =~ /^mswin/i) {
        system "cls";
    } else {
        system "clear";
    }
}

# Quotes safely strings containing single quote
sub quote_single($) {
	my $string = shift(@_);
	$string =~ s/\'/\\'/g;
	return $string;
}

# trims whitespace
# Example code from O'Reilly Perl Cookbook
sub trim {
    my @out = @_;
    for (@out) {
        s/^\s+//;
        s/\s+$//;
    }
    return wantarray ? @out : $out[0];
}


# checks IMAP mailbox name. Detect 8bit input and special characters
# returns 0 (folder name is not correct) or 1 (folder name is correct)
sub check_imap_folder($) {
    my $folder_name = shift(@_);

    if ($folder_name =~ /[\x80-\xFFFF]/) {
        print "Folder name contains 8bit characters. Configuration utility requires\n";
        print "UTF7-IMAP encoded folder names.\n";
        print "Press enter to continue...";
        my $tmp = <STDIN>;
        return 0;
    } elsif ($folder_name =~ /[&\*\%]/) {
        # check for ampersand and list-wildcards
        print "Folder name contains special UTF7-IMAP characters.\n";
        print "Are you sure that folder name is correct? (yN): ";
        my $tmp = <STDIN>;
        $tmp = lc(trim($tmp));
        if ($tmp =~ /^y$/) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 1;
    }
}
