function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Ti preghiamo di compilare il campo \"Username\"." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Ti preghiamo di compilare il campo \"Nome\"." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Ti preghiamo di compilare il campo \"Cognome\"." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Ti preghiamo di compilare il campo \"Domanda Password\"." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Ti preghiamo di inserire almeno 5 caratteri nel campo \"password\"." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Password Errata." + '');
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
    alert('' + "Ti preghiamo di inserire nel campo \"username\" solo lettere, cifre e segni \".-_\" ." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "Devi accettare le nostre condizioni contrattuali prima di confermare." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "L'account che stai creando esiste già nel nostro database." + '' + "Scegli un nome di una sola parola e non comune, i nomi più comuni sono già utilizzati." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

