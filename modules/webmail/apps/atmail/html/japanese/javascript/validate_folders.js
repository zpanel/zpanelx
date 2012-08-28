
function validate_form(theForm)
{

  if (theForm.foldername.value.length < 1)
  {
    alert("Please specify at least one character for the folder-name");
    theForm.foldername.focus();
    return (false);
  }

  if (theForm.foldername.value.length > 64)
  {
    alert("フォルダ名には64文字以上は使用できません。他の名前を使用してください。");
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
	    alert("\&quot;FolderName\&quot;フィールドには、文字、数字、スペースおよび\&quot;.-_[]()\&quot;のみ入力してください。");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("フォルダー名を64文字以下で指定してください。");
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
	    alert("\&quot;FolderName\&quot;フィールドには、文字、数字、スペースおよび\&quot;.-_[]()\&quot;のみ入力してください。");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("フォルダー名を64文字以下で指定してください。");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

