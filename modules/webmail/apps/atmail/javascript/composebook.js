var winAddr;

// Open the AddRecipients menu for the simple/professional interface using the DOM
function AddRecpientsDOM(type) {
	if(!type)	{
		var url = "abook.php?func=composebook";
		url += "&emailto=" + escape(document.getElementById("ComposeMsgTo").value);
		url += "&emailcc=" + escape(document.getElementById("ComposeMsgCc").value);
		url += "&emailbcc=" + escape(document.getElementById("ComposeMsgBcc").value);
		winAddr = openPopup( url, "Addresbook", 610, 520,0,1,0);
	} else	{
		var url = "abook.php?func=group";
		winAddr = openPopup( url, "Addresbook", 405, 340,0,1,0);
	}
}

// Open the AddRecipients menu for the simple/professional interface
function AddRecpients(type)
{

	if(!type)	{
	var url = "abook.php?func=composebook";
	url += "&emailto=" + escape( document.Compose.emailto.value );
	url += "&emailcc=" + escape( document.Compose.emailcc.value );
	url += "&emailbcc=" + escape( document.Compose.emailbcc.value );
	winAddr = openPopup( url, "Addresbook", 610, 520,0,1,0)
	} else	{
	var url = "abook.php?func=group";
	winAddr = openPopup( url, "Addresbook", 405, 340,0,1,0)
	}

}

// Open the AddRecipients menu for the XP/Advanced interface
function AddRecpientsXP(type) {

        if (!type) {
                var url = "abook.php?func=composebook";
                winAddr = openPopup( url, "_blank", 645, 492,0,1,0)
        } else  {
                var url = "abook.php?func=group";
                winAddr = openPopup( url, "_blank", 405, 340,0,1,0)
        }
}

function AddCal(form)
{
        var url = "abook.php?func=composebook";
        url += "&emailto=" + escape( form.emailto.value );
        url += "&emailcc=" + escape( form.emailcc.value );
        url += "&cal=1&abookview=global";
        winAddr = openPopup( url, "Addresbook", 590, 320,0,1,0)
}

function openPopup( url, title, width, height, scroll, resize, status )
{
	var param = "width=" + width;
	param += ",height=" + height;
	param += ",left=" + ( parseInt( screen.width / 2 ) - width / 2 );
	param += ",top=" + ( parseInt( screen.height / 2 ) - height / 2 );
	param += ",scrollbars=" + scroll;
	param += ",resizable=" + resize;
	param += ",status=" + status;
	return self.open( url, title, param );

}

function abookview(type) {

if(type == "personal") {
  location.href="abook.php?type=personal";
  }
if(type == "global") {
  location.href="abook.php?type=global";
  }
}

function ReadReceipt()	{

if( document.Compose.ReadReceipt.value == '1' )	{
	document.Compose.ReadReceipt.value = '0';
} else	{
	document.Compose.ReadReceipt.value = '1';
}


}
