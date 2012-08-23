<?php

/**
 * Deliver_IMAP.class.php
 *
 * Delivery backend for the Deliver class.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: Deliver_IMAP.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This of course depends upon Deliver.. */

require_once(SM_PATH . 'class/deliver/Deliver.class.php');

/**
 * This class is incomplete and entirely undocumented.
 * @package squirrelmail
 */
class Deliver_IMAP extends Deliver {

    function getBcc() {
       return true;
    }

    /**
     * function send_mail - send the message parts to the IMAP stream
     *
     * Overridden from parent class so that we can insert some 
     * IMAP APPEND commands before and after the message is 
     * sent on the IMAP stream.
     *
     * @param Message  $message      Message object to send
     * @param string   $header       Headers ready to send
     * @param string   $boundary     Message parts boundary
     * @param resource $stream       Handle to the SMTP stream
     *                               (when FALSE, nothing will be
     *                               written to the stream; this can
     *                               be used to determine the actual
     *                               number of bytes that will be
     *                               written to the stream)
     * @param int     &$raw_length   The number of bytes written (or that
     *                               would have been written) to the 
     *                               output stream - NOTE that this is
     *                               passed by reference
     * @param string   $folder       The IMAP folder to which the 
     *                               message is being sent
     *
     * @return void
     *
     */
    function send_mail($message, $header, $boundary, $stream=false, 
                       &$raw_length, $folder) {

        // write the body without providing a stream so we
        // can calculate the final length - after this call,
        // $final_length will be our correct final length value
        //
        $final_length = $raw_length;
        $this->writeBody($message, 0, $final_length, $boundary);


        // now if we have a real live stream, send the message
        //
        if ($stream) {
            sqimap_append ($stream, $folder, $final_length);

            $this->preWriteToStream($header);
            $this->writeToStream($stream, $header);
            $this->writeBody($message, $stream, $raw_length, $boundary);

            sqimap_append_done ($stream, $folder);
        }

    }


    /* to do: finishing the imap-class so the initStream function can call the
       imap-class */
}

