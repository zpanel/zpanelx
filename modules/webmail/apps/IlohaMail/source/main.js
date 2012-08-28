
var user_agent=navigator.userAgent.toLowerCase();
var is_ie=(user_agent.indexOf("msie")>0 && document.all);
var target_win;
var req;
var row_count;
var total_num;
var num_show;
var page_cache={};
var cache_size=0;
var FOLDER;
var FUID=0;
var LUID=0;
var DEBUG_ENABLED=false;
var PROFILE_ENABLED=false;
var UID_ENABLED=false;
//----
//          UTILITY FUNCTIONS
//----

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

function debugOut(str){
    var elem = getElement(window, "debugdiv");

    if (elem){
        elem.innerHTML += str + "<br>";
    }else{
        alert("Couldn't get console");
    }
}

function debugClear(){
    var elem = getElement(window, "debugdiv");

    if (elem){
        elem.innerHTML="";
    }else{
        alert("Couldn't get console");
    }
}

function toggleProfile(){
    PROFILE_ENABLED=!PROFILE_ENABLED;
}

function toggleDebug(){
    DEBUG_ENABLED = !DEBUG_ENABLED;
}

function echo(str){
    if (!DEBUG_ENABLED) return false;
    debugOut(str);
}

function profile(str){ 
    if (PROFILE_ENABLED)
        debugOut(getMS()+" "+str);
}

function getKeyCode(e){
	if (!e) var e = target_win.event;
	if (!e) var e = window.event;
    if (is_ie) return e.keyCode;
	else return e.which;
}

function disableHTML(str){
	return str.replace(/\>/g, '&gt;').replace(/\</g, '&lt;');
}

