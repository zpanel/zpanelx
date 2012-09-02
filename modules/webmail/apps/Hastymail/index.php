<?php

/*  index.php: Main index file. All requests start here 
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id: index.php 2007 2011-11-11 03:58:31Z sailfrog $
*/

/* Important defaults and base includes
----------------------------------------------------------------*/

/* configuration file */
$hm2_config = '/etc/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.rc';

/* include file prefix. This should be left blank unless you want to use an
   absolute path for file includes. In that case it should be set to a
   filesystem path ending with a delimiter that leads to the main Hastymail2
   directory, for example:
   $include_path = '/var/www/hastymail2/'
 */
$include_path = '';

/* the filesystem delimiter to use when building include statements */
$fd = '/';

/* capture any accidental output */
ob_start();

/* Timer debug preperation, used by the show_imap_debug hastymail2.conf setting. */
$page_start = microtime();

/* Required includes */
require_once($include_path.'lib'.$fd.'misc_functions.php');    /* various helpers */
require_once($include_path.'lib'.$fd.'utility_classes.php');   /* base classes    */
require_once($include_path.'lib'.$fd.'url_action_class.php');  /* GET processing  */
require_once($include_path.'lib'.$fd.'imap_class.php');        /* IMAP routines   */
require_once($include_path.'lib'.$fd.'site_page_class.php');   /* print functions */

/* Read in the site configuration file */
$conf = get_config($hm2_config);

/* Get the PHP version */
$phpversion = get_php_version();

/* Get the current page URL. */
$sticky_url = get_page_url();

/* Generate a unique page id. */
$page_id = md5(uniqid(rand(),1));

/* Define the current version. */
$hastymail_version = 'Hastymail2 1.1 RC2';


/* Data structures used by different parts of the program
----------------------------------------------------------------*/

/* Available languages. Translation files are located in the
   lang directory. They are named such that they match the keys
   of this array, but with ".php" extensions (lang/en_US.php).
   This array also defines the contents of the Language dropdown
   on the options page.
*/
$langs = array(
    'bg_BG' => 'Bulgarian',
    'ca_ES' => 'Catalan',
    'zh_CN' => 'Chinese',
    'nl_NL' => 'Dutch',
    'en_US' => 'English',
    'fi_FI' => 'Finnish',
    'fr_FR' => 'French',
    'de_DE' => 'German',
    'gr_GR' => 'Greek',
    'it_IT' => 'Italian',
    'ja_JP' => 'Japanese',
    'pl_PL' => 'Polish',
    'ro_RO' => 'Romanian',
    'ru_RU' => 'Russian',
    'es_ES' => 'Spanish',
    'tr_TR' => 'Turkish',
    'uk_UA' => 'Ukranian',
);

/* Plugin display hooks. Plugins use these hooks to insert content into
   existing Hastymail pages. Some are generic and occur on every page while
   some only execute on specific pages */
$available_display_hooks = array(
    'page_top',                   'icon',                   'clock',
    'menu',                       'folder_list_top',        'folder_list_bottom',
    'notices_top',                'notices_bottom',         'content_bottom',
    'footer',                     'mailbox_top',            'mailbox_meta',
    'mailbox_sort_form',          'mailbox_controls_1',     'mailbox_controls_2',
    'mailbox_search',             'mailbox_bottom',         'message_top',
    'message_meta',               'message_headers_bottom', 'message_bottom',
    'new_page_top',               'new_page_title_row',     'new_page_controls',
    'new_page_bottom',            'search_page_top',        'search_result_meta',
    'search_result_controls',     'search_result_bottom',   'search_form_top',
    'search_form_bottom',         'search_page_bottom',     'about_page_top',
    'about_table_bottom',         'about_page_bottom',      'options_page_top',
    'options_page_title_row',     'general_options_table',  'folder_options_table',
    'message_options_table',      'mailbox_options_table',  'new_options_table',
    'options_page_bottom',        'contacts_page_top',      'contact_detail_top',
    'contact_detail_bottom',      'contacts_quick_links',   'existing_contacts_top',
    'existing_contacts_bottom',   'contacts_page_bottom',   'import_contact_form',
    'add_contact_email_table',    'add_contact_name_table', 'add_contact_address_table',
    'add_contact_phone_table',    'add_contact_org_table',  'folders_page_top',
    'folder_controls_bottom',     'folder_options_top',     'folder_options_bottom',
    'folders_page_bottom',        'compose_options_table',  'compose_top',
    'compose_form_top',           'compose_form_bottom',    'compose_contacts_top',
    'compose_contacts_bottom',    'compose_above_from',     'compose_options',
    'compose_after_message',      'compose_bottom',         'message_body_top',
    'message_parts_table',        'compose_page_to_row',    'compose_page_cc_row',
    'compose_page_bcc_row',       'compose_after_options',  'message_headers_bottom',
    'message_body_bottom',        'message_links',          'message_part_headers_top',
    'message_part_headers_bottom','message_prev_next_links','msglist_after_subject',
);

