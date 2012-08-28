var MousePosXY = new Array();

function MousePos(e) {
	var posx = 0;
	var posy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) {
		posx = e.pageX;
		posy = e.pageY;
	} else if (e.clientX || e.clientY) {
		posx = e.clientX + document.body.scrollLeft;
		posy = e.clientY + document.body.scrollTop;
	}
	MousePosXY[0] = posx;
	MousePosXY[1] = posy;
}

var SpellChkWords = new Array();
var AjustedTxtArray = new Array();

var SpellChkReq = false;
var ComposeModeStorage = "";
var SpellCheckMode = false;

function SpellCheck(ReturnData) {
	if (ComposeMode == "Text") {
		ObjComposeMsgText = document.getElementById("ComposeMsgText");
	}
	if (SpellCheckMode == false && ReturnData != true) {
		SpellCheckMode = true;
		document.body.style.cursor = "wait";
		ObjPleaseWait = document.createElement("div");
		ObjPleaseWait.id = "SCPleaseWait";
		ObjPleaseWait.style.position = "absolute";
		ObjPleaseWait.style.top = "0px";
		ObjPleaseWait.style.left = "0px";
		ObjPleaseWait.style.width = "100%";
		ObjPleaseWait.style.height = "100%";
		ObjPleaseWait.style.backgroundColor = "white";
		ObjPleaseWait.style.filter = "Alpha(Opacity=10)";
		ObjPleaseWait.style.zIndex = "999";
		document.body.appendChild(ObjPleaseWait);

		if (BrowserType == "ie") {
			document.getElementById("sendMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconSend").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconSend").onclick = "";
			document.getElementById("ToolbarIconSend").onmouseover = "";
			document.getElementById("ToolbarIconSend").onmouseout = "";
			document.getElementById("recipientsMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconRecipients").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconRecipients").onclick = "";
			document.getElementById("ToolbarIconRecipients").onmouseover = "";
			document.getElementById("ToolbarIconRecipients").onmouseout = "";
			document.getElementById("attachmentMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconAttachment").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconAttachment").onclick = "";
			document.getElementById("ToolbarIconAttachment").onmouseover = "";
			document.getElementById("ToolbarIconAttachment").onmouseout = "";
			document.getElementById("priorityMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconPriority").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconPriority").onclick = "";
			document.getElementById("ToolbarIconPriority").onmouseover = "";
			document.getElementById("ToolbarIconPriority").onmouseout = "";
			if (document.getElementById("ToolbarIconEncrypt")) {
				document.getElementById("encryptMiddle").style.filter = "Alpha(Opacity=50)";
				document.getElementById("ToolbarIconEncrypt").style.cursor = "not-allowed";
				document.getElementById("ToolbarIconEncrypt").onclick = "";
				document.getElementById("ToolbarIconEncrypt").onmouseover = "";
				document.getElementById("ToolbarIconEncrypt").onmouseout = "";
			}
			document.getElementById("saveMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconSave").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconSave").onclick = "";
			document.getElementById("ToolbarIconSave").onmouseover = "";
			document.getElementById("ToolbarIconSave").onmouseout = "";

			try {
			document.getElementById("videoMiddle").style.filter = "Alpha(Opacity=50)";
			document.getElementById("ToolbarIconVideo").style.cursor = "not-allowed";
			document.getElementById("ToolbarIconVideo").onclick = "";
			document.getElementById("ToolbarIconVideo").onmouseover = "";
			document.getElementById("ToolbarIconVideo").onmouseout = "";
			} catch(e)	{
			// Videomail disabled
			}

			MenuPullData["File"][1]["DisabledTmp"] = MenuPullData["File"][1]["Disabled"];
			MenuPullData["File"][1]["Disabled"] = true;
			MenuPullData["File"][2]["DisabledTmp"] = MenuPullData["File"][2]["Disabled"];
			MenuPullData["File"][2]["Disabled"] = true;
			MenuPullData["Edit"][0]["DisabledTmp"] = MenuPullData["Edit"][0]["Disabled"];
			MenuPullData["Edit"][0]["Disabled"] = true;
			MenuPullData["Edit"][1]["DisabledTmp"] = MenuPullData["Edit"][1]["Disabled"];
			MenuPullData["Edit"][1]["Disabled"] = true;
			MenuPullData["Edit"][2]["DisabledTmp"] = MenuPullData["Edit"][2]["Disabled"];
			MenuPullData["Edit"][2]["Disabled"] = true;
			MenuPullData["Edit"][3]["DisabledTmp"] = MenuPullData["Edit"][3]["Disabled"];
			MenuPullData["Edit"][3]["Disabled"] = true;
			MenuPullData["Tools"][1]["DisabledTmp"] = MenuPullData["Tools"][1]["Disabled"];
			MenuPullData["Tools"][1]["Disabled"] = true;
			MenuPullData["Tools"][2]["DisabledTmp"] = MenuPullData["Tools"][2]["Disabled"];
			MenuPullData["Tools"][2]["Disabled"] = true;
			MenuPullData["Tools"][3]["DisabledTmp"] = MenuPullData["Tools"][3]["Disabled"];
			MenuPullData["Tools"][3]["Disabled"] = true;
			MenuPullData["Tools"][4]["DisabledTmp"] = MenuPullData["Tools"][4]["Disabled"];
			MenuPullData["Tools"][4]["Disabled"] = true;
			MenuPullData["Tools"][5]["DisabledTmp"] = MenuPullData["Tools"][5]["Disabled"];
			MenuPullData["Tools"][5]["Disabled"] = true;
			MenuPullData["Message"][0]["DisabledTmp"] = MenuPullData["Message"][0]["Disabled"];
			MenuPullData["Message"][0]["Disabled"] = true;
			MenuPullData["Message"][1]["DisabledTmp"] = MenuPullData["Message"][1]["Disabled"];
			MenuPullData["Message"][1]["Disabled"] = true;
			MenuPullData["Message"][2]["DisabledTmp"] = MenuPullData["Message"][2]["Disabled"];
			MenuPullData["Message"][2]["Disabled"] = true;
			MenuPullData["Message"][3]["DisabledTmp"] = MenuPullData["Message"][3]["Disabled"];
			MenuPullData["Message"][3]["Disabled"] = true;
			MenuPullData["Priority"][0]["DisabledTmp"] = MenuPullData["Priority"][0]["Disabled"];
			MenuPullData["Priority"][0]["Disabled"] = true;
			MenuPullData["Priority"][1]["DisabledTmp"] = MenuPullData["Priority"][1]["Disabled"];
			MenuPullData["Priority"][1]["Disabled"] = true;
			MenuPullData["Priority"][2]["DisabledTmp"] = MenuPullData["Priority"][2]["Disabled"];
			MenuPullData["Priority"][2]["Disabled"] = true;
			if (document.getElementById("ToolbarIconEncrypt")) {
				MenuPullData["Encrypt"][0]["DisabledTmp"] = MenuPullData["Encrypt"][0]["Disabled"];
				MenuPullData["Encrypt"][0]["Disabled"] = true;
				MenuPullData["Encrypt"][1]["DisabledTmp"] = MenuPullData["Encrypt"][1]["Disabled"];
				MenuPullData["Encrypt"][1]["Disabled"] = true;
				MenuPullData["Encrypt"][2]["DisabledTmp"] = MenuPullData["Encrypt"][2]["Disabled"];
				MenuPullData["Encrypt"][2]["Disabled"] = true;
				MenuPullData["Encrypt"][3]["DisabledTmp"] = MenuPullData["Encrypt"][3]["Disabled"];
				MenuPullData["Encrypt"][3]["Disabled"] = true;
			}
			MenuPullData["Tools"][0]["Status"] = true;
			MenuPullCtrl("All");
		} else if (BrowserType == "ff") {
			parent.changeMenu(true, "menufile");
			parent.changeMenu(true, "menutools");
			parent.changeMenu(true, "menumessage");

			parent.showButton("none", "button-sendmessage");
			parent.showButton("none", "button-recipients");
			parent.showButton("none", "button-attach");
			parent.showButton("none", "button-priority");
			parent.showButton("none", "button-spelling");

			try {
			parent.showButton("none", "button-videomail");
			} catch(e) {
			// Videomail disabled
			}

			parent.showButton("", "button-spelling-return");
			parent.showButton("none", "button-savemsg");

			parent.showButton("none", "divider-menu1");
			parent.showButton("none", "divider-menu2");
			parent.showButton("none", "divider-menu3");
			parent.showButton("none", "divider-menu4");
		}

		SpellChkWords.length = 0;
		SpellChkReq = false;

		if (SpellChkReq && SpellChkReq.readyState < 4) SpellChkReq.abort();

		if (window.XMLHttpRequest) {
			SpellChkReq = new XMLHttpRequest();
		} else if (window.ActiveXObject) {
			SpellChkReq = new ActiveXObject("Microsoft.XMLHTTP");
		}

		SpellChkReq.onreadystatechange = SpellChkReqChange;
		var EmailMsg = "";
		if (ComposeMode == "HTML") {
			EmailMsg = oEdit1.getTextBody();
		} else if (ComposeMode == "Text") {
			EmailMsg = ObjComposeMsgText.value;
		}
		var POSTString = "ajax=1&emailmessage=" + encodeURIComponent(EmailMsg);
		SpellChkReq.open("POST", "spell.php", true);
		SpellChkReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		SpellChkReq.send(POSTString);
	} else {
		ObjSpellCheckerBox = document.getElementById("SpellCheckerBox");
		if (ObjSpellCheckerBox.style.display == "none") {
			if (ComposeMode == "HTML") {
				ComposeModeStorage = oEdit1.getHTMLBody();
				oEdit1 = null;
				document.getElementById("ComposeMsgTextContainer").innerHTML = "";
			} else if (ComposeMode == "Text") {
				ObjComposeMsgText.style.display = "none";
			}

			if (window.ActiveXObject) {
				ObjSpellCheckerBox.style.width = "100%";
				ObjSpellCheckerBox.style.height = "100%";
			} else {
				ObjSpellCheckerBox.style.width = "95%";
				ObjSpellCheckerBox.style.height = (document.body.clientHeight - 150) + "px";
			}
			ObjSpellCheckerBox.style.display = "";
			ObjSpellCheckerBox.style.border = "1px solid red";
			ObjSpellCheckerBox.style.backgroundColor = "white";
			ObjSpellCheckerBox.style.overflow = "auto";
			ObjSpellCheckerBox.style.fontFamily = "Arial, Helvetica, sans-serif";
			ObjSpellCheckerBox.style.fontSize = "9pt";
			if (ComposeMode == "HTML") {
				ObjSpellCheckerBox.style.padding = "42px 11px 11px 11px";
				var AdjustedTxt = ComposeModeStorage;
			} else if (ComposeMode == "Text") {
				ObjSpellCheckerBox.style.padding = "2px 1px 1px 1px";
				ObjSpellCheckerBox.style.cursor = "default";
				var AdjustedTxt = ObjComposeMsgText.value.replace(/\r|\n/g, "<br>");
			}

			AjustedTxtArray.length = 0;

			ObjAdjustedTxt = document.createElement("div");
			ObjAdjustedTxt.innerHTML = AdjustedTxt;

			CycleDOMNodes(ObjAdjustedTxt);

			ObjSpellCheckerBox.innerHTML = ObjAdjustedTxt.innerHTML;

			document.body.style.cursor = "";
			document.body.removeChild(document.getElementById("SCPleaseWait"));
		} else {
			SpellCheckMode = false;
			if (BrowserType == "ie") {
				document.getElementById("sendMiddle").style.filter = "";
				document.getElementById("ToolbarIconSend").style.cursor = "hand";
				document.getElementById("ToolbarIconSend").onclick = new Function("SendMsg();");
				document.getElementById("ToolbarIconSend").onmouseover = new Function("ButtonSwapLang('send','Down');");
				document.getElementById("ToolbarIconSend").onmouseout = new Function("ButtonSwapLang('send','Flat'); DontHide = 0;");
				document.getElementById("recipientsMiddle").style.filter = "";
				document.getElementById("ToolbarIconRecipients").style.cursor = "hand";
				document.getElementById("ToolbarIconRecipients").onclick = new Function("AddRecpientsXP();");
				document.getElementById("ToolbarIconRecipients").onmouseover = new Function("ButtonSwap('recipients','Down');");
				document.getElementById("ToolbarIconRecipients").onmouseout = new Function("ButtonSwap('recipients','Flat'); DontHide = 0;");
				document.getElementById("attachmentMiddle").style.filter = "";
				document.getElementById("ToolbarIconAttachment").style.cursor = "hand";
				document.getElementById("ToolbarIconAttachment").onclick = new Function("AttachmentDialog('" + unique + "');");
				document.getElementById("ToolbarIconAttachment").onmouseover = new Function("ButtonSwapLang('attachment','Down');");
				document.getElementById("ToolbarIconAttachment").onmouseout = new Function("ButtonSwapLang('attachment','Flat'); DontHide = 0;");
				document.getElementById("priorityMiddle").style.filter = "";
				document.getElementById("ToolbarIconPriority").style.cursor = "hand";
				document.getElementById("ToolbarIconPriority").onclick = new Function("ShowMenu(MenuPullPriority,33,75);");
				document.getElementById("ToolbarIconPriority").onmouseover = new Function("ButtonSwap('priority','Down');");
				document.getElementById("ToolbarIconPriority").onmouseout = new Function("ButtonSwap('priority','Flat'); DontHide = 0;");
				if (document.getElementById("ToolbarIconEncrypt")) {
					document.getElementById("encryptMiddle").style.filter = "";
					document.getElementById("ToolbarIconEncrypt").style.cursor = "hand";
					document.getElementById("ToolbarIconEncrypt").onclick = new Function("ShowMenu(MenuPullEncrypt,33,115);");
					document.getElementById("ToolbarIconEncrypt").onmouseover = new Function("ButtonSwap('encrypt','Down');");
					document.getElementById("ToolbarIconEncrypt").onmouseout = new Function("ButtonSwap('encrypt','Flat'); DontHide = 0;");
				}
				document.getElementById("saveMiddle").style.filter = "";
				document.getElementById("ToolbarIconSave").style.cursor = "hand";
				document.getElementById("ToolbarIconSave").onclick = new Function("SaveMsg();");
				document.getElementById("ToolbarIconSave").onmouseover = new Function("ButtonSwap('save','Down');");
				document.getElementById("ToolbarIconSave").onmouseout = new Function("ButtonSwap('save','Flat'); DontHide = 0;");

				try {
				document.getElementById("videoMiddle").style.filter = "";
				document.getElementById("ToolbarIconVideo").style.cursor = "hand";
				document.getElementById("ToolbarIconVideo").onclick = new Function("ToggleVideo()");
				document.getElementById("ToolbarIconVideo").onmouseover = new Function("ButtonSwap('video','Down');");
				document.getElementById("ToolbarIconVideo").onmouseout = new Function("ButtonSwap('video','Flat'); DontHide = 0;");
				} catch(e) {
				// Videomail disabled
				}

				MenuPullData["File"][1]["Disabled"] = MenuPullData["File"][1]["DisabledTmp"];
				MenuPullData["File"][2]["Disabled"] = MenuPullData["File"][2]["DisabledTmp"];
				MenuPullData["Edit"][0]["Disabled"] = MenuPullData["Edit"][0]["DisabledTmp"];
				MenuPullData["Edit"][1]["Disabled"] = MenuPullData["Edit"][1]["DisabledTmp"];
				MenuPullData["Edit"][2]["Disabled"] = MenuPullData["Edit"][2]["DisabledTmp"];
				MenuPullData["Edit"][3]["Disabled"] = MenuPullData["Edit"][3]["DisabledTmp"];
				MenuPullData["Tools"][1]["Disabled"] = MenuPullData["Tools"][1]["DisabledTmp"];
				MenuPullData["Tools"][2]["Disabled"] = MenuPullData["Tools"][2]["DisabledTmp"];
				MenuPullData["Tools"][3]["Disabled"] = MenuPullData["Tools"][3]["DisabledTmp"];
				MenuPullData["Tools"][4]["Disabled"] = MenuPullData["Tools"][4]["DisabledTmp"];
				MenuPullData["Tools"][5]["Disabled"] = MenuPullData["Tools"][5]["DisabledTmp"];
				MenuPullData["Message"][0]["Disabled"] = MenuPullData["Message"][0]["DisabledTmp"];
				MenuPullData["Message"][1]["Disabled"] = MenuPullData["Message"][1]["DisabledTmp"];
				MenuPullData["Message"][2]["Disabled"] = MenuPullData["Message"][2]["DisabledTmp"];
				MenuPullData["Message"][3]["Disabled"] = MenuPullData["Message"][3]["DisabledTmp"];
				MenuPullData["Priority"][0]["Disabled"] = MenuPullData["Priority"][0]["DisabledTmp"];
				MenuPullData["Priority"][1]["Disabled"] = MenuPullData["Priority"][1]["DisabledTmp"];
				MenuPullData["Priority"][2]["Disabled"] = MenuPullData["Priority"][2]["DisabledTmp"];
				if (document.getElementById("ToolbarIconEncrypt")) {
					MenuPullData["Encrypt"][0]["Disabled"] = MenuPullData["Encrypt"][0]["DisabledTmp"];
					MenuPullData["Encrypt"][1]["Disabled"] = MenuPullData["Encrypt"][1]["DisabledTmp"];
					MenuPullData["Encrypt"][2]["Disabled"] = MenuPullData["Encrypt"][2]["DisabledTmp"];
					MenuPullData["Encrypt"][3]["Disabled"] = MenuPullData["Encrypt"][3]["DisabledTmp"];
				}
				MenuPullData["Tools"][0]["Status"] = false;
				MenuPullCtrl("All");
			} else if (BrowserType == "ff") {
				ObjPopUpBox = document.getElementById("PopUpBox");
				if (ObjPopUpBox) document.body.removeChild(ObjPopUpBox);

				parent.changeMenu(false, "menufile");
				parent.changeMenu(false, "menutools");
				parent.changeMenu(false, "menumessage");

				parent.showButton("", "button-sendmessage");
				parent.showButton("", "button-recipients");
				parent.showButton("", "button-attach");
				parent.showButton("", "button-priority");
				parent.showButton("", "button-spelling");

				try {
				parent.showButton("", "button-videomail");
				} catch(e) {
				// Videomail disabled
				}

				parent.showButton("none", "button-spelling-return");
				parent.showButton("", "button-savemsg");

				parent.showButton("", "divider-menu1");
				parent.showButton("", "divider-menu2");
				parent.showButton("", "divider-menu3");
				parent.showButton("", "divider-menu4");
			}

			ObjSpellCheckerBox = document.getElementById("SpellCheckerBox");
			ObjSpellCheckerBox.style.display = "none";

			for (var i in AjustedTxtArray) {
				ObjSpellCheckerWord = document.getElementById("SpellChkWord" + i);
				if (ObjSpellCheckerWord.childNodes[0].tagName == "INPUT") {
					ObjSpellCheckerWord.parentNode.replaceChild(document.createTextNode(ObjSpellCheckerWord.childNodes[0].value), ObjSpellCheckerWord);
				} else {
					ObjSpellCheckerWord.parentNode.replaceChild(document.createTextNode(ObjSpellCheckerWord.innerHTML), ObjSpellCheckerWord);
				}
			}

			var UpdatedMsgData = ObjSpellCheckerBox.innerHTML;

			if (ComposeMode == "HTML") {
				ObjMRTableTbodyTrTd = document.getElementById("ComposeMsgTextContainer");
				ObjMRTableTbodyTrTd.innerHTML = "";

				ObjSpellCheckerBox = document.createElement("div");
				ObjSpellCheckerBox.id = "SpellCheckerBox";
				ObjSpellCheckerBox.style.display = "none";
				if (window.ActiveXObject) {
					ObjSpellCheckerBox.style.width = "100%";
					ObjSpellCheckerBox.style.height = "100%";
				} else {
					ObjSpellCheckerBox.style.width = "95%";
					ObjSpellCheckerBox.style.height = "500px";
				}
				ObjMRTableTbodyTrTd.appendChild(ObjSpellCheckerBox);
		    ObjMRTableTbodyTrTdTextArea = document.createElement("textarea");
				ObjMRTableTbodyTrTdTextArea.id = "ComposeMsgText";
				ObjMRTableTbodyTrTdTextArea.value = UpdatedMsgData;
				ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTextArea);
				oEdit1 = new InnovaEditor("oEdit1");
				oEdit1.width = "100%";
				if (window.ActiveXObject) {
					oEdit1.height = "100%";
				} else {
					oEdit1.height = document.body.clientHeight - 88;
				}
				oEdit1.arrStyle = [["BODY",false,"","font-family:Arial, Helvetica, sans-serif;font-size:9pt;"]];
				oEdit1.btnStyles = true;
				oEdit1.dropTopAdjustment = 0;
				oEdit1.dropLeftAdjustment = 0;
				oEdit1.REPLACE("ComposeMsgText");
				oEdit1.fullScreen();
				oEdit1.fullScreen();
				oEdit1.focus();
			} else if (ComposeMode == "Text") {
				ObjComposeMsgText.style.display = "";
				UpdatedMsgData = UpdatedMsgData.replace(/<span>|<\/span>/gi, "");
				UpdatedMsgData = UpdatedMsgData.replace(/<br>|<br \/>/gi, "\n");
				UpdatedMsgData = UpdatedMsgData.replace(/&lt;/gi, "<");
				UpdatedMsgData = UpdatedMsgData.replace(/&gt;/gi, ">");
				ObjComposeMsgText.value = UpdatedMsgData;
			}
		}
	}
}

