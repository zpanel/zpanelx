function validate_abook_simple()	 {

if(!document.abook.UserEmail.value)	{
alert('アドレス帳にEメールアドレスを入力してください。');
document.abook.UserEmail.focus();
return false;
}

document.abook.submit();

}

function validate_abook()	 {

if(!document.abook.UserFirstName.value)	{
alert('\"名\" フィールドを記入してください');
ChangeTab('0');
document.abook.UserFirstName.focus();
return false;
}

if(!document.abook.UserLastName.value)	{
alert('\"姓\" フィールドを記入してください');
ChangeTab('0');
document.abook.UserLastName.focus();
return false;
}

if(!document.abook.UserEmail.value)	{
alert('アドレス帳にEメールアドレスを入力してください。');
ChangeTab('0');
document.abook.Email.focus();
return false;
}

if(!document.abook.WriteSelectedUsers.value && !document.abook.WriteSelectedGroups.value && ( document.abook.ReadSelectedUsers.value || document.abook.ReadSelectedGroups.value ) )	{

	alert('1ユーザーもしくは1グループに必ずアドレス帳情報の閲覧/書き込み権限を与える必要があります。');
	ChangeTab('4');
	return false;
}

document.body.style.cursor = 'wait';
return true;

}