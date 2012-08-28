function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Veuillez entrer une valeur pour le champs \"Nom d'utilisateur\"." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Veuillez entrer une valeur pour le champs \"Prénom\"." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Veuillez entrer une valeur pour le champs \"Nom\"." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Veuillez entrer une valeur pour le champs \"Question du Mot de Passe\"." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Entrez s\'il vous plaît au moins 8 caractères dans le champs \"Mot de passe\"." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Mot de passe incorrect." + '');
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
    alert('' + "Entrez s\'il vous plaît seulement des lettres, chiffres et \".-_\" dans le champs \"Nom d'utilisateur\"." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "Vous devez accepter nos conditions générales avant de vous inscrire." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "Le compte que vous essayez de créer existe déjà dans la base de données." + '' + "Choisissez un nom unique, les noms génériques sont d'habitude pris." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

