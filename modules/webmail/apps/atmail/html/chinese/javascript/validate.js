function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "請輸入值為\“Username\”領域." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "請輸入值為\“First Name\”字段." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "請輸入值為\“\Last Name”字段." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "請輸入值為\“Password Question\”字段." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "請輸入至少8個字符的\“password\”領域." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "密碼不符." + '');
    theForm.password.focus();
    return (false);
  }

  var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
  var checkStr = theForm.username.value;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
    ch = checkStr.charAt(i);
    for (j = 0;  j < checkOK.length;  j++)
      if (ch == checkOK.charAt(j))
        break;
    if (j == checkOK.length)
    {
      allValid = false;
      break;
    }
  }
  if (!allValid)
  {
    alert('' + "請只輸入文字，數字和\“.- _\”字符的\“username\”字段." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "你在申請前必須同意我們的免責條例." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "你正在建立的帳戶已經存在於資料庫中." + '' + "選擇獨特的名稱, 一般名稱通常已被使用." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

