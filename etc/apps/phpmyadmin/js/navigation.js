var today=new Date,expires=new Date(today.getTime()+48384E5),pma_navi_width,pma_saveframesize_timeout=null;
function toggle(a,d){
    var b=document.getElementById("subel"+a);
    if(!b)return false;
    var c=document.getElementById("el"+a+"Img");
    if(b.style.display=="none"||d){
        b.style.display="";
        if(c){
            c.src=image_minus;
            c.alt="-"
            }
        }else{
    b.style.display="none";
    if(c){
        c.src=image_plus;
        c.alt="+"
        }
    }
return true
}
function PMA_callFunctionDelayed(){
    if(typeof pma_saveframesize_timeout=="number"){
        window.clearTimeout(pma_saveframesize_timeout);
        pma_saveframesize_timeout=null
        }
    }
function PMA_saveFrameSizeReal(){
    pma_navi_width=parent.text_dir=="ltr"?parseInt(parent.document.getElementById("mainFrameset").cols):parent.document.getElementById("mainFrameset").cols.match(/\d+$/);
    pma_navi_width>0&&pma_navi_width!=PMA_getCookie("pma_navi_width")&&PMA_setCookie("pma_navi_width",pma_navi_width,expires)
    }
function PMA_saveFrameSize(){
    if(typeof pma_saveframesize_timeout=="number"){
        window.clearTimeout(pma_saveframesize_timeout);
        pma_saveframesize_timeout=null
        }
        pma_saveframesize_timeout=window.setTimeout(PMA_saveFrameSizeReal,2E3)
    }
function PMA_setFrameSize(){
    pma_navi_width=PMA_getCookie("pma_navi_width");
    if(pma_navi_width!=null&&parent.document!=document)if(parent.text_dir=="ltr")parent.document.getElementById("mainFrameset").cols=pma_navi_width+",*";else parent.document.getElementById("mainFrameset").cols="*,"+pma_navi_width
        }
function PMA_getCookie(a){
    var d=document.cookie.indexOf(a+"="),b=d+a.length+1;
    if(!d&&a!=document.cookie.substring(0,a.length))return null;
    if(d==-1)return null;
    a=document.cookie.indexOf(";",b);
    if(a==-1)a=document.cookie.length;
    return unescape(document.cookie.substring(b,a))
    }
    function PMA_setCookie(a,d,b,c,e,f){
    document.cookie=a+"="+escape(d)+(b?";expires="+b.toGMTString():"")+(c?";path="+c:"")+(e?";domain="+e:"")+(f?";secure":"")
    }
function fast_filter(a){
    lowercase_value=a.toLowerCase();
    $("#subel0 a[class!='tableicon']").each(function(d,b){
        $elem=$(b);
        a&&$elem.html().toLowerCase().indexOf(lowercase_value)==-1?$elem.parent().hide():$elem.parents("li").show()
        })
    }
    function clear_fast_filter(){
    var a=$("#NavFilter input");
    a.val("");
    fast_filter("");
    a.focus()
    }
$(document).ready(function(){
    $("#NavFilter").css("display","inline");
    $('input[id="fast_filter"]').focus(function(){
        $(this).attr("value")==="filter tables by name"&&clear_fast_filter()
        });
    $("#clear_fast_filter").click(clear_fast_filter);
    $("#fast_filter").focus(function(a){
        a.target.select()
        });
    $("#fast_filter").keyup(function(a){
        fast_filter(a.target.value)
        })
    });
