function validate_group(theForm)
{

  if (!theForm.UserGroup.value)
  {
    alert("Entrez une valeur pour le champs \"Nom du Groupe\".");
    theForm.UserGroup.focus();
    return (false);
  }
  if (theForm.UserGroup.value.length > 32) {
    alert("Veuillez limiter votre entrée \"Nom du Groupe\" à 32 caractères.");
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
	alert('Le groupe existe déjà - Spécifiez un nouveau groupe');
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
    alert("Entrez seulement des lettres, chiffres et \".-_\" dans le champs \"Groupe d\'utilisateur\".");
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

    alert("Sélectionnez des groupes et des utilisateurs");
    document.MainForm.ToAddress.focus();
    return (false);
}

document.MainForm.submit();
}


}