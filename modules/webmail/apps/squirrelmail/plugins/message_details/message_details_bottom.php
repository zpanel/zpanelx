<?php
/**
 * Message Details plugin - bottom frame with message structure and rfc822 body
 *
 * Plugin to view the RFC822 raw message output and the bodystructure of a message
 *
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @author Marc Groot Koerkamp
 * @copyright 2002 Marc Groot Koerkamp, The Netherlands
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: message_details_bottom.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage message_details
 */

/** @ignore */
define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/mime.php');

global $color, $uid_support;

sqgetGlobalVar('passed_id', $passed_id, SQ_GET);
if (!sqGetGlobalVar('passed_ent_id', $passed_ent_id, SQ_FORM))
    $passed_ent_id = 0;
sqgetGlobalVar('mailbox', $mailbox, SQ_GET);

sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key', $key, SQ_COOKIE);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

/**
 * Calculates id of MIME entity
 * @param string $entString
 * @param integer $direction
 * @return string
 * @access private
 */
function CalcEntity($entString, $direction) {
    $result = $entString;
    if ($direction == -1) {
        $pos = strrpos($entString,'.');
        $result = substr($entString,0,$pos);
    }

    switch ($direction) {
       case 0:
          $pos = strrpos($entString,'.');
          if ($pos === false) {
         $entString++;
	     $result= $entString;
          } 
	  else {
             $level = substr($entString,0,$pos);
	     $sublevel = substr($entString,$pos+1);
	     $sublevel++;
	     $result = "$level".'.'."$sublevel";
	  }
	  break;
       case 1:
          $result = "$entString".".0";
	  break;
       default:
          break;
    }
    return ($result);
}

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$read = sqimap_mailbox_select($imapConnection, $mailbox);
if (!empty($passed_ent_id))
    $body = sqimap_run_command($imapConnection, "FETCH $passed_id BODY[$passed_ent_id]",true, $response, $readmessage, $uid_support);
else
    $body = sqimap_run_command($imapConnection, "FETCH $passed_id RFC822",true, $response, $readmessage, $uid_support);
$message_body = '';
$header = false;
$mimepart = false;
$bnd_end = false;
$messageheader = true;
$messageheaderstart=false;
$boundaries = array();
$entities = array();
session_unregister("entities");
$pre = '<b>';
$end = '</b>';
$entStr = '';
$bla ='';
$content = array ();
$content_indx = -1;
$contentset = false;

