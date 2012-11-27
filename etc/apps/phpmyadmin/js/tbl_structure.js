$(document).ready(function(){
    $(".drop_column_anchor").live("click",function(a){
        a.preventDefault();
        a=window.parent.table;
        var d=$(this).parents("tr"),c=$(d).children("th").children("label").text(),b=$("select[name='after_field'] option[value='"+c+"']");
        a=PMA_messages.strDoYouReally+" :\n ALTER TABLE `"+escapeHtml(a)+"` DROP `"+escapeHtml(c)+"`";
        $(this).PMA_confirm(a,$(this).attr("href"),function(e){
            PMA_ajaxShowMessage(PMA_messages.strDroppingColumn);
            $.get(e,{
                is_js_confirmed:1,
                ajax_request:true
            },function(f){
                if(f.success==
                    true){
                    PMA_ajaxShowMessage(f.message);
                    b.remove();
                    $(d).hide("medium").remove()
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+f.error)
                    })
            })
        });
    $(".action_primary a").live("click",function(a){
        a.preventDefault();
        a=window.parent.table;
        var d=$(this).parents("tr").children("th").children("label").text();
        a=PMA_messages.strDoYouReally+" :\n ALTER TABLE `"+escapeHtml(a)+"` ADD PRIMARY KEY(`"+escapeHtml(d)+"`)";
        $(this).PMA_confirm(a,$(this).attr("href"),function(c){
            PMA_ajaxShowMessage(PMA_messages.strAddingPrimaryKey);
            $.get(c,{
                is_js_confirmed:1,
                ajax_request:true
            },function(b){
                if(b.success==true){
                    PMA_ajaxShowMessage(b.message);
                    $(this).remove();
                    typeof b.reload!="undefined"&&window.parent.frame_content.location.reload()
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+b.error)
                    })
            })
        });
    $(".drop_primary_key_index_anchor").live("click",function(a){
        a.preventDefault();
        $anchor=$(this);
        a=$anchor.parents("tr");
        for(var d=$anchor.parents("td").attr("rowspan")||1,c=a,b=1,e=a.next();b<d;b++,e=e.next())c=c.add(e);
        a=a.children("td").children(".drop_primary_key_index_msg").val();
        $anchor.PMA_confirm(a,$anchor.attr("href"),function(f){
            PMA_ajaxShowMessage(PMA_messages.strDroppingPrimaryKeyIndex);
            $.get(f,{
                is_js_confirmed:1,
                ajax_request:true
            },function(g){
                if(g.success==true){
                    PMA_ajaxShowMessage(g.message);
                    c.hide("medium").remove()
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+g.error)
                    })
            })
        })
    });
