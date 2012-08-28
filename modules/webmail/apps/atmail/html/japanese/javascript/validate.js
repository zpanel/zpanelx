function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "\"ユーザー名\" フィールドを記入してください" + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "\"名\" フィールドを記入してください" + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "\"姓\" フィールドを記入してください" + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "\"パスワードヒント\"フィールドを記入してください" + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "\"パスワード\" フィールドには5文字以上記入してください" + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "パスワードが違います" + '');
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
    alert('' + "\"ユーザー名\" フィールドには半角英数字、ハイフン、アンダーバー以外の文字は使用できません" + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "サインアップの前に免責条項に同意していただく必要があります" + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "作成中のアカウントは既にデータベースに存在します。" + '' + "一般的な名前は既に使用されていることが多くあります。" + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