/* Plugin work hooks. Plugins can gain access to internal data before the
   the content for the requested page is built. This array defines the default
   work hooks. */
$available_work_hooks  = array(
    'init',                 'thread_view_start',            'about_page_start', 
    'not_found_start',      'search_page_start',            'folders_page_start',
    'logged_out',           'mailbox_page_start',           'message_page_start',
    'compose_page_start',   'options_page_start',           'contacts_page_start',
    'profile_page_start',   'new_page_start',               'update_settings',
    'message_send',         'compose_contact_list',         'first_time_login',
    'just_logged_in',       'register_contacts_source',     'on_login',
    'page_end',             'compose_after_send',           'message_save',
    'logged_out_init',      'mailbox_page_selected',        'message_page_selected',
    'imap_action',          'after_imap_action',            'before_logout',
    'set_config_value',
);

/* HTML message filtering package to use. Available options are:

   htmlpure    This is the most secure HTML filter and sanitizer, but it
               is also one of the slowest. If you want to use this be sure
               to setup the pure_serializer_path setting listed below to
               get the best performance.
   htmlawed    This is a newer HTML filer that can also correct some HTML
               compliance problems. It is lightweight and fast.
   legacy      This is the htmlfilter we have used since the first version.
               It is fast but has no HTML cleanup capability and is not
               actively developed anymore.
   none        Setting this to 'none' means that NO HTML FILTERING WILL
               BE USED. THIS IS EXTEMELY DANGEROUS UNLESS YOU CAN VERIFY
               THE SOURCE AND VALIDITY OF ALL HTML FORMATTED MESSAGES.
*/
$filter_backend = 'htmlpure';

/* If the filter_backend is set to htmlpure then your should enable a
   cache location for the serializer to speed up the filter. You can
   do so by setting the following to a directory to use as a cache. This
   directory MUST be writable by the user your web server software runs
   as (just like the attachment and user setting directories). To disable
   this caching in the filter set this to false. If the configured
   directory is not writable then the cache will be disabled. */

$pure_serializer_path = '/var/hastymail2/serializer_cache';

/* This defines HTML tags the filter allows when displaying an HTML 
   message part. We use a white-list approach to HTML message types.
   It can sometimes cause problems with accurate rendering but the
   additional security is worth it. Note that htmlpure has it's own
   whitelist so this list is not used for that filter. */
$allowed_tag_list  = array(
    'table', 'tr', 'td', 'tbody', 'th', 'ul', 'ol', 'li', 'hr',
    'em', 'u', 'font', 'br', 'strong', 'span', 'a', 'p', 'img',
    'blockquote', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
);

/* Mbstring available charsets. This is used to validate the character set
   defined in a message part. */
