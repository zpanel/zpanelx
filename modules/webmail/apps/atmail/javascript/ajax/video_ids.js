// Videomail functions
function GetVideoID(GetUniqueID, EmailFrom, EmailSubject) {
	if (!EmailFrom) EmailFrom = "";
	if (!EmailSubject) EmailSubject = "";
		
	// If video is disabled, close it, or if we have no GetUniqueID, return function we dont want to call videomail.php
	if (document.getElementById("VideoMail").value == 0 || (VideoStreamUID == null && GetUniqueID != true)) return null;

	var SendData = new Array();
	if (GetUniqueID == true) {
		SendData["func"] = "getuniqueid";
	} else {
		SendData["func"] = "getstreamid";
		SendData["UniqueID"] = VideoStreamUID;
		SendData["EmailFrom"] = EmailFrom;
		SendData["EmailSubject"] = EmailSubject;
	}
	var ObjGetStreamID = new VideoAjaxRequester("videomail.php", SendData);
	ObjGetStreamID.Request();
	var Results = ObjGetStreamID.Results();
	var Response;
	
	if (Results) {
		if (GetUniqueID == true) {
			try {
				return Results.getElementsByTagName("UniqueID")[0].firstChild.data;
			} catch(e) {
				alert("Could not load UniqueID for VideoMail - Check the Video Server is online and the max number of connections has not been exceeded. If the problem exists send an email with Video-Mail disabled");
			}
		} else {
			try {
				Response = Results.getElementsByTagName("StreamID")[0].firstChild.data;
			} catch(e) {
				alert("Could not load StreamID for VideoMail - Check the Video Server is online and the max number of connections has not been exceeded. If the problem exists send an email with Video-Mail disabled");
			}
			
			if(Response == 'd41d8cd98f00b204e9800998ecf8427e')	{
				alert("No Videomail was successfully recorded - Check you have a Webcam or microphone connected successfully. View the Videomail help guide for a tutorial");
			} else	{
				return Response;
			}
			
		}
	} else {
		return null;
	}
}

function VideoAjaxRequester(FileName, DataArray, CallBackFunc, ForwardData) {
	this.ForwardData = ForwardData;

	this.XMLObj = null;

	try { this.XMLObj = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	try { this.XMLObj = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
	try { this.XMLObj = new XMLHttpRequest(); } catch(e) {}

	this.Results = function(DataType) {
		if (this.XMLObj.readyState == 4){

			if(this.XMLObj.status == 200){
				VidCheckXMLError(this.XMLObj);
				if (DataType == true) {
					if (this.XMLObj.responseText) {
						return this.XMLObj.responseText;
					} else {
						return false;
					}
				} else {
					if (this.XMLObj.responseXML) {
						return this.XMLObj.responseXML;
					} else {
						return false;
					}
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	if (CallBackFunc) {
		this.XMLObj.onreadystatechange = eval(CallBackFunc);
	}

	var SendString = "";
	for (var i in DataArray) {
		SendString += "&" + i + "=" + DataArray[i];
	}
	SendString = SendString.substring(1, SendString.length);

	// Not compat for IE6
	//this.XMLObj.url = FileName + ":" + SendString;
	
	this.Request = function(Post) {
		if (Post == true) {
			this.XMLObj.open("POST", FileName, false);
			this.XMLObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			this.XMLObj.send(SendString);
		} else {
			this.XMLObj.open("GET", FileName + "?" + SendString, false);
			this.XMLObj.send(null);
		}
	}
}

function VidCheckXMLError(XMLReq)	{
	try {
		var err = XMLReq.responseXML.getElementsByTagName("ErrorMessage")[0].firstChild.data;
		document.write(err);
		return 0;
	} catch (e) {
		//alert(e);
		return 1;
	}
}
