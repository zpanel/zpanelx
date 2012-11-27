function PMA_adjustTotals(a){
    var b=a.closest("tr");
    a=b.find(".tbl_rows");
    var d=b.find(".tbl_size");
    b=parseInt(a.text());
    a.text("0");
    d.text("-");
    if(!isNaN(b)){
        $total_rows_td=$("#tbl_summary_row").find(".tbl_rows");
        a=parseInt($total_rows_td.text());
        isNaN(a)||$total_rows_td.text(a-b)
        }
        a=$("#tbl_summary_row").find(".tbl_size");
    a.text(a.text().replace(/^/,"~"))
    }
$(document).ready(function(){
    $(".truncate_table_anchor").live("click",function(a){
        a.preventDefault();
        var b=$(this);
        a="TRUNCATE "+b.parents("tr").children("th").children("a").text();
        b.PMA_confirm(a,b.attr("href"),function(d){
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            $.get(d,{
                is_js_confirmed:1,
                ajax_request:true
            },function(c){
                if(c.success==true){
                    PMA_ajaxShowMessage(c.message);
                    c=b.html().replace(/b_empty.png/,"bd_empty.png");
                    PMA_adjustTotals(b);
                    b.replaceWith(c).removeClass("truncate_table_anchor")
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+
                    " : "+c.error)
                })
            })
        });
    $(".drop_table_anchor").live("click",function(a){
        a.preventDefault();
        var b=$(this),d=b.parents("tr");
        a="DROP TABLE "+d.children("th").children("a").text();
        b.PMA_confirm(a,b.attr("href"),function(c){
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            $.get(c,{
                is_js_confirmed:1,
                ajax_request:true
            },function(e){
                if(e.success==true){
                    PMA_ajaxShowMessage(e.message);
                    PMA_adjustTotals(b);
                    d.hide("medium").remove();
                    window.parent&&window.parent.frame_navigation&&window.parent.frame_navigation.location.reload()
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+
                    " : "+e.error)
                })
            })
        });
    $(".drop_event_anchor").live("click",function(a){
        a.preventDefault();
        var b=$(this).parents("tr");
        a="DROP EVENT "+$(b).children("td:first").text();
        $(this).PMA_confirm(a,$(this).attr("href"),function(d){
            PMA_ajaxShowMessage(PMA_messages.strDroppingEvent);
            $.get(d,{
                is_js_confirmed:1,
                ajax_request:true
            },function(c){
                if(c.success==true){
                    PMA_ajaxShowMessage(c.message);
                    $(b).hide("medium").remove()
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+c.error)
                    })
            })
        });
    $(".drop_procedure_anchor").live("click",
        function(a){
            a.preventDefault();
            a=$(this).parents("tr");
            a=$(a).children("td").children(".drop_procedure_sql").val();
            $(this).PMA_confirm(a,$(this).attr("href"),function(b){
                PMA_ajaxShowMessage(PMA_messages.strDroppingProcedure);
                $.get(b,{
                    is_js_confirmed:1,
                    ajax_request:true
                },function(d){
                    if(d.success==true){
                        PMA_ajaxShowMessage(d.message);
                        $(curr_event_row).hide("medium").remove()
                        }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+d.error)
                        })
                })
            });
    $(".drop_tracking_anchor").live("click",
        function(a){
            a.preventDefault();
            a=$(this);
            var b=a.parents("tr");
            a.PMA_confirm(PMA_messages.strDeleteTrackingData,a.attr("href"),function(d){
                PMA_ajaxShowMessage(PMA_messages.strDeletingTrackingData);
                $.get(d,{
                    is_js_confirmed:1,
                    ajax_request:true
                },function(c){
                    if(c.success==true){
                        PMA_ajaxShowMessage(c.message);
                        $(b).hide("medium").remove()
                        }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+c.error)
                        })
                })
            });
    $("#real_end_input").live("click",function(a){
        a.preventDefault();
        a=PMA_messages.strOperationTakesLongTime;
        $(this).PMA_confirm(a,"",function(){
            return true
            });
        return false
        })
    },"top.frame_content");
