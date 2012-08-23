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
if (!$raw_css){
?>
		<STYLE type="text/css">
			<!--
<?php
}
$font_family = $my_colors["font_family"];
?>
				A:link,
				A:active,
				A:visited{
					text-decoration: none;
					color: <?php echo $linkc?>;
					padding: 1px;
				}
				A:hover{
					color: <?php echo $link?>;
					padding: 1px;
				}
				body
				{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo $font_size?>px;
					background-color: <?php echo $bgc?>;
				}
				td, input, select, textarea
				{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo $my_colors["font_size"]?>px;
				}
				h2
				{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["font_size"]+6)?>px;
					font-weight: bold;
					padding: 0px;
				}
				td.mainContent{
					font-color: <?php echo $my_colors["main_text"]?>;
					background-color: <?php echo $my_colors["main_bg"]?>;
				}
				.bigTitle{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["font_size"]+4)?>px;
					color: <?php echo $my_colors["main_head_txt"]?>;
					font-weight: bold;
				}
				.small{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo $my_colors["small_font_size"]?>px;
				}
				A.tblheader:link, 
				A.tblheader:active, 
				A.tblheader:visited,
				A.tblheader:hover{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["font_size"])?>px;
					font-weight: bold;
					color: <?php echo $my_colors["tool_link"]?>;
				}
				.tblheader{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["font_size"])?>px;
					font-weight: bold;
					color: <?php echo $my_colors["tool_link"]?>;
				}
				.hilite{
					background-color: <?php echo $hilitec?>;
				}
				.menuText{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["menu_font_size"])?>px;
					color: <?php echo $my_colors["tool_link"]?>;
					font-weight: bold;
				}
				.folderList{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					font-size: <?php echo ($my_colors["font_size"])?>px;
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
					text-decoration: none;
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_head_txt"]?>;
				}
				.mainHeadingSmall{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_head_txt"]?>;
					font-size: <?php echo $my_colors["small_font_size"]?>;
				}
				A.mainHeadingSmall:link,
				A.mainHeadingSmall:visited,
				A.mainHeadingSmall:active,
				A.mainHeadingSmall:hover{
					text-decoration: none;
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_head_txt"]?>;
					font-size: <?php echo $my_colors["small_font_size"]?>;
				}
				.mainLight{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_light_txt"]?>;
				}
				A.mainLight:link, 
				A.mainLight:active, 
				A.mainLight:visited,
				A.mainLight:hover{
					text-decoration: none;
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_light_txt"]?>;
				}
				.mainLightSmall{
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_light_txt"]?>;
					font-size: <?php echo $my_colors["small_font_size"]?>;
				}
				A.mainLightSmall:link,
				A.mainLightSmall:visited,
				A.mainLightSmall:active,
				A.mainLightSmall:hover{
					text-decoration: none;
					<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
					color: <?php echo $my_colors["main_light_txt"]?>;
					font-size: <?php echo $my_colors["small_font_size"]?>;
				}
<?php
if (!$raw_css){
?>
			//-->
		</STYLE>
<?php
}
?>