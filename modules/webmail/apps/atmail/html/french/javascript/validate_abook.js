function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('S\'il vous plait, entrez une adresse email pour l\'entrée du carnet d\'adresses.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Veuillez entrer une valeur pour le champs \"Prénom\".');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Veuillez entrer une valeur pour le champs \"Nom\".');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('S\'il vous plait, entrez une adresse email pour l\'entrée du carnet d\'adresses.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Au moins un utilisateur ou un groupe doit avoir accès en lecture/écriture à la ligne du carnet d\'adresses');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}