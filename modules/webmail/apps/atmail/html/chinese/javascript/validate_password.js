function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("請輸入至少8個字符的\'New Password\'領域.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("請輸入最多64個字符的\'New Password\'領域.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("密碼不符.");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("請輸入某種\'Password Question\'﹐ 以幫助你忘記時記住你的密碼.");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}