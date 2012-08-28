<?php

/**
 * Message.class.php
 *
 * This file contains functions needed to handle mime messages.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: Message.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.2
 */

/**
 * The object that contains a message.
 *
 * message is the object that contains messages. It is a recursive object in
 * that through the $entities variable, it can contain more objects of type
 * message. See documentation in mime.txt for a better description of how this
 * works.
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.0
 */
class Message {
    /**
     * rfc822header object
     * @var object
     */
    var $rfc822_header = '';
    /**
     * Headers from original email in reply
     * @var string 
     */
    var $reply_rfc822_header = '';
    /**
     * MessageHeader object
     * @var object
     */
    var $mime_header = '';
    /**
     * @var mixed
     */
    var $flags = '';
    /**
     * Media type
     * @var string
     */
    var $type0='';
    /**
     * Media subtype
     * @var string
     */
    var $type1='';
    /**
     * Nested mime parts
     * @var array
     */
    var $entities = array();
    /**
     * Message part id
     * @var string
     */
    var $entity_id = '';
    /**
     * Parent message part id
     * @var string
     */
    var $parent_ent;
    /**
     * @var mixed
     */
    var $entity;
    /**
     * @var mixed
     */
    var $parent = '';
    /**
     * @var string
     */
    var $decoded_body='';
    /**
     * Message \seen status
     * @var boolean
     */
    var $is_seen = 0;
    /**
     * Message \answered status
     * @var boolean
     */
    var $is_answered = 0;
    /**
     * Message \deleted status
     * @var boolean
     */
    var $is_deleted = 0;
    /**
     * Message \flagged status
     * @var boolean
     */
    var $is_flagged = 0;
    /**
     * Message mdn status
     * @var boolean
     */
    var $is_mdnsent = 0;
    /**
     * Message text body
     * @var string
     */
    var $body_part = '';
    /**
     * Message part offset
     * for fetching body parts out of raw messages
     * @var integer
     */
    var $offset = 0;
    /**
     * Message part length
     * for fetching body parts out of raw messages
     * @var integer
     */
    var $length = 0;
    /**
     * Local attachment filename location where the tempory attachment is
     * stored. For use in delivery class.
     * @var string
     */
    var $att_local_name = '';

    /**
     * @param string $ent entity id
     */
    function setEnt($ent) {
        $this->entity_id= $ent;
    }

    /**
     * Add nested message part
     * @param object $msg
     */
    function addEntity ($msg) {
        $this->entities[] = $msg;
    }

    /**
     * Get file name used for mime part
     * @return string file name
     * @since 1.3.2
     */
    function getFilename() {
         $filename = '';
         $header = $this->header;
         if (is_object($header->disposition)) {
              $filename = $header->disposition->getProperty('filename');
              if (trim($filename) == '') {
                  $name = decodeHeader($header->disposition->getProperty('name'));
                  if (!trim($name)) {
                      $name = $header->getParameter('name');
                      if(!trim($name)) {
                          if (!trim( $header->id )) {
                              $filename = 'untitled-[' . $this->entity_id . ']' . '.' . strtolower($header->type1);
                          } else {
                              $filename = 'cid: ' . $header->id . '.' . strtolower($header->type1);
                          }
                      } else {
                          $filename = $name;
                      }
                  } else {
                      $filename = $name;
                  }
              }
         } else {
              $filename = $header->getParameter('filename');
              if (!trim($filename)) {
                  $filename = $header->getParameter('name');
                  if (!trim($filename)) {
                      if (!trim( $header->id )) {
                          $filename = 'untitled-[' . $this->entity_id . ']' . '.' . strtolower($header->type1);
                      } else {
                          $filename = 'cid: ' . $header->id . '.' . strtolower($header->type1);
                      }
                  }
              }
         }
         return $filename;
    }

    /**
     * Add header object to message object.
     * WARNING: Unfinished code. Don't expect it to work in older sm versions.
     * @param mixed $read array or string with message headers
     * @todo FIXME: rfc822header->parseHeader() does not return rfc822header object
     */
    function addRFC822Header($read) {
        $header = new Rfc822Header();
        $this->rfc822_header = $header->parseHeader($read);
    }

