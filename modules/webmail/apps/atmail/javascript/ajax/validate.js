var liveSearchReq = false;
var t = null;
var liveSearchLast = "";
	
var isIE = false;
// on !IE we only have to initialize it once
if (window.XMLHttpRequest) {
	liveSearchReq = new XMLHttpRequest();
}

function checkGroup(url) {


// Check we are running a decent browser
var agt=navigator.userAgent.toLowerCase();
var ie=(agt.indexOf("msie") != -1);

// Check if we are running Mozilla
var saf=(agt.indexOf('safari')!=-1);
var konq=(!saf && (agt.indexOf('konqueror')!=-1) ) ? true : false;
var moz=((!saf && !konq ) && ( agt.indexOf('gecko')!=-1 ) ) ? true : false;

// Dont run if old browser
if(!ie && !moz)	{
return 0;
}

	if (liveSearchReq && liveSearchReq.readyState < 4) {
		liveSearchReq.abort();
	}

	if (window.XMLHttpRequest) {
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		liveSearchReq = new ActiveXObject("Microsoft.XMLHTTP");
	}

	// Trigger which function to call
	liveSearchReq.open("GET", url, false);
	liveSearchReq.send(null);

	var result;

	if(liveSearchReq.status == 200)	{
	result = liveSearchReq.responseText;
	return result;
	} else	{
	return 0;
	}

}

function CheckStatus(max)	{

	max = max + 1;

	// We have tried for more then 10 ties, exit
	if(max == 100)	{
	alert('XML HTTP Timeout!');
	return 0;	
	} else if (liveSearchReq.readyState == 4) {
	alert('Respnse text = ' + liveSearchReq.responseText);
	return 0;
	} else	{
	setTimeout("CheckStatus(" + max + ")", 100);
	}

}

function liveSearchProcessReqChange() {
	
	var value;

	if (liveSearchReq.readyState == 4) {
	alert(liveSearchReq.responseText);
	//return liveSearchReq.responseText;
	}

	return;

	
}