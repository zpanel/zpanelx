function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Gelieve het veld \"Gebruikersnaam\" in te vullen." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Gelieve het veld \"Voornaam\" in te vullen." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Gelieve het veld \"Familienaam\" in te vullen." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Gelieve het veld \"Paswoord-vraag\" in te vullen." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Gelieve tenminste 5 karakters in het veld \"Paswoord\" in te vullen." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Ongeldig paswoord." + '');
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
    alert('' + "Gelieve in het veld \"Gebruikersnaam\" enkel letters, cijfers en/of \".-_\" te gebruiken." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "Vooraleer u te kunnen inschrijven, dient u akkoord te gaan met onze voorwaarden." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "De account die u wenst aan te maken bestaat al in onze database." + '' + "Kies een unieke naam; algemene naam zijn vaak al in beslag genomen." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

