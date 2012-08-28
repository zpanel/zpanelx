<?php
include("../lang/eng/folders.inc"); // <-- do not delete

/********************************************************
	lang/jp/folders.inc
	(C) 1999-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

	PURPOSE:
		Provide misc strings and HTML code for "source/folders.inc"
		
********************************************************/


	$fl_str["folders"] = "フォルダー";
	$fl_str["manage"] = "フォルダー管理";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"]="受信箱";
	$defaults[$sentbox]="送信済み";
	$defaults[$trashname]="ゴミ箱";
	
	$fstr["reload"] = "再ロード";
	$fstr["expunge"] = "空にする";
?>