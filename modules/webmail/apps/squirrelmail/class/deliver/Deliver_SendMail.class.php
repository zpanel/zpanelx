<?php

/**
 * Deliver_SendMail.class.php
 *
 * Delivery backend for the Deliver class.
 *
 * @author Marc Groot Koerkamp
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: Deliver_SendMail.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */


/** This of course depends upon Deliver */
require_once(SM_PATH . 'class/deliver/Deliver.class.php');

/**
 * Delivers messages using the sendmail binary
 * @package squirrelmail
 */
class Deliver_SendMail extends Deliver {
    /**
     * Extra sendmail arguments
     *
     * Parameter can be set in class constructor function.
     *
     * WARNING: Introduction of this parameter broke backwards compatibility 
     * with workarounds specific to qmail-inject.
     *
     * If parameter needs some security modifications, it should be set to 
     * private in PHP 5+ in order to prevent uncontrolled access.
     * @var string
     * @since 1.5.1 and 1.4.8
     */
    var $sendmail_args = '-i -t';

    /**
     * Stores used sendmail command
     * Private variable that is used to inform about used sendmail command.
     * @var string
     * @since 1.5.1 and 1.4.8
     */
    var $sendmail_command = '';

    /**
     * Constructor function
     * @param array configuration options. array key = option name, 
     * array value = option value.
     * @return void
     * @since 1.5.1 and 1.4.8
     */
    function Deliver_SendMail($params=array()) {
        if (!empty($params) && is_array($params)) {
            // set extra sendmail arguments
            if (isset($params['sendmail_args'])) {
                $this->sendmail_args = $params['sendmail_args'];
            }
        }
    }

   /**
    * function preWriteToStream
    *
    * Sendmail needs LF's as line endings instead of CRLF.
    * This function translates the line endings to LF and should be called
    * before each line is written to the stream.
    *
    * @param string $s Line to process
    * @return void
    * @access private
    */
    function preWriteToStream(&$s) {
       if ($s) {
           $s = str_replace("\r\n", "\n", $s);
       }
    }

   /**
    * function initStream
    *
    * Initialise the sendmail connection.
    *
    * @param Message $message Message object containing the from address
    * @param string $sendmail_path Location of sendmail binary
    * @return void
    * @access public
    */
    function initStream($message, $sendmail_path) {
        $rfc822_header = $message->rfc822_header;
        $from = $rfc822_header->from[0];
        $envelopefrom = trim($from->mailbox.'@'.$from->host);
        $envelopefrom = str_replace(array("\0","\n"),array('',''),$envelopefrom);
        // save executed command for future reference
        $this->sendmail_command = "$sendmail_path $this->sendmail_args -f$envelopefrom";
        // open process handle for writing
        $stream = popen(escapeshellcmd($this->sendmail_command), "w");
        return $stream;
    }

   /**
    * function finalizeStream
    *
    * Close the stream.
    *
    * @param resource $stream
    * @return boolean
    * @access public
    */
    function finalizeStream($stream) {
        $ret = true;
        $status = pclose($stream);
        // check pclose() status.
        if ($status!=0) {
            $ret = false;
            $this->dlv_msg=_("Email delivery error");
            $this->dlv_ret_nr=$status;
            // we can get better error messsage only if we switch to php 4.3+ and proc_open().
            $this->dlv_server_msg=sprintf(_("Can't execute command '%s'."),$this->sendmail_command);
        }
        return $ret;
    }

   /**
    * function getBcc
    *
    * In case of sendmail, the rfc822header must contain the bcc header.
    *
    * @return boolean true if rfc822header should include the bcc header.
    * @access private
    */
    function getBcc() {
        return true;
    }

   /**
    * function clean_crlf
    *
    * Cleans each line to only end in a LF
    * Returns the length of the line including a CR,
    * so that length is correct when the message is saved to imap
    * Implemented to fix sendmail->postfix rejection of messages with
    * attachments because of stray LF's
    *
    * @param string $s string to strip of CR's
    * @return integer length of string including a CR for each LF
    * @access private
    */
    function clean_crlf(&$s) {
        $s = str_replace("\r\n", "\n", $s);
        $s = str_replace("\r", "\n", $s);
        $s2 = str_replace("\n", "\r\n", $s);
        return strlen($s2);
    }


}
