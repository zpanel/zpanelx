function abook(email,name,phone) {
	location.href = 'abook.php?add=1&func=open&UserEmail=' + escape(email) + '&UserFirstName=' + escape(firstname) + '&UserLastName=' + escape(lastname) + '&UserWorkPhone=' + escape(phone) + '&ldapadd=1';
}
