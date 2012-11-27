function nullify(a,c,b,d){
    c=document.forms.insertForm;
    if(typeof c.elements["funcs"+d+"["+b+"]"]!="undefined")c.elements["funcs"+d+"["+b+"]"].selectedIndex=-1;
    if(a==1)c.elements["fields"+d+"["+b+"]"][1].selectedIndex=-1;
    else if(a==2){
        a=c.elements["fields"+d+"["+b+"]"];
        if(a.checked)a.checked=false;
        else{
            b=a.length;
            for(d=0;d<b;d++)a[d].checked=false
                }
            }else if(a==3)c.elements["fields"+d+"["+b+"][]"].selectedIndex=-1;
else if(a==4)c.elements["fields"+d+"["+b+"]"].selectedIndex=-1;else c.elements["fields"+
    d+"["+b+"]"].value="";
return true
}
function daysInFebruary(a){
    return a%4==0&&(a%100!=0||a%400==0)?29:28
    }
    function fractionReplace(a){
    a=parseInt(a);
    var c="00";
    switch(a){
        case 1:
            c="01";
            break;
        case 2:
            c="02";
            break;
        case 3:
            c="03";
            break;
        case 4:
            c="04";
            break;
        case 5:
            c="05";
            break;
        case 6:
            c="06";
            break;
        case 7:
            c="07";
            break;
        case 8:
            c="08";
            break;
        case 9:
            c="09"
            }
            return c
    }
