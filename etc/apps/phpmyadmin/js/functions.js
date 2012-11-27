var sql_box_locked=false,only_once_elements=[],ajax_message_init=false;
function PMA_prepareForAjaxRequest(a){
    a.find("input:hidden").is("#ajax_request_hidden")||a.append('<input type="hidden" id="ajax_request_hidden" name="ajax_request" value="true" />')
    }
function suggestPassword(a){
    var b=a.generated_pw;
    b.value="";
    for(i=0;i<16;i++)b.value+="abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ".charAt(Math.floor(Math.random()*53));
    a.text_pma_pw.value=b.value;
    a.text_pma_pw2.value=b.value;
    return true
    }
function parseVersionString(a){
    if(typeof a!="string")return false;
    var b=0,c=a.split("-");
    if(c.length>=2)if(c[1].substr(0,2)=="rc")b=-20-parseInt(c[1].substr(2));
        else if(c[1].substr(0,4)=="beta")b=-40-parseInt(c[1].substr(4));
        else if(c[1].substr(0,5)=="alpha")b=-60-parseInt(c[1].substr(5));
        else if(c[1].substr(0,3)=="dev")b=0;
    var d=a.split(".");
    a=parseInt(d[0])||0;
    c=parseInt(d[1])||0;
    var e=parseInt(d[2])||0;
    d=parseInt(d[3])||0;
    return a*1E8+c*1E6+e*1E4+d*100+b
    }
function PMA_current_version(){
    var a=parseVersionString(pmaversion),b=parseVersionString(PMA_latest_version);
    $("#li_pma_version").append(PMA_messages.strLatestAvailable+" "+PMA_latest_version);
    if(b>a){
        var c=$.sprintf(PMA_messages.strNewerVersion,PMA_latest_version,PMA_latest_date);
        klass=Math.floor(b/1E4)==Math.floor(a/1E4)?"error":"notice";
        $("#maincontainer").after('<div class="'+klass+'">'+c+"</div>")
        }
    }
function displayPasswordGenerateButton(){
    $("#tr_element_before_generate_password").parent().append("<tr><td>"+PMA_messages.strGeneratePassword+'</td><td><input type="button" id="button_generate_password" value="'+PMA_messages.strGenerate+'" onclick="suggestPassword(this.form)" /><input type="text" name="generated_pw" id="generated_pw" /></td></tr>');
    $("#div_element_before_generate_password").parent().append('<div class="item"><label for="button_generate_password">'+PMA_messages.strGeneratePassword+
        ':</label><span class="options"><input type="button" id="button_generate_password" value="'+PMA_messages.strGenerate+'" onclick="suggestPassword(this.form)" /></span><input type="text" name="generated_pw" id="generated_pw" /></div>')
    }
function PMA_addDatepicker(a){
    var b=false;
    if(a.is(".datetimefield"))b=true;
    a.datepicker({
        showOn:"button",
        buttonImage:themeCalendarImage,
        buttonImageOnly:true,
        duration:"",
        time24h:true,
        stepMinutes:1,
        stepHours:1,
        showTime:b,
        dateFormat:"yy-mm-dd",
        altTimeField:"",
        beforeShow:function(){
            a.data("comes_from","datepicker")
            },
        constrainInput:false
    })
    }
    function selectContent(a,b,c){
    if(!(c&&only_once_elements[a.name])){
        only_once_elements[a.name]=true;
        b||a.select()
        }
    }
function confirmLink(a,b){
    if(PMA_messages.strDoYouReally==""||typeof window.opera!="undefined")return true;
    var c=confirm(PMA_messages.strDoYouReally+" :\n"+b);
    if(c)if(typeof a.href!="undefined")a.href+="&is_js_confirmed=1";
        else if(typeof a.form!="undefined")a.form.action+="?is_js_confirmed=1";
    return c
    }
    function confirmAction(a){
    if(typeof window.opera!="undefined")return true;
    return confirm(a)
    }
