<?php
$linkc=$my_colors["main_link"];
$bgc=$my_colors["main_darkbg"];
$textc=$my_colors["main_text"];
$hilitec=$my_colors["main_hilite"];
$font_size = $my_colors["font_size"];
$font_family = $my_colors["font_family"];

include('themes/default/css_base.php');
?>
tr.header{
	background-color: <?php echo $my_colors["main_hilite"] ?>;	
}
tr.toolbar{
	background-color: <?php echo $my_colors['main_bg'] ?>;
}
table.dkbg,tr.dkbg,td.dkbg{
	background-color: <?php echo $my_colors['main_darkbg'] ?>;
}
A.tcnt{
	color: #AA0000;
}