    /**
     * @param string $ent
     * @return mixed (object or string?)
     */
    function getEntity($ent) {
        $cur_ent = $this->entity_id;
        $msg = $this;
        if (($cur_ent == '') || ($cur_ent == '0')) {
            $cur_ent_a = array();
        } else {
            $cur_ent_a = explode('.', $this->entity_id);
        }
        $ent_a = explode('.', $ent);

        for ($i = 0,$entCount = count($ent_a) - 1; $i < $entCount; ++$i) {
            if (isset($cur_ent_a[$i]) && ($cur_ent_a[$i] != $ent_a[$i])) {
                $msg = $msg->parent;
                $cur_ent_a = explode('.', $msg->entity_id);
                --$i;
            } else if (!isset($cur_ent_a[$i])) {
                if (isset($msg->entities[($ent_a[$i]-1)])) {
                    $msg = $msg->entities[($ent_a[$i]-1)];
                } else {
                    $msg = $msg->entities[0];
                }
            }
            if (($msg->type0 == 'message') && ($msg->type1 == 'rfc822')) {
                /*this is a header for a message/rfc822 entity */
                $msg = $msg->entities[0];
            }
        }

        if (($msg->type0 == 'message') && ($msg->type1 == 'rfc822')) {
            /*this is a header for a message/rfc822 entity */
            $msg = $msg->entities[0];
        }

        if (isset($msg->entities[($ent_a[$entCount])-1])) {
            if (is_object($msg->entities[($ent_a[$entCount])-1])) {
                $msg = $msg->entities[($ent_a[$entCount]-1)];
            }
        }

        return $msg;
    }

    /**
     * Set message body
     * @param string $s message body
     */
    function setBody($s) {
        $this->body_part = $s;
    }

    /**
     * Clean message object
     */
    function clean_up() {
        $msg = $this;
        $msg->body_part = '';

        foreach ($msg->entities as $m) {
            $m->clean_up();
        }
    }

    /**
     * @return string
     */
    function getMailbox() {
        $msg = $this;
        while (is_object($msg->parent)) {
            $msg = $msg->parent;
        }
        return $msg->mailbox;
    }

    /*
     * Bodystructure parser, a recursive function for generating the
     * entity-tree with all the mime-parts.
     *
     * It follows RFC2060 and stores all the described fields in the
     * message object.
     *
     * Question/Bugs:
     *
     * Ask for me (Marc Groot Koerkamp, stekkel@users.sourceforge.net)
     * @param string $read
     * @param integer $i
     * @param mixed $sub_msg
     * @return object Message object
     * @todo define argument and return types
     */
    function parseStructure($read, &$i, $sub_msg = '') {
        $msg = Message::parseBodyStructure($read, $i, $sub_msg);
        if($msg) $msg->setEntIds($msg,false,0);
        return $msg;
    }

    /**
     * @param object $msg
     * @param mixed $init
     * @param integer $i
     * @todo document me
     * @since 1.4.0
     */
    function setEntIds(&$msg,$init=false,$i=0) {
        $iCnt = count($msg->entities);
        if ($init !==false) {
            $iEntSub = $i+1;
            if ($msg->parent->type0 == 'message' &&
                $msg->parent->type1 == 'rfc822' &&
                $msg->type0 == 'multipart') {
                $iEntSub = '0';
            }
            if ($init) {
                $msg->entity_id = "$init.$iEntSub";
            } else {
                $msg->entity_id = $iEntSub;
            }
        } else if ($iCnt) {
            $msg->entity_id='0';
        } else {
            $msg->entity_id='1';
        }
        for ($i=0;$i<$iCnt;++$i) {
            $msg->entities[$i]->parent =& $msg;
            if (strrchr($msg->entity_id, '.') != '.0') {
                $msg->entities[$i]->setEntIds($msg->entities[$i],$msg->entity_id,$i);
            } else {
                $msg->entities[$i]->setEntIds($msg->entities[$i],$msg->parent->entity_id,$i);
            }
        }
    }