function SpellChkReqChange() {

	if (SpellChkReq.readyState == 4 && SpellChkReq.status == 200) {

		if (SpellChkReq.responseXML) {
			var error = SpellChkReq.responseXML.getElementsByTagName("Error");

			try {
				if (error) {
					alert(error[0].firstChild.nodeValue);
					return;
				}
			} catch(e) {

			}

			for (var x = 0; x < SpellChkReq.responseXML.getElementsByTagName("Suggestion").length; x++) {
				SpellChkWords.push(SpellChkReq.responseXML.getElementsByTagName("Suggestion")[x].firstChild.data.split(","));
			}
			SpellCheck(true);
		}
	}
}

// Cycle through each DOM element for the spell check
function CycleDOMNodes(ObjMasterNode) {
	for (var x = 0; x < ObjMasterNode.childNodes.length; x++) {
		if (ObjMasterNode.childNodes[x].hasChildNodes()) {
			CycleDOMNodes(ObjMasterNode.childNodes[x]);
		} else {
			if (ObjMasterNode.childNodes[x].data != undefined) {
				ObjNewTxtSpan = document.createElement("span");
				ObjNewTxtSpan.innerHTML = CheckDOMSpelling(ObjMasterNode.childNodes[x].data);
				ObjMasterNode.childNodes[x].parentNode.replaceChild(ObjNewTxtSpan, ObjMasterNode.childNodes[x]);
			}
		}
	}
}

