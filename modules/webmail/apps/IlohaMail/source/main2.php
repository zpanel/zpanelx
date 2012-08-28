<?php
function js_print_val($val, $with_keys=false){
	if (is_numeric($val)) return $val;
	else if ($val=="") return "\"\"";
	else if (is_array($val)) return js_print_array($val, $with_keys);
	else return "\"".addslashes(str_replace("\n", "", $val))."\"";
}

function js_print_array($a, $with_keys=false){
	$first = TRUE;
	if (!is_array($a) || count($a)==0) return "[]";
	$out = ($with_keys?"{":"[");
	
	reset($a);
	while(list($key,$val)=each($a)){
		if ($first) $first=FALSE;
		else $out.=",";
		$out.= ($with_keys?js_print_val($key).":":"");
		$out.=js_print_val($a[$key], $with_keys);
	}
	$out.=($with_keys?"}":"]");
	
	return $out;
}

include('../include/stopwatch.inc');
$clock = new stopwatch(true);
include_once("../include/super2global.inc");
include_once("../include/nocache.inc");
include_once("../include/session_auth.inc");
include_once("../lang/".$my_prefs["charset"].".inc");
include_once("../include/ryosimap.inc");
include_once('../include/ryosdates.inc');
include_once('../include/icl.inc');
include_once('../include/cache.inc');
include_once('../include/main.inc');
include_once('../lang/'.$my_prefs['lang'].'dates.inc');
include('../lang/'.$my_prefs['lang'].'defaultFolders.inc');
include('../lang/'.$my_prefs['lang'].'main.inc');
include('../lang/'.$my_prefs['lang'].'dates.inc');

//CSS
$current_page='main2.php';
include_once('themes/'.$my_prefs['theme'].'/info.inc');
$css_url = $CSS_URLS[$current_page];
if (empty($css_url)) $css_url = $CSS_URLS['default'];
if (empty($css_url)){
	$css_url = 'css.php?user='.$user.'&theme='.$my_prefs["theme"];
	if ($CSS_INCLUDES[$current_page]) $css_url.= '&page='.urlencode($current_page);
}

//theme
$disp_lib_path = 'themes/default/disp_lib_main.php';
if ($DISPLAY_LIB['main']) $disp_lib_path = 'themes/'.$my_prefs['theme'].'/'.$DISPLAY_LIB['main'];
include($disp_lib_path);


//filter stuff
if ($ICL_CAPABILITY["filters"]){
	//fetch filters
	include("../include/filter_engine.inc");
	$clock->register("post-filter");
	
	//put together filter form
	$fltrstr = theme_form_link(THEME_MAIN_FILTERS, $mainStrings[27], 'pref_filters.php?user='.$user);
	$fltrstr.= "\n".'<select name="apply_filter" id="filtermenu">'."\n";
	if (is_array($filters_a) && count($filters_a)>0){
		$fltrstr.= '<option value="">--'."\n";
		$fltrstr.= '<option value="all">'.$mainStrings[28]."\n";
		reset($filters_a);
		while ( list($filter_id, $v) = each($filters_a) ){
			if (!ereg("[d]", $v["flags"]))
				$fltrstr.= "<option value=\"".$filter_id."\">".$v["name"]."\n";	//add one to filter_id so 0 (i.e. false) becomes 1
		}
	}
	$fltrstr.= "</select>\n";
	$fltrstr.= '<input type="button" onClick="doFilter();" value="'.$mainStrings[29].'">';
}

//fetch folders
main_fetch_folderlist($folderlist);
$folderopts = GetRootedFolderOptions($folderlist, $defaults, $my_prefs["rootdir"]);

//init params
if ($is_draft_box){
	$row_params['args'] = 'user='.$user.'&draft=1&folder='.urlencode($folder); 
	$row_params['action'] = 'compose2.php'; 
	$row_params['open_tgt'] = ($my_prefs['compose_inside']?'list2':'_blank');
}else{		
	$row_params['args'] = "user=$user"; 
	$row_params['action'] = 'read_message.php'; 
	$row_params['open_tgt'] = ($my_prefs['view_inside']!=1?"scr".$user.urlencode($folder).$id:"list2");
}
$row_params['next_args'] = $next_args;
$row_params['prev_args'] = $prev_args;
$row_params['main_cols'] = $my_prefs['main_cols'];
$row_params['num_cols'] = strlen($my_prefs['main_cols']);
$row_params['theme'] = $my_prefs['theme'];
$row_params['charset'] = $my_prefs['charset'];
$row_params['user'] = $user;
$row_params['com_tgt'] = ($my_prefs['compose_inside']?'list2':'_blank');
$row_params['hilite_color'] = '#ddddff';

$init_js = '<script>
	var PARAMS='.js_print_array($row_params, true).';
	var USER="'.$user.'";
	var FOLDER="'.$folder.'";
	var SESSKEY=readCookie("IMAIL_SESS_KEY_'.$user.'");
	var XMLURL="main.xml.php?user='.$user.'&folder='.$folder.'&_sesskey="+SESSKEY;
	var COMPOSE_URL="compose2.php?user='.$user.'";
	var EDIT_URL = "edit_contact.php?user='.$user.'";
	var NEXT_ID;
	var START_ID;
	var PREV_ID;
	var UIDS="";
	var FOLDERS='.js_print_array($folderopts,true).';
	//init(window);
	</script>'."\n";


