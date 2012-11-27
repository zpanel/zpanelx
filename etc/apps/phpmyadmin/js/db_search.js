function loadResult(a,c,b,d){
    $(document).ready(function(){
        if(d){
            $("#sqlqueryform").hide();
            $("#togglequerybox").hide();
            $("#table-info").show();
            $("#table-link").attr({
                href:"sql.php?"+b
                }).text(c);
            $("#browse-results").load(a+" '#sqlqueryresults'").show()
            }else event.preventDefault()
            })
    }
function deleteResult(a,c,b){
    $(document).ready(function(){
        $("#table-info").hide();
        $("#browse-results").hide();
        $("#sqlqueryform").hide();
        $("#togglequerybox").hide();
        if(confirm(c))if(b){
            var d=PMA_ajaxShowMessage(PMA_messages.strDeleting);
            $("#browse-results").load(a+" '#result_query'",function(){
                $("#sqlqueryform").load(a+" '#sqlqueryform'",function(){
                    document.getElementById("buttonGo").click();
                    $("#togglequerybox").html(PMA_messages.strHideQueryBox);
                    PMA_ajaxRemoveMessage(d);
                    $("#browse-results").show();
                    $("#sqlqueryform").show();
                    $("#togglequerybox").show()
                    })
                })
            }else event.preventDefault()
            })
    }
$(document).ready(function(){
    $.ajaxSetup({
        cache:"false"
    });
    $("#table-info").prepend('<img id="table-image" src="./themes/original/img/s_tbl.png" />').hide();
    $("#buttonGo").click(function(){
        $("#table-info").hide();
        $("#browse-results").hide();
        $("#sqlqueryform").hide();
        $("#togglequerybox").hide()
        });
    $('<div id="togglesearchformdiv"><a id="togglesearchformlink"></a></div>').insertAfter("#db_search_form").hide();
    $("#togglequerybox").hide();
    $("#togglequerybox").bind("click",function(){
        var a=$(this);
        $("#sqlqueryform").slideToggle("medium");
        a.text()==PMA_messages.strHideQueryBox?a.text(PMA_messages.strShowQueryBox):a.text(PMA_messages.strHideQueryBox);
        return false
        });
    $("#togglesearchformlink").html(PMA_messages.strShowSearchCriteria).bind("click",function(){
        var a=$(this);
        $("#db_search_form").slideToggle();
        a.text()==PMA_messages.strHideSearchCriteria?a.text(PMA_messages.strShowSearchCriteria):a.text(PMA_messages.strHideSearchCriteria);
        return false
        });
    $("#db_search_form.ajax").live("submit",function(a){
        a.preventDefault();
        var c=PMA_ajaxShowMessage(PMA_messages.strSearching);
        $form=$(this);
        PMA_prepareForAjaxRequest($form);
        $.post($form.attr("action"),$form.serialize()+"&submit_search="+$("#buttonGo").val(),function(b){
            if(typeof b=="string"){
                $("#searchresults").html(b);
                $("#sqlqueryresults").trigger("appendAnchor");
                $("#db_search_form").slideToggle().hide();
                $("#togglesearchformlink").text(PMA_messages.strShowSearchCriteria);
                $("#togglesearchformdiv").show()
                }else $("#sqlqueryresults").html(b.message);
            PMA_ajaxRemoveMessage(c)
            })
        })
    },"top.frame_content");
