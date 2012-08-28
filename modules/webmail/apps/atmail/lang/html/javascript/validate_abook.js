function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('$lang['lang_javascript_validate_email']');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('$lang['lang_javascript_validate_firstname']');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('$lang['lang_javascript_validate_lastname']');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('$lang['lang_javascript_validate_email']');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('$lang['lang_javascript_validate_permissions']');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}