$mb_charset_codes = array_flip(array(
    'UCS-4',        'UCS-4BE',      'UCS-4LE',      'UCS-2',        'UCS-2BE',
    'UCS-2LE',      'UTF-32',       'UTF-32BE',     'UTF-32LE',     'UTF-16',
    'UTF-16BE',     'UTF-16LE',     'UTF-7',        'UTF7-IMAP',    'UTF-8',
    'ASCII',        'EUC-JP',       'SJIS',         'EUCJP-WIN',    'SJIS-WIN',
    'ISO-2022-JP',  'JIS',          'ISO-8859-1',   'ISO-8859-2',   'ISO-8859-3',
    'ISO-8859-4',   'ISO-8859-5',   'ISO-8859-6',   'ISO-8859-7',   'ISO-8859-8',
    'ISO-8859-9',   'ISO-8859-10',  'ISO-8859-13',  'ISO-8859-14',  'ISO-8859-15',
    'EUC-CN',       'CP936',        'HZ',           'EUC-TW',       'CP950',
    'BIG-5',        'BIG5',         'EUC-KR',       'UHC',          'CP949',
    'ISO-2022-KR',  'WINDOWS-1251', 'CP1251',       'WINDOWS-1252', 'CP1252',
    'CP866',        'IBM866',       'KOI8-R',       'GB2312'
));

/* Available internal charset conversions. These are defined in langs/charsets.php
   and are only used if PHP mbstring functionality is not available (this method is
   more limited and less efficient). */
$charset_codes = array(
    'iso-8859-1',   'iso-8859-2',   'iso-8859-3' ,  'iso-8859-4',
    'iso-8859-5',   'iso-8859-6',   'iso-8859-7',   'iso-8859-8',
    'iso-8859-9',   'iso-8859-10',  'iso-8859-11',  'iso-8859-14',
    'iso-8859-15',  'iso-8859-16',  'koi8-r',       'koi8-u',
    'windows-1252', 'windows-1251', 'ibm-850',      'windows-1256'
);

/* Sort types available for server side sorting. These define the sort
   options available when the IMAP server supports the SORT extension.
   The entries beginning with "R_" are the same sorting methods as the
   entries without the "R_" prefix but the order is reversed. The number
   values are the index of the display string defined in the language
   translation files. */
$sort_types = array( 
    'ARRIVAL'   => 279, 'R_ARRIVAL' => 280,
    'DATE'      => 281, 'R_DATE'    => 282,
    'FROM'      => 283, 'R_FROM'    => 284,
    'SUBJECT'   => 285, 'R_SUBJECT' => 286,
    'CC'        => 287, 'R_CC'      => 288,
    'TO'        => 289, 'R_TO'      => 290,
    'R_SIZE'    => 291, 'SIZE'      => 292,
    'THREAD_R'  => 293, 'THREAD_O'  => 294,
);

/* Sort types for client side sorting. When server side sorting is not
   available we provide some client side sorting ability to fall back on.
   it is more limited and definitely not as fast as server side. */
$client_sort_types = array (
    'ARRIVAL'   => 279, 'R_ARRIVAL' => 280,
    'DATE'      => 281, 'R_DATE'    => 282,
    'FROM'      => 283, 'R_FROM'    => 284,
    'SUBJECT'   => 285, 'R_SUBJECT' => 286,
);

/* Sort filters. For IMAP servers who support the SORT extension a message
   filtering option is available. These define the IMAP keywords available,
   the numbers represent the language file index of the string to be displayed
   for each option */
$sort_filters = array(
    'ALL'        => 113, 'UNSEEN'   => 114, 
    'SEEN'       => 115, 'FLAGGED'  => 116, 
    'UNFLAGGED'  => 117, 'ANSWERED' => 118, 
    'UNANSWERED' => 119, 'DELETED'  => 295, 
    'UNDELETED'  => 296, 
);

/* Main application pages. Pages in Hastymail2 use a simple page=some_page URL
   argument. This defines the internal pages in the program and are used to validate
   the page request. Plugins can add new pages to this dynamically. */
$app_pages = array(
    'login',    'logout',      'new',     'inline_image',
    'contacts', 'profile',     'options', 'compose',
    'search',   'thread_view', 'mailbox', 'message',
    'about',    'folders',     'contact_groups', 'not_found',
);

/* IMAP SEARCH CHARSET options. Defines what character set options are
   availble to be used with an IMAP search command. */
$imap_search_charsets = array(
    'UTF-8',
    'US-ASCII',
    '',
);

