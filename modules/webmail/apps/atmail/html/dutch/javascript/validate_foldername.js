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
    alert("Gelieve in het veld \"Mappennaam\" enkel letters, cijfers, spaties en \".-_[]()\" tekens in te geven.");
    return (false);
  } else if(folder.length > 64) {
    alert("Gelieve maximum 64 karakters in te geven als mappennaam.");
    return (false);
  } else	 {
	return true;
  }

}