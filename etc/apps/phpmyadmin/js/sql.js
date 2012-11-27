var $data_a;
function PMA_urldecode(d){
    return decodeURIComponent(d.replace(/\+/g,"%20"))
    }
    function PMA_urlencode(d){
    return encodeURIComponent(d.replace(/\%20/g,"+"))
    }
function getFieldName(d,b){
    if(b=="vertical"){
        var a=d.siblings("th").find("a").clone();
        if(a.length==0)a=d.siblings("th").clone()
            }else{
        var e=d.index();
        a=$("#table_results").find("thead").find("th:nth("+(e-4)+") a").clone();
        if(a.length==0)a=$("#table_results").find("thead").find("th:nth("+(e-4)+")").clone()
            }
            a.children().remove();
    return $.trim(a.text())
    }
function appendInlineAnchor(){
    if($("#top_direction_dropdown").val()=="vertical"){
        $("#table_results tr").find(".edit_row_anchor").removeClass("edit_row_anchor").parent().each(function(){
            var d=$(this),b=d.clone(),a=b.find("img:first").attr("title",PMA_messages.strInlineEdit);
            if(a.length!=0){
                var e=a.attr("src").replace(/b_edit/,"b_inline_edit");
                a.attr("src",e)
                }
                b.find("td").addClass("inline_edit_anchor").find("a").attr("href","#");
            e=b.find('span:contains("'+PMA_messages.strEdit+'")');
            var g=b.find("a").find("span");
            if(e.length>0){
                g.text(" "+PMA_messages.strInlineEdit);
                g.prepend(a)
                }else{
                g.text("");
                g.append(a)
                }
                b.insertAfter(d)
            });
        $("#rowsDeleteForm").find("tbody").find("th").each(function(){
            var d=$(this);
            d.attr("rowspan")==4&&d.attr("rowspan","5")
            })
        }else{
        $(".edit_row_anchor").each(function(){
            var d=$(this);
            d.removeClass("edit_row_anchor");
            var b=d.clone(),a=b.find("img").attr("title",PMA_messages.strInlineEdit);
            if(a.length!=0){
                var e=a.attr("src").replace(/b_edit/,"b_inline_edit");
                a.attr("src",e);
                b.find("a").attr("href",
                    "#");
                e=b.find('span:contains("'+PMA_messages.strEdit+'")');
                var g=b.find("a").find("span");
                if(e.length>0){
                    g.text(" "+PMA_messages.strInlineEdit);
                    g.prepend(a)
                    }else{
                    g.text("");
                    g.append(a)
                    }
                }else{
            b.find("a").attr("href","#");
            b.find("a span").text(PMA_messages.strInlineEdit);
            a=b.find("input:image").attr("title",PMA_messages.strInlineEdit);
            if(a.length>0){
                e=a.attr("src").replace(/b_edit/,"b_inline_edit");
                a.attr("src",e)
                }
                b.find(".clickprevimage").text(" "+PMA_messages.strInlineEdit)
            }
            b.addClass("inline_edit_anchor");
            d.after(b)
            });
    $("#rowsDeleteForm").find("thead, tbody").find("th").each(function(){
        var d=$(this);
        d.attr("colspan")==4&&d.attr("colspan","5")
        })
    }
}
$(document).ready(function(){
    $.ajaxSetup({
        cache:"false"
    });
    var d=$("#top_direction_dropdown").val();
    $("#top_direction_dropdown, #bottom_direction_dropdown").live("change",function(){
        d=$(this).val()
        });
    $("#sqlqueryresults").live("appendAnchor",function(){
        appendInlineAnchor()
        });
    $("#sqlqueryresults.ajax").trigger("appendAnchor");
    if(!$("#sqlqueryform").find("a").is("#togglequerybox")){
        $('<a id="togglequerybox"></a>').html(PMA_messages.strHideQueryBox).appendTo("#sqlqueryform").hide();
        $("#togglequerybox").bind("click",
            function(){
                var b=$(this);
                b.siblings().slideToggle("fast");
                if(b.text()==PMA_messages.strHideQueryBox){
                    b.text(PMA_messages.strShowQueryBox);
                    $("#togglequerybox_spacer").remove();
                    b.before('<br id="togglequerybox_spacer" />')
                    }else b.text(PMA_messages.strHideQueryBox);
                return false
                })
        }
        $("#sqlqueryform.ajax input:submit").live("click",function(b){
        b.preventDefault();
        var a=$(this).closest("form");
        "button_submit_query"==$(this).attr("id")&&a.find("select[name=id_bookmark]").attr("value","");
        if(!checkSqlQuery(a[0]))return false;
        $(".error").remove();
        var e=PMA_ajaxShowMessage();
        PMA_prepareForAjaxRequest(a);
        $.post(a.attr("action"),a.serialize(),function(g){
            if(g.success==true){
                $(".success").fadeOut();
                $(".sqlquery_message").fadeOut();
                if(typeof g.sql_query!="undefined"){
                    $('<div class="sqlquery_message"></div>').html(g.sql_query).insertBefore("#sqlqueryform");
                    $(".notice").remove()
                    }else $("#sqlqueryform").before(g.message);
                $("#sqlqueryresults").show();
                if(typeof g.reload!="undefined"){
                    $("#sqlqueryform.ajax").die("submit");
                    a.find("input[name=db]").val(g.db);
                    a.find("input[name=ajax_request]").remove();
                    a.append('<input type="hidden" name="reload" value="true" />');
                    $.post("db_sql.php",a.serialize(),function(m){
                        $("body").html(m)
                        })
                    }
                }else if(g.success==false){
            $("#sqlqueryform").before(g.error);
            $("#sqlqueryresults").hide()
            }else{
            $(".success").fadeOut();
            $(".sqlquery_message").fadeOut();
            $received_data=$(g);
            $zero_row_results=$received_data.find('textarea[name="sql_query"]');
            if($zero_row_results.length>0)$("#sqlquery").val($zero_row_results.val());
            else{
                $("#sqlqueryresults").show();
                $("#sqlqueryresults").html(g);
                $("#sqlqueryresults").trigger("appendAnchor");
                $("#togglequerybox").show();
                $("#togglequerybox").siblings(":visible").length>0&&$("#togglequerybox").trigger("click");
                PMA_init_slider()
                }
            }
        PMA_ajaxRemoveMessage(e)
        })
    });
$("input[name=navig].ajax").live("click",function(b){
    b.preventDefault();
    var a=PMA_ajaxShowMessage();
    b=$(this).parent("form");
    b.append('<input type="hidden" name="ajax_request" value="true" />');
    $.post(b.attr("action"),b.serialize(),function(e){
        $("#sqlqueryresults").html(e);
        $("#sqlqueryresults").trigger("appendAnchor");
        PMA_init_slider();
        PMA_ajaxRemoveMessage(a)
        })
    });
$("#pageselector").live("change",function(b){
    var a=$(this).parent("form");
    if($(this).hasClass("ajax")){
        b.preventDefault();
        var e=PMA_ajaxShowMessage();
        $.post(a.attr("action"),a.serialize()+"&ajax_request=true",function(g){
            $("#sqlqueryresults").html(g);
            $("#sqlqueryresults").trigger("appendAnchor");
            PMA_init_slider();
            PMA_ajaxRemoveMessage(e)
            })
        }else a.submit()
        });
$("#table_results.ajax").find("a[title=Sort]").live("click",
    function(b){
        b.preventDefault();
        var a=PMA_ajaxShowMessage();
        $anchor=$(this);
        $.get($anchor.attr("href"),$anchor.serialize()+"&ajax_request=true",function(e){
            $("#sqlqueryresults").html(e).trigger("appendAnchor");
            PMA_ajaxRemoveMessage(a)
            })
        });
$("#displayOptionsForm.ajax").live("submit",function(b){
    b.preventDefault();
    $form=$(this);
    $.post($form.attr("action"),$form.serialize()+"&ajax_request=true",function(a){
        $("#sqlqueryresults").html(a).trigger("appendAnchor");
        PMA_init_slider()
        })
    });
$(".inline_edit_anchor span a").live("click",
    function(b){
        b.preventDefault();
        var a=$(this).parents("td");
        a.removeClass("inline_edit_anchor").addClass("inline_edit_active").parent("tr").addClass("noclick");
        var e=a.children("span.nowrap").children("a").children("span.nowrap");
        $data_a=a.children("span.nowrap").children("a").clone();
        b=e.find("img");
        e.parent("a").find('span:contains("'+PMA_messages.strInlineEdit+'")').length>0?e.text(" "+PMA_messages.strSave):e.empty();
        if(b.length>0){
            b.attr("title",PMA_messages.strSave);
            var g=b.attr("src").replace(/b_inline_edit/,
                "b_save");
            b.attr("src",g);
            e.prepend(b)
            }
            e=a.children("span.nowrap").children("a").clone().attr("id","hide");
        var m=e.find("span");
        b=e.find("span img");
        e.find('span:contains("'+PMA_messages.strSave+'")').length>0?m.text(" "+PMA_messages.strHide):m.empty();
        if(b.length>0){
            b.attr("title",PMA_messages.strHide);
            g=b.attr("src").replace(/b_save/,"b_close");
            b.attr("src",g);
            m.prepend(b)
            }
            a.children("span.nowrap").append($("<br /><br />")).append(e);
        if(d!="vertical")$("#table_results tbody tr td span a#hide").click(function(){
            var f=
            $(this).parents("td"),c=f.find("span");
            c.find("a, br").remove();
            c.append($data_a.clone());
            f.removeClass("inline_edit_active hover").addClass("inline_edit_anchor");
            f.parent().removeClass("hover noclick");
            f.siblings().removeClass("hover");
            c=f.siblings().length;
            for(var i="",h=4;h<c;h++)if(f.siblings("td:eq("+h+")").hasClass("inline_edit")!=false){
                i=f.siblings("td:eq("+h+")").data("original_data");
                if(f.siblings("td:eq("+h+")").children().length!=0){
                    f.siblings("td:eq("+h+")").empty();
                    f.siblings("td:eq("+
                        h+")").append(i)
                    }
                }
            $(this).prev().prev().remove();
            $(this).prev().remove();
            $(this).remove()
            });
    else{
        var k="",r=a.parent().siblings().length;
        $("#table_results tbody tr td span a#hide").click(function(){
            var f=$(this),c=f.parents("td").index();
            f=f.parent();
            f.find("a, br").remove();
            f.append($data_a.clone());
            f=f.parents("tr");
            f.siblings("tr:eq(3) td:eq("+c+")").removeClass("inline_edit_active").addClass("inline_edit_anchor");
            f.parent("tbody").find("tr").find("td:eq("+c+")").removeClass("marked hover");
            for(var i=
                6;i<=r+2;i++)if(f.siblings("tr:eq("+i+") td:eq("+c+")").hasClass("inline_edit")!=false){
                k=f.siblings("tr:eq("+i+") td:eq("+c+")").data("original_data");
                f.siblings("tr:eq("+i+") td:eq("+c+")").empty();
                f.siblings("tr:eq("+i+") td:eq("+c+")").append(k)
                }
                $(this).prev().remove();
            $(this).prev().remove();
            $(this).remove()
            })
        }
        if(d=="vertical"){
        var l=a.index();
        b=a.parents("tbody").find("tr").find(".inline_edit:nth("+l+")");
        var q=a.parents("tbody").find("tr").find(".where_clause:nth("+l+")").val()
        }else{
        l=a.parent().index();
        b=a.parent("tr").find(".inline_edit");
        q=a.parent("tr").find(".where_clause").val()
        }
        b.each(function(){
        var f=$(this).html(),c=$(this),i=getFieldName(c,d),h=c.find("a").text(),t=c.find("a").attr("title"),s=c.text();
        if(c.is(":not(.not_null)")){
            c.html('<div class="null_div">Null :<input type="checkbox" class="checkbox_null_'+i+"_"+l+'"></div>');
            c.is(".null")&&$(".checkbox_null_"+i+"_"+l).attr("checked",true);
            if(c.is(".enum, .set"))c.find("select").live("change",function(){
                $(".checkbox_null_"+i+"_"+l).attr("checked",
                    false)
                });
            else if(c.is(".relation")){
                c.find("select").live("change",function(){
                    $(".checkbox_null_"+i+"_"+l).attr("checked",false)
                    });
                c.find(".browse_foreign").live("click",function(){
                    $(".checkbox_null_"+i+"_"+l).attr("checked",false)
                    })
                }else c.find("textarea").live("keypress",function(n){
                n.which!=0&&$(".checkbox_null_"+i+"_"+l).attr("checked",false)
                });
            $(".checkbox_null_"+i+"_"+l).bind("click",function(){
                if(c.is(".enum"))c.find("select").attr("value","");
                else if(c.is(".set"))c.find("select").find("option").each(function(){
                    $(this).attr("selected",
                        false)
                    });
                else if(c.is(".relation"))c.find("select").length>0?c.find("select").attr("value",""):c.find("span.curr_value").empty();else c.find("textarea").val("")
                    })
            }else c.html('<div class="null_div"></div>');
        if(c.is(":not(.truncated, .transformed, .relation, .enum, .set, .null)")){
            value=f.replace("<br>","\n");
            c.append("<textarea>"+value+"</textarea>");
            c.data("original_data",f)
            }else if(c.is(".truncated, .transformed")){
            h="SELECT `"+i+"` FROM `"+window.parent.table+"` WHERE "+PMA_urldecode(q);
            $.post("sql.php",

            {
                token:window.parent.token,
                server:window.parent.server,
                db:window.parent.db,
                ajax_request:true,
                sql_query:h,
                inline_edit:true
            },function(n){
                n.success==true&&a.hasClass("inline_edit_active")?c.append("<textarea>"+n.value+"</textarea>"):PMA_ajaxShowMessage(n.error)
                });
            c.data("original_data",f)
            }else if(c.is(".relation")){
            h={
                ajax_request:true,
                get_relational_values:true,
                server:window.parent.server,
                db:window.parent.db,
                table:window.parent.table,
                column:i,
                token:window.parent.token,
                curr_value:h,
                relation_key_or_display_column:t
            };
            $.post("sql.php",h,function(n){
                a.hasClass("inline_edit_active")&&c.append(n.dropdown)
                });
            c.data("original_data",f)
            }else if(c.is(".enum")){
            h={
                ajax_request:true,
                get_enum_values:true,
                server:window.parent.server,
                db:window.parent.db,
                table:window.parent.table,
                column:i,
                token:window.parent.token,
                curr_value:s
            };
            
            $.post("sql.php",h,function(n){
                a.hasClass("inline_edit_active")&&c.append(n.dropdown)
                });
            c.data("original_data",f)
            }else if(c.is(".set")){
            h={
                ajax_request:true,
                get_set_values:true,
                server:window.parent.server,
                db:window.parent.db,
                table:window.parent.table,
                column:i,
                token:window.parent.token,
                curr_value:s
            };
            
            $.post("sql.php",h,function(n){
                a.hasClass("inline_edit_active")&&c.append(n.select)
                });
            c.data("original_data",f)
            }else if(c.is(".null")){
            c.append("<textarea></textarea>");
            c.data("original_data","NULL")
            }
        })
});
$(".inline_edit_active span a").live("click",function(b){
    b.preventDefault();
    var a=$(this).parent().parent(),e="";
    if(d=="vertical")var g=a.index(),m=a.parents("tbody").find("tr").find(".inline_edit:nth("+
        g+")"),k=a.parents("tbody").find("tr").find(".where_clause:nth("+g+")").val();
    else{
        m=a.parent("tr").find(".inline_edit");
        k=a.parent("tr").find(".where_clause").val()
        }
        b=a.is(".nonunique")?0:1;
    var r={},l=$("#relational_display_K").attr("checked")?"K":"D",q={},f=false,c="UPDATE `"+window.parent.table+"` SET ",i=false,h="";
    m.each(function(){
        var j=$(this),o=getFieldName(j,d),p={};
        
        if(j.is(".transformed"))f=true;
        var u=true;
        if(j.find("input:checkbox").is(":checked")){
            c+=" `"+o+"`=NULL , ";
            i=true
            }else{
            if(j.is(":not(.relation, .enum, .set, .bit)")){
                p[o]=
                j.find("textarea").val();
                j.is(".transformed")&&$.extend(q,p)
                }else if(j.is(".bit")){
                p[o]="0b"+j.find("textarea").val();
                u=false
                }else if(j.is(".set")){
                e=j.find("select");
                p[o]=e.map(function(){
                    return $(this).val()
                    }).get().join(",")
                }else{
                e=j.find("select");
                if(e.length!=0)p[o]=e.val();
                e=j.find("span.curr_value");
                if(e.length!=0)p[o]=e.text();
                j.is(".relation")&&$.extend(r,p)
                }
                if(k.indexOf(o)>-1)h+="`"+window.parent.table+"`.`"+o+"` = '"+p[o].replace(/'/g,"''")+"' AND ";
            if(p[o]!=j.data("original_data")){
                c+=
                u==true?" `"+o+"`='"+p[o].replace(/'/g,"''")+"', ":" `"+o+"`="+p[o].replace(/'/g,"''")+", ";
                i=true
                }
            }
    });
c=c.replace(/,\s$/,"");
c=c.replace(/\\/g,"\\\\");
h=h.substring(0,h.length-5);
h=PMA_urlencode(h);
c+=" WHERE "+PMA_urldecode(k);
c+=" LIMIT 1";
var t=$.param(r),s=$.param(q),n=$(this).parent(),v=$(this);
i?$.post("tbl_replace.php",{
    ajax_request:true,
    sql_query:c,
    disp_direction:d,
    token:window.parent.token,
    server:window.parent.server,
    db:window.parent.db,
    table:window.parent.table,
    clause_is_unique:b,
    where_clause:k,
    rel_fields_list:t,
    do_transformations:f,
    transform_fields_list:s,
    relational_display:l,
    "goto":"sql.php",
    submit_type:"save"
},function(j){
    if(j.success==true){
        PMA_ajaxShowMessage(j.message);
        d=="vertical"?a.parents("tbody").find("tr").find(".where_clause:nth("+g+")").attr("value",h):a.parent("tr").find(".where_clause").attr("value",h);
        $("#result_query").remove();
        typeof j.sql_query!="undefined"&&$("#sqlqueryresults").prepend(j.sql_query);
        PMA_unInlineEditRow(n,v,a,m,j,d)
        }else PMA_ajaxShowMessage(j.error)
        }):
PMA_unInlineEditRow(n,v,a,m,"",d)
})
},"top.frame_content");
function PMA_unInlineEditRow(d,b,a,e,g,m){
    d.find("a, br").remove();
    d.append($data_a.clone());
    a.removeClass("inline_edit_active").addClass("inline_edit_anchor");
    a.parent("tr").removeClass("noclick");
    m!="vertical"?a.parent("tr").removeClass("hover").find("td").removeClass("hover"):a.parents("tbody").find("tr").find("td:eq("+a.index()+")").removeClass("marked hover");
    e.each(function(){
        $this_sibling=$(this);
        if($this_sibling.find("input:checkbox").is(":checked")){
            $this_sibling.html("NULL");
            $this_sibling.addClass("null")
            }else{
            $this_sibling.removeClass("null");
            if($this_sibling.is(":not(.relation, .enum, .set)")){
                var k=$this_sibling.find("textarea").val();
                if($this_sibling.is(".transformed")){
                    var r=getFieldName($this_sibling,m);
                    typeof g.transformations!="undefined"&&$.each(g.transformations,function(q,f){
                        if(q==r){
                            if($this_sibling.is(".text_plain, .application_octetstream"))k=f;
                            else{
                                var c=$this_sibling.find("textarea").val();
                                k=$(f).append(c)
                                }
                                return false
                            }
                        })
                }
            }else{
        var l=k="";
        $test_element=$this_sibling.find("select");
        if($test_element.length!=0)l=$test_element.val();
        $test_element=$this_sibling.find("span.curr_value");
        if($test_element.length!=0)l=$test_element.text();
        if($this_sibling.is(".relation")){
            r=getFieldName($this_sibling,m);
            typeof g.relations!="undefined"&&$.each(g.relations,function(q,f){
                if(q==r){
                    k=$(f);
                    return false
                    }
                })
        }else if($this_sibling.is(".enum"))k=l;
        else if($this_sibling.is(".set"))if(l!=null){
        $.each(l,function(q,f){
            k=k+f+","
            });
        k=k.substring(0,k.length-1)
        }
    }
    $this_sibling.text(k)
}
})
}
function PMA_changeClassForColumn(d,b){
    var a=d.index();
    !d.closest("tr").children(":first").hasClass("column_heading")&&a--;
    a=d.closest("table").find("tbody tr").find("td.data:eq("+a+")");
    if(d.data("has_class_"+b)){
        a.removeClass(b);
        d.data("has_class_"+b,false)
        }else{
        a.addClass(b);
        d.data("has_class_"+b,true)
        }
    }
$(document).ready(function(){
    $(".browse_foreign").live("click",function(d){
        d.preventDefault();
        window.open(this.href,"foreigners","width=640,height=240,scrollbars=yes,resizable=yes");
        $anchor=$(this);
        $anchor.addClass("browse_foreign_clicked");
        return false
        });
    $(".column_heading.pointer").live("hover",function(){
        PMA_changeClassForColumn($(this),"hover")
        });
    $(".column_heading.marker").live("click",function(){
        PMA_changeClassForColumn($(this),"marked")
        })
    });
