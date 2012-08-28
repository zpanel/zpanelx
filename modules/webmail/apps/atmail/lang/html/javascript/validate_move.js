function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("$lang['lang_javascript_validate_move_folder']");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("$lang['lang_javascript_alert_perm']") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("$lang['lang_javascript_validate_move_folder']");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("$lang['lang_javascript_alert_perm']") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}