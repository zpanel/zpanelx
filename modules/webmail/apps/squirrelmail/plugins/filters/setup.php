<?php
/**
 * Message and Spam Filter Plugin - Setup script
 *
 * This plugin filters your inbox into different folders based upon given
 * criteria.  It is most useful for people who are subscibed to mailing lists
 * to help organize their messages.  The argument stands that filtering is
 * not the place of the client, which is why this has been made a plugin for
 * SquirrelMail.  You may be better off using products such as Sieve or
 * Procmail to do your filtering so it happens even when SquirrelMail isn't
 * running.
 *
 * If you need help with this, or see improvements that can be made, please
 * email me directly at the address above.  I definately welcome suggestions
 * and comments.  This plugin, as is the case with all SquirrelMail plugins,
 * is not directly supported by the developers.  Please come to me off the
 * mailing list if you have trouble with it.
 *
 * Also view plugins/README.plugins for more information.
 *
 * @version $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @copyright (c) 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package plugins
 * @subpackage filters
 */

/** SquirrelMail required files. */
require_once(SM_PATH . 'plugins/filters/filters.php');

/**
 * Imap connection control
 *
 * Set this to true if you have problems -- check the README file
 * Note:  This doesn't work all of the time (No idea why)
 *        Seems to be related to UW
 * @global bool $UseSeparateImapConnection
 */
global $UseSeparateImapConnection;
$UseSeparateImapConnection = false;

/**
 * User level spam filters control
 *
 * Set this to false if you do not want the user to be able to enable
 * spam filters
 * @global bool $AllowSpamFilters
 */
global $AllowSpamFilters;
$AllowSpamFilters = true;

/**
 * SpamFilters YourHop Setting
 *
 * Set this to a string containing something unique to the line in the
 * header you want me to find IPs to scan the databases with.  For example,
 * All the email coming IN from the internet to my site has a line in
 * the header that looks like (all on one line):
 * Received: [from usw-sf-list1.sourceforge.net (usw-sf-fw2.sourceforge.net
 *    [216.136.171.252]) by firewall.persistence.com (SYSADMIN-antispam
 *     0.2) with
 * Since this line indicates the FIRST hop the email takes into my network,
 * I set my SpamFilters_YourHop to 'by firewall.persistence.com' but any
 * case-sensitive string will do.  You can set it to something found on
 * every line in the header (like ' ') if you want to scan all IPs in
 * the header (lots of false alarms here tho).
 * @global string $SpamFilters_YourHop
 */
global $SpamFilters_YourHop;
$SpamFilters_YourHop = ' ';

/**
 * Commercial Spam Filters Control
 *
 * Some of the SPAM filters are COMMERCIAL and require a fee. If your users
 * select them and you're not allowed to use them, it will make SPAM filtering
 * very slow. If you don't want them to even be offered to the users, you
 * should set SpamFilters_ShowCommercial to false.
 * @global bool $SpamFilters_ShowCommercial
 */
global $SpamFilters_ShowCommercial;
$SpamFilters_ShowCommercial = false;

/**
 * SpamFiltring Cache
 *
 * A cache of IPs we've already checked or are known bad boys or good boys
 * ie. $SpamFilters_DNScache["210.54.220.18"] = true;
 * would tell filters to not even bother doing the DNS queries for that
 * IP and any email coming from it are SPAM - false would mean that any
 * email coming from it would NOT be SPAM
 * @global array $SpamFilters_DNScache
 */
global $SpamFilters_DNScache;

/**
 * Path to bulkquery program
 *
 * Absolute path to the bulkquery program. Leave blank if you don't have
 * bulkquery compiled, installed, and lwresd running. See the README file
 * in the bulkquery directory for more information on using bulkquery.
 * @global string $SpamFilters_BulkQuery
 */
global $SpamFilters_BulkQuery;
$SpamFilters_BulkQuery = '';

/**
 * Shared filtering cache control
 *
 * Do you want to use a shared file for the DNS cache or a session variable?
 * Using a shared file means that every user can benefit from any queries
 * made by other users. The shared file is named "dnscache" and is in the
 * data directory.
 * @global bool $SpamFilters_SharedCache
 */
global $SpamFilters_SharedCache;
$SpamFilters_SharedCache = true;

/**
 * DNS query TTL
 *
 * How long should DNS query results be cached for by default (in seconds)?
 * @global integer $SpamFilters_CacheTTL
 */
global $SpamFilters_CacheTTL;
$SpamFilters_CacheTTL = 7200;

/**
 * Init plugin
 * @access private
 */
function squirrelmail_plugin_init_filters() {
    global $squirrelmail_plugin_hooks;

    if (sqgetGlobalVar('mailbox',$mailbox,SQ_FORM)) {
	sqgetGlobalVar('mailbox',$mailbox,SQ_FORM);
    } else {
        $mailbox = 'INBOX';
    }

    $squirrelmail_plugin_hooks['left_main_before']['filters'] = 'start_filters';
    if (isset($mailbox) && $mailbox == 'INBOX') {
        $squirrelmail_plugin_hooks['right_main_after_header']['filters'] = 'start_filters';
    }
    $squirrelmail_plugin_hooks['optpage_register_block']['filters'] = 'filters_optpage_register_block';
    $squirrelmail_plugin_hooks['special_mailbox']['filters'] = 'filters_special_mailbox';
    $squirrelmail_plugin_hooks['rename_or_delete_folder']['filters'] = 'update_for_folder';
    $squirrelmail_plugin_hooks['webmail_bottom']['filters'] = 'start_filters';
}

/**
 * Report spam folder as special mailbox
 * @param string $mb variable used by hook
 * @return string spam folder name
 * @access private 
 */
function filters_special_mailbox( $mb ) {
    global $data_dir, $username;

    return( $mb == getPref($data_dir, $username, 'filters_spam_folder', 'na' ) );

}

/**
 * Register option blocks
 * @access private
 */
function filters_optpage_register_block() {
    global $optpage_blocks;
    global $AllowSpamFilters;

    $optpage_blocks[] = array(
        'name' => _("Message Filters"),
        'url'  => '../plugins/filters/options.php',
        'desc' => _("Filtering enables messages with different criteria to be automatically filtered into different folders for easier organization."),
        'js'   => false
    );

    if ($AllowSpamFilters) {
        $optpage_blocks[] = array(
            'name' => _("SPAM Filters"),
            'url'  => '../plugins/filters/spamoptions.php',
            'desc' => _("SPAM filters allow you to select from various DNS based blacklists to detect junk email in your INBOX and move it to another folder (like Trash)."),
            'js'   => false
        );
    }
}
?>