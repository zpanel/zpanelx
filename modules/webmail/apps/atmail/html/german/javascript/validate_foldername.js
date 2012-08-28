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
    alert("Geben Sie bitte nur Buchstaben, Zahlen, Leerzeichen oder die folgenden Sonderzeichen (\".-_[]()\")in das \"FolderName\" Feld ein.");
    return (false);
  } else if(folder.length > 64) {
    alert("Bitte geben Sie weniger als 64 Zeichen für den Ordnernamen an");
    return (false);
  } else	 {
	return true;
  }

}