function validate_move()
{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("請選擇目標文件夾移動電子郵件訊息");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("你確定要永久刪除選定的郵件？") )	{
return false;
}


return (true);
}

function validate_move_xp()	{

if (!document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value)
{
alert("請選擇目標文件夾移動電子郵件訊息");
return (false);
}

else if(document.mail.NewFolder.options[document.mail.NewFolder.selectedIndex].value == 'erase' && !confirm("你確定要永久刪除選定的郵件？") )	{
return false;
} else	{
top.updatewait();
document.mail.submit();
return true;
}

}