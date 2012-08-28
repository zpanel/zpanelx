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
    alert("S'il vous plait, n'entrez que des lettres, des chiffres, des espaces, et les caractères \".-_[]()\" dans le champs du nom de dossier.");
    return (false);
  } else if(folder.length > 64) {
    alert("Veuillez indiquer moins de 64 caractères pour le nom du dossier");
    return (false);
  } else	 {
	return true;
  }

}