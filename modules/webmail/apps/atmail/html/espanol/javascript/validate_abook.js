function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Por favor entrar dirección de correo electrónico para ingresar en libreta de direcciones');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Introduce un valor para el campo \"Nombre\".');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Introduce un valor para el campo \"Apellidos\".');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Por favor entrar dirección de correo electrónico para ingresar en libreta de direcciones');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Por lo menos un usuario o grupo debe tener acceso de leer/escribir para la entrada de direcciones a la libreta.');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}