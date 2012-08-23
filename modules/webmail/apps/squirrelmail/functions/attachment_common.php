<?php

/**
 * attachment_common.php
 *
 * This file provides the handling of often-used attachment types.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: attachment_common.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * FIXME Needs phpDocumentator style documentation
 */
require_once(SM_PATH . 'functions/global.php');

global $attachment_common_show_images_list;
$attachment_common_show_images_list = array();

global $FileExtensionToMimeType, $attachment_common_types;
$FileExtensionToMimeType = array('bmp'  => 'image/x-bitmap',
                                 'gif'  => 'image/gif',
                                 'htm'  => 'text/html',
                                 'html' => 'text/html',
                                 'jpg'  => 'image/jpeg',
                                 'jpeg' => 'image/jpeg',
                                 'php'  => 'text/plain',
                                 'png'  => 'image/png',
                                 'rtf'  => 'text/richtext',
                                 'txt'  => 'text/plain',
                                 'patch'=> 'text/plain',
                                 'vcf'  => 'text/x-vcard');

/* Register browser-supported image types */
sqgetGlobalVar('attachment_common_types', $attachment_common_types);
if (isset($attachment_common_types)) {
    // var is used to detect activation of jpeg image types
    unset($jpeg_done);
    /* Don't run this before being logged in. That may happen
       when plugins include mime.php */
    foreach ($attachment_common_types as $val => $v) {
        if ($val == 'image/gif')
            register_attachment_common('image/gif',       'link_image');
        elseif (($val == 'image/jpeg' || $val == 'image/pjpeg') and
                (!isset($jpeg_done))) {
            $jpeg_done = 1;
            register_attachment_common('image/jpeg',      'link_image');
            register_attachment_common('image/pjpeg',     'link_image');
        }
        elseif ($val == 'image/png')
            register_attachment_common('image/png',       'link_image');
        elseif ($val == 'image/x-xbitmap')
            register_attachment_common('image/x-xbitmap', 'link_image');
        elseif ($val == '*/*' || $val == 'image/*') {
            /**
             * browser (Firefox) declared that anything is acceptable. 
             * Lets register some common image types.
             */
            if (! isset($jpeg_done)) {
                $jpeg_done = 1;
                register_attachment_common('image/jpeg',  'link_image');
                register_attachment_common('image/pjpeg', 'link_image');
            }
            register_attachment_common('image/gif',       'link_image');
            register_attachment_common('image/png',       'link_image');
            register_attachment_common('image/x-xbitmap', 'link_image');
        }
    }
    unset($jpeg_done);
}

/* Register text-type attachments */
register_attachment_common('message/rfc822', 'link_message');
register_attachment_common('text/plain',     'link_text');
register_attachment_common('text/richtext',  'link_text');

/* Register HTML */
register_attachment_common('text/html',      'link_html');


/* Register vcards */
register_attachment_common('text/x-vcard',   'link_vcard');
register_attachment_common('text/directory', 'link_vcard');

/* Register rules for general types.
 * These will be used if there isn't a more specific rule available. */
register_attachment_common('text/*',  'link_text');
register_attachment_common('message/*',  'link_text');

/* Register "unknown" attachments */
register_attachment_common('application/octet-stream', 'octet_stream');


/* Function which optimizes readability of the above code */

function register_attachment_common($type, $func) {
    global $squirrelmail_plugin_hooks;
    $squirrelmail_plugin_hooks['attachment ' . $type]['attachment_common'] =
                      'attachment_common_' . $func;
}


function attachment_common_link_text(&$Args) {

    global $squirrelmail_attachments_finished_handling;
    if (!empty($squirrelmail_attachments_finished_handling[$Args[7]])) return;
    $squirrelmail_attachments_finished_handling[$Args[7]] = TRUE;

    /* If there is a text attachment, we would like to create a "View" button
       that links to the text attachment viewer.

       $Args[1] = the array of actions

       Use the name of this file for adding an action
       $Args[1]['attachment_common'] = Array for href and text

       $Args[1]['attachment_common']['text'] = What is displayed
       $Args[1]['attachment_common']['href'] = Where it links to */
    sqgetGlobalVar('QUERY_STRING', $QUERY_STRING, SQ_SERVER);

    $Args[1]['attachment_common']['href'] = SM_PATH . 'src/view_text.php?'. $QUERY_STRING;
    $Args[1]['attachment_common']['href'] =
          set_url_var($Args[1]['attachment_common']['href'],
          'ent_id',$Args[5]);

    /* The link that we created needs a name. */
    $Args[1]['attachment_common']['text'] = _("View");

    /* Each attachment has a filename on the left, which is a link.
       Where that link points to can be changed.  Just in case the link above
       for viewing text attachments is not the same as the default link for
       this file, we'll change it.

       This is a lot better in the image links, since the defaultLink will just
       download the image, but the one that we set it to will format the page
       to have an image tag in the center (looking a lot like this text viewer) */
    $Args[6] = $Args[1]['attachment_common']['href'];
}

