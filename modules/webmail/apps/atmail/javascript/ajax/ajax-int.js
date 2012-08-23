var VideoStreamUID = null;

var MousePosXY = new Array();
var MailType;

var BrowserVer = new BrowserVerChk();
var MsgArrayMove = new Array();
var unique;
var atmailRoot = '';
var MsgCacheLimit = 100;
var FieldInFocus = false;
var NewHeaderStyle = false;
var ComposeMode;
var ComposeModeStorage = null;
var oEdit1 = null;
var oPopup = null;

var ObjFolderBox;
var ObjMsgListBox;
var GlobalRow;
var Loaded;
var msgwinnum = 1;

window.onresize = FixResize;

// For login-light template
var AppColors = new Array();
//Border Background  Txt Background
AppColors["Private"] = new Array("#185aad", "#78aad7", "#d3e3f2");
//AppColors["Private"] = new Array("#185aad", "#719ed7", "#a3c2e9");
AppColors["Shared"] = new Array("#8000ff", "#b95aee", "#dc97ef");
var ColorScheme = "Private";

function FCKeditor_OnComplete( editorInstance )
{
   document.getElementById('ComposeMsgText___Frame').style.height = "500px";
}

function FixResize()	{
	centerObjWindow();
	var size = document.body.clientWidth;

}
var MainNav = new Array();
	MainNav["GrowTo"] = 12;
	MainNav["MainNavCompose"] = new Array();
	MainNav["MainNavCompose"]["EnlargeTimeout"] = null;
	MainNav["MainNavCompose"]["ShrinkTimeout"] = null;
	MainNav["MainNavCompose"]["Increment"] = 0;
	MainNav["MainNavCompose"]["Top"] = 118;
	MainNav["MainNavCompose"]["Left"] = 4;
	MainNav["MainNavCompose"]["Width"] = 51;
	MainNav["MainNavCompose"]["Height"] = 44;
	MainNav["MainNavCompose"]["SRC"] = "icon_compose";
	MainNav["MainNavChkMail"] = new Array();
	MainNav["MainNavChkMail"]["EnlargeTimeout"] = null;
	MainNav["MainNavChkMail"]["ShrinkTimeout"] = null;
	MainNav["MainNavChkMail"]["Increment"] = 0;
	MainNav["MainNavChkMail"]["Top"] = 94;
	MainNav["MainNavChkMail"]["Left"] = 57;
	MainNav["MainNavChkMail"]["Width"] = 64;
	MainNav["MainNavChkMail"]["Height"] = 44;
	MainNav["MainNavChkMail"]["SRC"] = "icon_chkmail";
	MainNav["MainNavSettings"] = new Array();
	MainNav["MainNavSettings"]["EnlargeTimeout"] = null;
	MainNav["MainNavSettings"]["ShrinkTimeout"] = null;
	MainNav["MainNavSettings"]["Increment"] = 0;
	MainNav["MainNavSettings"]["Top"] = 50;
	MainNav["MainNavSettings"]["Left"] = 99;
	MainNav["MainNavSettings"]["Width"] = 49;
	MainNav["MainNavSettings"]["Height"] = 44;
	MainNav["MainNavSettings"]["SRC"] = "icon_settings";
	MainNav["MainNavSearch"] = new Array();
	MainNav["MainNavSearch"]["EnlargeTimeout"] = null;
	MainNav["MainNavSearch"]["ShrinkTimeout"] = null;
	MainNav["MainNavSearch"]["Increment"] = 0;
	MainNav["MainNavSearch"]["Top"] = 3;
	MainNav["MainNavSearch"]["Left"] = 117;
	MainNav["MainNavSearch"]["Width"] = 51;
	MainNav["MainNavSearch"]["Height"] = 44;
	MainNav["MainNavSearch"]["SRC"] = "icon_search";

var MsgListData = new Array();
	MsgListData["Data"] = new Array();
	MsgListData["CurrentFolder"] = "";
	MsgListData["Folders"] = new Array();
	MsgListData["Views"] = new Array();
	MsgListData["Views"]["MsgListViewer"] = true;
	MsgListData["Views"]["MsgReader"] = false;
	MsgListData["Views"]["MsgComposer"] = false;
	MsgListData["Ctrl"] = new Array();
	MsgListData["Ctrl"]["Initialised"] = false;
	MsgListData["Ctrl"]["Timeout"] = null;
	MsgListData["Ctrl"]["Selected"] = new Array();
	MsgListData["Ctrl"]["SortCol"] = null;
	MsgListData["Ctrl"]["SortDescending"] = new Array();
	MsgListData["Ctrl"]["Loading"] = false;
	MsgListData["Ctrl"]["CtrlKey"] = false;
	MsgListData["Ctrl"]["ShiftKey"] = false;
	MsgListData["Ctrl"]["DnD"] = new Array();

var MsgReaderData = new Array();

function createXMLHttpRequest() {
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
	try { return new XMLHttpRequest(); } catch(e) {}
	alert("XMLHttpRequest not supported");
	return null;
}

function ShowEmailSentNotice(Hide) {
	document.getElementById("EmailSentNotice").style.display = (Hide) ? "none" : "";
	if (!Hide) window.setTimeout("ShowEmailSentNotice(true);", 5000);
}

// Ajax objects
var MessagesReq = false;
var AddAbookReq = false;
var BlockSenderReq = false;
var MoveMessagesReq = false;
var ReadMsgReq = false;
var SendMessagesReq = false;
var MarkMessageReq = false;
var WebMailLoginReq = false;

var ReadMsgFoldersLoaded = false;
var ReadMsgReply = null;
var AddToDicReq = false;

// Spell check vars
var SpellChkWords = new Array();
var AjustedTxtArray = new Array();
var SpellChkReq = false;

// Message row functions
var onClickFunc = "MsgRowCtrl(this.rowIndex, 'Click', this.id);";
var onDblClickFunc = "MsgRowCtrl(this.rowIndex, 'DblClick', this.id);";
var onMouseOverFunc = "MsgRowCtrl(this.rowIndex, 'Over');";
var onMouseOutFunc = "MsgRowCtrl(this.rowIndex, 'Out');";
var onMouseDownFunc = "MsgRowCtrl(this.rowIndex, 'Down');";
var onMouseUpFunc = "MsgRowCtrl(this.rowIndex, 'Up');";
var onMouseMoveFunc = "MsgRowCtrl(this.rowIndex, 'Move');";
var onContextMenuFunc = "LoadContextMenu;";

// Return null for onselect
function filtervs() { return false; }

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

var prevComposeHeight = 0;

// Calculate the height for the HTML editor in Firefox
function CalcMsgComposeHeight(live) {

	if(!live)
	live = '';

	// Don't run for IE, it supports height 100%
	if (window.ActiveXObject) {
		return;
	}

	// Find the height, minus our offset
	var ComposeHeight = window.innerHeight - 250;

	// Firefox should do height 100% - Calculate the offset below, temp workaround
	if(document.getElementById('ComposeMsgTo').style.height == '40px') {
		ComposeHeight = ComposeHeight - 20;
	}

	if(document.getElementById('ComposeMsgCc').style.height == '40px') {
		ComposeHeight = ComposeHeight - 20;
	}

	if(document.getElementById('ComposeMsgBcc').style.height == '40px') {
		ComposeHeight = ComposeHeight - 20;
	}

	// If BCC is enabled
	if(document.getElementById('ComposeMsgBcc').style.display == "")	{
		ComposeHeight = ComposeHeight - 30;
	}

	if(document.getElementById('ComposeMsgAttachmentsRow').style.display == "") {
		ComposeHeight = ComposeHeight - 30;
	}

	//if (ComposeHeight < 490) ComposeHeight = 400;

	if(live && document.getElementById("MsgComposer").style.display == "") {

	// Don't update if the same height
	if(prevComposeHeight == ComposeHeight)
	return;

	oEdit1.height = ComposeHeight + "px";
	//oEdit1.fullScreen();
	//oEdit1.fullScreen();
	prevComposeHeight  = ComposeHeight;
	} else {
	return ComposeHeight + "px";
	}

}

function CalcMsgRowHeight()	{

	// Compose page, resize the height for FF
	//if(document.getElementById("MsgComposer").style.display == "") {
	//oEdit1.height = CalcMsgComposeHeight();
	//oEdit1.fullScreen();
	//oEdit1.fullScreen();
	//oEdit1.focus();
	//}

	var MsgHeight = (document.body.offsetHeight - 155 - document.body.scrollTop);
	if (MsgHeight < 370) MsgHeight = 370;

	// Calculate our row height, set the scrollbars on
	MsgRowHeight = 18 * MsgListData["Ctrl"]["Increment"];

	if(MsgRowHeight > MsgHeight)
	ObjMsgListBox.style.overflowY = "scroll";
	else
	ObjMsgListBox.style.overflowY = "auto";

}

// Onresize event loader - Resize the content
function FixShowMail() {
	centerObjAdvancedWindow();

	if (navigator.userAgent.indexOf("Safari") != -1)	{
		var ReadWidth = document.body.offsetWidth - 190;
		ObjMsgListBox.style.width = ReadWidth + 'px';
	}

	if (!window.ActiveXObject) {
		document.getElementById("MsgListViewer").style.height = "";
		document.body.scrollTop = "99999";

    	// Show mail box
		var MsgHeight = (document.body.offsetHeight - 155 - document.body.scrollTop);
		if (MsgHeight < 370) MsgHeight = 370;
		ObjMsgListBox.style.height = MsgHeight + "px";

		// Read mail boxs
		var ReadHeight = (document.body.offsetHeight - 150 - document.body.scrollTop);
		if (ReadHeight < 500) ReadHeight = 500;
		document.getElementById("MsgReader").style.height = ReadHeight + "px";
		if (document.getElementById("MsgReaderData")) document.getElementById("MsgReaderData").style.height = (ReadHeight - 60) + "px";

		// Compose page
		CalcMsgRowHeight();

		document.body.scrollTop = "0";
	}


}

function GrowPNG(ImageID, Action) {
	if (Action == true) {
		window.clearTimeout(MainNav[ImageID]["ShrinkTimeout"]);
		MainNav[ImageID]["Increment"] ++;
	} else {
		window.clearTimeout(MainNav[ImageID]["EnlargeTimeout"]);
		MainNav[ImageID]["Increment"] --;
	}

	ObjImage = document.getElementById(ImageID);
	ObjImage.style.top = (MainNav[ImageID]["Top"] - (MainNav[ImageID]["Increment"] / 2)) + "px";
	ObjImage.style.left = (MainNav[ImageID]["Left"] - (MainNav[ImageID]["Increment"] / 2)) + "px";
	ObjImage.style.width = (MainNav[ImageID]["Width"] + MainNav[ImageID]["Increment"]) + "px";
	ObjImage.style.height = (MainNav[ImageID]["Height"] + MainNav[ImageID]["Increment"]) + "px";

	if (!window.ActiveXObject) {
		ObjImage = document.getElementById(ImageID + "Img");
		ObjImage.style.width = MainNav[ImageID]["Width"] + MainNav[ImageID]["Increment"];
		ObjImage.style.height = MainNav[ImageID]["Height"] + MainNav[ImageID]["Increment"];
	}

	if (Action == true && MainNav[ImageID]["Increment"] < MainNav["GrowTo"]) {
		MainNav[ImageID]["EnlargeTimeout"] = window.setTimeout("GrowPNG('" + ImageID + "', true)", 0);
	} else if (Action == true) {
		if (window.ActiveXObject) {
			ObjImage.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/" + MainNav[ImageID]["SRC"] + "_big.png', sizingMethod='scale');";
		} else {
			ObjImage.src = "imgs/simple/" + MainNav[ImageID]["SRC"] + "_big.png";
		}
	}

	if (Action == false && MainNav[ImageID]["Increment"] > 0) {
		MainNav[ImageID]["ShrinkTimeout"] = window.setTimeout("GrowPNG('" + ImageID + "', false)", 10);
	} else if (Action == false) {
		if (window.ActiveXObject) {
			ObjImage.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/" + MainNav[ImageID]["SRC"] + ".png', sizingMethod='scale');";
		} else {
			ObjImage.src = "imgs/simple/" + MainNav[ImageID]["SRC"] + ".png";
		}
	}
}

function MultiSelectDown(e) {
	var EventTest = window.event ? event : e;
	var WhichKey = EventTest.charCode? EventTest.charCode : EventTest.keyCode;
	// Turn off for compose panel
	if(document.getElementById("MsgComposer").style.display == "")
		return WhichKey;
// If We are using Safari, the keycodes are different
//if(navigator.userAgent.indexOf("Safari") == -1)	{
			if(EventTest.shiftKey && !WhichKey)	{
				WhichKey = 16;
			} else if(EventTest.altKey && !WhichKey)	{
				WhichKey = 17;
			} else	{
				MsgListData["Ctrl"]["ShiftKey"] = false;
				MsgListData["Ctrl"]["CtrlKey"] = false;
			}
	if (WhichKey && FieldInFocus == false) {

		if (WhichKey == 16) { // Capture and remap Shift
			MsgListData["Ctrl"]["ShiftKey"] = true;
		} else if (WhichKey == 17) { // Capture and remap Ctrl
			MsgListData["Ctrl"]["CtrlKey"] = true;
		} else if ( WhichKey == 8 && FieldInFocus == false) { // Capture and remap backspace if not in text field
			WhichKey = 505;
		}

		if (WhichKey != 505) {
			WhichKey = ListBoxKeyCtrl(WhichKey);
		}

		if (WhichKey == 505) {
			return false;
		}
	}
}

function MultiSelectUp(e) {
	var EventTest = window.event ? event : e;
	var WhichKey = EventTest.charCode? EventTest.charCode : EventTest.keyCode;

	if (WhichKey && FieldInFocus == false) {
		if (WhichKey == 16) { // Capture and remap Shift
			MsgListData["Ctrl"]["ShiftKey"] = false;
		} else if (WhichKey == 17) { // Capture and remap Ctrl
			MsgListData["Ctrl"]["CtrlKey"] = false;
		}
	}
}

function NoSelectText() {
	if (FieldInFocus == false) return false;
}

function SelectText() {
	return true;
}

var ChkMailTimeout = null;
var LastUnreadMsgCount = 999999;
var PlaySound = false;

function ChMailInterval() {

	// We are are viewing the calendar, do not show! Breaks the calendar UI
	try {
		if(CalendarInOperation == true)	{
			ChkMailTimeout = window.setTimeout("ChMailInterval()", MailRefreshTime);
			return;
		}

	} catch(e) { }

	// Reload the mailbox if no messages are selected to move, and the results are not the search window Trac #671 issue
	if (MsgListData["Ctrl"]["Initialised"] == true && MsgListData["Ctrl"]["Loading"] == false && MsgListData["Views"]["MsgListViewer"] == true && MsgListData["Ctrl"]["Selected"].length == 0 && MsgListData["Ctrl"]["CtrlKey"] == false && MsgListData["Ctrl"]["ShiftKey"] == false && MsgListData["CurrentFolder"] != 'Search') {
		PlaySound = true;
		LoadMsgs(MsgListData["CurrentFolder"]);
		ChkMailTimeout = window.setTimeout("ChMailInterval()", MailRefreshTime);
	} else {
		ChkMailTimeout = window.setTimeout("ChMailInterval()", 60000);
	}
}

var ObjFadeWindow = null;

function PageLoaded(func, To, Cc, Bcc) {
	if (func == "login") {
		ObjFadeWindow = createObjFadeWindow();
		document.body.appendChild(ObjFadeWindow);
	}

	if (MailRefreshTime < 60) MailRefreshTime = 60;
	MailRefreshTime = MailRefreshTime * 1000;

	ChkMailTimeout = window.setTimeout("ChMailInterval()", MailRefreshTime);

	document.body.onmousemove = MousePos;
	// Detect which Compose editor we are using ( HTML or Plain ) from the settings panel
	if(!allow_HtmlEditor)	{
		ComposeMode = "Text";
	} else if (document.getElementById("HtmlEditor").value == 1)	{
		ComposeMode = "HTML";
	} else	{
		ComposeMode = "Text";
	}

	// Get which MailType we are ( POP3, IMAP, SQL, FILE )
	MailType = document.getElementById("MailType").value;

	// If we are first load from the login page, toggle showmail.php to query a list of IMAP servers
	var FolderLoad = document.getElementById("FolderLoad").value;
	if(FolderLoad == '1')
	document.getElementById("FolderLoad").value = 0;

	ObjMsgListBox = document.getElementById("MsgListBox");
	liveSearchInit();

	if (func == 'login') {
		LoadLoginPage();
	} else if(func == 'Compose')	{
		LoadMsgs();
		ComposeMsg('', To, Cc, Bcc);
	} else if(func == 'Search')	{
		LoadMsgs();
		ToggleSearchRow();
	} else if(To)	{
		LoadMsgs(To);
	} else if(func == 'LoadCalendar')	{
		LoadMsgs();
		Loaded = "Calendar";
	} else {
		LoadMsgs('', '', FolderLoad);
	}
}

function DataIsLoading(ToDo, Message) {
	if (!Message) {
		Message = "Connecting";
		document.getElementById('Connecting').innerText = Message;
		document.body.style.cursor = 'wait';
	}

	if (ToDo == true) {
		setOpacity(document.getElementById("Connecting"), "100");
		setOpacity(document.getElementById("LoadingImage"), "100");

		document.getElementById("LoadingText").style.display = "";
		document.getElementById("LoadingIcon").style.display = "";
		document.getElementById("BrandingLogo").style.display = "none";

	} else {
		LoadingFade();
	}
}

function LoadMsgs(Folder, Start, FolderLoad) {
	if(!FolderLoad)
		FolderLoad = 0;


	// Only capture these events on the showmail panel
	document.onkeydown = MultiSelectDown;

	if(navigator.userAgent.indexOf("Safari") != -1)	{
	document.onmousemove = MultiSelectDown;
	}

	document.onkeyup = MultiSelectUp;
	document.onselectstart = NoSelectText;

	// Check we are within the Ajax frame
	if(TestAjaxFrameNull()) return;

	// If we are using firefox, disable selection so users can toggle rows
	document.body.setAttribute("style","-moz-user-select: none;");

	if (MsgListData["CurrentFolder"] != Folder) LastUnreadMsgCount = 999999;

	if (MsgListData["Views"]["MsgListViewer"] == true || MsgListData["CurrentFolder"] != Folder) {
		DataIsLoading(true);

		window.clearTimeout(MsgListData["Ctrl"]["Timeout"]);

		if (!Folder) Folder = "Inbox";
		if (!Start || Start < 0) Start = 0;

		MsgListData["CurrentFolder"] = Folder;

		MessagesReq = false;

		if (MessagesReq && MessagesReq.readyState < 4) MessagesReq.abort();

		MessagesReq = createXMLHttpRequest();

		MessagesReq.onreadystatechange = MessagesReqChange;
		if (Folder == "Search") {
			var SearchFrom = document.getElementById("SearchFrom").value;
			var SearchTo = document.getElementById("SearchTo").value;
			var SearchSubject = document.getElementById("SearchSubject").value;
			var SearchMessage = document.getElementById("SearchMessage").value;

			var EmailAttach = '';
			if(document.getElementById("SearchAttachments").checked == true)
				EmailAttach = '1';

			var EmailFlag = '';
			if(document.getElementById("SearchFlagged").checked == true)
				EmailFlag = '1';

			var SearchLocation = document.getElementById("SearchLocation").options[document.getElementById("SearchLocation").selectedIndex].value;

			// Get message dates, before
			var SearchBeforeDay = document.getElementById("SearchBeforeDay").options[document.getElementById("SearchBeforeDay").selectedIndex].value || '';
			var SearchBeforeMonth = document.getElementById("SearchBeforeMonth").options[document.getElementById("SearchBeforeMonth").selectedIndex].value || '';
			var SearchBeforeYear = document.getElementById("SearchBeforeYear").options[document.getElementById("SearchBeforeYear").selectedIndex].value || '';

			// Get message dates, after
			var SearchAfterDay = document.getElementById("SearchAfterDay").options[document.getElementById("SearchAfterDay").selectedIndex].value || '';
			var SearchAfterMonth = document.getElementById("SearchAfterMonth").options[document.getElementById("SearchAfterMonth").selectedIndex].value || '';

			var SearchAfterYear = document.getElementById("SearchAfterYear").options[document.getElementById("SearchAfterYear").selectedIndex].value || '';

			if (SearchFrom || SearchTo || SearchSubject || SearchMessage || EmailAttach || EmailFlag || SearchAfterYear || SearchAfterMonth || SearchAfterDay ) {
				ToggleSearchRow(true);
				MessagesReq.open("GET", "search.php?ajax=1&EmailFrom=" + encodeURIComponent(SearchFrom) + "&EmailTo=" + encodeURIComponent(SearchTo) + "&EmailSubject=" + encodeURIComponent(SearchSubject) + "&EmailMessage=" + encodeURIComponent(SearchMessage) + "&EmailBox=" + encodeURIComponent(SearchLocation) + "&EmailAttach=" + encodeURIComponent(EmailAttach) + "&EmailFlag=" + encodeURIComponent(EmailFlag) + "&BeforeDay=" + encodeURIComponent(SearchBeforeDay) + "&BeforeMonth=" + encodeURIComponent(SearchBeforeMonth) + '&BeforeYear=' + encodeURIComponent(SearchBeforeYear) + "&AfterDay=" + encodeURIComponent(SearchAfterDay) + "&AfterMonth=" + encodeURIComponent(SearchAfterMonth) + "&AfterYear=" + encodeURIComponent(SearchAfterYear) + "&start=" + encodeURIComponent(Start) + "&func=start", true);
				MessagesReq.send(null);
			} else {
				MessagesReq.abort();
				DataIsLoading(false);
				alert("You must first enter in your search criteria.");
			}
		} else {
			ClearListBoxData();
			MessagesReq.open("GET", atmailRoot + "showmail.php?ajax=1&Folder=" + encodeURIComponent(Folder) + "&start=" + encodeURIComponent(Start) + "&LoadFolder=" + encodeURIComponent(FolderLoad), true );
			MessagesReq.send(null);

			if(Folder == 'Sent')	{
			document.getElementById('FromToField').innerHTML = Lang_To;
			} else	{
			document.getElementById('FromToField').innerHTML = Lang_From;
			}


		}
	}
	if (MsgListData["Views"]["MsgListViewer"] == false) {
		document.getElementById("MsgListViewer").style.display = "";
		MsgListData["Views"]["MsgListViewer"] = true;
		document.getElementById("MsgReader").style.display = "none";
		MsgListData["Views"]["MsgReader"] = false;
		document.getElementById("MsgComposer").style.display = "none";
		MsgListData["Views"]["MsgComposer"] = false;
	}

	atmailRoot = '';
}

