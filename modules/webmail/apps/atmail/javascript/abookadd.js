function doOnLoad()
{
	mainForm = document.MainForm;
	self.focus();
}

function numberOfSelectedOptions( obj )
{
	var counter = 0
	for ( var i = 0; i < obj.length; i++ )
		if ( obj.options[i].selected )
			counter++
	return counter
}

function setFive( obj )
{
	var five = 0
	for ( var i = 0; i < obj.length; i++ )
	{
		if ( obj.options[i].selected && ++five > 5 )
			obj.options[i].selected = false
	}
}

function checkToken( inStr, token )
{
	for ( var i = 0; i < inStr.length; i++ )
	{
		if ( inStr.charAt(i) == token )
			return true;
	}
	return false;
}

function moveItems( FromBox, ToBox, Type, Disable )
{
    var moveAll;
    var langd;

    if(!Disable)
    Disable='';

    if(Disable == 1)	{
        document.MainForm.addbutton.disabled = true;
    }

    ToBox = document.getElementById(ToBox);

    if ( window.navigator.appName.indexOf("Microsoft") >= 0
    && window.navigator.appVersion.indexOf("MSIE 5.0") >= 0
    && numberOfSelectedOptions(FromBox) > 5 )
    {
        alert("Due to a problem in Internet Explorer 5, you can not \nmove more than 5 users at the same time.")
        setFive( FromBox )
        return
    }

    var same = 0;

    var val = new Array( FromBox.length )
    var txt = new Array( FromBox.length )

    for ( var i = 0; i < ToBox.length; i++ )
    {
        if ( ToBox.options[i].value == -1 )
        {
            ToBox.length = 0
            break
        }
    }

    var j = 0
    var k = 0
    for ( var i = 0; i < FromBox.length; i++ )
    {
        if ((FromBox.options[i].selected == true) && (FromBox.options[i].value == -1))
        moveAll = true;
    }

    if (!moveAll)
    {
        for ( var i = 0; i < FromBox.length; i++ )
        {
            //			if ( ( FromBox.options[i].selected ) && ( ! checkToken( FromBox.options[i].text, "@" ) ) )
            if ( FromBox.options[i].selected )
            {
                to_insert = true;
                langd = ToBox.length;

                for ( var m = 0; m < langd; m++ )
                {
                    if (ToBox.options[m].value == FromBox.options[i].value)
                    {
                        to_insert = false;
                        break;
                    }
                }

                if (to_insert)
                {
                    var _value = FromBox.options[i].value
                    var _text  = FromBox.options[i].text

                    ToBox.options[langd] = new Option (_text,_value)
                    ToBox.options[langd].selected = true
                }
            }
            else
            {
                val[j] = FromBox.options[i].value
                txt[j] = FromBox.options[i].text
                j++
            }
        }
    }
    else
    {
        for ( var i = 0; i < FromBox.length; i++ )
        {
            if (FromBox.options[i].value != -1)
            {
                to_insert = true;
                langd = ToBox.length;

                for ( var m = 0; m < langd; m++ )
                {
                    if (ToBox.options[m].value == FromBox.options[i].value)
                    {
                        to_insert = false;
                        break;
                    }
                }

                if (to_insert)
                {
                    var _value = FromBox.options[i].value
                    var _text  = FromBox.options[i].text

                    ToBox.options[langd] = new Option (_text,_value)
                    ToBox.options[langd].selected = true
                }
            }
        }
        //		document.MainForm.GroupSend.value = 1
    }
}

function deleteItems(selectBox, Disable)
{

	selectBox = document.getElementById(selectBox);

	if(Disable == '1')	{
	document.MainForm.addbutton.disabled = false;
	}

	var val = new Array(selectBox.length)
	var txt = new Array(selectBox.length)
	var j=0

	for ( var i = 0; i < selectBox.length; i++ )
	{
		if ( selectBox.options[i].selected )
		{
			selectBox.options[i].length = 0
		}
		else
		{
			val[j] = selectBox.options[i].value
			txt[j] = selectBox.options[i].text
			j++
		}
	}

	selectBox.length=0

	for ( i = 0; i < j; i++ )
		selectBox.options[i] = new Option(txt[i],val[i])

	if(document.MainForm.add)	{
	document.MainForm.addbutton.disabled = true;
	}

}

function insertAddr(type)
{
	var l_ToAddress = ''
	var l_CCAddress = ''
	var l_BCCAddress = ''

	for ( var i = 0; i < mainForm.ToAddress.length; i++ )

if( checkToken( mainForm.ToAddress.options[i].value, "@" ) )	{
		l_ToAddress = l_ToAddress + mainForm.ToAddress.options[i].value + "; " ;
}

if(type)	{


        opener.window.document.Compose.emailto.value = l_ToAddress
        window.close();


} else	{

	for ( var i = 0; i < mainForm.CCAddress.length; i++ )
if( checkToken( mainForm.CCAddress.options[i].value, "@" ) )	{
		l_CCAddress = l_CCAddress + mainForm.CCAddress.options[i].value + "; " ;
}
	for ( var i = 0; i < mainForm.BCCAddress.length; i++ )
if( checkToken( mainForm.BCCAddress.options[i].value, "@" ) )	{
		l_BCCAddress = l_BCCAddress + mainForm.BCCAddress.options[i].value + "; ";
}
	opener.window.document.Compose.emailto.value = l_ToAddress
	opener.window.document.Compose.emailcc.value = l_CCAddress
	opener.window.document.Compose.emailbcc.value = l_BCCAddress

// Automatically open the Bcc field if its hidden
var bcc = opener.window.document.getElementById('bcc');
var bcctr = opener.window.document.getElementById('bcctr');

if(opener.window.document.Compose.emailbcc.value == '')	{
bcc.style.display='none';
bcctr.style.display='none';
} else	{
bcc.style.display='';
bcctr.style.display='';
}


window.close();

	}

}

function savelist(ToBox)	{

                for ( var i = 0; i < ToBox.length; i++ )
                {

                        if (ToBox.options[i].value != -1)
                        {
                                ToBox.options[i].selected = true
                        }
                }

window.close();

        }


