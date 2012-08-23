function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Inserisci un indirizzo e-mail per la rubrica.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Ti preghiamo di compilare il campo \"Nome\".');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Ti preghiamo di compilare il campo \"Cognome\".');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Inserisci un indirizzo e-mail per la rubrica.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Almeno un utente o un gruppo deve avere permessi di lettura e scrittura nella rubrica');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}