/* list of IMAP keywords to validate against. This is used to check user
   input that is supplied to an IMAP command.  */
$imap_keywords = array(
    'ARRIVAL',    'DATE',    'FROM',      'SUBJECT',
    'CC',         'TO',      'SIZE',      'UNSEEN',
    'SEEN',       'FLAGGED', 'UNFLAGGED', 'ANSWERED',
    'UNANSWERED', 'DELETED', 'UNDELETED', 'TEXT',
    'ALL',
);

/* Viewabled message parts. This defines what types of messages parts
   Hastymail2 will let the browser view. The array keys are the MIME
   type and subtype of the message part, and the values must be one of
   text, image, html, or frame. It is also possible to use plugins to
   add support for additional content types. */
$message_part_types = array( 
    'message/disposition-notification'   => 'text',   /* text part for MDN                       */
    'message/delivery-status'            => 'text',   /* text part for message bounce            */
    'message/rfc822-headers'             => 'text',   /* text part for message headers           */
    'text/csv'                           => 'text',   /* comma separated values                  */
    'text/plain'                         => 'text',   /* normal text message                     */
    'text/unknown'                       => 'text',   /* normal text message                     */
    'text/html'                          => 'html',   /* HTML message (blech)                    */
    'text/x-vcard'                       => 'text',   /* Vcard                                   */
    'text/calendar'                      => 'text',   /* Vcal                                    */
    'text/x-vCalendar'                   => 'text',   /* Vcal                                    */
    'text/x-sql'                         => 'text',   /* sql                                     */
    'text/x-comma-separated-values'      => 'text',   /* CSV                                     */
    'text/enriched'                      => 'text',   /* enriched text                           */
    'text/rfc822-headers'                => 'text',   /* another text part for message headers   */
    'text/x-diff'                        => 'text',   /* patch/diff                              */
    'text/x-patch'                       => 'text',   /* patch/diff                              */
    'image/jpeg'                         => 'image',  /* JPEG images                             */
    'image/pjpeg'                        => 'image',  /* JPEG images                             */
    'image/jpg'                          => 'image',  /* JPEG images                             */
    'image/png'                          => 'image',  /* PNG images                              */
    'image/bmp'                          => 'image',  /* BMP images                              */
    'image/gif'                          => 'image',  /* GIF images                              */
    'application/pgp-signature'          => 'text',   /* PGP signatures                          */
    'application/x-httpd-php'            => 'text',   /* PHP source code                         */
    'application/pdf'                    => 'frame',  /* PDF document                            */
);

/* Small headers available for user selection. The message view allows users
   to select which headers are visible. This list defines the availble selections
   on the options page. */
$small_header_options = array(
    'subject',            'from',          'to',               'date',
    'cc',                 'x-spam-status', 'x-spam-level',     'envelope-to',
    'received',           'content-type',  'message-id',       'sender',
    'list-id',            'precedence',    'dilevery-date',    'x-priority',
    'in-reply-to',        'references',    'list-unsubscribe', 'list-subscribe',
    'IMAP message flags', 'x-mailer',      'user-agent',       'content-transfer-encoding'
);

/* Message headers to search for the add contact dropdown. The add contact option is
   on the mesage view page and collects email addresses from the header fields defined
   in this array. */
$add_contact_headers = array(
    'sender',
    'x-envelope-from',
    'from',
    'to',
    'reply-to',
    'cc',
);

/* Date format options. These define the date format and display string
   for the date format settings on the options page. The array keys are
   the PHP date() command format strings and the values are what users
   see in the date format dropdown. */
$date_formats = array(
    'm/d/y'  => 'mm/dd/yy',
    'm/d/Y'  => 'mm/dd/yyyy',
    'm-d-y'  => 'mm-dd-yy',
    'm/d/Y'  => 'mm-dd-yyyy',
    'M j, Y' => 'mon dd, yyyy',
    'M j, y' => 'mon dd, yy',
    'M j'    => 'mon dd   ',
    'F d, Y' => 'month dd, yyyy',
    'F d, y' => 'month dd, yy',
    'r'      => 'rfc822',
    'd/m/Y'  => 'dd/mm/yyyy ',
    'd/m/y'  => 'dd/mm/yy',
    'Y-m-d'  => 'yyyy-mm-dd',
    'y-m-d'  => 'yy-mm-dd',
    'd.m.Y'  => 'dd.mm.yyyy',
    'd.m.y'  => 'dd.mm.yy',
);

