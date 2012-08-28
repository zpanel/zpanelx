function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Введите по крайней мере 8 символов в поле \'Новый пароль\'.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Поле \'Новый Пароль\' не может быть более 64 символов.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Пароли не совпадают.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Пожалуйста, введите что-нибудь в поле \'Секретный вопрос\' чтобы вы смогли вспомнить пароль если вдруг забудете его.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}