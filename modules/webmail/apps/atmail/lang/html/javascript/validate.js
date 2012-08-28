function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "$lang['lang_javascript_validate_username']" + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "$lang['lang_javascript_validate_firstname']" + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "$lang['lang_javascript_validate_lastname']" + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "$lang['lang_javascript_validate_question']" + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "$lang['lang_javascript_validate_minpasswd']" + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "$lang['lang_javascript_validate_passwords']" + '');
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
    alert('' + "$lang['lang_javascript_validate_characters']" + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "$lang['lang_javascript_validate_disclaimer']" + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "$lang['lang_auth_userexists_msg']" + '' + "$lang['lang_auth_userexists_unique']" + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

