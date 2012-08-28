<?php

/**
 * Rfc822Header.class.php
 *
 * This file contains functions needed to handle headers in mime messages.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: Rfc822Header.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.2
 */

/**
 * MIME header class
 * input: header_string or array
 * You must call parseHeader() function after creating object in order to fill object's
 * parameters.
 * @todo FIXME: there is no constructor function and class should ignore all input args.
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.0
 */
class Rfc822Header {
    /**
     * Date header
     * @var mixed
     */
    var $date = -1;
    /**
     * Original date header as fallback for unparsable dates
     * @var mixed
     */
    var $date_unparsed = '';
    /**
     * Subject header
     * @var string
     */
    var $subject = '';
    /**
     * From header
     * @var array
     */
    var $from = array();
    /**
     * @var mixed
     */
    var $sender = '';
    /**
     * Reply-To header
     * @var array
     */
    var $reply_to = array();
    /**
     * Mail-Followup-To header
     * @var array
     */
    var $mail_followup_to = array();
    /**
     * To header
     * @var array
     */
    var $to = array();
    /**
     * Cc header
     * @var array
     */
    var $cc = array();
    /**
     * Bcc header
     * @var array
     */
    var $bcc = array();
    /**
     * In-reply-to header
     * @var string
     */
    var $in_reply_to = '';
    /**
     * Message-ID header
     * @var string
     */
    var $message_id = '';
    /**
     * References header
     * @var string
     */
    var $references = '';
    /**
     * @var mixed
     */
    var $mime = false;
    /**
     * @var mixed
     */
    var $content_type = '';
    /**
     * @var mixed
     */
    var $disposition = '';
    /**
     * X-Mailer header
     * @var string
     */
    var $xmailer = '';
    /**
     * Priority header
     * @var integer
     */
    var $priority = 3;
    /**
     * @var mixed
     */
    var $dnt = '';
    /**
     * @var mixed
     */
    var $encoding = '';
    /**
     * @var mixed
     */
    var $mlist = array();
    /**
     * SpamAssassin 'x-spam-status' header
     * @var mixed
     */
    var $x_spam_status = array();
    /**
     * Extra header
     * only needed for constructing headers in delivery class
     * @var array
     */
    var $more_headers = array();

    /**
     * @param mixed $hdr string or array with message headers
     */
    function parseHeader($hdr) {
        if (is_array($hdr)) {
            $hdr = implode('', $hdr);
        }
        /* First we replace \r\n by \n and unfold the header */
        /* FIXME: unfolding header with multiple spaces "\n( +)" */
        $hdr = trim(str_replace(array("\r\n", "\n\t", "\n "),array("\n", ' ', ' '), $hdr));

        /* Now we can make a new header array with */
        /* each element representing a headerline  */
        $hdr = explode("\n" , $hdr);
        foreach ($hdr as $line) {
            $pos = strpos($line, ':');
            if ($pos > 0) {
                $field = substr($line, 0, $pos);
                if (!strstr($field,' ')) { /* valid field */
                        $value = trim(substr($line, $pos+1));
                        $this->parseField($field, $value);
                }
            }
        }
        if (!is_object($this->content_type)) {
            $this->parseContentType('text/plain; charset=us-ascii');
        }
    }

    /**
     * @param string $value
     * @return string
     */
    function stripComments($value) {
        $result = '';
        $cnt = strlen($value);
        for ($i = 0; $i < $cnt; ++$i) {
            switch ($value{$i}) {
                case '"':
                    $result .= '"';
                    while ((++$i < $cnt) && ($value{$i} != '"')) {
                        if ($value{$i} == '\\') {
                            $result .= '\\';
                            ++$i;
                        }
                        $result .= $value{$i};
                    }
                    $result .= $value{$i};
                    break;
                case '(':
                    $depth = 1;
                    while (($depth > 0) && (++$i < $cnt)) {
                        switch($value{$i}) {
                            case '\\':
                                ++$i;
                                break;
                            case '(':
                                ++$depth;
                                break;
                            case ')':
                                --$depth;
                                break;
                            default:
                                break;
                        }
                    }
                    break;
                default:
                    $result .= $value{$i};
                    break;
            }
        }
        return $result;
    }

