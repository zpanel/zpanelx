
function validate_form(theForm)
{

  if (theForm.foldername.value.length < 1)
  {
    alert("Bitte geben Sie mindestens ein Zeichen im Ordnernamen ein");
    theForm.foldername.focus();
    return (false);
  }

  if (theForm.foldername.value.length > 64)
  {
    alert("Der Postfach Name darf nicht nicht mehr als 64 Zeichen lang sein. Bitte wählen Sie einen anderen Namen.");
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
	    alert("Geben Sie bitte nur Buchstaben, Zahlen, Leerzeichen oder die folgenden Sonderzeichen (\".-_[]()\")in das \"FolderName\" Feld ein.");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Bitte geben Sie weniger als 64 Zeichen für den Ordnernamen an");
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
	    alert("Geben Sie bitte nur Buchstaben, Zahlen, Leerzeichen oder die folgenden Sonderzeichen (\".-_[]()\")in das \"FolderName\" Feld ein.");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Bitte geben Sie weniger als 64 Zeichen für den Ordnernamen an");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

