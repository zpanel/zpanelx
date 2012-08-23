<?php
/*
	File:		compose_attachments.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	process attachments to compose MIME message
*/

while ( list($file, $v) = each($attach) ){
	if ($v==1){
		//split up file name
		$file_parts = explode(".", $file);
		
		//put together full path
		$a_path = $uploadDir."/".$file;

		//get name and type
		$a_name=mod_base64_decode($file_parts[1]);
		$a_type=strtolower(mod_base64_decode($file_parts[2]));
		if ($a_type=="") $a_type="application/octet-stream";			

		//if data is good...
		if (($file_parts[0]==$user) && (file_exists(realpath($a_path)))){
			echo "Attachment $i is good <br>\n";
			$num_parts++;			
			
			//stick it in conent array
			$part[$num_parts]["type"]="Content-Type: ".$a_type."; name=\"".$a_name."\"\r\n";
			$part[$num_parts]["disposition"]="Content-Disposition: attachment; filename=\"".$a_name."\"\r\n";
			$part[$num_parts]["encoding"]="Content-Transfer-Encoding: base64\r\n";
			$part[$num_parts]["size"] = filesize($a_path);
			$attachment_size += $part[$num_parts]["size"];
			$part[$num_parts]["path"] = $a_path;
		}else if (strpos($file_parts[0], "fwd-")!==false){
		//forward an attachment
			//extract specs of attachment
			$fwd_specs = explode("-", $file_parts[0]);
			$fwd_folder = mod_base64_decode($fwd_specs[1]);
			$fwd_id = $fwd_specs[2];
			$fwd_part = mod_base64_decode($fwd_specs[3]);
			
			//get attachment content
			$fwd_content = iil_C_FetchPartBody($conn, $fwd_folder, $fwd_id, $fwd_part);

			//get attachment header
			$fwd_header = iil_C_FetchPartHeader($conn, $fwd_folder, $fwd_id, $fwd_part);
			
			//extract "content-transfer-encoding field
			$head_a = explode("\n", $fwd_header);
			if (is_array($head_a)){
				while ( list($k,$head_line)=each($head_a) ){
					$head_line = chop($head_line);
					if (strlen($head_line)>15){
						list($head_field,$head_val)=explode(":", $head_line);
						if (strcasecmp($head_field, "content-transfer-encoding")==0){
							$fwd_encoding = trim($head_val);
							echo $head_field.": ".$head_val."<br>\n";
						}
					}
				}
			}
				
			//create file in uploads dir
			$file = $user.".".$file_parts[1].".".$file_parts[2].".".$file_parts[3];
			$a_path = $uploadDir."/".$file;
			$fp = fopen($a_path, "w");
			if ($fp){
				fputs($fp, $fwd_content);
				fclose($fp);
			}else{
				echo "Error when saving fwd att to $a_path <br>\n";
			}
			$fwd_content = "";
				
			echo "Attachment $i is a forward <br>\n";
			$num_parts++;

			//stick it in conent array
			$part[$num_parts]["type"]="Content-Type: ".$a_type."; name=\"".$a_name."\"\r\n";
			$part[$num_parts]["disposition"]="Content-Disposition: attachment; filename=\"".$a_name."\"\r\n";
			if (!empty($fwd_encoding)) $part[$num_parts]["encoding"] = "Content-Transfer-Encoding: $fwd_encoding\r\n";
			$part[$num_parts]["size"] = filesize($a_path);
			$attachment_size += $part[$num_parts]["size"];
			$part[$num_parts]["path"] = $a_path;
			$part[$num_parts]["encoded"] = true;
			
		}
	}
}
?>