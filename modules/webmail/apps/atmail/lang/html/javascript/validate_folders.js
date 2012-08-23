
function validate_form(theForm)
{

  if (theForm.foldername.value.length < 1)
  {
    alert("$lang['lang_javascript_validate_folders_minfolder']");
    theForm.foldername.focus();
    return (false);
  }

  if (theForm.foldername.value.length > 64)
  {
    alert("$lang['lang_javascript_validate_folders_maxfolder']");
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
	    alert("$lang['lang_newfoldermodal_foldernamealert']");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("$lang['lang_newfoldermodal_foldernamelimitalert']");
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
	    alert("$lang['lang_newfoldermodal_foldernamealert']");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("$lang['lang_newfoldermodal_foldernamelimitalert']");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