    /**
     * @param string $read
     * @param integer $i
     * @param mixed $sub_msg
     * @return object Message object
     * @todo document me
     * @since 1.4.0 (code was part of parseStructure() in 1.3.x)
     */
    function parseBodyStructure($read, &$i, $sub_msg = '') {
        $arg_no = 0;
        $arg_a  = array();
        if ($sub_msg) {
            $message = $sub_msg;
        } else {
            $message = new Message();
        }

        for ($cnt = strlen($read); $i < $cnt; ++$i) {
            $char = strtoupper($read{$i});
            switch ($char) {
                case '(':
                    switch($arg_no) {
                        case 0:
                            if (!isset($msg)) {
                                $msg = new Message();
                                $hdr = new MessageHeader();
                                $hdr->type0 = 'text';
                                $hdr->type1 = 'plain';
                                $hdr->encoding = '7bit';
                            } else {
                                $msg->header->type0 = 'multipart';
                                $msg->type0 = 'multipart';
                                while ($read{$i} == '(') {
                                    $msg->addEntity($msg->parseBodyStructure($read, $i, $msg));
                                }
                            }
                            break;
                        case 1:
                            /* multipart properties */
                            ++$i;
                            $arg_a[] = $msg->parseProperties($read, $i);
                            ++$arg_no;
                            break;
                        case 2:
                            if (isset($msg->type0) && ($msg->type0 == 'multipart')) {
                                ++$i;
                                $arg_a[] = $msg->parseDisposition($read, $i);
                            } else { /* properties */
                                $arg_a[] = $msg->parseProperties($read, $i);
                            }
                            ++$arg_no;
                            break;
                        case 3:
                            if (isset($msg->type0) && ($msg->type0 == 'multipart')) {
                                ++$i;
                                $arg_a[]= $msg->parseLanguage($read, $i);
                            }
                        case 7:
                            if (($arg_a[0] == 'message') && ($arg_a[1] == 'rfc822')) {
                                $msg->header->type0 = $arg_a[0];
                                $msg->header->type1 = $arg_a[1];
                                $msg->type0 = $arg_a[0];
                                $msg->type1 = $arg_a[1];
                                $rfc822_hdr = new Rfc822Header();
                                $msg->rfc822_header = $msg->parseEnvelope($read, $i, $rfc822_hdr);
                                while (($i < $cnt) && ($read{$i} != '(')) {
                                    ++$i;
                                }
                                $msg->addEntity($msg->parseBodyStructure($read, $i,$msg));
                            }
                            break;
                        case 8:
                            ++$i;
                            $arg_a[] = $msg->parseDisposition($read, $i);
                            ++$arg_no;
                            break;
                        case 9:
                            ++$i;
                            if (($arg_a[0] == 'text') || (($arg_a[0] == 'message') && ($arg_a[1] == 'rfc822'))) {
                                $arg_a[] = $msg->parseDisposition($read, $i);
                            } else {
                                $arg_a[] = $msg->parseLanguage($read, $i);
                            }
                            ++$arg_no;
                            break;
                       case 10:
                           if (($arg_a[0] == 'text') || (($arg_a[0] == 'message') && ($arg_a[1] == 'rfc822'))) {
                               ++$i;
                               $arg_a[] = $msg->parseLanguage($read, $i);
                           } else {
                               $i = $msg->parseParenthesis($read, $i);
                               $arg_a[] = ''; /* not yet described in rfc2060 */
                           }
                           ++$arg_no;
                           break;
                       default:
                           /* unknown argument, skip this part */
                           $i = $msg->parseParenthesis($read, $i);
                           $arg_a[] = '';
                           ++$arg_no;
                           break;
                   } /* switch */
                   break;
                case '"':
                    /* inside an entity -> start processing */
                    $arg_s = $msg->parseQuote($read, $i);
                    ++$arg_no;
                    if ($arg_no < 3) {
                        $arg_s = strtolower($arg_s); /* type0 and type1 */
                    }
                    $arg_a[] = $arg_s;
                    break;
                case 'n':
                case 'N':
                    /* probably NIL argument */
                    $tmpnil = strtoupper(substr($read, $i, 4));
                    if ($tmpnil == 'NIL ' || $tmpnil == 'NIL)') {
                        $arg_a[] = '';
                        ++$arg_no;
                        $i += 2;
                    }
                    break;
                case '{':
                    /* process the literal value */
                    $arg_a[] = $msg->parseLiteral($read, $i);
                    ++$arg_no;
                    break;
        case '0':
                case is_numeric($read{$i}):
                    /* process integers */
                    if ($read{$i} == ' ') { break; }
            ++$arg_no;
            if (preg_match('/^([0-9]+).*/',substr($read,$i), $regs)) {
                $i += strlen($regs[1])-1;
                $arg_a[] = $regs[1];
            } else {
                $arg_a[] = 0;
            }
                    break;
                case ')':
                    $multipart = (isset($msg->type0) && ($msg->type0 == 'multipart'));
                    if (!$multipart) {
                        $shifted_args = (($arg_a[0] == 'text') || (($arg_a[0] == 'message') && ($arg_a[1] == 'rfc822')));
                        $hdr->type0 = $arg_a[0];
                        $hdr->type1 = $arg_a[1];

                        $msg->type0 = $arg_a[0];
                        $msg->type1 = $arg_a[1];
                        $arr = $arg_a[2];
                        if (is_array($arr)) {
                            $hdr->parameters = $arg_a[2];
                        }
                        $hdr->id = str_replace('<', '', str_replace('>', '', $arg_a[3]));
                        $hdr->description = $arg_a[4];
                        $hdr->encoding = strtolower($arg_a[5]);
                        $hdr->entity_id = $msg->entity_id;
                        $hdr->size = $arg_a[6];
                        if ($shifted_args) {
                            $hdr->lines = $arg_a[7];
                            $s = 1;
                        } else {
                            $s = 0;
                        }
                        $hdr->md5 = (isset($arg_a[7+$s]) ? $arg_a[7+$s] : $hdr->md5);
                        $hdr->disposition = (isset($arg_a[8+$s]) ? $arg_a[8+$s] : $hdr->disposition);
                        $hdr->language = (isset($arg_a[9+$s]) ? $arg_a[9+$s] : $hdr->language);
                        $msg->header = $hdr;
                    } else {
                        $hdr->type0 = 'multipart';
                        $hdr->type1 = $arg_a[0];
                        $msg->type0 = 'multipart';
                        $msg->type1 = $arg_a[0];
                        $hdr->parameters = (isset($arg_a[1]) ? $arg_a[1] : $hdr->parameters);
                        $hdr->disposition = (isset($arg_a[2]) ? $arg_a[2] : $hdr->disposition);
                        $hdr->language = (isset($arg_a[3]) ? $arg_a[3] : $hdr->language);
                        $msg->header = $hdr;
                    }
                    return $msg;
                default: break;
            } /* switch */
        } /* for */
    } /* parsestructure */