function MessagesReqChange() {
	if (MessagesReq.readyState == 4 && MessagesReq.status == 200) {
		if ( MessagesReq.responseXML && CheckXMLError(MessagesReq) ) {
			// Refresh our moved message array, since indexes will change
			MsgArrayMove = new Array();
			// We are the search results, just push the last array
			try
			{
				if(MessagesReq.responseXML.getElementsByTagName("Fol").length > 1)	{
					MsgListData["Folders"].length = 0;

				for (var i = 0; i < MessagesReq.responseXML.getElementsByTagName("Fol").length; i ++) {
					MsgListData["Folders"][i] = new Array();
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[i].getAttribute("Name"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[i].getAttribute("Icon"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[i].getAttribute("Count"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[i].getAttribute("State"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[i].getAttribute("Display"));
				}

				} else	{
					var i = MsgListData["Folders"].length;

					if(MsgListData["Folders"][i-1][0] == 'Search')
						i--;

					MsgListData["Folders"][i] = new Array();
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[0].getAttribute("Name"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[0].getAttribute("Icon"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[0].getAttribute("Count"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[0].getAttribute("State"));
					MsgListData["Folders"][i].push(MessagesReq.responseXML.getElementsByTagName("Fol")[0].getAttribute("Display"));
				}

				if (document.getElementById("MsgListViewer").style.display == "") LoadFolders();

				ClearListBoxData();

			}
			catch (e)
			{
			    // check for session timeout
    			try
    			{
    			    if (MessagesReq.responseXML.getElementsByTagName('error')[0].firstChild.data == 2) {
    			        alert('Your Session Has Timed Out');
    			        document.location = 'index.php';
    			        return;
    			    }
    			} catch (e) {}

				alert('Message loading failed - Please check the remote mail-server is responding correctly, remote mail-server online, no network timeouts, authentication error or mailbox lock');
			}

			for (var i = 0; i < MessagesReq.responseXML.getElementsByTagName("Msg").length; i ++) {
				MsgListData["Data"][i] = new Array();
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("ID"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Folder")[i].firstChild.data);
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Subject")[i].firstChild.data);
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("From")[i].firstChild.data);
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("Attach"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("Epoch"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("Date"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("Priority"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("Size"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("MsgIcon"));
				if (IsMsgLoaded(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("ID")) != "false") {
					MsgListData["Data"][i].push(true);
				} else {
					MsgListData["Data"][i].push(false);
				}
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("SizeRaw"));
				MsgListData["Data"][i].push(MessagesReq.responseXML.getElementsByTagName("Msg")[i].getAttribute("EmailCache"));
			}

			ObjMsgPageListRow = document.getElementById("MsgPageListRow");
			var MsgNavTotal = MessagesReq.responseXML.getElementsByTagName("MsgNav")[0].getAttribute("Total") * 1;
			var MsgNavView = MessagesReq.responseXML.getElementsByTagName("MsgNav")[0].getAttribute("View") * 1;
			var MsgNavStart = MessagesReq.responseXML.getElementsByTagName("MsgNav")[0].getAttribute("Start") * 1;

			// Make a master array with our folders, to calculate msg-id sequence when emails are moved
			for(var i = 1; i <= MsgNavTotal; i++)	MsgArrayMove.push(i);

			var ViewingTo = MsgNavStart + MsgNavView;
			if (ViewingTo > MsgNavTotal) ViewingTo = MsgNavTotal;
			document.getElementById("TotalMsgsStatus").innerHTML = Lang_Viewing + " " + ( MsgNavStart || '1') + " " + Lang_To.toLowerCase() + " " + ViewingTo + " " + Lang_Of + " " + MsgNavTotal + " " + Lang_Messages;

			document.getElementById("MsgPageListPrevious").style.display = "none";
			document.getElementById("MsgPageListPrevious").title = Lang_PrevMsg;

			document.getElementById("MsgPageListNext").style.display = "none";
			document.getElementById("MsgPageListNext").title = Lang_NextMsg;

			if (MsgNavStart > 0) {
				document.getElementById("MsgPageListPrevious").style.display = "";
			}
			if (MsgNavStart + MsgNavView < MsgNavTotal) {
				document.getElementById("MsgPageListNext").style.display = "";
			}


			if (MsgNavTotal > MsgNavView) {
				ObjMsgPageListRow.style.display = "";
				ObjMsgPageListSelect = document.getElementById("MsgPageList");
				ObjMsgPageListSelect.length = 0;

				var HowManyPages = Math.ceil(MsgNavTotal / MsgNavView);
				var LoopFrom = (MsgNavStart / MsgNavView) - 1;
				var LoopTo = LoopFrom + 7;
				if (LoopTo > HowManyPages) {
					LoopFrom = HowManyPages - 8;
					LoopTo = HowManyPages;
				}
				if (LoopFrom < 0) LoopFrom = 0;

				if (LoopFrom > 0) {
						ObjMsgPageListSelectOption.className = "ObjMsgPageListSelectOption";

					ObjMsgPageListSelectOption = document.createElement("option");
					ObjMsgPageListSelectOption.value = 0;
					ObjMsgPageListSelectOption.appendChild(document.createTextNode(Lang_First + " " + Lang_Page + " 1"));
					ObjMsgPageListSelect.appendChild(ObjMsgPageListSelectOption);

					ObjMsgPageListSelectOption = document.createElement("option");
					ObjMsgPageListSelectOption.value = 0;
					ObjMsgPageListSelectOption.appendChild(document.createTextNode("-------------"));
					ObjMsgPageListSelect.appendChild(ObjMsgPageListSelectOption);
				}

				for (var i = LoopFrom; i < LoopTo; i++) {
					ObjMsgPageListSelectOption = document.createElement("option");
					ObjMsgPageListSelectOption.value = i * MsgNavView;
					if ((MsgNavView * i) == MsgNavStart) {
						ObjMsgPageListSelectOption.selected = 1;
					} else {
						ObjMsgPageListSelectOption.className = "ObjMsgPageListSelectOption";
					}
					ObjMsgPageListSelectOption.appendChild(document.createTextNode(Lang_Page + " " + (i + 1)));
					ObjMsgPageListSelect.appendChild(ObjMsgPageListSelectOption);
				}

				if (HowManyPages > 8 && LoopTo < HowManyPages) {
					ObjMsgPageListSelectOption = document.createElement("option");
					ObjMsgPageListSelectOption.value = (HowManyPages - 1) * MsgNavView;
					ObjMsgPageListSelectOption.appendChild(document.createTextNode("-------------"));
					ObjMsgPageListSelectOption.className = "ObjMsgPageListSelectOption";
					ObjMsgPageListSelect.appendChild(ObjMsgPageListSelectOption);

					ObjMsgPageListSelectOption = document.createElement("option");
					ObjMsgPageListSelectOption.value = (HowManyPages - 1) * MsgNavView;
					ObjMsgPageListSelectOption.appendChild(document.createTextNode(Lang_Last + " " + Lang_Page + " " + HowManyPages));
					ObjMsgPageListSelectOption.className = "ObjMsgPageListSelectOption";
					ObjMsgPageListSelect.appendChild(ObjMsgPageListSelectOption);
				}

				ObjMsgPageListRow.style.display = "";
			} else {
				ObjMsgPageListRow.style.display = "none";
			}

			DataIsLoading(false);
			SortMsgsBy();
		}
	}

	if(Loaded == 'Calendar')	{
		setTimeout("LoadCalendar()", 5000);
		Loaded = '';
	}
}

function BlockSender() {
	DataIsLoading(true);

	BlockSenderReq = false;

	if (BlockSenderReq && BlockSenderReq.readyState < 4) BlockSenderReq.abort();

	BlockSenderReq = createXMLHttpRequest();

	BlockSenderReq.onreadystatechange = BlockSenderReqChange;
	var AddRecipients = "";
	for (var i in MsgListData["Ctrl"]["Selected"]) {
		AddRecipients += escape(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][3]) + ",";
	}
	AddRecipients = AddRecipients.substring(0, AddRecipients.length - 1);

	var LocalAccount = document.getElementById('LocalAccount').value;

	if(LocalAccount)	{
 		BlockSenderReq.open("GET", "util.php?func=spamsettings&Filter=1&Header=blacklist_from&Type=1&Add=Add&Refresh=1&Value=" + AddRecipients, true);
 		BlockSenderReq.send(null);

	} else	{
 		BlockSenderReq.open("GET", "util.php?func=info&spamadd=1&SpamEmail=" + AddRecipients, true);
 		BlockSenderReq.send(null);
	}

}

function BlockSenderReqChange() {
	if (BlockSenderReq.readyState == 4 && BlockSenderReq.status == 200) {

		if (BlockSenderReq.responseText) {
			DataIsLoading(false);
			alert(Lang_BlackListAdded);
		}
	}
}

function AddAbook() {
	DataIsLoading(true);

	AddAbookReq = false;

	if (AddAbookReq && AddAbookReq.readyState < 4) AddAbookReq.abort();

	AddAbookReq = createXMLHttpRequest();

	AddAbookReq.onreadystatechange = AddAbookReqChange;
	var AddRecipients = "";
	for (var i in MsgListData["Ctrl"]["Selected"]) {
		AddRecipients += escape(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][3]) + ",";
	}
	AddRecipients = AddRecipients.substring(0, AddRecipients.length - 1);
	AddAbookReq.open("GET", "abook.php?func=quicksearch&add=1&AddRecipients=" + AddRecipients, true);
	AddAbookReq.send(null);
}

function AddAbookReqChange() {
	if (AddAbookReq.readyState == 4 && AddAbookReq.status == 200) {
		if (AddAbookReq.responseXML) {
			DataIsLoading(false);
			alert(Lang_AbookAdded);
		}
	}
}

function PrintEmail() {
	ShowMsg = IsMsgLoaded(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]);

  ObjReadMsgInfoTable = document.getElementById("ReadMsgInfoTableHeading");
  ObjReadMsgInfoTableCopy = ObjReadMsgInfoTable.cloneNode(true);

	ObjMsgData = document.createElement("div");
	ObjMsgData.appendChild(ObjReadMsgInfoTableCopy);

	win = open("html/blankiframe.html", "_blank");
	win.document.open();
	win.document.write("<HTML><BODY onload='setTimeout(\"print();\", 1000)'><table width='100%' cellpadding='2' cellspacing='2'><tr><td><input type=button name=Close value=Close onclick='window.close()' style='' id='closebutton'></td><td align='right'>" + document.getElementById('BrandingLogo').innerHTML + "</td></tr></table>" + ObjMsgData.innerHTML + "<link rel='stylesheet' href='html/ajax-int.css' type='text/css'><style> BODY { font-family:Arial, Helvetica, sans-serif;font-size:9pt;color:#000000; padding: 5px;} </style><br>" + MsgReaderData[ShowMsg][8] + "</BODY></HTML>");
	win.document.close();
}

function ViewHeaders() {
	ObjMRTableTbodyHeadersRow = document.getElementById("ReadMsgInfoTableTbodyHeadersRow");

	if (ObjMRTableTbodyHeadersRow) {
		ObjMRTableTbodyHeadersRow.parentNode.removeChild(ObjMRTableTbodyHeadersRow);
	} else {
		ShowMsg = IsMsgLoaded(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]);

		var MsgHeader = MsgReaderData[ShowMsg][15].split("<br>");
		for (var i in MsgHeader) {
			MsgHeader[i] = MsgHeader[i].replace(/\s+/gi, " ");
		}

		ObjMRTableTbody = document.getElementById("ReadMsgInfoTableTbody");
		ObjMRTableTbodyFirstRow = document.getElementById("ReadMsgInfoTableTbodyFirstRow");

		ObjMRTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTr.id = "ReadMsgInfoTableTbodyHeadersRow";

		ObjMRTableTbodyTrTd = document.createElement("td");
		if (MsgReaderData[ShowMsg][17] != "") ObjMRTableTbodyTrTd.colSpan = "2";
		if (NewHeaderStyle == true) ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd";

		ObjMRTableTbodyTrTdTable = document.createElement("table");
		ObjMRTableTbodyTrTdTable.width = "100%";
		ObjMRTableTbodyTrTdTable.height = "100%";
		if (NewHeaderStyle == true) {
			ObjMRTableTbodyTrTdTable.cellSpacing = "5";
			ObjMRTableTbodyTrTdTable.cellPadding = "0";
		} else {
			ObjMRTableTbodyTrTdTable.cellSpacing = "0";
			ObjMRTableTbodyTrTdTable.cellPadding = "5";
		}
		ObjMRTableTbodyTrTdTable.border = "0";
		ObjMRTableTbodyTrTdTable.style.tableLayout = "fixed";
		ObjMRTableTbodyTrTdTableTbody = document.createElement("tbody");

		// Start New Msg Headers Row
		ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");

		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd7";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Headers + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);

		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd8";

		ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "3";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv = document.createElement("div");
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.className = "ObjMRTableTbodyTrTdTableTbodyTrTdDiv9";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.innerHTML = MsgHeader.join("<br>");

		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdDiv);
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);
		ObjMRTableTbodyTrTdTable.appendChild(ObjMRTableTbodyTrTdTableTbody);
		ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTable);
		ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

		ObjMRTableTbody.insertBefore(ObjMRTableTbodyTr, ObjMRTableTbodyFirstRow);
	}
}

function LoadMsgListBox(Increment) {
	MsgListData["Ctrl"]["Increment"] = Increment;

	if (MsgListData["Ctrl"]["Initialised"] == false) {
		MsgListData["Ctrl"]["Initialised"] = true;
		MsgListData["Ctrl"]["Loading"] = true;

		if(navigator.userAgent.indexOf("Safari") == -1)	ObjMsgListBox.style.width = "100%";

		ObjMsgListBox.style.height = "100%";
		FixShowMail();

		ObjMsgListBox.style.overflowY = "hidden";
		ObjMsgListBox.style.overflowX = "hidden";

		ObjMLTable = document.createElement("table");
		ObjMLTable.width = "100%";
		ObjMLTable.cellSpacing = "0";
		ObjMLTable.cellPadding = "1";
		ObjMLTable.border = "0";
		ObjMLTable.style.tableLayout = "fixed";
		ObjMsgListBox.appendChild(ObjMLTable);

		ObjMLTableTbody = document.createElement("tbody");
		ObjMLTable.appendChild(ObjMLTableTbody);
	}

	// Calculate our row height, set the scrollbars on
	var MsgHeight = (document.body.offsetHeight - 155 - document.body.scrollTop);
	MsgRowHeight = 23 * Increment;

	if (MsgListData["CurrentFolder"] == 'Search') {
	   MsgHeight -= 43;
	}

	if(MsgRowHeight > MsgHeight && ObjMsgListBox.style.overflowY != "scroll")
	ObjMsgListBox.style.overflowY = "scroll";

	ObjMLTableTbodyTr = document.createElement("tr");
	// If the folder contains messages, not a "folder has no messages" warning
	if(MsgListData["Data"][Increment][9] != 'object_close')	{
		ObjMLTableTbodyTr.onclick = new Function(onClickFunc);
		ObjMLTableTbodyTr.ondblclick = new Function(onDblClickFunc);
		ObjMLTableTbodyTr.onmouseover = new Function(onMouseOverFunc);
		ObjMLTableTbodyTr.onmouseout = new Function(onMouseOutFunc);
		ObjMLTableTbodyTr.onmousedown = new Function(onMouseDownFunc);
		ObjMLTableTbodyTr.onmouseup = new Function(onMouseUpFunc);
		ObjMLTableTbodyTr.onmousemove = new Function(onMouseMoveFunc);

		// Need to call oncontextmenu directly, otherwise e blank
		ObjMLTableTbodyTr.oncontextmenu = LoadContextMenu;
		ObjMLTableTbodyTr.className = "ObjMLTableTbodyTr";
	}

	ObjMLTableTbody.appendChild(ObjMLTableTbodyTr);

	// Loaded Status
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.title = Lang_CacheStatus;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv3";
	ObjMLTableTbodyTrTdImg = document.createElement("img");
	ObjMLTableTbodyTrTdImg.id = "ListBoxMsgLoadedIcon" + ObjMLTableTbodyTr.rowIndex;

	// Need to save the reference of the name above, otherwise we can't access the icon if msg sequence changes
	ObjMLTableTbodyTr.id = ObjMLTableTbodyTr.rowIndex;

	if (MsgListData["Data"][Increment][10] == true) {
		if (window.ActiveXObject) {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
			ObjMLTableTbodyTrTdImg.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_msg_loaded.png', sizingMethod='image')";
		} else {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/icon_msg_loaded.png";
		}
	} else {
		if (window.ActiveXObject) {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
			ObjMLTableTbodyTrTdImg.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_msg_notloaded.png', sizingMethod='image')";
		} else {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/icon_msg_notloaded.png";
		}
	}
	ObjMLTableTbodyTrTdImg.width = "15";
	ObjMLTableTbodyTrTdImg.height = "14";
	ObjMLTableTbodyTrTdImg.border = "0";
	ObjMLTableTbodyTrTdDiv.appendChild(ObjMLTableTbodyTrTdImg);
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// Attachment
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.title = Lang_DoubleClick;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv4";
	ObjMLTableTbodyTrTdImg = document.createElement("img");
	if (MsgListData["Data"][Increment][4] > 0) {
		if (window.ActiveXObject) {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/attachment.gif";
		} else {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/icon_attachment.png";
		}
	} else {
		ObjMLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
	}
	ObjMLTableTbodyTrTdImg.width = "12";
	ObjMLTableTbodyTrTdImg.height = "14";
	ObjMLTableTbodyTrTdImg.border = "0";
	ObjMLTableTbodyTrTdDiv.appendChild(ObjMLTableTbodyTrTdImg);
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// MsgIcon
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.title = Lang_DoubleClick;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv5";
	ObjMLTableTbodyTrTdImg = document.createElement("img");
	ObjMLTableTbodyTrTdImg.id = "ListBoxMsgIcon" + ObjMLTableTbodyTr.rowIndex;
	if (window.ActiveXObject) {
		ObjMLTableTbodyTrTdImg.src = "imgs/xp/" + MsgListData["Data"][Increment][9] + ".gif";
	} else {
		if (MsgListData["Data"][Increment][9] != 'move') {
			ObjMLTableTbodyTrTdImg.src = "imgs/simple/icon_" + MsgListData["Data"][Increment][9] + ".png";
		} else {
			ObjMLTableTbodyTrTdImg.src = "imgs/xp/" + MsgListData["Data"][Increment][9] + ".gif";
		}
	}

	if( MsgListData["Data"][Increment][9] == 'object_close' )	{
		ObjMLTableTbodyTrTdImg.width = "13";
		ObjMLTableTbodyTrTdImg.height = "13";
		ObjMLTableTbodyTrTdImg.border = "0";
	} else {
		ObjMLTableTbodyTrTdImg.width = "16";
		ObjMLTableTbodyTrTdImg.height = "14";
		ObjMLTableTbodyTrTdImg.border = "0";
	}

	ObjMLTableTbodyTrTdDiv.appendChild(ObjMLTableTbodyTrTdImg);
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// From
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd2";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.id = "ListBoxMsgFrom" + ObjMLTableTbodyTr.rowIndex;
	ObjMLTableTbodyTrTdDiv.title = Lang_DoubleClick;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv2";
	if (MsgListData["Data"][Increment][9] == "unread") ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDivBold";
	ObjMLTableTbodyTrTdDiv.appendChild(document.createTextNode(MsgListData["Data"][Increment][3]));
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// Subject
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd3";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.id = "ListBoxMsgSubject" + ObjMLTableTbodyTr.rowIndex;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv2";
	ObjMLTableTbodyTrTdDiv.title = Lang_DragDrop;
	ObjMLTableTbodyTrTdDiv.style.padding = "2px 4px 2px 15px";
	if (MsgListData["Data"][Increment][9] == "unread") ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDivBold";
	ObjMLTableTbodyTrTdDiv.appendChild(document.createTextNode(MsgListData["Data"][Increment][2]));
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// Date
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd2";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.id = "ListBoxMsgDate" + ObjMLTableTbodyTr.rowIndex;
	ObjMLTableTbodyTrTdDiv.title = Lang_DragDrop;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv2";
	ObjMLTableTbodyTrTdDiv.style.padding = "2px 4px 2px 30px";
	if (MsgListData["Data"][Increment][9] == "unread") ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDivBold";
	ObjMLTableTbodyTrTdDiv.appendChild(document.createTextNode(MsgListData["Data"][Increment][6]));
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	// Size
	ObjMLTableTbodyTrTd = document.createElement("td");
	ObjMLTableTbodyTrTd.className = "ObjMLTableTbodyTrTd4";
	ObjMLTableTbodyTrTdDiv = document.createElement("div");
	ObjMLTableTbodyTrTdDiv.title = Lang_DragDrop;
	ObjMLTableTbodyTrTdDiv.id = "ListBoxMsgSize" + ObjMLTableTbodyTr.rowIndex;
	ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDiv";
	if (MsgListData["Data"][Increment][9] == "unread") ObjMLTableTbodyTrTdDiv.className = "ObjMLTableTbodyTrTdDivBoldSize";
	ObjMLTableTbodyTrTdDiv.appendChild(document.createTextNode(MsgListData["Data"][Increment][8]));
	ObjMLTableTbodyTrTd.appendChild(ObjMLTableTbodyTrTdDiv);
	ObjMLTableTbodyTr.appendChild(ObjMLTableTbodyTrTd);

	if ((Increment + 1) < MsgListData["Data"].length) {
		MsgListData["Ctrl"]["Timeout"] = window.setTimeout("LoadMsgListBox(" + (Increment + 1) + ", false)", 0);
	} else {
		MsgListData["Ctrl"]["Loading"] = false;
	}
}

function ListBoxKeyCtrl(WhichKey) {

	if (WhichKey == 13) {
		MsgRowCtrl(null, "DblClick");
		WhichKey = 505;
	} else if (WhichKey == 46) {
		MoveMsgs('Trash');
		WhichKey = 505;
	} else if (WhichKey == 38 || WhichKey == 40) {
		var Increment = MsgListData["Ctrl"]["Selected"][MsgListData["Ctrl"]["Selected"].length - 1];
		var DataLength = MsgListData["Data"].length;
		if ((WhichKey == 38 && Increment > 0) || (WhichKey == 40 && Increment < (DataLength - 1) && Increment < MsgListData["Ctrl"]["Increment"])) {
			var MaskHeightScroll = ObjMsgListBox.scrollHeight;
			var MaskHeightBox = ObjMsgListBox.clientHeight;
			var MaskTop = ObjMsgListBox.scrollTop;

			if(WhichKey == 38) {
				Increment --;
			} else if(WhichKey == 40) {
				Increment ++;
			}

			var RowHeight = 18;
			var MoveAmount = Increment * RowHeight;

			if (MoveAmount > MaskTop && (MoveAmount + RowHeight) < (MaskTop + MaskHeightBox)) {
			} else {
				ObjMsgListBox.scrollTop = MoveAmount;
			}

			MsgRowCtrl(Increment, "Click");
		}
		WhichKey = 505;
	}
	return WhichKey;
}

function MultiArraySort(a, b) {
	if (MsgListData["Ctrl"]["SortCol"] == 5 || MsgListData["Ctrl"]["SortCol"] == 11) {
    aa = parseFloat(a[MsgListData["Ctrl"]["SortCol"]]);
    if (isNaN(aa)) aa = 0;
    bb = parseFloat(b[MsgListData["Ctrl"]["SortCol"]]);
    if (isNaN(bb)) bb = 0;
    return aa-bb;
	} else {
	  if (a[MsgListData["Ctrl"]["SortCol"]] < b[MsgListData["Ctrl"]["SortCol"]]) return -1;
	  if (a[MsgListData["Ctrl"]["SortCol"]] > b[MsgListData["Ctrl"]["SortCol"]]) return 1;
	  return 0;
	}
}

function SortMsgsBy(Column) {
	if (Column || MsgListData["Ctrl"]["LastSorted"]) {
		if (!Column) {
			Column = MsgListData["Ctrl"]["LastSorted"];
			MsgListData["Ctrl"]["LastSorted"] = null;
			if (MsgListData["Ctrl"]["SortColReverse"] == true) {
				MsgListData["Ctrl"]["SortCol"] = Column;
			}
		}
		if (MsgListData["Ctrl"]["Loading"] == false && MsgListData["Data"].length > 1) {
			ObjMsgListBox.innerHTML = "";
			MsgListData["Ctrl"]["Initialised"] = false;
			if(MsgListData["Ctrl"]["LastSorted"]) {
				document.getElementById("MsgListBoxSort" + MsgListData["Ctrl"]["LastSorted"] + "Img").src = "imgs/simple/shim.gif";
			}
			MsgListData["Ctrl"]["LastSorted"] = Column;
			if (MsgListData["Ctrl"]["SortCol"] == Column) {
				document.getElementById("MsgListBoxSort" + Column + "Img").src = "imgs/simple/listbox_header_sort_down.gif";
				MsgListData["Ctrl"]["SortDescending"][Column] = true;
				MsgListData["Ctrl"]["SortCol"] = Column;
				MsgListData["Ctrl"]["SortColReverse"] = true;
				MsgListData["Data"].sort(MultiArraySort);
				MsgListData["Data"].reverse();
				MsgListData["Ctrl"]["SortCol"] = null;
				LoadMsgListBox(0);
			} else {
				document.getElementById("MsgListBoxSort" + Column + "Img").src = "imgs/simple/listbox_header_sort_up.gif";
				MsgListData["Ctrl"]["SortDescending"][Column] = false;
				MsgListData["Ctrl"]["SortCol"] = Column;
				MsgListData["Ctrl"]["SortColReverse"] = false;
				MsgListData["Data"].sort(MultiArraySort);
				LoadMsgListBox(0);
			}
		}
	} else {
		LoadMsgListBox(0);
	}

}

function NumericSort(a, b) {
	return a - b;
}

function ClearListBoxData() {
	MsgListData["Data"].length = 0;
	ObjMsgListBox.innerHTML = "";
	MsgListData["Ctrl"]["Initialised"] = false;
	MsgListData["Ctrl"]["Timeout"] = null;
	MsgListData["Ctrl"]["Increment"] = null;
	MsgListData["Ctrl"]["Selected"].length = 0;
	MsgListData["Ctrl"]["SortCol"] = null;
	MsgListData["Ctrl"]["SortDescending"].length = 0;
	MsgListData["Ctrl"]["CtrlKey"] = false;
	MsgListData["Ctrl"]["ShiftKey"] = false;
	MsgListData["Ctrl"]["DnD"].length = 0;

	// If we are using the single login window, open-src copy, remove it from successful login
	try {
		document.getElementById('ObjAdvancedWindow').style.display = "none";
	} catch(e) {
	}

}

function ToggleSearchRow(Override) {

	if(TestAjaxFrame('Search'))
	return;

	if(MsgListData["Views"]["MsgListViewer"] == false)	{
LoadFolders(); LoadMsgs();
	}

	// Make the select box with search results
	var index = '1';
	document.getElementById('SearchLocation').options.length = 0;

	var opt = document.createElement('OPTION');
	opt.value = '';
	opt.text = Lang_AllFolders;
	opt.className = "opt";
	document.getElementById('SearchLocation').options[document.getElementById('SearchLocation').options.length] = opt;

	for (i in MsgListData["Folders"])	{
	// Disable searching Inbox for POP3
	if(MailType == 'pop3' && MsgListData["Folders"][i][0] == 'Inbox')	{
	} else	{

	var opt = document.createElement('OPTION');
	opt.value = MsgListData["Folders"][i][0];
	opt.text = MsgListData["Folders"][i][4];

	document.getElementById('SearchLocation').options[document.getElementById('SearchLocation').options.length] = opt;

	if(opt.value == 'Inbox')
	document.getElementById('SearchLocation').options[index].selected = true;

	index++;
	}

}


	ObjSearchRow = document.getElementById("MsgSearchRow");
	if (Override == true) {

		ObjSearchRow.style.display = "none";
	} else {
		if (ObjSearchRow.style.display == "none") {
			ObjSearchRow.style.display = "";
		} else {
			ObjSearchRow.style.display = "none";
		}
	}
}

function ToggleSearchRowMore(Override)	{

	ObjSearchRow = document.getElementById("MsgSearchRowMore");
	var MsgSearchButton = document.getElementById("MsgSearchButton");

	if (Override == true) {
		ObjSearchRow.style.display = "none";
	} else {
		if (ObjSearchRow.style.display == "none") {
			ObjSearchRow.style.display = "";
			MsgSearchButton.text = "Less";

		} else {
			ObjSearchRow.style.display = "none";
			MsgSearchButton.text = "More";

		}
	}


}

function FolderHighlight(Folder, Action, MainFolders) {
	if (MainFolders == true) {
		if (MsgListData["Folders"][Folder][0] != MsgListData["CurrentFolder"]) {
			if (Action == "Over") {
				document.getElementById("FolderIcon" + MsgListData["Folders"][Folder][0]).src = "imgs/simple/sidebar_" + MsgListData["Folders"][Folder][1] + "_on.gif";
				document.getElementById("FolderIcon" + MsgListData["Folders"][Folder][0]).className="FolderHighlightIconOn";
				document.getElementById("Folder" + MsgListData["Folders"][Folder][0]).className="FolderHighlightOn";

			} else if (Action == "Out") {
				document.getElementById("FolderIcon" + MsgListData["Folders"][Folder][0]).src = "imgs/simple/sidebar_" + MsgListData["Folders"][Folder][1] + "_off.gif";
				document.getElementById("FolderIcon" + MsgListData["Folders"][Folder][0]).className="FolderHighlightIconOff";
				document.getElementById("Folder" + MsgListData["Folders"][Folder][0]).className="FolderHighlightOff";

			}
		}
	} else {
		if (Action == "Over") {
			document.getElementById("Folder" + Folder).className="FolderHighlightOver";
		} else if (Action == "Out") {
			document.getElementById("Folder" + Folder).className="FolderHighlightOut";
		}
	}
}

function ToggleVideo() {
	if (document.getElementById("ComposeMsgVideoContainer").style.display == "none") {
		document.getElementById("ComposeMsgTextContainer").colSpan = "2";
		document.getElementById("ComposeMsgVideoContainer").style.display = "";
		if (document.getElementById("ComposeMsgVideoIFrame").src.substring(document.getElementById("ComposeMsgVideoIFrame").src.length - 4, document.getElementById("ComposeMsgVideoIFrame").src.length) == "html") {
			VideoStreamUID = GetVideoID(true);
			document.getElementById("ComposeMsgVideoIFrame").src = "http://" + document.getElementById('VideoMailServer').value + "/videomail/record.pl?UniqueID=" + VideoStreamUID;
		}
		document.getElementById("VideoMovedOptionsSource1").style.display = "none";
		document.getElementById("VideoMovedOptionsSource2").style.display = "none";
		document.getElementById("VideoMovedOptionsSource3").style.display = "none";
		document.getElementById("VideoMovedOptionsSource4").style.display = "none";
		document.getElementById("VideoMovedOptionsSource5").style.display = "none";
		document.getElementById("VideoMovedOptionsSource6").style.display = "none";
		document.getElementById("VideoMovedOptionsWidth1").style.width = "100%";
		document.getElementById("VideoMovedOptionsWidth2").style.width = "100%";
		document.getElementById("VideoMovedOptionsWidth3").style.width = "100%";
		document.getElementById("VideoMovedOptionsDestination").style.display = "";
		document.getElementById("VideoMovedHelpDestination").style.display = "";
		if (document.getElementById("VideoMovedOptionsColSpan")) document.getElementById("VideoMovedOptionsColSpan").colSpan = "1";
		document.getElementById("VideoMovedOptionsColSpan1").colSpan = "1";
		document.getElementById("VideoMovedOptionsColSpan2").colSpan = "1";
		document.getElementById("VideoMovedOptionsColSpan3").colSpan = "1";
	} else {
		document.getElementById("ComposeMsgTextContainer").colSpan = "1";
		document.getElementById("ComposeMsgVideoContainer").style.display = "none";
		document.getElementById("VideoMovedOptionsSource1").style.display = "";
		document.getElementById("VideoMovedOptionsSource2").style.display = "";
		document.getElementById("VideoMovedOptionsSource3").style.display = "";
		document.getElementById("VideoMovedOptionsSource4").style.display = "";
		document.getElementById("VideoMovedOptionsSource5").style.display = "";
		document.getElementById("VideoMovedOptionsSource6").style.display = "";
		document.getElementById("VideoMovedOptionsWidth1").style.width = "70%";
		document.getElementById("VideoMovedOptionsWidth2").style.width = "70%";
		document.getElementById("VideoMovedOptionsWidth3").style.width = "70%";
		document.getElementById("VideoMovedOptionsDestination").style.display = "none";
		document.getElementById("VideoMovedHelpDestination").style.display = "none";

		if (document.getElementById("VideoMovedOptionsColSpan")) document.getElementById("VideoMovedOptionsColSpan").colSpan = "3";
		document.getElementById("VideoMovedOptionsColSpan1").colSpan = "3";
		document.getElementById("VideoMovedOptionsColSpan2").colSpan = "3";
		document.getElementById("VideoMovedOptionsColSpan3").colSpan = "3";
	}
}

function LoadFolders(FolderSet) {
	if (!FolderSet) FolderSet = "";
    
    //FolderSet = Url.decode(FolderSet);
	if (TestAjaxFrame("Inbox", "To=" + Url.encode(FolderSet))) return;
    
	ObjFolderBox = document.getElementById("FolderBox");
	ObjFolderBox.innerHTML = '';

	ObjFLTable = document.createElement("table");
	ObjFLTable.width = "150";
	ObjFLTable.cellSpacing = "0";
	ObjFLTable.cellPadding = "0";
	ObjFLTable.border = "0";
	ObjFLTable.style.tableLayout = "fixed";
	ObjFolderBox.appendChild(ObjFLTable);

	ObjFLTableTbody = document.createElement("tbody");
	ObjFLTable.appendChild(ObjFLTableTbody);

	if (FolderSet) {
		FieldInFocus = true;
		var Folders = new Array();
			Folders["ReadMsg"] = new Array();
			Folders["ReadMsg"]["Selected"] = null;
			Folders["ReadMsg"].push(new Array(Lang_Back, "back", "LoadFolders(); LoadMsgs('" + MsgListData["CurrentFolder"] + "'); FixShowMail();"));
			if (MsgListData["CurrentFolder"] == "Drafts") {
				Folders["ReadMsg"].push(new Array(Lang_Open, "open", "ReadMsg(null, null, 'Open');"));
			} else {
				Folders["ReadMsg"].push(new Array(Lang_Reply, "reply", "ReadMsg(null, null, 'Reply');"));
				Folders["ReadMsg"].push(new Array(Lang_ReplyAll, "replyall", "ReadMsg(null, null, 'ReplyAll');"));
				Folders["ReadMsg"].push(new Array(Lang_Forward, "forward", "ReadMsg(null, null, 'Forward');"));
			}
			Folders["ReadMsg"].push(new Array(Lang_Delete, "delete", "LoadFolders(); LoadMsgs('" + MsgListData["CurrentFolder"] + "'); MoveMsgs('Trash');"));
			if (MsgListData["CurrentFolder"] != "Drafts") Folders["ReadMsg"].push(new Array(Lang_Abook, "address", "AddAbook();"));
			Folders["ReadMsg"].push(new Array(Lang_Print, "print", "PrintEmail();"));
			if (MsgListData["CurrentFolder"] != "Drafts") Folders["ReadMsg"].push(new Array(Lang_BlockSender, "block", "BlockSender()"));
			Folders["ReadMsg"].push(new Array(Lang_ViewHeaders, "search", "ViewHeaders();"));
			Folders["ReadMsg"].push(new Array(Lang_Next, "next", "NextMsg();"));
			Folders["ReadMsg"].push(new Array(Lang_Prev, "previous", "PreviousMsg();"));

			Folders["ComposeMsg"] = new Array();

			// Generate our unique ID for the attachments to upload
			unique = Math.round(Math.random()*99999);

			Folders["ComposeMsg"].push(new Array(Lang_Send, "send", "SendMsg('" + unique + "');"));

			Folders["ComposeMsg"].push(new Array(Lang_Back, "back", "LoadFolders(); LoadMsgs('" + MsgListData["CurrentFolder"] + "');"));

			Folders["ComposeMsg"].push(new Array(Lang_AddRecpt, "addrec", "AddRecpientsDOM();"));
			Folders["ComposeMsg"].push(new Array(Lang_AddBCC, "newgroup", "ToggleBccRow();"));

			if(document.getElementById('spellcheck').value == 1){
				Folders["ComposeMsg"].push(new Array(Lang_SpellCheck, "spell", "SpellCheck(true);"));
			}

			Folders["ComposeMsg"].push(new Array(Lang_Attachments, "attach", "Attachment(" + unique + ");"));

			// If Videomail is enabled via the Webadmin
			if(document.getElementById('VideoMail').value == 1)	{
			Folders["ComposeMsg"].push(new Array(Lang_VideoMail, "video", "ToggleVideo();"));
			}

			Folders["ComposeMsg"].push(new Array(Lang_SaveMsg, "save_settings", "SendMsg('" + unique + "', '1');"));

			Folders["SpellChecker"] = new Array();
			Folders["SpellChecker"].push(new Array(Lang_Resume, "spell", "SpellCheck();"));

		for (var i in Folders[FolderSet]) {

			if (i != "Selected") {
				ObjFLTableTbodyTr = document.createElement("tr");
				ObjFLTableTbodyTrTd = document.createElement("td");
				ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTd";
				ObjFLTableTbodyTrTdImg = document.createElement("img");
				ObjFLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
				ObjFLTableTbodyTrTdImg.width = "38";
				ObjFLTableTbodyTrTdImg.height = "10";
				ObjFLTableTbodyTrTdImg.border = "0";
				ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
				ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
				ObjFLTableTbodyTrTd = document.createElement("td");
				ObjFLTableTbodyTrTd.width = "125";
				ObjFLTableTbodyTrTdImg = document.createElement("img");
				ObjFLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
				ObjFLTableTbodyTrTdImg.width = "125";
				ObjFLTableTbodyTrTdImg.height = "10";
				ObjFLTableTbodyTrTdImg.border = "0";
				ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
				ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
				ObjFLTableTbody.appendChild(ObjFLTableTbodyTr);

				ObjFLTableTbodyTr = document.createElement("tr");
				ObjFLTableTbodyTr.id = "FolderRow" + Folders[FolderSet][i][1];
				if (Folders[FolderSet][i][1] == Folders[FolderSet]["Selected"]) {
					ObjFLTableTbodyTr.className = "ObjFLTableTbodyTr";
				} else {
					ObjFLTableTbodyTr.className = "ObjFLTableTbodyTr2";
				}
				if (Folders[FolderSet][i][2]) {
					var onClickFunc = Folders[FolderSet][i][2];
					ObjFLTableTbodyTr.onclick = new Function(onClickFunc);
				}
				if (Folders[FolderSet][i][1] != Folders[FolderSet]["Selected"]) {
					var onMouseOverFunc = "FolderHighlight('" + Folders[FolderSet][i][1] + "', 'Over');";
		 			ObjFLTableTbodyTr.onmouseover = new Function(onMouseOverFunc);
					var onMouseOutFunc = "FolderHighlight('" + Folders[FolderSet][i][1] + "', 'Out');";
					ObjFLTableTbodyTr.onmouseout = new Function(onMouseOutFunc);
				}

				ObjFLTableTbodyTrTd = document.createElement("td");
				ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTd";
				ObjFLTableTbodyTrTdImg = document.createElement("img");
				ObjFLTableTbodyTrTdImg.id = "FolderIcon" + Folders[FolderSet][i][1];
				if (Folders[FolderSet][i][1] == Folders[FolderSet]["Selected"]) {
					ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_" + Folders[FolderSet][i][1] + "_on.gif";
				} else if (Folders[FolderSet][i][1] == "back") {
					var FoundMatch = false;
					for (var x in MsgListData["Folders"]) {
						if (MsgListData["Folders"][x][0] == MsgListData["CurrentFolder"]) {
							ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_" + MsgListData["Folders"][x][1] + "_off.gif";
							Folders[FolderSet][i][0] = MsgListData["Folders"][x][0];
							FoundMatch = true;
							break;
						}
					}
					if (FoundMatch == false) {
						ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_inbox_off.gif";
						Folders[FolderSet][i][0] = "Inbox";
					}
				} else {
					ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_" + Folders[FolderSet][i][1] + ".gif";
				}
				ObjFLTableTbodyTrTdImg.width = "38";
				ObjFLTableTbodyTrTdImg.height = "31";
				ObjFLTableTbodyTrTdImg.border = "0";
				if (Folders[FolderSet][i][1] == Folders[FolderSet]["Selected"]) {
					ObjFLTableTbodyTrTdImg.className = "ObjFLTableTbodyTrTdImg";
				} else {
					ObjFLTableTbodyTrTdImg.className = "ObjFLTableTbodyTrTdImg2";
				}
				ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
				ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
				ObjFLTableTbodyTrTd = document.createElement("td");
				ObjFLTableTbodyTrTd.id = "Folder" + Folders[FolderSet][i][1];
				ObjFLTableTbodyTrTd.width = "125";
				if (Folders[FolderSet][i][1] == Folders[FolderSet]["Selected"]) {
					ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTdBorder";
				} else {
					ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTdBorder2";
				}
				if (Folders[FolderSet][i][1] == "back") {
			 		ObjFLTableTbodyTrTd.appendChild(document.createTextNode(Lang_BackTo + " " + Folders[FolderSet][i][0]));
				} else {
			 		ObjFLTableTbodyTrTd.appendChild(document.createTextNode(Folders[FolderSet][i][0]));
				}
				ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
				ObjFLTableTbody.appendChild(ObjFLTableTbodyTr);
			}
		}
	} else {
	    FieldInFocus = false;
		for (var i in MsgListData["Folders"]) {
			ObjFLTableTbodyTr = document.createElement("tr");
			ObjFLTableTbodyTrTd = document.createElement("td");
			ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTd";
			ObjFLTableTbodyTrTdImg = document.createElement("img");
			ObjFLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
			ObjFLTableTbodyTrTdImg.width = "38";
			ObjFLTableTbodyTrTdImg.height = "10";
			ObjFLTableTbodyTrTdImg.border = "0";
			ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
			ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
			ObjFLTableTbodyTrTd = document.createElement("td");
			ObjFLTableTbodyTrTd.width = "125";
			ObjFLTableTbodyTrTdImg = document.createElement("img");
			ObjFLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
			ObjFLTableTbodyTrTdImg.width = "125";
			ObjFLTableTbodyTrTdImg.height = "10";
			ObjFLTableTbodyTrTdImg.border = "0";
			ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
			ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
			ObjFLTableTbody.appendChild(ObjFLTableTbodyTr);
			if (MsgListData["Folders"][i][0] == "erase") {
				ObjFLTableTbodyTr.id = "EraseMsgFolderIconTr";
				ObjFLTableTbodyTr.style.display = "none";
			}

			ObjFLTableTbodyTr = document.createElement("tr");
			ObjFLTableTbodyTr.className = "ObjFLTableTbodyTr2";
			if (MsgListData["Folders"][i][0] == "erase") {
				ObjFLTableTbodyTr.id = "EraseMsgFolderIcon";
				ObjFLTableTbodyTr.style.display = "none";
			}
			var onClickFunc = "LoadMsgs('" + MsgListData["Folders"][i][0] + "');";
			ObjFLTableTbodyTr.onclick = new Function(onClickFunc);
			if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
				var onMouseOverFunc = "FolderHighlight(" + i + ", 'Over', true); if (MsgListData['Ctrl']['DnD']['Active'] == true) this.style.cursor = 'not-allowed';";
				ObjFLTableTbodyTr.onmouseover = new Function(onMouseOverFunc);
				var onMouseOutFunc = "FolderHighlight(" + i + ", 'Out', true); this.style.cursor = 'pointer';";
				ObjFLTableTbodyTr.onmouseout = new Function(onMouseOutFunc);
			} else {
				var onMouseOverFunc = "FolderHighlight(" + i + ", 'Over', true); if (MsgListData['Ctrl']['DnD']['Active'] == true) MsgListData['Ctrl']['DnD']['MoveTo'] = '" + MsgListData["Folders"][i][0] + "';";
				ObjFLTableTbodyTr.onmouseover = new Function(onMouseOverFunc);
				var onMouseOutFunc = "FolderHighlight(" + i + ", 'Out', true); if (MsgListData['Ctrl']['DnD']['Active'] == true) MsgListData['Ctrl']['DnD']['MoveTo'] = '';";
				ObjFLTableTbodyTr.onmouseout = new Function(onMouseOutFunc);
			}
			ObjFLTableTbodyTrTd = document.createElement("td");
			ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTd";
			ObjFLTableTbodyTrTdImg = document.createElement("img");
			ObjFLTableTbodyTrTdImg.id = "FolderIcon" + MsgListData["Folders"][i][0];
			if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
				ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_" + MsgListData["Folders"][i][1] + "_on.gif";
			} else {
				ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_" + MsgListData["Folders"][i][1] + "_off.gif";
			}
			ObjFLTableTbodyTrTdImg.width = "38";
			ObjFLTableTbodyTrTdImg.height = "31";
			ObjFLTableTbodyTrTdImg.border = "0";
			if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
				ObjFLTableTbodyTrTdImg.className = "ObjFLTableTbodyTrTdImg";
			} else {
				ObjFLTableTbodyTrTdImg.className = "ObjFLTableTbodyTrTdImg2";
			}
			ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
			ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
			ObjFLTableTbodyTrTd = document.createElement("td");
			ObjFLTableTbodyTrTd.id = "Folder" + MsgListData["Folders"][i][0];
			ObjFLTableTbodyTrTd.width = "125";
			if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
				ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTdBorder";
			} else {
				ObjFLTableTbodyTrTd.className = "ObjFLTableTbodyTrTdBorder2";
			}
			if (MsgListData["Folders"][i][2] > 0) {
		 		ObjFLTableTbodyTrTd.appendChild(document.createTextNode(MsgListData["Folders"][i][4] + " (" + MsgListData["Folders"][i][2] + ")"));
				if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"] && MsgListData["Folders"][i][2] > LastUnreadMsgCount && PlaySound == true) {
					LastUnreadMsgCount = MsgListData["Folders"][i][2];
					try {
						if (window.ActiveXObject) document.NewMsgSound.Run();
					} catch (e) {
					}
					PlaySound = false;
				} else if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
					LastUnreadMsgCount = MsgListData["Folders"][i][2];
				}
			} else {
		 		ObjFLTableTbodyTrTd.appendChild(document.createTextNode(MsgListData["Folders"][i][4]));
			}
			ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
			ObjFLTableTbody.appendChild(ObjFLTableTbodyTr);
		}
	}
	ObjFLTableTbodyTr = document.createElement("tr");
	ObjFLTableTbodyTrTd = document.createElement("td");
	ObjFLTableTbodyTrTd.width = "38";
	ObjFLTableTbodyTrTdImg = document.createElement("img");
	ObjFLTableTbodyTrTdImg.src = "imgs/simple/sidebar_bottom_tile_small.gif";
	ObjFLTableTbodyTrTdImg.width = "38";
	ObjFLTableTbodyTrTdImg.height = "45";
	ObjFLTableTbodyTrTdImg.border = "0";
	ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
	ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
	ObjFLTableTbodyTrTd = document.createElement("td");
	ObjFLTableTbodyTrTd.width = "125";
	ObjFLTableTbodyTrTdImg = document.createElement("img");
	ObjFLTableTbodyTrTdImg.src = "imgs/simple/shim.gif";
	ObjFLTableTbodyTrTdImg.width = "125";
	ObjFLTableTbodyTrTdImg.height = "1";
	ObjFLTableTbodyTrTdImg.border = "0";
	ObjFLTableTbodyTrTd.appendChild(ObjFLTableTbodyTrTdImg);
	ObjFLTableTbodyTr.appendChild(ObjFLTableTbodyTrTd);
	ObjFLTableTbody.appendChild(ObjFLTableTbodyTr);
}