function confirmQuery(a,b){
    if(PMA_messages.strDoYouReally=="")return true;
    else if(typeof b.value.replace=="undefined")return true;
    else{
        if(PMA_messages.strNoDropDatabases!="")if(/(^|;)\s*DROP\s+(IF EXISTS\s+)?DATABASE\s/i.test(b.value)){
            alert(PMA_messages.strNoDropDatabases);
            a.reset();
            b.focus();
            return false
            }
            var c=/^\s*ALTER\s+TABLE\s+((`[^`]+`)|([A-Za-z0-9_$]+))\s+DROP\s/i,d=/^\s*DELETE\s+FROM\s/i,e=/^\s*TRUNCATE\s/i;
        if(/^\s*DROP\s+(IF EXISTS\s+)?(TABLE|DATABASE|PROCEDURE)\s/i.test(b.value)||c.test(b.value)||
            d.test(b.value)||e.test(b.value)){
            c=b.value.length>100?b.value.substr(0,100)+"\n    ...":b.value;
            if(confirm(PMA_messages.strDoYouReally+" :\n"+c)){
                a.elements.is_js_confirmed.value=1;
                return true
                }else{
                window.focus();
                b.focus();
                return false
                }
            }
    }
return true
}
function confirmDisableRepository(){
    if(PMA_messages.strDoYouReally==""||typeof window.opera!="undefined")return true;
    return confirm(PMA_messages.strBLOBRepositoryDisableStrongWarning+"\n"+PMA_messages.strBLOBRepositoryDisableAreYouSure)
    }
function checkSqlQuery(a){
    var b=a.elements.sql_query,c=1;
    if(typeof b.value.replace=="undefined"){
        if((c=b.value==""?1:0)&&typeof a.elements.sql_file!="undefined")c=a.elements.sql_file.value==""?1:0;
        if(c&&typeof a.elements.sql_localfile!="undefined")c=a.elements.sql_localfile.value==""?1:0;
        if(c&&typeof a.elements.id_bookmark!="undefined")c=a.elements.id_bookmark.value==null||a.elements.id_bookmark.value==""
            }else{
        var d=/\s+/;
        if(typeof a.elements.sql_file!="undefined"&&a.elements.sql_file.value.replace(d,
            "")!="")return true;
        if(typeof a.elements.sql_localfile!="undefined"&&a.elements.sql_localfile.value.replace(d,"")!="")return true;
        if(c&&typeof a.elements.id_bookmark!="undefined"&&(a.elements.id_bookmark.value!=null||a.elements.id_bookmark.value!="")&&a.elements.id_bookmark.selectedIndex!=0)return true;
        if(b.value.replace(d,"")!="")return confirmQuery(a,b)?true:false;
        a.reset();
        c=1
        }
        if(c){
        b.select();
        alert(PMA_messages.strFormEmpty);
        b.focus();
        return false
        }
        return true
    }
function emptyCheckTheField(a,b){
    var c=1;
    c=a.elements[b];
    return c=typeof c.value.replace!="undefined"?c.value.replace(/\s+/,"")==""?1:0:c.value==""?1:0
    }
    function emptyFormElements(a,b){
    return emptyCheckTheField(a,b)
    }
function checkFormElementInRange(a,b,c,d,e){
    a=a.elements[b];
    b=parseInt(a.value);
    if(typeof d=="undefined")d=0;
    if(typeof e=="undefined")e=Number.MAX_VALUE;
    if(isNaN(b)){
        a.select();
        alert(PMA_messages.strNotNumber);
        a.focus();
        return false
        }else if(b<d||b>e){
        a.select();
        alert(c.replace("%d",b));
        a.focus();
        return false
        }else a.value=b;
    return true
    }
function checkTableEditForm(a,b){
    var c=0,d,e,f,j;
    for(d=0;d<b;d++){
        e="#field_"+d+"_2";
        e=$(e);
        j=e.val();
        if(j=="VARCHAR"||j=="CHAR"||j=="BIT"||j=="VARBINARY"||j=="BINARY"){
            e=$("#field_"+d+"_3");
            j=parseInt(e.val());
            f=$("#field_"+d+"_1");
            if(isNaN(j)&&f.val()!=""){
                e.select();
                alert(PMA_messages.strNotNumber);
                e.focus();
                return false
                }
            }
        if(c==0){
        e="field_"+d+"_1";
        emptyCheckTheField(a,e)||(c=1)
        }
    }
    if(c==0){
    c=a.elements.field_0_1;
    alert(PMA_messages.strFormEmpty);
    c.focus();
    return false
    }
    if($("input.textfield[name='table']").val()==
    ""){
    alert(PMA_messages.strFormEmpty);
    $("input.textfield[name='table']").focus();
    return false
    }
    return true
}
function checkTransmitDump(a,b){
    var c=a.elements;
    if(b=="zip"&&c.zip.checked){
        if(!c.asfile.checked)a.elements.asfile.checked=true;
        if(typeof c.gzip!="undefined"&&c.gzip.checked)a.elements.gzip.checked=false;
        if(typeof c.bzip!="undefined"&&c.bzip.checked)a.elements.bzip.checked=false
            }else if(b=="gzip"&&c.gzip.checked){
        if(!c.asfile.checked)a.elements.asfile.checked=true;
        if(typeof c.zip!="undefined"&&c.zip.checked)a.elements.zip.checked=false;
        if(typeof c.bzip!="undefined"&&c.bzip.checked)a.elements.bzip.checked=
            false
            }else if(b=="bzip"&&c.bzip.checked){
        if(!c.asfile.checked)a.elements.asfile.checked=true;
        if(typeof c.zip!="undefined"&&c.zip.checked)a.elements.zip.checked=false;
        if(typeof c.gzip!="undefined"&&c.gzip.checked)a.elements.gzip.checked=false
            }else if(b=="transmit"&&!c.asfile.checked){
        if(typeof c.zip!="undefined"&&c.zip.checked)a.elements.zip.checked=false;
        if(typeof c.gzip!="undefined"&&c.gzip.checked)a.elements.gzip.checked=false;
        if(typeof c.bzip!="undefined"&&c.bzip.checked)a.elements.bzip.checked=
            false
            }
            return true
    }
    $(document).ready(function(){
    $("tr.odd:not(.noclick), tr.even:not(.noclick)").live("click",function(a){
        if(!$(a.target).is("a, img, a *")){
            var b=$(this),c=b.find(":checkbox");
            if(c.length){
                var d=c.attr("checked");
                if(!$(a.target).is(":checkbox, label")){
                    d=!d;
                    c.attr("checked",d)
                    }
                    d?b.addClass("marked"):b.removeClass("marked")
                }else b.toggleClass("marked")
                }
            });
$(".datefield, .datetimefield").each(function(){
    PMA_addDatepicker($(this))
    })
});
$(document).ready(function(){
    $("tr.odd, tr.even").live("hover",function(){
        var a=$(this);
        a.toggleClass("hover");
        a.children().toggleClass("hover")
        })
    });
var marked_row=[];
function markAllRows(a){
    $("#"+a).find("input:checkbox:enabled").attr("checked","checked").parents("tr").addClass("marked");
    return true
    }
    function unMarkAllRows(a){
    $("#"+a).find("input:checkbox:enabled").removeAttr("checked").parents("tr").removeClass("marked");
    return true
    }
function setCheckboxes(a,b){
    b?$("#"+a).find("input:checkbox").attr("checked","checked"):$("#"+a).find("input:checkbox").removeAttr("checked");
    return true
    }
    function setSelectOptions(a,b,c){
    $("form[name='"+a+"'] select[name='"+b+"']").find("option").attr("selected",c);
    return true
    }
function insertQuery(a){
    var b=document.sqlform.dummy,c="",d=document.sqlform.table.value;
    if(b.options.length>0){
        sql_box_locked=true;
        for(var e="",f="",j="",g=0,h=0;h<b.options.length;h++){
            g++;
            if(g>1){
                e+=", ";
                f+=",";
                j+=","
                }
                e+=b.options[h].value;
            f+="[value-"+g+"]";
            j+=b.options[h].value+"=[value-"+g+"]"
            }
            if(a=="selectall")c="SELECT * FROM `"+d+"` WHERE 1";
        else if(a=="select")c="SELECT "+e+" FROM `"+d+"` WHERE 1";
        else if(a=="insert")c="INSERT INTO `"+d+"`("+e+") VALUES ("+f+")";
        else if(a=="update")c="UPDATE `"+
            d+"` SET "+j+" WHERE 1";
        else if(a=="delete")c="DELETE FROM `"+d+"` WHERE 1";
        document.sqlform.sql_query.value=c;
        sql_box_locked=false
        }
    }
function insertValueQuery(){
    var a=document.sqlform.sql_query,b=document.sqlform.dummy;
    if(b.options.length>0){
        sql_box_locked=true;
        for(var c="",d=0,e=0;e<b.options.length;e++)if(b.options[e].selected){
            d++;
            if(d>1)c+=", ";
            c+=b.options[e].value
            }
            if(document.selection){
            a.focus();
            sel=document.selection.createRange();
            sel.text=c;
            document.sqlform.insert.focus()
            }else if(document.sqlform.sql_query.selectionStart||document.sqlform.sql_query.selectionStart=="0"){
            b=document.sqlform.sql_query.selectionEnd;
            d=document.sqlform.sql_query.value;
            a.value=d.substring(0,document.sqlform.sql_query.selectionStart)+c+d.substring(b,d.length)
            }else a.value+=c;
        sql_box_locked=false
        }
    }
function goToUrl(a,b){
    eval("document.location.href = '"+b+"pos="+a.options[a.selectedIndex].value+"'")
    }
    function getElement(a,b){
    if(document.layers){
        b=b?b:self;
        if(b.document.layers[a])return b.document.layers[a];
        for(W=0;W<b.document.layers.length;)return getElement(a,b.document.layers[W])
            }
            if(document.all)return document.all[a];
    return document.getElementById(a)
    }
function refreshDragOption(a){
    if($("#"+a).css("visibility")=="visible"){
        refreshLayout();
        TableDragInit()
        }
    }
function refreshLayout(){
    var a=$("#pdflayout"),b=$("#orientation_opt").val(),c=$("#paper_opt").length==1?$("#paper_opt").val():"A4";
    if(b=="P"){
        posa="x";
        posb="y"
        }else{
        posa="y";
        posb="x"
        }
        a.css("width",pdfPaperSize(c,posa)+"px");
    a.css("height",pdfPaperSize(c,posb)+"px")
    }
function ToggleDragDrop(a){
    a=$("#"+a);
    if(a.css("visibility")=="hidden"){
        PDFinit();
        a.css("visibility","visible");
        a.css("display","block");
        $("#showwysiwyg").val("1")
        }else{
        a.css("visibility","hidden");
        a.css("display","none");
        $("#showwysiwyg").val("0")
        }
    }
function dragPlace(a,b,c){
    a=$("#table_"+a);
    b=="x"?a.css("left",c+"px"):a.css("top",c+"px")
    }
function pdfPaperSize(a,b){
    switch(a.toUpperCase()){
        case "4A0":
            return b=="x"?4767.87:6740.79;
        case "2A0":
            return b=="x"?3370.39:4767.87;
        case "A0":
            return b=="x"?2383.94:3370.39;
        case "A1":
            return b=="x"?1683.78:2383.94;
        case "A2":
            return b=="x"?1190.55:1683.78;
        case "A3":
            return b=="x"?841.89:1190.55;
        case "A4":
            return b=="x"?595.28:841.89;
        case "A5":
            return b=="x"?419.53:595.28;
        case "A6":
            return b=="x"?297.64:419.53;
        case "A7":
            return b=="x"?209.76:297.64;
        case "A8":
            return b=="x"?147.4:209.76;
        case "A9":
            return b==
            "x"?104.88:147.4;
        case "A10":
            return b=="x"?73.7:104.88;
        case "B0":
            return b=="x"?2834.65:4008.19;
        case "B1":
            return b=="x"?2004.09:2834.65;
        case "B2":
            return b=="x"?1417.32:2004.09;
        case "B3":
            return b=="x"?1000.63:1417.32;
        case "B4":
            return b=="x"?708.66:1000.63;
        case "B5":
            return b=="x"?498.9:708.66;
        case "B6":
            return b=="x"?354.33:498.9;
        case "B7":
            return b=="x"?249.45:354.33;
        case "B8":
            return b=="x"?175.75:249.45;
        case "B9":
            return b=="x"?124.72:175.75;
        case "B10":
            return b=="x"?87.87:124.72;
        case "C0":
            return b=="x"?
            2599.37:3676.54;
        case "C1":
            return b=="x"?1836.85:2599.37;
        case "C2":
            return b=="x"?1298.27:1836.85;
        case "C3":
            return b=="x"?918.43:1298.27;
        case "C4":
            return b=="x"?649.13:918.43;
        case "C5":
            return b=="x"?459.21:649.13;
        case "C6":
            return b=="x"?323.15:459.21;
        case "C7":
            return b=="x"?229.61:323.15;
        case "C8":
            return b=="x"?161.57:229.61;
        case "C9":
            return b=="x"?113.39:161.57;
        case "C10":
            return b=="x"?79.37:113.39;
        case "RA0":
            return b=="x"?2437.8:3458.27;
        case "RA1":
            return b=="x"?1729.13:2437.8;
        case "RA2":
            return b==
            "x"?1218.9:1729.13;
        case "RA3":
            return b=="x"?864.57:1218.9;
        case "RA4":
            return b=="x"?609.45:864.57;
        case "SRA0":
            return b=="x"?2551.18:3628.35;
        case "SRA1":
            return b=="x"?1814.17:2551.18;
        case "SRA2":
            return b=="x"?1275.59:1814.17;
        case "SRA3":
            return b=="x"?907.09:1275.59;
        case "SRA4":
            return b=="x"?637.8:907.09;
        case "LETTER":
            return b=="x"?612:792;
        case "LEGAL":
            return b=="x"?612:1008;
        case "EXECUTIVE":
            return b=="x"?521.86:756;
        case "FOLIO":
            return b=="x"?612:936
            }
            return 0
    }
function popupBSMedia(a,b,c,d,e,f){
    if(e==undefined)e=640;
    if(f==undefined)f=480;
    window.open("bs_play_media.php?"+a+"&bs_reference="+b+"&media_type="+c+"&custom_type="+d,"viewBSMedia","width="+e+", height="+f+", resizable=1, scrollbars=1, status=0")
    }
    function requestMIMETypeChange(a,b,c,d){
    if(undefined==d)d="";
    var e=prompt("Enter custom MIME type",d);
    e&&e!=d&&changeMIMEType(a,b,c,e)
    }
function changeMIMEType(a,b,c,d){
    jQuery.post("bs_change_mime_type.php",{
        bs_db:a,
        bs_table:b,
        bs_reference:c,
        bs_new_mime_type:d
    })
    }
$(document).ready(function(){
    $(".inline_edit_sql").live("click",function(){
        var a=$(this).prev().find("input[name='server']").val(),b=$(this).prev().find("input[name='db']").val(),c=$(this).prev().find("input[name='table']").val(),d=$(this).prev().find("input[name='token']").val(),e=$(this).prev().find("input[name='sql_query']").val(),f=$(this).parent().prev().find(".inner_sql"),j=f.html(),g='<textarea name="sql_query_edit" id="sql_query_edit">'+e+"</textarea>\n";
        g+='<input type="button" class="btnSave" value="'+
        PMA_messages.strGo+'">\n';
        g+='<input type="button" class="btnDiscard" value="'+PMA_messages.strCancel+'">\n';
        f.replaceWith(g);
        $(".btnSave").each(function(){
            $(this).click(function(){
                e=$(this).prev().val();
                window.location.replace("import.php?server="+encodeURIComponent(a)+"&db="+encodeURIComponent(b)+"&table="+encodeURIComponent(c)+"&sql_query="+encodeURIComponent(e)+"&show_query=1&token="+d)
                })
            });
        $(".btnDiscard").each(function(){
            $(this).click(function(){
                $(this).closest(".sql").html('<span class="syntax"><span class="inner_sql">'+
                    j+"</span></span>")
                })
            });
        return false
        });
    $(".sqlbutton").click(function(a){
        a.target.id=="clear"?$("#sqlquery").val(""):insertQuery(a.target.id);
        return false
        });
    $("#export_type").change(function(){
        if($("#export_type").val()=="svg"){
            $("#show_grid_opt").attr("disabled","disabled");
            $("#orientation_opt").attr("disabled","disabled");
            $("#with_doc").attr("disabled","disabled");
            $("#show_table_dim_opt").removeAttr("disabled");
            $("#all_table_same_wide").removeAttr("disabled");
            $("#paper_opt").removeAttr("disabled",
                "disabled");
            $("#show_color_opt").removeAttr("disabled","disabled")
            }else if($("#export_type").val()=="dia"){
            $("#show_grid_opt").attr("disabled","disabled");
            $("#with_doc").attr("disabled","disabled");
            $("#show_table_dim_opt").attr("disabled","disabled");
            $("#all_table_same_wide").attr("disabled","disabled");
            $("#paper_opt").removeAttr("disabled","disabled");
            $("#show_color_opt").removeAttr("disabled","disabled");
            $("#orientation_opt").removeAttr("disabled","disabled")
            }else if($("#export_type").val()=="eps"){
            $("#show_grid_opt").attr("disabled",
                "disabled");
            $("#orientation_opt").removeAttr("disabled");
            $("#with_doc").attr("disabled","disabled");
            $("#show_table_dim_opt").attr("disabled","disabled");
            $("#all_table_same_wide").attr("disabled","disabled");
            $("#paper_opt").attr("disabled","disabled");
            $("#show_color_opt").attr("disabled","disabled")
            }else if($("#export_type").val()=="pdf"){
            $("#show_grid_opt").removeAttr("disabled");
            $("#orientation_opt").removeAttr("disabled");
            $("#with_doc").removeAttr("disabled","disabled");
            $("#show_table_dim_opt").removeAttr("disabled",
                "disabled");
            $("#all_table_same_wide").removeAttr("disabled","disabled");
            $("#paper_opt").removeAttr("disabled","disabled");
            $("#show_color_opt").removeAttr("disabled","disabled")
            }
        });
$("#sqlquery").focus();
    if($("#input_username"))$("#input_username").val()==""?$("#input_username").focus():$("#input_password").focus()
    });
function PMA_ajaxShowMessage(a,b){
    if(a=="")return true;
    var c=a?a:PMA_messages.strLoading,d=b?b:5E3;
    if(ajax_message_init)$("#loading").stop(true,true).html(c).fadeIn("medium").delay(d).fadeOut("medium",function(){
        $(this).html("").hide()
        });
    else{
        $(function(){
            $('<div id="loading_parent"></div>').insertBefore("#serverinfo");
            $('<span id="loading" class="ajax_notification"></span>').appendTo("#loading_parent").html(c).fadeIn("medium").delay(d).fadeOut("medium",function(){
                $(this).html("").hide()
                })
            },"top.frame_content");
        ajax_message_init=true
        }
        return $("#loading")
    }
    function PMA_ajaxRemoveMessage(a){
    a.stop(true,true).fadeOut("medium",function(){
        a.hide()
        })
    }
    function PMA_showNoticeForEnum(a){
    var b=a.attr("id").split("_")[1];
    b+="_"+(parseInt(a.attr("id").split("_")[2])+1);
    a=a.attr("value");
    a=="ENUM"||a=="SET"?$("p[id='enum_notice_"+b+"']").show():$("p[id='enum_notice_"+b+"']").hide()
    }
jQuery.fn.PMA_confirm=function(a,b,c){
    if(PMA_messages.strDoYouReally=="")return true;
    var d={};
    
    d[PMA_messages.strOK]=function(){
        $(this).dialog("close").remove();
        $.isFunction(c)&&c.call(this,b)
        };
        
    d[PMA_messages.strCancel]=function(){
        $(this).dialog("close").remove()
        };
        
    $('<div id="confirm_dialog"></div>').prepend(a).dialog({
        buttons:d
    })
    };
jQuery.fn.PMA_sort_table=function(a){
    return this.each(function(){
        var b=$(this),c=$(this).find("tr").get();
        $.each(c,function(d,e){
            e.sortKey=$.trim($(e).find(a).text().toLowerCase())
            });
        c.sort(function(d,e){
            if(d.sortKey<e.sortKey)return-1;
            if(d.sortKey>e.sortKey)return 1;
            return 0
            });
        $.each(c,function(d,e){
            $(b).append(e);
            e.sortKey=null
            });
        $(this).find("tr:odd").removeClass("even").addClass("odd").end().find("tr:even").removeClass("odd").addClass("even")
        })
    };
$(document).ready(function(){
    $("#create_table_form_minimal.ajax").live("submit",function(a){
        a.preventDefault();
        $form=$(this);
        var b={};
        
        b[PMA_messages.strCancel]=function(){
            $(this).dialog("close").remove()
            };
            
        var c={};
        
        c[PMA_messages.strOK]=function(){
            $(this).dialog("close").remove()
            };
            
        var d=PMA_ajaxShowMessage();
        PMA_prepareForAjaxRequest($form);
        $.get($form.attr("action"),$form.serialize(),function(e){
            e.success!=undefined&&e.success==false?$('<div id="create_table_dialog"></div>').append(e.error).dialog({
                title:PMA_messages.strCreateTable,
                height:230,
                width:900,
                open:PMA_verifyTypeOfAllColumns,
                buttons:c
            }).find("fieldset").remove():$('<div id="create_table_dialog"></div>').append(e).dialog({
                title:PMA_messages.strCreateTable,
                height:600,
                width:900,
                open:PMA_verifyTypeOfAllColumns,
                buttons:b
            });
            PMA_ajaxRemoveMessage(d)
            });
        $form.find("input[name=table],input[name=num_fields]").val("")
        });
    $("#create_table_form input[name=do_save_data]").live("click",function(a){
        a.preventDefault();
        a=$("#create_table_form");
        if(checkTableEditForm(a[0],a.find("input[name=orig_num_fields]").val()))if(a.hasClass("ajax")){
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            PMA_prepareForAjaxRequest(a);
            $.post(a.attr("action"),a.serialize()+"&do_save_data="+$(this).val(),function(b){
                if(b.success==true){
                    $("#properties_message").removeClass("error").html("");
                    PMA_ajaxShowMessage(b.message);
                    $("#create_table_dialog").length>0&&$("#create_table_dialog").dialog("close").remove();
                    var c=$("#tablesForm").find("tbody").not("#tbl_summary_row");
                    if(c.length==0)window.parent&&window.parent.frame_content&&window.parent.frame_content.location.reload();
                    else{
                        var d=$(c).find("tr:last");
                        d=$(d).find("input:checkbox").attr("id").match(/\d+/)[0];
                        d="checkbox_tbl_"+(parseFloat(d)+1);
                        b.new_table_string=b.new_table_string.replace(/checkbox_tbl_/,d);
                        $(b.new_table_string).appendTo(c);
                        $(c).PMA_sort_table("th")
                        }
                        window.parent&&window.parent.frame_navigation&&window.parent.frame_navigation.location.reload()
                    }else{
                    $("#properties_message").addClass("error").html(b.error);
                    $("#properties_message")[0].scrollIntoView()
                    }
                })
        }else{
            a.append('<input type="hidden" name="do_save_data" value="save" />');
            a.submit()
            }
        });
$("#create_table_form.ajax input[name=submit_num_fields]").live("click",function(a){
    a.preventDefault();
    a=$("#create_table_form");
    var b=PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
    PMA_prepareForAjaxRequest(a);
    $.post(a.attr("action"),a.serialize()+"&submit_num_fields="+$(this).val(),function(c){
        $("#create_table_dialog").length>0&&$("#create_table_dialog").html(c);
        $("#create_table_div").length>0&&$("#create_table_div").html(c);
        PMA_verifyTypeOfAllColumns();
        PMA_ajaxRemoveMessage(b)
        })
    })
},"top.frame_content");
$(document).ready(function(){
    $(".drop_trigger_anchor").live("click",function(a){
        a.preventDefault();
        $anchor=$(this);
        var b=$anchor.parents("tr");
        a="DROP TRIGGER IF EXISTS `"+b.children("td:first").text()+"`";
        $anchor.PMA_confirm(a,$anchor.attr("href"),function(c){
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            $.get(c,{
                is_js_confirmed:1,
                ajax_request:true
            },function(d){
                if(d.success==true){
                    PMA_ajaxShowMessage(d.message);
                    $("#topmenucontainer").next("div").remove().end().after(d.sql_query);
                    b.hide("medium").remove()
                    }else PMA_ajaxShowMessage(d.error)
                    })
            })
        })
    },
"top.frame_content");
$(document).ready(function(){
    $("#drop_db_anchor").live("click",function(a){
        a.preventDefault();
        a=PMA_messages.strDropDatabaseStrongWarning+"\n"+PMA_messages.strDoYouReally+" :\nDROP DATABASE "+escapeHtml(window.parent.db);
        $(this).PMA_confirm(a,$(this).attr("href"),function(b){
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            $.get(b,{
                is_js_confirmed:"1",
                ajax_request:true
            },function(){
                window.parent.refreshNavigation();
                window.parent.refreshMain()
                })
            })
        })
    });
$(document).ready(function(){
    $("#create_database_form.ajax").live("submit",function(a){
        a.preventDefault();
        $form=$(this);
        PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
        PMA_prepareForAjaxRequest($form);
        $.post($form.attr("action"),$form.serialize(),function(b){
            if(b.success==true){
                PMA_ajaxShowMessage(b.message);
                $("#tabledatabases").find("tbody").append(b.new_db_string).PMA_sort_table(".name").find("#db_summary_row").appendTo("#tabledatabases tbody").removeClass("odd even");
                b=$("#databases_count");
                var c=parseInt(b.text());
                b.text(++c);
                window.parent&&window.parent.frame_navigation&&window.parent.frame_navigation.location.reload()
                }else PMA_ajaxShowMessage(b.error)
                })
        })
    });
$(document).ready(function(){
    $("#change_password_anchor.dialog_active").live("click",function(a){
        a.preventDefault();
        return false
        });
    $("#change_password_anchor.ajax").live("click",function(a){
        a.preventDefault();
        $(this).removeClass("ajax").addClass("dialog_active");
        var b={};
        
        b[PMA_messages.strCancel]=function(){
            $(this).dialog("close").remove()
            };
            
        $.get($(this).attr("href"),{
            ajax_request:true
        },function(c){
            $('<div id="change_password_dialog"></div>').dialog({
                title:PMA_messages.strChangePassword,
                width:600,
                close:function(){
                    $(this).remove()
                    },
                buttons:b,
                beforeClose:function(){
                    $("#change_password_anchor.dialog_active").removeClass("dialog_active").addClass("ajax")
                    }
                }).append(c);
            displayPasswordGenerateButton()
            })
    });
$("#change_password_form.ajax").find("input[name=change_pw]").live("click",function(a){
    a.preventDefault();
    a=$("#change_password_form");
    var b=$(this).val(),c=PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
    $(a).append('<input type="hidden" name="ajax_request" value="true" />');
    $.post($(a).attr("action"),
        $(a).serialize()+"&change_pw="+b,function(d){
            if(d.success==true){
                $("#topmenucontainer").after(d.sql_query);
                $("#change_password_dialog").hide().remove();
                $("#edit_user_dialog").dialog("close").remove();
                $("#change_password_anchor.dialog_active").removeClass("dialog_active").addClass("ajax");
                PMA_ajaxRemoveMessage(c)
                }else PMA_ajaxShowMessage(d.error)
                })
    })
});
$(document).ready(function(){
    PMA_verifyTypeOfAllColumns();
    $("select[class='column_type']").live("change",function(){
        PMA_showNoticeForEnum($(this))
        })
    });
function PMA_verifyTypeOfAllColumns(){
    $("select[class='column_type']").each(function(){
        PMA_showNoticeForEnum($(this))
        })
    }
    function disable_popup(){
    $("#popup_background").fadeOut("fast");
    $("#enum_editor").fadeOut("fast");
    $("#enum_editor #values input").remove();
    $("#enum_editor input[type='hidden']").remove()
    }
$(document).ready(function(){
    $("a[class='open_enum_editor']").live("click",function(){
        var c=document.documentElement.clientWidth,d=document.documentElement.clientHeight,e=c/2,f=d*0.8;
        d=d/2-f/2;
        c=c/2-e/2;
        $("#enum_editor").css({
            position:"absolute",
            top:d,
            left:c,
            width:e,
            height:f
        });
        $("#popup_background").css({
            opacity:"0.7"
        });
        $("#popup_background").fadeIn("fast");
        $("#enum_editor").fadeIn("fast");
        e=$(this).parent().prev("input").val();
        e=$("<div/>").text(e).html();
        f=[];
        c=false;
        for(var j,g="",h=0;h<e.length;h++){
            d=
            e.charAt(h);
            j=h==e.length?"":e.charAt(h+1);
            if(!c&&d=="'")c=true;
            else if(c&&d=="\\"&&j=="\\"){
                g+="&#92;";
                h++
            }else if(c&&j=="'"&&(d=="'"||d=="\\")){
                g+="&#39;";
                h++
            }else if(c&&d=="'"){
                c=false;
                f.push(g);
                g=""
                }else if(c)g+=d
                }
                g.length>0&&f.push(g);
        for(h=0;h<f.length;h++)$("#enum_editor #values").append("<input type='text' value='"+f[h]+"' />");
        $("#enum_editor").append("<input type='hidden' value='"+$(this).parent().prev("input").attr("id")+"' />");
        return false
        });
    $("a[class='close_enum_editor']").live("click",
        function(){
            disable_popup()
            });
    $("a[class='cancel_enum_editor']").live("click",function(){
        disable_popup()
        });
    $("a[class='add_value']").live("click",function(){
        $("#enum_editor #values").append("<input type='text' />")
        });
    $("#enum_editor input[type='submit']").live("click",function(){
        var c=[];
        $.each($("#enum_editor #values input"),function(e,f){
            val=jQuery.trim(f.value);
            val!=""&&c.push("'"+val.replace(/\\/g,"\\\\").replace(/'/g,"''")+"'")
            });
        var d=$("#enum_editor input[type='hidden']").attr("value");
        $("input[id='"+
            d+"']").attr("value",c.join(","));
        disable_popup()
        });
    if($("input[type='hidden'][name='table_type']").val()=="table"){
        var a=$("table[id='tablestructure']");
        a.find("td[class='browse']").remove();
        a.find("td[class='primary']").remove();
        a.find("td[class='unique']").remove();
        a.find("td[class='index']").remove();
        a.find("td[class='fulltext']").remove();
        a.find("th[class='action']").attr("colspan",3);
        a.find("td[class='more_opts']").show();
        $(".structure_actions_dropdown").each(function(){
            var c=$(this),d=c.parent().offset().left+
            c.parent().innerWidth()-c.innerWidth(),e=c.parent().offset().top+c.parent().innerHeight();
            c.offset({
                top:e,
                left:d
            })
            });
        var b=$("select[name='after_field']");
        $("iframe[class='IE_hack']").width(b.width()).height(b.height()).offset({
            top:b.offset().top,
            left:b.offset().left
            });
        a.find("td[class='more_opts']").mouseenter(function(){
            $.browser.msie&&$.browser.version=="6.0"&&$("iframe[class='IE_hack']").show().width(b.width()+4).height(b.height()+4).offset({
                top:b.offset().top,
                left:b.offset().left
                });
            $(".structure_actions_dropdown").hide();
            $(this).children(".structure_actions_dropdown").show();
            if($.browser.msie){
                var c=$(this).offset().left+$(this).innerWidth()-$(this).children(".structure_actions_dropdown").innerWidth(),d=$(this).offset().top+$(this).innerHeight();
                $(this).children(".structure_actions_dropdown").offset({
                    top:d,
                    left:c
                })
                }
            }).mouseleave(function(){
        $(this).children(".structure_actions_dropdown").hide();
        $.browser.msie&&$.browser.version=="6.0"&&$("iframe[class='IE_hack']").hide()
        })
    }
});
$(document).ready(function(){
    $(".footnotes").hide();
    $(".footnotes span").each(function(){
        $(this).children("sup").remove()
        });
    $(".footnotes").css("border","none");
    $(".footnotes").css("padding","0px");
    $("sup[class='footnotemarker']").hide();
    $("img[class='footnotemarker']").show();
    $("img[class='footnotemarker']").each(function(){
        var a=$(this).attr("id");
        a=a.split("_")[1];
        a=$(".footnotes span[id='footnote_"+a+"']").html();
        $(this).qtip({
            content:a,
            show:{
                delay:0
            },
            hide:{
                delay:1E3
            },
            style:{
                background:"#ffffcc"
            }
        })
    })
});
function menuResize(){
    var a=$("#topmenu"),b=a.innerWidth()-5,c=a.find(".submenu"),d=c.outerWidth(true),e=c.find("ul");
    a=a.find("> li");
    for(var f=e.find("li"),j=f.length>0,g=j?d:0,h=0,k=0;k<a.length-1;k++){
        var m=$(a[k]),l=m.outerWidth(true);
        m.data("width",l);
        g+=l;
        if(g>b){
            g-=l;
            if(g+d<b)h=k;
            else{
                h=k-1;
                g-=$(a[k-1]).data("width")
                }
                break
        }
    }
    if(h>0){
    for(k=h;k<a.length-1;k++)$(a[k])[j?"prependTo":"appendTo"](e);
    c.addClass("shown")
    }else if(j){
    g-=d;
    for(k=0;k<f.length;k++){
        g+=$(f[k]).data("width");
        if(g+d<b||k==
            f.length-1&&g<b){
            $(f[k]).insertBefore(c);
            k==f.length-1&&c.removeClass("shown")
            }else break
    }
    }
    c.find(".tabactive").length?c.addClass("active").find("> a").removeClass("tab").addClass("tabactive"):c.removeClass("active").find("> a").addClass("tab").removeClass("tabactive")
}
$(function(){
    var a=$("#topmenu");
    if(a.length!=0){
        var b=$("<a />",{
            href:"#",
            "class":"tab"
        }).text(PMA_messages.strMore).click(function(d){
            d.preventDefault()
            }),c=a.find("li:first-child img");
        c.length&&c.clone().attr("src",c.attr("src").replace(/\/[^\/]+$/,"/b_more.png")).prependTo(b);
        b=$("<li />",{
            "class":"submenu"
        }).append(b).append($("<ul />")).mouseenter(function(){
            $(this).find("ul .tabactive").length==0&&$(this).addClass("submenuhover").find("> a").addClass("tabactive")
            }).mouseleave(function(){
            $(this).find("ul .tabactive").length==
            0&&$(this).removeClass("submenuhover").find("> a").removeClass("tabactive")
            });
        a.append(b);
        $(window).resize(menuResize);
        menuResize()
        }
    });
$(document).ready(function(){
    $(".multi_checkbox").live("click",function(a){
        var b=this.id,c=b.replace("_right","_left"),d=b.replace("_left","_right"),e="";
        e=b==c?d:c;
        var f=$("#"+b);
        b=$("#"+e);
        if(a.shiftKey){
            var j=$(".multi_checkbox").index(f);
            a=$(".multi_checkbox").filter(".last_clicked");
            var g=$(".multi_checkbox").index(a);
            $(".multi_checkbox").filter(function(h){
                return j>g&&h>g&&h<j||g>j&&h<g&&h>j
                }).each(function(){
                var h=$(this);
                f.is(":checked")?h.attr("checked",true):h.attr("checked",false)
                })
            }
            $(".multi_checkbox").removeClass("last_clicked");
        f.addClass("last_clicked");
        f.is(":checked")?b.attr("checked",true):b.attr("checked",false)
        })
    });
function PMA_getRowNumber(a){
    return parseInt(a.split(/row_/)[1])
    }
    function PMA_set_status_label(a){
    $("#"+a).css("display")=="none"?$("#anchor_status_"+a).text("+ "):$("#anchor_status_"+a).text("- ")
    }
function PMA_init_slider(){
    $(".pma_auto_slider").each(function(a,b){
        if(!$(b).hasClass("slider_init_done")){
            $(b).addClass("slider_init_done");
            $('<span id="anchor_status_'+b.id+'"></span>').insertBefore(b);
            PMA_set_status_label(b.id);
            $('<a href="#'+b.id+'" id="anchor_'+b.id+'">'+b.title+"</a>").insertBefore(b).click(function(){
                $("#"+b.id).toggle("clip",function(){
                    PMA_set_status_label(b.id)
                    });
                return false
                })
            }
        })
}
$(document).ready(function(){
    $(".vpointer").live("hover",function(){
        var a=$(this);
        a=PMA_getRowNumber(a.attr("class"));
        $(".vpointer").filter(".row_"+a).toggleClass("hover")
        })
    });
$(document).ready(function(){
    $(".vmarker").live("click",function(){
        var a=$(this);
        a=PMA_getRowNumber(a.attr("class"));
        $(".vmarker").filter(".row_"+a).toggleClass("marked")
        });
    $("#visual_builder_anchor").show();
    $("#tableslistcontainer").find("#pageselector").live("change",function(){
        $(this).parent("form").submit()
        });
    $("#navidbpageselector").find("#pageselector").live("change",function(){
        $(this).parent("form").submit()
        });
    $("#body_browse_foreigners").find("#pageselector").live("change",function(){
        $(this).closest("form").submit()
        });
    $(".jsversioncheck").length>0&&$.getScript("http://www.phpmyadmin.net/home_page/version.js",PMA_current_version);
    PMA_init_slider();
    $(".clickprevimage").css("color",function(){
        return $("a").css("color")
        }).css("cursor",function(){
        return $("a").css("cursor")
        }).live("click",function(){
        $this_span=$(this);
        $this_span.closest("td").is(".inline_edit_anchor")||$this_span.parent().find("input:image").click()
        })
    });
function escapeHtml(a){
    return a.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;")
    };
