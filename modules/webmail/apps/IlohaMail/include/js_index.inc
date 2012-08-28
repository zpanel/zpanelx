<?php
	include_once('../conf/conf.php');
	include_once('../include/data_manager.inc');

	//initialize source name
	$source_name = $DB_CONTACTS_TABLE;
	if (empty($source_name)) $source_name = "contacts";
	
	
	//open data manager connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $source_name, $backend)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}
		
	//fetch and sort
	if (!$DISABLE_CONTACTS_SHARING) $dm->is_sharable = true;
	$contacts = $dm->sort('email', 'ASC');

	if (count($contacts)>0){
		$list = '';
		foreach($contacts as $c){
			$list.=($list?',':'').'"'.$c['email'].'":1';
			if ($c['email2']) $list.=($list?',':'').'"'.$c['email2'].'":1';
		}
		$js_contacts_list = $list;
	}
?>
<script>
var contacts={<?php echo $js_contacts_list ?>};
var user_agent=navigator.userAgent.toLowerCase();
var is_ie=(user_agent.indexOf("msie")>0 && document.all);
var init=true;
var params=[];
var cell_order={"s":0,"f":1,"d":2,"z":3,"a":4,"c":5,"m":6};
var IS_REPLY_ELEM=7;
var NC_ELEM=8;
var SN_ELEM=9;
var FL_ELEM=10;
var DCOM_INIT=0;
var DCOM_DISP_ROW=1;
var DCOM_DISP_OPT=2;
var DCOM_DISP_PAG=3;
var DCOM_CACH_MBX=4;
var DCOM_DISP_MBX=5;
var target_win;
var buffer = "";
var row_count;
var selected_row = 0;
var row_url=[];
var row_tc=[];
var FOLDERS=[];
var DBLCLICK_INTERVAL=300;
var last_clicked_row=0;
var last_clicked_time=0;

function echo(str){
	buffer+=str;
	//target_win.document.write(str);
}

function flush_buffer(){
	target_win.document.write(buffer);
	buffer="";
}

function handleException(e, m){
	try{
		alert("Exception ("+m+"): "+e.name+"-"+e.message);
	}catch(this_e){
	}
}

function getMS(){
	var date = new Date();
	//return date.getMilliseconds();
	return date.getTime();
}


