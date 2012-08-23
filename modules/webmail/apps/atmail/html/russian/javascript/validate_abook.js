function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('Пожалуйста, введите адрес для контакта.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('Введите значение в поле \"Имя\".');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('Введите значение в поле \"Фамилия\".');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('Пожалуйста, введите адрес для контакта.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('Только один пользователь или группа имеют права на запись\чтение записей адресной книги');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}