// Display the spell-check works on the DOM element
function CheckDOMSpelling(TextToCk) {
	if (SpellChkWords.length > 0) {
		TextToCk = TextToCk.split(/\s+/);
		for (var x in TextToCk) {
			if (TextToCk[x].match(/\w/)) {
				TextToCk[x] = new Array(TextToCk[x], false);
				for (var y in SpellChkWords) {
					if (TextToCk[x][0] == SpellChkWords[y][0] && TextToCk[x][1] == false) {
						TextToCk[x][0] = "<span id=\"SpellChkWord" + AjustedTxtArray.length + "\" style=\"color: red; cursor: pointer; text-decoration: underline;\" onclick=\"SpellCheckSuggestion(" + AjustedTxtArray.length + ", " + y + ");\" oncontextmenu=\"SpellCheckSuggestion(" + AjustedTxtArray.length + ", " + y + "); return false;\">" + SpellChkWords[y][0] + "</span>";
						TextToCk[x][1] = true;
						AjustedTxtArray.push(1);
						break;
					}
				}
				TextToCk[x] = TextToCk[x][0];
			} else if (TextToCk[x] == " ") {
				if(ComposeMode == 'HTML')
				TextToCk[x] = "&nbsp;";
			}
		}
		var separator=(BrowserType == "ie")?" ":"";
		return TextToCk.join(separator);
	} else {
		return TextToCk;
	}
}