    /**
     * @param string $read
     * @param integer $i
     * @return array
     */
    function parseProperties($read, &$i) {
        $properties = array();
        $prop_name = '';

        for (; $read{$i} != ')'; ++$i) {
            $arg_s = '';
            if ($read{$i} == '"') {
                $arg_s = $this->parseQuote($read, $i);
            } else if ($read{$i} == '{') {
                $arg_s = $this->parseLiteral($read, $i);
            }

            if ($arg_s != '') {
                if ($prop_name == '') {
                    $prop_name = strtolower($arg_s);
                    $properties[$prop_name] = '';
                } else if ($prop_name != '') {
                    $properties[$prop_name] = $arg_s;
                    $prop_name = '';
                }
            }
        }
        return $properties;
    }

    /**
     * @param string $read
     * @param integer $i
     * @param object $hdr MessageHeader object
     * @return object MessageHeader object
     */
    function parseEnvelope($read, &$i, $hdr) {
        $arg_no = 0;
        $arg_a = array();
        ++$i;
        for ($cnt = strlen($read); ($i < $cnt) && ($read{$i} != ')'); ++$i) {
            $char = strtoupper($read{$i});
            switch ($char) {
                case '"':
                    $arg_a[] = $this->parseQuote($read, $i);
                    ++$arg_no;
                    break;
                case '{':
                    $arg_a[] = $this->parseLiteral($read, $i);
            /* temp bugfix (SM 1.5 will have a working clean version)
               too much work to implement that version right now */
//            --$i;
                    ++$arg_no;
                    break;
                case 'N':
                    /* probably NIL argument */
                    if (strtoupper(substr($read, $i, 3)) == 'NIL') {
                        $arg_a[] = '';
                        ++$arg_no;
                        $i += 2;
                    }
                    break;
                case '(':
                    /* Address structure (with group support)
                     * Note: Group support is useless on SMTP connections
                     *       because the protocol doesn't support it
                     */
                    $addr_a = array();
                    $group = '';
                    $a=0;
                    for (; $i < $cnt && $read{$i} != ')'; ++$i) {
                        if ($read{$i} == '(') {
                            $addr = $this->parseAddress($read, $i);
                            if (($addr->host == '') && ($addr->mailbox != '')) {
                                /* start of group */
                                $group = $addr->mailbox;
                                $group_addr = $addr;
                                $j = $a;
                            } else if ($group && ($addr->host == '') && ($addr->mailbox == '')) {
                               /* end group */
                                if ($a == ($j+1)) { /* no group members */
                                    $group_addr->group = $group;
                                    $group_addr->mailbox = '';
                                    $group_addr->personal = "$group: Undisclosed recipients;";
                                    $addr_a[] = $group_addr;
                                    $group ='';
                                }
                            } else {
                                $addr->group = $group;
                                $addr_a[] = $addr;
                            }
                            ++$a;
                        }
                    }
                    $arg_a[] = $addr_a;
                    break;
                default: break;
            }
        }

        if (count($arg_a) > 9) {
            $d = strtr($arg_a[0], array('  ' => ' '));
            $d_parts = explode(' ', $d);
            if (!$arg_a[1]) $arg_a[1] = _("(no subject)");

            $hdr->date = getTimeStamp($d_parts); /* argument 1: date */
            $hdr->date_unparsed = strtr($d,'<>','  '); /* original date */
            $hdr->subject = $arg_a[1];     /* argument 2: subject */
            $hdr->from = is_array($arg_a[2]) ? $arg_a[2][0] : '';     /* argument 3: from        */
            $hdr->sender = is_array($arg_a[3]) ? $arg_a[3][0] : '';   /* argument 4: sender      */
            $hdr->reply_to = is_array($arg_a[4]) ? $arg_a[4][0] : '';  /* argument 5: reply-to    */
            $hdr->to = $arg_a[5];          /* argument 6: to          */
            $hdr->cc = $arg_a[6];          /* argument 7: cc          */
            $hdr->bcc = $arg_a[7];         /* argument 8: bcc         */
            $hdr->in_reply_to = $arg_a[8];   /* argument 9: in-reply-to */
            $hdr->message_id = $arg_a[9];  /* argument 10: message-id */
        }
        return $hdr;
    }

