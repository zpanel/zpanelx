function PMA_queryAutoCommit(){
    document.getElementById("sqlqueryform").target=window.opener.frame_content.name;
    document.getElementById("sqlqueryform").submit()
    }
    function PMA_querywindowCommit(a){
    document.getElementById("hiddenqueryform").querydisplay_tab.value=a;
    document.getElementById("hiddenqueryform").submit();
    return false
    }
    function PMA_querywindowSetFocus(){
    document.getElementById("sqlquery").focus()
    }
function PMA_querywindowResize(){
    if(typeof self.sizeToContent=="function"){
        self.sizeToContent();
        self.resizeBy(10,50)
        }else if(document.getElementById&&typeof document.getElementById("querywindowcontainer")!="undefined"){
        var a=document.getElementById("querywindowcontainer").offsetWidth,b=document.getElementById("querywindowcontainer").offsetHeight;
        self.resizeTo(a+45,b+75)
        }
    };