/* Time format options. Same as the date options above, the keys
   are PHP date format strings the values are display strings. */
$time_formats = array(
    'g:i:s a' => '12:00:00',
    'H:i:s'   => '24:00:00',
    'g:i a'   => '12:00',
    'H:i'     => '24:00',
);

/* First page after login options. Defines the available pages
   for the first page after login setting on the options page. */
$start_pages = array(
    'mailbox' => 22,
    'new' => 10,
    'options' => 4,
    'compose' => 3,
    'contacts' => 8,
    'profile' => 236,
    'folders' => 7,
    'about' => 2,
);

/* Sort types for the contacts page. Defines the available sort
   methods for the contacts display. */
$contact_sort_types = array(
    'EMAIL'  => 16,
    'FN'     => 149,
    'FAMILY' => 150,
    'GIVEN'  => 151,
    'NAME'   => 152,
);

/* Phone types for the contacts page. Defines the phone types available
   for a contact entry. */
$phone_types = array(
    1 => 'Work',
    2 => 'Home',
    3 => 'Cell',
    4 => 'Voice',
    5 => 'Fax',
    6 => 'Preferred'
);

/* Phone display types for translations. Maps the phone type to an
   interface translation index. */
$phone_dsp_types = array(
    'Work'  => 325,
    'Home'  => 326,
    'Cell'  => 327,
    'Voice' => 328,
    'Fax'   => 329,
    'Preferred' => 330,
);

/* Address types for the contacts page. Defines the address types
   a contact can have. */
$address_types = array(
    1 => 'Work',
    2 => 'Home',
    3 => 'Parcel',
    4 => 'Postal'
);

/* Address display types for string translations. Maps to the
   interface translation index. */
$address_dsp_types = array(
    'Work' => 325,
    'Home' => 326,
    'Parcel' => 331,
    'Postal' => 332,
);

/* Text output encoding options for the compose section of the
   options page. In order they are 8bit quoted-printable, and base64.
   The values map to the interface translation index for each options.
 */
$text_encodings = array(
    0 => 308,
    1 => 309,
    2 => 310,
);

/* Text output format options for the compose section of the
   options page. In order they are Fixed, Flowed, and Preformatted.
   The values map to the interface translation index for each options.
 */
$text_formats = array(
    0 => 305,
    1 => 306,
    2 => 307,
);

/* SMTP auth mechs available. These are the authentication options available
   for sending mail with authenticated SMTP */
$smtp_auth_mechs = array(
    'none',
    'plain',
    'login',
    'cram-md5',
    'external',
);

/* SMTP auth mechs for translations. Maps the above list
   to the correct interface translation index for the compose
   section of the options page. */
$smtp_dsp_mechs = array(
    'none' => 242,
    'plain' => 311,
    'login' => 312,
    'cram-md5' => 313,
    'external' => 314,
); 

/* Output filter tags. The final HTML output of a request contains special
   tags that are used to filter out mark-up depending on the display mode.
   This defines the tag setup that corresponds to normal display mode. If the
   false and true values where reversed it would be "simple" display mode */
$hm_tags = array(
    'complex' => false,
    'simple' => true,
);

/* Previous and next options. On the message view page there is a "previous or
   next plus action dialog. This list defines the message actions available on
   the dropdown and maps them to their interface strings. */
$prev_next_actions = array(
    ' ' => 428,
    'move' => 66,
    'copy' => 67,
    'unread' => 34,
    'flag' => 35,
    'unflag' => 65, 
    'delete' => 59,
    'expunge' => 68,
);