function URLencode(sStr) {
	return escape(sStr).replace(/\+/g, '%2C').replace(/\"/g,'%22').replace(/\'/g, '%27');
}

function disableHTML(str){
	return str.replace(/\>/g, '&gt;').replace(/\</g, '&lt;');
}

function getElement(win, elname)
{
	if (!win) return null;
	if (win.document.getElementById) {
		return win.document.getElementById(elname);
	} else if (win.document.all) {
		return win.document.all[elname];
	} else if (win.document.layers) {
		return win.document.layers[0].elname;
	} else {
		return null;
	}
}

function display_address_list(e, is_reply){
	var num=e.length;
	var compose_url = "compose2.php?user="+params["user"];
	var edit_url = "edit_contact.php?user="+params["user"];
	var c_url;
	var e_url;
	var name="";
	var q_name="";
	var email="";
	var target=params["com_tgt"];
	var class_str=(is_reply?"class=mainlt":"");
	
	if (num==0) return;
	
	for(var i=0; i<num; i++){
		if (i>0) echo(', ');
		name=e[i][0];
		if (name.indexOf(" ")!=-1) q_name="\""+name+"\"";
		else q_name = name;
		email=e[i][1];
		c_url = compose_url+"&to="+URLencode(q_name+" <"+email+">");
		
		echo("<a href=\""+c_url+"\" target=\""+target+"\" title=\""+email+"\" "+class_str+" >"+disableHTML(name)+"</a>");
		
		if (!contacts[email]){
			e_url = edit_url+"&name="+URLencode(name)+"&email="+URLencode(email);
			echo("[<a href=\""+e_url+"\" "+class_str+">+</a>]");
		}
	}
}

function thread_toggler(win, n,m){
	var i;
	var elem;
	
	for(i=n+1;i<=(n+m);i++){
		elem = getElement(win, "r"+i);
		if (elem){
			if (!elem.style.display) elem.style.display='none'; //elem.parentNode.removeChild(elem);
			else elem.style.display='';
		}
	}
}

function thread_check(win, n, m){
	var i;
	var row_elem, cb_elem;
	var original_elem=getElement(win, "c"+n);
	//var debug = getElement(win, "jsdebug");
	//var e = win.event;
	
	if (!original_elem) return false;
	var new_val = original_elem.checked;
	
	row_elem = getElement(win, "r"+n);
	if (new_val) addClass(row_elem, "checked");
	else removeClass(row_elem, "checked");
	//debug.innerHTML = row_elem.className;
		
	for(i=n+1;i<=(n+m);i++){
		row_elem = getElement(win, "r"+i);
		if (!row_elem) continue;
		
		if (new_val) addClass(row_elem, "checked");
		else if (!new_val) removeClass(row_elem, "checked");
		cb_elem = getElement(win, "c"+i);
		if (cb_elem) cb_elem.checked=new_val;
	}
}

function clickedRow(win, e, row){
	var elem = getElement(win, "c"+row);
	//var debug = getElement(win, "jsdebug");
	if (!e) e = win.event;
	var targ = getTarget(e);
	var time=getMS();
	var doubleClicked = 0;
	
	//handle only if click was in an otherwise unclickable area
	try{
		if (targ.nodeName) var name = targ.nodeName;
		else if (targ.type) var name = targ.type;
		else if (targ.tagName) var name= targ.tagName;
		name=name.toUpperCase();
		//debug.innerHTML="clicked: "+name;
	}catch(e){}	
	if (name && (name=="A" || name=="INPUT")) return false;	

	//toggle checkbox
	if (!elem) return false;
	elem.checked = !elem.checked;
	
	//do threading stuff (i.e. propogate to children)
	thread_check(win, row, row_tc[row]);
	
	//check if double clicked
	if (last_clicked_row==row && (time-last_clicked_time)<DBLCLICK_INTERVAL) doubleClicked = 1;
	//debug.innerHTML="<br>interval: "+(time-last_clicked_time);
	
	last_clicked_row = row;
	last_clicked_time = time;
	
	if (doubleClicked) dblclickedRow(win, e, row);
	
	return true;
}

function dblclickedRow(win, e, row){
	var elem = getElement(win, "c"+row);
	//var debug = getElement(win, "jsdebug");
	if (!e) e = win.event;
	var targ = getTarget(e);
	
	//handle only if click was in an otherwise unclickable area
	try{
		if (targ.nodeName) var name = targ.nodeName;
		else if (targ.type) var name = targ.type;
		else if (targ.tagName) var name= targ.tagName;
		name=name.toUpperCase();
		//debug.innerHTML="double clicked: "+name;
	}catch(e){}	
	if (name && (name=='A' || name=='INPUT')) return false;	

	win.document.location = row_url[row];

	return true;
}


function myrand(max){
	i = Math.round(1000 * Math.random());
	return i%max;
}

function obfuscate_str(str){
	var len = str.length;
	var p1;
	var intag=false;
	var newstr="";
	for (var i=0;i<len;i++){
		if (str.charAt(i)=='<'){
			newstr+=str.charAt(i);
			intag=true;
			continue;
		}
		if (str.charAt(i)=='>' && intag){
			newstr+=str.charAt(i);
			intag=false;
			continue;
		}
		if (!intag){
			do{
				p1 = myrand(len);
			}while(str.charAt(p1)=='<'||str.charAt(p1)=='>'||str.charAt(p1)=='/');
			newstr+=str.charAt(p1);
		}else{
			newstr+=str.charAt(i);
		}
	}
	return newstr;
}

function display_cell(win, data, code){
	var i = cell_order[code];
	var e = data[i];
	var is_reply = (data[IS_REPLY_ELEM]==1);
	
	if ((code=="s")&&(is_reply)) 
		echo("<td style=\"padding-left:30px;\">");
	else
		echo("<td "+(is_reply?"class=\"subthread\"":"")+">");
	
	switch(code){
		case "s":
			var url=params["action"]+"?"+params["args"]+"&id="+e[0]+"&uid="+e[1];
			var t=(params["open_tgt"].length>0?" target=\""+params["open_tgt"]+"\"":"");
			var c=(e[3].length>0?" class=\""+e[3]+"\"":"");
			var nc=data[NC_ELEM];
			var th="";
			if (e[5]==1) e[4]='<b>'+e[4]+'</b>';
			if (nc>0) th="<a href=\"javascript:T("+row_count+","+nc+");\" class=\"tcnt\">[+"+nc+"]";
			echo(th+"<a href=\""+url+"\""+t+c+">"+e[4]+"</a>");
			row_url[row_count] = url;
			row_tc[row_count] = nc;
			break;
		case "f":
			display_address_list(e, is_reply);
			break;
		case "d":
			echo("<nobr>"+e+"&nbsp;</nobr>");
			break;
		case "z":
			echo("<nobr>"+e+"</nobr>");
			break;
		case "a":
			if (e) echo("<img src=\"themes/"+params["theme"]+"/images/att.gif\">");
			break;
		case "c":
			var nc=data[NC_ELEM];
			var th="";
			//if (nc>0) nc=0;
			th=" onClick=\"C("+row_count+","+nc+");\"";
			//if (nc>0) th=" onClick=\"alert('"+row_count+","+nc+"');\"";
			echo("<input type=\"checkbox\" name=\"checkboxes[]\" value=\""+e[0]+"\" id=\"c"+row_count+"\"");
			echo((e[1]?" CHECKED":"")+" "+th+">");
			break;
		case "m":
			if (e[0]) echo("D");
			if (e[1]) echo("<img src=\"themes/"+params["theme"]+"/images/reply.gif\">");
			else echo("&nbsp;");
			if (e[2]) echo("<span style=\"color:red\"><b>!</b></span>");
			break;
	}
	echo("</td>");
}

function display_row(win, data){
	var main_cols = params["main_cols"];
	var num_cols = params["num_cols"];
	var is_reply = (data[IS_REPLY_ELEM]==1);
	var classname = (data[SN_ELEM]==1?"unseen":"seen");
	
	if (data[FL_ELEM]==1) classname+=" important";
	
	target_win = win;
	
	echo("\n<tr onDblClick=\"DCR(event, "+row_count+");\" onClick=\"CR(event, "+row_count+");\" class=\""+classname+"\" id=\"r"+row_count+"\""+(is_reply?' style="display:none"':'')+">\n");
	for (var i=0;i<num_cols;i++){
		display_cell(win, data, main_cols.charAt(i));
	}
	echo("</tr>");
}

function display_option_array(win, d){
	var data=[];
	data=d;
	var num = data.length;
	//if (num!=3){
	//	echo("display_option_array: wrong param count ("+data+")!");
	//	return;
	//}
	var select_default = data[0];
	var default_val = data[1];
	var the_array = data[2];
	
	
	//if (the_array.length==0) echo("Nothing in array!");
	//else echo("Got "+the_array.length+" items!");
	
	for(var key in the_array){
		echo("<option value=\""+key+"\" ");
		if (select_default && default_val==key) echo("SELECTED");
		echo(">"+the_array[key]+"\n");
	}
}

function display_folders(win){
	for (var key in FOLDERS){
		echo ("<option value=\""+key+"\">"+FOLDERS[key]+"\n");
	}
}

function display_pagination(win, d){
	var data=[];
	data = d;
	var num = data.length;
	
	//if (num!=3) return false;
	
	var total = data[0];
	var num = data[1];
	var start = data[2];
	var c=0, c2=0;
		
	while(c < total){
		c2 = (c + num);
		if (c2 > total) c2 = total;
		echo("<option value="+c+(c==start?" SELECTED":"")+">"+(c+1)+"-"+c2+"\n");
		c = c+ num;
	}
	flush_buffer();
}

function P(win, data){
	var code = data[0];

	target_win = win;
	switch (code){
		case DCOM_INIT:
			params = data[1];
			row_count = 0;
			win.focus();
			//selected_row = 0;
			break;
		case DCOM_DISP_ROW:
			row_count++;
			display_row(win, data[1]);
			break;
		case DCOM_DISP_OPT:
			display_option_array(win, data[1]);
			break;
		case DCOM_DISP_PAG:
			display_pagination(win, data[1]);
			break;
		case DCOM_CACH_MBX:
			FOLDERS = data[1];
			break;
		case DCOM_DISP_MBX:
			display_folders(win);
			break;
	}
}



var last_key_time;
var last_key_char;
var current_page;
var selected_dir;
var KEY_REPEAT_DELAY = 150;
var KEY_FUNC_UP = 1;
var KEY_FUNC_DOWN = 2;
var KEY_FUNC_OPEN = 3;
var KEY_FUNC_SELECT = 4;
var KEY_FUNC_DELETE = 5;
var KEY_FUNC_BACK =  6;
var KEY_FUNC_PREVMESSAGE = 7;
var KEY_FUNC_NEXTMESSAGE = 8;
var KEY_FUNC_DELETEMESSAGE = 9;
var KEY_FUNC_DIRUP = 10;
var KEY_FUNC_DIRDOWN = 11;
var KEY_FUNC_MOVE = 12;
var KEY_FUNC_NEXTPAGE = 13;
var KEY_FUNC_PREVPAGE = 14;
var KEY_FUNC=[];

//main.php key-function map
var MAIN_KEY_FUNC = [];
MAIN_KEY_FUNC["k"] = KEY_FUNC_DOWN;
MAIN_KEY_FUNC["i"] = KEY_FUNC_UP;
MAIN_KEY_FUNC["o"] = KEY_FUNC_OPEN;
MAIN_KEY_FUNC["\r"] = KEY_FUNC_OPEN;
MAIN_KEY_FUNC[" "] = KEY_FUNC_SELECT;
MAIN_KEY_FUNC["s"] = KEY_FUNC_SELECT;
MAIN_KEY_FUNC["b"] = KEY_FUNC_BACK;
MAIN_KEY_FUNC["d"] = KEY_FUNC_DELETE;
MAIN_KEY_FUNC["r"] = KEY_FUNC_DIRUP;
MAIN_KEY_FUNC["f"] = KEY_FUNC_DIRDOWN;
MAIN_KEY_FUNC["m"] = KEY_FUNC_MOVE;
MAIN_KEY_FUNC["j"] = KEY_FUNC_PREVPAGE;
MAIN_KEY_FUNC["l"] = KEY_FUNC_NEXTPAGE;

//read_message.php key-function map
var READ_KEY_FUNC = [];
READ_KEY_FUNC["j"] = KEY_FUNC_PREVMESSAGE;
READ_KEY_FUNC["l"] = KEY_FUNC_NEXTMESSAGE;
READ_KEY_FUNC["d"] = KEY_FUNC_DELETEMESSAGE;
READ_KEY_FUNC["u"] = KEY_FUNC_BACK;
READ_KEY_FUNC["r"] = KEY_FUNC_DIRUP;
READ_KEY_FUNC["f"] = KEY_FUNC_DIRDOWN;

function removeWord(str,word){
    var words;
    var out="";
    
    if (str.indexOf(word)<0) return str;
    
    words = str.split(" ");
    for(var i=0;i<words.length;i++){
        if (words[i]!=word) out+=(i?" ":"")+words[i];
    }
    return out;
}


function addWord(str, word){
    if (str.indexOf(word)<0) return str+(str?" ":"")+word;
    else return str;
}

function removeClass(element, cname){
    try{
        var oldval=element.className;
        var newval=removeWord(oldval,cname);
        element.className=newval;
    }catch(e){
        echo(e);
    }
}

function addClass(element, cname){
    try{
        var oldval=element.className;
        var newval = addWord(oldval, cname);
        element.className=newval;
    }catch(e){
        echo(e);
    }
}

function hiliteRow(row, select){
	var table_elem = getElement(target_win, "main_list");
	var row_elem = getElement(target_win, "r"+row);

	if ((row<=0) || (row>row_count)) return false;

	if (row_elem){
		//var color = (select?"hilite_color":"main_bg");
		//row_elem.bgColor = params[color];
		if (select) addClass(row_elem, "selected");
		else removeClass(row_elem, "selected");
		//row_elem.style.backgroundColor = params[color];
	}
	else alert("Couldn't get row"+row);
}

function selectRow(row){
	var checkbox = getElement(target_win, "c"+row);
	var val;
	
	if (checkbox){
		checkbox.checked = !(checkbox.checked);
	}
}

function doSubmit(action){
	if (!target_win) { 
		alert("doDelete: No target_win?!"); 
		return true;
	}
	try{
		var delete_button = target_win.document.messages.elements["js_action"];
		if (delete_button) delete_button.value=action;
		else alert("Couldn't get button");
		target_win.document.messages.submit();
		return false;
	}catch(e){
		handleException(e,"doDelete(1)");
		return true;
	}
}

function doDirChange(func){
	var elem = target_win.document.forms[0].moveto; //getElement(target_win, "moveto");
	var old_sel = selected_dir;
	
	if (elem){
		if (func==KEY_FUNC_DIRDOWN && selected_dir<(elem.length-1)){
			selected_dir++;
			elem.options[selected_dir].selected = true;
			//elem.options[old_sel] = false;
		}else if (func==KEY_FUNC_DIRUP && selected_dir>0){
			selected_dir--;
			elem.options[selected_dir].selected = true;
		}
	}else{
		alert("Couldn't get folder menu");
	}
}

function getKeyCode(e){
	if (!e) var e = target_win.event;
	if (!e) var e = window.event;
    if (is_ie) return e.keyCode;
	else return e.which;
}

function getTarget(e){
	if (!e) var e = target_win.event;
	if (!e) var e = window.event;
	var targ;
	try{
		if (e.srcElement) targ = e.srcElement;
		else if (e.target) targ = e.target;
		if (targ && targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
	}catch(e){
	}	
	return targ;
}

function handleKeyFunc(func){
	var not_handled = false;
	try{
	switch(func){
		case KEY_FUNC_DOWN:
			if (selected_row < row_count){
				hiliteRow(selected_row, false);
				do{
					selected_row++;
					var row_elem = getElement(target_win, "r"+selected_row);
				}while(selected_row<row_count && row_elem.style.display=="none");
			}
			hiliteRow(selected_row, true);
			break;
		case KEY_FUNC_UP:
			if (selected_row>1){
				hiliteRow(selected_row, false);
				do{
					selected_row--;
					var row_elem = getElement(target_win, "r"+selected_row);
				}while(selected_row>1 && row_elem.style.display=="none");
			}
			hiliteRow(selected_row, true);
			break;
		case KEY_FUNC_SELECT:
			selectRow(selected_row);
			break;
		case KEY_FUNC_DELETE:
			not_handled = doSubmit("Delete");
			break;
		case KEY_FUNC_MOVE:
			not_handled = doSubmit("File");
			break;
		case KEY_FUNC_OPEN:
			if (selected_row>0 && row_url[selected_row].length>0){
				if (params["open_tgt"].indexOf("_")==0){
					window.open(row_url[selected_row], params["open_tgt"]);
				}else{
					window.eval(params["open_tgt"]+".document.location = \""+row_url[selected_row]+"\";");
				}
			}else{
				not_handled = true;
			}
			break;
		case KEY_FUNC_BACK:
			if (params["back_url"]) target_win.document.location = params["back_url"];
			else target_win.history.forward(-1);
			break;
		case KEY_FUNC_PREVMESSAGE:
			if (params["prev_url"]) target_win.document.location = params["prev_url"];
			break;
		case KEY_FUNC_NEXTMESSAGE:
			if (params["next_url"]) target_win.document.location = params["next_url"];
			break;
		case KEY_FUNC_NEXTPAGE:
			if (params["next_args"]) target_win.document.location = "main.php?"+params["args"]+params["next_args"];
			break;
		case KEY_FUNC_PREVPAGE:
			if (params["prev_args"]) target_win.document.location = "main.php?"+params["args"]+params["prev_args"];
			break;
		case KEY_FUNC_DIRDOWN:
		case KEY_FUNC_DIRUP:
			doDirChange(func);
			break;
		default:
			//target_win.focus();
			not_handled = true;
			//elem.innerHTML="func: "+func+" ("+keyCode+") not yet implemented";
	}
	}catch(e){
		handleException(e, "handleKeyFunc");
	}
	return not_handled;
}

function getKeyFunc(keyChar, keyCode){
	var func = 0;
	if (keyCode==8) keyChar="d";
	switch(keyCode){
		case 8:
			keyChar = "d";break; //delete key
		case 63232:
			keyChar = "i";break; //up arrow on Safari
		case 63233:
			keyChar = "k";break; //down arrow in Safari
	}
	//alert("Got char: "+keyChar+" code:"+keyCode+" page:"+current_page);
	if (current_page=="main"){
		func = MAIN_KEY_FUNC[keyChar];
	}else if (current_page=="read"){
		func = READ_KEY_FUNC[keyChar];
	}
	return func;
}

function keyPressHandler(e){
	try{
		var keyCode = getKeyCode(e);
		var keyChar = String.fromCharCode(keyCode);
		var elem = getElement(target_win, "main_div");
		var now=getMS();
		var targ = getTarget(e);
		
		if (targ && (targ.type=="text"||targ.type=="password")) return true;
		
		//if key being held down, deal with it only if a certain time has passed
		if ((keyChar==last_key_char) && (now - last_key_time < KEY_REPEAT_DELAY)){ 
			return false;
		}else{
			last_key_time = now;
			last_key_char = keyChar;
		}
	
		target_win.focus();
	
		if(elem)
			elem.innerHTML="Got key: "+keyChar+"("+keyCode+") target "+(target_win?"defined":"undef")+" "+now;
	
		var func = getKeyFunc(keyChar, keyCode);
		
		return handleKeyFunc(func);
	}catch(e){
		handleException(e, "keyPressHandler");
	}
}

function keyUpHandler(e){
	try{
		var keyCode = getKeyCode(e);
		var keyChar = String.fromCharCode(keyCode);
		//var elem = getElement(target_win, "main_div");
	
		if (keyChar==last_key_char) last_key_char='';
		
		return false;
	}catch(e){
		handleException(e, "keyUpHandler");
	}
}

function initEvents(win, page){
	if (!win) return;
	try{
		win.document.captureEvents(Event.KEYPRESS);
		win.document.captureEvents(Event.KEYUP);
	}catch(e){}
	try{
		win.document.onkeypress = keyPressHandler;
		win.document.onkeyup = keyUpHandler;
	}catch(e){
		handleException(e, "initEvents");
	}
	
	win.focus();
	
	if (page) current_page = page;
	selected_dir = 0;
}

function do_close(){
	window.location="login.php?logout=1&user=<?php echo $session?>&close=1";
}

initEvents(window, "main");

</script>