function MsgRowCtrl(RowIndex, Source, Unique) {

	if (Source == "Click") {
		if (MsgListData["Ctrl"]["CtrlKey"] == true) {
			if (IsSelected(RowIndex) == "false") {
				MsgListData["Ctrl"]["Selected"].push(RowIndex);
				RowHighlight(RowIndex, true);
			} else {
				MsgListData["Ctrl"]["Selected"].splice(IsSelected(RowIndex), 1);
				RowHighlight(RowIndex, false);
			}

		} else if (MsgListData["Ctrl"]["ShiftKey"] == true) {
			if (MsgListData["Ctrl"]["Selected"].length > 1) {
				for (var i in MsgListData["Ctrl"]["Selected"]) {
					if (i > 0 && MsgListData["Ctrl"]["Selected"][i] < ObjMLTable.rows.length) {
						RowHighlight(MsgListData["Ctrl"]["Selected"][i], false);
					}
				}
				MsgListData["Ctrl"]["Selected"].length = 1;
			}
			if (MsgListData["Ctrl"]["Selected"].length > 0) {
				if (MsgListData["Ctrl"]["Selected"][0] < RowIndex) {
					for (var i = MsgListData["Ctrl"]["Selected"][0]; i <= RowIndex; i++) {
						if (IsSelected(i) == "false") {
							MsgListData["Ctrl"]["Selected"].push(i);
							RowHighlight(i, true);
						}
					}
				} else {
					for (var i = MsgListData["Ctrl"]["Selected"][0]; i >= RowIndex; i--) {
						if (IsSelected(i) == "false") {
							MsgListData["Ctrl"]["Selected"].push(i);
							RowHighlight(i, true);
						}
					}
				}
			} else {
				MsgListData["Ctrl"]["Selected"][0] = RowIndex;
				RowHighlight(RowIndex, true);
			}
		} else {
			if (MsgListData["Ctrl"]["Selected"].length > 1) {
				for (var i in MsgListData["Ctrl"]["Selected"]) {
					if (MsgListData["Ctrl"]["Selected"][i] < ObjMLTable.rows.length) {
						RowHighlight(MsgListData["Ctrl"]["Selected"][i], false);
					}
				}
				MsgListData["Ctrl"]["Selected"].length = 0;
			} else {
				if (MsgListData["Ctrl"]["Selected"][0] != null && MsgListData["Ctrl"]["Selected"][0] < ObjMLTable.rows.length) {
					RowHighlight(MsgListData["Ctrl"]["Selected"][0], false);
				}
			}
			MsgListData["Ctrl"]["Selected"][0] = RowIndex;
			RowHighlight(RowIndex, true);
		}

	} else if (Source == "DblClick") {
		GlobalUnique = Unique;
		ReadMsg();

	} else if (Source == "Over") {
		if (MsgListData["Ctrl"]["DnD"]["Active"] != true) {
			if (IsSelected(RowIndex) == "false") {
				RowHighlight(RowIndex, true, true);
			}
		}

	} else if (Source == "Out") {
		if (MsgListData["Ctrl"]["DnD"]["Active"] != true) {
			if (IsSelected(RowIndex) == "false") {
				RowHighlight(RowIndex, false);
			}
		}

	} else if (Source == "ContextMenu") {
		// Can't call like LoadContextMenu() otherwise e will be null
		LoadContextMenu;

	} else if (Source == "Down") {
		if (MsgListData["Ctrl"]["CtrlKey"] == false && MsgListData["Ctrl"]["ShiftKey"] == false) {
			if (IsSelected(RowIndex) == "false") {
				MsgRowCtrl(RowIndex, "Click");
			}
			MsgListData["Ctrl"]["DnD"]["OnDrag"] = true;
		}

	} else if (Source == "Up") {
		MsgListData["Ctrl"]["DnD"]["OnDrag"] = false;

	} else if (Source == "Move") {
		if (MsgListData["Ctrl"]["DnD"]["OnDrag"] == true && MsgListData["Ctrl"]["DnD"]["Active"] != true && IsSelected(RowIndex) != "false") {
			document.onmousemove = StartDnD;
		}
	}

}

