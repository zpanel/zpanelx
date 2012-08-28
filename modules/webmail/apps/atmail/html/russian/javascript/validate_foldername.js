function validate_folder(folder)	 {

  return true;

  var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_. []()";
  var checkStr = folder;
  var allValid = true;
  for (i = 0;  i < checkStr.length;  i++)
  {
    ch = checkStr.charAt(i);
    for (j = 0;  j < checkOK.length;  j++)
      if (ch == checkOK.charAt(j))
        break;
    if (j == checkOK.length)
    {
      allValid = false;
      break;
    }
  }

  if (!allValid)
  {
    alert("Вводите только буквы, цифры, пробел и \".-_[]()\" в поле \"НазваниеПапки\".");
    return (false);
  } else if(folder.length > 64) {
    alert("Укажите название Папки");
    return (false);
  } else	 {
	return true;
  }

}