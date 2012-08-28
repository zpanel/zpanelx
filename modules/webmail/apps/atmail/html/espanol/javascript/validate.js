function validate_form(theForm)
{

  if (theForm.username.value == "")
  {
    alert('' + "Introduce un valor para el campo \"Nombre de usuario\"." + '');
    theForm.username.focus();
    return (false);
  }

  if (theForm.FirstName.value == "")
  {
    alert('' + "Introduce un valor para el campo \"Nombre\"." + '');
    theForm.FirstName.focus();
    return (false);
  }

  if (theForm.LastName.value == "")
  {
    alert('' + "Introduce un valor para el campo \"Apellidos\"." + '');
    theForm.LastName.focus();
    return (false);
  }

  if (theForm.PasswordQuestion.value == "")
  {
    alert('' + "Introduce un valor para el campo \"Pregunta de contraseña\"." + '');
    theForm.PasswordQuestion.focus();
    return (false);
  }

  if (theForm.password.value.length < 8)
  {
    alert('' + "Introduce al menos 8 caracteres en el campo \"contraseña\"." + '');
    theForm.password.focus();
    return (false);
  }

  if (theForm.password.value != theForm.password2.value)
  {
    alert('' + "Las contraseña no coinciden." + '');
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
    alert('' + "Introduce solo letras, números y caracteres \".-_\" en el campo \"Nombre de usuario\" ¡." + '');
    theForm.username.focus();
    return (false);
  }

  if (!theForm.disclaimer.checked)
  {
    alert('' + "Has de aceptar la licencia antes de crear una Cuenta." + '');
    theForm.disclaimer.focus();
    return (false);
  }

if( checkGroup('index.php?func=checkuser&Account=' + theForm.username.value + '@' + theForm.pop3host.options[theForm.pop3host.selectedIndex].value ) )	{
	alert('' + "La Cuenta de Usuario que has solicitado, ya existe en la Base de Datos." + '' + "Selecciona un Nombre de Usuario único." + '');
    theForm.username.focus();
	theForm.username.select();
	return (false);
  }


  return (true);
}