$count=count($body);
$body[$count-1] = substr($body[$count-1], -1);
for ($i=1; $i < $count; $i++) {
    $line = trim($body[$i]);
    if ($line == '') {
	$pre = '';
	$end = '';
        if ($bnd_end) {
	    $header = true;
	    $mimepart = false;
	} else if ($messageheader) {
	    if ($header) {
		$header=false;
		$end = "\n \n".'</div>'."\n \n".'<div class="ent_body" id="'.$entStr.'B">'."\n \n"; 
	    }
	    $mimepart = -$header;
	    $bnd_end = false;
	    if ($messageheaderstart) {
		$messageheaderstart=false;
	    }
	} else if ($messageheaderstart) {
	    $messageheader= false;
	} else {
	    if ($header) {
	        $pre = '';
		$end = "\n \n".'</div>'."\n \n".'<div class="ent_body" id="'.$entStr.'B">'."\n \n"; 
	    }
	    $header = false;
	    $mimepart=true;
	}  
	$contentset = false;
	$nameset = false;
    } else {
        if (!$header && $messageheader) {
	    $messageheaderstart=true;
	    if ($pre != '<b>') {
		$pre = '<i><font color ='."$color[1]>";
		$end = '</font></i>';
	    }
	}     
	if (!$messageheader && !$header ) {
	    $mimepart=true;
	}  else {
	    $mimepart=false;
	}
	$pre = '';
	$end = '';
    }
    if (  ( $header || $messageheader) && (preg_match("/^.*boundary=\"?(.+(?=\")|.+).*/i",$line,$reg)) )  {
        $bnd = $reg[1];
        $bndreg = $bnd;    
        $bndreg = str_replace("\\","\\\\",$bndreg);	
        $bndreg = str_replace("?","\\?",$bndreg);
        $bndreg = str_replace("+","\\+",$bndreg);		
        $bndreg = str_replace(".","\\.",$bndreg);			
        $bndreg = str_replace("/","\\/",$bndreg);
        $bndreg = str_replace("-","\\-",$bndreg);
        $bndreg = str_replace("(","\\(",$bndreg);
        $bndreg = str_replace(")","\\)",$bndreg);

        $boundaries[] = array( 'bnd' => $bnd, 'bndreg' => $bndreg);
        $messageheader = false;
        $messageheaderstart=false;
        $mimepart=false;
        if ($entStr=='') {
            $entStr='0';
        } else {
            $entStr = CalcEntity("$entStr",1);
        }
    }
    
    if (($line != '' && $line{0} == '-' || $header)  && isset($boundaries[0])) {
        $cnt=count($boundaries)-1;
	$bnd = $boundaries[$cnt]['bnd'];
	$bndreg = $boundaries[$cnt]['bndreg'];
      
	$regstr = '/^--'."($bndreg)".".*".'/';
	if (preg_match($regstr,$line,$reg) ) {
	    $bndlen = strlen($reg[1]);
	    $bndend = false;	    
            if (strlen($line) > ($bndlen + 3)) {
		if ($line{$bndlen+2} == '-' && $line{$bndlen+3} == '-') 
		    $bndend = true;
	    }
	    if ($bndend) {
            $entStr = CalcEntity("$entStr",-1);
            array_pop($boundaries);
            $pre .= '<b><font color ='."$color[2]>";
            $end .= '</font></b>';
            $header = true;
            $mimepart = false;
            $bnd_end = true;
            $encoding = '';
	    } else {
            $header = true;
            $bnd_end = false;
            $entStr = CalcEntity("$entStr",0);
            $content_indx++;
            $content[$content_indx]=array();		
            $content[$content_indx]['ent'] = '<a href="#'."$entStr \">$entStr".'</a>';
            $pre .= "\n \n".'</div>'."\n \n".'<div class="entheader" id="'.
                $entStr.'H"><a name="'."$entStr".'"><b><font color ='."$color[2]>";
            $end .= '</font></b>'."\n";
            $header = true;
            $mimepart = false;
            $encoding = '';
	    }
	}  else {
	    if ($header) {
		if (!$contentset && preg_match("/^.*(content-type:)\s*(\w+)\/(\w+).*/i",$line,$reg)) {
		    if (strtolower($reg[2]) == 'message' && strtolower($reg[3]) == 'rfc822') {
			$messageheader = true;
		    }
		    $content[$content_indx]['type'] = "$reg[2]/$reg[3]";
		    $contentset = true;
		    if ($reg[2] == 'image') {
			$entities["$entStr"] = array();
			$entities["$entStr"]['entity'] = $entStr;
			$entities["$entStr"]['contenttype']=$reg[2].'/'.$reg[3];
		    }	
   		} else if (!$nameset && preg_match("/^.*(name=\s*)\"(.*)\".*/i",$line,$reg)) {
		    $name = htmlspecialchars($reg[2]);
		    $content[$content_indx]['name'] = decodeHeader($name);
		    $nameset = true;
		    if (isset($entities["$entStr"])) {
			$entities["$entStr"]['name'] = urlEncode($reg[2]);
		    }
   		} else if (preg_match("/^.*(content-transfer-encoding:)\s*(\w+-?(\w+)?).*/i",$line,$reg) ) {
		    $encoding = $reg[2];
		    if (isset($entities["$entStr"])) {
			$entities["$entStr"]['encoding']=$reg[2];
		    }
		    $content[$content_indx]['encoding'] = $encoding;
		    $mimeentity = '';
		}

		$pre .= '<b><font color ='."$color[7]>";
		$end .= '</font></b>';
		//$mimepart=false;
	    } 		  
	}
    }  
/*
    if ($mimepart) {
    	if (isset($entities["$entStr"])) {
	    if (isset($encoding) && $encoding == 'base64') {
    		if (!isset( $entities["$entStr"]['content'])) $entities[$entStr]['content'] = '';
		$entities["$entStr"]['content'] .= $line;
	    }
        }
    } 	
*/
    $line = htmlspecialchars($line);
    $message_body .= "$pre"."$line"."$end".'<br />'."\r\n";
}

$xtra = <<<ECHO

<style>

<!--
.ent_body {
  display:inline;
}

.header {
  display:inline;
}

.entheader {
  display:inline;
  width:99%;
}
//-->

</style>

ECHO;

displayHtmlHeader( _("Message Details"), $xtra, FALSE );
/* body */
echo "<body text=\"$color[8]\" bgcolor=\"$color[4]\" link=\"$color[7]\" vlink=\"$color[7]\" alink=\"$color[7]\">\n";
echo '<code>'."\n";
echo '<font face = "monospace">'."\n";
echo '<br />'."\n";

if (count($content) > 0) {
    echo '<h2>' . _("Bodystructure") . "</h2>\n\n";
    echo '<table border=1 width="98%"><thead>'.
	  '<tr bgcolor="'."$color[7]".'">'.
	    '<td><b><font color="'."$color[5]".'">' . _("Entity") . '</font></b></td>'.
	    '<td><b><font color="'."$color[5]".'">' . _("Content-Type") . '</font></b></td>'.
	    '<td><b><font color="'."$color[5]".'">' . _("Name") . '</font></b></td>'.
	    '<td><b><font color="'."$color[5]".'">' . _("Encoding") . '</font></b></td>'.

	  '</tr>'.
	  '</thead><tbody>';
    for ($i = 0; $i < count($content);$i++) {
	echo '<tr><td>';
	echo $content[$i]['ent'].'</td><td>';
	if (isset($content[$i]['type'])) {
	    echo $content[$i]['type'];
	} else echo 'TEXT/PLAIN';
	echo '</td><td>';
	if (isset($content[$i]['name'])) {
	    echo $content[$i]['name'];
	} else echo '&nbsp;';
	echo '</td><td>';
	if (isset($content[$i]['encoding'])) {
	    echo $content[$i]['encoding'];
	} else echo '&nbsp;';
	echo '</td></tr>'."\n";
    }
    echo '</tbody></table><br>'."\n";
}
echo '<h2>' . _("RFC822 Message body") . "</h2>\n\n";
echo '<div><div class="header">'."\n\n";
echo $message_body;
echo '</div></div></font></code></body></html>';
?>
