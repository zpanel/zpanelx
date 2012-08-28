function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('請輸入電子郵件地址的通訊錄條目.');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('請輸入值為\“First Name\”字段.');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('請輸入值為\“\Last Name”字段.');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('請輸入電子郵件地址的通訊錄條目.');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('至少有一個用戶或組必須具有讀/寫訪問的地址簿條目');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}