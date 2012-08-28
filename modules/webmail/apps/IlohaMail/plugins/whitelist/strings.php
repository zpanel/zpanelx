<?php

function wblstr($key){
	global $wlstr, $my_prefs;
	
	$lang = $my_prefs['lang'];
	if (empty($lang)) $lang='eng/';
	
	if (isset($wlstr[$lang][$key])) return $wlstr[$lang][$key];
	else return $wlstr['eng/'][$key];
}

$engstr = array();
$engstr['whitelist'] = 'Whitelist';
$engstr['enable'] = 'Enable';
$engstr['disable'] = 'Disable';
$engstr['emailad'] = 'Email Address';
$engstr['curon'] = 'Currently enabled';
$engstr['curoff'] = 'Currently disabled';
$engstr['add'] = 'Add';
$engstr['remove'] = 'Remove';
$engstr['emptywl'] = 'You have no addresses in your whitelist (addresses in your contacts list are automatically included).';
$engstr['wlaction'] = 'Whitelist Action';
$engstr['moveto1'] = 'Move to';
$engstr['moveto2'] = '';
$engstr['flag'] = 'Flag as important';
$engstr['update'] = 'Update Action';
$engstr['blacklist'] = 'Blacklist';
$engstr['emptybl'] = 'You have no addresses in your blacklist.';
$engstr['blaction'] = 'Blacklist Action';
$engstr['delete'] = 'Delete';
$wlstr['eng/'] = $engstr;


$jpstr = array();
$jpstr['whitelist'] = '&#12507;&#12527;&#12452;&#12488;&#12522;&#12473;&#12488;';
$jpstr['enable'] = '&#20351;&#29992;&#12377;&#12427;';
$jpstr['disable'] = '&#20351;&#29992;&#12375;&#12394;&#12356;';
$jpstr['emailad'] = '&#12513;&#12540;&#12523;&#12450;&#12489;&#12524;&#12473;';
$jpstr['curon'] = '&#29694;&#22312;&#20351;&#29992;&#20013;';
$jpstr['curoff'] = '&#29694;&#22312;&#26410;&#20351;&#29992;';
$jpstr['add'] = '&#36861;&#21152;';
$jpstr['remove'] = '&#21066;&#38500;';
$jpstr['emptywl'] = '&#29694;&#22312;&#12507;&#12527;&#12452;&#12488;&#12522;&#12473;&#12488;&#12395;&#12450;&#12489;&#12524;&#12473;&#12399;&#30331;&#37682;&#12373;&#12428;&#12390;&#12356;&#12414;&#12379;&#12435;&#12290;&#65288;&#12450;&#12489;&#12524;&#12473;&#24115;&#20869;&#12398;&#12513;&#12540;&#12523;&#12450;&#12489;&#12524;&#12473;&#12399;&#33258;&#21205;&#30340;&#12395;&#12507;&#12527;&#12452;&#12488;&#12522;&#12473;&#12488;&#12395;&#21547;&#12414;&#12428;&#12414;&#12377;&#12290;&#65289;';
$jpstr['wlaction'] = '&#12450;&#12463;&#12471;&#12519;&#12531;';
$jpstr['moveto1'] = '&#35442;&#24403;&#38917;&#30446;&#12434;';
$jpstr['moveto2'] = '&#12395;&#31227;&#21205;';
$jpstr['flag'] = '&#12300;&#37325;&#35201;&#12301;&#12501;&#12521;&#12464;';
$jpstr['update'] = '&#26356;&#26032;';
$jpstr['blacklist'] = '&#12502;&#12521;&#12483;&#12463;&#12522;&#12473;&#12488;';
$jpstr['emptybl'] = '&#29694;&#22312;&#12502;&#12521;&#12483;&#12463;&#12522;&#12473;&#12488;&#12395;&#12450;&#12489;&#12524;&#12473;&#12399;&#30331;&#37682;&#12373;&#12428;&#12390;&#12356;&#12414;&#12379;&#12435;&#12290;';
$jpstr['blaction'] = '&#12450;&#12463;&#12471;&#12519;&#12531;';
$jpstr['delete'] = '&#35442;&#24403;&#38917;&#30446;&#12434;&#21066;&#38500;';
$wlstr['jp/'] = $jpstr;


$dastr = array();
$dastr['whitelist'] = 'Positivliste';
$dastr['enable'] = 'Sl&aring; til';
$dastr['disable'] = 'Sl&aring; fra';
$dastr['emailad'] = 'Email adresse';
$dastr['curon'] = 'Sl&aring;et til';
$dastr['curoff'] = 'Sl&aring;et fra';
$dastr['add'] = 'Tilf&oslash;j';
$dastr['remove'] = 'Fjern';
$dastr['emptywl'] = 'Din positivliste er tom (dine kontaktpersoner er altid med).';
$dastr['wlaction'] = 'Positiv handling';
$dastr['moveto1'] = 'Flyt til';
$dastr['moveto2'] = '';
$dastr['flag'] = 'Marker som vigtig';
$dastr['update'] = 'Ret handling';
$dastr['blacklist'] = 'Negativliste';
$dastr['emptybl'] = 'Din negativliste er tom.';
$dastr['blaction'] = 'Negativ handling';
$dastr['delete'] = 'Slet';
$wlstr['dk/'] = $dastr;

?>