function attachment_common_link_message(&$Args) {

    global $squirrelmail_attachments_finished_handling;
    if (!empty($squirrelmail_attachments_finished_handling[$Args[7]])) return;
    $squirrelmail_attachments_finished_handling[$Args[7]] = TRUE;

    $Args[1]['attachment_common']['href'] = SM_PATH . 'src/read_body.php?startMessage=' .
        $Args[2] . '&amp;passed_id=' . $Args[3] . '&amp;mailbox=' . $Args[4] .
        '&amp;passed_ent_id=' . $Args[5] . '&amp;override_type0=message&amp;override_type1=rfc822';

    $Args[1]['attachment_common']['text'] = _("View");

    $Args[6] = $Args[1]['attachment_common']['href'];
}


function attachment_common_link_html(&$Args) {

    global $squirrelmail_attachments_finished_handling;
    if (!empty($squirrelmail_attachments_finished_handling[$Args[7]])) return;
    $squirrelmail_attachments_finished_handling[$Args[7]] = TRUE;

    sqgetGlobalVar('QUERY_STRING', $QUERY_STRING, SQ_SERVER);

    $Args[1]['attachment_common']['href'] = SM_PATH . 'src/view_text.php?'. $QUERY_STRING.
       /* why use the overridetype? can this be removed */
       '&amp;override_type0=text&amp;override_type1=html';
    $Args[1]['attachment_common']['href'] =
          set_url_var($Args[1]['attachment_common']['href'],
          'ent_id',$Args[5]);

    $Args[1]['attachment_common']['text'] = _("View");

    $Args[6] = $Args[1]['attachment_common']['href'];
}

function attachment_common_link_image(&$Args) {

    global $squirrelmail_attachments_finished_handling;
    if (!empty($squirrelmail_attachments_finished_handling[$Args[7]])) return;
    $squirrelmail_attachments_finished_handling[$Args[7]] = TRUE;

    global $attachment_common_show_images, $attachment_common_show_images_list;

    sqgetGlobalVar('QUERY_STRING', $QUERY_STRING, SQ_SERVER);

    $info['passed_id'] = $Args[3];
    $info['mailbox'] = $Args[4];
    $info['ent_id'] = $Args[5];

    $attachment_common_show_images_list[] = $info;

    $Args[1]['attachment_common']['href'] = SM_PATH . 'src/image.php?'. $QUERY_STRING;
    $Args[1]['attachment_common']['href'] =
          set_url_var($Args[1]['attachment_common']['href'],
          'ent_id',$Args[5]);

    $Args[1]['attachment_common']['text'] = _("View");

    $Args[6] = $Args[1]['attachment_common']['href'];
}


function attachment_common_link_vcard(&$Args) {

    global $squirrelmail_attachments_finished_handling;
    if (!empty($squirrelmail_attachments_finished_handling[$Args[7]])) return;
    $squirrelmail_attachments_finished_handling[$Args[7]] = TRUE;

    sqgetGlobalVar('QUERY_STRING', $QUERY_STRING, SQ_SERVER);

    $Args[1]['attachment_common']['href'] = SM_PATH . 'src/vcard.php?'. $QUERY_STRING;
    $Args[1]['attachment_common']['href'] =
          set_url_var($Args[1]['attachment_common']['href'],
          'ent_id',$Args[5]);

    $Args[1]['attachment_common']['text'] = _("View Business Card");

    $Args[6] = $Args[1]['attachment_common']['href'];
}


function attachment_common_octet_stream(&$Args) {
    global $FileExtensionToMimeType;

    do_hook('attachment_common-load_mime_types');

    preg_match('/\.([^.]+)$/', $Args[7], $Regs);

    $Ext = '';
    if (is_array($Regs) && isset($Regs[1])) {
        $Ext = $Regs[1];
        $Ext = strtolower($Regs[1]);
    }

    if ($Ext == '' || ! isset($FileExtensionToMimeType[$Ext]))
        return;

    $Ret = do_hook('attachment ' . $FileExtensionToMimeType[$Ext],
        $Args[1], $Args[2], $Args[3], $Args[4], $Args[5], $Args[6],
        $Args[7], $Args[8], $Args[9]);

    foreach ($Ret as $a => $b) {
        $Args[$a] = $b;
    }
}

