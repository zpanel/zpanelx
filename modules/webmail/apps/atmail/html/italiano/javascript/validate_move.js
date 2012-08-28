function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Specifica la cartella di destinazione per spostare i messaggi e-mail");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Sei sicuro di voler cancellare definitivamente i messaggi selezionati?") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Specifica la cartella di destinazione per spostare i messaggi e-mail");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Sei sicuro di voler cancellare definitivamente i messaggi selezionati?") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}