/* Message list field order. This defines the order of fields on the mailbox
   page, search results, unread mail page, and thread view. Omitting an entry
   will remove it from the display. This can be overriden by a theme, and then
   again by a user's settings */
$msg_list_flds = array(
    'checkbox_cell',
    'image_cell',
    'from_cell',
    'indicators_cell',
    'subject_cell',
    'plugin_cell',
    'date_cell',
    'size_cell',
);

/* display message list headings or not. Can be overriden by a theme,
 * and then again by a user's settings */
$default_list_heading = true;

/* add onclick events to message list rows that open the message. Can be
 * overridden by a theme and again by a user's settings */
$default_onclick = false;

/* Contact list per page count on the compose page */
$contacts_per_page = 20;

/* Maximum messages per page in message lists */
$max_msg_per_page = 200;

/* Maximum read length for message parts (0 is unlimited) in characters.
   This only applies to text or html parts being viewed. If set too
   high a big enough text part can overload the browser. */
$max_read_length = 350000;

/* Maximum header length on the message view page. If a header value exceeds this
   length a link will be available to display the entire value. This setting is
   in characters */
$max_header_length = 300;

/* Development option to force plugins to completely reload each page load.
   Under normal circumstances plugin hooks are registered when a user logs in. */
$force_plugin_reloading = false;

/* Maximum amount of time to skip new and mailbox page updates when the content
   has not changed (in seconds). This keeps the Date field updated when it is showing
   the age of the message. */
$force_page_update = 300;

/* If an in-process message is exited without sending uploaded attachments could be
   left on the server. This sets the number of seconds to wait before purging these
   attachments from the server.*/
$attachment_lifetime = 7200;

/* Set this to true to use the uncompressed versions of the javascript include files.
   Useful for javascript development. */
$javascript_dev = false;

/* event handlers added by plugins are by default wrapped into try blocks so one problematic
   handler does not impact any others. This can make debugging difficult so the exceptions
   can be disabled here
 */
$allow_js_exception = false;

/* If a message does not contain a mime-version header IMAP servers are allowed to
   ignore the MIME structure and parse the message as plain text. Setting the following
   to true will enable a work around in hastymail that will correct this problem, but
   on for very simple single part messages. */
$override_missing_mime_header = false;

/* Css files are dynamically streamed to the browser using PHP. This sets the max age
   cache control HTTP header value for css content (in seconds), and is used to determine
   the date value for the expires HTTP header. */
$css_max_age = 21600;

/* Enable support for the hastymail_utils PHP5 module */
$hm_utils_mod = false;

/* Maximum number of recipients for a single outgoing message. Leave at 0 for unlimited */
$max_outbound_recipients = 0;

/* Disable atlernate profile support and "lock down" the email address */
$no_profiles = false;

/* Add CSS font-size to the message view area when in simple mode. This value is set
 * in pts and is used to help correct odd browser font sizing when reading messages
 * in simplemode */
$simple_msg_font_size = 11;

/* Allow for a comma separated list of SMTP servers defined in the hastymai2.conf file.
 * When a send attempt is made the servers will be selected randomly from the list
 * until one is successfully connected to. */
$smtp_server_pool = false;

/* This sets the maximum amount of messages we will show when a user uses the
 * "show all" link in the mailbox view. */
$show_all_max = 1000;

/* There are two types of E-mail validation. Full validation based on RFC 3696, and
 * a simpler regex version. The full validation is 5 times slower than the default
 * regex but both are sub millisecond. The regex pattern can be altered with the
 * $valid_email_regex value below. Valid values here are "full" and "regex". */
$email_validation_type = 'regex';

/* Default regular expression used to determine E-mail validity */
$valid_email_regex = "/^([a-zA-Z0-9\.\-\=\+\'\`])+@(localhost|(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)$/";

/* White list of valid built in ajax callback functions we allow to be sent from a Hastymail page's
 * javascript. Plugins can add to these but those are valided dynamically and don't need to be listed
 * here. Don't remove or add things to this unless you know what you are doing or it will break some
 * ajax functionality */
