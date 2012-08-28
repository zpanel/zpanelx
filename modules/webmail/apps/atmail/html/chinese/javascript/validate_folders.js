
function validate_form(theForm)
{

  if (theForm.foldername.value.length < 1)
  {
    alert("請至少指定一個字符的文件夾名稱");
    theForm.foldername.focus();
    return (false);
  }

  if (theForm.foldername.value.length > 64)
  {
    alert("郵箱名稱不能超過64個字符.請指定其他名稱.");
    theForm.foldername.focus();
    return (false);
  }

  if (utf7enabled == 1) {

	  var checkBad = "./\\'()\"";
	  var checkStr = theForm.foldername.value;
	  var allValid = true;
	  for (i = 0;  i < checkStr.length;  i++)
	  {
	    ch = checkStr.charAt(i);
	    for (j = 0;  j < checkBad.length;  j++)
	      if (ch == checkBad.charAt(j)) {
	      	allValid = false;
	      	break;
	      }
	  }
	  if (!allValid)
	  {
	    alert("請只輸入文字，數字，空格和\ ".-_[]() \ “中的字符\ “文件夾\ ”領域.");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("請指定下64個字符的文件夾名稱");
	    return (false);
	  } else	 {
		return true;
	  }

	  return (true);
  } else {
  	
	  var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_. []()";
	  var checkStr = theForm.foldername.value;
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
	  } else if(theForm.foldername.length > 64) {
	    alert("請指定下64個字符的文件夾名稱");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

