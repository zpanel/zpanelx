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
    alert("Please enter only letter, digit , spaces and \".-_[]()\" characters in the \"FolderName\" field.");
    return (false);
  } else if(folder.length > 64) {
    alert("Please specify under 64 characters for the Folder Name");
    return (false);
  } else	 {
	return true;
  }

}