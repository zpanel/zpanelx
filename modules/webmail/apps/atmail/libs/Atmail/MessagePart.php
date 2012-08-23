<?php
// +----------------------------------------------------------------+
// | MessagePart.php												|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: September 2005											|
// +----------------------------------------------------------------+


class Message_Part
{
	var $content_type;
	var $content_disposition;
	var $disposition_filename;
	var $filename;
	var $body;
	var $content_id;
	var $headers;
	var $path;

	function Message_Part($vars)
	{

		settype($vars, 'array');

		foreach ($vars as $key => $val)
		{
			$key = strtolower(str_replace(array('-', ':'), array('_', ''), $key));

			if (is_array($val))
			{
			    if ($key == 'headers')
			    {
			        foreach ($val as $k => $v)
    				{
    					if (empty($k)) continue;
    					$k = strtolower(str_replace(':', '', $k));
    					$this->headers[$k] = $v;
    				}
			    }

				foreach ($val as $k => $v)
				{
					if (empty($k) || !isset($k)) continue;
					$k = strtolower(str_replace(array('-', ':'), array('_', ''), $k));
					if($k)
					$this->$k = $v;
				}

				continue;
			}

			$this->$key = $val;

			if (!$this->ctype_primary && $this->content_type)
			{
				list($this->ctype_primary, $this->ctype_secondary) = explode('/', $this->content_type);
				$this->ctype_secondary = preg_replace('/;.*$/', '', $this->ctype_secondary);
			}
		}
	}

	function get_mime_type()
	{
		return $this->content_type;
	}

	function get_mime_ctypes()
	{
		return array($this->ctype_primary, $this->ctype_secondary);
	}

	function get_content_disposition()
	{
		return $this->content_disposition;
	}

	function get_filename()
	{
	    /*
		$filename = $this->filename ? $this->filename : $this->disposition_filename;

		if (!$filename)
		 	$filename = $this->content_name;


		// Another try
		if (!$filename && preg_match('/name=\s*(.+)/i', $this->headers['content-type'], $m)) {
			$filename = $m[1];
		}
		*/
        if (!empty($this->headers['content-disposition'])) {
	        if (preg_match('/filename=\s*([^;]+)/i', $this->headers['content-disposition'], $m)) {
	            $filename = $m[1];
	        }
	    }

		// try to get filename from Content-Type if missing from Content-Disposition
	    if (empty($filename) && !empty($this->headers['content-type'])) {
	        if (preg_match('/name=\s*([^;]+)/i', $this->headers['content-type'], $m)) {
	            $filename = $m[1];
	        }
	    }

		// If part is a message forwarded as an attachment (message/rfc822)
		// Do some extra checks
		if (strpos(strtolower($this->content_type), 'message/rfc822') !== false) {
    		if (empty($filename)) {

    		    //$filename = 'Forwarded_Message.txt';
                if (preg_match('/^Subject:(.+?)$/m', $this->body, $m)) {

                    if (preg_match('/\s*=\?([^\?]+)\?([QqBb])/', $m[1], $match)) {

                        $match[2] = strtoupper($match[2]);
                        if ($match[2] == 'Q') {
                            $m[1] = preg_replace('/\s*=\?([^\?]+)\?[Qq]\?([^\?]+)?\?=/e', "GetMail::decode_language('\\1', GetMail::decode_mime_head('\\1', '\\2'))", $m[1]);
                        } else {
                            $m[1] = preg_replace('/\s*=\?([^\?]+)\?[Bb]\?([^\?]+)?\?=/e', "GetMail::decode_language('\\1', base64_decode('\\2'))", $m[1]);
                        }

                        $filename = $m[1];

                    } else {
                        $filename = GetMail::decode_language($this->get_charset, $m[1]);
                    }

                    if (substr($filename, -4) != '.eml') {
                        $filename .= '.eml';
                    }

                } else {
                    $filename = 'Fowarded Message-'. rand(10000, 99999) . '.eml';
                }
            } else {
            	$filename = trim($filename);
				$filename = preg_replace('/(^")|(";?$)/', '', $filename);
				if (strtolower(substr($filename, -4)) != '.eml') {
                    $filename .= '.eml';
				}
                return $filename;
            }
		}

		// Some clients break up long filenames into two parts ie filename*0="" and filename*1=""
		// lets see if this is the case and join them together.
		if (empty($filename)) {
		    if (false !== $pos = strpos($this->headers['content-disposition'], 'filename')) {
		        $parts = preg_split('/filename\*\d+\*?=/', substr($this->headers['content-disposition'], $pos));
		        foreach ($parts as $part) {
		            $filename .= trim(preg_replace('/; .*/', '', $part), ' "');
		        }
		    }
		}
		
				// Some clients break up long filenames into two parts ie filename*0="" and filename*1=""
		// lets see if this is the case and join them together.
		if (empty($filename)) {
		    if (false !== $pos = strpos($this->headers['content-type'], 'name')) {
		        $parts = preg_split('/name\*\d+\*?=/', substr($this->headers['content-type'], $pos));
		        foreach ($parts as $part) {
		            $filename .= trim(preg_replace('/; .*/', '', $part), ' "');
		        }
		    }
		}
		
		/*
		if (empty($filename)) {
		    if (preg_match_all('/filename\*\d+\*?=(.+?)(;|filename)/i', $this->headers['content-disposition'], $m, PREG_PATTERN_ORDER)) {
		        var_dump($m);
		        $filename = '';
		        foreach($m[1] as $part) {
		            $filename .= trim($part, '";');    
		        }
		    }
		}

		// Some clients break up long filenames into two parts ie filename*0="" and filename*1=""
		// lets see if this is the case and join them together.
		if (empty($filename)) {
		    if (preg_match_all('/filename\*\d+\*?=(.+?)(;|$)/i', $this->headers['content-type'], $m, PREG_PATTERN_ORDER)) {
		        $filename = '';
		        foreach($m[1] as $part) {
		            $filename .= trim($part, '";');    
		        }		    
		    }
		}
        */
        // If no filename is found but the part has a Content-ID header use that as the name
		if (empty($filename)) {
		    if (isset($this->headers['content-id']) && !empty($this->headers['content-id'])) {
		        $filename = $this->headers['content-id'];
		    } else {
                return '';
		    }
		}

		// clean up filename
		$filename = trim($filename);
		$filename = preg_replace('/(^")|(";?$)/', '', $filename);
		return $filename;
	}

