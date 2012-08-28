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
    alert("請只輸入文字，數字，空格和\ ".-_[]() \ “中的字符\ “文件夾\ ”領域.");
    return (false);
  } else if(folder.length > 64) {
    alert("請指定下64個字符的文件夾名稱");
    return (false);
  } else	 {
	return true;
  }

}