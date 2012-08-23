function loadXMLDoc(url, object) {

// branch for native XMLHttpRequest object
if (window.XMLHttpRequest) {
req = new XMLHttpRequest();
req.onreadystatechange = processReqChange;
req.open("GET", url, true);
req.send(null);

// branch for IE/Windows ActiveX version
} else if (window.ActiveXObject) {
req = new ActiveXObject("Microsoft.XMLHTTP");

if (req) {
req.onreadystatechange = processReqChange;
req.open("GET", url, true);
req.send();
}
}
}

function processReqChange(object) {
// only if req shows "loaded"

if (req.readyState == 4) {
// only if "OK"
if (req.status == 200) {
eval(object);
return true
} else {
return false
//	alert("There was a problem retrieving the XML data:\n" + req.statusText);
}
}
}

// Ajax abook add
function addabook()	{
document.getElementById('AlertAbook').style.display='';
setTimeout("document.getElementById('AlertAbook').style.display='none'", 3000);
}
