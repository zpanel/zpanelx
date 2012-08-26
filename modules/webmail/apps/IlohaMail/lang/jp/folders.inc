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


	$fl_str["folders"] = "�ե������";
	$fl_str["manage"] = "�ե����������";

	$sentbox=$my_prefs["sent_box_name"];
	$trashname=$my_prefs["trash_name"];
	$defaults["INBOX"]="����Ȣ";
	$defaults[$sentbox]="�����Ѥ�";
	$defaults[$trashname]="����Ȣ";
	
	$fstr["reload"] = "�ƥ�����";
	$fstr["expunge"] = "���ˤ���";
?>