var oPopupSpellChk = null;


function SpellCheckSuggestion(TxtAryIndex, SpellWord, NoEdit) {
	if (!NoEdit) NoEdit = false;

	if (window.ActiveXObject) {
		oPopupSpellChk = window.createPopup();

		var oPopBody = oPopupSpellChk.document.body;
		oPopBody.style.border = "1px solid #8EBEE5";
		oPopBody.style.padding = "1px";
	} else {
		if (document.getElementById("PopUpBox")) {
			document.body.removeChild(ObjPopUpBox);
		}
	}

	ObjPopUpBox = document.createElement("div");

	var PopUpBoxHeight = 4;

	ObjCurrentWord = document.getElementById("SpellChkWord" + TxtAryIndex);
	var CurrentWordTxt = "";
	if (ObjCurrentWord.childNodes[0].tagName == "INPUT") {
		CurrentWordTxt = ObjCurrentWord.childNodes[0].value;
	} else {
		CurrentWordTxt = ObjCurrentWord.childNodes[0].data;
	}
	var FoundMatch = false;

	for (var i in SpellChkWords[SpellWord]) {
		if (i > 0 && CurrentWordTxt != SpellChkWords[SpellWord][i]) {
			PopUpBoxHeight += 25;
			ObjPopUpBoxItem = document.createElement("div");
			if (window.ActiveXObject) {
	 			ObjPopUpBoxItem.onclick = "parent.SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", " + i + ", " + NoEdit + "); parent.oPopupSpellChk.hide();";
				ObjPopUpBoxItem.onmouseover = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
				ObjPopUpBoxItem.onmouseout = "this.style.backgroundColor = ''; this.style.color = 'black';";
				ObjPopUpBoxItem.style.width = "100%";
			} else {
				var onClickFunc = "SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", " + i + ", " + NoEdit + "); document.body.removeChild(ObjPopUpBox);";
				ObjPopUpBoxItem.onclick = new Function(onClickFunc);
				var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
				ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
				var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
				ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
				ObjPopUpBoxItem.style.width = "165px";
			}
			ObjPopUpBoxItem.style.cursor = "default";
			ObjPopUpBoxItem.style.fontFamily = "Arial, Helvetica, sans-serif";
			ObjPopUpBoxItem.style.fontSize = "9pt";
			ObjPopUpBoxItem.style.padding = "5px";
			ObjPopUpBoxItem.appendChild(document.createTextNode(SpellChkWords[SpellWord][i]));
			ObjPopUpBox.appendChild(ObjPopUpBoxItem);
		} else if (CurrentWordTxt == SpellChkWords[SpellWord][i]) {
			FoundMatch = true;
		}
	}

	if (CurrentWordTxt == SpellChkWords[SpellWord][0] || FoundMatch == false) {
		if (SpellChkWords[SpellWord].length > 1) {
			PopUpBoxHeight += 9;
			ObjPopUpBoxItem = document.createElement("div");
			ObjPopUpBoxItem.style.width = "100%";
			ObjPopUpBoxItem.style.overflow = "hidden";
			ObjPopUpBoxItem.style.height = "5px";
			ObjPopUpBoxItem.style.cursor = "default";
			ObjPopUpBoxItem.style.borderTop = "1px solid silver";
			ObjPopUpBoxItem.style.marginTop = "4px";
			ObjPopUpBox.appendChild(ObjPopUpBoxItem);
		}

		PopUpBoxHeight += 25;
		ObjPopUpBoxItem = document.createElement("div");
		if (window.ActiveXObject) {
			ObjPopUpBoxItem.onclick = "parent.AddToDictionary(" + TxtAryIndex + "); parent.oPopupSpellChk.hide();";
			ObjPopUpBoxItem.onmouseover = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
			ObjPopUpBoxItem.onmouseout = "this.style.backgroundColor = ''; this.style.color = 'black';";
			ObjPopUpBoxItem.style.width = "100%";
		} else {
			var onClickFunc = "AddToDictionary(" + TxtAryIndex + "); document.body.removeChild(ObjPopUpBox);";
			ObjPopUpBoxItem.onclick = new Function(onClickFunc);
			var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
			ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
			var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
			ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
			ObjPopUpBoxItem.style.width = "165px";
		}
		ObjPopUpBoxItem.style.cursor = "default";
		ObjPopUpBoxItem.style.fontFamily = "Arial, Helvetica, sans-serif";
		ObjPopUpBoxItem.style.fontSize = "9pt";
		ObjPopUpBoxItem.style.padding = "5px";
		ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_AddWord));
		ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	}

	if (NoEdit != true) {
		PopUpBoxHeight += 9;
		ObjPopUpBoxItem = document.createElement("div");
		ObjPopUpBoxItem.style.width = "100%";
		ObjPopUpBoxItem.style.overflow = "hidden";
		ObjPopUpBoxItem.style.height = "5px";
		ObjPopUpBoxItem.style.cursor = "default";
		ObjPopUpBoxItem.style.borderTop = "1px solid silver";
		ObjPopUpBoxItem.style.marginTop = "4px";
		ObjPopUpBox.appendChild(ObjPopUpBoxItem);

		PopUpBoxHeight += 25;
		ObjPopUpBoxItem = document.createElement("div");
		if (window.ActiveXObject) {
			ObjPopUpBoxItem.onclick = "parent.SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", 'Edit'); parent.oPopupSpellChk.hide();";
			ObjPopUpBoxItem.onmouseover = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
			ObjPopUpBoxItem.onmouseout = "this.style.backgroundColor = ''; this.style.color = 'black';";
			ObjPopUpBoxItem.style.width = "100%";
		} else {
			var onClickFunc = "SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", 'Edit'); document.body.removeChild(ObjPopUpBox);";
			ObjPopUpBoxItem.onclick = new Function(onClickFunc);
			var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
			ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
			var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
			ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
			ObjPopUpBoxItem.style.width = "165px";
		}
		ObjPopUpBoxItem.style.cursor = "default";
		ObjPopUpBoxItem.style.fontFamily = "Arial, Helvetica, sans-serif";
		ObjPopUpBoxItem.style.fontSize = "9pt";
		ObjPopUpBoxItem.style.padding = "5px";
		ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Edit + " ..."));
		ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	}

	if (window.ActiveXObject) {
		oPopBody.innerHTML = ObjPopUpBox.innerHTML;
		oPopupSpellChk.show(MousePosXY[0], MousePosXY[1], 175, PopUpBoxHeight, document.body);
	} else {
		ObjPopUpBox.id = "PopUpBox";
		ObjPopUpBox.style.border = "1px solid #8EBEE5";
		ObjPopUpBox.style.padding = "1px";
		ObjPopUpBox.style.position = "absolute";
		ObjPopUpBox.style.width = "175px";
		ObjPopUpBox.style.height = PopUpBoxHeight + "px";
		ObjPopUpBox.style.top = MousePosXY[1];
		ObjPopUpBox.style.left = MousePosXY[0];
		ObjPopUpBox.style.backgroundColor = "white";
		document.body.appendChild(ObjPopUpBox);

		var onClickFunc = "document.body.removeChild(ObjPopUpBox); document.body.onclick = '';";
//		document.body.onclick = new Function(onClickFunc);
	}
}

