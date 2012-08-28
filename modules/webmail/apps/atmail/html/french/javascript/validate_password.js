function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Entrez au moins à 8 caractères dans le champs \'Nouveau mot de passe\'.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Entrez au maximum 64 caractères dans le champs \'Nouveau mot de passe\'.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Mot de passe incorrect.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Entrez une question pour vous souvenir de votre mot de passe si vous l\'oubliez.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}