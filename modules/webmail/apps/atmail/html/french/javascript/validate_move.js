function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Veuillez choisir un dossier de destination afin de déplacer le message.");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Etes-vous certain de détruire de manière permanente les messages sélectionnés ?") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Veuillez choisir un dossier de destination afin de déplacer le message.");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Etes-vous certain de détruire de manière permanente les messages sélectionnés ?") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}