function IsSelected(Criteria) {
	var CriteriaFound = "false";
	for (var i in MsgListData["Ctrl"]["Selected"]) {
		if (MsgListData["Ctrl"]["Selected"][i] == Criteria) {
			CriteriaFound = i;
		}
	}
	return CriteriaFound;
}

function RowHighlight(RowIndex, On, Hover) {
	if (On == true) {
		if (Hover == true) {

			ObjMLTable.rows[RowIndex].style.color = "";
			ObjMLTable.rows[RowIndex].style.backgroundColor = "#deebf6";
		} else {
			ObjMLTable.rows[RowIndex].style.color = "white";
			ObjMLTable.rows[RowIndex].style.backgroundColor = "#8EBEE5";
		}
	} else {
		ObjMLTable.rows[RowIndex].style.color = "";
		ObjMLTable.rows[RowIndex].style.backgroundColor = "";
	}
}

function LoadContextMenu(e) {
	e = fixE(e);

	if (document.getElementById("PopUpBox")) document.body.removeChild(ObjPopUpBox);

	ObjPopUpBox = document.createElement("div");

	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "ReadMsg(); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";

	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Open));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	ObjPopUpBoxItem = document.createElement("div");
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem";
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "ReadMsg(null, null, 'Reply'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Reply));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "ReadMsg(null, null, 'ReplyAll'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_ReplyAll));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "ReadMsg(null, null, 'Forward'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Forward));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	ObjPopUpBoxItem = document.createElement("div");
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem";
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	// Mark as read
	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "MarkMessage('o'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_MarkAsRead));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	// Mark as unread
	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "MarkMessage('x'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_MarkAsUnread));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	ObjPopUpBoxItem = document.createElement("div");
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem";
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	// Move to Trash
	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "MoveMsgs('Trash'); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem2";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Delete));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	ObjPopUpBoxItem = document.createElement("div");
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem";
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

	// Add to address book
	ObjPopUpBoxItem = document.createElement("div");
		var onClickFunc = "AddAbook(); document.body.removeChild(ObjPopUpBox);";
		ObjPopUpBoxItem.onclick = new Function(onClickFunc);
		var onMouseOverFunc = "this.style.backgroundColor = '#85b3dc'; this.style.color = 'white';";
		ObjPopUpBoxItem.onmouseover = new Function(onMouseOverFunc);
		var onMouseOutFunc = "this.style.backgroundColor = ''; this.style.color = 'black';";
		ObjPopUpBoxItem.onmouseout = new Function(onMouseOutFunc);
		ObjPopUpBoxItem.style.width = "95px";
	ObjPopUpBoxItem.className = "ObjPopUpBoxItem3";
	ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_AddSender));
	ObjPopUpBox.appendChild(ObjPopUpBoxItem);

		ObjPopUpBox.id = "PopUpBox";
		ObjPopUpBox.className = "ObjPopUpBox";
		ObjPopUpBox.style.top = e.clientY;
		ObjPopUpBox.style.left = e.clientX;
		document.body.appendChild(ObjPopUpBox);
		var onClickFunc = "try { document.body.removeChild(ObjPopUpBox);} catch(e) { }";
		document.body.onclick = new Function(onClickFunc);

	return false;
}

function DeleteMsgs() {
	var CurrentFolderIndex = null;
	for (var i in MsgListData["Folders"]) {
		if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
			CurrentFolderIndex = i;
			break;
		}
	}
	MsgListData["Ctrl"]["Selected"].sort(NumericSort);
	for (var i = 0; i < MsgListData["Ctrl"]["Selected"].length; i ++) {
		if (MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][9] == "unread") {
			MsgListData["Folders"][CurrentFolderIndex][2] --;
		}
		ObjMLTable.deleteRow(MsgListData["Ctrl"]["Selected"][i]);
		MsgListData["Data"].splice(MsgListData["Ctrl"]["Selected"][i], 1);
		MsgListData["Ctrl"]["Selected"].splice(i, 1);
		i --;
		for (var x in MsgListData["Ctrl"]["Selected"]) {
			MsgListData["Ctrl"]["Selected"][x] --;
		}
	}

	var unread = MsgListData["Folders"][CurrentFolderIndex][2];

	if(unread > 0)
	document.getElementById("Folder" + MsgListData["CurrentFolder"]).innerHTML = MsgListData["CurrentFolder"] + " (" + unread + ")";
	else
	document.getElementById("Folder" + MsgListData["CurrentFolder"]).innerHTML = MsgListData["CurrentFolder"];

	// Create the empty folder message
	var EmptyFolder = Lang_HasNoMsgs;
	EmptyFolder = EmptyFolder.replace(/\$var\['FolderName'\]/,MsgListData["CurrentFolder"]);

	if (MsgListData["Data"].length == 0) document.getElementById("MsgListBox").innerHTML = "<div style=\"width: 100%; text-align: center; padding: 5px;\" class=\"ObjMLTableTbodyTrTdDiv2\">" + EmptyFolder + "</div>";
}

function StartDnD(e) {
	e = fixE(e);
	if (MsgListData["Ctrl"]["DnD"]["OnDrag"] == true && MsgListData["Ctrl"]["DnD"]["Active"] != true) {
		document.getElementById("EraseMsgFolderIcon").style.display = "";
		document.getElementById("EraseMsgFolderIconTr").style.display = "";
		MsgListData["Ctrl"]["DnD"]["Active"] = true;
		MsgListData["Ctrl"]["DnD"]["MoveTo"] = "";
		ObjDnDDiv = document.createElement("div");
		document.body.appendChild(ObjDnDDiv);
      ObjDnDDiv.className = "ObjDnDDiv";
		ObjDnDDiv.style.left = (e.clientX + 25) + "px";
		ObjDnDDiv.style.top = e.clientY + "px";
		ObjDnDDiv.style.filter = 'alpha(opacity=90)';
		ObjDnDDiv.style.opacity = '0.90';
		ObjDnDDivContent = document.createElement("div");
		ObjDnDDivContent.className = "ObjDnDDivContent";
		ObjDnDDivContent.appendChild(document.createTextNode(MsgListData["Ctrl"]["Selected"].length + " " + Lang_ItemsToMove));
		ObjDnDDiv.appendChild(ObjDnDDivContent);

		for (var i in MsgListData["Ctrl"]["Selected"]) {
			if (i < 6) {
				ObjDnDDivContent = document.createElement("div");
				ObjDnDDivContent.className = "ObjDnDDivContent";
				if (i < 5) {
					ObjDnDDivContent.appendChild(document.createTextNode(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][2]));
				} else {
					ObjDnDDivContent.appendChild(document.createTextNode("..."));
				}
				ObjDnDDiv.appendChild(ObjDnDDivContent);
			}
			ObjMLTable.rows[MsgListData["Ctrl"]["Selected"][i]].style.backgroundColor = "#a3c5e3";
		}
		document.onmousemove = MoveDnD;
		document.onmouseup = FinishDnD;
	}
}

var lastpos = 0;

function MoveDnD(e) {
	e = fixE(e);
	if (MsgListData["Ctrl"]["DnD"]["OnDrag"] == true && MsgListData["Ctrl"]["DnD"]["Active"] == true) {
		// scroll page if need be so user can drop to folders that may be out of view
		if ((document.body.clientHeight + document.body.scrollTop) < document.body.scrollHeight) {
			if (MousePosXY[1] > (document.body.clientHeight + document.body.scrollTop - 50) && (lastpos < MousePosXY[1])) {
				document.body.scrollTop += 5;
				ObjDnDDiv.style.top = MousePosXY[1] + "px";

			} else if (MousePosXY[1] < (document.body.scrollTop + 50) && (lastpos > MousePosXY[1])) {
				document.body.scrollTop -= 5;
				ObjDnDDiv.style.top = MousePosXY[1] + "px";
			} else {
				ObjDnDDiv.style.top = MousePosXY[1] + "px"
				ObjDnDDiv.style.left = (e.clientX + 25) + "px";
			}

			ObjDnDDiv.style.left = (e.clientX + 25) + "px";

		} else {
			ObjDnDDiv.style.top = MousePosXY[1] + "px"
			ObjDnDDiv.style.left = (e.clientX + 25) + "px";
		}

		lastpos = MousePosXY[1];
	}
}

function FinishDnD() {
	document.getElementById("EraseMsgFolderIcon").style.display = "none";
	document.getElementById("EraseMsgFolderIconTr").style.display = "none";
	document.onmousemove = "";
	document.onmouseup = "";
	document.body.removeChild(ObjDnDDiv);
	MsgListData["Ctrl"]["DnD"]["OnDrag"] = false;
	MsgListData["Ctrl"]["DnD"]["Active"] = false;

	if (MailType == "pop3" && MsgListData["Ctrl"]["DnD"]["MoveTo"] == "Inbox") {
		alert("Sorry, you cannot move emails back to the\nInbox because the POP3 protocol does not support it.");
	} else {
		for (var i in MsgListData["Ctrl"]["Selected"]) {
			ObjMLTable.rows[MsgListData["Ctrl"]["Selected"][i]].style.backgroundColor = "#8EBEE5";
		}
		if (MsgListData["Ctrl"]["DnD"]["MoveTo"] != MsgListData["CurrentFolder"]) {
			MoveMsgs(MsgListData["Ctrl"]["DnD"]["MoveTo"]);
		}
	}
}

function fixE(e) {
	if (typeof e == "undefined") e = window.event;
	if (typeof e.layerX == "undefined") e.layerX = e.offsetX;
	if (typeof e.layerY == "undefined") e.layerY = e.offsetY;

	return e;
}

function getE(e)	{
	if (typeof e == "undefined") e = window.event;
	if (typeof e.layerX == "undefined") e.layerX = e.offsetX;
	if (typeof e.layerY == "undefined") e.layerY = e.offsetY;

	return e;

}