function getTarget(e)
{
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


function getElementValue(id){
    var elem = getElement(window, id);
    if (elem) return elem.value;
    else return "";
}


function readCookie(name)
{
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++)
        {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
}

function getNodeText(node){
    try{
        return node.firstChild.nodeValue;
    }catch(e){
    }
    return "";
}

function getSingleNodeText(xmlobj,name){
    var elem = xmlobj.getElementsByTagName(name);
    try{
        return getNodeText(elem[0]);
    }catch(e){
        return "";
    }
}

function URLencode(sStr) {
	return escape(sStr).replace(/\+/g, '%2C').replace(/\"/g,'%22').replace(/\'/g, '%27');
	var str ="\"";
}

function getMS(){	var date = new Date();	//return date.getMilliseconds();	return date.getTime();}

function showElement(el){
    try{
        el.style.display="";
    }catch(e){
    }
}

function hideElement(el){
    try{
        el.style.display="none";
    }catch(e){
    }
}


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
        echo(oldval+"-"+cname+"=>"+newval);
        element.className=newval;
    }catch(e){
        echo(e);
    }
}

function addClass(element, cname){
    try{
        var oldval=element.className;
        var newval = addWord(oldval, cname);
        echo(oldval+"+"+cname+"=>"+newval);
        element.className=newval;
    }catch(e){
        echo(e);
    }
}

function array2csv(a){
    var out="";
    for(var i in a) out+=(out?",":"")+a[i];
    return out;
}


//----
//          XMLHTTPREQUEST CODE
//----

// retrieve XML document (reusable generic function);
// parameter is URL string (relative or complete) to
// an .xml file whose Content-Type is a valid XML
// type, such as text/xml; XML source must be from
// same domain as HTML file
function loadXMLDoc(url,handler) {
    profile("loaddXMLDoc called");
    // branch for native XMLHttpRequest object
    if (window.XMLHttpRequest) {
        req = new XMLHttpRequest();
        req.onreadystatechange = processReqChange;
        req.open("GET", url, true);
        req.send(null);
    // branch for IE/Windows ActiveX version
    } else if (window.ActiveXObject) {
        isIE = true;
        req = new ActiveXObject("Microsoft.XMLHTTP");
        if (req) {
            req.onreadystatechange = processReqChange;
            req.open("GET", url, true);
            req.send();
        }
    }
}


// handle onreadystatechange event of req object
function processReqChange() {
    // only if req shows "loaded"
    if (req.readyState == 4) {
        // only if "OK"
        if (req.status == 200) {
            var datatype=getSingleNodeText(req.responseXML,"type");
            profile("Got response: "+datatype);
            if (datatype=="m") updateMessages();
         } else {
            //alert("There was a problem retrieving the XML data:\n" + req.statusText);
         }
    }
}


// retrieve text of an XML document element, including
// elements using namespaces
function getElementTextNS(prefix, local, parentElem, index) {
    var result = "";
    if (prefix && isIE) {
        // IE/Windows way of handling namespaces
        result = parentElem.getElementsByTagName(prefix + ":" + local)[index];
    } else {
        // the namespace versions of this method (getElementsByTagNameNS()) operate
        // differently in Safari and Mozilla, but both return value with just local name, provided 
        // there aren't conflicts with non-namespace element names
        result = parentElem.getElementsByTagName(local)[index];
    }
    if (result) {
        // get text, accounting for possible
        // whitespace (carriage return) text nodes 
        if (result.childNodes.length > 1) {
            return result.childNodes[1].nodeValue;
        } else {
            return result.firstChild.nodeValue;    		
        }
    } else {
        return "n/a";
    }
}


function getReqTarget(){
    var target_elem = req.responseXML.getElementsByTagName("target");
    if (target_elem.length>0) return getNodeText(target_elem[0]);
    else{
        alert("No target element in XML");
        return "";
    }
}

var S_OFFSET = 0; //(is_ie?0:1);
var F_OFFSET = 1; //(is_ie?1:2);
var D_OFFSET = 2; //(is_ie?2:3);
var Z_OFFSET = 3; //(is_ie?3:4);
var A_OFFSET = 4; //(is_ie?4:5);
var C_OFFSET = 5; //(is_ie?5:6);
var M_OFFSET = 6; //(is_ie?6:7);
var IR_OFFSET = 7; //(is_ie?7:8);
var NC_OFFSET = 8; //(is_ie?8:9);
var SN_OFFSET = 9; 
var FL_OFFSET = 10;

function printNode(i, node, prefix){
    echo(prefix+i+":"+node.nodeName+"=>"+getNodeText(node)+"("+(node.hasChildren?node.childNodes.length:0)+")");
}

function printDOM(data,prefix){
    var i;
    var node;
    try{
        for (i=0;i<data.childNodes.length;i++){
            node = data.childNodes[i];
            printNode(i, node, prefix);
            //echo(prefix+i+":"+node.nodeName+"=>"+getNodeText(node)+"("+node.nodeType+")");
            if (node.hasChildNodes() && node.childNodes.length>1) 
                printDOM(node,prefix+"&nbsp;&nbsp;&nbsp;");
        }
    }catch(e){
    }
}

function printChildValues(data,prefix){
    var i=0;
    var node;
    
    echo("Printing children of "+data);
    try{
        echo("Has "+data.childNodes.length+" children");
        for (i=0;i<data.childNodes.length;i++){
            node = data.childNodes[i];
            printNode(i, node, prefix);
        }
    }catch(e){
    }
}

function printChildNames(data){
    var i;
    for (i=0;i<data.childNodes.length;i++){
        echo(i+"=>"+data.childNodes[i].nodeValue);
    }
}


function formatAddressList(addresses, is_reply){
	var num=addresses.length;
	var c_url;
	var e_url;
	var name="";
	var q_name="";
	var email="";
	var target=PARAMS["com_tgt"];
	var class_str=(is_reply==1?"class=mainlt":"");
	var str="";
	
	if (num==0) return;
	
	for(var i=0; i<num; i++){
        node = addresses[i];
        name = getNodeText(node.childNodes[0]);
        email = getNodeText(node.childNodes[1]);
		if (i>0) str+=', ';
		if (name.indexOf(" ")!=-1) q_name="\""+name+"\"";
		else q_name = name;
		c_url = COMPOSE_URL+"&to="+URLencode(q_name+" <"+email+">");
		
		str+="<a href=\""+c_url+"\" target=\""+target+"\" title=\""+email+"\" "+class_str+" >"+disableHTML(name)+"</a>";
		
		//if (!contacts[email]){
			e_url = EDIT_URL+"&name="+URLencode(name)+"&email="+URLencode(email);
			str+="[<a href=\""+e_url+"\" "+class_str+">+</a>]";
		//}
	}
	return str;
}


function childrenToDictionary(node){
    var i;
    var name;
    var result = {};
    for(i=0;i<node.childNodes.length;i++){
        name = node.childNodes[i].nodeName;
        if (name)
            result[name] = getNodeText(node.childNodes[i]);
    }
    return result;
}

function childrenToArray(node){
    var i;
    var result = [];
    for(i=0;i<node.childNodes.length;i++){
        result[i] = getNodeText(node.childNodes[i]);
    }
    return result;
}

function setClassName(el, classname){
    try{
        el.className = classname;
    }catch(e){
        try{
            el.setAttribute("class",classname);
        }catch(e){
        }
    }
}

function createMessageCell(data, code, is_reply, unread){
    var e;
    var str="";

	switch(code){
        case "s":
            var node = data.childNodes[S_OFFSET];
            e = childrenToArray(node);
            //printChildNames(node);
            var url=PARAMS["action"]+"?"+PARAMS["args"]+"&id="+e[0]+"&uid="+e[1];
			var t=(PARAMS["open_tgt"].length>0?" target=\""+PARAMS["open_tgt"]+"\"":"");
			var c=(e[3].length>0?" class=\""+e[3]+"\"":"");
			var nc=getNodeText(data.childNodes[NC_OFFSET]);
			var th="";
			var s=""; //(e[5]==1?" style=\"font-weight:bold\"":"");
            UIDS+=e[1]+",";

			if (nc>0) th="<a href=\"javascript:T("+row_count+","+nc+");\" class=\"tcnt\">[+"+nc+"]";
            str = th+"<a href=\""+url+"\""+t+c+s+" id=l"+row_count+">"+e[4]+"</a>";
			break;
		case "f":
			str = formatAddressList(data.childNodes[F_OFFSET].childNodes, is_reply);
			break;
        case "d":
            e = getNodeText(data.childNodes[D_OFFSET]);
            str = "<nobr>"+e+"&nbsp;</nobr>";
			break;
		case "z":
            e = getNodeText(data.childNodes[Z_OFFSET]);
			str = "<nobr>"+e+"</nobr>";
			break;
		case "a":
            e = getNodeText(data.childNodes[A_OFFSET]);
			if (e==1) str = "<img src=\"themes/"+PARAMS["theme"]+"/images/att.gif\">";
			break;
		case "c":
            var node = data.childNodes[C_OFFSET];
            e = childrenToArray(node);
			var nc=getNodeText(data.childNodes[NC_OFFSET]);
			var th="";
			var mid=(UID_ENABLED?e[2]:e[0]);
			if (nc>0) th=" onClick=\"C("+row_count+","+nc+");\"";
            str ="<input type=\"checkbox\" name=\"checkboxes\" value=\""+mid+"\" id=\"c"+row_count+"\"";
	        str+=(e[1]==1?" CHECKED":"")+" "+th+">";
			break;
		case "m":
            var node = data.childNodes[M_OFFSET];
            e = childrenToArray(node);
			if (e[0]==1) str+= "D";
			if (e[1]==1) str+= "<img src=\"themes/"+PARAMS["theme"]+"/images/reply.gif\">";
			else str+= "&nbsp;";
			if (e[2]==1) str+= "<span style=\"color:red\"><b>!</b></span>";
			break;
	}

	var td="<td";
	var s="";
	var c="";
	
	if (is_reply==1){
	   if (code=="s") c="thsbj"; //s="padding-left:30px;";
	   else c="subthread";
	}
	if (unread==1 && code=="s") c+=" "+"unseen"; //s+="font-weight:bold;";
    if (s) s="style=\""+s+"\"";
    if (c) c="class=\""+c+"\"";
    
	td="<td "+s+" "+c+">"+str+"</td>";

	return td;
}

function createMessageRow(item){
	var main_cols = PARAMS["main_cols"];
	var num_cols = PARAMS["num_cols"];
	var is_reply = getElementTextNS("", "is_reply", item, 0);
	var is_unread = parseInt(getElementTextNS("", "sn", item, 0));
	var is_flagged = parseInt(getElementTextNS("", "fl",item, 0));
    var newtd;
    var classname=(is_unread?"unseen":"seen");
    var stylestr = (is_reply==1?"style=\"display:none\"":"style=\"height:5px\"");
    var html="";
    
    if (is_flagged) classname+=" important";
    //html+="class="+classname+" id=r"+row_count+" ";
    if (is_reply==1) html+="style=\"display: none\"";
    //html+=">";
    html="<tr class=\""+classname+"\" id=r"+row_count+" "+stylestr+">";  
    for (var i=0;i<num_cols;i++){
        html+=createMessageCell(item, main_cols.charAt(i), is_reply, is_unread);
	}
	html+="</tr>";
	return html;
}

function getNodeChildByTag(node, tag){
    var i;
    try{
        for (i=0;i<node.childNodes.length;i++){
            if (node.childNodes[i].nodeName==tag) return node.childNodes[i];
        }
    }catch(e){
    }
    return false;
}

function toggleDisplay(elid, on){
    var el = getElement(target_win, elid);
    if (el){
        if (on==1) el.style.display="";
        else el.style.display="none";
    }
}

function clearPageMenu(select){
    while (select.length>0){
        select.remove(0);
    }
}

function getCheckboxes(){
    var checkboxes = new Array();
    var e;
    
    for(var i=0;i<=row_count;i++){
        e = getElement(target_win, "c"+i);
        if (!e) continue;
        if (e.checked) checkboxes[i]=e.value;
    }
    return checkboxes;
}

function updatePagination(start, total, num){
    var select = document.getElementById("pagemenu");
    var c=0, c2=0;
    var text,opt;
    if (!select) return;
    
    clearPageMenu(select);
    
    while(c<total){
        c2 = (c + num);
        if (c2 > total) c2 = total;
        text = document.createTextNode((c+1)+"-"+c2);
        opt = document.createElement("option");
        opt.value=c;
        if (c==start) opt.selected=true;
        opt.appendChild(text);
        select.appendChild(opt);
        c+=num;
    }
}

function updatePage(start){
    var select = document.getElementById("pagemenu");
    var i=0;
    
    for(i=0;i<select.childNodes.length;i++){
        if (select.childNodes[i].value==start)
            select.childNodes[i].selected=true;
        else
            select.childNodes[i].selected=false;
    }
}

function updateUI(data){
    var next = data["next"];
    var start = data["start"];//getNodeChildByTag(node, "start");
    var prev = data["prev"]; //getNodeChildByTag(node, "prev");
    var tnum = data["tnum"]; //getNodeChildByTag(node, "tnum");
    var show = data["show"]; //getNodeChildByTag(node, "show");
    var old_start, old_total_num, old_num_show;

    old_start = START_ID;
    old_total_num = total_num;
    old_num_show = num_show;

    if (next){
        NEXT_ID = parseInt(next);
        if (NEXT_ID>0)toggleDisplay("nextlink",1);
        else toggleDisplay("nextlink",0);
    }
    if (start) START_ID = parseInt(start); 
    if (prev){
        PREV_ID = parseInt(prev);
        if (PREV_ID!=START_ID)toggleDisplay("prevlink",1);
        else toggleDisplay("prevlink",0);
    }
    if (tnum) total_num = parseInt(tnum);
    if (show) num_show = parseInt(show);
    
    FUID=parseInt(data["fuid"]);
    LUID=parseInt(data["luid"]);
    //UID_ENABLED=parseInt(data["uid"]);
    
    if (total_num!=old_total_num || num_show!=old_num_show)
        updatePagination(START_ID, total_num, num_show);
    if (old_start != START_ID)
        updatePage(START_ID);
}

function fetchCachedMsgList(data,naive){
    //try to find based on basic info
    var folder = data["folder"];
    if (!folder) return "folder:"+folder;
    if (!page_cache[folder]) return "no[folder]";
    var start="c"+data["start"];
    if (!page_cache[folder][start]) return "start:"+start;
    
    if (!naive){
        //not naive = do integrety check
        var fuid=parseInt(data["fuid"]);
        var luid=parseInt(data["luid"]);
        if (page_cache[folder][start]["fuid"]!=fuid) return "fuid:"+fuid+":"+page_cache[folder][start]["fuid"];
        if (page_cache[folder][start]["luid"]!=luid) return "luid:"+luid;
    }
    
    //update some globals
    FOLDER = folder;
    row_count = parseInt(page_cache[folder][start]["rows"]);
    updateUI(page_cache[folder][start]);
    echo("Returning cache for "+folder+" "+start);
    return page_cache[folder][start]["data"];
}

function purgeCachedMsgList(folder, start){
    try{
        page_cache[folder]["c"+start]="";
    }catch(e){
        echo("purgeCachedMsgList:"+e);
    }
}

function cacheMsgList(data, html){
    var folder = data["folder"];
    if (!page_cache[folder]) page_cache[folder] = {};
    var start = "c"+data["start"];
    echo("Searching cache for "+folder+" "+data["start"]);
    if (!page_cache[folder][start]) page_cache[folder][start] = {};
    
    for (var key in data){
        page_cache[folder][start][key] = data[key];
        //echo("cache: "+key+"=&gt;"+data[key]);
    }
    
    cache_size+=html.length;
    page_cache[folder][start]["data"] = html;
    echo("Cached "+folder+" "+start+" ("+cache_size+")");
}

function getMessageListing(messages,data){
    var html=fetchCachedMsgList(data);
    
    if (html!=""&&html.length>50){
        echo("Cache hit for "+data["folder"]+" "+data["start"]);
        return html;
    }
    echo("Cache result: "+html);
    html="";
    
    UIDS="";
    row_count=0;
    for (var i=0; i<messages.length; i++){
        //printChildValues(messages[i],"");
        if (messages[i].nodeName=="msg"){
            row_count++;
            //msgtable.appendChild(createMessageRow(messages[i]));
            //msgtable.innerHTML+=createMessageRow(messages[i]);
            html+=createMessageRow(messages[i], row_count-1);
        }
    }
    
    cacheMsgList(data,html);
    return html;
}

function updateMsgtable(msgtable, html){
    try{
        if (!msgtable) msgtable = getElement(window,"msglist");
        msgtable.innerHTML = html;
        echo("msglist updated "+html.length);
    }catch(e){
    }
}

function updateMessages(){
    if (is_ie){
        var messages = req.responseXML.getElementsByTagName("msg");
        var data = getNodeChildByTag(req.responseXML,"data");
        var ctx = getNodeChildByTag(data, "ctx");
    }else{
        var msgnode = getNodeChildByTag(req.responseXML.childNodes[0], "messages");
        var messages = msgnode.childNodes;
        var data = req.responseXML.childNodes[0];
        var ctx = data.childNodes[1];
    }
    var msgtable = getElement(window,"msglist");
    var node_data = childrenToDictionary(ctx);
    //var num_rows = parseInt(node_data["rows"]);
    //var first_uid = parseInt(node_data["fuid"]);
    //var last_uid = parseInt(node_data["luid"]);
    //var start = parseInt(node_data["start"]);
    var html="";

    FOLDER = node_data["folder"];
    UID_ENABLED = parseInt(node_data["uid"]);
    
    echo("uid:"+UID_ENABLED);

    profile("initialized ");
    html = getMessageListing(messages, node_data);
    updateMsgtable(msgtable, html);
    
    profile("listed ");
    updateUI(node_data);
    profile(" done");
    //clearMessages(msgtable);
}

function doNextPrevPage(next){
    echo("<br>");
    profile("doNextPrevPage start");
    var goto_id = (next==1?NEXT_ID:PREV_ID);
    var url=XMLURL+"&start="+goto_id;
    var data={"folder":FOLDER,"start":goto_id};
    var html=fetchCachedMsgList(data,true);
    
    if (html.length>20){
        updateMsgtable(null, html);
        url+="&luid="+LUID+"&fuid="+FUID;
        profile("preloaded cache");
    }else{
        echo("doNextPrev cache: "+html);
    }
    
    echo("Loading: "+url);
    
    loadXMLDoc(url);
}

function doRefreshPage(){
    var url=XMLURL+"&cmc=1&haveuids="+UIDS;
    echo("Loading: "+url);
    loadXMLDoc(url);
}

function doFilter(){
    var menu = getElement(window, "filtermenu");
    var url=XMLURL;
    if (menu&&menu.value>0){
        echo("You selected "+menu.value);
        url+="&apply_filter="+menu.value+"&do_apply_filter=1";
        echo("Loading: "+url);
        loadXMLDoc(url);
    }
}

function doQuickSearch(){
    var url=XMLURL;
    var box = getElement(window, "qsearch");
    if (box&&box.value.length>0){
        url+="&quick_search_str="+box.value+"&do_quick_search=1";
        echo("Loading: "+url);
        loadXMLDoc(url);
    }
}

function doChangePage(){
    var select = document.getElementById("pagemenu");
    var url=XMLURL+"&start="+select.value;
    echo("Loading: "+url);
    loadXMLDoc(url);
}

function purgeSelectedRows(selected){
    var msgtable = getElement(target_win, "msglist");

    for(var row in selected){
        msgtable.rows[row-1].style.display="none";
    }
}

function doMove(){
    var selected = getCheckboxes();
    var menu = getElement(target_win, "foldermenu");
    var url=XMLURL;
    url+="&"+(UID_ENABLED?"uidl":"midl")+"="+array2csv(selected)+"&moveto="+menu.value;
    url+="&submit=File&start="+START_ID;
    //url+="&luid="+LUID+"&fuid="+FUID;
    //purgeCachedMsgList(FOLDER, START_ID);
    purgeSelectedRows(selected);
    echo(url);
    loadXMLDoc(url);
}

function doDelete(){
    var selected = getCheckboxes();
    var url=XMLURL;
    url+="&"+(UID_ENABLED?"uidl":"midl")+"="+array2csv(selected);
    url+="&submit=Delete&start="+START_ID;
    purgeSelectedRows(selected);
    echo(url);
    loadXMLDoc(url);
}

function doFlagRead(selected,read){
    var msgtable = getElement(target_win, "msglist");
    var soffset = PARAMS["main_cols"].indexOf("s");
    var newclass=(read?"seen":"unseen");
    
    if (!msgtable){
        echo("No msgtable!");
        return false;
    }
    //echo(selected.length+" selected "+soffset);
    for(var row in selected){
        echo("selected "+row+"->"+selected[row]);
        if (msgtable.rows[row-1].className!="important")
            msgtable.rows[row-1].className=newclass;
        //echo("set row "+row+" to ");
        if (read) removeClass(msgtable.rows[row-1].cells[soffset], "unseen");
        else addClass(msgtable.rows[row-1].cells[soffset], "unseen");
    }
}


function doFlagFlagged(selected, flagged){
    var msgtable = getElement(target_win, "msglist");
    var soffset = PARAMS["main_cols"].indexOf("s");
    var oldclass="";
    
    if (!msgtable) return false;
    
    for(var row in selected){
        echo("seting row "+row+" to important");
        if (flagged) addClass(msgtable.rows[row-1],"important");
        else removeClass(msgtable.rows[row-1],"important");
    }
}


function doFlag(){
    var selected = getCheckboxes();
    var menu = getElement(target_win, "flagmenu");
    
    if (!menu){
        echo("No flag menu!");
        return false;
    }
    echo("Flag menu is: "+menu.value+" "+menu.length);
    if (menu.value=="Read") doFlagRead(selected, true);
    else if (menu.value=="Unread") doFlagRead(selected, false);
    else if (menu.value=="Flagged") doFlagFlagged(selected,true);
    else if (menu.value=="Unflagged") doFlagFlagged(selected,false);
    
    //main.php?user=1108838830_56988&folder=INBOX&checkboxes[]=762&uids[]=3339&submit=Unread&start=0
    var url=XMLURL;
    url+="&"+(UID_ENABLED?"uidl":"midl")+"="+array2csv(selected)+"&submit="+menu.value+"&start="+START_ID;
    url+="&luid="+LUID+"&fuid="+FUID;
    purgeCachedMsgList(FOLDER, START_ID);
    echo(url);
    loadXMLDoc(url);
}

function threadCheck(n, m){	var i;	var row_elem, cb_elem;	var original_elem=getElement(window, "c"+n);	
	echo("threadCheck "+n+","+m);
		if (!original_elem) return false;	var new_val = original_elem.checked;			for(i=n+1;i<=(n+m);i++){		row_elem = getElement(window, "r"+i);		if (row_elem&&new_val) row_elem.style.display=(new_val?'':'none');		cb_elem = getElement(window, "c"+i);		if (cb_elem) cb_elem.checked=new_val;	}}
function C(n,m){ threadCheck(n,m);}

function T(n,m){
    toggleThreads(n,m);
}

function toggleThreads(n,m){
	var i;	var elem;		for(i=n+1;i<=(n+m);i++){		elem = getElement(window, "r"+i);
		if (elem){			if (!elem.style.display) elem.style.display='none'; //elem.parentNode.removeChild(elem);			else elem.style.display='';		}	}}
//----
//          KEY HANDLER CODE 
//----

function handleException(e, m){
	try{
		alert("Exception ("+m+"): "+e.name+"-"+e.message);
	}catch(this_e){
	}
}




function keyPressHandler(e){
	try{
		var keyCode = getKeyCode(e);
		var keyChar = String.fromCharCode(keyCode);
		var elem = getElement(target_win, "main_div");
		var targ = getTarget(e);
		
		if (targ){
            try{
                //alert("suggest.php?t="+targ.name+"&hint="+targ.value);
                loadData(targ.name, targ.value);
                //loadXMLDoc("suggest.php?t="+targ.name+"&hint="+targ.value);
            }catch(e){
            }
		}
		
		return true;
	}catch(e){
		handleException(e, "keyPressHandler");
	}
}


function ac(){
    var artist = getElementValue("artist");
    var url = "";
    if (artist){
        url = "suggest2.php?m=books&f=author&a="+artist;
        loadXMLDoc(url);
    }
}

function tc(){
    var artist = getElementValue("artist");
    var title = getElementValue("title");
    var url = "suggest2.php?m=books&f=title";
    
    if (artist) url += "&a=" + artist;
    if (title){
        url += "&t=" + title;
        loadXMLDoc(url);
    }
}

function initEvents(win){
	if (!win) return;
	try{
		win.document.captureEvents(Event.KEYUP);
	}catch(e){}
	try{
		win.document.onkeyup = keyPressHandler;
	}catch(e){
		handleException(e, "initEvents");
	}
	target_win = win;
}

function processHint(){
    var elem = document.getElementById("hint");
    alert(elem.value);
}

function initFolderMenu(){
    var menu=getElement(target_win, "foldermenu");
    var text="";
    var opt;
    var select;
    
    opt = document.createElement("option");
    menu.appendChild(opt);
    
    for (var key in FOLDERS){
        opt = document.createElement("option");
        opt.innerHTML=FOLDERS[key];
        opt.value=key;
        menu.appendChild(opt);
    }
}

function initUI(){
    initFolderMenu();
}

function init(win){
    target_win = win;
    initUI();
    debugOut(XMLURL);
    loadXMLDoc(XMLURL);
}

