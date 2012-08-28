<?php
/////////////////////////////////////////////////////////
//	
//	include/read_message_print.inc
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	PURPOSE:
		Actual code that displays message body part in "source/read_message.php"
	PRE-CONDITIONS:
		There's a bunch.  Look in "source/read_message.php"
	COMMENTS:
		Yeah, I know it's messy.

********************************************************/
				include_once('../include/ryosimap.inc');
				
				echo "\n<!-- Printing part: $part //-->\n";


				/* figure out the body part's type */
					
					if (empty($typestring) || empty($type) || empty($subtype)){
						$typestring=iml_GetPartTypeString($structure, $part);
						list($type, $subtype) = explode("/", $typestring);
					}
					
				/* fetch body part */
					$body=iil_C_FetchPartBody($conn, $folder, $id, $part);

				/* decode body part */
					$encoding=iml_GetPartEncodingCode($structure, $part);
					if ($encoding == 3 ) $body=base64_decode($body);
					else if ($encoding == 4) $body=quoted_printable_decode($body);

				/* detect HTML */
					if (eregi("^<!DOCTYPE", $body) && eregi("<html>", $body)){
						if ($my_prefs["html_in_frame"]){
							$subtype = "html";
						}else{
							$body = "";
							$view_url = "view.php?user=$user&folder=$folder_url&id=$id&part=$part&is_html=1";
							echo "<p>".$rmStrings[7];
							echo '<a href="'.$view_url.'" target=_blank>'.$rmStrings[8].'</a>';
						}
					}

				/* decrypt message */
					if (eregi("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----", $body))
					{
						//show password field if passphrase not specified
						if (empty($gpg_passphrase)){
							?>
							<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']?>">
							GPG Passphrase: <input type="password" name="gpg_passphrase" value="">
							<input type="submit" name="gpg_decrypt" value="Decrypt">
							</form>
							<?php
						}else{
							include("../include/gpg.inc");
							gpg_decrypt($gpg_passphrase, $body);
						}
					} else { 
					}

				/* check if UTF-8 */
					$charset=iml_GetPartCharset($structure, $part);
					if (empty($charset)){
						$part_header = iil_C_FetchPartHeader($conn, $folder, $id, $part);
						$pattern = "/charset=[\"]?([a-zA-Z0-9_-]+)[\"]?/";
						preg_match($pattern, $part_header, $matches);
						if (count($matches)==2) $charset = $matches[1];
					}
					if (strcasecmp($charset, "utf-8")==0){
						include_once("../include/utf8.inc");
						$is_unicode = true;
						//$body = utf8ToUnicodeEntities($body);
					}else if (preg_match("/#[0-9]{5};/", $body)){
						//look for unicode that look like #12345; (without '&')
						$body = preg_replace("/(?<!&)(#[0-9]{5};)/", "&$1", $body);
						$is_unicode = false;
					}else{
						$is_unicode = false;
					}
						
				/* run through character encoding engine */
					echo "\n<!-- converting from $charset to $my_charset //-->\n";
					$body=LangConvert($body, $my_charset, $charset);

				/* dump! */
					echo "<p>";
					if (strcasecmp($subtype, "html")==0){
						if (!$my_prefs["html_in_frame"]){
							$body=strip_tags($body, '<a><b><i><u><p><br><font><div>');
						}
						$body = eregi_replace("src=\"cid:", "src=\"view.php?user=$user&folder=$folder&id=$id&cid=", $body);
						echo $body;
					}else{
						/* quote colorization */
                        $process = (($my_prefs["colorize_quotes"]==1) || ($my_prefs["detect_links"]==1));
                        if ($process){
                            $color=$my_colors["quotes"];
                            if (empty($color)) $color="blue";
                            
                            $lines=explode("\n", $body);
                            while (list($key, $line)=each($lines)){
                                $line = chop($line);
								
								//color quotes
                                if ($my_prefs["colorize_quotes"]==1){
                                    //colorize quotes
                                    if (($line[0]==">")&&(!$quoteLN)){
                                        $quoteLN=true;
                                        echo "<font color=$color>";
                                    }
                                    if (($line[0]!=">")&&($quoteLN)){
                                        $quoteLN=false;
                                        echo "</font>";
                                    }
                                }
								
								//detect links
								$html_encoded = false;
                                if ($my_prefs["detect_links"]==1){
                                    //detect URL
									$pattern = "/(.*)([fh]+[t]*tp[s]*:\/\/[a-zA-Z0-9_~#=&%\/\:;@,\.\?\+-]+)(.*)/";
									if (preg_match($pattern, $line, $match)){
										$line = encodeHTML($match[1]);
										$line.= "<a href=\"".$match[2]."\" target=_blank\">".$match[2]."</a>";
										$line.= encodeHTML($match[3]);
										$html_encoded = true;
									}
                                }
								
								//encode and spit out
								if (!$html_encoded){
									if ($is_unicode) $line = utf8ToUnicodeEntities($line);	
									else $line = encodeUTFSafeHTML($line);
								}
		                        //if ((!$html_encoded) && (!$is_unicode)) $line = encodeHTML($line);
								//else if ($is_unicode) $line = utf8ToUnicodeEntities($line);							
							
								//convert leading spaces to &nbsp;
								$indent = "";
								for ($c_pos=0; $line[$c_pos]==' ' || $line[$c_pos]=='\t'; $c_pos++){
									if ($line[$c_pos]==' ') $indent .= "&nbsp;";
									else if ($line[$c_pos]=='\t') $index .= "&nbsp;&nbsp;&nbsp;&nbsp;";
								}
								$line = $indent.trim($line);

                                echo $line."<br>\n"; flush();
                            }
						}else{
							if (!$is_unicode) $body = encodeHTML($body);
							else  $body = utf8ToUnicodeEntities($body);

							echo "<p>".nl2br($body);
						}
					}

					flush();
					
?>
