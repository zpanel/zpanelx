<?php

//always include flags from default
include('themes/default/flags_main.php');

function theme_form_link($type, $content, $url='', $target=''){
	/*
	theme_form_link
		Description: Form link HTML tag, and return. 
		Parameters:
			$type	- A flag defined in themes/default/flags_rm.php
			$content- Text content of link
			$url 	- URL to link to.  [optional]
			$target	- Link target [optional]
		Output: An <a> tag as string
	*/
	switch($type){
		case THEME_MAIN_NEW:
		case THEME_MAIN_DELETE:
		case THEME_MAIN_APPLY:
		case THEME_MAIN_FILTERS:
			$str = $content;
			$class = 'rmnav';
			break;
		case THEME_MAIN_NEXT:
			$str = $content.'&gt;&nbsp;';
			$class = 'rmnav';
			break;
		case THEME_MAIN_PREVIOUS:
			$str = '&lt; '.$content;
			$class = 'rmnav';
			break;
		case THEME_MAIN_DIVIDER:
			$str = '&nbsp;|&nbsp;';
			$class = 'mainLight';
			break;
		case THEME_MAIN_FOLDER:
			$str = '<span class="bigTitle">'.$content.'</span>&nbsp;';
			break;
		default:
			$str = $content;
			$class = '';
	}
	
	$out = $pre;
	if ($url)
		$out.='<a href="'.$url.'" target="'.$target.'" '.($class?'class="'.$class.'"':'').'>';
	$out.=$str.($url?'</a>':'').$post;
	return $out;
}

?>