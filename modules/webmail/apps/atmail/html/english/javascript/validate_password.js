function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("Please enter at least 8 characters in the \'New Password\' field.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("Please enter a maximum of 64 characters in the \'New Password\' field.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("Passwords Mismatch.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("Please enter some sort of \'Password Question\' to help you remember your password when you forget it.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}