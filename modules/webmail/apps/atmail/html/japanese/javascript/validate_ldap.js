function checkldap(form)	 {

if(!document.ldap.FirstName.value && !document.ldap.LastName.value && !document.ldap.email.value && !document.ldap.UserWorkCompany.value && !document.ldap.UserHomeAddress.value && !document.ldap.UserHomeCity.value && !document.ldap.UserHomeState.value && !document.ldap.UserHomeCountry.value)	{
alert('検索キーワードを指定してください。');
return false;
} else	{

//document.body.style.cursor = 'wait';
return true;
}

}