    /**
     * @param string $read
     * @param integer $i
     * @return string
     * @todo document me
     */
    function parseLiteral($read, &$i) {
        $lit_cnt = '';
        ++$i;
        $iPos = strpos($read,'}',$i);
        if ($iPos) {
            $lit_cnt = substr($read, $i, $iPos - $i);
            $i += strlen($lit_cnt) + 3; /* skip } + \r + \n */
            /* Now read the literal */
            $s = ($lit_cnt ? substr($read,$i,$lit_cnt): '');
            $i += $lit_cnt;
            /* temp bugfix (SM 1.5 will have a working clean version)
               too much work to implement that version right now */
            --$i;
        } else { /* should never happen */
            $i += 3; /* } + \r + \n */
            $s = '';
        }
        return $s;
    }

    /**
     * function parseQuote
     *
     * This extract the string value from a quoted string. After the end-quote
     * character is found it returns the string. The offset $i when calling
     * this function points to the first double quote. At the end it points to
     * The ending quote. This function takes care of escaped double quotes.
     * "some \"string\""
     * ^               ^
     * initial $i      end position $i
     *
     * @param string $read
     * @param integer $i offset in $read
     * @return string string inbetween the double quotes
     * @author Marc Groot Koerkamp
     */
    function parseQuote($read, &$i) {
        $s = '';
        $iPos = ++$i;
        $iPosStart = $iPos;
        while (true) {
            $iPos = strpos($read,'"',$iPos);
            if (!$iPos) break;
            if ($iPos && $read{$iPos -1} != '\\') {
                $s = substr($read,$i,($iPos-$i));
                $i = $iPos;
                break;
            } else if ($iPos > 1 && $read{$iPos -1} == '\\' && $read{$iPos-2} == '\\') {
                // This is an unique situation where the fast detection of the string
                // fails. If the quote string ends with \\ then we need to iterate
                // through the entire string to make sure we detect the unexcaped
                // double quotes correctly.
                $s = '';
                $bEscaped = false;
                $k = 0;
                 for ($j=$iPosStart,$iCnt=strlen($read);$j<$iCnt;++$j) {
                    $cChar = $read{$j};
                    switch ($cChar) {
                        case '\\':
                           $bEscaped = !$bEscaped;
                            $s .= $cChar;
                            break;
                         case '"':
                            if ($bEscaped) {
                                $s .= $cChar;
                                $bEscaped = false;
                            } else {
                                $i = $j;
                                break 3;
                            }
                            break;
                         default:
                            if ($bEscaped) {
                               $bEscaped = false;
                            }
                            $s .= $cChar;
                            break;
                    }
                }
            }
            ++$iPos;
            if ($iPos > strlen($read)) {
                break;
            }
        }
        return $s;
    }