function SpellChkFixWord(TxtAryIndex, SpellWord, SpellWordCount, ConvertFromEdit) {
	ObjSpellChkWord = document.getElementById("SpellChkWord" + TxtAryIndex);
	if (SpellWordCount == "Edit") {
		ObjSpellChkWord.onclick = "";
		//ObjSpellChkWord.oncontextmenu = "";
		ObjSpellChkWord.innerHTML = "<input id=\"SpellChkEdit" + TxtAryIndex + "\" type=\"text\" value=\"" + ObjSpellChkWord.innerHTML + "\" style=\"width: 75px;\">";
		ObjSpellChkEdit = document.getElementById("SpellChkEdit" + TxtAryIndex);
		ObjSpellChkEdit.style.border = "1px solid red";
		var OnDblClickFunc = "SpellCheckSuggestion(" + TxtAryIndex + ", " + SpellWord + ", true);";
		ObjSpellChkEdit.ondblclick = new Function(OnDblClickFunc);
		var OnFocusFunc = "FieldInFocus = true;";
		ObjSpellChkEdit.onfocus = new Function(OnFocusFunc);
		var OnBlurFunc = "FieldInFocus = false;";
		ObjSpellChkEdit.onblur = new Function(OnBlurFunc);
		var OnChangeFunc = "this.style.border = '1px solid green';";
		ObjSpellChkEdit.onchange = new Function(OnChangeFunc);
		ObjSpellChkEdit.focus();
	} else {
		ObjSpellChkWord.innerHTML = SpellChkWords[SpellWord][SpellWordCount];
		ObjSpellChkWord.style.color = "green";
		if (ConvertFromEdit == true) {
			var OnClickFunc = "SpellCheckSuggestion(" + TxtAryIndex + ", " + SpellWord + ");";
			ObjSpellChkWord.onclick = new Function(OnClickFunc);
			//ObjSpellChkWord.oncontextmenu = new Function(OnClickFunc);
		}
	}
}

