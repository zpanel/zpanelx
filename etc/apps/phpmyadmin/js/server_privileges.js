function checkPassword(a){
    if(typeof a.elements.nopass!="undefined"&&a.elements.nopass[0].checked)return true;
    else if(typeof a.elements.pred_password!="undefined"&&(a.elements.pred_password.value=="none"||a.elements.pred_password.value=="keep"))return true;
    var c=a.elements.pma_pw;
    a=a.elements.pma_pw2;
    var b=false;
    if(c.value=="")b=PMA_messages.strPasswordEmpty;
    else if(c.value!=a.value)b=PMA_messages.strPasswordNotSame;
    if(b){
        alert(b);
        c.value="";
        a.value="";
        c.focus();
        return false
        }
        return true
    }
function checkAddUser(a){
    if(a.elements.pred_hostname.value=="userdefined"&&a.elements.hostname.value==""){
        alert(PMA_messages.strHostEmpty);
        a.elements.hostname.focus();
        return false
        }
        if(a.elements.pred_username.value=="userdefined"&&a.elements.username.value==""){
        alert(PMA_messages.strUserEmpty);
        a.elements.username.focus();
        return false
        }
        return checkPassword(a)
    }
function appendNewUser(a,c,b){
    var d=$("#usersForm").find("tbody").find("tr:last"),e=$("#usersForm").find("tbody").find("tr:first").find("label").html().substr(0,1).toUpperCase(),f=d.find("label").html().substr(0,1).toUpperCase(),g=d.find("input:checkbox").attr("id").match(/\d+/)[0];
    g="checkbox_sel_users_"+(parseFloat(g)+1);
    e=e!=f?true:false;
    if(f==c||e)$(a).insertAfter(d).find("input:checkbox").attr("id",g).val(function(){
        return $(this).val().replace(/&/,"&amp;")
        }).end().find("label").attr("for",g).end();
    $("#usersForm").find("tbody").PMA_sort_table("label");
    $("#initials_table").find("td:contains("+c+")").html(b)
    }
