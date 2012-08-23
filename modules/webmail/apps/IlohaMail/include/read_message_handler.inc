<?php
/////////////////////////////////////////////////////////
//	
//	include/read_message_handler.inc
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
		Decides which part to display in read message window (and printer friendly view)
	PRE-CONDITIONS:
		There's a bunch.  Look in "source/read_message.php"
	COMMENTS:
		Yeah, I know it's messy.

********************************************************/


	$typeCode=iml_GetPartTypeCode($structure, $part);
	list($dummy,$subtype) = explode("/",iml_GetPartTypeString($structure,$part));
	
	echo "\n<!-- Type code: ".$header->ctype."//-->\n";
			
	if (($typeCode==3)&&(strcasecmp($subtype, "ms-tnef")==0)){
		//ms-tnef
		$type = $dummy;
		include_once("../include/tnef_decoder.inc");
		include("../include/read_tnef_print.inc");
	}else if ($typeCode==0){
		// major type is "TEXT"
		$typestring=iml_GetPartTypeString($structure, $part);
		
		// if part=0, and there's a conflict in content-type, use what's specified in header
		if (empty($part) && !empty($header->ctype) && strcmp($typestring, $header->ctype)!=0)
			$typestring = $header->ctype;

		
		list($type, $subtype) = explode("/", $typestring);
		
		
		if (strcasecmp($subtype, "HTML")==0){
			// type is "TEXT/HTML"
			if ($my_prefs["html_in_frame"]){
				//$part = $structure->pathcode;
				include("../include/read_message_print.inc");
			}else{
				$view_url = "view.php?user=$user&folder=$folder_url&id=$id&part=$part&is_html=1";
				echo "<p>".$rmStrings[7];
				echo '<a href="'.$view_url.'" target=_blank>'.$rmStrings[8].'</a>';
			}
		}else{
			// type "TEXT/PLAIN"
			//echo "<tt>\n";
			include("../include/read_message_print.inc");
			//echo "</tt>\n";
		}
	}else if ($typeCode==1 && empty($part) && ($structure[0][0]=="message")){
		// message content type is message/rfc822
		$part = "1.1";
		$typestring=iml_GetPartTypeString($structure, $part);
		list($type, $subtype) = explode("/", $typestring);
		$typeCode=iml_GetPartTypeCode($structure, $part);
		$disposition=iml_GetPartDisposition($structure, $part);
		include("../include/read_message_print.inc");
	}else if (($typeCode==1) || ($typeCode==2)){
		// multipart message
		$typestring=iml_GetPartTypeString($structure, $part);
		list($type, $subtype) = explode("/", $typestring);
		
		$mode=0;
		$subtypes = array("mixed"=>1, "signed"=>1, "related"=>1, "array"=>2, "alternative"=>2);
		$subtype = strtolower($subtype);
		if ($subtypes[$subtype]>0){
			$mode = $subtypes[$subtype];
		}else if (strcasecmp($subtype, "rfc822")==0){
			$temp_num = iml_GetNumParts($structure, $part);
			if ($temp_num > 0) $mode = 2;
		}else if (strcasecmp($subtype, "encrypted")==0){
			//check for RFC2015
			$first_part = $part.(empty($part)?"":".")."2";
			$encrypted_type = iml_GetPartTypeString($structure, $part.".1");
			if (stristr($encrypted_type, "pgp-encrypted")!==false){
				$mode=-1;
			}
		}
		
		echo "\n<!-- MODE:  $mode TypeString: $typestring Sub: $subtype //-->\n";
		
		if ($mode==-1){
			//handle RFC2015 message
			$part = $part.(empty($part)?"":".")."2";
			$typestring=iml_GetPartTypeString($structure, $part);
			list($type, $subtype) = explode("/", $typestring);
			$typeCode=iml_GetPartTypeCode($structure, $part);
			$disposition=iml_GetPartDisposition($structure, $part);
			include("../include/read_message_print.inc");
		}else if ($mode > 0){
			$originalPart=$part;
			for ($i=1;$i<=$num_parts;$i++){
				//get part info
				$part=$originalPart.(empty($originalPart)?"":".").$i;
				$typestring=iml_GetPartTypeString($structure, $part);
				list($type, $subtype) = explode("/", $typestring);
				$typeCode=iml_GetPartTypeCode($structure, $part);
				$disposition=iml_GetPartDisposition($structure, $part);
				
				echo "\n<!-- \nPart: $part \nTypeCode: $typeCode \nMode: $mode \nType: $typestring \nSubtype: $subtype \n Disp: $disposition \n//-->\n";
				
				//if NOT attachemnt...
				if (strcasecmp($disposition, "attachment")!=0){
					if (($mode==1) && ($typeCode==0)){
						//if "mixed" and type is "text" then show
						include("../include/read_message_print.inc");
					}else if ($mode==2){
						//if "alternative" and type is "text/plain" then show
						if ($my_prefs["html_in_frame"] && strcasecmp($subtype, "html")==0){
							//"Show HTML" and is html, then show
							include("../include/read_message_print.inc");
						}else if (!$my_prefs["html_in_frame"] && strcasecmp($subtype, "plain")==0){
							//Not "Show HTML" and not html, then show
							include("../include/read_message_print.inc");
						}
					}else if (($typeCode==5) && (strcasecmp($disposition, "inline")==0 || $my_prefs["show_images_inline"])){
						//if type is image and disposition is "inline" show
						echo "<img src=\"view.php?user=$user&folder=$folder_url&id=$id&part=".$part."\">";
					}else if ($typeCode==1){
						//multipart part
						$part = iml_GetFirstTextPart($structure, $part);
						if ($my_prefs["html_in_frame"]){
							//if HTML preferred, see if next part is HTML
							$next_part = iml_GetNextPart($part);
							$next_type = iml_GetPartTypeString($structure, $next_part);
							echo "\n<!-- want html: $next_part '$next_type' //-->\n";
							//if it is HTML, use it instead of text part
							if (stristr($next_type,"html")!==false){
								$typestring = "text/html";
								$type = "text";
								$subtype = "html";
								$part = $next_part;
							}
							$i++;
						}
						include("../include/read_message_print.inc");
					}
				}else{
					if (($typeCode==5) && ($my_prefs["show_images_inline"])){
						echo "<img src=\"view.php?user=$user&folder=$folder_url&id=$id&part=".$part."\"><br>\n";
					}
				}
			}
		}else{
			//echo "<p>This is a multi-part MIME message.";
			if (strcasecmp($subtype, "rfc822")!=0){
				$part = iml_GetFirstTextPart($structure, "");
				if ($my_prefs["html_in_frame"]){
					//if HTML preferred, see if next part is HTML
					$next_part = iml_GetNextPart($part);
					$next_type = iml_GetPartTypeString($structure, $next_part);
					echo "\n<!-- want html: $next_part '$next_type' //-->\n";
					//if it is HTML, use it instead of text part
					if (stristr($next_type,"html")!==false){
						$typestring = "text/html";
						$type = "text";
						$subtype = "html";
						$part = $next_part;
					}
				}
			}
			include("../include/read_message_print.inc");
		}
	}else{
		//not text or multipart, i.e. it's a file
			
		$type=iml_GetPartTypeCode($structure, $part);
		$name=iml_GetPartName($structure, $part);
		$typestring=iml_GetPartTypeString($structure,$part);
		$bytes=iml_GetPartSize($structure,$part);
		$encoding=iml_GetPartEncodingCode($structure, $part);
		$disposition = iml_GetPartDisposition($structure, $part);
		$icons_a = array("text.gif", "multi.gif", "multi.gif", "application.gif", "music.gif", "image.gif", "movie.gif", "unknown.gif");
		$href = "view.php?user=$user&folder=$folder_url&id=$id&part=".$part;
		echo "<table><tr><td align=\"center\">";
		echo "<a href=\"".$href."\" target=_blank>";
		echo "<img src=\"themes/".$my_prefs["theme"]."/images/".$icons_a[$type]."\" border=0><br>";
		echo "<font size=\"-1\">".LangDecodeSubject($name, $my_charset)."<br>[".ShowBytes($bytes)."]";
		echo "<br>".$typestring."</font>";
		echo "</a>";
		echo "</td></tr></table>\n";
	}
