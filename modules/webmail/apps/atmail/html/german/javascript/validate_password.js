function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Bitte geben Sie min. 5 Zeichen in das Feld \'Neues Passwort\' ein.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Bitte geben Sie höchsten 64 Zeichen in das Feld \'Neues Passwort\' ein.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Passwörter stimmen nicht überein.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Bitte geben Sie eine \'Passwort Frage\' ein um sich später an das Passwort errinern zu können.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}