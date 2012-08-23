function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Please select the destination folder to move the email-messages");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Tem certeza que deseja apagar permenentemente as mensagens selecionadas?") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("Please select the destination folder to move the email-messages");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("Tem certeza que deseja apagar permenentemente as mensagens selecionadas?") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}