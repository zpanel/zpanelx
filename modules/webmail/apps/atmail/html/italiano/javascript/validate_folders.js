
function validate_form(theForm)
{

  if (theForm.foldername.value.length < 1)
  {
    alert("Specificare almeno un carattere per il nome della cartella");
    theForm.foldername.focus();
    return (false);
  }

  if (theForm.foldername.value.length > 64)
  {
    alert("Il nome della Mailbox non può superare i 64 caratteri. Ti preghiamo di usare un'altro nome.");
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
	    alert("Inserisci solo lettere, numeri , spazi e i caratteri \".-_[]()\" nel campo \"Nome Cartella\".");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Specifica un nome della cartella inferiore a 64 caratteri");
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
	    alert("Inserisci solo lettere, numeri , spazi e i caratteri \".-_[]()\" nel campo \"Nome Cartella\".");
	    return (false);
	  } else if(theForm.foldername.length > 64) {
	    alert("Specifica un nome della cartella inferiore a 64 caratteri");
	    return (false);
	  } else	 {
		return true;
	  }

  }
}

