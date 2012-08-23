function searchtype(v)	{
  if(v == 1 || v == 2)	{
	document.ldap.servername.disabled = true;
  } 
  else {
	document.ldap.servername.disabled = false;
	document.ldap.servername.focus();
	document.ldap.servername.select();
  }
}