function MoveMsgs(MoveTo) {
	if (MoveTo) {
		DataIsLoading(true);
		MoveMessagesReq = false;

		if (MoveMessagesReq && MoveMessagesReq.readyState < 4) MoveMessagesReq.abort();

		MoveMessagesReq = createXMLHttpRequest();

		MoveMessagesReq.onreadystatechange = MoveMessagesReqChange;
		var POSTString = "ajax=1&NewFolder=" + encodeURIComponent(MoveTo);
		var IDstring = '';
		var InboxCheck;

		for (var i in MsgListData["Ctrl"]["Selected"]) {
			var id = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][0];
			var folder = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][1];
			var uidl = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][12];
			if(folder == 'Inbox' && ( MailType == 'pop3' || MailType == 'imap') )
			InboxCheck++;
			// Make the string [msg-id]::[folder] - Has to be like this when using the search since we
			// don't know which folder we are located under, especially for the search menu
			IDstring += "&id[]=" + CalcMoveMsgs(id, folder) + "::" + folder + "::" + uidl;
			SpliceMoveMsgs(id, folder);

		}

		if(MsgListData["CurrentFolder"] == 'Search' && ( MailType == 'pop3' || MailType == 'imap') )	{
			POSTString += "&Folder=Inbox";
		} else	{
			POSTString += "&Folder=" + Url.encode(MsgListData["CurrentFolder"]);
		}

		if(MoveTo == 'erase' && !confirm(Lang_AlertPerm)) return false;

		POSTString += IDstring;

		MoveMessagesReq.open("POST", "showmail.php", true);
		MoveMessagesReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		MoveMessagesReq.send(POSTString);

	}
}

function MoveMessagesReqChange() {
	if (MoveMessagesReq.readyState == 4 && MoveMessagesReq.status == 200) {

		if ( MoveMessagesReq.responseXML && CheckXMLError(MoveMessagesReq) ) {
			// We are the search results, just push the last array
			try {
			var status = MoveMessagesReq.responseXML.getElementsByTagName("status")[0].firstChild.data;
				// Check the message was moved successfully
				if(status == 1)	{
				DeleteMsgs();
				} else if(status == 0) {
				    alert(MoveMessagesReq.responseXML.getElementsByTagName("message")[0].firstChild.data)
				}
			} catch(e) { alert('Error deleting message - Please reload mailbox'); }
		}

		DataIsLoading(false);
	}
}

// Read an email message
function ReadMsg(ShowMsg, FoldersLoaded, Reply, DisplayImages) {

	// Test if we are inside the Ajax panel, if so silently return
	if (TestAjaxFrameNull()) return;

	// If we are using firefox, enable selection so users can select text on readmail pane
	document.body.setAttribute("style","-moz-user-select: text;");

	// If no messages are selected, don't do anything
	if (MsgListData["Ctrl"]["Selected"][0] == undefined) return;

	DataIsLoading(true);

	if (Reply) {
		ReadMsgReply = Reply;
	} else {
		ReadMsgReply = null;
	}

	if (ShowMsg && Reply != true) {
		ObjMsgReader = document.getElementById("MsgReader");
		ObjMsgReader.innerHTML = "";

		ObjMRTable = document.createElement("table");
		ObjMRTable.id = "ReadMsgInfoTable";
		ObjMRTable.className = "ObjMRTable";
		ObjMsgReader.appendChild(ObjMRTable);
		ObjMRTableTbody = document.createElement("tbody");
		ObjMRTableTbody.id = "ReadMsgInfoTableTbody";
		ObjMRTable.appendChild(ObjMRTableTbody);
		ObjMRTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTr.id = "ReadMsgInfoTableTbodyFirstRow";
		ObjMRTableTbody.appendChild(ObjMRTableTbodyTr);
		ObjMRTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTd.width = "100%";
		ObjMRTableTbodyTrTd.vAlign = "middle";
		if (NewHeaderStyle == true) ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd";
		ObjMRTableTbodyTrTdTable = document.createElement("table");
		ObjMRTableTbodyTrTdTable.id = "ReadMsgInfoTableHeading";
		ObjMRTableTbodyTrTdTable.width = "100%";
		if (NewHeaderStyle == true) {
			ObjMRTableTbodyTrTdTable.cellSpacing = "5";
			ObjMRTableTbodyTrTdTable.cellPadding = "0";
		} else {
			ObjMRTableTbodyTrTdTable.cellSpacing = "0";
			ObjMRTableTbodyTrTdTable.cellPadding = "5";
		}
		ObjMRTableTbodyTrTdTable.border = "0";
		ObjMRTableTbodyTrTdTableTbody = document.createElement("tbody");

		// Start New Msg Header Row
		ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_From + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd2";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgReaderData[ShowMsg][3]));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Sent + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgReaderData[ShowMsg][4]));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);
		// Start New Msg Header Row - To / Sent
		ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_To + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd4";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv = document.createElement("div");

		// Cleanup the data, take out whitespace between recipients
		var ToData = MsgReaderData[ShowMsg][5];
		ToData = ToData.replace(/,\s+/g, ', ');
		ToData = ToData.replace(/;\s+/g, '; ');

		// Make the title on mouseover all the recipients, easier to read
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.title = ToData;

		// If over a 100 characters, chop down to fit on two rows for usability
		if(ToData.length > 100)	{
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.style.height = "30px";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.style.overflow = "hidden";
		}

		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.appendChild(document.createTextNode(ToData));
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdDiv);
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Priority + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgReaderData[ShowMsg][12]));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

		// Check CC header exists
		if(MsgReaderData[ShowMsg][6])	{
		ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Cc + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd2";
		ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "4";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv = document.createElement("div");

		// Cleanup the data, take out whitespace between recipients
		var CcData = MsgReaderData[ShowMsg][6];
		CcData = CcData.replace(/,\s+/g, ', ');
		CcData = CcData.replace(/;\s+/g, '; ');

		// Make the title on mouseover all the recipients, easier to read
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.title = CcData;

		// If over a 100 characters, chop down to fit on two rows for usability
		if(CcData.length > 100)	{
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.style.height = "30px";
		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.style.overflow = "hidden";
		}

		ObjMRTableTbodyTrTdTableTbodyTrTdDiv.appendChild(document.createTextNode(MsgReaderData[ShowMsg][6]));
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdDiv);
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);
}

		// Start New Msg Header Row - Subject
		ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Subject + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd2";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgReaderData[ShowMsg][2]));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Type + ":"));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
		ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";
		ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgReaderData[ShowMsg][7]));
		ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
		ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);
		ObjMRTableTbodyTrTdTable.appendChild(ObjMRTableTbodyTrTdTableTbody);
		ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTable);
		ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);
		// Check if Attachments exists
		if(MsgReaderData[ShowMsg][10])	{
			ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
			ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
			ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd";
			ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Attachments + ":"));
			ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
			ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
			ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd2";
			ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "4";
			ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = MsgReaderData[ShowMsg][10];
			ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
			ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);
		}

		if (MsgReaderData[ShowMsg][17] != "") {
			ObjMRTableTbodyTrTd = document.createElement("td");
			ObjMRTableTbodyTrTd.width = "240";
			if (NewHeaderStyle == false) ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd3";
			ObjMRTableTbodyTrTdIFrame = document.createElement("iframe");
			ObjMRTableTbodyTrTdIFrame.width = "240";
			ObjMRTableTbodyTrTdIFrame.height = "210";
			ObjMRTableTbodyTrTdIFrame.src = MsgReaderData[ShowMsg][17];
			ObjMRTableTbodyTrTdIFrame.scrolling = "no";
			ObjMRTableTbodyTrTdIFrame.frameBorder = "0";
			ObjMRTableTbodyTrTdIFrame.marginHeight = "0";
			ObjMRTableTbodyTrTdIFrame.marginWidth = "0";
			if (NewHeaderStyle == true) {
				ObjMRTableTbodyTrTdIFrame.className = "ObjMRTableTbodyTrTdIFrame";
			} else {
				ObjMRTableTbodyTrTdIFrame.className = "ObjMRTableTbodyTrTdIFrame2";
			}
			ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdIFrame);
			ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);
		}

		ObjMRTableTbodyTr = document.createElement("tr");
		ObjMRTableTbody.appendChild(ObjMRTableTbodyTr);
		ObjMRTableTbodyTrTd = document.createElement("td");
		if (MsgReaderData[ShowMsg][17] != "") ObjMRTableTbodyTrTd.colSpan = "2";
		ObjMRTableTbodyTrTd.height = "100%";
		if (NewHeaderStyle == false) ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd4";
		ObjMRTableTbodyTrTdDiv = document.createElement("div");
		ObjMRTableTbodyTrTdDiv.id = "MsgReaderData";
		ObjMRTableTbodyTrTdDiv.className = "ObjMRTableTbodyTrTdDiv";
		if (!MsgReaderData[ShowMsg][8]) MsgReaderData[ShowMsg][8] = MsgReaderData[ShowMsg][9];
			ObjMRTableTbodyTrTdDivIFrame = document.createElement("iframe");
			ObjMRTableTbodyTrTdDivIFrame.width = "100%";
			ObjMRTableTbodyTrTdDivIFrame.height = "100%";
			ObjMRTableTbodyTrTdDivIFrame.src = "html/blankiframe.html";
			ObjMRTableTbodyTrTdDivIFrame.scrolling = "auto";
			ObjMRTableTbodyTrTdDivIFrame.frameBorder = "0";
			ObjMRTableTbodyTrTdDivIFrame.marginHeight = "0";
			ObjMRTableTbodyTrTdDivIFrame.marginWidth = "0";
			ObjMRTableTbodyTrTdDivIFrame.className = "ObjMRTableTbodyTrTdDivIFrame";

			var FrameNo = 0;
			if (MsgReaderData[ShowMsg][17] != "") FrameNo = 1;

            // Toggle if we want to view images in email-messages
		    if (!DisplayImages && MsgReaderData[ShowMsg][11] == 1) {
		        var displayImagesLink = '<a href="javascript:parent.ReadMsg(null, null, null, 1)">' + Lang_Display + '</a><br><br>';
		    } else {
		        var displayImagesLink = '';
		    }

			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdDivIFrame.id = "msgwindow";
				ObjMRTableTbodyTrTdDivIFrame.name = "msgwindow";

				ObjMRTableTbodyTrTdDiv.appendChild(ObjMRTableTbodyTrTdDivIFrame);
				ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);
				ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

				window.frames[FrameNo].document.open();
				window.frames[FrameNo].document.write("<style> BODY, .sw { font-family:Arial, Helvetica, sans-serif;font-size:9pt;color:#000000; } </style><div class='container'><div class='fade_bottom'></div>" + displayImagesLink + MsgReaderData[ShowMsg][8] + MsgReaderData[ShowMsg][19] + MsgReaderData[ShowMsg][10] + "<br><br><br><br></div>");
				window.frames[FrameNo].document.close();

			} else if (navigator.userAgent.indexOf("Safari/41") != -1) {
				ObjMRTableTbodyTrTdDiv.innerHTML = displayImagesLink + MsgReaderData[ShowMsg][8];
				ObjMRTableTbodyTrTdDiv.innerHTML += MsgReaderData[ShowMsg][10];
				ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);
                ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

			} else {
				// Workaround for Firefox - Needs a unique Window name each time, why? Need to null the windows.frames to reduce mem, or auto in FF?

				ObjMRTableTbodyTrTdDivIFrame.id = "msgwindow" + msgwinnum;
				ObjMRTableTbodyTrTdDivIFrame.name = "msgwindow" + msgwinnum;

				ObjMRTableTbodyTrTdDiv.appendChild(ObjMRTableTbodyTrTdDivIFrame);
				ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);
				ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

				window.frames["msgwindow" + msgwinnum].document.open();
				window.frames["msgwindow" + msgwinnum].document.write("<style> BODY, .sw { font-family:Arial, Helvetica, sans-serif;font-size:9pt;color:#000000; } </style><div class='container'><div class='fade_bottom'></div>" + displayImagesLink + MsgReaderData[ShowMsg][8] + MsgReaderData[ShowMsg][19] + MsgReaderData[ShowMsg][10] + "<br><br><br><br></div>" + "</div>");
				window.frames["msgwindow" + msgwinnum].document.close();
				msgwinnum++;
			}
		document.getElementById("MsgListViewer").style.display = "none";
		MsgListData["Views"]["MsgListViewer"] = false;
		document.getElementById("MsgReader").style.display = "";
		MsgListData["Views"]["MsgReader"] = true;
		if (ReadMsgFoldersLoaded != true) LoadFolders("ReadMsg");
		FixShowMail();

		// The readmail page has finished loading
		DataIsLoading(false);

		// Only run if there is something selected
	} else {
		ReadMsgFoldersLoaded = FoldersLoaded;

		if (!DisplayImages && IsMsgLoaded(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]) != "false") {
			if (Reply) {
				ComposeMsg(Reply);
			} else {
				ReadMsg(IsMsgLoaded(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]));
			}
		} else {
		    // If we are reloading the message to get images then we should
		    // delete the message from cache so that the new message complete with
		    // images will be used from cache in future requests
            if (DisplayImages) {
                for (var i in MsgReaderData) {
                    if (MsgReaderData[i][0] == MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]) {
                        MsgReaderData[i].shift();
                    }
                }
            }

			MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][10] = true;
			if (window.ActiveXObject) {
				try{

				document.getElementById("ListBoxMsgLoadedIcon" + GlobalUnique).src = "imgs/simple/shim.gif";
				document.getElementById("ListBoxMsgLoadedIcon" + GlobalUnique).style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_msg_loaded.png', sizingMethod='image')";
				} catch (e) {
				}

			} else {

				try{
				document.getElementById("ListBoxMsgLoadedIcon" + GlobalUnique).src = "imgs/simple/icon_msg_loaded.png";
				} catch (e) {
				}

			}

			DataIsLoading(true);

			ReadMsgReq = false;

			if (ReadMsgReq && ReadMsgReq.readyState < 4) ReadMsgReq.abort();

			ReadMsgReq = createXMLHttpRequest();

			var id = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0];
			var folder = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][1];
			var uidl = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][12];

			ReadMsgReq.onreadystatechange = ReadMsgReqChange;
			ReadMsgReq.open("GET", "reademail.php?ajax=1&folder=" + encodeURIComponent(folder) + "&id=" + encodeURIComponent( CalcMoveMsgs(id, folder) ) + "&cache=" + encodeURIComponent(uidl) + "&DisplayImages=" + DisplayImages, true);
			ReadMsgReq.send(null);
		}
	}
}

function IsMsgLoaded(MsgID) {
	var ReturnResult = "false";
	for (var i in MsgReaderData) {
		if (MsgID == MsgReaderData[i][0]) {
			ReturnResult = i;
			break;
		}
	}
	return ReturnResult;
}

function ReadMsgReqChange() {
	if (ReadMsgReq.readyState == 4 && ReadMsgReq.status == 200) {
		if (ReadMsgReq.responseXML && CheckXMLError(ReadMsgReq) ) {
			var CurrentFolderIndex = null;
			for (var i in MsgListData["Folders"]) {
				if (MsgListData["Folders"][i][0] == MsgListData["CurrentFolder"]) {
					CurrentFolderIndex = i;
					break;
				}
			}
			if (MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][9] == "unread") {
				MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][9] = "read";
				if (window.ActiveXObject) {
					document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).src = "imgs/simple/shim.gif";
					document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_read.png', sizingMethod='image')";
				} else {
					document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).src = "imgs/simple/icon_read.png";
				}
				document.getElementById("ListBoxMsgFrom" + MsgListData["Ctrl"]["Selected"][0]).className = "ObjMLTableTbodyTrTdDiv2";
				document.getElementById("ListBoxMsgSubject" + MsgListData["Ctrl"]["Selected"][0]).className = "ObjMLTableTbodyTrTdDiv2";
				document.getElementById("ListBoxMsgDate" + MsgListData["Ctrl"]["Selected"][0]).className = "ObjMLTableTbodyTrTdDiv2";
				document.getElementById("ListBoxMsgSize" + MsgListData["Ctrl"]["Selected"][0]).className = "ObjMLTableTbodyTrTdDiv";

				MsgListData["Folders"][CurrentFolderIndex][2] --;

				if(MsgListData["Folders"][CurrentFolderIndex][2] > 0)
				document.getElementById("Folder" + MsgListData["CurrentFolder"]).innerHTML = MsgListData["CurrentFolder"] + " (" + MsgListData["Folders"][CurrentFolderIndex][2] + ")";
			}

			var DataFields = new Array("id","folder","EmailSubject","EmailFrom","EmailDate","EmailToList","EmailCcList","EmailType","EmailTxt","EmailHtml","Attachments","BlockedImages","EmailPriority","RawAttachments", "Charset", "RawHeaders", "UIDL", "VideoMail", "EmailReplyTo", "ImageAttachments", "HTMLtoTextReply");
			if (MsgReaderData.length >= MsgCacheLimit) {
				MsgReaderData.shift();
			}
			MsgReaderData.push(new Array());
			for (var i in DataFields) {

				var field;

				field = getXMLfieldName(ReadMsgReq.responseXML, DataFields[i]);

				MsgReaderData[MsgReaderData.length - 1].push(field);
			}
			DataIsLoading(false);
			if (ReadMsgReply) {
				ComposeMsg(ReadMsgReply);
			} else {
				ReadMsg(MsgReaderData.length - 1);
			}
		} else  {
        alert('Message could not be loaded from the server - Please try again or view message using another interface');
        }
	}
}

function NextMsg() {
	var MsgIndex = MsgListData["Ctrl"]["Selected"][0];
	if (MsgIndex < (MsgListData["Data"].length - 1)) {
		MsgIndex ++;
	} else {
		MsgIndex = 0;
	}
	MsgRowCtrl(MsgIndex, "Click");
	ReadMsg(null, true);
}

function PreviousMsg() {
	var MsgIndex = MsgListData["Ctrl"]["Selected"][0];
	if (MsgIndex > 0) {
		MsgIndex --;
	} else {
		MsgIndex = MsgListData["Data"].length - 1;
	}
	MsgRowCtrl(MsgIndex, "Click");
	ReadMsg(null, true);
}

function LoadCal(Language)	{
	if(TestAjaxFrame("LoadCalendar", Language)) return;
	LoadCalendar(Language);
}

