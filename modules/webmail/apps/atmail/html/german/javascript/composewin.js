this.window.focus()

var remote=null;                                                  
function rs(n,u,w,h,x) {                              
args="left=10,top=10,width="+w+",height="+h+",resizable=yes,scrollbars=yes,status=0";
remote=window.open(u,n,args);                                                                                
if (remote != null) {          
if (remote.opener == null)                         
remote.opener = self;   
}                               
if (x == 1) { return remote; }                     
}                                                             
           
var awnd=null;                                                          

// Attachment window for the simple / professional interface
function Attachment(unique) {                                                 
awnd=rs('attach','compose.php?func=attachment&unique=' + unique,370,280,1);
}  

function composenew()	{
awnd=rs('compose','compose.php?func=new',670,540,1);
return
}

// Attachment window for the Advanced interface
function AttachmentDialog(unique) { 
	var DialogData = showModalDialog('compose.php?func=attachmentmodal&unique=' + unique, 'attach', 'dialogWidth: 400px; dialogHeight: 245px; center: Yes; help: No; resizable: Yes; status: No; scroll: No;');

	if (DialogData) {
		AttachmentDisplay.style.display = "";
		document.Compose.AttachmentList.value = DialogData;
		resize_iframe();
	} else {
		AttachmentDisplay.style.display = "none";
		document.Compose.AttachmentList.value = '';
		resize_iframe();
	}
}  

function ChangeEditor(type) {
// type has to be 'html' or 'txt'
  if (confirm(['Bei der Umstellung des Editors gehen Ihre Änderungen verloren, wollen Sie den Editor wirklich wechseln?'])) {
	if(type == "txt") {
    location.href = 'compose.php?func=new&HtmlEditor=2';
	}
	if(type == "html") {
    location.href = 'compose.php?func=new&HtmlEditor=1';
	}
  }
}