var AddToDicReq = false;

function AddToDictionary(TxtAryIndex) {
	ObjAddWord = document.getElementById("SpellChkWord" + TxtAryIndex);
	ObjAddWord.style.color = "";
	ObjAddWord.style.cursor = "";
	ObjAddWord.style.textDecoration = "";
	var AddWord = "";
	if (ObjAddWord.childNodes[0].tagName == "INPUT") {
		AddWord = ObjAddWord.childNodes[0].value;
		ObjAddWord.replaceChild(document.createTextNode(AddWord), ObjAddWord.childNodes[0]);
	} else {
		AddWord = ObjAddWord.childNodes[0].data;
		ObjAddWord.onclick = "";
		//ObjAddWord.oncontextmenu = "return false;";
	}

	AddToDicReq = false;

	if (AddToDicReq && AddToDicReq.readyState < 4) AddToDicReq.abort();

	if (window.XMLHttpRequest) {
		AddToDicReq = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		AddToDicReq = new ActiveXObject("Microsoft.XMLHTTP");
	}

	AddToDicReq.onreadystatechange = AddToDicReqChange;
	AddToDicReq.open("GET", "spell.php?add=1&replace=" + encodeURIComponent(AddWord), true );
	AddToDicReq.send(null);
}

function AddToDicReqChange() {
	if (AddToDicReq.readyState == 4 && AddToDicReq.status == 200) {
		if (AddToDicReq.responseXML) {
			alert("Custom word has been added to the dictionary.");
		}
	}
}

