	/*
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Bitflux GmbH                                      |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: Bitflux GmbH <devel@bitflux.ch>                              |
// | Modified by Calacode.com for @Mail                              |
// +----------------------------------------------------------------------+

*/
var liveSearchReq = false;
var t = null;
var liveSearchLast = "";
	
var field;

var isIE = false;
// on !IE we only have to initialize it once
liveSearchReq = createXMLHttpRequest();

function addemail(v, FromKeyboard)	{
	v = v.replace("<b>", "").replace("</b>", "").replace("\n", "").replace("\r", "").replace("\n", "").replace("\r", "");

	var emails = field.value.split(/,|;/);
	var replaceStr = emails[emails.length - 1] + "$";
	replaceStr = replaceStr.replace(/^ /, '');
	var re = new RegExp(replaceStr, "g");

	var group = new RegExp(/Group/);
	var m = group.exec(field.value);

	if (checkToken(field.value, "@") || m) {
		var str = field.value;
		field.value = str.replace(re, v + "; ");
	} else	{
		field.value = v + "; ";
	}

	liveSearchHide();
}

function liveSearchInit() {
	ObjLSResult = document.createElement("div");
	ObjLSResult.id = "LSResult";
	ObjLSResult.style.display = "none";
	ObjLSResult.style.zIndex = "100";
	ObjLSResult.style.position = "absolute";
	ObjLSResult.style.backgroundColor = "white";
	ObjLSResult.style.border = "1px solid #918E82";
	ObjLSResult.style.width = "300px";

	ObjLSResultDiv = document.createElement("div");
	ObjLSResultDiv.id = "LSShadow";
	ObjLSResultDiv.style.color = "#282211";
	ObjLSResultDiv.style.fontFamily = "Arial, Helvetica, sans-serif";
	ObjLSResultDiv.style.fontSize = "9pt";
	ObjLSResultDiv.style.border = "1px solid #FCFCF8";
	ObjLSResultDiv.style.padding = "0px 2px 0px 2px";
	ObjLSResultDiv.style.cursor = "pointer";

	ObjLSResult.appendChild(ObjLSResultDiv);
	document.body.appendChild(ObjLSResult);

	document.body.onclick = new Function("liveSearchHide();");

	if (navigator.userAgent.indexOf("Safari") > 0) {
	} else if (navigator.product == "Gecko") {
	} else {
		isIE = true;
	}
}

function liveSearchHideDelayed() {
	window.setTimeout("liveSearchHide()",400);
}
	
function liveSearchHide() {
	document.getElementById("LSResult").style.display = "none";
	var highlight = document.getElementById("LSHighlight");
	if (highlight) highlight.id = "";
}

function liveSearchKeyPress(e) {
	e = (e) ? e : ((window.event) ? window.event : "");

	try {
		if (document.getElementById("LSResult").style.display == "none") return false;
	} catch(err)	{
		return false;
	}

	if ((e.which == 40) || (e.keyCode == 40)) { //KEY DOWN
		var ObjHighlight = document.getElementById("LSHighlight");
		if (!ObjHighlight) {
			ObjHighlight = document.getElementById("tabledata").tBodies[0].rows[0];
		} else {
			ObjHighlight.id = "";
			ObjHighlight = ObjHighlight.nextSibling;
		}
		if (ObjHighlight) ObjHighlight.id = "LSHighlight";
		if (!isIE) e.preventDefault();
	} else if ((e.which == 38) || (e.keyCode == 38)) { //KEY UP
		var ObjHighlight = document.getElementById("LSHighlight");
		if (!ObjHighlight) {
			ObjHighlight = document.getElementById("tabledata").tBodies[0].rows[document.getElementById("tabledata").tBodies[0].rows.length - 1];
		} else {
			ObjHighlight.id = "";
			ObjHighlight = ObjHighlight.previousSibling;
		}
		if (ObjHighlight) ObjHighlight.id = "LSHighlight";
		if (!isIE) e.preventDefault();
	} else if ((e.which == 27) || (e.keyCode == 27)) { //ESC
		var ObjHighlight = document.getElementById("LSHighlight");
		if (ObjHighlight) ObjHighlight.id = "";
		document.getElementById("LSResult").style.display = "none";
	} else if ((e.which == 13) || (e.keyCode == 13)) { // Return key
		var ObjHighlight = document.getElementById("LSHighlight");
		document.getElementById("LSResult").style.display = "none";
		if (ObjHighlight) {
			if (isIE) {
				addemail(ObjHighlight.innerText, true);
			} else {
				addemail(ObjHighlight.textContent, true);
			}
			ObjHighlight.id = "";
		}
	}
}

