function validate_group(theForm)
{

  if (!theForm.UserGroup.value)
  {
    alert("Bitte geben Sie einen Wert für das Feld \"Gruppenname\" ein.");
    theForm.UserGroup.focus();
    return (false);
  }
  if (theForm.UserGroup.value.length > 32) {
    alert("Bitte Ihre Eingabe im Feld \"Gruppenname\" auf 32 Zeichen begrenzen.");
    theForm.UserGroup.focus();
	theForm.UserGroup.select();
    return (false);
  }

  var prev;
  if(theForm.UserGroupPrev)	{
	  prev = theForm.UserGroupPrev.value;
  } else	{
	  prev = '';
  }

  // First check the group does not exist already
  if( prev == theForm.UserGroup.value )	{
	
  } else if( checkGroup("abook.php?func=checkgroup&GroupName=" + theForm.UserGroup.value) )	{
	alert('Der gewählte Gruppenname existiert bereits - bitte geben Sie einen anderen Gruppennamen an');
    theForm.UserGroup.focus();
	theForm.UserGroup.select();
	return (false);
  }

  var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
  var checkStr = theForm.UserGroup.value;
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
    alert("Bitte nur Buchstaben, Zahlen oder \".-_\" Zeichen in das Feld \"Benutzergruppe\" eingeben.");
    theForm.UserGroup.focus();
    return (false);
  }
 

  return (true);
}

function SubmitGroup()	{

// Loop through and select all emails in the Group box
if(validate_group(document.MainForm))	{
len=document.MainForm.ToAddress.options.length 

for (i=0;i<len;i++){
document.MainForm.ToAddress.options[i].selected=true;
}


if(!len)	{

    alert("Wählen Sie Benutzer und Gruppen aus.");
    document.MainForm.ToAddress.focus();
    return (false);
}

document.MainForm.submit();
}


}