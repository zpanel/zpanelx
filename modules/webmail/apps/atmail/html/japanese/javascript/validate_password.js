function validate_form(theForm)
{


  if (theForm.newpass.value.length < 8)
  {
    alert("\'新パスワード\' フィールドには8文字以上記入してください");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value.length > 64)
  {
    alert("\'新パスワード\' フィールドは64文字以内にしてください");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }

  if (theForm.newpass.value != theForm.newpass2.value)
  {
    alert("パスワードが違います");
	theForm.newpass.value = "";
	theForm.newpass2.value = "";
    theForm.newpass.focus();
    return (false);
  }
 /* 
  if (theForm.PasswordQuestion.value.length < 5)
  {
    alert("パスワードを忘れたり紛失した時のために \'パスワードヒント\' を記入してください");
    theForm.PasswordQuestion.focus();
    return (false);
  }
*/

  return (true);


}