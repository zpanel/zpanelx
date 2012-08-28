function validate_group(theForm)
{

  if (!theForm.UserGroup.value)
  {
    alert("Ti preghiamo di inserire un valore per il campo \"Nome Gruppo\".");
    theForm.UserGroup.focus();
    return (false);
  }
  if (theForm.UserGroup.value.length > 32) {
    alert("Ti preghiamo di ridurre il \"Nome gruppo\" a 32 caratteri.");
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
	alert('Il nome del gruppo indicato esiste già – Specifica un nuovo gruppo');
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
    alert("Ti preghiamo di inserire nel campo \"Gruppo Utenti\" solo lettere, numeri e i segni \".-_\".");
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

    alert("Seleziona Gruppi e Utenti");
    document.MainForm.ToAddress.focus();
    return (false);
}

document.MainForm.submit();
}


}