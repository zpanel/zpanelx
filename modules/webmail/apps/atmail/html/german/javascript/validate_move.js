function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Bitte wählen Sie einen Zielordner für das Verschieben der E-Mails aus");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Sind Sie sicher, daß Sie die Nachricht endgültig löschen möchten?") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Bitte wählen Sie einen Zielordner für das Verschieben der E-Mails aus");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Sind Sie sicher, daß Sie die Nachricht endgültig löschen möchten?") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}