    /**
     * Parse header field according to field type
     * @param string $field field name
     * @param string $value field value
     */
    function parseField($field, $value) {
        $field = strtolower($field);
        switch($field) {
            case 'date':
                $value = $this->stripComments($value);
                $d = strtr($value, array('  ' => ' '));
                $d = explode(' ', $d);
                $this->date = getTimeStamp($d);
                $this->date_unparsed = strtr($value,'<>','  ');
                break;
            case 'subject':
                $this->subject = $value;
                break;
            case 'from':
                $this->from = $this->parseAddress($value,true);
                break;
            case 'sender':
                $this->sender = $this->parseAddress($value);
                break;
            case 'reply-to':
                $this->reply_to = $this->parseAddress($value, true);
                break;
            case 'mail-followup-to':
                $this->mail_followup_to = $this->parseAddress($value, true);
                break;
            case 'to':
                $this->to = $this->parseAddress($value, true);
                break;
            case 'cc':
                $this->cc = $this->parseAddress($value, true);
                break;
            case 'bcc':
                $this->bcc = $this->parseAddress($value, true);
                break;
            case 'in-reply-to':
                $this->in_reply_to = $value;
                break;
            case 'message-id':
                $value = $this->stripComments($value);
                $this->message_id = $value;
                break;
            case 'references':
                $value = $this->stripComments($value);
                $this->references = $value;
                break;
            case 'x-confirm-reading-to':
            case 'return-receipt-to':
            case 'disposition-notification-to':
                $value = $this->stripComments($value);
                $this->dnt = $this->parseAddress($value);
                break;
            case 'mime-version':
                $value = $this->stripComments($value);
                $value = str_replace(' ', '', $value);
                $this->mime = ($value == '1.0' ? true : $this->mime);
                break;
            case 'content-type':
                $value = $this->stripComments($value);
                $this->parseContentType($value);
                break;
            case 'content-disposition':
                $value = $this->stripComments($value);
                $this->parseDisposition($value);
                break;
            case 'user-agent':
            case 'x-mailer':
                $this->xmailer = $value;
                break;
            case 'x-priority':
            case 'importance':
            case 'priority':
                $this->priority = $this->parsePriority($value);
                break;
            case 'list-post':
                $value = $this->stripComments($value);
                $this->mlist('post', $value);
                break;
            case 'list-reply':
                $value = $this->stripComments($value);
                $this->mlist('reply', $value);
                break;
            case 'list-subscribe':
                $value = $this->stripComments($value);
                $this->mlist('subscribe', $value);
                break;
            case 'list-unsubscribe':
                $value = $this->stripComments($value);
                $this->mlist('unsubscribe', $value);
                break;
            case 'list-archive':
                $value = $this->stripComments($value);
                $this->mlist('archive', $value);
                break;
            case 'list-owner':
                $value = $this->stripComments($value);
                $this->mlist('owner', $value);
                break;
            case 'list-help':
                $value = $this->stripComments($value);
                $this->mlist('help', $value);
                break;
            case 'list-id':
                $value = $this->stripComments($value);
                $this->mlist('id', $value);
                break;
            case 'x-spam-status':
                $this->x_spam_status = $this->parseSpamStatus($value);
                break;
            default:
                break;
        }
    }

