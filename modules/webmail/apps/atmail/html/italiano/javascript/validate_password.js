function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Ti preghiamo di inserire almeno 5 caratteri nel campo \'Nuova Password\'.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Ti preghiamo di inserire nel campo  \'Nuova Password\' non più di 64 caratteri.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Password Errata.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Ti preghiamo di inserire una \'Domanda Segreta\'  che ti aiuti a ricordare la password nel caso tu la dimentichi.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}