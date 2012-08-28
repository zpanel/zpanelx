<?php

/*  config.php: Plugin file responsible for defining how the plugin interacts with Hastymail 
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

* $Id:$
*/

$js_help_hooks = array(
    'work_hooks'        => array('init'),
    'display_hooks'     => array('options_page_top'),
    'page_hook'         => false,
);

global $user;
$js_help_langs = array(
    'en_US' => array(
        196 => 'The %s option sets the colors, fonts, and icons to different arrangements. Themes can be made with nothing but CSS, and can be expanded using PHP templates. We could use more themes if anybody is interested :)',
        197 => 'The %s controls the final XHTML output. When set to "Simple" javascript and css are excluded and the page is very size is very small. The default is the full XHTML interface',
        198 => 'The %s setting adjusts the clock and displayed date fields to the selected zone. The default is the server timezone.',
        199 => 'The %s sets the format for the clock in the toolbar at the top of the page. You can set the format for the date and time individually',
        200 => 'The %s sets the format for date fields in messages. You can set the format for the date and time individually',
        201 => 'The %s setting determines what page is first displayed just after you login. The default is the mailbox view of the INBOX',
        202 => 'The %s controls the font size of text in the interface. 100%% is based on the default size set in the current theme. All of the themes currently set this to 10pt',
        203 => 'The %s setting determines the language used in the interface. When not using English if any text is displayed in English it is because it has not been translated yet. We could use translation help :)',
        204 => 'The %s option will prompt you with a javascript confirmation dialog whenever a delete action is performed',
        205 => 'The %s option displays a folder tree on the left side of the display.',
        499 => 'The %s option hides the folder icons displayed in the folder tree when the <b>Show folder list</b> option is enabled', 
        504 => 'The %s option disables the javascript that highlights selected messages after checking the checkbox in message lists (mailbox view, new mail view, and search results).',
        498 => 'The %s option hides the envelope icon images from all message lists',
        206 => 'The %s setting will automatically enable the "Simple" <b>Display Mode</b> when a handheld device like a PDA or phone is detected',
        427 => 'The %s setting causes all messages flagged for deletion to be expunged from the INBOX when logging out', 
        447 => 'The %s option will cause messages with the delete flag set to be excluded from all message listings',
        208 => 'The %s setting changes the way deleted messages are handled. If set to a folder the message is copied to that folder then permanently removed from it\'s current location. If set to "None" then deleted messages are flagged for deletion and only permanently removed when the folder is expunged',
        209 => 'The %s setting saves a copy of each outgoing message to the selected folder. If set to "None" no copy of the outgoing mail is saved',
        210 => 'The %s enables a "Save" button on the compose page. When clicked the in process message is saved to the Draft folder and can be resumed by viewing the message and selecting "Resume Draft"',
        211 => 'The %s option determines how folder lists will be displayed. The "Nested" displays subfolders with indentation to represent the folder hierarchy. The "Flat" option displays all folders without indetation and each has the full folder path displayed including parent folders',
        212 => 'The %s option sets the level of detail to display next to folders that are being tracked for new mail in the folder list and folder dropdown. The options are none, just the number of unread messages, or the number of unread messages and the total number of messages in the folder',
        213 => 'The %s setting sets up an AJAX update mechanism that updates the folder dropdown and list when folders that are being tracked for unread mail change.',
        434 => 'The %s option limits all folder lists to only folders that are subscribed using the IMAP subscribe mechanism. Currently Hastymail does not have a way to manage IMAP folder subscriptions',
        214 => 'The %s setting looks for URLs in text message parts and converts them to links',
        215 => 'The %s setting looks for email addresses in text messages and makes them links to the compose page with the address in the To: field',
        216 => 'The %s setting will alter the style of quoted text in a reply message.',
        217 => 'The %s option will cause HTML message parts to be preferred when a message has multiple alternative parts. The default behavior is to prefer text parts',
        455 => 'By default all message parts are displayed in the "Message Parts" section. The %s option hides alternative message parts and "container" parts.',
        429 => 'External images in HTML message parts are blocked for security reasons. The %s setting will enable loading these images by default. Use caution!',
        451 => 'The %s option will cause Hastymail to open a new browser window when reading a message',
        430 => 'The %s setting sets the default action in the previous/next + action form in the upper right of the page.',
        431 => 'The %s option sets the default font family for message text',
        219 => 'The %s setting will display image attachment thumbnails in the "Message Parts" section next to supported image types (gif, png, jpeg)',
        220 => 'The %s setting will display the full set of message headers instead of the small headers display',
        221 => 'The %s setting determines what headers will be displayed when viewing a message.',
        222 => 'The %s option sets the number of messages to show on one page. 200 is the highest amount allowed', 
        223 => 'The %s setting will show an additional set of message controls (read, unread, delete, etc) below the message list as well as above',
        224 => 'The %s option will enable the "Mailbox Freeze". This setting will freeze a mailboxe\'s state and ignore update information from the IMAP server. This can be useful for very large folders that change a lot. One can "freeze" the folder, quickly navigate messages, then "unfreeze" it when they want updates to resume.', 
        423 => 'Normally the "Expunge" button is only displayed if there is no "Trash" folder setup. The %s setting causes the button to always be present',
        424 => 'Be default expunge acts on all messages with a delete flag set. This %s setting the action\'s behavior to only expunge messages that have the delete flag set and are selected', 
        435 => 'The %s setting causes the page links to be displayed at the top and bottom of the page',
        //461 => 'The %s setting displays an option in the message controls to apply the selected action to all the messages in the folder instead of just the selected messages',
        497 => 'The %s option will cause the message list to update if the AJAX update system notices a change in the mailbox state',
        225 => 'The %s option sets how often to check for new messages. If AJAX updating is the default. If AJAX is disabled for the site the page uses a META tag to update',
        226 => 'Be default all folders being tracked for new messages will be displayed on the new mail page. The %s setting will hide a folder if it currently has no unread messages',
        227 => 'The %s option sets the way the outgoing text body will be formatted. The options are fixed, flowed, and pre-formatted',
        228 => 'The %s option sets the default encoding for outgoing text messages. The options are 8bit, quoted-printable, and base64',
        229 => 'The %s option will remove the user-agent header from outgoing mail.',
        448 => 'The %s option will cause any saved drafts of the in process message to be deleted when the message is sent',
        450 => 'The %s setting causes Hastymail to open a new browser window when composing a message',
        232 => 'The %s setting enables an AJAX auto-save feature that will periodically save a draft of the in process message',
        233 => 'The %s option sets the type of SMTP authentication to use. Options are none, plain, login, cram-md5, and external',
        234 => 'The %s option sets the username to use with SMTP authentication',
        235 => 'The %s option sets the password to use with SMTP authentication',
        218 => 'The %s option sets the default font-family to use when displaying text messages',
        461 => 'The %s setting enables a dropdown on the mailbox page that changes the message controls so that they effect the entire mailbox instead of just the selected messages.',
        518 => 'The %s option sets the maximum length in characters of the from field in message lists. A very long from address can have annoying effects on the layout',
        520 => 'The %s option only applies when the "compose messages in a new window" option is enabled. When selected the new compose window will automatically close when the outgoing message is successfully sent.',
        523 => 'The %s option set the maximum length in characters of the subject field in messge lists. This is to keep long subjects from wrapping',
        531 => 'The %s option enables a link in message lists that will open the messge in a new window. If the "Open messages in a new window" option is set this setting is ignored.',
        'message_digest_plugin' => array(
            1 => 'The %s option shows a list of all the digest message parts below the message headers. The subjects are links that navigate to that mesasge part',
        ),
        'move_sent_plugin' => array(
            'config_global_enable' => 'The %s option will display an option on the compose page to alter the sent folder used for the current message, and optionally move the replied-to message to the same folder',
        ),
        'auto_address_plugin' => array(
            1 => 'The %s setting enables auto-completing the To, Cc, and Bcc address fields from your contacts. Includes auto-completing from external addressbock sources',
            2 => 'The %s setting restricts the auto-complete function to only your personal addressbook',
            3 => 'The %s setting determines how much needs to be typed into an address field before the auto-complete function kicks in',
            4 => 'The %s setting setting defines the maximum number of matching entries to show when auto-completing an address',
            
        ),
        'html_mail_plugin' => array(
            1 => 'The %s option enables the tinyMCE WYSIWYG HTML editor on the compose page to author messages with',
            2 => 'The %s option sets the default font size of the tinyMCE WYSIWYG HTML editor',
            3 => 'The %s option sets the default font-family of the tinyMCE WYSIWYG HTML editor',
            4 => 'The %s option enables the ability to dynamically switch between HTML and Text when composing a message.',
        ),
        'compose_warning_plugin' => array(
            0 => 'The %s option enables a javascript confirmation whenever the Send button is clicked.',
            1 => 'The %s option enables a javascript confirmation when the Send button is clicked and the subject is blank.',
            2 => 'The %s option enables a javascript confirmation when the exiting the compose page with a message in progress.',
        ),
        'quota_plugin' => array(
            0 => 'The %s option displays an IMAP quota summary in the selected location.',
        ),
        'calendar_plugin' => array(
            0 => 'The %s option displays a summary of events scheduled today. The text is a link to the daily view of the calendar for today.',
        ),
        'notices_plugin' => array(
            0 => 'The %s option enables a javascript/flash based sound player (Sound Manager 2) that is tied to the AJAX update system. To try out a sound use the "test" link that will open the flash player in a pop up window.',
            1 => 'The %s option enables a browser popup window that appears when a new message arrives. The setting is tied to the AJAX update system.',
        ),
        'custom_reply_to_plugin' => array(
            1 => 'The %s option adds a reply-to field to the compose page that lets users define a special reply-to value for the current message. If left blank then the current profile reply-to value is used.',
        ),
        'custom_headers_plugin' => array(
            1 => 'The %s option adds a custom header field to the compose page that lets users define a special mail header name and value for the outgoing message',
        ),
        'message_tags_plugin' => array(
            4 => 'The %s option sets the display method for the message tags plugin. Tags can be hidden, listed above or below the folder tree, or they can replace the folder tree completely',
            8 => 'The %s option disables the message tag plugin completely'
        ),
        'spam_folder_plugin' => array(
            0 => 'The %s option configures a "Spam" folder. This folder can now be emptied (without moving copies to the Trash folder) with a single click using the "Empty Spam" button.',
            1 => 'The %s option sets up an automatic delete routine that will delete messages older than this many days when logging out'
        ),
    ),
    'pl_PL' => array(
        196 => 'Opcja %s zmienia kolory, czcionki, oraz ikony, trybu wyświetlania całości.',
        197 => 'Opcja %s kontroluje zestaw wynikowy języka XHTML. Jeżeli zostanie wybrana opcja "Prosty"  "javascript" i "css" zostaną wyłączone, a strona będzie wyświetlana w trybie tekstowym',
        198 => 'Opcja %s ustawia zegar zgodnie z wybraną strefą czasową. Domyślną wartością jest srefa czasowa serwera.',
        199 => 'Opcja %s zmienia format wyświetlania czasu w górnej części strony.',
        200 => 'Opcja %s ustawia format dla daty i czasu wykorzystywanych dla wiadomości.',
        201 => ' %s ustawienie określa która strona jest wyświetlana po zalogowaniu. Domyślnym jest widok Skrzynki odbiorczej',
        202 => 'Opcja %s kontroluje rozmiar tekstu. 100%% jest domyślnym ustawieniem w aktualnie wybranym profilu (skórce). Wszystkie profile wyświetlania mają obecnie ustawioną wartość na 10pt',
        203 => 'Ustawienie %s definiuje język wyświetlania.',
        204 => 'The %s option will prompt you with a javascript confirmation dialog whenever a delete action is performed',
        205 => 'Opcja %s wyświetla listę folderów po lewej stronie.',
        499 => 'Opcja %s ukrywa ikony folderów wyświetlane w menu. Dostępna jest przy włączonej opcji <b>Pokazuj listę folderów</b>.', 
        504 => 'Opcja %s option disables the javascript that highlights selected messages after checking the checkbox in message lists (mailbox view, new mail view, and search results).',
        498 => 'Opcja %s ukrywa ikonę koperty ze wszystkich list wiadomości.',
        206 => 'Opcja  %s automatycznie przełączy <b>Sposób wyświetlania</b> w tryb "Prosty" w momencie wykrycia urządzenia przenośniego t.j. palmtopy, telefony komórkowe, kieszonkowe PC',
        427 => 'Opcja %s po wylogowaniu, spowoduje wymazanie wszystkich wiadomości z folderu Skrzynka odbiorcza, które zostały zaznaczone do usunięcia.', 
        447 => 'Opcja %s spowoduje, że wiadomości zaznaczone do usunięcia będą wykluczone z listy wiadomości',
        208 => 'Ustawienie %s zmienia sposób obsługi usuwanych wiadomości. If set to a folder the message is copied to that folder then permanently removed from it\'s current location. If set to "None" then deleted messages are flagged for deletion and only permanently removed when the folder is expunged',
        209 => 'The %s setting saves a copy of each outgoing message to the selected folder. If set to "None" no copy of the outgoing mail is saved',
        210 => 'The %s enables a "Save" button on the compose page. When clicked the in process message is saved to the Draft folder and can be resumed by viewing the message and selecting "Resume Draft"',
        211 => 'The %s option determines how folder lists will be displayed. The "Nested" displays subfolders with indentation to represent the folder hierarchy. The "Flat" option displays all folders without indetation and each has the full folder path displayed including parent folders',
        212 => 'The %s option sets the level of detail to display next to folders that are being tracked for new mail in the folder list and folder dropdown. The options are none, just the number of unread messages, or the number of unread messages and the total number of messages in the folder',
        213 => 'The %s setting sets up an AJAX update mechanism that updates the folder dropdown and list when folders that are being tracked for unread mail change.',
        434 => 'The %s option limits all folder lists to only folders that are subscribed using the IMAP subscribe mechanism. Currently Hastymail does not have a way to manage IMAP folder subscriptions',
        214 => 'The %s setting looks for URLs in text message parts and converts them to links',
        215 => 'The %s setting looks for email addresses in text messages and makes them links to the compose page with the address in the To: field',
        216 => 'The %s setting will alter the style of quoted text in a reply message.',
        217 => 'The %s option will cause HTML message parts to be preferred when a message has multiple alternative parts. The default behavior is to prefer text parts',
        455 => 'By default all message parts are displayed in the "Message Parts" section. The %s option hides alternative message parts and "container" parts.',
        429 => 'External images in HTML message parts are blocked for security reasons. The %s setting will enable loading these images by default. Use caution!',
        451 => 'The %s option will cause Hastymail to open a new browser window when reading a message',
        430 => 'The %s setting sets the default action in the previous/next + action form in the upper right of the page.',
        431 => 'The %s option sets the default font family for message text',
        219 => 'The %s setting will display image attachment thumbnails in the "Message Parts" section next to supported image types (gif, png, jpeg)',
        220 => 'The %s setting will display the full set of message headers instead of the small headers display',
        221 => 'The %s setting determines what headers will be displayed when viewing a message.',
        222 => 'The %s option sets the number of messages to show on one page. 200 is the highest amount allowed', 
        223 => 'The %s setting will show an additional set of message controls (read, unread, delete, etc) below the message list as well as above',
        224 => 'The %s option will enable the "Mailbox Freeze". This setting will freeze a mailboxe\'s state and ignore update information from the IMAP server. This can be useful for very large folders that change a lot. One can "freeze" the folder, quickly navigate messages, then "unfreeze" it when they want updates to resume.', 
        423 => 'Normally the "Expunge" button is only displayed if there is no "Trash" folder setup. The %s setting causes the button to always be present',
        424 => 'Be default expunge acts on all messages with a delete flag set. This %s setting the action\'s behavior to only expunge messages that have the delete flag set and are selected', 
        435 => 'The %s setting causes the page links to be displayed at the top and bottom of the page',
        //461 => 'The %s setting displays an option in the message controls to apply the selected action to all the messages in the folder instead of just the selected messages',
        497 => 'The %s option will cause the message list to update if the AJAX update system notices a change in the mailbox state',
        225 => 'The %s option sets how often to check for new messages. If AJAX updating is the default. If AJAX is disabled for the site the page uses a META tag to update',
        226 => 'Be default all folders being tracked for new messages will be displayed on the new mail page. The %s setting will hide a folder if it currently has no unread messages',
        227 => 'The %s option sets the way the outgoing text body will be formatted. The options are fixed, flowed, and pre-formatted',
        228 => 'The %s option sets the default encoding for outgoing text messages. The options are 8bit, quoted-printable, and base64',
        229 => 'The %s option will remove the user-agent header from outgoing mail.',
        448 => 'The %s option will cause any saved drafts of the in process message to be deleted when the message is sent',
        450 => 'The %s setting causes Hastymail to open a new browser window when composing a message',
        232 => 'The %s setting enables an AJAX auto-save feature that will periodically save a draft of the in process message',
        233 => 'The %s option sets the type of SMTP authentication to use. Options are none, plain, login, cram-md5, and external',
        234 => 'The %s option sets the username to use with SMTP authentication',
        235 => 'The %s option sets the password to use with SMTP authentication',
        218 => 'The %s option sets the default font-family to use when displaying text messages',
        461 => 'The %s setting enables a dropdown on the mailbox page that changes the message controls so that they effect the entire mailbox instead of just the selected messages.',
        518 => 'The %s option sets the maximum length in characters of the from field in message lists. A very long from address can have annoying effects on the layout',
        520 => 'The %s option only applies when the "compose messages in a new window" option is enabled. When selected the new compose window will automatically close when the outgoing message is successfully sent.',
        523 => 'The %s option set the maximum length in characters of the subject field in messge lists. This is to keep long subjects from wrapping',
        531 => 'The %s option enables a link in message lists that will open the messge in a new window. If the "Open messages in a new window" option is set this setting is ignored.',
        'message_digest_plugin' => array(
            1 => 'The %s option shows a list of all the digest message parts below the message headers. The subjects are links that navigate to that mesasge part',
        ),
        'move_sent_plugin' => array(
            'config_global_enable' => 'The %s option will display an option on the compose page to alter the sent folder used for the current message, and optionally move the replied-to message to the same folder',
        ),
        'auto_address_plugin' => array(
            1 => 'The %s setting enables auto-completing the To, Cc, and Bcc address fields from your contacts. Includes auto-completing from external addressbock sources',
            2 => 'The %s setting restricts the auto-complete function to only your personal addressbook',
            3 => 'The %s setting determines how much needs to be typed into an address field before the auto-complete function kicks in',
            4 => 'The %s setting setting defines the maximum number of matching entries to show when auto-completing an address',
            
        ),
        'html_mail_plugin' => array(
            1 => 'The %s option enables the tinyMCE WYSIWYG HTML editor on the compose page to author messages with',
            2 => 'The %s option sets the default font size of the tinMCE WYSIWYG HTML editor',
            3 => 'The %s option sets the default font-family of the tinyMCE WYSIWYG HTML editor',
            4 => 'The %s option enables the ability to dynamically switch between HTML and Text when composing a message.',
        ),
        'compose_warning_plugin' => array(
            0 => 'The %s option enables a javascript confirmation whenever the Send button is clicked.',
            1 => 'The %s option enables a javascript confirmation when the Send button is clicked and the subject is blank.',
            2 => 'The %s option enables a javascript confirmation when the exiting the compose page with a message in progress.',
        ),
        'quota_plugin' => array(
            0 => 'The %s option displays an IMAP quota summary in the selected location.',
        ),
        'calendar_plugin' => array(
            0 => 'The %s option displays a summary of events scheduled today. The text is a link to the daily view of the calendar for today.',
        ),
        'notices_plugin' => array(
            0 => 'The %s option enables a javascript/flash based sound player (Sound Manager 2) that is tied to the AJAX update system. To try out a sound use the "test" link that will open the flash player in a pop up window.',
            1 => 'The %s option enables a browser popup window that appears when a new message arrives. The setting is tied to the AJAX update system.',
        ),
        'custom_reply_to_plugin' => array(
            1 => 'The %s option adds a reply-to field to the compose page that lets users define a special reply-to value for the current message. If left blank then the current profile reply-to value is used.',
        ),
        'custom_headers_plugin' => array(
            1 => 'The %s option adds a custom header field to the compose page that lets users define a special mail header name and value for the outgoing message',
        ),
        'message_tags_plugin' => array(
            0 => 'The %s options sets the display method for the message tags plugin. Tags can be hidden, listed above or below the folder tree, or they can replace the folder tree completely',
            1 => 'The %s option disables the message tag plugin completely'
        ),
        'spam_folder_plugin' => array(
            0 => 'The %s option configures a "Spam" folder. This folder can now be emptied (without moving copies to the Trash folder) with a single click using the "Empty Spam" button.',
            1 => 'The %s option sets up an automatic delete routine that will delete messages older than this many days when logging out'
        ),
    ),
);
?>