    /**
     * @param string $read
     * @param integer $i
     * @return object AddressStructure object
     */
    function parseAddress($read, &$i) {
        $arg_a = array();
        for (; $read{$i} != ')'; ++$i) {
            $char = strtoupper($read{$i});
            switch ($char) {
                case '"': $arg_a[] = $this->parseQuote($read, $i); break;
                case '{': $arg_a[] = $this->parseLiteral($read, $i); break;
                case 'n':
                case 'N':
                    if (strtoupper(substr($read, $i, 3)) == 'NIL') {
                        $arg_a[] = '';
                        $i += 2;
                    }
                    break;
                default: break;
            }
        }

        if (count($arg_a) == 4) {
            $adr = new AddressStructure();
            $adr->personal = $arg_a[0];
            $adr->adl = $arg_a[1];
            $adr->mailbox = $arg_a[2];
            $adr->host = $arg_a[3];
        } else {
            $adr = '';
        }
        return $adr;
    }

    /**
     * @param string $read
     * @param integer $i
     * @param object Disposition object or empty string
     */
    function parseDisposition($read, &$i) {
        $arg_a = array();
        for (; $read{$i} != ')'; ++$i) {
            switch ($read{$i}) {
                case '"': $arg_a[] = $this->parseQuote($read, $i); break;
                case '{': $arg_a[] = $this->parseLiteral($read, $i); break;
                case '(': $arg_a[] = $this->parseProperties($read, $i); break;
                default: break;
            }
        }

        if (isset($arg_a[0])) {
            $disp = new Disposition($arg_a[0]);
            if (isset($arg_a[1])) {
                $disp->properties = $arg_a[1];
            }
        }
        return (is_object($disp) ? $disp : '');
    }

    /**
     * @param string $read
     * @param integer $i
     * @return object Language object or empty string
     */
    function parseLanguage($read, &$i) {
        /* no idea how to process this one without examples */
        $arg_a = array();

        for (; $read{$i} != ')'; ++$i) {
            switch ($read{$i}) {
                case '"': $arg_a[] = $this->parseQuote($read, $i); break;
                case '{': $arg_a[] = $this->parseLiteral($read, $i); break;
                case '(': $arg_a[] = $this->parseProperties($read, $i); break;
                default: break;
            }
        }

        if (isset($arg_a[0])) {
            $lang = new Language($arg_a[0]);
            if (isset($arg_a[1])) {
                $lang->properties = $arg_a[1];
            }
        }
        return (is_object($lang) ? $lang : '');
    }

    /**
     * Parse message text enclosed in parenthesis
     * @param string $read
     * @param integer $i
     * @return integer
     */
    function parseParenthesis($read, $i) {
        for ($i++; $read{$i} != ')'; ++$i) {
            switch ($read{$i}) {
                case '"': $this->parseQuote($read, $i); break;
                case '{': $this->parseLiteral($read, $i); break;
                case '(': $this->parseProperties($read, $i); break;
                default: break;
            }
        }
        return $i;
    }