$(document).ready(function(){
    $.ajaxSetup({
        cache:"false"
    });
    $("#fieldset_add_user a.ajax").live("click",function(a){
        a.preventDefault();
        var c=PMA_ajaxShowMessage(),b={};
        
        b[PMA_messages.strCreateUser]=function(){
            var d=$(this).find("form[name=usersForm]").last();
            if(!checkAddUser(d.get(0))){
                PMA_ajaxShowMessage(PMA_messages.strFormEmpty);
                return false
                }
                $.post(d.attr("action"),d.serialize()+"&adduser_submit="+$(this).find("input[name=adduser_submit]").attr("value"),function(e){
                if(e.success==true){
                    $("#add_user_dialog").dialog("close").remove();
                    PMA_ajaxShowMessage(e.message);
                    $("#topmenucontainer").next("div").remove().end().after(e.sql_query);
                    var f=$("#topmenucontainer").next("div").find(".notice");
                    f.text()==""&&f.remove();
                    appendNewUser(e.new_user_string,e.new_user_initial,e.new_user_initial_string)
                    }else PMA_ajaxShowMessage(PMA_messages.strErrorProcessingRequest+" : "+e.error,"7000")
                    })
            };
            
        b[PMA_messages.strCancel]=function(){
            $(this).dialog("close").remove()
            };
            
        $.get($(this).attr("href"),{
            ajax_request:true
        },function(d){
            $('<div id="add_user_dialog"></div>').prepend(d).find("#fieldset_add_user_footer").hide().end().find("form[name=usersForm]").append('<input type="hidden" name="ajax_request" value="true" />').end().dialog({
                title:PMA_messages.strAddNewUser,
                width:800,
                height:600,
                modal:true,
                buttons:b
            });
            displayPasswordGenerateButton();
            PMA_ajaxRemoveMessage(c)
            })
        });
    $("#reload_privileges_anchor.ajax").live("click",function(a){
        a.preventDefault();
        PMA_ajaxShowMessage(PMA_messages.strReloadingPrivileges);
        $.get($(this).attr("href"),{
            ajax_request:true
        },function(c){
            c.success==true?PMA_ajaxShowMessage(c.message):PMA_ajaxShowMessage(c.error)
            })
        });
    $("#fieldset_delete_user_footer #buttonGo.ajax").live("click",function(a){
        a.preventDefault();
        PMA_ajaxShowMessage(PMA_messages.strRemovingSelectedUsers);
        $form=$("#usersForm");
        $.post($form.attr("action"),$form.serialize()+"&delete="+$(this).attr("value")+"&ajax_request=true",function(c){
            if(c.success==true){
                PMA_ajaxShowMessage(c.message);
                $form.find("input:checkbox:checked").parents("tr").slideUp("medium",function(){
                    var b=$(this).find("input:checkbox").val().charAt(0).toUpperCase();
                    $(this).remove();
                    $("#tableuserrights").find("input:checkbox[value^="+b+"]").length==0&&$("#initials_table").find("td > a:contains("+b+")").parent("td").html(b);
                    $form.find("tbody").find("tr:odd").removeClass("even").addClass("odd").end().find("tr:even").removeClass("odd").addClass("even")
                    })
                }else PMA_ajaxShowMessage(c.error)
                })
        });
    $(".edit_user_anchor.ajax").live("click",function(a){
        a.preventDefault();
        var c=PMA_ajaxShowMessage();
        $(this).parents("tr").addClass("current_row");
        var b={};
        
        b[PMA_messages.strCancel]=function(){
            $(this).dialog("close").remove()
            };
            
        $.get($(this).attr("href"),{
            ajax_request:true,
            edit_user_dialog:true
        },function(d){
            $('<div id="edit_user_dialog"></div>').append(d).dialog({
                width:900,
                height:600,
                buttons:b
            });
            displayPasswordGenerateButton();
            PMA_ajaxRemoveMessage(c)
            })
        });
    $("#edit_user_dialog").find("form:not(#db_or_table_specific_priv)").live("submit",
        function(a){
            a.preventDefault();
            PMA_ajaxShowMessage(PMA_messages.strProcessingRequest);
            $(this).append('<input type="hidden" name="ajax_request" value="true" />');
            a=$(this).find(".tblFooters").find("input:submit").attr("name");
            var c=$(this).find(".tblFooters").find("input:submit").val();
            $.post($(this).attr("action"),$(this).serialize()+"&"+a+"="+c,function(b){
                if(b.success==true){
                    PMA_ajaxShowMessage(b.message);
                    $("#edit_user_dialog").dialog("close").remove();
                    if(b.sql_query){
                        $("#topmenucontainer").next("div").remove().end().after(b.sql_query);
                        var d=$("#topmenucontainer").next("div").find(".notice");
                        $(d).text()==""&&$(d).remove()
                        }
                        b.new_user_string&&appendNewUser(b.new_user_string,b.new_user_initial,b.new_user_initial_string);
                    d=!!$("#dbspecificuserrights").length;
                    var e=false;
                    if(b.db_specific_privs==false||d==b.db_specific_privs)e=true;
                    b.new_privileges&&e&&$("#usersForm").find(".current_row").find("tt").html(b.new_privileges);
                    $("#usersForm").find(".current_row").removeClass("current_row")
                    }else PMA_ajaxShowMessage(b.error)
                    })
            });
    $(".export_user_anchor.ajax").live("click",
        function(a){
            a.preventDefault();
            var c=PMA_ajaxShowMessage(),b={};
            
            b[PMA_messages.strClose]=function(){
                $(this).dialog("close").remove()
                };
                
            $.get($(this).attr("href"),{
                ajax_request:true
            },function(d){
                $('<div id="export_dialog"></div>').prepend(d).dialog({
                    width:500,
                    buttons:b
                });
                PMA_ajaxRemoveMessage(c)
                })
            });
    $("#initials_table.ajax").find("a").live("click",function(a){
        a.preventDefault();
        var c=PMA_ajaxShowMessage();
        $.get($(this).attr("href"),{
            ajax_request:true
        },function(b){
            $("#usersForm").hide("medium").remove();
            $("#fieldset_add_user").hide("medium").remove();
            $("#initials_table").after(b).show("medium").siblings("h2").not(":first").remove();
            PMA_ajaxRemoveMessage(c)
            })
        });
    $("#checkbox_drop_users_db").click(function(){
        $this_checkbox=$(this);
        if($this_checkbox.is(":checked"))confirm(PMA_messages.strDropDatabaseStrongWarning+"\n"+PMA_messages.strDoYouReally+" :\nDROP DATABASE")||$this_checkbox.attr("checked",false)
            });
    displayPasswordGenerateButton()
    },"top.frame_content");