function ToggleComposeMode(HTML) {

	if (document.getElementById("SpellCheckerBox").style.display == "") {
		alert("You can not change edit modes whilst using the spell checker.");
	} else {
		if (HTML == true && ComposeMode != "HTML") {
			ComposeMode = "HTML";
			ObjComposeMsgText = document.getElementById("ComposeMsgText");
			ObjComposeMsgText.value = ObjComposeMsgText.value.replace(/\r|\n/g, "<br>");
			oEdit1 = new InnovaEditor("oEdit1");
			oEdit1.width = "100%";
			if (window.ActiveXObject) {
				oEdit1.height = "100%";
			} else {
				oEdit1.height = (document.body.clientHeight - 88) + "px";
			}
			oEdit1.arrStyle = [["BODY",false,"","font-family:Arial, Helvetica, sans-serif;font-size:9pt;"]];
			oEdit1.btnStyles = true;
			oEdit1.dropTopAdjustment = 0;
			oEdit1.dropLeftAdjustment = 0;
			oEdit1.REPLACE("ComposeMsgText");
			oEdit1.fullScreen();
			oEdit1.fullScreen();
			oEdit1.focus();
		} else if (HTML == false && ComposeMode != "Text") {
			ComposeMode = "Text";
			ComposeModeStorage = oEdit1.getHTMLBody();
			oEdit1 = null;

			ObjMRTableTbodyTrTd = document.getElementById("ComposeMsgTextContainer");
			ObjMRTableTbodyTrTd.innerHTML = "";

			ObjMRTableTbodyTrTdDiv = document.createElement("div");
			ObjMRTableTbodyTrTdDiv.id = "SpellCheckerBox";
			ObjMRTableTbodyTrTdDiv.style.display = "none";
			ObjMRTableTbodyTrTdDiv.style.width = "100%";
			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdDiv.style.height = "100%";
			} else {
				ObjMRTableTbodyTrTdDiv.style.height = (document.body.clientHeight - 88) + "px";
			}
			ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);

			ObjMRTableTbodyTrTdTextArea = document.createElement("textarea");
			ObjMRTableTbodyTrTdTextArea.id = "ComposeMsgText";
			ObjMRTableTbodyTrTdTextArea.style.width = "100%";
			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdTextArea.style.height = "100%";
			} else {
				ObjMRTableTbodyTrTdTextArea.style.height = (document.body.clientHeight - 88) + "px";
			}
			ObjMRTableTbodyTrTdTextArea.style.border = "1px solid silver";
			ObjMRTableTbodyTrTdTextArea.style.overflow = "auto";
			ObjMRTableTbodyTrTdTextArea.style.fontFamily = "Arial, Helvetica, sans-serif";
			ObjMRTableTbodyTrTdTextArea.style.fontSize = "9pt";

			ObjTmpDiv = document.createElement("div");
			ObjTmpDiv.innerHTML = ComposeModeStorage;
			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdTextArea.value = ObjTmpDiv.innerText;
			} else {
				ObjMRTableTbodyTrTdTextArea.value = ObjTmpDiv.textContent;
			}

            ObjMRTableTbodyTrTdTextArea.value = ObjMRTableTbodyTrTdTextArea.value.replace(/\s*BODY \{.+?\}/, '');
            
			ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTextArea);
			ObjMRTableTbodyTrTdTextArea.focus();
		}
	}
}
