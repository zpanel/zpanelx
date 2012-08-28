ie = document.all?1:0
ns4 = document.layers?1:0
var deltotal = '';

function TRS(CB){
if (CB.checked)
TRON(CB);
else
TROFF(CB);
}

function TRON(E,doc){
if (ie)
{
while (E.tagName!="TR")
{E=E.parentElement;}
}
else
{
while (E.tagName!="TR")
{E=E.parentNode;}
}

E.className = "on";
}

function TRGRAY(E,doc){
if (ie)
{
while (E.tagName!="TR")
{E=E.parentElement;}
}
else
{
while (E.tagName!="TR")
{E=E.parentNode;}
}

if(E.className != "on")
E.className = "ongray";
}
function TROFF(E,doc){
var orig = E;
if (ie)
{
while (E.tagName!="TR")
{E=E.parentElement;}
}
else
{
while (E.tagName!="TR")
{E=E.parentNode;}
}

if(E.className == "ongray")	{
E.className = "off";
return;
}

if(E.className == "on" && orig.checked==false) {
E.className = "off";
return;
}

var chk, len, i;

oForm = document.mail;

	// Make the trrow highlight if we move the mouse over
	if(oForm)	{

	len=document.mail.elements.length;

        for (i=0;i<len;i++){
                if (document.mail.elements[i].type=='checkbox'){

					if(document.mail.elements[i].value == doc && document.mail.elements[i].checked == true)
						chk = 'on';

				};
        };
	}

aForm = document.abook;

	// Make the trrow highlight if we move the mouse over
	if(aForm)	{
	deltotal = 0;
	len=document.abook.elements.length;
        for (i=0;i<len;i++){
                if (document.abook.elements[i].type=='checkbox'){

					if( document.abook.elements[i].name == 'To' || document.abook.elements[i].name == 'Cc' || document.abook.elements[i].name == 'Bcc' )
						if ( document.abook.elements[i].checked == true && document.abook.elements[i].value == doc && E.className != 'on')
						E.className = 'on'; //chk = 'on';

						if(  document.abook.elements[i].checked == true && ( document.abook.elements[i].name == 'del[]' || document.abook.elements[i].name == 'delgroup[]' || document.abook.elements[i].name == 'delshared[]' || document.abook.elements[i].name == 'delsharedgroup[]') )	{

							if(E.className != 'on')
							E.className = 'on'; //chk = 'on';
							top.FramePage.MenuBar.ButtonToggle("delete", "On");
							deltotal++;
						}

				};

        };

// Deselect the "X" if no row is open, and we have no rows currently selected
if(deltotal == 0 && top.FramePage.ToggleRight.rows == "100%,0%,2")
top.FramePage.MenuBar.ButtonToggle("delete", "Fade");

	}

//E.className = chk;

}

function check_delete()	{

var chk;
oForm = top.FramePage.emailwin.document.mail;

	if(oForm)	{
	len=oForm.elements.length;

        for (i=0;i<len;i++){
                if (oForm.elements[i].type=='checkbox'){

					if(oForm.elements[i].checked == true)
						chk = 'on';

				};
        };
	}

if(chk)	{
top.FramePage.MenuBar.ButtonToggle("forward", "On");
top.FramePage.MenuBar.ButtonToggle("delete", "On");
top.FramePage.MenuBar.ButtonToggle("move", "On");
} else	{
top.FramePage.MenuBar.ButtonToggle("forward", "Fade");
top.FramePage.MenuBar.ButtonToggle("delete", "Fade");
top.FramePage.MenuBar.ButtonToggle("move", "Fade");
}

}

function check_delete_abook()	{

var chk;
oForm = top.FramePage.emailwin.document.abook;

	if(oForm)	{
	len=oForm.elements.length;

        for (i=0;i<len;i++){
                if (oForm.elements[i].type=='checkbox' && ( document.abook.elements[i].name == 'del[]' || document.abook.elements[i].name == 'delgroup[]' || document.abook.elements[i].name == 'delshared[]' || document.abook.elements[i].name == 'delsharedgroup[]') && document.abook.elements[i].disabled == false){

					if(oForm.elements[i].checked == true)
						chk = 'on';

				};
        };
	}

if(chk)	{
top.FramePage.MenuBar.ButtonToggle("delete", "On");
top.FramePage.MenuBar.ButtonToggle("editcontact", "Fade");
top.FramePage.MenuBar.ButtonToggle("print", "Fade");
} else	{
top.FramePage.MenuBar.ButtonToggle("delete", "Fade");
top.FramePage.MenuBar.ButtonToggle("editcontact", "Fade");
top.FramePage.MenuBar.ButtonToggle("print", "Fade");
}

}
