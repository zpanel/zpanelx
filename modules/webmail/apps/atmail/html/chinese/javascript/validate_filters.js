function validate_filters_form(theForm) {

	if (theForm.Value.value == "") {
		alert("Please enter an Expression to add.");
		theForm.Value.focus();
		return (false);
	}

	if (theForm.Score.value < -100 || theForm.Score.value > 100 ) {
		alert("Please specify a score between -100 and 100");
		theForm.Score.focus();
		theForm.Score.select();
		return (false);
	}
	
	if (theForm.Score.value == "" ) {
		alert("Please enter a Score");
		theForm.Score.focus();
		return (false);
	}


document.spamsubmit.Filter.value = theForm.Filter.value;
document.spamsubmit.Header.value = theForm.Header.value;
document.spamsubmit.Display.value = theForm.Display.value;
document.spamsubmit.Value.value = theForm.Value.value;
document.spamsubmit.Type.value = theForm.Type.value;
document.spamsubmit.Score.value = theForm.Score.value;

theForm.Value.value='';
//theForm.Score.value='';

if(theForm.Header.value == 'body')	{
document.spamsubmit.target = theForm.Header.value + 'msg';
} else	{
document.spamsubmit.target = theForm.Header.value;	
}

document.spamsubmit.submit();

return false;
}