function ComposeMsg(Reply, To, Cc, Bcc) {
	if (!To) To = "";
	if (!Cc) Cc = "";
	if (!Bcc) Bcc = "";

	// Build our To, Cc, Bcc into an argument list
	var args;
	if (To) args += "&To=" + escape(To);
	if (Cc) args += "&Cc=" + escape(Cc);
	if (Bcc) args += '&Bcc=' + escape(Bcc);
	if (TestAjaxFrame("Compose", args)) return;

	DataIsLoading(true);

	// Generate a new unique ID for the 'attachments' panel
	if (!ReadMsgReply) ReadMsgReply = null;
	// Reset the Videostream each time
	VideoStreamUID = null;
	// If we are using firefox, enable selection so users can type!
	document.body.setAttribute("style","-moz-user-select: text;");

	MsgListData["Views"]["MsgListViewer"] = false;
	LoadFolders("ComposeMsg");
	var ShowMsg = null;
	if (Reply) ShowMsg = IsMsgLoaded(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0]);

	document.getElementById("MsgListViewer").style.display = "none";
	document.getElementById("MsgReader").style.display = "none";
	ObjMsgComposerDiv = document.getElementById("MsgComposer");
	ObjMsgComposerDiv.innerHTML = "";
	ObjMsgComposerDiv.style.display = "";

	ObjMRTable = document.createElement("table");

	ObjMRTable.width = "100%";
	ObjMRTable.height = "100%";
	ObjMRTable.cellSpacing = "0";
	ObjMRTable.cellPadding = "0";
	ObjMRTable.border = "0";
	ObjMRTable.style.tableLayout = "fixed";
	if (NewHeaderStyle == true) ObjMRTable.className = "ObjMRTableBorder";
	ObjMsgComposerDiv.appendChild(ObjMRTable);

	ObjMRTableTbody = document.createElement("tbody");
	ObjMRTable.appendChild(ObjMRTableTbody);

	ObjMRTableTbodyTr = document.createElement("tr");
	ObjMRTableTbody.appendChild(ObjMRTableTbodyTr);

	ObjMRTableTbodyTrTd = document.createElement("td");

	if (NewHeaderStyle == true) ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd";
	ObjMRTableTbodyTrTdTable = document.createElement("table");
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTable.cellSpacing = "5";
		ObjMRTableTbodyTrTdTable.cellPadding = "0";
	} else {
		ObjMRTableTbodyTrTdTable.cellSpacing = "0";
		ObjMRTableTbodyTrTdTable.cellPadding = "5";
	}
	ObjMRTableTbodyTrTdTable.border = "0";
	ObjMRTableTbodyTrTdTable.style.tableLayout = "fixed";
	ObjMRTableTbodyTrTdTableTbody = document.createElement("tbody");

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_From + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsWidth1";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd2";
	// Generate a select box with the Prioirty types
	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("select");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgFrom";
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInputBorder";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInputBorder2";
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource1";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Date + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource2";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";

	// Get our date
	var curdate = new Date()
	var DayOfWeek = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
	var MonthName = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    var minutes = curdate.getMinutes();
    if (minutes < 10) minutes = '0' + minutes;
    var MsgDate = DayOfWeek[curdate.getDay()] + " " + MonthName[curdate.getMonth()] + " " + curdate.getDate() + " " + curdate.getHours() + ":" + minutes;

	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(MsgDate));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	// Add the To: field that is clickable for the addrecipients panel
	ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span onclick=\"AddRecpientsDOM()\" style=\"cursor: pointer;\" title=\"" + Lang_AddRecpt + "\">" + Lang_To + ":</span>";
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsWidth2";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd6";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("textarea");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgTo";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "emailto";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput2";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.autocomplete = "off";
	if (document.getElementById("AutoComplete").value == "1") {
		if (BrowserVer.Type == "MSIE") {
			var OnKeyPress = "liveSearchStart(this);";
			var OnKeyDown = "liveSearchKeyPress();";
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeypress = new Function(OnKeyPress);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeydown = new Function(OnKeyDown);
		} else {
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keypress", function (e) {
				liveSearchStart(this, e);
			}, true);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keydown", function (e) {
				liveSearchKeyPress(e);
			}, true);
		}
		var OnFocus = "liveSearchHide();";
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.onfocus = new Function(OnFocus);
	}
	var OnKeyUp = "if (this.value.length > 35) { this.style.height = '40px'; } else this.style.height='22px'; ";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeyup = new Function(OnKeyUp);

	if (Reply) {
		if (ReadMsgReply == "Reply") {
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = MsgReaderData[ShowMsg][18];
		} else if (ReadMsgReply == "ReplyAll") {
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = MsgReaderData[ShowMsg][18] + ", " + MsgReaderData[ShowMsg][5];
		} else if (ReadMsgReply == "Open") {
		    ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = MsgReaderData[ShowMsg][5]; 
		}
	} else if(To)	{
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = To;
			// Expand the field automatically if content large
			if(ObjMRTableTbodyTrTdTableTbodyTrTdInput.value.length > 35)
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput6";
	}

	ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.width = "100%";
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid silver";
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Editor + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource4";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";
	// Turn off HTML editor if not allowed
	if(!allow_HtmlEditor) {
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span id=\"ComposeModeText\" onclick=\"ToggleComposeMode(false);\">" + Lang_Text + "</span>";
	} else if (ComposeMode == "HTML") {
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span id=\"ComposeModeText\" onclick=\"ToggleComposeMode(false);\" style=\"cursor: pointer;\">Text</span> / <span id=\"ComposeModeHTML\" onclick=\"ToggleComposeMode(true);\" style=\"cursor: pointer; font-weight: bold;\">HTML</span>";
	} else if (ComposeMode == "Text") {
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span id=\"ComposeModeText\" onclick=\"ToggleComposeMode(false);\" style=\"cursor: pointer; font-weight: bold;\">" + Lang_Text + "</span> / <span id=\"ComposeModeHTML\" onclick=\"ToggleComposeMode(true);\" style=\"cursor: pointer;\">HTML</span>";
	}
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	// Add the Cc: field that is clickable for the addrecipients panel
	ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span onclick=\"AddRecpientsDOM()\" style=\"cursor: pointer;\" title=\"" + Lang_AddRecpt + "\">" + Lang_Cc + ":</span>";
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsWidth3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd6";
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("textarea");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgCc";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "emailcc";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput3";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.autocomplete = "off";
	if (document.getElementById("AutoComplete").value == "1") {
		if (BrowserVer.Type == "MSIE") {
			var OnKeyPress = "liveSearchStart(this);";
			var OnKeyDown = "liveSearchKeyPress();";
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeypress = new Function(OnKeyPress);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeydown = new Function(OnKeyDown);
		} else {
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keypress", function (e) {
				liveSearchStart(this, e);
			}, true);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keydown", function (e) {
				liveSearchKeyPress(e);
			}, true);
		}
		var OnFocus = "liveSearchHide();";
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.onfocus = new Function(OnFocus);
	}
	var OnKeyUp = "if (this.value.length > 35) { this.style.height = '40px'; } else this.style.height='22px';";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeyup = new Function(OnKeyUp);

	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid silver";
	}

	// Append CC if called from URL
	if(Cc)	{
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = Cc;

		// Expand the field automatically if content large
		if(ObjMRTableTbodyTrTdTableTbodyTrTdInput.value.length > 35)
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.height = '40px';

	}

	if (ReadMsgReply == "ReplyAll") {
		if (ObjMRTableTbodyTrTdTableTbodyTrTdInput.value) ObjMRTableTbodyTrTdTableTbodyTrTdInput.value += ", ";
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.value += MsgReaderData[ShowMsg][6];
	} else if (ReadMsgReply == "Open") {
	    ObjMRTableTbodyTrTdTableTbodyTrTdInput.value += MsgReaderData[ShowMsg][6];
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Priority + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsSource6";
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd3";

	// Generate a select box with the Prioirty types
	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("select");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeEmailPriority";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput4";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
	ObjMRTableTbodyTrTdTableTbodyTr.id = "ComposeMsgBccRow";
	ObjMRTableTbodyTrTdTableTbodyTr.name = "emailbcc";
	ObjMRTableTbodyTrTdTableTbodyTr.style.display = "none";
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	// Add the To: field that is clickable for the addrecipients panel
	ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span onclick=\"AddRecpientsDOM()\" style=\"cursor: pointer;\" title=\"" + Lang_AddRecpt + "\">" + Lang_Bcc + ":</span>";
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsColSpan1";
	ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.style.width = "100%";
	if (NewHeaderStyle == false) ObjMRTableTbodyTrTdTableTbodyTrTd.style.borderBottom = "1px solid #ebe9e4";
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("textarea");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgBcc";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput3";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.autocomplete = "off";
	if (document.getElementById("AutoComplete").value == "1") {
		if (BrowserVer.Type == "MSIE") {
			var OnKeyPress = "liveSearchStart(this);";
			var OnKeyDown = "liveSearchKeyPress();";
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeypress = new Function(OnKeyPress);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeydown = new Function(OnKeyDown);
		} else {
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keypress", function (e) {
				liveSearchStart(this, e);
			}, true);
			ObjMRTableTbodyTrTdTableTbodyTrTdInput.addEventListener("keydown", function (e) {
				liveSearchKeyPress(e);
			}, true);
		}
		var OnFocus = "liveSearchHide();";
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.onfocus = new Function(OnFocus);
	}
	var OnKeyUp = "if (this.value.length > 35) { this.style.height = '40px'; } else this.style.height='22px';";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.onkeyup = new Function(OnKeyUp);

	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid silver";
	}

	if (Bcc)	{
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = Bcc;
		ObjMRTableTbodyTrTdTableTbodyTr.style.display = '';

		// Expand the field automatically if content large
		if(ObjMRTableTbodyTrTdTableTbodyTrTdInput.value.length > 35)
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.height = '40px';
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
	ObjMRTableTbodyTrTdTableTbodyTr.id = "ComposeMsgAttachmentsRow";
	ObjMRTableTbodyTrTdTableTbodyTr.style.display = "none";
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span onclick=\"Attachment('" + unique + "');\" style=\"cursor: pointer;\">" + Lang_Attachments + ":</span>";
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsColSpan2";
	ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.style.width = "100%";
	if (NewHeaderStyle == false) ObjMRTableTbodyTrTdTableTbodyTrTd.style.borderBottom = "1px solid #ebe9e4";
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgAttachments";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput5"; // Same style as To/Cc/etc
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "text";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.disabled = "1";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.width = "100%";
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid silver";
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);

	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Hidden field for email-encoding
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgCharset";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "hidden";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "Charset";
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);

	// Hidden field for the message UIDL ( so replied/forwarded markers work )
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgUIDL";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "hidden";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "UIDL";

	try
	{
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = MsgReaderData[ShowMsg][16];
	}
	catch (e)
	{
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = '';
	}

	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);

	// Hidden field for the message reply type ( e.g the flag to update the UIDL, forwarded, read, etc )
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgType";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "hidden";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "Type";

	// If null, set a blank field so the sent message is unread
	if(!ReadMsgReply)
	ReadMsgReply = '';
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = ReadMsgReply;

	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);

	// Hidden field for DraftID ( the message-id )
 	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgDraftID";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "hidden";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.name = "DraftID";
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
	ObjMRTableTbodyTrTdTableTbodyTr.id = "VideoMovedOptionsDestination";
	ObjMRTableTbodyTrTdTableTbodyTr.style.display = "none";
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Priority + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd8";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("select");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeEmailPriority2";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.className = "ObjMRTableTbodyTrTdTableTbodyTrTdInput4";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);

	// Turn off HTML editor if not allowed
	if(!allow_HtmlEditor) {
		//ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML = "<span id=\"ComposeModeText\" onclick=\"ToggleComposeMode(false);\">" + Lang_Text + "</span>";
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>" + Lang_Editor + "</b>: " + Lang_Text + "&nbsp;&nbsp;";
	} else if (ComposeMode == "HTML") {
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML += "<span id=\"ComposeModeText2\" onclick=\"ToggleComposeMode(false);\" style=\"cursor: pointer;\">Text</span> / <span id=\"ComposeModeHTML2\" onclick=\"ToggleComposeMode(true);\" style=\"cursor: pointer; font-weight: bold;\">HTML</span>";
	} else if (ComposeMode == "Text") {
		ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML += "<span id=\"ComposeModeText2\" onclick=\"ToggleComposeMode(false);\" style=\"cursor: pointer; font-weight: bold;\">" + Lang_Text + "</span> / <span id=\"ComposeModeHTML2\" onclick=\"ToggleComposeMode(true);\" style=\"cursor: pointer;\">HTML</span>";
	}

	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Start New Msg Header Row
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd5";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_Subject + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.id = "VideoMovedOptionsColSpan3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.colSpan = "3";
	ObjMRTableTbodyTrTdTableTbodyTrTd.style.width = "100%";
	if (NewHeaderStyle == false) ObjMRTableTbodyTrTdTableTbodyTrTd.style.borderBottom = "1px solid #ebe9e4";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput = document.createElement("input");
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.id = "ComposeMsgSubject";
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.type = "text";
	if (Reply) {
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.value = TestSubjectReply(ReadMsgReply, MsgReaderData[ShowMsg][2] );
	}
	ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.width = "100%";
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdTableTbodyTrTdInput.style.border = "1px solid silver";
	}
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTdInput);
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);

	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// Video Mail Help Guide
	ObjMRTableTbodyTrTdTableTbodyTr = document.createElement("tr");
	ObjMRTableTbodyTrTdTableTbodyTr.id = "VideoMovedHelpDestination";
	ObjMRTableTbodyTrTdTableTbodyTr.style.display = "none";

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTdvidmail";
	ObjMRTableTbodyTrTdTableTbodyTrTd.appendChild(document.createTextNode(Lang_VideoMail + ":"));
	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);

	ObjMRTableTbodyTrTdTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTdTableTbodyTrTd.className = "ObjMRTableTbodyTrTdTableTbodyTrTd8";
	ObjMRTableTbodyTrTdTableTbodyTrTd.innerHTML += "<a href='javascript:help(\"videomail.html\", \"english\")'>" + Lang_Help + "</a>";

	ObjMRTableTbodyTrTdTableTbodyTr.appendChild(ObjMRTableTbodyTrTdTableTbodyTrTd);
	ObjMRTableTbodyTrTdTableTbody.appendChild(ObjMRTableTbodyTrTdTableTbodyTr);

	// The rest of the interface
	ObjMRTableTbodyTrTdTable.appendChild(ObjMRTableTbodyTrTdTableTbody);
	ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTable);
	ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

	ObjMRTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTd.id = "ComposeMsgVideoContainer";
	ObjMRTableTbodyTrTd.className = "ObjMRTableTbodyTrTd2";
	ObjMRTableTbodyTrTd.style.display = "none";
	if (NewHeaderStyle == false) {
		ObjMRTableTbodyTrTd.style.paddingLeft = "1px";
		ObjMRTableTbodyTrTd.style.paddingTop = "10px";
	}
	ObjMRTableTbodyTrTdIFrame = document.createElement("iframe");
	ObjMRTableTbodyTrTdIFrame.id = "ComposeMsgVideoIFrame";
	ObjMRTableTbodyTrTdIFrame.width = "242";
	ObjMRTableTbodyTrTdIFrame.height = "204";
	ObjMRTableTbodyTrTdIFrame.src = "html/blankiframe.html";
	ObjMRTableTbodyTrTdIFrame.scrolling = "no";
	ObjMRTableTbodyTrTdIFrame.frameBorder = "0";
	ObjMRTableTbodyTrTdIFrame.marginHeight = "0";
	ObjMRTableTbodyTrTdIFrame.marginWidth = "0";
	if (NewHeaderStyle == true) {
		ObjMRTableTbodyTrTdIFrame.style.border = "1px solid #bad4ea";
	} else {
		ObjMRTableTbodyTrTdIFrame.style.border = "1px solid silver";
	}
	ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdIFrame);
	ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

	ObjMRTableTbodyTr = document.createElement("tr");
	ObjMRTableTbody.appendChild(ObjMRTableTbodyTr);

	ObjMRTableTbodyTrTd = document.createElement("td");
	ObjMRTableTbodyTrTd.id = "ComposeMsgTextContainer";
	ObjMRTableTbodyTrTd.width = "100%";
	ObjMRTableTbodyTrTd.height = "100%";
	if (NewHeaderStyle == false) {
		ObjMRTableTbodyTrTd.style.paddingLeft = "0px";
		ObjMRTableTbodyTrTd.style.paddingTop = "0px";
	}
	ObjMRTableTbodyTrTd.style.border='1px solid #EEE';
	
	ObjMRTableTbodyTrTdDiv = document.createElement("div");
	ObjMRTableTbodyTrTdDiv.id = "SpellCheckerBox";
	ObjMRTableTbodyTrTdDiv.style.display = "none";
	ObjMRTableTbodyTrTdDiv.style.width = "100%";
	if (window.ActiveXObject) {
		ObjMRTableTbodyTrTdDiv.style.height="100%";
	} else {
		ObjMRTableTbodyTrTdDiv.style.height="500px";
	}
	ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);

	ObjMRTableTbodyTrTdTextArea = document.createElement("textarea");
	ObjMRTableTbodyTrTdTextArea.id = "ComposeMsgText";
	var onFocusFunc = "FieldInFocus = true;";
	ObjMRTableTbodyTrTdTextArea.onfocus = new Function(onFocusFunc);
	var onBlurFunc = "FieldInFocus = false;";
	ObjMRTableTbodyTrTdTextArea.onblur = new Function(onBlurFunc);
	ObjMRTableTbodyTrTdTextArea.style.width = "100%";
	if (window.ActiveXObject) {
		ObjMRTableTbodyTrTdTextArea.style.height="100%";
	} else {
		ObjMRTableTbodyTrTdTextArea.style.height="500px";
	}
	if (NewHeaderStyle == true) {
		//ObjMRTableTbodyTrTdTextArea.style.border = "2px solid #bad4ea";
	} else {
		//ObjMRTableTbodyTrTdTextArea.style.border = "2px solid silver";
	}

	ObjMRTableTbodyTrTdTextArea.className = "ObjMRTableTbodyTrTdTextArea";
	var OnFocusFunc = "FieldInFocus = true;";
	ObjMRTableTbodyTrTdTextArea.onfocus = new Function(OnFocusFunc);
	var OnBlurFunc = "FieldInFocus = false;";
	ObjMRTableTbodyTrTdTextArea.onblur = new Function(OnBlurFunc);

	// Load our Signature
	var Signature = document.getElementById('Signature').innerHTML;
	//alert(Signature);

	if (ComposeMode == "HTML") {
		// Add our signature, make line breaks into HTML
		//Signature = Signature.replace(/\n|\r/gi, "<BR>");
	} else	{
		// Add our signature, make line breaks into HTML
		if (window.ActiveXObject)
		Signature = Signature.replace(/<br>/gi, "\r");
		Signature = Signature.replace(/<\/?[^>]+>/gi, "");
	}

	if (Reply) {
		var MsgFrom = MsgReaderData[ShowMsg][3];

		// Format the reply as a text
		if(ComposeMode == "Text") {

			var ReplyText = "";

			if (Reply != "Open") {
				// Add our signature, dont worry about linebreaks
				ReplyText += "\n\r\n\r" + Signature;

				ReplyText += "\n\r\n\rOn " + MsgReaderData[ShowMsg][4] + " , " + MsgFrom + " wrote:\n\r";

				// Make the reply from the XML of the server, nicely formatted in PHP/html2text
				ReplyText += MsgReaderData[ShowMsg][20];

			} else	{
				// We are opening a draft message
				ReplyText = MsgReaderData[ShowMsg][8];

			}

			ObjMRTableTbodyTrTdTextArea.value = ReplyText;
			ObjMRTableTbodyTrTdTextArea.focus();

		} else	{
			// We are replying to a message with the HTML editor

			// Load the body
			ObjMsgContainerDiv = document.createElement("div");
			ObjMsgContainerDiv.id = "msgReply";
			ObjMsgContainerDiv.className = "ObjMsgContainerDiv";
			ObjMsgContainerDiv.style.display = "";

			// Convert into HTML friendly format
			MsgFrom = MsgFrom.replace(/</, '&lt;');
			MsgFrom = MsgFrom.replace(/>/, '&gt;');
				    
		    // MsgReaderData[ShowMsg][2] == subject
		    // MsgReaderData[ShowMsg][5] == To
		    // MsgReaderData[ShowMsg][6] == CC?
			if (Reply != "Open") {
    			// Apply our default stylesheet to the message for fonts
    			ObjMsgContainerDiv.innerHTML = "<style> BODY { font-family:Arial, Helvetica, sans-serif;font-size:12px; }</style><br><br>On " + MsgReaderData[ShowMsg][4] + " , " + MsgFrom + " wrote:<br><br>";
    			ObjMsgContainerDiv.innerHTML += "<BLOCKQUOTE style='BORDER-LEFT: #5167C6 2px solid; MARGIN-LEFT: 5px; MARGIN-RIGHT: 0px; PADDING-LEFT: 5px; PADDING-RIGHT: 0px'>" + MsgReaderData[ShowMsg][8] + "</BLOCKQUOTE>";
    			ObjMRTableTbodyTrTd.appendChild(ObjMsgContainerDiv);
			    ObjMRTableTbodyTrTdTextArea.appendChild(document.createTextNode("<BR>" + Signature));
			} else {
    			ObjMsgContainerDiv.innerHTML += MsgReaderData[ShowMsg][8];
    			ObjMRTableTbodyTrTd.appendChild(ObjMsgContainerDiv);
			}
			
			if(window.ActiveXObject) {
				ObjMRTableTbodyTrTdTextArea.innerText += ObjMsgContainerDiv.innerHTML;
			} else {
				ObjMRTableTbodyTrTdTextArea.value += ObjMsgContainerDiv.innerHTML;
			}

			ObjMRTableTbodyTrTdTextArea.focus();
		}
	} else	{
		if (ComposeMode == "HTML") {
				ObjMRTableTbodyTrTdTextArea.appendChild(document.createTextNode("<BR>" + Signature));
		} else	{
				// Add our signature, dont worry about linebreaks
				ObjMRTableTbodyTrTdTextArea.appendChild(document.createTextNode("\n\r\n\r" + Signature + "\n\r"));
		}

		// Make the To field the default selected in focus
		document.getElementById('ComposeMsgTo').focus();
	}

	ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTextArea);
	ObjMRTableTbodyTr.appendChild(ObjMRTableTbodyTrTd);

	if (ComposeMode == "HTML") {
		oEdit1 = new FCKeditor("ComposeMsgText");
		oEdit1.BasePath = 'javascript/fckeditor/';
		oEdit1.Config["CustomConfigurationsPath"] = "javascript/fckeditor/atmailconfig.js";
		oEdit1.ToolbarSet = 'Atmail';
		oEdit1.ReplaceTextarea();
		//FCKeditorAPI.GetInstance('ComposeMsgText').EditorDocument.style.height = '500px';
	}

	// If forwarding a message, we need to fire off a Ajax response to rename the MIME parts on disk
	if (ReadMsgReply == "Forward") {
		var RawAttachments = MsgReaderData[ShowMsg][13].split("::");
		var AttachmentList = "";

		for(i in RawAttachments) {
			if(RawAttachments[i]) {
				AttachmentList += unescape(RawAttachments[i]) + ", ";
			}
		}

		// Take the last , off the name
		AttachmentList = AttachmentList.substr(0, AttachmentList.length-2);

		// Ajax call to move attachments on the server
		AttachMIME(RawAttachments, unique);

		document.getElementById('ComposeMsgAttachmentsRow').style.display = "";
		document.getElementById('ComposeMsgAttachments').value = unescape(AttachmentList);

		// Fire off our ajax request to make these attachments into our unique id for email
	}

	// Add select options for the Email Priority box
	AddSelectOption('ComposeEmailPriority', Lang_Normal, 'Normal');
	AddSelectOption('ComposeEmailPriority', Lang_High, 'High');
	AddSelectOption('ComposeEmailPriority', Lang_Low, 'Low');
	AddSelectOption('ComposeEmailPriority2', Lang_Normal, 'Normal');
	AddSelectOption('ComposeEmailPriority2', Lang_High, 'High');
	AddSelectOption('ComposeEmailPriority2', Lang_Low, 'Low');

	// Add select options for the email-from field ( with different personalities if available )
	var EmailFrom = document.getElementById('EmailFrom').innerHTML;
	var EmailFromArray = EmailFrom.split("::");
	for(i in EmailFromArray)	{

		if(EmailFromArray[i])	{
			AddSelectOption("ComposeMsgFrom", EmailFromArray[i], EmailFromArray[i]);
		}
	}

	// For Safari, doesn't add the To/CC/Bcc on top.opencompose for some unknown reason, must be at the end
	if(navigator.userAgent.indexOf("Safari") != -1)	{
		if(To)
		document.getElementById("ComposeMsgTo").value = To;
		if(Cc)
		document.getElementById("ComposeMsgCc").value = Cc;
		if(Bcc)
		document.getElementById("ComposeMsgBcc").value = Bcc;
	}

	DataIsLoading(false);
	document.onselectstart = SelectText;
}

