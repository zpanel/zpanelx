function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Favor digitar um email para o livro de endereços.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Favor digitar um \"Nome\".');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Favor digitar um \"Sobrenome\".');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Favor digitar um email para o livro de endereços.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('No mínimo um usuário ou grupo precisa ter acesso de ler/escrever nesse livro de endereços');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}