    /**
     * Function to fill the message structure in case the
     * bodystructure is not available
     * NOT FINISHED YET
     * @param string $read
     * @param string $type0 message part type
     * @param string $type1 message part subtype
     * @return string (only when type0 is not message or multipart)
     */
    function parseMessage($read, $type0, $type1) {
        switch ($type0) {
            case 'message':
                $rfc822_header = true;
                $mime_header = false;
                break;
            case 'multipart':
                $rfc822_header = false;
                $mime_header = true;
                break;
            default: return $read;
        }

        for ($i = 1; $i < $count; ++$i) {
            $line = trim($body[$i]);
            if (($mime_header || $rfc822_header) &&
                (preg_match("/^.*boundary=\"?(.+(?=\")|.+).*/i", $line, $reg))) {
                $bnd = $reg[1];
                $bndreg = $bnd;
                $bndreg = str_replace("\\", "\\\\", $bndreg);
                $bndreg = str_replace("?", "\\?", $bndreg);
                $bndreg = str_replace("+", "\\+", $bndreg);
                $bndreg = str_replace(".", "\\.", $bndreg);
                $bndreg = str_replace("/", "\\/", $bndreg);
                $bndreg = str_replace("-", "\\-", $bndreg);
                $bndreg = str_replace("(", "\\(", $bndreg);
                $bndreg = str_replace(")", "\\)", $bndreg);
            } else if ($rfc822_header && $line == '') {
                $rfc822_header = false;
                if ($msg->type0 == 'multipart') {
                    $mime_header = true;
                }
            }

            if ((($line{0} == '-') || $rfc822_header)  && isset($boundaries[0])) {
                $cnt = count($boundaries)-1;
                $bnd = $boundaries[$cnt]['bnd'];
                $bndreg = $boundaries[$cnt]['bndreg'];

                $regstr = '/^--'."($bndreg)".".*".'/';
                if (preg_match($regstr, $line, $reg)) {
                    $bndlen = strlen($reg[1]);
                    $bndend = false;
                    if (strlen($line) > ($bndlen + 3)) {
                        if (($line{$bndlen+2} == '-') && ($line{$bndlen+3} == '-')) {
                            $bndend = true;
                        }
                    }
                    if ($bndend) {
                        /* calc offset and return $msg */
                        //$entStr = CalcEntity("$entStr", -1);
                        array_pop($boundaries);
                        $mime_header = true;
                        $bnd_end = true;
                    } else {
                        $mime_header = true;
                         $bnd_end = false;
                        //$entStr = CalcEntity("$entStr", 0);
                        ++$content_indx;
                    }
                } else {
                    if ($header) { }
                }
            }
        }
    }

    /**
     * @param array $entity
     * @param array $alt_order
     * @param boolean $strict
     * @return array
     */
    function findDisplayEntity($entity = array(), $alt_order = array('text/plain', 'text/html'), $strict=false) {
        $found = false;
        if ($this->type0 == 'multipart') {
            if($this->type1 == 'alternative') {
                $msg = $this->findAlternativeEntity($alt_order);
                if ( ! is_null($msg) ) {
                    if (count($msg->entities) == 0) {
                        $entity[] = $msg->entity_id;
                    } else {
                        $entity = $msg->findDisplayEntity($entity, $alt_order, $strict);
                    }
                    $found = true;
                }
            } else if ($this->type1 == 'related') { /* RFC 2387 */
                $msgs = $this->findRelatedEntity();
                foreach ($msgs as $msg) {
                    if (count($msg->entities) == 0) {
                        $entity[] = $msg->entity_id;
                    } else {
                        $entity = $msg->findDisplayEntity($entity, $alt_order, $strict);
                    }
                }
                if (count($msgs) > 0) {
                    $found = true;
                }
            } else { /* Treat as multipart/mixed */
                foreach ($this->entities as $ent) {
                    if(!(is_object($ent->header->disposition) && strtolower($ent->header->disposition->name) == 'attachment') &&
                            (!isset($ent->header->parameters['filename'])) &&
                            (!isset($ent->header->parameters['name'])) &&
                            (($ent->type0 != 'message') && ($ent->type1 != 'rfc822'))) {
                        $entity = $ent->findDisplayEntity($entity, $alt_order, $strict);
                        $found = true;
                    }
                }
            }
        } else { /* If not multipart, then just compare with each entry from $alt_order */
            $type = $this->type0.'/'.$this->type1;
//        $alt_order[] = "message/rfc822";
            foreach ($alt_order as $alt) {
                if( ($alt == $type) && isset($this->entity_id) ) {
                    if ((count($this->entities) == 0) &&
                            (!isset($this->header->parameters['filename'])) &&
                            (!isset($this->header->parameters['name'])) &&
                            (isset($this->header->disposition) && is_object($this->header->disposition) &&
                             strtolower($this->header->disposition->name) != 'attachment')) {
                        $entity[] = $this->entity_id;
                        $found = true;
                    }
                }
            }
        }
        if(!$found) {
            foreach ($this->entities as $ent) {
                if(!(is_object($ent->header->disposition) && strtolower($ent->header->disposition->name) == 'attachment') &&
                   (($ent->type0 != 'message') && ($ent->type1 != 'rfc822'))) {
                    $entity = $ent->findDisplayEntity($entity, $alt_order, $strict);
                    $found = true;
                }
            }
        }
        if(!$strict && !$found) {
            if (($this->type0 == 'text') &&
                in_array($this->type1, array('plain', 'html', 'message')) &&
                isset($this->entity_id)) {
                if (count($this->entities) == 0) {
                    if (!is_object($this->header->disposition) || strtolower($this->header->disposition->name) != 'attachment') {
                        $entity[] = $this->entity_id;
                    }
                }
            }
        }
        return $entity;
    }

