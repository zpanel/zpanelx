function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Gelieve in het veld \'Nieuw paswoord\' minimum 8 karakters in te vullen.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Gelieve in het veld \'Nieuw paswoord\' maximum 64 karakters in te vullen.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Ongeldig paswoord.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Gelieve een \'Paswoord-vraag\' in te geven die u zal helpen u aan uw paswoord te herinneren.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}