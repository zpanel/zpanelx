
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
    alert("Le nom de la boîte de messagerie != peut pas dépasser 64 caractères. Entrez un autre nom.");
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
	    alert("S'il vous plait, n'entrez que des lettres, des chiffres, des espaces, et les caractères \".-_[]()\" dans le champs du nom de dossier.");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Veuillez indiquer moins de 64 caractères pour le nom du dossier");
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
	    alert("S'il vous plait, n'entrez que des lettres, des chiffres, des espaces, et les caractères \".-_[]()\" dans le champs du nom de dossier.");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Veuillez indiquer moins de 64 caractères pour le nom du dossier");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

