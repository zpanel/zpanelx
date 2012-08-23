function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Bitte geben Sie einen Wert ins das Feld \"Benutzername\" ein." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Bitte geben Sie einen Wert ins das Feld \"Vorname\" ein." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Bitte geben Sie einen Wert ins das Feld \"Nachname\" ein." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Bitte geben Sie einen Wert ins das Feld \"Passwort Frage\" ein." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Bitte geben Sie mind. 8 Zeichen ins das Feld \"Passwort\" ein." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Die Passwörter stimmen nicht überein." + '');
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
    alert('' + "Bitte geben Sie nur Buchstaben, Zahlen oder \".-_\" Zeichen in das Feld \"Benutzername\" ein." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "Mit der Anmeldung akzeptieren Sie unsere Nutzungsbedienungen." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "Das Postfach, dass Sie erstellen existiert bereits in der Datenbank." + '' + "Bitte einen individuellen Namen wählen, allgemeine Namen sind oft bereits belegt." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