	function &get_body()
	{
		return $this->body;
	}

	function get_disposition()
	{
		return $this->content_disposition;
	}

	function get_contentid()
	{
		return $this->content_id;
	}

	function get_header_field($field)
	{
		$field = strtolower($field);
		if ($this->headers[$field])
			return $this->headers[$field];

		$field = str_replace('-', '_', $field);
		return $this->$field;
	}

	function get_attribute($attrib)
	{
		return $this->$attrib;

	}

	function is_attachment()
    {
        if (strpos(strtolower($this->headers['content-type']), 'name=') !== false)
            return true;

        if (!empty($this->content_disposition) && strtolower($this->content_disposition) != 'inline')
            return true;

        if (isset($this->headers['content-disposition']) && strpos(strtolower($this->headers['content-disposition']), 'filename='))
            return true;

        if ($this->disposition)
            return true;

        if (preg_match('/image\//i', $this->content_type))
                return true;

        return false;
    }

    function is_image()
    {
        if ($this->ctype_primary == 'image') {
            return true;
        }

        if ($this->ctype_secondary == 'octet-stream' && preg_match('/\.([^.]+)$/', $this->get_filename(), $m)) {
            return in_array(strtolower($m[1]), array('jpg', 'jpeg', 'gif', 'png'));
        }

        return false;
    }

	function set_path($path)
	{
		$this->path = $path;
	}

	function get_path()
	{
		return $this->path;
	}


	function get_charset()
	{
	    if (is_string($this->content_type)) {
    		if (preg_match('/charset="(.*)"/i', $this->content_type, $match)) {
    			$result = $match[1];
    		} else if(preg_match('/charset=(.*?);/i', $this->content_type, $match)) {
	    			$result = $match[1];
			} else {
    			preg_match('/charset=\s*(.*)\s*/i', $this->content_type, $match);
    			$result = $match[1];
    		}

            return $result;
	    }

	    return '';
	}


    function parent_mime_type()
    {
        if(preg_match('/([a-z0-9]+\/[a-z0-9]+)/i', $this->parent_ctype, $m)) {
            return $m[1];
        }

        return '';
    }
}

?>
