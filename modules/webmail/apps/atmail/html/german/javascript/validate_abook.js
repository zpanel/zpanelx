function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Bitte geben Sie eine E-Mail Adresse für den Adressbuch Eintrag ein.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Bitte geben Sie einen Wert ins das Feld \"Vorname\" ein.');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Bitte geben Sie einen Wert ins das Feld \"Nachname\" ein.');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Bitte geben Sie eine E-Mail Adresse für den Adressbuch Eintrag ein.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Mindestens ein(e) Benutzer/Gruppe muss schreib/lese Zugriff auf den Adressbucheintrag haben.');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}