$valid_ajax_callbacks = array('ajax_update_page', 'ajax_save_outgoing_message', 'ajax_prev_contacts',
    'ajax_next_contacts', 'ajax_save_folder_state', 'ajax_save_folder_vis_state');


/* Start required objects and prep global space for possible use
----------------------------------------------------------------*/

/* Holds the database connection object if needed by the request */
$dbase = false;

/* Holds the plugin tools object set if needed by the request. This
   provides an API to plugins to function within Hastymail. */
$tools = false;

/* Holds the mime message object if needed by the request. This
   class handles formatting outgoing messages. */
$message = false;

/* Holds the SMTP object if needed by the request. This class
   handles sending outgoing messages. */
$smtp = false;

/* Instantiate the imap object. This is used for all IMAP communications */
$imap = hm_new('imap');

/* Instantiate the user object. This handles the core logic of the program */
$user = hm_new('fw_user');

/* Apply the site configuration. */
get_site_config();

/* CSS streamer that dynamically outputs a theme's css files combined into one
   compressed file. If css_streamer() gets called page execution ends without
   returning here. */
if (isset($_GET['css']) && isset($_GET['page']) && isset($_GET['theme'])) {
    css_streamer($_GET['page'], $_GET['theme']);
}


/* Handle the request and perform any resulting actions needed
----------------------------------------------------------------*/

/* Start the user object checks. This handles all input frome the user and
   calls the appropriate code to perform required actions.*/
$user->init();

/* Start Sajax based ajax system if we need it. If we are handling an ajax
   request we do not return from the handle_client_request call, it outputs
   the ajax response. */
if ($user->ajax_enabled && isset($_POST['rs'])) {
    require_once($include_path.'lib'.$fd.'ajax_functions.php');
    handle_client_request();
}

/* Counter for the new page, only reset on non-ajax requests */
$_SESSION['new_page_refresh_count'] = 0;

/* Clean up IMAP communication. At this point all the work that needs to be done
   for this request is complete. */
if ($imap->connected) {
    $imap->disconnect();
}

/* Do a handy work hook. This is a good way for plugins to get access to the completed
   data for a page request before the XHTML is built. */
do_work_hook('page_end');


/* Build the XHTML and sent it to the browser 
----------------------------------------------------------------*/

/* Setup template data. The code is broken out into multiple includes to keep
   the application memory footprint smaller. */
if ($user->sub_class_names['url']) {
    $class_name = 'site_page_'.$user->sub_class_names['url'];
    $pd = hm_new($class_name);
}
else {
    $pd = hm_new('site_page');
}

/* Build the page XHTML. The resulting page is constructed but not sent to the browser yet */
build_page($pd);

/* Filter the output XHTML for the current display mode, and send it to the browser */
output_filtered_content($hm_tags);

/* IMAP debug. Outputs debug information below the page if the show_imap_debug setting
   is enabled in the hastymail2.conf file. */
if (isset($conf['show_imap_debug']) && $conf['show_imap_debug']) {
    if (isset($conf['show_full_debug']) && $conf['show_full_debug']) {
        $imap->puke(true);
    }
    else {
        $imap->puke();
    }
}

/* SMTP debug. Outputs debug information about any SMTP operations performed */
if (isset($conf['show_smtp_debug']) && $conf['show_smtp_debug']) {
    if (is_object($smtp)) {
        $smtp->puke();
    }
}

/* PHP session cache usage. Shows some memory use information if the show_cache_usage
   hastymail2.conf file setting is enabled. */
if (isset($conf['show_cache_usage']) && $conf['show_cache_usage']) {
    $imap->show_cache();
    if (function_exists('memory_get_peak_usage')) {
        echo '<br />Peak PHP memory usage : '.(sprintf("%0.2f", memory_get_peak_usage()/1024)).'KB';
    }
}

/* Clean up the user object and properly close any active sessions. */
$user->clean_up();

/* DB debug statements. Show the database connection debug out if the db_debug option is
   enabled in the hastymail2.conf file. */
if (is_object($dbase) && isset($conf['db_debug']) && $conf['db_debug']) {
    echo $dbase->puke(true);
}
?>