function GetElePos(StartElement, Bottom) {
	if (StartElement.offsetParent) {
		var XYPos = [StartElement.offsetLeft, StartElement.offsetTop];
		if (Bottom == true) XYPos[1] += StartElement.offsetHeight;
		while (StartElement = StartElement.offsetParent) {
			XYPos[0] += StartElement.offsetLeft;
			XYPos[1] += StartElement.offsetTop;
		}
		return XYPos;
	} else {
		return [0, 0];
	}
}

function liveSearchStart(inputfield, e) {
	e = (e) ? e : ((window.event) ? window.event : "");
	var ElePos = GetElePos(inputfield, true);
	document.getElementById("LSResult").style.left = ElePos[0];
	document.getElementById("LSResult").style.top = ElePos[1];

	if ((e.which == 13) || (e.keyCode == 13)) return;
	field = inputfield;
	if (t) window.clearTimeout(t);
	t = window.setTimeout("liveSearchDoSearch()", 250);
}

function liveSearchDoSearch() {
	if (typeof liveSearchRoot == "undefined") liveSearchRoot = "";
	if (typeof liveSearchRootSubDir == "undefined") liveSearchRootSubDir = "";
	if (typeof liveSearchParams == "undefined") liveSearchParams = "";
	if (liveSearchLast != field.value) {
		if (liveSearchReq && liveSearchReq.readyState < 4) liveSearchReq.abort();
		if (field.value == "") {
			liveSearchHide();
			return false;
		}
		// We need to init each time?
		liveSearchReq = createXMLHttpRequest();
		liveSearchReq.onreadystatechange= liveSearchProcessReqChange;

		var re = new RegExp(/\w/);
		var m = re.exec(field.value);

		// Return the email only if the regex matches
		if(field.value.length > 1 && m)	{
			
			// Require to use POST rather then GET, if the To/Cc/Bcc line is too long IE JS error
			var POSTString = "addr=" + escape(field.value) + liveSearchParams + "&rand=" + Math.round(Math.random() * 9999);
			liveSearchReq.open("POST", "abook.php?func=quicksearch&ajax=1", true);
			liveSearchReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			liveSearchReq.send(POSTString);
			
			liveSearchLast = field.value;
		}
	}
}

function liveSearchProcessReqChange() {
	if (liveSearchReq.readyState == 4) {
		if (liveSearchReq.responseText) {
//			alert(liveSearchReq.responseText);
			var res = document.getElementById("LSResult");
			res.style.display = "";
			var sh = document.getElementById("LSShadow");
			sh.innerHTML = liveSearchReq.responseText;
		} else {
			liveSearchHide();
		}
	}
}

function liveSearchSubmit() {
	var highlight = document.getElementById("LSHighlight");
	if (highlight && highlight.firstChild) {
		window.location = liveSearchRoot + liveSearchRootSubDir + highlight.firstChild.nextSibling.getAttribute("href");
		return false;
	} else {
		return true;
	}
}

function changeShadow(elm)	{
	var highlight = document.getElementById("LSHighlight");
	if (highlight) highlight.id = "";
	elm.id = "LSHighlight";
}

function checkToken(inStr, token) {
	for ( var i = 0; i < inStr.length; i++) {
		if (inStr.charAt(i) == token) return true;
	}
	return false;
}

function createXMLHttpRequest() {
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
	try { return new XMLHttpRequest(); } catch(e) {}
	alert("XMLHttpRequest not supported");
	return null;
}