?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $my_prefs['charset'] ?>">
	<link rel="stylesheet" href="<?php echo $css_url ?>" type="text/css">
	<script type="text/javascript" src="main.js"></script>
	<?php echo $init_js ?>
</head>
<body>

<form name="theform" method="post" action="main.php">
<input name="user" value="<?php echo $user ?>" type="hidden">
<input name="folder" value="<?php echo $folder?>" type="hidden">
<input name="post_total_num" value="596" type="hidden">
<input name="js_action" value="" type="hidden">
<input name="next_id" type="hidden">
<input name="start_id" type="hidden">
<input name="prev_id" type="hidden">
<table cellpadding="2" cellspacing="0" width="100%">
	<tbody>
		<tr class="dk">
			<td align="left" valign="bottom">
				<span class="bigTitle" id="foldername">Inbox</span>&nbsp;<span class="mainHeadingSmall">
					<a href="javascript:doRefreshPage()" target="" class="rmnav" id="checknew">Check New</a>&nbsp;|&nbsp;
					<a href="" target="" class="rmnav" id="delall">Delete All</a>&nbsp;|&nbsp;
					<a href="" target="" class="rmnav" id="appfilter">Apply Filter</a>
				</span>
			</td>
			<td class="mainHeadingSmall" align="right" valign="bottom"></td>
		</tr>
	</tbody>
</table>
<table width="100%">
	<tbody>
		<tr>
			<td align="left" valign="bottom"><span class="mainLightSmall" id="numdisplay">Showing 15 of 596&nbsp;</span></td>
			<td align="center"><span class="mainLightSmall"></span></td>
			<td class="mainLightSmall" align="right" valign="bottom">
				<a href="javascript:doNextPrevPage(0)" target="" class="rmnav" style="display:none" id="prevlink">&lt; Previous 15</a>&nbsp;|&nbsp;
				<a href="javascript:doNextPrevPage(1)" target="" class="rmnav" style="display:none" id="nextlink">Next 15&gt;&nbsp;</a>
				<select name="start" class="small" id="pagemenu">
				</select>
				<input value="Show" type="button" onClick="doChangePage()">
			</td>
		</tr>
	</tbody>
</table>

<!-- MAIN LIST //-->
<table class="md" border="0" cellpadding="1" cellspacing="1" width="100%">
<tbody id="msgtool">
	<tr class="dk" id="msgtoolbar">
		<td colspan="6">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
					<tr>
						<td class="mainLightSmall" width="50%">
							<?php echo $fltrstr ?>
						</td>
						<td class="mainLightSmall" align="right" width="50%">
							<span class="mainLightSmall">Quick Search:</span>
							<input name="quick_search_str" value="" size="15" class="small" type="text" id="qsearch">
							<input name="do_quick_search" onClick="doQuickSearch()" value="Select" type="button">
						</td>
					</tr>
					<tr>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	<tr class="dk" id="msgheader">
		<td>
			<script type="text/javascript" language="JavaScript1.2">
			<!-- Make old browsers think this is a comment.
			document.write("<a href=javascript:SelectAllMessages(true) class='tblheader'><b>+</b></a><span class=tblheader>|</span><a href=javascript:SelectAllMessages(false) class=tblheader><b>-</b></a>")
			-->
			</script>
		</td>
		<td>
			<a href="" class="tblheader"><b>Date</b></a>
		</td>
		<td>
			<a href="" class="tblheader"><b>Subject</b></a>
		</td>
		<td>
			<a href="" class="tblheader"><b>From</b></a>
		</td>
		<td>
			<img src="themes/<?php echo $my_prefs['theme']?>/images/att.gif">
		</td>
		<td>
			<img src="themes/<?php echo $my_prefs['theme']?>/images/reply.gif">
		</td>
	</tr>
</tbody>
<tbody id="msglist">
</tbody>
</table>
<input name="displayed_set" value="" type="hidden">
<input name="max_messages" value="21" type="hidden">
<table width="100%">
	<tbody>
		<tr>
			<td>
				<input name="delete_selected" value="Delete" type="button" onClick="doDelete()">
			</td>
			<td>
				<span class="mainLight">Mark as 
				<select name="mark_as" id="flagmenu">
				<option value="">--</option>
				<option value="Read">Read</option>
				<option value="Unread">Unread</option>
				<option value="Flagged">Important</option>
				<option value="Unflagged">Normal</option>
				</select>
				<input name="mark" value="Mark" type="button" onClick="doFlag()">
				</span>
			</td>
			<td align="right">
				<select name="moveto" id="foldermenu">
				</select>
				<input name="move_selected" value="Move" type="button" onClick="doMove()">
			</td>
		</tr>
	</tbody>
</table>
</form>

<a href="javascript:debugClear()" class="mainLight">Clear</a>&nbsp;
<a href="javascript:toggleDebug()" class="mainLight">Debug</a>&nbsp;
<a href="javascript:toggleProfile()" class="mainLight">Profile</a><br>
<div id="debugdiv" class="mainLight"></div>

<script language="JavaScript">
init(window);
if (parent.radar)  parent.radar.location="radar.php?user=1108579624-70114";
</script>
</body></html>