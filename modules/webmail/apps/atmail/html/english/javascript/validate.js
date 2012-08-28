function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Please enter a value for the \"Username\" field." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Please enter a value for the \"First Name\" field." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Please enter a value for the \"Last Name\" field." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Please enter a value for the \"Password Question\" field." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Please enter at least 8 characters in the \"password\" field." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Passwords Mismatch." + '');
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
    alert('' + "Please enter only letter, digit and \".-_\" characters in the \"username\" field." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "You have to agree to our Disclaimer before signing up." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "The account you are creating already exists in the database." + '' + "Choose a unique name, generic names are usually taken." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