    /**
     * @param array $alt_order
     * @return entity
     */
    function findAlternativeEntity($alt_order) {
        /* If we are dealing with alternative parts then we  */
        /* choose the best viewable message supported by SM. */
        $best_view = 0;
        $entity = null;
        foreach($this->entities as $ent) {
            $type = $ent->header->type0 . '/' . $ent->header->type1;
            if ($type == 'multipart/related') {
                $type = $ent->header->getParameter('type');
                // Mozilla bug. Mozilla does not provide the parameter type.
                if (!$type) $type = 'text/html';
            }
            $altCount = count($alt_order);
            for ($j = $best_view; $j < $altCount; ++$j) {
                if (($alt_order[$j] == $type) && ($j >= $best_view)) {
                    $best_view = $j;
                    $entity = $ent;
                }
            }
        }
        return $entity;
    }

    /**
     * @return array
     */
    function findRelatedEntity() {
        $msgs = array();
        $related_type = $this->header->getParameter('type');
        // Mozilla bug. Mozilla does not provide the parameter type.
        if (!$related_type) $related_type = 'text/html';
        $entCount = count($this->entities);
        for ($i = 0; $i < $entCount; ++$i) {
            $type = $this->entities[$i]->header->type0.'/'.$this->entities[$i]->header->type1;
            if ($related_type == $type) {
                $msgs[] = $this->entities[$i];
            }
        }
        return $msgs;
    }

    /**
     * @param array $exclude_id
     * @param array $result
     * @return array
     */
    function getAttachments($exclude_id=array(), $result = array()) {
/*
        if (($this->type0 == 'message') &&
        ($this->type1 == 'rfc822') &&
        ($this->entity_id) ) {
            $this = $this->entities[0];
        }
*/
        if (count($this->entities)) {
            foreach ($this->entities as $entity) {
                $exclude = false;
                foreach ($exclude_id as $excl) {
                    if ($entity->entity_id === $excl) {
                        $exclude = true;
                    }
                }

                if (!$exclude) {
                    if ($entity->type0 == 'multipart') {
                        $result = $entity->getAttachments($exclude_id, $result);
                    } else if ($entity->type0 != 'multipart') {
                        $result[] = $entity;
                    }
                }
            }
        } else {
            $exclude = false;
            foreach ($exclude_id as $excl) {
                $exclude = $exclude || ($this->entity_id == $excl);
            }

            if (!$exclude) {
                $result[] = $this;
            }
        }
        return $result;
    }

    /**
     * Add attachment to message object
     * @param string $type attachment type
     * @param string $name attachment name
     * @param string $location path to attachment
     */
    function initAttachment($type, $name, $location) {
        $attachment = new Message();
        $mime_header = new MessageHeader();
        $mime_header->setParameter('name', $name);
        $pos = strpos($type, '/');
        if ($pos > 0) {
            $mime_header->type0 = substr($type, 0, $pos);
            $mime_header->type1 = substr($type, $pos+1);
        } else {
            $mime_header->type0 = $type;
        }
        $attachment->att_local_name = $location;
        $disposition = new Disposition('attachment');
        $disposition->properties['filename'] = $name;
        $mime_header->disposition = $disposition;
        $attachment->mime_header = $mime_header;
        $this->entities[]=$attachment;
    }

    /**
     * Delete all attachments from this object from disk.
     * @since 1.4.6
     */
    function purgeAttachments() {
        if ($this->att_local_name) {
            global $username, $attachment_dir;
            $hashed_attachment_dir = getHashedDir($username, $attachment_dir);
            if ( file_exists($hashed_attachment_dir . '/' . $this->att_local_name) ) {
                unlink($hashed_attachment_dir . '/' . $this->att_local_name);
            }
        }
        // recursively delete attachments from entities contained in this object
        for ($i=0, $entCount=count($this->entities);$i< $entCount; ++$i) {
            $this->entities[$i]->purgeAttachments();
        }
    }
}

