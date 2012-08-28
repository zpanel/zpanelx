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
    alert("Favor digitar somente letras, números, espaços e os caracteres\".-_[]()\" no campo \"Nome da Pasta\".");
    return (false);
  } else if(folder.length > 64) {
    alert("Favor digitar menos de 64 caracteres para o Nome da Pasta");
    return (false);
  } else	 {
	return true;
  }

}