    /**
     * @param string $address
     * @return array
     */
    function getAddressTokens($address) {
        $aTokens = array();
        $aAddress = array();
        $aSpecials = array('(' ,'<' ,',' ,';' ,':');
        $aReplace =  array(' (',' <',' ,',' ;',' :');
        $address = str_replace($aSpecials,$aReplace,$address);
        $iCnt = strlen($address);
        $i = 0;
        while ($i < $iCnt) {
            $cChar = $address{$i};
            switch($cChar)
            {
            case '<':
                $iEnd = strpos($address,'>',$i+1);
                if (!$iEnd) {
                   $sToken = substr($address,$i);
                   $i = $iCnt;
                } else {
                   $sToken = substr($address,$i,$iEnd - $i +1);
                   $i = $iEnd;
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case '"':
                $iEnd = strpos($address,$cChar,$i+1);
                if ($iEnd) {
                   // skip escaped quotes
                   $prev_char = $address{$iEnd-1};
                   while ($prev_char === '\\' && substr($address,$iEnd-2,2) !== '\\\\') {
                       $iEnd = strpos($address,$cChar,$iEnd+1);
                       if ($iEnd) {
                          $prev_char = $address{$iEnd-1};
                       } else {
                          $prev_char = false;
                       }
                   }
                }
                if (!$iEnd) {
                    $sToken = substr($address,$i);
                    $i = $iCnt;
                } else {
                    // also remove the surrounding quotes
                    $sToken = substr($address,$i+1,$iEnd - $i -1);
                    $i = $iEnd;
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case '(':
                array_pop($aTokens); //remove inserted space
                $iEnd = strpos($address,')',$i);
                if (!$iEnd) {
                    $sToken = substr($address,$i);
                    $i = $iCnt;
                } else {
                    $iDepth = 1;
                    $iComment = $i;
                    while (($iDepth > 0) && (++$iComment < $iCnt)) {
                        $cCharComment = $address{$iComment};
                        switch($cCharComment) {
                            case '\\':
                                ++$iComment;
                                break;
                            case '(':
                                ++$iDepth;
                                break;
                            case ')':
                                --$iDepth;
                                break;
                            default:
                                break;
                        }
                    }
                    if ($iDepth == 0) {
                        $sToken = substr($address,$i,$iComment - $i +1);
                        $i = $iComment;
                    } else {
                        $sToken = substr($address,$i,$iEnd - $i + 1);
                        $i = $iEnd;
                    }
                }
                // check the next token in case comments appear in the middle of email addresses
                $prevToken = end($aTokens);
                if (!in_array($prevToken,$aSpecials,true)) {
                    if ($i+1<strlen($address) && !in_array($address{$i+1},$aSpecials,true)) {
                        $iEnd = strpos($address,' ',$i+1);
                        if ($iEnd) {
                            $sNextToken = trim(substr($address,$i+1,$iEnd - $i -1));
                            $i = $iEnd-1;
                        } else {
                            $sNextToken = trim(substr($address,$i+1));
                            $i = $iCnt;
                        }
                        // remove the token
                        array_pop($aTokens);
                        // create token and add it again
                        $sNewToken = $prevToken . $sNextToken;
                        if($sNewToken) $aTokens[] = $sNewToken;
                    }
                }
                $sToken = str_replace($aReplace, $aSpecials,$sToken);
                if ($sToken) $aTokens[] = $sToken;
                break;
            case ',':
            case ':':
            case ';':
            case ' ':
                $aTokens[] = $cChar;
                break;
            default:
                $iEnd = strpos($address,' ',$i+1);
                if ($iEnd) {
                    $sToken = trim(substr($address,$i,$iEnd - $i));
                    $i = $iEnd-1;
                } else {
                    $sToken = trim(substr($address,$i));
                    $i = $iCnt;
                }
                if ($sToken) $aTokens[] = $sToken;
            }
            ++$i;
        }
        return $aTokens;
    }

    /**
     * @param array $aStack
     * @param array $aComment
     * @param string $sEmail
     * @param string $sGroup
     * @return object AddressStructure object
     */
    function createAddressObject(&$aStack,&$aComment,&$sEmail,$sGroup='') {
        //$aStack=explode(' ',implode('',$aStack));
        if (!$sEmail) {
            while (count($aStack) && !$sEmail) {
                $sEmail = trim(array_pop($aStack));
            }
        }
        if (count($aStack)) {
            $sPersonal = trim(implode('',$aStack));
        } else {
            $sPersonal = '';
        }
        if (!$sPersonal && count($aComment)) {
            $sComment = trim(implode(' ',$aComment));
            $sPersonal .= $sComment;
        }
        $oAddr = new AddressStructure();
        if ($sPersonal && substr($sPersonal,0,2) == '=?') {
            $oAddr->personal = encodeHeader($sPersonal);
        } else {
            $oAddr->personal = $sPersonal;
        }
 //       $oAddr->group = $sGroup;
        $iPosAt = strpos($sEmail,'@');
        if ($iPosAt) {
           $oAddr->mailbox = substr($sEmail, 0, $iPosAt);
           $oAddr->host = substr($sEmail, $iPosAt+1);
        } else {
           $oAddr->mailbox = $sEmail;
           $oAddr->host = false;
        }
        $sEmail = '';
        $aStack = $aComment = array();
        return $oAddr;
    }

    /**
     * recursive function for parsing address strings and storing them in an address stucture object.
     *  personal name: encoded: =?charset?Q|B?string?=
     *                 quoted:  "string"
     *                 normal:  string
     *  email        : <mailbox@host>
     *               : mailbox@host
     *  This function is also used for validating addresses returned from compose
     *  That's also the reason that the function became a little bit huge
     * @param string $address
     * @param boolean $ar return array instead of only the first element
     * @param array $addr_ar (obsolete) array with parsed addresses
     * @param string $group (obsolete)
     * @param string $host default domainname in case of addresses without a domainname
     * @param string $lookup (since) callback function for lookup of address strings which are probably nicks (without @)
     * @return mixed array with AddressStructure objects or only one address_structure object.
     */
    function parseAddress($address,$ar=false,$aAddress=array(),$sGroup='',$sHost='',$lookup=false) {
        $aTokens = $this->getAddressTokens($address);
        $sPersonal = $sEmail = $sComment = $sGroup = '';
        $aStack = $aComment = array();
        foreach ($aTokens as $sToken) {
            $cChar = $sToken{0};
            switch ($cChar)
            {
            case '=':
            case '"':
            case ' ':
                $aStack[] = $sToken;
                break;
            case '(':
                $aComment[] = substr($sToken,1,-1);
                break;
            case ';':
                if ($sGroup) {
                    $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                    $oAddr = end($aAddress);
                    if(!$oAddr || ((isset($oAddr)) && !strlen($oAddr->mailbox) && !$oAddr->personal)) {
                        $sEmail = $sGroup . ':;';
                    }
                    $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                    $sGroup = '';
                    $aStack = $aComment = array();
                    break;
                }
            case ',':
                $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail,$sGroup);
                break;
            case ':':
                $sGroup = trim(implode(' ',$aStack));
                $sGroup = preg_replace('/\s+/',' ',$sGroup);
                $aStack = array();
                break;
            case '<':
               $sEmail = trim(substr($sToken,1,-1));
               break;
            case '>':
               /* skip */
               break;
            default: $aStack[] = $sToken; break;
            }
        }
        /* now do the action again for the last address */
        $aAddress[] = $this->createAddressObject($aStack,$aComment,$sEmail);
        /* try to lookup the addresses in case of invalid email addresses */
        $aProcessedAddress = array();
        foreach ($aAddress as $oAddr) {
          $aAddrBookAddress = array();
          if (!$oAddr->host) {
            $grouplookup = false;
            if ($lookup) {
                 $aAddr = call_user_func_array($lookup,array($oAddr->mailbox));
                 if (isset($aAddr['email'])) {
                     if (strpos($aAddr['email'],',')) {
                         $grouplookup = true;
                         $aAddrBookAddress = $this->parseAddress($aAddr['email'],true);
                     } else {
                         $iPosAt = strpos($aAddr['email'], '@');
                         if ($iPosAt === FALSE) {
                             $oAddr->mailbox = $aAddr['email'];
                             $oAddr->host = FALSE;
                         } else {
                             $oAddr->mailbox = substr($aAddr['email'], 0, $iPosAt);
                             $oAddr->host = substr($aAddr['email'], $iPosAt+1);
                         }
                         if (isset($aAddr['name'])) {
                             $oAddr->personal = $aAddr['name'];
                         } else {
                             $oAddr->personal = encodeHeader($sPersonal);
                         }
                     }
                 }
            }
            if (!$grouplookup && !strlen($oAddr->mailbox)) {
                $oAddr->mailbox = trim($sEmail);
                if ($sHost && strlen($oAddr->mailbox)) {
                    $oAddr->host = $sHost;
                }
            } else if (!$grouplookup && !$oAddr->host) {
                if ($sHost && strlen($oAddr->mailbox)) {
                    $oAddr->host = $sHost;
                }
            }
          }
          if (!$aAddrBookAddress && strlen($oAddr->mailbox)) {
              $aProcessedAddress[] = $oAddr;
          } else {
              $aProcessedAddress = array_merge($aProcessedAddress,$aAddrBookAddress);
          }
        }
        if ($ar) {
            return $aProcessedAddress;
        } else {
            if (isset($aProcessedAddress[0]))
                return $aProcessedAddress[0];
            else
                return '';
        }
    }

    /**
     * Normalise the different Priority headers into a uniform value,
     * namely that of the X-Priority header (1, 3, 5). Supports:
     * Priority, X-Priority, Importance.
     * X-MS-Mail-Priority is not parsed because it always coincides
     * with one of the other headers.
     *
     * NOTE: this is actually a duplicate from the function in
     * functions/imap_messages. I'm not sure if it's ok here to call
     * that function?
     * @param string $sValue literal priority name
     * @return integer
     */
    function parsePriority($sValue) {
        // don't use function call inside array_shift.
        $aValue = preg_split('/\s/',trim($sValue));
        $value = strtolower(array_shift($aValue));

        if ( is_numeric($value) ) {
            return $value;
        }
        if ( $value == 'urgent' || $value == 'high' ) {
            return 1;
        } elseif ( $value == 'non-urgent' || $value == 'low' ) {
            return 5;
        }
        // default is normal priority
        return 3;
    }

    /**
     * @param string $value content type header
     */
    function parseContentType($value) {
        $pos = strpos($value, ';');
        $props = '';
        if ($pos > 0) {
           $type = trim(substr($value, 0, $pos));
           $props = trim(substr($value, $pos+1));
        } else {
           $type = $value;
        }
        $content_type = new ContentType($type);
        if ($props) {
            $properties = $this->parseProperties($props);
            if (!isset($properties['charset'])) {
                $properties['charset'] = 'us-ascii';
            }
            $content_type->properties = $this->parseProperties($props);
        }
        $this->content_type = $content_type;
    }

    /**
     * RFC2184
     * @param array $aParameters
     * @return array
     */
    function processParameters($aParameters) {
        $aResults = array();
        $aCharset = array();
        // handle multiline parameters
        foreach($aParameters as $key => $value) {
            if ($iPos = strpos($key,'*')) {
                $sKey = substr($key,0,$iPos);
                if (!isset($aResults[$sKey])) {
                    $aResults[$sKey] = $value;
                    if (substr($key,-1) == '*') { // parameter contains language/charset info
                        $aCharset[] = $sKey;
                    }
                } else {
                    $aResults[$sKey] .= $value;
                }
            } else {
                $aResults[$key] = $value;
            }
        }
        foreach ($aCharset as $key) {
            $value = $aResults[$key];
            // extract the charset & language
            $charset = substr($value,0,strpos($value,"'"));
            $value = substr($value,strlen($charset)+1);
            $language = substr($value,0,strpos($value,"'"));
            $value = substr($value,strlen($charset)+1);
            /* FIXME: What's the status of charset decode with language information ????
             * Maybe language information contains only ascii text and charset_decode() 
             * only runs htmlspecialchars() on it. If it contains 8bit information, you 
             * get html encoded text in charset used by selected translation.
             */
            $value = charset_decode($charset,$value);
            $aResults[$key] = $value;
        }
        return $aResults;
    }

    /**
     * @param string $value
     * @return array
     */
    function parseProperties($value) {
        $propArray = explode(';', $value);
        $propResultArray = array();
        foreach ($propArray as $prop) {
            $prop = trim($prop);
            $pos = strpos($prop, '=');
            if ($pos > 0)  {
                $key = trim(substr($prop, 0, $pos));
                $val = trim(substr($prop, $pos+1));
                if (strlen($val) > 0 && $val{0} == '"') {
                    $val = substr($val, 1, -1);
                }
                $propResultArray[$key] = $val;
            }
        }
        return $this->processParameters($propResultArray);
    }

    /**
     * Fills disposition object in rfc822Header object
     * @param string $value
     */
    function parseDisposition($value) {
        $pos = strpos($value, ';');
        $props = '';
        if ($pos > 0) {
            $name = trim(substr($value, 0, $pos));
            $props = trim(substr($value, $pos+1));
        } else {
            $name = $value;
        }
        $props_a = $this->parseProperties($props);
        $disp = new Disposition($name);
        $disp->properties = $props_a;
        $this->disposition = $disp;
    }

    /**
     * Fills mlist array keys in rfc822Header object 
     * @param string $field
     * @param string $value
     */
    function mlist($field, $value) {
        $res_a = array();
        $value_a = explode(',', $value);
        foreach ($value_a as $val) {
            $val = trim($val);
            if ($val{0} == '<') {
                $val = substr($val, 1, -1);
            }
            if (substr($val, 0, 7) == 'mailto:') {
                $res_a['mailto'] = substr($val, 7);
            } else {
                $res_a['href'] = $val;
            }
        }
        $this->mlist[$field] = $res_a;
    }

    /**
     * Parses the X-Spam-Status header
     * @param string $value
     */
    function parseSpamStatus($value) {
        // Header value looks like this:
        // No, score=1.5 required=5.0 tests=MSGID_FROM_MTA_ID,NO_REAL_NAME,UPPERCASE_25_50 autolearn=disabled version=3.1.0-gr0

        $spam_status = array();

        if (preg_match ('/^(No|Yes),\s+score=(-?\d+\.\d+)\s+required=(-?\d+\.\d+)\s+tests=(.*?)\s+autolearn=(.*?)\s+version=(.+?)$/', $value, $matches)) {
            // full header
            $spam_status['bad_format'] = 0;
            $spam_status['value'] = $matches[0];
            // is_spam
            if (isset($matches[1])
                && strtolower($matches[1]) == 'yes') {
                $spam_status['is_spam'] = true;
            } else {
                $spam_status['is_spam'] = false;
            }

            // score
            $spam_status['score'] = $matches[2];

            // required
            $spam_status['required'] = $matches[3];

            // tests
            $tests = array();
            $tests = explode(',', $matches[4]);
            foreach ($tests as $test) {
                $spam_status['tests'][] = trim($test);
            }

            // autolearn
            $spam_status['autolearn'] = $matches[5];

            // version
            $spam_status['version'] = $matches[6];
        } else {
            $spam_status['bad_format'] = 1;
            $spam_status['value'] = $value;
        }
        return $spam_status;
    }

    /**
     * function to get the address strings out of the header.
     * example1: header->getAddr_s('to').
     * example2: header->getAddr_s(array('to', 'cc', 'bcc'))
     * @param mixed $arr string or array of strings
     * @param string $separator
     * @param boolean $encoded (since 1.4.0) return encoded or plain text addresses
     * @param boolean $unconditionally_quote (since 1.4.21/1.5.2) When TRUE, always
     *                                                      quote the personal part,
     *                                                      whether or not it is
     *                                                      encoded, otherwise quoting
     *                                                      is only added if the
     *                                                      personal part is not encoded
     * @return string
     */
    function getAddr_s($arr, $separator = ',',$encoded=false,$unconditionally_quote=FALSE) {
        $s = '';

        if (is_array($arr)) {
            foreach($arr as $arg) {
                if ($this->getAddr_s($arg, $separator, $encoded, $unconditionally_quote)) {
                    $s .= $separator;
                }
            }
            $s = ($s ? substr($s, 2) : $s);
        } else {
            $addr = $this->{$arr};
            if (is_array($addr)) {
                foreach ($addr as $addr_o) {
                    if (is_object($addr_o)) {
                        if ($encoded) {
                            $s .= $addr_o->getEncodedAddress($unconditionally_quote) . $separator;
                        } else {
                            $s .= $addr_o->getAddress(TRUE, FALSE, $unconditionally_quote) . $separator;
                        }
                    }
                }
                $s = substr($s, 0, -strlen($separator));
            } else {
                if (is_object($addr)) {
                    if ($encoded) {
                        $s .= $addr->getEncodedAddress($unconditionally_quote);
                    } else {
                        $s .= $addr->getAddress(TRUE, FALSE, $unconditionally_quote);
                    }
                }
            }
        }
        return $s;
    }

    /**
     * function to get the array of addresses out of the header.
     * @param mixed $arg string or array of strings
     * @param array $excl_arr array of excluded email addresses
     * @param array $arr array of added email addresses
     * @return array
     */
    function getAddr_a($arg, $excl_arr = array(), $arr = array()) {
        if (is_array($arg)) {
            foreach($arg as $argument) {
                $arr = $this->getAddr_a($argument, $excl_arr, $arr);
            }
        } else {
            $addr = $this->{$arg};
            if (is_array($addr)) {
                foreach ($addr as $next_addr) {
                    if (is_object($next_addr)) {
                        if (isset($next_addr->host) && ($next_addr->host != '')) {
                            $email = $next_addr->mailbox . '@' . $next_addr->host;
                        } else {
                            $email = $next_addr->mailbox;
                        }
                        $email = strtolower($email);
                        if ($email && !isset($arr[$email]) && !isset($excl_arr[$email])) {
                            $arr[$email] = $next_addr->personal;
                        }
                    }
                }
            } else {
                if (is_object($addr)) {
                    $email  = $addr->mailbox;
                    $email .= (isset($addr->host) ? '@' . $addr->host : '');
                    $email  = strtolower($email);
                    if ($email && !isset($arr[$email]) && !isset($excl_arr[$email])) {
                        $arr[$email] = $addr->personal;
                    }
                }
            }
        }
        return $arr;
    }

    /**
//FIXME: This needs some documentation (inside the function too)!  Don't code w/out comments!
     * @param mixed $address array or string
     * @param boolean $recurs
     * @return mixed array, boolean
     * @since 1.3.2
     */
    function findAddress($address, $recurs = false) {
        $result = false;
        if (is_array($address)) {
            $i=0;
            foreach($address as $argument) {
                $match = $this->findAddress($argument, true);
                $last = end($match);
                if ($match[1]) {
                    return $i;
                } else {
                    if (count($match[0]) && !$result) {
                        $result = $i;
                    }
                }
                ++$i;
            }
        } else {
            if (!is_array($this->cc)) $this->cc = array();
            if (!is_array($this->to)) $this->to = array();
            $srch_addr = $this->parseAddress($address);
            $results = array();
            foreach ($this->to as $to) {
                if (strtolower($to->host) == strtolower($srch_addr->host)) {
                    if (strtolower($to->mailbox) == strtolower($srch_addr->mailbox)) {
                        $results[] = $srch_addr;
                        if (strtolower($to->personal) == strtolower($srch_addr->personal)) {
                            if ($recurs) {
                                return array($results, true);
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            foreach ($this->cc as $cc) {
                if (strtolower($cc->host) == strtolower($srch_addr->host)) {
                    if (strtolower($cc->mailbox) == strtolower($srch_addr->mailbox)) {
                        $results[] = $srch_addr;
                        if (strtolower($cc->personal) == strtolower($srch_addr->personal)) {
                            if ($recurs) {
                                return array($results, true);
                            } else {
                                return true;
                            }
                        }
                    }
                }
            }
            if ($recurs) {
                return array($results, false);
            } elseif (count($result)) {
                return true;
            } else {
                return false;
            }
        }
        //exit;
        return $result;
    }

    /**
     * @param string $type0 media type
     * @param string $type1 media subtype
     * @return array media properties
     * @todo check use of media type arguments
     */
    function getContentType($type0, $type1) {
        $type0 = $this->content_type->type0;
        $type1 = $this->content_type->type1;
        return $this->content_type->properties;
    }
}

