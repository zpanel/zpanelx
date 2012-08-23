<?php
$linkc = $my_colors["tool_link"];
$bgc = $my_colors["tool_bg"];
$textc = $my_colors["tool_link"];
$font_size = $my_colors["font_size"];
$font_family = $my_colors["font_family"];
include('themes/default/css_base.php');
?>
body.tool{
	margin: 0px 0px 0px 0px;
	background-color: <?php echo $my_colors["tool_bg"] ?>;
	color: <?php echo $my_colors["tool_link"] ?>;
	font-size: <?php echo $my_colors["menu_font_size"] ?>px;
}
.menuText{
	<?php echo (!empty($font_family)?"font-family: ".$font_family.";\n":"")?>
	font-size: <?php echo ($my_colors["menu_font_size"])?>px;
	color: <?php echo $my_colors["tool_link"]?>;
	font-weight: bold;
}
.menuText td{
	vertical-align: bottom;
}
