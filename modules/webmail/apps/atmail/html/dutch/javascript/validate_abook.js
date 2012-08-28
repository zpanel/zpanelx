function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Gelieve een e-mail adres voor het adresboek in te geven.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Gelieve het veld \"Voornaam\" in te vullen.');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Gelieve het veld \"Familienaam\" in te vullen.');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Gelieve een e-mail adres voor het adresboek in te geven.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Tenminste één gebruiker of groep moet toestemming krijgen om adresboek gegevens te lezen/schrijven.');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}