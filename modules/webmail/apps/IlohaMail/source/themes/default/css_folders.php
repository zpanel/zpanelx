<?php
$linkc = $my_colors["folder_link"];
$bgc = $my_colors["folder_bg"];
$textc = $my_colors["folder_link"];
$font_size = $my_colors["folderlist_font_size"];
$font_family = $my_colors["font_family"];
include('themes/default/css_base.php');
?>
span.spacer{
	font-size: <?php echo $font_size ?>px;
	color: <?php echo $bgc ?>;
}
body.folders{
	font-size: <?php echo $font_size ?>px;
	background-color: <?php echo $bgc ?>;
	color: <?php echo $linkc ?>;
}