function isDate(a,c){
    a=a.replace(/[.|*|^|+|//|@]/g,"-");
    for(var b=a.split("-"),d=0;d<b.length;d++)if(b[d].length==1)b[d]=fractionReplace(b[d]);a=b.join("-");
    b=2;
    dtexp=RegExp(/^([0-9]{4})-(((01|03|05|07|08|10|12)-((0[0-9])|([1-2][0-9])|(3[0-1])))|((02|04|06|09|11)-((0[0-9])|([1-2][0-9])|30)))$/);
    if(a.length==8){
        dtexp=RegExp(/^([0-9]{2})-(((01|03|05|07|08|10|12)-((0[0-9])|([1-2][0-9])|(3[0-1])))|((02|04|06|09|11)-((0[0-9])|([1-2][0-9])|30)))$/);
        b=0
        }
        if(dtexp.test(a)){
        d=parseInt(a.substring(b+3,b+5));
        var e=parseInt(a.substring(b+6,b+8)),g=parseInt(a.substring(0,b+2));
        if(d==2&&e>daysInFebruary(g))return false;
        if(a.substring(0,b+2).length==2)g=a.substring(0,b+2).length==2?parseInt("20"+a.substring(0,b+2)):parseInt("19"+a.substring(0,b+2));
        if(c==true){
            if(g<1978)return false;
            if(g>2038||g>2037&&e>19&&d>=1||g>2037&&d>1)return false
                }
            }else return false;
return true
}
function isTime(a){
    a=a.split(":");
    for(var c=0;c<a.length;c++)if(a[c].length==1)a[c]=fractionReplace(a[c]);a=a.join(":");
    tmexp=RegExp(/^(([0-1][0-9])|(2[0-3])):((0[0-9])|([1-5][0-9])):((0[0-9])|([1-5][0-9]))$/);
    if(!tmexp.test(a))return false;
    return true
    }
function verificationsAfterFieldChange(a,c,b){
    var d=window.event||arguments.callee.caller.arguments[0],e=d.target||d.srcElement;
    $("input[name='fields_null[multi_edit]["+c+"]["+a+"]']").attr({
        checked:false
    });
    $("input[name='insert_ignore_"+c+"']").attr({
        checked:false
    });
    d=$("input[name='fields[multi_edit]["+c+"]["+a+"]']");
    if(d.data("comes_from")=="datepicker"){
        d.data("comes_from","");
        return true
        }
        if(e.name.substring(0,6)=="fields"){
        if(b=="datetime"||b=="time"||b=="date"||b=="timestamp"){
            d.removeClass("invalid_value");
            e=d.val();
            if(b=="date"){
                if(!isDate(e)){
                    d.addClass("invalid_value");
                    return false
                    }
                }else if(b=="time"){
            if(!isTime(e)){
                d.addClass("invalid_value");
                return false
                }
            }else if(b=="datetime"||b=="timestamp"){
        tmstmp=false;
        if(e=="CURRENT_TIMESTAMP")return true;
        if(b=="timestamp")tmstmp=true;
        if(e=="0000-00-00 00:00:00")return true;
        var g=e.indexOf(" ");
        if(g==-1){
            d.addClass("invalid_value");
            return false
            }else if(!(isDate(e.substring(0,g),tmstmp)&&isTime(e.substring(g+1)))){
            d.addClass("invalid_value");
            return false
            }
        }
}
if(b.substring(0,
    3)=="int"){
    d.removeClass("invalid_value");
    if(isNaN(d.val())){
        d.addClass("invalid_value");
        return false
        }
    }
}
}
$(document).ready(function(){
    $(".foreign_values_anchor").show();
    $(".checkbox_null").bind("click",function(){
        nullify($(this).siblings(".nullify_code").val(),$(this).closest("tr").find("input:hidden").first().val(),$(this).siblings(".hashed_field").val(),$(this).siblings(".multi_edit").val())
        });
    $("#insertFormDEACTIVATED").live("submit",function(a){
        var c=$(this);
        a.preventDefault();
        PMA_ajaxShowMessage();
        PMA_prepareForAjaxRequest(c);
        $.post(c.attr("action"),c.serialize(),function(b){
            if(typeof b.success!=
                "undefined")if(b.success==true){
                PMA_ajaxShowMessage(b.message);
                $("#topmenucontainer").next("div").remove().end().after(b.sql_query);
                b=$("#topmenucontainer").next("div").find(".notice");
                b.text()==""&&b.remove();
                b=c.find("select[name='submit_type']").val();
                if("insert"==b||"insertignore"==b)c.find("input:reset").trigger("click")
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+b.error,"7000");
            else{
                $("#insertForm").remove();
                $("#topmenucontainer").after('<div id="sqlqueryresults"></div>');
                $("#sqlqueryresults").html(b)
                }
            })
    });
$("#insert_rows").live("change",function(a){
    a.preventDefault();
    a=$(".insertRowTable").length;
    var c=$("#insert_rows").val();
    $(".datefield,.datetimefield").each(function(){
        $(this).datepicker("destroy")
        });
    if(a<c){
        for(;a<c;){
            var b=0;
            $("#insertForm").find(".insertRowTable:last").clone().insertBefore("#actions_panel").find("input[name*=multi_edit],select[name*=multi_edit],textarea[name*=multi_edit]").each(function(){
                var f=$(this),i=f.attr("name"),j=i.split(/\[\d+\]/);
                i=i.match(/\[\d+\]/)[0];
                b=parseInt(i.match(/\d+/)[0])+1;
                i=j[0]+"["+b+"]"+j[1];
                j=j[1].match(/\[(.+)\]/)[1];
                f.attr("name",i);
                if(f.is(".textfield")){
                    f.closest("tr").find("span.column_type").html()!="enum"&&f.attr("value",f.closest("tr").find("span.default_value").html());
                    f.unbind("change").attr("onchange",null).data("hashed_field",j).data("new_row_index",b).bind("change",function(){
                        var h=$(this);
                        verificationsAfterFieldChange(h.data("hashed_field"),h.data("new_row_index"),h.closest("tr").find("span.column_type").html())
                        })
                    }
                    f.is(".checkbox_null")&&
                f.unbind("click").data("hashed_field",j).data("new_row_index",b).bind("click",function(){
                    var h=$(this);
                    nullify(h.siblings(".nullify_code").val(),f.closest("tr").find("input:hidden").first().val(),h.data("hashed_field"),"[multi_edit]["+h.data("new_row_index")+"]")
                    })
                }).end().find(".foreign_values_anchor").each(function(){
                $anchor=$(this);
                var f="rownumber="+b;
                f=$anchor.attr("href").replace(/rownumber=\d+/,f);
                $anchor.attr("href",f)
                });
            if(a==1)$('<input id="insert_ignore_1" type="checkbox" name="insert_ignore_1" checked="checked" />').insertBefore(".insertRowTable:last").after('<label for="insert_ignore_1">'+
                PMA_messages.strIgnore+"</label>");
            else{
                var d=$("#insertForm").children("input:checkbox:last"),e=$(d).attr("name"),g=parseInt(e.match(/\d+/));
                e=e.replace(/\d+/,g+1);
                $(d).clone().attr({
                    id:e,
                    name:e,
                    checked:true
                }).add("label[for^=insert_ignore]:last").clone().attr("for",e).before("<br />").insertBefore(".insertRowTable:last")
                }
                a++
        }
        var k=0;
        $(".textfield").each(function(){
            k++;
            $(this).attr("tabindex",k);
            $(this).attr("id","field_"+k+"_3")
            });
        $(".control_at_footer").each(function(){
            k++;
            $(this).attr("tabindex",
                k)
            });
        $(".datefield,.datetimefield").each(function(){
            PMA_addDatepicker($(this))
            })
        }else if(a>c)for(;a>c;){
        $("input[id^=insert_ignore]:last").nextUntil("fieldset").andSelf().remove();
        a--
    }
    })
},"top.frame_content");
