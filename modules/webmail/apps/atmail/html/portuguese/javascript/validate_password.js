function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Favor digitar no mínimo 5 caracteres no campo \'Nova Senha\'.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("O campo \'Nova Senha\' pode ter no máximo 64 caracteres.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Senha incorreta.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Favor digitar o \"Pergunta Secreta\". Ela serve para lembrá-lo dessa senha se você esquecer.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}