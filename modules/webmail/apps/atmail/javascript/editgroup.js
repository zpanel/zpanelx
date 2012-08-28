function editgroup(id, abooktype)	{
        var url = "abook.php?func=group&edit=" + id + "&type=" + abooktype; 
		location.href=url;
}

function editgroupxp(id, abooktype)	{
        var url = "abook.php?func=editgroupxp&edit=" + id + "&type=" + abooktype;
        var winAddr = openPopup( url, "Addresbook", 542, 548,0,1,0);
}
