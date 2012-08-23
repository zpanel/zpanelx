<?php
/////////////////////////////////////////////////////////
//	
//	include/css.inc
//
//	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE:  include/css.php
	PURPOSE:
		Display CSS
	PRE-CONDITIONS:
		Needs to be included from within HTML <head>
		session_auth.inc
	
********************************************************/
?>
A:link,
A:active,
A:visited{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	text-decoration: none;
	color: <?php echo $linkc?>;
	padding: 1px;
}
A:hover{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $linkc?>;
	padding: 1px;
}
body
{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($font_size) ? "font-size: ".$font_size."px;\n":"") ?>
	background-color: <?php echo $bgc?>;
	color: <?php echo $my_colors["main_text"] ?>;
}
td, input, select, textarea
{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".$my_colors["font_size"]."px;\n":"") ?>
}
h2
{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".($my_colors["font_size"]+6)."px;\n":"") ?>
	font-weight: bold;
	padding: 0px;
}
td.mainContent{
	color: <?php echo $my_colors["main_text"]?>;
	background-color: <?php echo $my_colors["main_bg"]?>;
}
table.dk,tr.dk,td.dk{
	background-color: <?php echo $my_colors["main_head_bg"]?>;
}
table.md,tr.md,td.md{
	background-color: <?php echo $my_colors["main_hilite"]?>;
}
tr.lt,td.lt{
	background-color: <?php echo $my_colors["main_bg"]?>;
}
.bigTitle{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".($my_colors["font_size"]+4)."px;\n":"") ?>
	color: <?php echo $my_colors["main_head_txt"]?>;
	font-weight: bold;
}
.small{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["small_font_size"]) ? "font-size: ".$my_colors["small_font_size"]."px;\n":"") ?>
}
A.tblheader:link, 
A.tblheader:active, 
A.tblheader:visited,
A.tblheader:hover{
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".$my_colors["font_size"]."px;\n":"") ?>
	font-weight: bold;
	color: <?php echo $my_colors["tool_link"]?>;
}
.tblheader{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".$my_colors["font_size"]."px;\n":"") ?>
	font-weight: bold;
	color: <?php echo $my_colors["main_head_txt"]?>;
}
.hilite{
	<?php echo (!empty($hilitec)?"background-color: $hilitec;\n":"")?>;
}
.folderList{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".$my_colors["font_size"]."px;\n":"") ?>
	color: <?php echo $my_colors["folder_link"]?>;
}
.mainHeading{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $my_colors["main_head_txt"]?>;
}
A.mainHeading:link, 
A.mainHeading:active, 
A.mainHeading:visited,
A.mainHeading:hover{
	color: <?php echo $my_colors["main_head_txt"]?>;
}
.mainHeadingSmall{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $my_colors["main_head_txt"]?>;
	<?php echo (!empty($my_colors["font_size"]) ? "font-size: ".$my_colors["font_size"]."px;\n":"") ?>
}
A.mainHeadingSmall:link,
A.mainHeadingSmall:visited,
A.mainHeadingSmall:active,
A.mainHeadingSmall:hover{
	color: <?php echo $my_colors["main_head_txt"]?>;
	<?php echo (!empty($my_colors["small_font_size"]) ? "font-size: ".$my_colors["small_font_size"]."px;\n":"") ?>
}
.mainLight{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $my_colors["main_light_txt"]?>;
}
.error{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $my_colors["error"]?>;
}
A.mainLight:link, 
A.mainLight:active, 
A.mainLight:visited,
A.mainLight:hover{
	color: <?php echo $my_colors["main_light_txt"]?>;
}
.mainLightSmall{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	color: <?php echo $my_colors["main_light_txt"]?>;
	<?php echo (!empty($my_colors["small_font_size"]) ? "font-size: ".$my_colors["small_font_size"]."px;\n":"") ?>
}
A.mainLightSmall:link,
A.mainLightSmall:visited,
A.mainLightSmall:active,
A.mainLightSmall:hover{
	color: <?php echo $my_colors["main_light_txt"]?>;
	<?php echo (!empty($my_colors["small_font_size"]) ? "font-size: ".$my_colors["small_font_size"]."px;\n":"") ?>
}
A.mainlt{
	color: <?php echo $my_colors["main_link_lt"]?>;
}
A.nae{
	font-weight: bold;
}
A.nac{
	color: #EE9900;
}
.nads{
	color: #444444;
	padding: 6px 30px 6px 30px;
	/*color: <?php echo $my_colors["main_md_text"] ?>;*/
}
.ra{
	text-align: right;
}
span.quotes{
	color: <?php echo $my_colors['quotes'] ?>;
}
A.rmnav:link,
A.rmnav:active,
A.rmnav:visited,
A.rmnav:hover{
	color: #FFAA00;
}
td.subthread{
	color: <?php echo $my_colors['main_link_lt'] ?>;
}
A.splink{
	color: #AA0000;
}
