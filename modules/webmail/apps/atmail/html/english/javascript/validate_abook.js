function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Please enter an email address for the address-book entry.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Please enter a value for the \"First Name\" field.');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Please enter a value for the \"Last Name\" field.');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Please enter an email address for the address-book entry.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('At least one user or group must have read/write access on the address-book entry');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}