function SpellCheck(GetData) {
	if (ComposeMode == "Text") {
		ObjComposeMsgText = document.getElementById("ComposeMsgText");
	}
	if (GetData == true) {
		DataIsLoading(true);
		SpellChkWords.length = 0;
		SpellChkReq = false;

		if (SpellChkReq && SpellChkReq.readyState < 4) SpellChkReq.abort();

		SpellChkReq = createXMLHttpRequest();

		SpellChkReq.onreadystatechange = SpellChkReqChange;
		var EmailMsg = "";
		if (ComposeMode == "HTML") {
			EmailMsg = FCKeditorAPI.GetInstance('ComposeMsgText').GetData();
		} else if (ComposeMode == "Text") {
			EmailMsg = ObjComposeMsgText.value;
		}
		var POSTString = "ajax=1&emailmessage=" + encodeURIComponent(EmailMsg);
		SpellChkReq.open("POST", "spell.php", true);
		SpellChkReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		SpellChkReq.send(POSTString);
	} else {
    LoadFolders("SpellChecker");
		ObjSpellCheckerBox = document.getElementById("SpellCheckerBox");

		if (ObjSpellCheckerBox.style.display == "none") {
			if (ComposeMode == "HTML") {
				ComposeModeStorage = FCKeditorAPI.GetInstance('ComposeMsgText').GetHTML();
				oEdit1 = null;

				ObjMRTableTbodyTrTd = document.getElementById("ComposeMsgTextContainer");
				ObjMRTableTbodyTrTd.innerHTML = "";

				ObjSpellCheckerBox = document.createElement("div");

				ObjSpellCheckerBox.id = "SpellCheckerBox";
				ObjSpellCheckerBox.style.display = "none";
				if (window.ActiveXObject) {
					ObjSpellCheckerBox.style.width = "100%";
					ObjSpellCheckerBox.style.height = "100%";
				} else {
					ObjSpellCheckerBox.style.width = "97%";
					ObjSpellCheckerBox.style.height = "500px";
				}
				ObjMRTableTbodyTrTd.appendChild(ObjSpellCheckerBox);
			} else if (ComposeMode == "Text") {
				ObjComposeMsgText.style.display = "none";

			}
			ObjSpellCheckerBox.className = "ObjSpellCheckerBox";
			ObjSpellCheckerBox.style.display = "";
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

			DataIsLoading(false);
		} else {
			ObjPopUpBox = document.getElementById("PopUpBox");
			if (!window.ActiveXObject && ObjPopUpBox) document.body.removeChild(ObjPopUpBox);
	    LoadFolders("ComposeMsg");
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
					ObjSpellCheckerBox.style.width = "97%";
					ObjSpellCheckerBox.style.height = "500px";
				}
				ObjMRTableTbodyTrTd.appendChild(ObjSpellCheckerBox);
		    ObjMRTableTbodyTrTdTextArea = document.createElement("textarea");
				ObjMRTableTbodyTrTdTextArea.id = "ComposeMsgText";
				ObjMRTableTbodyTrTdTextArea.value = UpdatedMsgData;
				ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTextArea);
				oEdit1 = new FCKeditor("ComposeMsgText");
				oEdit1.BasePath = 'javascript/fckeditor/';
				oEdit1.Config["CustomConfigurationsPath"] = "javascript/fckeditor/atmailconfig.js";
				oEdit1.ToolbarSet = 'Atmail';
				oEdit1.ReplaceTextarea();
				//FCKeditorAPI.GetInstance('ComposeMsgText').EditorDocument.style.height = '500px';
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

		    try {
		        var error = SpellChkReq.responseXML.getElementsByTagName("Error").firstChild.nodeValue;
		    } catch (e){}

	        if (error) {
	            alert(error);
	            return;
	        }

			for (var x = 0; x < SpellChkReq.responseXML.getElementsByTagName("Suggestion").length; x ++) {
				SpellChkWords.push(SpellChkReq.responseXML.getElementsByTagName("Suggestion")[x].firstChild.data.split(","));
			}
			SpellCheck();
		}
	}
}

// Cycle through each DOM element for the spell check
function CycleDOMNodes(ObjMasterNode) {
	for (var x = 0; x < ObjMasterNode.childNodes.length; x ++) {
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
	var AddSpace = false;
	if (SpellChkWords.length > 0) {
		TextToCk = TextToCk.split(/\b/);
		for (var x in TextToCk) {
			if (TextToCk[x].match(/\w/)) {
				TextToCk[x] = new Array(TextToCk[x], false);
				for (var y in SpellChkWords) {
					if (TextToCk[x][0] == SpellChkWords[y][0] && TextToCk[x][1] == false) {
						TextToCk[x][0] = "<span id=\"SpellChkWord" + AjustedTxtArray.length + "\" style=\"color: red; cursor: pointer; text-decoration: underline;\" onclick=\"SpellCheckSuggestion(" + AjustedTxtArray.length + ", " + y + ");\" oncontextmenu=\"SpellCheckSuggestion(" + AjustedTxtArray.length + ", " + y + "); return false;\">" + SpellChkWords[y][0] + "</span>";
						TextToCk[x][1] = true;
						AjustedTxtArray.push(1);
					}
				}
				TextToCk[x] = TextToCk[x][0];
			} else if (TextToCk[x] == " ") {
				if(ComposeMode == 'HTML')
				TextToCk[x] = "&nbsp;";
			}
		}
		return TextToCk.join("");
	} else {
		return TextToCk;
	}
}

function SpellCheckSuggestion(TxtAryIndex, SpellWord, NoEdit) {
	if (!NoEdit) NoEdit = false;

	if (window.ActiveXObject) {
		oPopup = window.createPopup();

		var oPopBody = oPopup.document.body;
		oPopBody.className = "oPopBody";

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
	 			ObjPopUpBoxItem.onclick = "parent.SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", " + i + ", " + NoEdit + "); parent.oPopup.hide();";
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

			ObjPopUpBoxItem.className = "ObjPopUpBoxItem4";

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
			ObjPopUpBoxItem.className = "ObjPopUpBoxItem";

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
			ObjPopUpBoxItem.onclick = "parent.AddToDictionary(" + TxtAryIndex + "); parent.oPopup.hide();";
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
		ObjPopUpBoxItem.className = "ObjPopUpBoxItem5";
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
		ObjPopUpBoxItem.className = "ObjPopUpBoxItem";

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
			ObjPopUpBoxItem.onclick = "parent.SpellChkFixWord(" + TxtAryIndex + ", " + SpellWord + ", 'Edit'); parent.oPopup.hide();";
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
				ObjPopUpBoxItem.className = "ObjPopUpBoxItem5";

		ObjPopUpBoxItem.style.cursor = "default";
		ObjPopUpBoxItem.style.fontFamily = "Arial, Helvetica, sans-serif";
		ObjPopUpBoxItem.style.fontSize = "9pt";
		ObjPopUpBoxItem.style.padding = "5px";
		ObjPopUpBoxItem.appendChild(document.createTextNode(Lang_Edit + " ..."));
		ObjPopUpBox.appendChild(ObjPopUpBoxItem);
	}

	if (window.ActiveXObject) {
		oPopBody.innerHTML = ObjPopUpBox.innerHTML;
		oPopup.show(MousePosXY[0], MousePosXY[1], 175, PopUpBoxHeight, document.body);
	} else {
		ObjPopUpBox.id = "PopUpBox";
						ObjPopUpBox.className = "ObjPopUpBox2";

		ObjPopUpBox.style.border = "1px solid #8EBEE5";
		ObjPopUpBox.style.padding = "1px";
		ObjPopUpBox.style.position = "absolute";
		ObjPopUpBox.style.width = "175px";
		ObjPopUpBox.style.height = PopUpBoxHeight + "px";
		ObjPopUpBox.style.top = MousePosXY[1];
		ObjPopUpBox.style.left = MousePosXY[0];
		ObjPopUpBox.style.backgroundColor = "white";
		document.body.appendChild(ObjPopUpBox);
	}
}

function SpellChkFixWord(TxtAryIndex, SpellWord, SpellWordCount, ConvertFromEdit) {
	ObjSpellChkWord = document.getElementById("SpellChkWord" + TxtAryIndex);
	if (SpellWordCount == "Edit") {
		ObjSpellChkWord.onclick = "";
		ObjSpellChkWord.innerHTML = "<input id=\"SpellChkEdit" + TxtAryIndex + "\" type=\"text\" value=\"" + ObjSpellChkWord.innerHTML + "\">";
		ObjSpellChkEdit = document.getElementById("SpellChkEdit" + TxtAryIndex);
		ObjSpellChkEdit.className = "ObjSpellChkEdit";
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
		ObjSpellChkWord.className = "ObjSpellChkWord";
		if (ConvertFromEdit == true) {
			var OnClickFunc = "SpellCheckSuggestion(" + TxtAryIndex + ", " + SpellWord + ");";
			ObjSpellChkWord.onclick = new Function(OnClickFunc);
		}
	}
}

function AddToDictionary(TxtAryIndex) {
	DataIsLoading(true);
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
	}

	AddToDicReq = false;

	if (AddToDicReq && AddToDicReq.readyState < 4) AddToDicReq.abort();

	AddToDicReq = createXMLHttpRequest();

	AddToDicReq.onreadystatechange = AddToDicReqChange;
	AddToDicReq.open("GET", "spell.php?add=1&replace=" + encodeURIComponent(AddWord), true);
	AddToDicReq.send(null);
}

function AddToDicReqChange() {
	if (AddToDicReq.readyState == 4 && AddToDicReq.status == 200) DataIsLoading(false);
}

function ReplaceTags(xStr) {

	var regExp = /<p>/gi;
	xStr = xStr.replace(regExp,"\n");

	var regExp = /<br>/gi;
	xStr = xStr.replace(regExp,"\n");

	var regExp = /<\/?[^>]+>/gi;
	xStr = xStr.replace(regExp,"");

	var regExp = /&lt;/gi;
	xStr = xStr.replace(regExp,"<");

	var regExp = /&gt;/gi;
	xStr = xStr.replace(regExp,">");

	return xStr;
}

function ToggleBccRow(Override) {
	ObjComposeMsgBccRow = document.getElementById("ComposeMsgBccRow");
	if (Override == true) {
		ObjComposeMsgBccRow.style.display = "";
	} else {
		if (ObjComposeMsgBccRow.style.display == "") {
			ObjComposeMsgBccRow.style.display = "none";
		} else {
			ObjComposeMsgBccRow.style.display = "";
		}
	}
}

function UpdateAttachDiv(AttachmentList)	{
	ObjAttachmentsField = document.getElementById("ComposeMsgAttachments");
	ObjAttachmentsRow = document.getElementById("ComposeMsgAttachmentsRow");

	if (AttachmentList)	{
		ObjAttachmentsField.value = AttachmentList;
	} else	{
		ObjAttachmentsField.value = "";
	}

	if (ObjAttachmentsField.value == "") {
 		ObjAttachmentsRow.style.display = "none";
	} else {
 		ObjAttachmentsRow.style.display = "";
	}
}

function ToggleComposeMode(HTML) {

	if(navigator.userAgent.indexOf("Safari") != -1)	{
		alert("Currently Safari does not support HTML editing. This will be included once supported for Safari. In the meantime please use Firefox or IE for this feature");
		return;
	}
	if (document.getElementById("SpellCheckerBox").style.display == "") {
		alert("You can not change edit modes whilst using the spell checker.");
	} else {
		if (HTML == true && ComposeMode != "HTML") {
			ComposeMode = "HTML";
			document.getElementById("ComposeModeHTML").style.fontWeight = "bold";
			document.getElementById("ComposeModeText").style.fontWeight = "";
			document.getElementById("ComposeModeHTML2").style.fontWeight = "bold";
			document.getElementById("ComposeModeText2").style.fontWeight = "";
			ObjComposeMsgText = document.getElementById("ComposeMsgText");
			oEdit1 = new FCKeditor("ComposeMsgText");
			oEdit1.BasePath = 'javascript/fckeditor/';
			oEdit1.Config["CustomConfigurationsPath"] = "javascript/fckeditor/atmailconfig.js";
			oEdit1.ToolbarSet = 'Atmail';
			ObjComposeMsgText.value = ObjComposeMsgText.value.replace(/\r|\n/g, "<br>\n");
			oEdit1.ReplaceTextarea();
			//FCKeditorAPI.GetInstance('ComposeMsgText').EditorDocument.style.height = '500px';

		} else if (HTML == false && ComposeMode != "Text") {
			ComposeMode = "Text";
			document.getElementById("ComposeModeHTML").style.fontWeight = "";
			document.getElementById("ComposeModeText").style.fontWeight = "bold";
			document.getElementById("ComposeModeHTML2").style.fontWeight = "";
			document.getElementById("ComposeModeText2").style.fontWeight = "bold";
			ComposeModeStorage = FCKeditorAPI.GetInstance('ComposeMsgText').GetHTML();
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
				ObjMRTableTbodyTrTdDiv.style.height = "500px";
			}
			ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdDiv);

			ObjMRTableTbodyTrTdTextArea = document.createElement("textarea");
			ObjMRTableTbodyTrTdTextArea.id = "ComposeMsgText";
			ObjMRTableTbodyTrTdTextArea.style.width = "100%";
			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdTextArea.style.height = "100%";
			} else {
				ObjMRTableTbodyTrTdTextArea.style.height = "500px";
			}
			if (NewHeaderStyle == true) {
				ObjMRTableTbodyTrTdTextArea.style.border = "1px solid #bad4ea";
			} else {
				ObjMRTableTbodyTrTdTextArea.style.border = "1px solid silver";
			}
			ObjMRTableTbodyTrTdTextArea.className = "ObjMRTableTbodyTrTdTextArea2";
			var OnFocusFunc = "FieldInFocus = true;";
			ObjMRTableTbodyTrTdTextArea.onfocus = new Function(OnFocusFunc);
			var OnBlurFunc = "FieldInFocus = false;";
			ObjMRTableTbodyTrTdTextArea.onblur = new Function(OnBlurFunc);
			ObjTmpDiv = document.createElement("div");
			ObjTmpDiv.innerHTML = ComposeModeStorage;
			if (window.ActiveXObject) {
				ObjMRTableTbodyTrTdTextArea.innerText = ObjTmpDiv.innerText;
			} else {
				// For firefox use textContent rather then innerText
				ObjMRTableTbodyTrTdTextArea.value = ObjTmpDiv.textContent;
			}
			ObjMRTableTbodyTrTd.appendChild(ObjMRTableTbodyTrTdTextArea);
			ObjMRTableTbodyTrTdTextArea.focus();
		}
	}
}

function SendMsg(unique, draft) {
	if (document.getElementById("ComposeMsgTo").value) {
		DataIsLoading(true);
		SendMessagesReq = false;

		if (SendMessagesReq && SendMessagesReq.readyState < 4) SendMessagesReq.abort();

		SendMessagesReq = createXMLHttpRequest();

		SendMessagesReq.onreadystatechange = SendMessagesReqChange;

		// Build our HTTP post for the message
		var POSTString = "ajax=1&unique=" + encodeURIComponent(unique);
		//+ "&UIDL=" + MsgSendUIDL + "&unique=" + MsgSendUnique + "&type=" + MsgSendType + "&DraftID=" + MsgSendDraftID + "&Draft=&Charset=" + MsgSendCharset;

		// Character set
		POSTString += "&Charset=" + encodeURIComponent(document.getElementById("ComposeMsgCharset").value);

		// DraftID for replying/sending a draft ( used to delete the msg in the Draft folder after sending )
		POSTString += "&DraftID=" + encodeURIComponent(document.getElementById("ComposeMsgDraftID").value);


		// UIDL field ( used to pass on the message status, e.g forwarded, replied, etc
		POSTString += "&UIDL=" + encodeURIComponent(document.getElementById("ComposeMsgUIDL").value);
		try {
			var id = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0];
			var folder = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][1];
			POSTString += "&id=" + encodeURIComponent(CalcMoveMsgs(id, folder));
		}
		catch(e) {

		}


		// Type field ( e.g forwarded, replied, for UIDL update )
		POSTString += "&type=" + encodeURIComponent(document.getElementById("ComposeMsgType").value);

		// Email To/Cc/Bcc/Header fields
		POSTString += "&emailto=" + encodeURIComponent(document.getElementById("ComposeMsgTo").value);
		POSTString += "&emailpriority=0";
		POSTString += "&emailcc=" + encodeURIComponent(document.getElementById("ComposeMsgCc").value);
		POSTString += "&emailfrom=" + encodeURIComponent(document.getElementById("ComposeMsgFrom").value);
		var VideoStream = GetVideoID(false, encodeURIComponent(document.getElementById("ComposeMsgFrom").value), encodeURIComponent(document.getElementById("ComposeMsgSubject").value));
		if (document.getElementById("VideoMail").value == 1) {
			if (document.getElementById("ComposeMsgVideoContainer").style.display == "none") {
				POSTString += "&emailpriority=" + encodeURIComponent(document.getElementById("ComposeEmailPriority").value);
				if (VideoStream != null) {
					if (confirm("You have a video recorded, do you want to send it?")) POSTString += "&VideoStream=" + VideoStream;
				}
			} else {
				POSTString += "&emailpriority=" + encodeURIComponent(document.getElementById("ComposeEmailPriority2").value);
				if (VideoStream != null) {
				POSTString += "&VideoStream=" + VideoStream;
				// Set the Videostream to null, message sent, so not to alert on the next compose attempt
				VideoStreamUID = null;
				}

			}
		} else {
			POSTString += "&emailpriority=" + encodeURIComponent(document.getElementById("ComposeEmailPriority").value);
		}

		if(draft == '1')	{
			POSTString += "&Draft=1";
		}

		if (document.getElementById("ComposeMsgBcc").style.display == "") {
			POSTString += "&emailbcc=" + encodeURIComponent(document.getElementById("ComposeMsgBcc").value);
		}
		POSTString += "&emailsubject=" + encodeURIComponent(document.getElementById("ComposeMsgSubject").value);

		if (ComposeMode == "HTML") {
			POSTString += "&contype=text/html";

			// MUST CALL encodeURIComponent for big data on posts - Otherwise data will be stripped!
			var msg = encodeURIComponent(FCKeditorAPI.GetInstance('ComposeMsgText').GetHTML());
			POSTString += "&emailmessage=" + msg;

		} else if (ComposeMode == "Text") {
			POSTString += "&contype=text/plain";

			// MUST CALL encodeURIComponent for big data on posts - Otherwise data will be stripped!
			POSTString += "&emailmessage=" + encodeURIComponent(document.getElementById("ComposeMsgText").value);
		}
		SendMessagesReq.open("POST", "sendmail.php", true);
		SendMessagesReq.setRequestHeader("Method", "POST sendmail.php HTTP/1.1");

		SendMessagesReq.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
      SendMessagesReq.setRequestHeader("Connection", "close");

		try {

		var type = document.getElementById("ComposeMsgType").value.toLowerCase();

		// Update the message status buttons in realtime
		if(type == 'reply' || type == 'forward')	{

		if (window.ActiveXObject) {
			document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).src = "imgs/simple/shim.gif";
			document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_" + type + ".png', sizingMethod='image')";
		} else {
			document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][0]).src = "imgs/simple/icon_" + type + ".png";
		}

	}

} catch(e)	{

}

		SendMessagesReq.send(POSTString);
	} else {
		alert("Please enter an email address in the to field.");
	}
}

function SendMessagesReqChange() {
	if (SendMessagesReq.readyState == 4 && SendMessagesReq.status == 200) {

		DataIsLoading(false);

		// Check the response
		if (SendMessagesReq.responseXML) {

			try
			{

				var status = SendMessagesReq.responseXML.getElementsByTagName("Status")[0].firstChild.data;

					if(status == 0)	{
					LoadFolders();
					LoadMsgs(MsgListData['CurrentFolder']);
					ShowEmailSentNotice();

					} else	{
				    	alert(SendMessagesReq.responseXML.getElementsByTagName("StatusMessage")[0].firstChild.data);
    	    		    LoadFolders();
			    		LoadMsgs(MsgListData['CurrentFolder']);
					}

				return 0;
			} catch(e)	{
				alert('Could not send message - Please check the recipients are correctly formatted and contact the System Admin');
			}

		}

	}
}

// Calculate moved messages only if it's from the POP3 server in the Inbox, or IMAP ( any folder )
function CalcMoveMsgs(v, folder)	{

// If expunge on logout, do not change the message array index
if(document.getElementById('Expunge').value == '1')
return v;

var c = 0;

	if(MailType == 'pop3' && folder =='Inbox')	{

		for(i in MsgArrayMove)	{
			c++;
			if(v == MsgArrayMove[i])
			return c;
		}

	} else	{
		return v;
	}


}

// Splice the array down for POP3/IMAP mailboxes, so the unique ID is in sync
function SpliceMoveMsgs(v, folder)	{
var c = 0;

// We need to correct our ID if we have moved messages in the past too!
v = CalcMoveMsgs(v, folder);

if(!parseInt(v)) return v;

for(i in MsgArrayMove)	{
	if(MsgArrayMove[i] == v) MsgArrayMove.splice(c,1);
	c++;
}

}

