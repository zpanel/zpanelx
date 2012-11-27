var querywindow="",query_to_load="";
function addEvent(a,b,c){
    if(a.attachEvent){
        a["e"+b+c]=c;
        a[b+c]=function(){
            a["e"+b+c](window.event)
            };
            
        a.attachEvent("on"+b,a[b+c])
        }else a.addEventListener(b,c,false)
        }
        function removeEvent(a,b,c){
    if(a.detachEvent){
        a.detachEvent("on"+b,a[b+c]);
        a[b+c]=null
        }else a.removeEventListener(b,c,false)
        }
function getElementsByClassName(a,b,c){
    var d=[];
    if(b==null)b=document;
    if(c==null)c="*";
    var e=0;
    c=b.getElementsByTagName(c);
    var f=c.length;
    for(i=0;i<f;i++)if(c[i].className.indexOf(a)!=-1){
        b=","+c[i].className.split(" ").join(",")+",";
        if(b.indexOf(","+a+",")!=-1){
            d[e]=c[i];
            e++
        }
    }
    return d
}
function setDb(a){
    if(a!=db){
        var b=db;
        db=a;
        if(window.frame_navigation.document.getElementById(db)==null)refreshNavigation();
        else{
            unmarkDbTable(b);
            markDbTable(db)
            }
            refreshQuerywindow()
        }
    }
function setTable(a){
    if(a!=table){
        table=a;
        window.frame_navigation.document.getElementById(db+"."+table)==null&&table!=""&&refreshNavigation();
        refreshQuerywindow()
        }
    }
function refreshMain(a){
    a||(a=db?opendb_url:"main.php");
    goTo(a+"?server="+encodeURIComponent(server)+"&token="+encodeURIComponent(token)+"&db="+encodeURIComponent(db)+"&table="+encodeURIComponent(table)+"&lang="+encodeURIComponent(lang)+"&collation_connection="+encodeURIComponent(collation_connection),"main")
    }
function refreshNavigation(a){
    typeof a!=undefined&&a&&window.parent&&window.parent.frame_navigation?window.parent.frame_navigation.location.reload():goTo("navigation.php?server="+encodeURIComponent(server)+"&token="+encodeURIComponent(token)+"&db="+encodeURIComponent(db)+"&table="+encodeURIComponent(table)+"&lang="+encodeURIComponent(lang)+"&collation_connection="+encodeURIComponent(collation_connection))
    }
function unmarkDbTable(a,b){
    var c=window.frame_navigation.document.getElementById(a);
    c!=null&&$(c).parent().removeClass("marked");
    c=window.frame_navigation.document.getElementById(a+"."+b);
    c!=null&&$(c).parent().removeClass("marked")
    }
function markDbTable(a,b){
    var c=window.frame_navigation.document.getElementById(a);
    if(c!=null){
        $(c).parent().addClass("marked");
        c.focus();
        c.blur()
        }
        c=window.frame_navigation.document.getElementById(a+"."+b);
    if(c!=null){
        $(c).parent().addClass("marked");
        c.focus();
        c.blur()
        }
        window.frame_content.focus()
    }
function setAll(a,b,c,d,e,f){
    if(c!=server||a!=lang||b!=collation_connection){
        server=c;
        db=d;
        table=e;
        collation_connection=b;
        lang=a;
        token=f;
        refreshNavigation()
        }else if(d!=db||e!=table){
        a=db;
        b=table;
        db=d;
        table=e;
        if(window.frame_navigation.document.getElementById(db)==null&&window.frame_navigation.document.getElementById(db+"."+table)==null)refreshNavigation();
        else{
            unmarkDbTable(a,b);
            markDbTable(db,table)
            }
            refreshQuerywindow()
        }
    }
function reload_querywindow(a,b,c){
    if(!querywindow.closed&&querywindow.location)if(!querywindow.document.sqlform.LockFromUpdate||!querywindow.document.sqlform.LockFromUpdate.checked){
        querywindow.document.getElementById("hiddenqueryform").db.value=a;
        querywindow.document.getElementById("hiddenqueryform").table.value=b;
        if(c)querywindow.document.getElementById("hiddenqueryform").sql_query.value=c;
        querywindow.document.getElementById("hiddenqueryform").submit()
        }
    }
function focus_querywindow(a){
    if(!querywindow||querywindow.closed||!querywindow.location){
        query_to_load=a;
        open_querywindow();
        insertQuery(0)
        }else{
        if(querywindow.document.getElementById("hiddenqueryform").querydisplay_tab!="sql"){
            querywindow.document.getElementById("hiddenqueryform").querydisplay_tab.value="sql";
            querywindow.document.getElementById("hiddenqueryform").sql_query.value=a;
            querywindow.document.getElementById("hiddenqueryform").submit()
            }
            querywindow.focus()
        }
        return true
    }
function insertQuery(){
    if(query_to_load!=""&&querywindow.document&&querywindow.document.getElementById&&querywindow.document.getElementById("sqlquery")){
        querywindow.document.getElementById("sqlquery").value=query_to_load;
        query_to_load="";
        return true
        }
        return false
    }
function open_querywindow(a){
    a||(a="querywindow.php?"+common_query+"&db="+encodeURIComponent(db)+"&table="+encodeURIComponent(table));
    if(!querywindow.closed&&querywindow.location){
        goTo(a,"query");
        querywindow.focus()
        }else querywindow=window.open(a+"&init=1","","toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=yes,resizable=yes,width="+querywindow_width+",height="+querywindow_height);
    if(!querywindow.opener)querywindow.opener=window.window;
    window.focus&&querywindow.focus();
    return true
    }
function refreshQuerywindow(a){
    if(!querywindow.closed&&querywindow.location)if(!querywindow.document.sqlform.LockFromUpdate||!querywindow.document.sqlform.LockFromUpdate.checked)open_querywindow(a)
        }
        function goTo(a,b){
    if(b=="main")b=window.frame_content;
    else if(b=="query")b=querywindow;
    else if(!b)b=window.frame_navigation;
    if(b){
        if(b.location.href==a)return true;
        else if(b.location.href==pma_absolute_uri+a)return true;
        if(safari_browser)b.location.href=a;else b.location.replace(a)
            }
            return true
    }
function openDb(a){
    setDb(a);
    setTable("");
    refreshMain(opendb_url);
    return true
    }
    function updateTableTitle(a,b){
    if(window.parent.frame_navigation.document&&window.parent.frame_navigation.document.getElementById(a)){
        var c=window.parent.frame_navigation.document,d=c.getElementById(a);
        d.title=window.parent.pma_text_default_tab+": "+b;
        d=c.getElementById("quick_"+a);
        d.title=window.parent.pma_text_left_default_tab+": "+b;
        return true
        }
        return false
    };
