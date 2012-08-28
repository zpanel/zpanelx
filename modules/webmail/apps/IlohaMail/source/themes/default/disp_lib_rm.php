<?php

//always include flags from default
include('themes/default/flags_rm.php');

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
		case THEME_RM_PREV_LINK:
			$str = '&lt; '.$content;
			$class = 'rmnav'; 
			break;
		case THEME_RM_NEXT_LINK:
			$str = $content.' &gt;'; 
			$class = 'rmnav'; 
			break;
		case THEME_RM_BACK_LINK:
			$str = $content;
			$class = 'rmnav';
			break;
		case THEME_RM_TH_FIRST_LINK:
			$str = '<b>|&lt;-</b>'; 
			$class = 'rmnav'; 
			break;
		case THEME_RM_TH_PREV_LINK:
			$str = '&nbsp;<b>&lt;-</b>'; 
			$class = 'rmnav'; 
			break;
		case THEME_RM_TH_NEXT_LINK:
			$str = '<b>-&gt;</b>&nbsp;'; 
			$class = 'rmnav'; 
			break;
		case THEME_RM_TH_LAST_LINK:
			$str = '<b>-&gt;|</b>'; 
			$class = 'rmnav'; 
			break;
		case THEME_RM_REPLY_LINK:
		case THEME_RM_REPLYALL_LINK:
		case THEME_RM_FORWARD_LINK:
		case THEME_RM_DELETE_LINK:
		case THEME_RM_UNDELETE_LINK:
		case THEME_RM_READ_LINK:
		case THEME_RM_UNREAD_LINK:
			$str = '<b>'.$content.'</b>';
			$class = 'tcnt';
			break;
		case THEME_RM_SOURCE_LINK:
		case THEME_RM_HEADER_LINK:
		case THEME_RM_PRINTABLE_LINK:
			$str = $content;
			$class = '';
			break;
		case THEME_RM_DIVIDER:
			$str = ' | ';
			break;
		case THEME_RM_FOLDER:
			$str = '<b>'.$content.'</b>';
			$class = 'mainLight';
			break;
		default:
			$str = $content;
			$class = '';
	}
	
	$out = '';
	if ($url)
		$out.='<a href="'.$url.'" target="'.$target.'" '.($class?'class="'.$class.'"':'').'>';
	$out.=$str.($url?'</a>':'');
	return $out;
}

?>