<?php
// +----------------------------------------------------------------+
// | spellChecker.php												|
// +----------------------------------------------------------------+
// | Function: Wrapper class for PHP pspell functions and aspell  	|
// |           command line utility                                 |
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: March 2006												|
// +----------------------------------------------------------------+



class spellChecker
{

	var $personal_words;
	var $db;
	var $dict;
	var $miss_spelled_words;
	var $aspell_proc;
	var $aspell_input;
	var $aspell_output;
	var $suggestions;

	/**
	 * Constructor
	 *
	 * @param string $dict the dictionary (language) to use
	 * @param string $account the current users account name
	 */
	function spellChecker($dict, $account, $use_pspell=false)
	{
		global $atmail, $pref;

		$this->personal_words = $atmail->db->sqlarray("select Word from SpellCheck where SUnique='0' and Account=?", $account);
		$this->suggestions = array();

		// check for availability of pspell functions
		$this->use_pspell = ($use_pspell && extension_loaded('pspell'));
		//function_exists('pspell_new');

		// use pspell functions if they are available otherwise
		// use the aspell binary directly
		if ($this->use_pspell && function_exists('pspell_new'))
			$this->dict = pspell_new($dict, null, null, 'utf-8', PSPELL_FAST);
		else
		{
		    $dict = escapeshellarg($dict);

			// Execute the aspell command and open file pointers for input/output
			$descriptorspec = array(array("pipe", "r"), array("pipe", "w"));
			$this->aspell_proc = proc_open("{$pref['aspell_path']} -a -l $dict --sug-mode=fast --encoding=utf-8", $descriptorspec, $pipes);
			$this->aspell_input = $pipes[0];
			$this->aspell_output = $pipes[1];

			// remove version info from stream
			$trash = fgets($this->aspell_output);

			unset($trash);
		}

		if (!$atmail->isset_chk($_SESSION['spellcheck_ignore']))
			$_SESSION['spellcheck_ignore'] = array();

	}


	/**
	 * Check a word for spelling errors, add to $this->miss_spelled_words
	 * array if miss spelled
	 *
	 * @param string $word
	 * @return void
	 */
	function check($word)
	{
	    global $atmail;

		$word = preg_replace('/[^a-zA-Z\-]/', '', $word);

		// if the word is in the personal_words array
		// or the ignore list then it is OK. If it is already
		// in the $suggestions array then ignore it
		if (in_array($word, $this->personal_words)  || $atmail->isset_chk($this->suggestions[$word]) || in_array($word, $_SESSION['spellcheck_ignore']))
			return;

		// if word is OK ignore it
		if ($this->use_pspell)
		{
			if (pspell_check($this->dict, $word))
				return;

			$this->suggestions[$word] = pspell_suggest($this->dict, $word);
		}
		else
		{
			fwrite($this->aspell_input, "$word\n");
			$result = fgets($this->aspell_output);

			// remove trash from stream
			$trash = fgets($this->aspell_output);
			unset($trash);

			if (preg_match('/.+?\d:(.+)/', $result, $m))
				$this->suggestions[$word] = explode(', ', $m[1]);
			else
				return;
		}
	}

	/**
	 * Suggest correct spellings for all miss-spelled words
	 *
	 * @return string xml/array $var data representing the suggestions
	 */
	function getSuggestions()
	{
	    global $atmail;

	    // Keep count of number of results so far
	    // We only want to return 15, otherwise list can get
	    // too long
	    $count = 0;

	    if ($atmail->LoginType == 'simpledisabled' && !isset($_REQUEST['RDF']) && !isset($_REQUEST['XUL']) && !isset($_REQUEST['ajax']))
	    {
	        $var['msgline'] = '';
	        $var['replace'] = '';

	        foreach ($this->suggestions as $word => $suggestions)
	        {
	            if (count($suggestions))
	            {
	                $var['msgline'] .= '<span style="background-color: lightblue">'.$word.'</span> ';
	                foreach ($suggestions as $suggestion)
	                {
	                    $var['replace'] .=
                              "<option value=\"$suggestion\">$suggestion</option>\n";

                        $count++;
                        if ($count == 15)
                            break;
	                }

	            }

	        }
	        return $var;
	    }
	    else
	    {
	        $xml = "<?xml version=\"1.0\"?>\n<SpellChkWords>\n";

	        foreach ($this->suggestions as $word => $suggestions)
	        {
	            if (count($suggestions))
	            {
	                $xml .= "<Suggestion><![CDATA[$word";
	                foreach ($suggestions as $suggestion) {
	                    $xml .= "," . $suggestion;
	                    $count++;
                        if ($count == 15)
                            break;
	                }

	                $xml .= "]]></Suggestion>\n";
	            }
	            else
	                $xml .= "<Suggestion><![CDATA[$word]]></Suggestion>\n";
	        }

	        $xml .= "</SpellChkWords>\n";


	        return $xml;
	    }

	}


	/**
	 * Add a word to the personal word list in the DB
	 *
	 * @param string $account the user account name
	 * @param string $word the word to add
	 * @return void
	 */
	function addWord($account, $word)
	{
		global $atmail;
		$atmail->db->sqldo("INSERT INTO SpellCheck (Account, Word) VALUES(?, ?)", array($account, $word));
	}


	/**
	 * Add a word to the session's ignore list
	 *
	 * @return void
	 */
	function ignoreWord($word)
	{
		$_SESSION['spellcheck_ignore'][] = $word;
	}


	/**
	 * Tell whether we have had any spelling errors
	 *
	 * @return bool
	 */
	function haveErrors()
	{
		if (count($this->suggestions))
			return true;

		return false;
	}


	function close()
	{
		if (is_resource($this->aspell_proc))
		{
			fclose($this->aspell_input);
			fclose($this->aspell_output);
			proc_close($this->aspell_proc);
		}
	}

	function display_html()
	{

	}
}

?>
