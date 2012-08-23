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
    alert("\&quot;FolderName\&quot;フィールドには、文字、数字、スペースおよび\&quot;.-_[]()\&quot;のみ入力してください。");
    return (false);
  } else if(folder.length > 64) {
    alert("フォルダー名を64文字以下で指定してください。");
    return (false);
  } else	 {
	return true;
  }

}