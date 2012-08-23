<?php

/*
	Default colors.  Used for login page, however,
	may be changed by user for customized looks.
*/
$default_colors=array(
	"tool_bg"=>"#222244",
	"tool_link"=>"#FFFFFF",
	"folder_bg"=>"#222244",
	"folder_link"=>"#FFFFFF",
	"main_bg"=>"#f0f0f0",
	"main_hilite"=>"#b1b1b9",
	"main_text"=>"#000000",
	"main_link"=>"#222222",
	"main_head_bg" => "#222244",
	"main_head_txt" => "#FFFFFF",
	"main_darkbg" => "#444466",
	"main_light_txt" => "#FFFFFF",
	"quotes"=>"blue",
	"font_family"=>"Verdana, Arial, Helvetica, sans-serif",
	"font_size"=>"12",
	"small_font_size"=>"10",
	"menu_font_size"=>"12",
	"folderlist_font_size"=>"12"
);
$init["my_colors"] = $default_colors;


/*
	Default prefernce values.  Note that
	these values may be changed by the user
	through the prefs panel
*/
$default_prefs=array(
	"colorize_quotes"=>1,
    "detect_links"=>1,
	"view_max"=>15,
	"show_size"=>1,
	"save_sent"=>0,
	"sent_box_name"=>"",
	"delete_trash"=>0,
	"trash_name"=>"",
	"rootdir"=>$rootdir,
	"user_name"=>"",
	"email_address"=>"",
	"signature1"=>"",
	"show_sig1"=>0,
	"lang"=>$lang,
	"charset"=>$lang_charset,
	"sort_field"=>"DATE",
	"sort_order"=>"DESC",
	"list_folders"=>1,
	"view_inside"=>1,
	"timezone"=>0,
	"html_in_frame"=>0,
	"show_images_inline"=>0,
	"showContacts"=>0,
	"showCC"=>1,
	"closeAfterSend"=>1,
	"showNav"=>1,
	"folderlistWidth"=>150,
	"hideUnsubscribed"=>0,
	"compose_inside"=>"",
	"show_quota"=>"",
	"showNumUnread"=>"",
	"refresh_folderlist"=>"",
	"folderlist_interval"=>"150",
	"radar_interval"=>"150",
	"main_toolbar"=>"b",
	"alt_identities"=>"",
	"main_cols" => "csfdzam",
	"clock_system"=>12,
	"nav_no_flag"=>0,
	"theme"=>"default"
);
$init["my_prefs"] = $default_prefs;


/*
	init_from_address
	Used to generate "From" and/or "Sender" header addresses,
	if host is different from domain.  If host is mail.domain.com
	but mail should be addressed to @domain.com, set this value
	to "%u@domains.com".  Default is blank, which is the same as
	"%u@%h".
*/
$init_from_address = ""; //for "user@host"

?>