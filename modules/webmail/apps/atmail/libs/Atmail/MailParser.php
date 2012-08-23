<?php
// +----------------------------------------------------------------+
// | MailParser.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: September 2005											|
// +----------------------------------------------------------------+

require_once('header.php');

require_once('MessagePart.php');

class MailParser
{

	var $parser;

	var $use_mailparse;

	var $output_dir;

	var $msg_res;

	var $struct;

	var $parts;

    var $path;

    var $parent_ctype = array();

	/**
	 * Constructor
	 */
	 function MailParser()
	 {
		require_once('mime_parser.php');
		$this->parser = new mime_parser_class;
	 }


	 function parse_open($path)
	 {
	 	$this->path = $path;

		$parameters = array('File' => $path);

		if(!$this->parser->Decode($parameters, $decoded))
		{
			$this->errors[] = 'Email Parsing failed. The selected email cannot be parsed since the email is corrupt. Please ask the sender to send the email again using another program or method. MIME message decoding error: '.$this->parser->error."\n";
			//var_dump($this->parser->error);
			return false;
		}

		for($message = 0; $message < count($decoded); $message++)
		{
			$this->_extract_parts($decoded[$message]);
		}

		$this->headers = $this->_clean_headers($decoded[0]['Headers']);

		$this->dump_attachments();
		
		return true;
	 }


	 function dump_attachments()
	 {
        $numParts = count($this->parts);

	 	for ($i = 0; $i < $numParts; $i++)
	 	{
	 		if ($this->parts[$i]->is_image() || (strpos($this->headers['content-type'], 'html') === false && $this->parts[$i]->is_attachment())
	 		    || strpos(strtolower($this->parts[$i]->content_type), 'message/rfc822') !== false)
	 		{
	 			$contents = $this->parts[$i]->get_body();
	 			$name = $this->parts[$i]->get_filename();

				// Take path out of the filename for security
				$name = basename($name);

				// Add .safe ext so webserver will not execute any uploaded
				// scripts if they are directly requested
				$name .= ".safe";
				
				if ($name)
				{
		 			$fh = fopen("$this->output_dir/$name", 'w');
		 			if (is_resource($fh))
					{
						$this->parts[$i]->set_path("$this->output_dir/$name");
						fwrite($fh, $contents);
		 				fclose($fh);
					}
				}
	 		}
	 	}
	 }


	 function stringify_header()
	 {
	 	$headerstring = '';

	 	if (!is_array($this->headers))
	 	    return $headerstring;

		foreach ($this->headers as $k => $v)
		{
			if (is_array($v))
			{
				foreach($v as $k2 => $v2)
					$headerstring .= "$k: " . htmlentities($v2) . "\n";

				continue;
			}

			//uppercase each word in the field name
			$k = str_replace('-', ' ', $k);
			$k = ucwords($k);
			$k = str_replace(' ', '-', $k);

			$headerstring .= "$k: ". htmlentities($v) . "\n";
		}

	 	return $headerstring;
	 }


     function stringify()
     {
         if (file_exists($this->path))
            return file_get_contents($this->path);
     }


	 function output_dir($dir)
	 {
	 	if (!is_dir($dir))
	 	{
	 		if (!mkdir($dir, 0777))
	 			return false;
	 	}

	 	$this->output_dir = $dir;
	 	return true;
	 }


	 function get_header_field($field)
	 {
		$field = strtolower($field);
	 	return $this->headers[$field];
	 }

	 /**
	  * @return array
	  */
	 function &get_parts()
	 {
	 	return $this->parts;
	 }


	 function &get_body()
	 {
	 	return $this->body;
	 }


	 function recommended_filename($part)
	 {
	 	$name = $part->get_filename();
	 	return $name;
	 }


	function _clean_headers($headers)
	{

		foreach ($headers as $k => $v)
		{
			if (is_array($v))
				$v = $this->_clean_headers($v);

			$k = str_replace(':', '', $k);

			if ($clean[$k])
				$clean[$k] .= " $v";
			else
				$clean[$k] = $v;

		}

		return $clean;
	}


	function _extract_parts($message)
	{
	    if (is_array($message) && isset($message['Parts']) && count($message['Parts'])) {
	        $this->parent_ctype[] = $message['Headers']['content-type:'];
            $parts =& $message['Parts'];
	    }
	    
		if (isset($parts))
		{
			foreach ($parts as $part)
			{
			    $this->parent_ctype[] = $message['Headers']['content-type:'];
				$this->_extract_parts($part);
			}
		}
		else
		{
		    $part = new Message_Part($message);
		    $part->parent_ctype = array_pop($this->parent_ctype);
	        $this->parts[] = $part;
		}
	}


	function get_charset()
	{

	    if (preg_match('/charset="(.+?)"/', $this->headers['content-type'], $m)) {
	        return $m[1];
	    } else if (preg_match('/charset=(.*);/', $this->headers['content-type'], $m)) {
	        return $m[1];
		}
		
	}
}

?>
