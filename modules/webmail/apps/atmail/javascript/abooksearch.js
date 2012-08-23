function submit_search(framename, id)	{

if(!id)
	id = '';

var searchtype = document.abook.PermissionsSearchField.value;
var searchfield = document.abook.PermissionsSearchQuery.value;
var abookview;


try
{
abookview = document.abook.abookview.options[document.abook.abookview.selectedIndex].value
}
catch (e)
{
abookview = 'global';
}

GroupsUsersFrame.location.href='abook.php?func=permissionsearch&frames=' + framename + '&PermissionsSearchField=' + searchtype + '&PermissionsSearchQuery=' + escape(searchfield) + '&abookview=' + abookview + '&id=' + id;
}

// Submit a search for the results
function submit_searchperms(frame)	{

if(!frame)
frame = 'GroupUsersFrame'

var searchtype = document.abook.PermissionsSearchField.value;
var searchfield = document.abook.PermissionsSearchQuery.value;

eval(frame + ".location.href='abook.php?func=permissionsearch&frames=Write,Read&PermissionsSearchField=' + searchtype + '&PermissionsSearchQuery=' + searchfield");

}

function changeview(framename, id)	{

if(!id)
	id = '';

var searchtype = 'UserFirstName';
var searchfield = "";
var abookview = document.abook.abookview.options[document.abook.abookview.selectedIndex].value

GroupsUsersFrame.location.href='abook.php?func=permissionsearch&frames=' + framename + '&abookview=' + abookview + "&id=" + id;
}