function StyleSheetChanger()	{

var mysheet=document.styleSheets[0]
var myrules=mysheet.cssRules? mysheet.cssRules: mysheet.rules
mysheet.crossdelete=mysheet.deleteRule? mysheet.deleteRule : mysheet.removeRule
for (i=0; i<myrules.length; i++){
if(myrules[i].selectorText.toLowerCase().indexOf("a")!=-1){
mysheet.crossdelete(i)

i-- //decrement i by 1, since deleting a rule collapses the array by 1
}
}

}

// Check our response - If it's an error reload the entire Window with the Error message ( e.g timeout, password, access probs )
function CheckXMLError(XMLReq)	{
			try
			{
				var err = XMLReq.responseXML.getElementsByTagName("ErrorMessage")[0].firstChild.data;

				if (XMLReq.responseXML.getElementsByTagName("ErrorMessage")[0].getAttribute('action') == 'logout')
				{
				    alert(err);
				    document.location = 'index.php?func=logout';
				    return;
				}
				document.write(err);

				return 0;
			}
			catch (e)
			{
				return 1;
			}

}

function TestAjaxFrame(func, args)	{

	if(!args)
		args = '';

	// Test if we are inside the Ajax panel
	try
	{
		var ErrorText = document.getElementById("FolderBox").innerHTML;
	}
	catch (e)
	{
	window.location.href='parse.php?file=html/LANG/simple/showmail_interface.html&ajax=1&func=' + func + '&' + args;
	return 1;
	}

}

function TestAjaxFrameNull()	{

	// Test if we are inside the Ajax panel
	try
	{
		var ErrorText = document.getElementById("FolderBox").innerHTML;
	}
	catch (e)
	{
	return 1;
	}

}

// About Window for @Mail
function aboutwin() {
var wdh = 270; hgt = 290;

helpWin = open('util.php?func=about', '', 'width=' + wdh + ',height=' + hgt + ',left=100,top=100,scrollbars=no');
}

function LoadingFade()	{
	fadeIn("Connecting", 100);
	fadeIn("LoadingImage", 100);
}

function FadeStatus(objId, opacity)    {
    if (opacity >= 0) {
      opacity -= 5;
      this.ObjFadeWindow.style.filter = "Alpha(Opacity=" + opacity + ");";
      window.setTimeout("FadeStatus('"+objId+"',"+opacity+")", 10);
    }
       if(opacity == '0')
       this.ObjFadeWindow.parentNode.removeChild(this.ObjFadeWindow);
}

function FadeStatusReverse(objId, opacity)     {
    if (opacity <= 65) {
      opacity += 5;
      this.ObjFadeWindow.style.filter = "Alpha(Opacity=" + opacity + ");";
      window.setTimeout("FadeStatusReverse('"+objId+"',"+opacity+")", 10);
    }
}

function LogoutAjax(EmptyTrash)        {
	LoadLoginPage('5');
	FadeStatusReverse(this.ObjFadeWindow, '5');
}

function WebmailLogin(username, domain, password, mailserver, protocol, language)      {
	//atmailRoot = (BrowserVer.Type == 'Firefox') ? '../' : '';
	atmailroot = document.location.href;
	atmailroot = atmailroot.replace(/index\.php.*/, '');
	DataIsLoading(true);
	WebMailLoginReq = createXMLHttpRequest();
	WebMailLoginReq.onreadystatechange = WebMailLoginReqChange;

	var POSTString = "ajax=1&username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password) + "&MailServer=" + encodeURIComponent(mailserver) + "&pop3host=" + encodeURIComponent(domain) + "&MailType=" + encodeURIComponent(protocol) + "&Language=" + "&LoginType=ajax";

	WebMailLoginReq.open("POST", atmailroot + "/atmail.php", true);

	WebMailLoginReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	WebMailLoginReq.send(POSTString);
}

function WebMailLoginReqChange() {
       if (WebMailLoginReq.readyState == 4 && WebMailLoginReq.status == 200) {
               DataIsLoading(false);

               var err;

               try
               {
               		err = WebMailLoginReq.responseXML.getElementsByTagName("ErrorMessage")[0].firstChild.data;
               } catch(e)      {

               }

               if ( WebMailLoginReq.responseXML && err ) {

               //alert('Cannot login in, there is an error. Check login details and mail-server responding');
               //var ErrorBody = WebMailLoginReq.responseXML.getElementsByTagName("ErrorBody")[0].firstChild.data;
	           //alert(ErrorBody);
			   GroupingFrame.document.getElementById('AuthStatus').innerHTML = "<font color='red'>Server responded: " + err + "</font>";

               return 0;

               } else  {
			   		document.getElementById('EmailFrom').innerHTML=WebMailLoginReq.responseText;
			        FadeStatus(ObjFadeWindow, '65');
					location.href='parse.php?file=html/LANG/simple/showmail_interface.html&ajax=1&func=Inbox&To=';
			        LoadMsgs();
			   }
       }
}

function LoadLoginPage(Amount) {
	this.ObjAdvancedWindow = createObjAdvancedWindow(ColorScheme, "440px", "310px");
	document.body.appendChild(this.ObjAdvancedWindow);
	document.getElementById("ObjAdvancedWindow").style.display = "none";

	this.ObjAdvancedWindowBody = document.createElement("div");
	this.ObjAdvancedWindowBody.style.width = (BrowserVer.Type == "MSIE") ? "100%" : "99%";
	this.ObjAdvancedWindowBody.style.height = (BrowserVer.Type == "MSIE") ? "100%" : "99%";
	this.ObjAdvancedWindowBody.style.backgroundColor = "#ffffff";
	this.ObjAdvancedWindowBody.style.padding = "2px";
	this.ObjAdvancedWindow.appendChild(this.ObjAdvancedWindowBody);

	var ObjIFrame = document.createElement("iframe");
	ObjIFrame.name = "GroupingFrame";
	ObjIFrame.scrolling = "auto";
	ObjIFrame.width = "100%";
	ObjIFrame.height = "100%";
	ObjIFrame.src = "parse.php?file=html/login-light.html";
	ObjIFrame.frameBorder = "0";
	ObjIFrame.marginHeight = "0";
	ObjIFrame.marginWidth = "0";
	ObjIFrame.application = "yes";
	this.ObjAdvancedWindowBody.appendChild(ObjIFrame);

	centerObjAdvancedWindow();
	document.getElementById("ObjAdvancedWindow").style.display = "";
	centerObjAdvancedWindow();

	DataIsLoading(false);
}

function BrowserVerChk() {
        this.Type = false;
        this.TypeLong = false;
        this.Version = false;
        this.LateGen = false;

        if (navigator.appVersion.indexOf("MSIE") != -1) {
                this.Type = "MSIE";
                this.TypeLong = "Internet Explorer";
                var TempArray = navigator.appVersion.split("MSIE");
                this.Version = parseFloat(TempArray[1]);
                if (this.Version >= 6) this.LateGen = true;
        } else if (navigator.userAgent.indexOf("Firefox") != -1) {
                this.Type = "Firefox";
                this.TypeLong = "Firefox";
                var VersionIndex = navigator.userAgent.indexOf("Firefox") + 8;
                this.Version = parseFloat(navigator.userAgent.charAt(VersionIndex) + "." + navigator.userAgent.charAt(VersionIndex + 2)) * 1;
                if (this.Version >= 1.5) this.LateGen = true;
        }
}
function fadeIn(objId,opacity) {
  if (document.getElementById) {
    obj = document.getElementById(objId);
    if (opacity >= 0) {
      setOpacity(obj, opacity);
      opacity -= 10;
      window.setTimeout("fadeIn('"+objId+"',"+opacity+")", 20);
    }
  }
}

function setOpacity(obj, opacity) {
  opacity = (opacity == 100)?99.999:opacity;
  obj.style.opacity = opacity/100;
  if(opacity == 0)	{
		document.getElementById("LoadingText").style.display = "none";
		document.getElementById("LoadingIcon").style.display = "none";
		document.getElementById("BrandingLogo").style.display = "";
		document.body.style.cursor = '';
  }
}


// Change an attachment MIME for forwarding
function AttachMIME(AttachmentList, unique) {

		AttachMIMEsReq = false;

		if (AttachMIMEsReq && AttachMIMEsReq.readyState < 4) AttachMIMEsReq.abort();

		AttachMIMEsReq = createXMLHttpRequest();

		AttachMIMEsReq.onreadystatechange = AttachMIMEsReqChange;

		// Build our HTTP post for the message
		var POSTString = "func=renameattach&ajax=1&unique=" + encodeURIComponent(unique);

		for(i in AttachmentList)	{
			// Unescape the attachment name when sending, since encodeURICompontent will do it twice
			if(AttachmentList[i])
				POSTString += "&Attachment[]=" + encodeURIComponent(unescape(AttachmentList[i]));
		}

		AttachMIMEsReq.open("GET", "compose.php?" + POSTString, true);
		AttachMIMEsReq.send(null);
}

function AttachMIMEsReqChange() {
	if (AttachMIMEsReq.readyState == 4 && AttachMIMEsReq.status == 200) {
		DataIsLoading(false);
	}
}

// Create select box options ( for compose panel )

function AddSelectOption(selectbox, text, value) {
  var option = document.createElement('option');
  option.text = text;
  option.value = value;
  var select = document.getElementById(selectbox);

  try {
    select.add(option, null); // standards compliant; doesn't work in IE
  }
  catch(e) {
    select.add(option); // IE only
  }
}

// Delete the message cache, then reload the email from the server w/ the images set
function DisplayImages()	{
id = MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0];
MsgListData["Data"][MsgListData["Ctrl"]["Selected"][0]][0] = false;
ReadMsg(null, null, null, 1);
}

// Load an XML doc, on success eval object
function loadXMLDoc(url, object) {

// branch for native XMLHttpRequest object
req = createXMLHttpRequest();

req.onreadystatechange = processReqChange;
req.open("GET", url, true);
req.send(null);

}

function processReqChange(object) {
// only if req shows "loaded"

	if (req.readyState == 4 && req.status == 200) {
	DisplayImages();
	return true
	} else {
	return false
	}

}

// Legacy function to open compose panel - From ReadMsg.pm and abook functions
function opencompose(to, cc, bcc, target)	{
ComposeMsg(null, to, cc, bcc);
}

function help(currFile, lang) {
var wdh = 700; hgt = 500;

if(!currFile)
currFile = 'file.html'

helpWin = open('parse.php?file=html/' + lang + '/help/filexp.html&FirstLoad=1&HelpFile=' + currFile +  '', '', 'width=' + wdh + ',height=' + hgt + ',left=100,top=100,status=no,resizable=yes,scrollbars=yes');
}

function MarkMessage(Flag)	{

			DataIsLoading(true);

			MarkMessageReq = false;

			if (MarkMessageReq && MarkMessageReq.readyState < 4) MarkMessageReq.abort();

			MarkMessageReq = createXMLHttpRequest();

			var ids = '';
			var folders = '';
			
			for (i in MsgListData["Ctrl"]["Selected"]) {
				ids += "&id[]=" + MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][0];
				folders += "&folders[]=" + Url.encode(MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][1]);
				//uidl += MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][12];
			}
			
			MarkMessageReq.open("GET", "showmail.php?ajax=1" + folders + ids + "&Flag=" + encodeURIComponent(Flag), true);
			MarkMessageReq.send(null);

			for (i in MsgListData["Ctrl"]["Selected"]) {
				if (Flag == 'o') {
					MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][9] = "read";
					if (window.ActiveXObject) {
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).src = "imgs/simple/shim.gif";
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_read.png', sizingMethod='image')";
					} else {
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).src = "imgs/simple/icon_read.png";
					}
					document.getElementById("ListBoxMsgFrom" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDiv2";
					document.getElementById("ListBoxMsgSubject" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDiv2";
					document.getElementById("ListBoxMsgDate" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDiv2";
					document.getElementById("ListBoxMsgSize" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDiv2";
				} else if(Flag == 'x')	{
	
					MsgListData["Data"][MsgListData["Ctrl"]["Selected"][i]][9] = "unread";
					if (window.ActiveXObject) {
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).src = "imgs/simple/shim.gif";
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='imgs/simple/icon_unread.png', sizingMethod='image')";
					} else {
						document.getElementById("ListBoxMsgIcon" + MsgListData["Ctrl"]["Selected"][i]).src = "imgs/simple/icon_unread.png";
					}
	
					document.getElementById("ListBoxMsgFrom" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDivBold";
					document.getElementById("ListBoxMsgSubject" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDivBold";
					document.getElementById("ListBoxMsgDate" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDivBold";
					document.getElementById("ListBoxMsgSize" + MsgListData["Ctrl"]["Selected"][i]).className = "ObjMLTableTbodyTrTdDivBoldSize";
				}
			}
			DataIsLoading(false);
}

function TestSubjectReply(Type, Subject)	{

	if(Type == 'Reply' || Type == 'ReplyAll')	{

	var group = new RegExp(/^Re.*?:|^Ynt|^TR:|^Oggetto:|^VS:|^Awt:|^Aw:|^TR:|^R:|^RES:/i);

		// If one of the Subject replys match, return as normal
		if(group.exec(Subject))	{
		return Subject;
		} else	{
		// Otherwise append Re: subject
		return "Re: " + Subject;
		}

	} else if(Type == 'Forward')	{

	var group = new RegExp(/^Fwd:/i);

		// If the subject has Fwd, return as normal
		if(group.exec(Subject))	{
		return Subject;
		} else	{
		// Otherwise append Fwd: subject
		return "Fwd: " + Subject;
		}


	} else	{
	return Subject;
	}

}

function getXMLfieldName(XMLobj, Field)	{

	var field;

	try {
	field = XMLobj.getElementsByTagName(Field)[0].firstChild.data;
	} catch(e) {
	field = '';
	}

	return field;
}

function formatHTMLtoText(txt, ReadMsgReply)	{
	var regExp = /<\/?[^>]+>/gi;

	// remove newlines from html so we
	// only have newlines made from <br> tags
    txt = txt.replace(/\n/g, '');

    txt = txt.replace(/<BR>/gi,"\n");
    txt = txt.replace(/<P>/gi,"\n");
    txt = txt.replace(/&nbsp;/gi, ' ');
    txt = txt.replace(/&quot;/gi, '"');
    txt = txt.replace(/&gt;/gi, '>');
    txt = txt.replace(/&lt;/gi, '<');
    txt = txt.replace(/&amp;/gi, '&');
    txt = txt.replace(/&copy;/gi, '(c)');
    txt = txt.replace(/&trade;/gi, '(tm)');
    txt = txt.replace(/&#8220;/g, '"');
    txt = txt.replace(/&#8221;/g, '"');
    txt = txt.replace(/&#8211;/g, '-');
    txt = txt.replace(/&#8217;/g, "'");
    txt = txt.replace(/&#38;/g, '&');
    txt = txt.replace(/&#169;/g, '(c)');
    txt = txt.replace(/&#8482;/g, '(tm)');
    txt = txt.replace(/&#151;/g, '--');
    txt = txt.replace(/&#147;/g, '"');
    txt = txt.replace(/&#148;/g, '"');
    txt = txt.replace(/&#149;/g, '*');
    txt = txt.replace(/&reg;/ig, '(R)');
    txt = txt.replace(/&bull;/ig, '*');
    txt = txt.replace(/&[&;]+;/g, '');
    txt = txt.replace(regExp,"");

	// If we are replying, quote email
	if(ReadMsgReply == 'Reply') {
	txt = txt.replace(/^\s?/gm, "\n>");
	txt = txt.replace(/^$/gm, "");
	}

	return txt;
}

function drawDate(elem) {
    var curdate = new Date()
	var DayOfWeek = new Array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");
	var MonthName = new Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
    var minutes = curdate.getMinutes();
    if (minutes < 10) minutes = '0' + minutes;
    var MsgDate = DayOfWeek[curdate.getDay()] + " " + MonthName[curdate.getMonth()] + " " + curdate.getDate() + " " + curdate.getHours() + ":" + minutes;
}


// Make in one template

function createObjFadeWindow()	{
	WindowOpen = true;

	try {
	changeScroll('hidden');
	} catch(e)	{

	}

	var ObjNewFadeWindow = document.createElement("div");
	ObjNewFadeWindow.id = "ObjFadeWindow";

	ObjNewFadeWindow.style.position = "absolute";
	ObjNewFadeWindow.style.top = "0px";
	ObjNewFadeWindow.style.left = "0px";
	ObjNewFadeWindow.style.width = "100%";
	ObjNewFadeWindow.style.height = "100%";
	ObjNewFadeWindow.style.backgroundColor = "#b3b3b3";
	ObjNewFadeWindow.style.cursor = "not-allowed";
	ObjNewFadeWindow.style.filter = "Alpha(Opacity=50)";
	ObjNewFadeWindow.style.opacity = "0.50";
	ObjNewFadeWindow.style.zIndex = "995";

	return ObjNewFadeWindow;
}

// Get the window height for resizing the floating div
function getWindowHeight() {
	var windowHeight = 0;
	if (typeof(window.innerHeight) == 'number') {
		windowHeight = window.innerHeight;
	}
	else {
		if (document.documentElement && document.documentElement.clientHeight) {
			windowHeight = document.documentElement.clientHeight;
		}
		else {
			if (document.body && document.body.clientHeight) {
				windowHeight = document.body.clientHeight;
			}
		}
	}

	return windowHeight;
}

// Get the window width for resizing the floating div
function getWindowWidth() {
	var windowWidth = 0;
	if (typeof(window.innerWidth) == 'number') {
		windowWidth = window.innerWidth;
	}
	else {
		if (document.documentElement && document.documentElement.clientWidth) {
			windowWidth = document.documentElement.clientWidth;
		}
		else {
			if (document.body && document.body.clientWidth) {
				windowWidth = document.body.clientWidth;
			}
		}
	}

	return windowWidth;
}

function createObjAdvancedWindow(ColorScheme, Width, Height, Top, Left)	{
	if (!Top) Top = "20%";
	if (!Left) Left = "15%";
	if (!Width) Width = "70%";
	if (!Height) Height = "60%";

	var ObjAdvancedWindow = document.createElement("div");
	ObjAdvancedWindow.id = "ObjAdvancedWindow";
	ObjAdvancedWindow.style.position = "absolute";

	// Center of the screen please!
	ObjAdvancedWindow.style.margin = 'auto';
	ObjAdvancedWindow.style.textAlign = 'left';
	//ObjAdvancedWindow.style.top = Top;
	//ObjAdvancedWindow.style.left = Left;
	ObjAdvancedWindow.style.width = Width;
	ObjAdvancedWindow.style.height = Height;
	ObjAdvancedWindow.style.borderTop = "2px solid " + AppColors[ColorScheme][0];
	ObjAdvancedWindow.style.borderRight = "1px solid " + AppColors[ColorScheme][0];
	ObjAdvancedWindow.style.borderBottom = "2px solid " + AppColors[ColorScheme][0];
	ObjAdvancedWindow.style.borderLeft = "1px solid " + AppColors[ColorScheme][0];
	ObjAdvancedWindow.style.backgroundColor = AppColors[ColorScheme][1];
	ObjAdvancedWindow.style.padding = "2px";
	ObjAdvancedWindow.style.zIndex = "999";

	WindowOpen = true;

	return ObjAdvancedWindow;
}


function centerObjAdvancedWindow() {

	if (BrowserVer.Type == "Safari")	{
	var contentElement = document.getElementById('ObjAdvancedWindow');

	contentElement.style.top = '20%';
	contentElement.style.left = '20%';
	return
	}

	if (document.getElementById) {

		var windowHeight = getWindowHeight();
		var windowWidth = getWindowWidth();

		//alert('in center = ' + windowHeight);

		if (windowHeight > 0) {
			var contentElement = document.getElementById('ObjAdvancedWindow');
			if (contentElement) {
				var contentHeight = contentElement.offsetHeight;
				var contentWidth = contentElement.offsetWidth;

				// Background "Halo" effect needs a little offset under IE
				//if(BrowserVer.Type == "MSIE")
				//	contentHeight = contentHeight - 4;

				if (windowHeight - contentHeight > 0) {
					//contentElement.style.position = 'relative';
					contentElement.style.top = ((windowHeight / 2) - (contentHeight / 2)) + 'px';

					// Required for IE. style.margin = 'auto'; in FF works already
					//if(BrowserVer.Type == "MSIE")
					contentElement.style.left = ((windowWidth / 2) - (contentWidth / 2)) + 'px';

				}
				else {
					//alert('in here ie?');
					contentElement.style.position = 'static';
				}
			}
		}

		if (document.getElementById('ObjFadeWindow') && document.getElementById("ObjAdvancedWindow")) {
			if (document.getElementById('ObjAdvancedWindow').style.width == '599px')	{
				document.getElementById('ObjFadeWindow').style.backgroundImage = "url(imgs/caloverlay-big.png)";
			} else {
				document.getElementById('ObjFadeWindow').style.backgroundImage = "url(imgs/caloverlay-small.png)";
			}

			document.getElementById('ObjFadeWindow').style.backgroundRepeat = "no-repeat";
			document.getElementById('ObjFadeWindow').style.backgroundPosition = "center";
		}

	}

}

function centerObjWindow()	{

	try {
		centerObjAdvancedWindow();
	} catch(e) {

	}

}
