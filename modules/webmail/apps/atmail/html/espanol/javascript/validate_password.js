function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Introduce al menos 8 caracteres en el campo \"contraseña nueva\".");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Introduce un maximo de 64 caracteres en el campo \"contraseña\".");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Las contraseña no coinciden.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Introduce una \'Pregunta de contraseña\' para recordarte la contraseña en caso de que sea olvidada o perdida.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}