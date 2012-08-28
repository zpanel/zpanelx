function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("$lang['lang_javascript_validate_password_minpasswd']");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("$lang['lang_javascript_validate_password_maxpasswd']");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("$lang['lang_javascript_validate_password_passwords']");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("$lang['lang_javascript_validate_password_question']");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}