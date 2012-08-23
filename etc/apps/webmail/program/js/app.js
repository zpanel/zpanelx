function rcube_webmail(){
    this.env={
        recipients_separator:",",
        recipients_delimiter:", "
    };
    
    this.labels={};
    
    this.buttons={};
    
    this.buttons_sel={};
    
    this.gui_objects={};
    
    this.gui_containers={};
    
    this.commands={};
    
    this.command_handlers={};
    
    this.onloads=[];
    this.messages={};
    
    this.ref="rcmail";
    var i=this;
    this.dblclick_time=500;
    this.message_time=4E3;
    this.identifier_expr=RegExp("[^0-9a-z-_]","gi");
    this.env.keep_alive=60;
    this.env.request_timeout=180;
    this.env.draft_autosave=0;
    this.env.comm_path="./";
    this.env.blankpage="program/blank.gif";
    $.ajaxSetup({
        cache:!1,
        error:function(a,b,d){
            i.http_error(a,b,d)
            },
        beforeSend:function(a){
            a.setRequestHeader("X-Roundcube-Request",i.env.request_token)
            }
        });
this.set_env=function(a,b){
    if(null!=a&&"object"===typeof a&&!b)for(var d in a)this.env[d]=a[d];else this.env[a]=b
        };
        
this.add_label=function(a,b){
    "string"==typeof a?this.labels[a]=b:"object"==typeof a&&$.extend(this.labels,a)
    };
    
this.register_button=function(a,b,d,e,f,h){
    this.buttons[a]||(this.buttons[a]=[]);
    b={
        id:b,
        type:d
    };
    
    e&&(b.act=e);
    f&&(b.sel=f);
    h&&(b.over=h);
    this.buttons[a].push(b);
    this.loaded&&s(a,b)
    };
    
this.gui_object=function(a,b){
    this.gui_objects[a]=this.loaded?rcube_find_object(b):b
    };
    
this.gui_container=function(a,b){
    this.gui_containers[a]=b
    };
    
this.add_element=function(a,b){
    this.gui_containers[b]&&this.gui_containers[b].jquery&&this.gui_containers[b].append(a)
    };
    
this.register_command=function(a,b,d){
    this.command_handlers[a]=b;
    d&&this.enable_command(a,!0)
    };
    
this.add_onload=function(a){
    this.onloads.push(a)
    };
    
this.init=function(){
    var a,b=this;
    this.task=this.env.task;
    if(!bw.dom||!bw.xmlhttp_test())this.goto_url("error","_code=0x199");
    else{
        for(a in this.gui_containers)this.gui_containers[a]=$("#"+this.gui_containers[a]);for(a in this.gui_objects)this.gui_objects[a]=rcube_find_object(this.gui_objects[a]);if(this.env.x_frame_options)try{
            if("deny"==this.env.x_frame_options&&top.location.href!=self.location.href)top.location.href=self.location.href;
            else if(top.location.hostname!=self.location.hostname)throw 1;
        }catch(d){
            $("form").each(function(){
                i.lock_form(this,
                    !0)
                });
            this.display_message("Blocked: possible clickjacking attack!","error");
            return
        }
        this.init_buttons();
        this.is_framed()&&(parent.rcmail.set_busy(!1,null,parent.rcmail.env.frame_lock),parent.rcmail.env.frame_lock=null);
        this.enable_command("logout","mail","addressbook","settings","save-pref","compose","undo",!0);
        this.env.permaurl&&this.enable_command("permaurl",!0);
        switch(this.task){
            case "mail":
                this.enable_command("list","checkmail","add-contact","search","reset-search","collapse-folder",!0);
                this.gui_objects.messagelist&&
                (this.message_list=new rcube_list_widget(this.gui_objects.messagelist,{
                    multiselect:!0,
                    multiexpand:!0,
                    draggable:!0,
                    keyboard:!0,
                    column_movable:this.env.col_movable,
                    dblclick_time:this.dblclick_time
                    }),this.message_list.row_init=function(a){
                    b.init_message_row(a)
                    },this.message_list.addEventListener("dblclick",function(a){
                    b.msglist_dbl_click(a)
                    }),this.message_list.addEventListener("click",function(a){
                    b.msglist_click(a)
                    }),this.message_list.addEventListener("keypress",function(a){
                    b.msglist_keypress(a)
                    }),this.message_list.addEventListener("select",
                    function(a){
                        b.msglist_select(a)
                        }),this.message_list.addEventListener("dragstart",function(a){
                    b.drag_start(a)
                    }),this.message_list.addEventListener("dragmove",function(a){
                    b.drag_move(a)
                    }),this.message_list.addEventListener("dragend",function(a){
                    b.drag_end(a)
                    }),this.message_list.addEventListener("expandcollapse",function(a){
                    b.msglist_expand(a)
                    }),this.message_list.addEventListener("column_replace",function(a){
                    b.msglist_set_coltypes(a)
                    }),document.onmouseup=function(a){
                    return b.doc_mouse_up(a)
                    },this.gui_objects.messagelist.parentNode.onmousedown=
                function(a){
                    return b.click_on_list(a)
                    },this.message_list.init(),this.enable_command("toggle_status","toggle_flag","menu-open","menu-save",!0),this.command("list"));
                this.gui_objects.qsearchbox&&(null!=this.env.search_text&&(this.gui_objects.qsearchbox.value=this.env.search_text),$(this.gui_objects.qsearchbox).focusin(function(){
                    rcmail.message_list.blur()
                    }));
                !this.env.flag_for_deletion&&this.env.trash_mailbox&&this.env.mailbox!=this.env.trash_mailbox&&this.set_alttext("delete","movemessagetotrash");
                this.env.message_commands="show,reply,reply-all,reply-list,forward,moveto,copy,delete,open,mark,edit,viewsource,download,print,load-attachment,load-headers,forward-attachment".split(",");
                if("show"==this.env.action||"preview"==this.env.action){
                    if(this.enable_command(this.env.message_commands,this.env.uid),this.enable_command("reply-list",this.env.list_post),"show"==this.env.action&&this.http_request("pagenav","_uid="+this.env.uid+"&_mbox="+urlencode(this.env.mailbox)+(this.env.search_request?"&_search="+
                        this.env.search_request:""),this.display_message("","loading")),this.env.blockedobjects&&(this.gui_objects.remoteobjectsmsg&&(this.gui_objects.remoteobjectsmsg.style.display="block"),this.enable_command("load-images","always-load",!0)),"preview"==this.env.action&&this.is_framed())this.enable_command("compose","add-contact",!1),parent.rcmail.show_contentframe(!0)
                        }else"compose"==this.env.action?(this.env.compose_commands=["send-attachment","remove-attachment","send","cancel","toggle-editor"],this.env.drafts_mailbox&&
                    this.env.compose_commands.push("savedraft"),this.enable_command(this.env.compose_commands,"identities",!0),this.env.spellcheck&&(this.env.spellcheck.spelling_state_observer=function(a){
                        i.set_spellcheck_state(a)
                        },this.env.compose_commands.push("spellcheck"),this.set_spellcheck_state("ready"),"1"==$("input[name='_is_html']").val()&&this.display_spellcheck_controls(!1)),document.onmouseup=function(a){
                        return b.doc_mouse_up(a)
                        },this.init_messageform()):"print"==this.env.action&&this.env.uid&&(bw.safari?
                    window.setTimeout("window.print()",10):window.print());
                this.gui_objects.mailboxlist&&(this.env.unread_counts={},this.gui_objects.folderlist=this.gui_objects.mailboxlist,this.http_request("getunread",""));
                this.env.mdn_request&&this.env.uid&&(a="_uid="+this.env.uid+"&_mbox="+urlencode(this.env.mailbox),confirm(this.get_label("mdnrequest"))?this.http_post("sendmdn",a):this.http_post("mark",a+"&_flag=mdnsent"));
                break;
            case "addressbook":
                this.gui_objects.folderlist&&(this.env.contactfolders=$.extend($.extend({},
                this.env.address_sources),this.env.contactgroups));
            this.enable_command("add","import",this.env.writable_source);
                this.enable_command("list","listgroup","listsearch","advanced-search",!0);
                this.gui_objects.contactslist&&(this.contact_list=new rcube_list_widget(this.gui_objects.contactslist,{
                multiselect:!0,
                draggable:this.gui_objects.folderlist?!0:!1,
                keyboard:!0
                }),this.contact_list.row_init=function(a){
                b.triggerEvent("insertrow",{
                    cid:a.uid,
                    row:a
                })
                },this.contact_list.addEventListener("keypress",function(a){
                b.contactlist_keypress(a)
                }),
            this.contact_list.addEventListener("select",function(a){
                b.contactlist_select(a)
                }),this.contact_list.addEventListener("dragstart",function(a){
                b.drag_start(a)
                }),this.contact_list.addEventListener("dragmove",function(a){
                b.drag_move(a)
                }),this.contact_list.addEventListener("dragend",function(a){
                b.drag_end(a)
                }),this.contact_list.init(),this.env.cid&&this.contact_list.highlight_row(this.env.cid),this.gui_objects.contactslist.parentNode.onmousedown=function(a){
                return b.click_on_list(a)
                },document.onmouseup=
            function(a){
                return b.doc_mouse_up(a)
                },this.gui_objects.qsearchbox&&$(this.gui_objects.qsearchbox).focusin(function(){
                rcmail.contact_list.blur()
                }),this.update_group_commands(),this.command("list"));
                this.set_page_buttons();
                this.env.cid&&(this.enable_command("show","edit",!0),this.gui_objects.editform&&$("input.groupmember").change(function(){
                i.group_member_change(this.checked?"add":"del",i.env.cid,i.env.source,this.value)
                }));
            this.gui_objects.editform&&(this.enable_command("save",!0),("add"==this.env.action||
                "edit"==this.env.action)&&this.init_contact_form());
                this.gui_objects.qsearchbox&&this.enable_command("search","reset-search","moveto",!0);
                break;
            case "settings":
                this.enable_command("preferences","identities","save","folders",!0);
                "identities"==this.env.action?this.enable_command("add",2>this.env.identities_level):"edit-identity"==this.env.action||"add-identity"==this.env.action?(this.enable_command("add",2>this.env.identities_level),this.enable_command("save","delete","edit","toggle-editor",!0)):"folders"==
                this.env.action?this.enable_command("subscribe","unsubscribe","create-folder","rename-folder",!0):"edit-folder"==this.env.action&&this.gui_objects.editform&&(this.enable_command("save","folder-size",!0),parent.rcmail.env.messagecount=this.env.messagecount,parent.rcmail.enable_command("purge",this.env.messagecount),$("input[type='text']").first().select());
                this.gui_objects.identitieslist?(this.identity_list=new rcube_list_widget(this.gui_objects.identitieslist,{
                    multiselect:!1,
                    draggable:!1,
                    keyboard:!1
                    }),
                this.identity_list.addEventListener("select",function(a){
                    b.identity_select(a)
                    }),this.identity_list.init(),this.identity_list.focus(),this.env.iid&&this.identity_list.highlight_row(this.env.iid)):this.gui_objects.sectionslist?(this.sections_list=new rcube_list_widget(this.gui_objects.sectionslist,{
                    multiselect:!1,
                    draggable:!1,
                    keyboard:!1
                    }),this.sections_list.addEventListener("select",function(a){
                    b.section_select(a)
                    }),this.sections_list.init(),this.sections_list.focus()):this.gui_objects.subscriptionlist&&
                this.init_subscription_list();
                break;
            case "login":
                a=$("#rcmloginuser");
                a.bind("keyup",function(a){
                return rcmail.login_user_keyup(a)
                });
            ""==a.val()?a.focus():$("#rcmloginpwd").focus();
                var e=new Date;
                a=e.getTimezoneOffset()/-60;
                e=e.getStdTimezoneOffset()/-60;
                $("#rcmlogintz").val(e);
                $("#rcmlogindst").val(a>e?1:0);
                $("form").submit(function(){
                $("input[type=submit]",this).prop("disabled",true);
                rcmail.display_message("","loading")
                });
            this.enable_command("login",!0)
                }
                bw.ie&&$("input[type=file]").keydown(function(a){
            a.keyCode==
            "13"&&a.preventDefault()
            });
        this.loaded=!0;
        this.pending_message&&this.display_message(this.pending_message[0],this.pending_message[1],this.pending_message[2]);
        this.gui_objects.folderlist&&(this.gui_containers.foldertray=$(this.gui_objects.folderlist));
        this.triggerEvent("init",{
            task:this.task,
            action:this.env.action
            });
        for(var f in this.onloads)if("string"===typeof this.onloads[f])eval(this.onloads[f]);
            else if("function"===typeof this.onloads[f])this.onloads[f]();this.start_keepalive()
        }
    };

this.log=function(a){
    window.console&&
    console.log&&console.log(a)
    };
    
this.command=function(a,b,d){
    var e,f,h,g;
    d&&d.blur&&d.blur();
    if(this.busy)return!1;
    if(!this.commands[a])return this.is_framed()&&parent.rcmail.command(a,b),!1;
    if("mail"==this.task&&"compose"==this.env.action&&0>$.inArray(a,this.env.compose_commands)&&this.cmp_hash!=this.compose_field_hash()&&!confirm(this.get_label("notsentwarning")))return!1;
    if("function"===typeof this.command_handlers[a])return e=this.command_handlers[a](b,d),void 0!==e?e:d?!1:!0;
    if("string"===typeof this.command_handlers[a])return e=
        window[this.command_handlers[a]](b,d),void 0!==e?e:d?!1:!0;
    this.triggerEvent("actionbefore",{
        props:b,
        action:a
    });
    e=this.triggerEvent("before"+a,b);
    if(void 0!==e){
        if(!1===e)return!1;
        b=e
        }
        e=void 0;
    switch(a){
        case "login":
            this.gui_objects.loginform&&this.gui_objects.loginform.submit();
            break;
        case "mail":case "addressbook":case "settings":case "logout":
            this.switch_task(a);
            break;
        case "permaurl":
            if(d&&d.href&&d.target)return!0;
            this.env.permaurl&&(parent.location.href=this.env.permaurl);
            break;
        case "menu-open":case "menu-save":
            return this.triggerEvent(a,

            {
            props:b
        }),!1;
        case "open":
            if(f=this.get_single_uid())return d.href="?_task="+this.env.task+"&_action=show&_mbox="+urlencode(this.env.mailbox)+"&_uid="+f,!0;
            break;
        case "list":
            this.reset_qsearch();
            "mail"==this.task?(this.list_mailbox(b),this.env.trash_mailbox&&!this.env.flag_for_deletion&&this.set_alttext("delete",this.env.mailbox!=this.env.trash_mailbox?"movemessagetotrash":"deletemessage")):"addressbook"==this.task&&this.list_contacts(b);
            break;
        case "load-headers":
            this.load_headers(d);
            break;
        case "sort":
            f=
            b;
            g=this.env.sort_col==f?"ASC"==this.env.sort_order?"DESC":"ASC":"ASC";
            this.set_list_sorting(f,g);
            this.list_mailbox("","",f+"_"+g);
            break;
        case "nextpage":
            this.list_page("next");
            break;
        case "lastpage":
            this.list_page("last");
            break;
        case "previouspage":
            this.list_page("prev");
            break;
        case "firstpage":
            this.list_page("first");
            break;
        case "expunge":
            this.env.messagecount&&this.expunge_mailbox(this.env.mailbox);
            break;
        case "purge":case "empty-mailbox":
            this.env.messagecount&&this.purge_mailbox(this.env.mailbox);
            break;
        case "show":
            if("mail"==this.task){
            if((f=this.get_single_uid())&&(!this.env.uid||f!=this.env.uid))this.env.mailbox==this.env.drafts_mailbox?this.goto_url("compose","_draft_uid="+f+"&_mbox="+urlencode(this.env.mailbox),!0):this.show_message(f)
                }else"addressbook"==this.task&&(h=b?b:this.get_single_cid())&&!("show"==this.env.action&&h==this.env.cid)&&this.load_contact(h,"show");
            break;
        case "add":
            "addressbook"==this.task?this.load_contact(0,"add"):"settings"==this.task&&(this.identity_list.clear_selection(),
            this.load_identity(0,"add-identity"));
        break;
        case "edit":
            if("addressbook"==this.task&&(h=this.get_single_cid()))this.load_contact(h,"edit");
            else if("settings"==this.task&&b)this.load_identity(b,"edit-identity");
            else if("mail"==this.task&&(h=this.get_single_uid()))g=this.env.mailbox==this.env.drafts_mailbox?"_draft_uid=":"_uid=",this.goto_url("compose",g+h+"&_mbox="+urlencode(this.env.mailbox),!0);
            break;
        case "save":
            var k;
            if(g=this.gui_objects.editform){
            if("search"!=this.env.action)if((k=$("input[name='_pagesize']",
                g))&&k.length&&isNaN(parseInt(k.val()))){
                alert(this.get_label("nopagesizewarning"));
                k.focus();
                break
            }else{
                if("reload"==b)g.action+="?_reload=1";
                else if("settings"==this.task&&0==this.env.identities_level%2&&(k=$("input[name='_email']",g))&&k.length&&!rcube_check_email(k.val())){
                    alert(this.get_label("noemailwarning"));
                    k.focus();
                    break
                }
                $("input.placeholder").each(function(){
                    this.value==this._placeholder&&(this.value="")
                    })
                }
                parent.rcmail&&parent.rcmail.env.source&&(g.action=this.add_url(g.action,"_orig_source",
                parent.rcmail.env.source));
            g.submit()
            }
            break;
        case "delete":
            "mail"==this.task?this.delete_messages():"addressbook"==this.task?this.delete_contacts():"settings"==this.task&&this.delete_identity();
            break;
        case "move":case "moveto":
            "mail"==this.task?this.move_messages(b):"addressbook"==this.task&&this.drag_active&&this.copy_contact(null,b);
            break;
        case "copy":
            "mail"==this.task&&this.copy_messages(b);
            break;
        case "mark":
            b&&this.mark_message(b);
            break;
        case "toggle_status":
            if(b&&!b._row)break;
            g="read";
            b._row.uid&&
            (f=b._row.uid,this.message_list.rows[f].deleted?g="undelete":this.message_list.rows[f].unread||(g="unread"));
            this.mark_message(g,f);
            break;
        case "toggle_flag":
            if(b&&!b._row)break;
            g="flagged";
            b._row.uid&&(f=b._row.uid,this.message_list.rows[f].flagged&&(g="unflagged"));
            this.mark_message(g,f);
            break;
        case "always-load":
            if(this.env.uid&&this.env.sender){
            this.add_contact(urlencode(this.env.sender));
            window.setTimeout(function(){
                i.command("load-images")
                },300);
            break
        }
        case "load-images":
            this.env.uid&&this.show_message(this.env.uid,
            !0,"preview"==this.env.action);
        break;
        case "load-attachment":
            g="_mbox="+urlencode(this.env.mailbox)+"&_uid="+this.env.uid+"&_part="+b.part;
            if(this.env.uid&&b.mimetype&&this.env.mimetypes&&0<=$.inArray(b.mimetype,this.env.mimetypes)&&("text/html"==b.mimetype&&(g+="&_safe=1"),this.attachment_win=window.open(this.env.comm_path+"&_action=get&"+g+"&_frame=1","rcubemailattachment"))){
            window.setTimeout(function(){
                i.attachment_win.focus()
                },10);
            break
        }
        this.goto_url("get",g+"&_download=1",!1);
            break;
        case "select-all":
            this.select_all_mode=
            b?!1:!0;
            this.dummy_select=!0;
            "invert"==b?this.message_list.invert_selection():this.message_list.select_all("page"==b?"":b);
            this.dummy_select=null;
            break;
        case "select-none":
            this.select_all_mode=!1;
            this.message_list.clear_selection();
            break;
        case "expand-all":
            this.env.autoexpand_threads=1;
            this.message_list.expand_all();
            break;
        case "expand-unread":
            this.env.autoexpand_threads=2;
            this.message_list.collapse_all();
            this.expand_unread();
            break;
        case "collapse-all":
            this.env.autoexpand_threads=0;
            this.message_list.collapse_all();
            break;
        case "nextmessage":
            this.env.next_uid&&this.show_message(this.env.next_uid,!1,"preview"==this.env.action);
            break;
        case "lastmessage":
            this.env.last_uid&&this.show_message(this.env.last_uid);
            break;
        case "previousmessage":
            this.env.prev_uid&&this.show_message(this.env.prev_uid,!1,"preview"==this.env.action);
            break;
        case "firstmessage":
            this.env.first_uid&&this.show_message(this.env.first_uid);
            break;
        case "checkmail":
            this.check_for_recent(!0);
            break;
        case "compose":
            g=this.url("mail/compose");
            if("mail"==this.task)g+=
            "&_mbox="+urlencode(this.env.mailbox),b&&(g+="&_to="+urlencode(b));
        else if("addressbook"==this.task){
            if(b&&0<b.indexOf("@")){
                g=this.get_task_url("mail",g);
                this.redirect(g+"&_to="+urlencode(b));
                break
            }
            h=[];
            if(b)h.push(b);
            else if(this.contact_list){
                k=this.contact_list.get_selection();
                g=0;
                for(f=k.length;g<f;g++)h.push(k[g])
                    }
                    h.length?this.http_post("mailto",{
                _cid:h.join(","),
                _source:this.env.source
                },!0):this.env.group&&this.http_post("mailto",{
                _gid:this.env.group,
                _source:this.env.source
                },!0);
            break
        }else b&&
            (g+="&_to="+urlencode(b));
        this.redirect(g);
            break;
        case "spellcheck":
            window.tinyMCE&&tinyMCE.get(this.env.composebody)?tinyMCE.execCommand("mceSpellCheck",!0):this.env.spellcheck&&this.env.spellcheck.spellCheck&&this.spellcheck_ready&&(this.env.spellcheck.spellCheck(),this.set_spellcheck_state("checking"));
            break;
        case "savedraft":
            self.clearTimeout(this.save_timer);
            if(!this.gui_objects.messageform)break;
            if(!this.env.drafts_mailbox||this.cmp_hash==this.compose_field_hash())break;
            g=this.gui_objects.messageform;
            f=this.set_busy(!0,"savingmessage");
            g.target="savetarget";
            g._draft.value="1";
            g.action=this.add_url(g.action,"_unlock",f);
            g.submit();
            break;
        case "send":
            if(!this.gui_objects.messageform)break;
            if(!b.nocheck&&!this.check_compose_input(a))break;
            self.clearTimeout(this.save_timer);
            h=this.spellcheck_lang();
            g=this.gui_objects.messageform;
            f=this.set_busy(!0,"sendingmessage");
            g.target="savetarget";
            g._draft.value="";
            g.action=this.add_url(g.action,"_unlock",f);
            g.action=this.add_url(g.action,"_lang",h);
            g.submit();
            clearTimeout(this.request_timer);
            break;
        case "send-attachment":
            self.clearTimeout(this.save_timer);
            this.upload_file(b);
            break;
        case "insert-sig":
            this.change_identity($("[name='_from']")[0],!0);
            break;
        case "reply-all":case "reply-list":case "reply":
            if(f=this.get_single_uid())g="_reply_uid="+f+"&_mbox="+urlencode(this.env.mailbox),"reply-all"==a?g+="&_all="+(!b&&this.commands["reply-list"]?"list":"all"):"reply-list"==a&&(g+="&_all=list"),this.goto_url("compose",g,!0);
            break;
        case "forward-attachment":case "forward":
            if(f=
            this.get_single_uid()){
            g="_forward_uid="+f+"&_mbox="+urlencode(this.env.mailbox);
            if("forward-attachment"==a||!b&&this.env.forward_attachment)g+="&_attachment=1";
            this.goto_url("compose",g,!0)
            }
            break;
        case "print":
            if(f=this.get_single_uid())i.printwin=window.open(this.env.comm_path+"&_action=print&_uid="+f+"&_mbox="+urlencode(this.env.mailbox)+(this.env.safemode?"&_safe=1":"")),this.printwin&&(window.setTimeout(function(){
            i.printwin.focus()
            },20),"show"!=this.env.action&&this.mark_message("read",f));
            break;
        case "viewsource":
            if(f=this.get_single_uid())i.sourcewin=window.open(this.env.comm_path+"&_action=viewsource&_uid="+f+"&_mbox="+urlencode(this.env.mailbox)),this.sourcewin&&window.setTimeout(function(){
            i.sourcewin.focus()
            },20);
        break;
        case "download":
            (f=this.get_single_uid())&&this.goto_url("viewsource","&_uid="+f+"&_mbox="+urlencode(this.env.mailbox)+"&_save=1");
            break;
        case "search":
            if(!b&&this.gui_objects.qsearchbox&&(b=this.gui_objects.qsearchbox.value),b){
            this.qsearch(b);
            break
        }
        case "reset-search":
            f=
            this.env.search_request||this.env.qsearch;
            this.reset_qsearch();
            this.select_all_mode=!1;
            if(f&&this.env.mailbox)this.list_mailbox(this.env.mailbox,1);
            else if(f&&"addressbook"==this.task){
                if(""==this.env.source){
                    for(g in this.env.address_sources)break;this.env.source=g;
                    this.env.group=""
                    }
                    this.list_contacts(this.env.source,this.env.group,1)
                }
                break;
        case "listgroup":
            this.reset_qsearch();
            this.list_contacts(b.source,b.id);
            break;
        case "import":
            if("import"==this.env.action&&this.gui_objects.importform){
            if((g=
                document.getElementById("rcmimportfile"))&&!g.value){
                alert(this.get_label("selectimportfile"));
                break
            }
            this.gui_objects.importform.submit();
            this.set_busy(!0,"importwait");
            this.lock_form(this.gui_objects.importform,!0)
            }else this.goto_url("import",this.env.source?"_target="+urlencode(this.env.source)+"&":"");
            break;
        case "export":
            0<this.contact_list.rowcount&&this.goto_url("export",{
            _source:this.env.source,
            _gid:this.env.group,
            _search:this.env.search_request
            });
        break;
        case "upload-photo":
            this.upload_contact_photo(b);
            break;
        case "delete-photo":
            this.replace_contact_photo("-del-");
            break;
        case "preferences":case "identities":case "folders":
            this.goto_url("settings/"+a);
            break;
        case "undo":
            this.http_request("undo","",this.display_message("","loading"));
            break;
        default:
            g=a.replace(/-/g,"_"),this[g]&&"function"===typeof this[g]&&(e=this[g](b))
            }!1===this.triggerEvent("after"+a,b)&&(e=!1);
    this.triggerEvent("actionafter",{
        props:b,
        action:a
    });
    return!1===e?!1:d?!1:!0
    };
    
this.enable_command=function(){
    var a,b,d=Array.prototype.slice.call(arguments),
    e=d.pop(),f;
    for(b=0;b<d.length;b++)if(f=d[b],"string"===typeof f)this.commands[f]=e,this.set_button(f,e?"act":"pas");else for(a in f)d.push(f[a])
        };
        
this.set_busy=function(a,b,d){
    a&&b?(d=this.get_label(b),d==b&&(d="Loading..."),d=this.display_message(d,"loading")):!a&&d&&this.hide_message(d);
    this.busy=a;
    this.gui_objects.editform&&this.lock_form(this.gui_objects.editform,a);
    this.request_timer&&clearTimeout(this.request_timer);
    a&&this.env.request_timeout&&(this.request_timer=window.setTimeout(function(){
        i.request_timed_out()
        },
    1E3*this.env.request_timeout));
    return d
    };
    
this.gettext=this.get_label=function(a,b){
    return b&&this.labels[b+"."+a]?this.labels[b+"."+a]:this.labels[a]?this.labels[a]:a
    };
    
this.switch_task=function(a){
    if(!(this.task===a&&"mail"!=a)){
        var b=this.get_task_url(a);
        "mail"==a&&(b+="&_mbox=INBOX");
        this.redirect(b)
        }
    };

this.get_task_url=function(a,b){
    b||(b=this.env.comm_path);
    return b.replace(/_task=[a-z]+/,"_task="+a)
    };
    
this.request_timed_out=function(){
    this.set_busy(!1);
    this.display_message("Request timed out!",
        "error")
    };
    
this.reload=function(a){
    this.is_framed()?parent.rcmail.reload(a):a?window.setTimeout(function(){
        rcmail.reload()
        },a):window.location&&(location.href=this.env.comm_path+(this.env.action?"&_action="+this.env.action:""))
    };
    
this.add_url=function(a,b,d){
    d=urlencode(d);
    if(/(\?.*)$/.test(a)){
        var e=RegExp.$1,f=RegExp("((\\?|&)"+RegExp.escape(b)+"=[^&]*)"),e=f.test(e)?e.replace(f,RegExp.$2+b+"="+d):e+("&"+b+"="+d);
        return a.replace(/(\?.*)$/,e)
        }
        return a+"?"+b+"="+d
    };
    
this.is_framed=function(){
    return this.env.framed&&
    parent.rcmail&&parent.rcmail!=this&&parent.rcmail.command
    };
    
this.save_pref=function(a){
    var b={
        _name:a.name,
        _value:a.value
        };
        
    a.session&&(b._session=a.session);
    a.env&&(this.env[a.env]=a.value);
    this.http_post("save-pref",b)
    };
    
this.html_identifier=function(a,b){
    a=""+a;
    return b?Base64.encode(a).replace(/=+$/,"").replace(/\+/g,"-").replace(/\//g,"_"):a.replace(this.identifier_expr,"_")
    };
    
this.html_identifier_decode=function(a){
    for(a=(""+a).replace(/-/g,"+").replace(/_/g,"/");a.length%4;)a+="=";
    return Base64.decode(a)
    };
this.drag_menu=function(a,b){
    var d=rcube_event.get_modifier(a),e=this.gui_objects.message_dragmenu;
    return e&&d==SHIFT_KEY&&this.commands.copy?(d=rcube_event.get_mouse_pos(a),this.env.drag_target=b,$(e).css({
        top:d.y-10+"px",
        left:d.x-10+"px"
        }).show(),!0):!1
    };
    
this.drag_menu_action=function(a){
    var b=this.gui_objects.message_dragmenu;
    b&&$(b).hide();
    this.command(a,this.env.drag_target);
    this.env.drag_target=null
    };
    
this.drag_start=function(a){
    var b="mail"==this.task?this.env.mailboxes:this.env.contactfolders;
    this.drag_active=!0;
    this.preview_timer&&clearTimeout(this.preview_timer);
    this.preview_read_timer&&clearTimeout(this.preview_read_timer);
    if(this.gui_objects.folderlist&&b){
        this.initialBodyScrollTop=bw.ie?0:window.pageYOffset;
        this.initialListScrollTop=this.gui_objects.folderlist.parentNode.scrollTop;
        var d,e,a=$(this.gui_objects.folderlist);
        pos=a.offset();
        this.env.folderlist_coords={
            x1:pos.left,
            y1:pos.top,
            x2:pos.left+a.width(),
            y2:pos.top+a.height()
            };
            
        this.env.folder_coords=[];
        for(d in b)if(a=this.get_folder_li(d))if(e=
            a.firstChild.offsetHeight)pos=$(a.firstChild).offset(),this.env.folder_coords[d]={
            x1:pos.left,
            y1:pos.top,
            x2:pos.left+a.firstChild.offsetWidth,
            y2:pos.top+e,
            on:0
        }
        }
        };

this.drag_end=function(){
    this.drag_active=!1;
    this.env.last_folder_target=null;
    this.folder_auto_timer&&(window.clearTimeout(this.folder_auto_timer),this.folder_auto_expand=this.folder_auto_timer=null);
    if(this.gui_objects.folderlist&&this.env.folder_coords)for(var a in this.env.folder_coords)this.env.folder_coords[a].on&&$(this.get_folder_li(a)).removeClass("droptarget")
        };
this.drag_move=function(a){
    if(this.gui_objects.folderlist&&this.env.folder_coords){
        var b,d,e,f,h;
        d="draglayernormal";
        a=rcube_event.get_mouse_pos(a);
        f=this.env.folderlist_coords;
        e=bw.ie?-document.documentElement.scrollTop:this.initialBodyScrollTop;
        var g=this.initialListScrollTop-this.gui_objects.folderlist.parentNode.scrollTop;
        this.contact_list&&this.contact_list.draglayer&&(h=this.contact_list.draglayer.attr("class"));
        a.y+=-g-e;
        if(a.x<f.x1||a.x>=f.x2||a.y<f.y1||a.y>=f.y2)this.env.last_folder_target&&
            ($(this.get_folder_li(this.env.last_folder_target)).removeClass("droptarget"),this.env.folder_coords[this.env.last_folder_target].on=0,this.env.last_folder_target=null);else for(b in this.env.folder_coords)f=this.env.folder_coords[b],a.x>=f.x1&&a.x<f.x2&&a.y>=f.y1&&a.y<f.y2?(f=this.check_droptarget(b))?(d=this.get_folder_li(b),e=$(d.getElementsByTagName("div")[0]),e.hasClass("collapsed")?(this.folder_auto_timer&&window.clearTimeout(this.folder_auto_timer),this.folder_auto_expand=this.env.mailboxes[b].id,
            this.folder_auto_timer=window.setTimeout(function(){
                rcmail.command("collapse-folder",rcmail.folder_auto_expand);
                rcmail.drag_start(null)
                },1E3)):this.folder_auto_timer&&(window.clearTimeout(this.folder_auto_timer),this.folder_auto_expand=this.folder_auto_timer=null),$(d).addClass("droptarget"),this.env.folder_coords[b].on=1,this.env.last_folder_target=b,d="draglayer"+(1<f?"copy":"normal")):this.env.last_folder_target=null:f.on&&($(this.get_folder_li(b)).removeClass("droptarget"),this.env.folder_coords[b].on=
            0);d!=h&&this.contact_list&&this.contact_list.draglayer&&this.contact_list.draglayer.attr("class",d)
        }
    };

this.collapse_folder=function(a){
    var b=this.get_folder_li(a,"",!0),d=$("div:first",b),e=$("ul:first",b);
    if(d.hasClass("collapsed"))e.show(),d.removeClass("collapsed").addClass("expanded"),this.env.collapsed_folders=this.env.collapsed_folders.replace(RegExp("&"+urlencode(a)+"&"),"");
    else if(d.hasClass("expanded"))e.hide(),d.removeClass("expanded").addClass("collapsed"),this.env.collapsed_folders=
        this.env.collapsed_folders+"&"+urlencode(a)+"&",0==this.env.mailbox.indexOf(a+this.env.delimiter)&&!$(b).hasClass("virtual")&&this.command("list",a);else return;
    if(bw.ie6||bw.ie7)if((d=b.nextSibling?b.nextSibling.getElementsByTagName("ul"):null)&&d.length&&(b=d[0])&&b.style&&"none"!=b.style.display)b.style.display="none",b.style.display="";
    this.command("save-pref",{
        name:"collapsed_folders",
        value:this.env.collapsed_folders
        });
    this.set_unread_count_display(a,!1)
    };
    
this.doc_mouse_up=function(a){
    var b,
    d,e;
    if(!$(rcube_event.get_target(a)).closest(".ui-dialog, .ui-widget-overlay").length&&((d=this.message_list)?(rcube_mouse_is_over(a,d.list.parentNode)?d.focus():d.blur(),b=this.env.mailboxes):(d=this.contact_list)?(rcube_mouse_is_over(a,d.list.parentNode)?d.focus():d.blur(),b=this.env.contactfolders):this.ksearch_value&&this.ksearch_blur(),this.drag_active&&b&&this.env.last_folder_target&&(b=b[this.env.last_folder_target],$(this.get_folder_li(this.env.last_folder_target)).removeClass("droptarget"),
        this.env.last_folder_target=null,d.draglayer.hide(),this.drag_menu(a,b)||this.command("moveto",b)),this.buttons_sel)){
        for(e in this.buttons_sel)"function"!==typeof e&&this.button_out(this.buttons_sel[e],e);this.buttons_sel={}
    }
};

this.click_on_list=function(){
    this.gui_objects.qsearchbox&&this.gui_objects.qsearchbox.blur();
    this.message_list?this.message_list.focus():this.contact_list&&this.contact_list.focus();
    return!0
    };
    
this.msglist_select=function(a){
    this.preview_timer&&clearTimeout(this.preview_timer);
    this.preview_read_timer&&clearTimeout(this.preview_read_timer);
    var b=null!=a.get_single_selection();
    this.enable_command(this.env.message_commands,b);
    b&&(this.env.mailbox==this.env.drafts_mailbox?this.enable_command("reply","reply-all","reply-list","forward","forward-attachment",!1):this.env.messages[a.get_single_selection()].ml||this.enable_command("reply-list",!1));
    this.enable_command("delete","moveto","copy","mark",0<a.selection.length?!0:!1);
    if(b||a.selection.length&&a.selection.length!=a.rowcount)this.select_all_mode=
        !1;
    b&&this.env.contentframe&&!a.multi_selecting&&!this.dummy_select?this.preview_timer=window.setTimeout(function(){
        i.msglist_get_preview()
        },200):this.env.contentframe&&this.show_contentframe(!1)
    };
    
this.msglist_click=function(a){
    !a.multi_selecting&&this.env.contentframe&&a.get_single_selection()&&window.frames&&window.frames[this.env.contentframe]&&0<=window.frames[this.env.contentframe].location.href.indexOf(this.env.blankpage)&&(this.preview_timer&&clearTimeout(this.preview_timer),this.preview_read_timer&&
        clearTimeout(this.preview_read_timer),this.preview_timer=window.setTimeout(function(){
            i.msglist_get_preview()
            },200))
    };
    
this.msglist_dbl_click=function(a){
    this.preview_timer&&clearTimeout(this.preview_timer);
    this.preview_read_timer&&clearTimeout(this.preview_read_timer);
    (a=a.get_single_selection())&&this.env.mailbox==this.env.drafts_mailbox?this.goto_url("compose","_draft_uid="+a+"&_mbox="+urlencode(this.env.mailbox),!0):a&&this.show_message(a,!1,!1)
    };
    
this.msglist_keypress=function(a){
    a.modkey!=CONTROL_KEY&&
    (a.key_pressed==a.ENTER_KEY?this.command("show"):a.key_pressed==a.DELETE_KEY||a.key_pressed==a.BACKSPACE_KEY?this.command("delete"):33==a.key_pressed?this.command("previouspage"):34==a.key_pressed&&this.command("nextpage"))
    };
    
this.msglist_get_preview=function(){
    var a=this.get_single_uid();
    a&&this.env.contentframe&&!this.drag_active?this.show_message(a,!1,!0):this.env.contentframe&&this.show_contentframe(!1)
    };
    
this.msglist_expand=function(a){
    this.env.messages[a.uid]&&(this.env.messages[a.uid].expanded=
        a.expanded)
    };
    
this.msglist_set_coltypes=function(a){
    var b,d=a.list.tHead.rows[0].cells;
    this.env.coltypes=[];
    for(a=0;a<d.length;a++)d[a].id&&d[a].id.match(/^rcm/)&&(b=d[a].id.replace(/^rcm/,""),this.env.coltypes.push("to"==b?"from":b));
    if(0<=(a=$.inArray("flag",this.env.coltypes)))this.env.flagged_col=a;
    if(0<=(a=$.inArray("subject",this.env.coltypes)))this.env.subject_col=a;
    this.command("save-pref",{
        name:"list_cols",
        value:this.env.coltypes,
        session:"list_attrib/columns"
    })
    };
    
this.check_droptarget=function(a){
    var b=
    !1,d=!1;
    "mail"==this.task?b=this.env.mailboxes[a]&&this.env.mailboxes[a].id!=this.env.mailbox&&!this.env.mailboxes[a].virtual:"settings"==this.task?b=a!=this.env.mailbox:"addressbook"==this.task&&a!=this.env.source&&this.env.contactfolders[a]&&("group"==this.env.contactfolders[a].type?(d=this.env.contactfolders[a].source,b=this.env.contactfolders[a].id!=this.env.group&&!this.env.contactfolders[d].readonly,d=d!=this.env.source):(b=!this.env.contactfolders[a].readonly,d=!0));
    return b?d?2:1:0
    };
    
this.init_message_row=
function(a){
    var b,d=this,e=a.uid,f=(null!=this.env.status_col?"status":"msg")+"icn"+a.uid;
    e&&this.env.messages[e]&&$.extend(a,this.env.messages[e]);
    if(a.icon=document.getElementById(f))a.icon._row=a.obj,a.icon.onmousedown=function(a){
        d.command("toggle_status",this);
        rcube_event.cancel(a)
        };
        
    a.msgicon=null!=this.env.status_col?document.getElementById("msgicn"+a.uid):a.icon;
    if(null!=this.env.flagged_col&&(a.flagicon=document.getElementById("flagicn"+a.uid)))a.flagicon._row=a.obj,a.flagicon.onmousedown=
        function(a){
            d.command("toggle_flag",this);
            rcube_event.cancel(a)
            };
            
    if(!a.depth&&a.has_children&&(b=document.getElementById("rcmexpando"+a.uid)))a.expando=b,b.onmousedown=function(a){
        return d.expand_message_row(a,e)
        };
        
    this.triggerEvent("insertrow",{
        uid:e,
        row:a
    })
    };
    
this.add_message_row=function(a,b,d,e){
    if(!this.gui_objects.messagelist||!this.message_list||d.mbox!=this.env.mailbox&&!d.skip_mbox_check)return!1;
    this.env.messages[a]||(this.env.messages[a]={});
    $.extend(this.env.messages[a],{
        deleted:d.deleted?
        1:0,
        replied:d.answered?1:0,
        unread:!d.seen?1:0,
        forwarded:d.forwarded?1:0,
        flagged:d.flagged?1:0,
        has_children:d.has_children?1:0,
        depth:d.depth?d.depth:0,
        unread_children:d.unread_children?d.unread_children:0,
        parent_uid:d.parent_uid?d.parent_uid:0,
        selected:this.select_all_mode||this.message_list.in_selection(a),
        ml:d.ml?1:0,
        ctype:d.ctype,
        flags:d.extra_flags
        });
    var f,h,g,k="",j="",l=this.message_list;
    g=l.rows;
    var i=this.env.messages[a];
    f="message"+(!d.seen?" unread":"")+(d.deleted?" deleted":"")+(d.flagged?
        " flagged":"")+(d.unread_children&&d.seen&&!this.env.autoexpand_threads?" unroot":"")+(i.selected?" selected":"");
    var m=document.createElement("tr");
    m.id="rcmrow"+a;
    m.className=f;
    f="msgicon";
    null===this.env.status_col&&(f+=" status",d.deleted?f+=" deleted":d.seen?0<d.unread_children&&(f+=" unreadchildren"):f+=" unread");
    d.answered&&(f+=" replied");
    d.forwarded&&(f+=" forwarded");
    i.selected&&!l.in_selection(a)&&l.selection.push(a);
    if(this.env.threading)if(i.depth)k+='<span id="rcmtab'+a+'" class="branch" style="width:'+
        15*i.depth+'px;">&nbsp;&nbsp;</span>',g[i.parent_uid]&&!1===g[i.parent_uid].expanded||(0==this.env.autoexpand_threads||2==this.env.autoexpand_threads)&&(!g[i.parent_uid]||!g[i.parent_uid].expanded)?(m.style.display="none",i.expanded=!1):i.expanded=!0;
    else if(i.has_children){
        if(void 0===i.expanded&&(1==this.env.autoexpand_threads||2==this.env.autoexpand_threads&&i.unread_children))i.expanded=!0;
        j='<div id="rcmexpando'+a+'" class="'+(i.expanded?"expanded":"collapsed")+'">&nbsp;&nbsp;</div>'
        }
        k+='<span id="msgicn'+
    a+'" class="'+f+'">&nbsp;</span>';
    !bw.ie&&b.subject&&(g=d.mbox==this.env.drafts_mailbox?"_draft_uid":"_uid",b.subject='<a href="./?_task=mail&_action='+(d.mbox==this.env.drafts_mailbox?"compose":"show")+"&_mbox="+urlencode(d.mbox)+"&"+g+"="+a+'" onclick="return rcube_event.cancel(event)" onmouseover="rcube_webmail.long_subject_title(this,'+(i.depth+1)+')">'+b.subject+"</a>");
    for(h in this.env.coltypes)f=this.env.coltypes[h],g=document.createElement("td"),g.className=(""+f).toLowerCase(),"flag"==f?
        (f=d.flagged?"flagged":"unflagged",f='<span id="flagicn'+a+'" class="'+f+'">&nbsp;</span>'):"attachment"==f?f=/application\/|multipart\/m/.test(d.ctype)?'<span class="attachment">&nbsp;</span>':/multipart\/report/.test(d.ctype)?'<span class="report">&nbsp;</span>':"&nbsp;":"status"==f?(f=d.deleted?"deleted":d.seen?0<d.unread_children?"unreadchildren":"msgicon":"unread",f='<span id="statusicn'+a+'" class="'+f+'">&nbsp;</span>'):"threads"==f?f=j:"subject"==f?(bw.ie&&(g.onmouseover=function(){
            rcube_webmail.long_subject_title_ie(this,
                i.depth+1)
            }),f=k+b[f]):f="priority"==f?0<d.prio&&6>d.prio?'<span class="prio'+d.prio+'">&nbsp;</span>':"&nbsp;":b[f],g.innerHTML=f,m.appendChild(g);l.insert_row(m,e);
    e&&this.env.pagesize&&l.rowcount>this.env.pagesize&&(a=l.get_last_row(),l.remove_row(a),l.clear_selection(a))
    };
    
this.set_list_sorting=function(a,b){
    $("#rcm"+this.env.sort_col).removeClass("sorted"+this.env.sort_order.toUpperCase());
    a&&$("#rcm"+a).addClass("sorted"+b);
    this.env.sort_col=a;
    this.env.sort_order=b
    };
    
this.set_list_options=function(a,
    b,d,e){
    var f,h="";
    void 0===b&&(b=this.env.sort_col);
    d||(d=this.env.sort_order);
    if(this.env.sort_col!=b||this.env.sort_order!=d)f=1,this.set_list_sorting(b,d);
    this.env.threading!=e&&(f=1,h+="&_threads="+e);
    if(a&&a.length){
        for(var g,k,j=[],i=this.env.coltypes,e=0;e<i.length;e++)k="to"==i[e]?"from":i[e],g=$.inArray(k,a),-1!=g&&(j.push(k),delete a[g]);
        for(e=0;e<a.length;e++)a[e]&&j.push(a[e]);
        j.join()!=i.join()&&(f=1,h+="&_cols="+j.join(","))
        }
        f&&this.list_mailbox("","",b+"_"+d,h)
    };
    
this.show_message=function(a,
    b,d){
    if(a){
        var e=window,f=d?"preview":"show",h="&_action="+f+"&_uid="+a+"&_mbox="+urlencode(this.env.mailbox);
        d&&this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(e=window.frames[this.env.contentframe],h+="&_framed=1");
        b&&(h+="&_safe=1");
        this.env.search_request&&(h+="&_search="+this.env.search_request);
        "preview"==f&&0<=(""+e.location.href).indexOf(h)?this.show_contentframe(!0):(this.location_href(this.env.comm_path+h,e,!0),"preview"==f&&this.message_list&&this.message_list.rows[a]&&
            this.message_list.rows[a].unread&&0<=this.env.preview_pane_mark_read&&(this.preview_read_timer=window.setTimeout(function(){
                i.set_message(a,"unread",false);
                i.update_thread_root(a,"read");
                if(i.env.unread_counts[i.env.mailbox]){
                    i.env.unread_counts[i.env.mailbox]=i.env.unread_counts[i.env.mailbox]-1;
                    i.set_unread_count(i.env.mailbox,i.env.unread_counts[i.env.mailbox],i.env.mailbox=="INBOX")
                    }
                    i.env.preview_pane_mark_read>0&&i.http_post("mark","_uid="+a+"&_flag=read&_quiet=1")
                },1E3*this.env.preview_pane_mark_read)))
        }
    };
this.show_contentframe=function(a){
    var b,d;
    if(this.env.contentframe&&(b=$("#"+this.env.contentframe))&&b.length)if(!a&&(d=window.frames[this.env.contentframe]))d.location&&0>d.location.href.indexOf(this.env.blankpage)&&(d.location.href=this.env.blankpage);
        else if(!bw.safari&&!bw.konq)b[a?"show":"hide"]();
    !a&&this.busy&&this.set_busy(!1,null,this.env.frame_lock)
    };
    
this.lock_frame=function(){
    this.env.frame_lock||((this.is_framed()?parent.rcmail:this).env.frame_lock=this.set_busy(!0,"loading"))
    };
    
this.list_page=
function(a){
    "next"==a?a=this.env.current_page+1:"last"==a?a=this.env.pagecount:"prev"==a&&1<this.env.current_page?a=this.env.current_page-1:"first"==a&&1<this.env.current_page&&(a=1);
    0<a&&a<=this.env.pagecount&&(this.env.current_page=a,"mail"==this.task?this.list_mailbox(this.env.mailbox,a):"addressbook"==this.task&&this.list_contacts(this.env.source,this.env.group,a))
    };
    
this.filter_mailbox=function(a){
    var b=this.set_busy(!0,"searching");
    this.clear_message_list();
    this.env.current_page=1;
    this.http_request("search",
        this.search_params(!1,a),b)
    };
    
this.list_mailbox=function(a,b,d,e){
    var f="",h=window;
    a||(a=this.env.mailbox?this.env.mailbox:"INBOX");
    e&&(f+=e);
    d&&(f+="&_sort="+d);
    this.env.search_request&&(f+="&_search="+this.env.search_request);
    this.env.mailbox!=a&&(b=1,this.env.current_page=b,this.select_all_mode=!1);
    this.clear_message_list();
    if(a!=this.env.mailbox||a==this.env.mailbox&&!b&&!d)f+="&_refresh=1";
    this.select_folder(a,"",!0);
    this.env.mailbox=a;
    if(this.gui_objects.messagelist)this.list_mailbox_remote(a,
        b,f);
    else if(this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(h=window.frames[this.env.contentframe],f+="&_framed=1"),a)this.set_busy(!0,"loading"),this.location_href(this.env.comm_path+"&_mbox="+urlencode(a)+(b?"&_page="+b:"")+f,h)
        };
        
this.clear_message_list=function(){
    this.env.messages={};
    
    this.last_selected=0;
    this.show_contentframe(!1);
    this.message_list&&this.message_list.clear(!0)
    };
    
this.list_mailbox_remote=function(a,b,d){
    this.message_list.clear();
    a="_mbox="+urlencode(a)+
    (b?"&_page="+b:"");
    b=this.set_busy(!0,"loading");
    this.http_request("list",a+d,b)
    };
    
this.update_selection=function(){
    var a=this.message_list.selection,b=this.message_list.rows,d,e=[];
    for(d in a)b[a[d]]&&e.push(a[d]);this.message_list.selection=e
    };
    
this.expand_unread=function(){
    for(var a,b=this.gui_objects.messagelist.tBodies[0].firstChild;b;){
        if(1==b.nodeType&&(a=this.message_list.rows[b.uid])&&a.unread_children)this.message_list.expand_all(a),this.set_unread_children(a.uid);
        b=b.nextSibling
        }
        return!1
    };
this.expand_message_row=function(a,b){
    var d=this.message_list.rows[b];
    d.expanded=!d.expanded;
    this.set_unread_children(b);
    d.expanded=!d.expanded;
    this.message_list.expand_row(a,b)
    };
    
this.expand_threads=function(){
    if(this.env.threading&&this.env.autoexpand_threads&&this.message_list)switch(this.env.autoexpand_threads){
        case 2:
            this.expand_unread();
            break;
        case 1:
            this.message_list.expand_all()
            }
        };
    
this.init_threads=function(a,b){
    if(b&&b!=this.env.mailbox)return!1;
    for(var d=0,e=a.length;d<e;d++)this.add_tree_icons(a[d]);
    this.expand_threads()
    };
    
this.add_tree_icons=function(a){
    var b,d,e,f,h=[],g=[],k,j=this.message_list.rows;
    for(k=a?j[a]?j[a].obj:null:this.message_list.list.tBodies[0].firstChild;k;){
        if(1==k.nodeType&&(d=j[k.uid]))if(d.depth){
            for(b=h.length-1;0<=b&&!(e=h[b].length,e>d.depth?(f=e-d.depth,h[b][f]&2||(h[b][f]=h[b][f]?h[b][f]+2:2)):e==d.depth&&(h[b][0]&2||(h[b][0]+=2)),d.depth>e);b--);
            h.push(Array(d.depth));
            h[h.length-1][0]=1;
            g.push(d.uid)
            }else{
            if(h.length){
                for(b in h)this.set_tree_icons(g[b],h[b]);h=[];
                g=[]
                }
                if(a&&k!=j[a].obj)break
        }
        k=k.nextSibling
        }
        if(h.length)for(b in h)this.set_tree_icons(g[b],h[b])
        };
        
this.set_tree_icons=function(a,b){
    var d,e=[],f="",h=b.length;
    for(d=0;d<h;d++)2<b[d]?e.push({
        "class":"l3",
        width:15
    }):1<b[d]?e.push({
        "class":"l2",
        width:15
    }):0<b[d]?e.push({
        "class":"l1",
        width:15
    }):e.length&&!e[e.length-1]["class"]?e[e.length-1].width+=15:e.push({
        "class":null,
        width:15
    });
    for(d=e.length-1;0<=d;d--)f=e[d]["class"]?f+('<div class="tree '+e[d]["class"]+'" />'):f+('<div style="width:'+e[d].width+
        'px" />');
    f&&$("#rcmtab"+a).html(f)
    };
    
this.update_thread_root=function(a,b){
    if(this.env.threading){
        var d=this.message_list.find_root(a);
        if(a!=d){
            var e=this.message_list.rows[d];
            if("read"==b&&e.unread_children)e.unread_children--;
            else if("unread"==b&&e.has_children)e.unread_children=e.unread_children?e.unread_children+1:1;else return;
            this.set_message_icon(d);
            this.set_unread_children(d)
            }
        }
};

this.update_thread=function(a){
    if(!this.env.threading)return 0;
    var b,d=0,e=this.message_list.rows,f=e[a],h=e[a].depth,
    g=[];
    f.depth?f.unread&&(a=this.message_list.find_root(a),e[a].unread_children--,this.set_unread_children(a)):d--;
    a=f.parent_uid;
    for(f=f.obj.nextSibling;f;){
        if(1==f.nodeType&&(b=e[f.uid])){
            if(!b.depth||b.depth<=h)break;
            b.depth--;
            $("#rcmtab"+b.uid).width(15*b.depth).html("");
            b.depth?(b.depth==h&&(b.parent_uid=a),b.unread&&g.length&&g[g.length-1].unread_children++):(d++,b.parent_uid=0,b.has_children&&($("#rcmrow"+b.uid+" .leaf:first").attr("id","rcmexpando"+b.uid).attr("class","none"!=b.obj.style.display?
                "expanded":"collapsed").bind("mousedown",{
                uid:b.uid,
                p:this
            },function(a){
                return a.data.p.expand_message_row(a,a.data.uid)
                }),b.unread_children=0,g.push(b)),"none"==b.obj.style.display&&$(b.obj).show())
            }
            f=f.nextSibling
        }
        for(b=0;b<g.length;b++)this.set_unread_children(g[b].uid);
    return d
    };
    
this.delete_excessive_thread_rows=function(){
    for(var a=this.message_list.rows,b=this.message_list.list.tBodies[0].firstChild,d=this.env.pagesize+1;b;){
        if(1==b.nodeType&&(r=a[b.uid]))!r.depth&&d&&d--,d||this.message_list.remove_row(b.uid);
        b=b.nextSibling
        }
    };
    
this.set_message_icon=function(a){
    var b=this.message_list.rows[a];
    if(!b)return!1;
    b.icon&&(a="msgicon",b.deleted?a+=" deleted":b.unread?a+=" unread":b.unread_children&&(a+=" unreadchildren"),b.msgicon==b.icon&&(b.replied&&(a+=" replied"),b.forwarded&&(a+=" forwarded"),a+=" status"),b.icon.className=a);
    b.msgicon&&b.msgicon!=b.icon&&(a="msgicon",!b.unread&&b.unread_children&&(a+=" unreadchildren"),b.replied&&(a+=" replied"),b.forwarded&&(a+=" forwarded"),b.msgicon.className=a);
    b.flagicon&&
    (a=b.flagged?"flagged":"unflagged",b.flagicon.className=a)
    };
    
this.set_message_status=function(a,b,d){
    a=this.message_list.rows[a];
    if(!a)return!1;
    "unread"==b?a.unread=d:"deleted"==b?a.deleted=d:"replied"==b?a.replied=d:"forwarded"==b?a.forwarded=d:"flagged"==b&&(a.flagged=d)
    };
    
this.set_message=function(a,b,d){
    var e=this.message_list.rows[a];
    if(!e)return!1;
    b&&this.set_message_status(a,b,d);
    b=$(e.obj);
    e.unread&&!b.hasClass("unread")?b.addClass("unread"):!e.unread&&b.hasClass("unread")&&b.removeClass("unread");
    e.deleted&&!b.hasClass("deleted")?b.addClass("deleted"):!e.deleted&&b.hasClass("deleted")&&b.removeClass("deleted");
    e.flagged&&!b.hasClass("flagged")?b.addClass("flagged"):!e.flagged&&b.hasClass("flagged")&&b.removeClass("flagged");
    this.set_unread_children(a);
    this.set_message_icon(a)
    };
    
this.set_unread_children=function(a){
    a=this.message_list.rows[a];
    a.parent_uid||(!a.unread&&a.unread_children&&!a.expanded?$(a.obj).addClass("unroot"):$(a.obj).removeClass("unroot"))
    };
    
this.copy_messages=function(a){
    a&&
    "object"===typeof a&&(a=a.id);
    if(a&&!(a==this.env.mailbox||!this.env.uid&&(!this.message_list||!this.message_list.get_selection().length))){
        var b=[],d=this.display_message(this.get_label("copyingmessage"),"loading"),a="&_target_mbox="+urlencode(a)+"&_from="+(this.env.action?this.env.action:"");
        if(this.env.uid)b[0]=this.env.uid;
        else{
            var e=this.message_list.get_selection(),f;
            for(f in e)b.push(e[f])
                }
                a+="&_uid="+this.uids_to_list(b);
        this.http_post("copy","_mbox="+urlencode(this.env.mailbox)+a,d)
        }
    };

this.move_messages=
function(a){
    a&&"object"===typeof a&&(a=a.id);
    if(a&&!(a==this.env.mailbox||!this.env.uid&&(!this.message_list||!this.message_list.get_selection().length))){
        var b=!1,a="&_target_mbox="+urlencode(a)+"&_from="+(this.env.action?this.env.action:"");
        "show"==this.env.action?b=this.set_busy(!0,"movingmessage"):this.show_contentframe(!1);
        this.enable_command(this.env.message_commands,!1);
        this._with_selected_messages("moveto",b,a)
        }
    };

this.delete_messages=function(){
    var a,b,d,e=this.env.trash_mailbox,f=this.message_list,
    h=f?$.merge([],f.get_selection()):[];
    if(this.env.uid||h.length){
        b=0;
        for(d=h.length;b<d;b++)a=h[b],f.rows[a].has_children&&!f.rows[a].expanded&&f.select_childs(a);
        if(this.env.flag_for_deletion)return this.mark_message("delete"),!1;
        !e||this.env.mailbox==e?this.permanently_remove_messages():f&&f.modkey==SHIFT_KEY?confirm(this.get_label("deletemessagesconfirm"))&&this.permanently_remove_messages():this.move_messages(e);
        return!0
        }
    };

this.permanently_remove_messages=function(){
    if(this.env.uid||this.message_list&&
        this.message_list.get_selection().length)this.show_contentframe(!1),this._with_selected_messages("delete",!1,"&_from="+(this.env.action?this.env.action:""))
        };
        
this._with_selected_messages=function(a,b,d){
    var e=[],f=0;
    if(this.env.uid)e[0]=this.env.uid;
    else{
        var h,g,k,j=[],i=this.message_list.get_selection();
        h=0;
        for(len=i.length;h<len;h++)g=i[h],e.push(g),this.env.threading&&(f+=this.update_thread(g),k=this.message_list.find_root(g),k!=g&&0>$.inArray(k,j)&&j.push(k)),this.message_list.remove_row(g,this.env.display_next&&
            h==i.length-1);
        this.env.display_next||this.message_list.clear_selection();
        h=0;
        for(len=j.length;h<len;h++)this.add_tree_icons(j[h])
            }
            this.env.search_request&&(d+="&_search="+this.env.search_request);
    this.env.display_next&&this.env.next_uid&&(d+="&_next_uid="+this.env.next_uid);
    0>f?d+="&_count="+-1*f:0<f&&this.delete_excessive_thread_rows();
    d+="&_uid="+this.uids_to_list(e);
    b||(b=this.display_message(this.get_label("moveto"==a?"movingmessage":"deletingmessage"),"loading"));
    this.http_post(a,"_mbox="+urlencode(this.env.mailbox)+
        d,b)
    };
    
this.mark_message=function(a,b){
    var d=[],e=[],f,h,g;
    g=this.message_list?this.message_list.get_selection():[];
    if(b)d[0]=b;
    else if(this.env.uid)d[0]=this.env.uid;
    else if(this.message_list){
        h=0;
        for(f=g.length;h<f;h++)d.push(g[h])
            }
            if(this.message_list){
        h=0;
        for(f=d.length;h<f;h++)g=d[h],("read"==a&&this.message_list.rows[g].unread||"unread"==a&&!this.message_list.rows[g].unread||"delete"==a&&!this.message_list.rows[g].deleted||"undelete"==a&&this.message_list.rows[g].deleted||"flagged"==a&&!this.message_list.rows[g].flagged||
            "unflagged"==a&&this.message_list.rows[g].flagged)&&e.push(g)
            }else e=d;
    if(e.length||this.select_all_mode)switch(a){
        case "read":case "unread":
            this.toggle_read_status(a,e);
            break;
        case "delete":case "undelete":
            this.toggle_delete_status(e);
            break;
        case "flagged":case "unflagged":
            this.toggle_flagged_status(a,d)
            }
        };
    
this.toggle_read_status=function(a,b){
    var d,e=b.length,f="_uid="+this.uids_to_list(b)+"&_flag="+a,h=this.display_message(this.get_label("markingmessage"),"loading");
    for(d=0;d<e;d++)this.set_message(b[d],
        "unread","unread"==a?!0:!1);
    this.env.search_request&&(f+="&_search="+this.env.search_request);
    this.http_post("mark",f,h);
    for(d=0;d<e;d++)this.update_thread_root(b[d],a)
        };
        
this.toggle_flagged_status=function(a,b){
    var d,e=b.length,f="_uid="+this.uids_to_list(b)+"&_flag="+a,h=this.display_message(this.get_label("markingmessage"),"loading");
    for(d=0;d<e;d++)this.set_message(b[d],"flagged","flagged"==a?!0:!1);
    this.env.search_request&&(f+="&_search="+this.env.search_request);
    this.http_post("mark",f,h)
    };
    
this.toggle_delete_status=
function(a){
    var b=a.length,d,e,f=!0,h=this.message_list?this.message_list.rows:[];
    if(1==b)return!h.length||h[a[0]]&&!h[a[0]].deleted?this.flag_as_deleted(a):this.flag_as_undeleted(a),!0;
    for(d=0;d<b;d++)if(e=a[d],h[e]&&!h[e].deleted){
        f=!1;
        break
    }
    f?this.flag_as_undeleted(a):this.flag_as_deleted(a);
    return!0
    };
    
this.flag_as_undeleted=function(a){
    var b,d=a.length,e="_uid="+this.uids_to_list(a)+"&_flag=undelete",f=this.display_message(this.get_label("markingmessage"),"loading");
    for(b=0;b<d;b++)this.set_message(a[b],
        "deleted",!1);
    this.env.search_request&&(e+="&_search="+this.env.search_request);
    this.http_post("mark",e,f);
    return!0
    };
    
this.flag_as_deleted=function(a){
    for(var b="",d=[],b=this.message_list?this.message_list.rows:[],e=0,f=0,h=a.length;f<h;f++)uid=a[f],b[uid]&&(b[uid].unread&&(d[d.length]=uid),this.env.skip_deleted?(e+=this.update_thread(uid),this.message_list.remove_row(uid,this.env.display_next&&f==this.message_list.selection.length-1)):this.set_message(uid,"deleted",!0));
    this.env.skip_deleted&&this.message_list&&
    (this.env.display_next||this.message_list.clear_selection(),0>e||0<e&&this.delete_excessive_thread_rows());
    b="&_from="+(this.env.action?this.env.action:"");
    lock=this.display_message(this.get_label("markingmessage"),"loading");
    d.length&&(b+="&_ruid="+this.uids_to_list(d));
    this.env.skip_deleted&&this.env.display_next&&this.env.next_uid&&(b+="&_next_uid="+this.env.next_uid);
    this.env.search_request&&(b+="&_search="+this.env.search_request);
    this.http_post("mark","_uid="+this.uids_to_list(a)+"&_flag=delete"+
        b,lock);
    return!0
    };
    
this.flag_deleted_as_read=function(a){
    var b,d,e,f=this.message_list?this.message_list.rows:[],a=(""+a).split(",");
    d=0;
    for(e=a.length;d<e;d++)b=a[d],f[b]&&this.set_message(b,"unread",!1)
        };
        
this.uids_to_list=function(a){
    return this.select_all_mode?"*":a.join(",")
    };
    
this.expunge_mailbox=function(a){
    var b,d="_mbox="+urlencode(a);
    a==this.env.mailbox&&(b=this.set_busy(!0,"loading"),d+="&_reload=1",this.env.search_request&&(d+="&_search="+this.env.search_request));
    this.http_post("expunge",
        d,b)
    };
    
this.purge_mailbox=function(a){
    var b=!1,d="_mbox="+urlencode(a);
    if(!confirm(this.get_label("purgefolderconfirm")))return!1;
    a==this.env.mailbox&&(b=this.set_busy(!0,"loading"),d+="&_reload=1");
    this.http_post("purge",d,b)
    };
    
this.purge_mailbox_test=function(){
    return this.env.messagecount&&(this.env.mailbox==this.env.trash_mailbox||this.env.mailbox==this.env.junk_mailbox||this.env.mailbox.match("^"+RegExp.escape(this.env.trash_mailbox)+RegExp.escape(this.env.delimiter))||this.env.mailbox.match("^"+
        RegExp.escape(this.env.junk_mailbox)+RegExp.escape(this.env.delimiter)))
    };
    
this.login_user_keyup=function(a){
    var b=rcube_event.get_keycode(a),d=$("#rcmloginpwd");
    return 13==b&&d.length&&!d.val()?(d.focus(),rcube_event.cancel(a)):!0
    };
    
this.init_messageform=function(){
    if(!this.gui_objects.messageform)return!1;
    var a=$("[name='_from']"),b=$("[name='_to']"),d=$("input[name='_subject']"),e=$("[name='_message']").get(0),f="1"==$("input[name='_is_html']").val(),h=["cc","bcc","replyto","followupto"],g;
    0<this.env.autocomplete_threads&&
    (g={
        threads:this.env.autocomplete_threads,
        sources:this.env.autocomplete_sources
        });
    this.init_address_input_events(b,g);
    for(var k in h)this.init_address_input_events($("[name='_"+h[k]+"']"),g);f||(this.set_caret_pos(e,this.env.top_posting?0:$(e).val().length),"select-one"==a.prop("type")&&""==$("input[name='_draft_saveid']").val()&&this.change_identity(a[0]));
    ""==b.val()?b.focus():""==d.val()?d.focus():e&&e.focus();
    this.env.compose_focus_elem=document.activeElement;
    this.compose_field_hash(!0);
    this.auto_save_start()
    };
this.init_address_input_events=function(a,b){
    this.env.recipients_delimiter=this.env.recipients_separator+" ";
    a[bw.ie||bw.safari||bw.chrome?"keydown":"keypress"](function(a){
        return i.ksearch_keydown(a,this,b)
        }).attr("autocomplete","off")
    };
    
this.check_compose_input=function(a){
    var b,d=$("[name='_to']"),e=$("[name='_cc']"),f=$("[name='_bcc']"),h=$("[name='_from']"),g=$("[name='_subject']"),k=$("[name='_message']");
    if("text"==h.prop("type")&&!rcube_check_email(h.val(),!0))return alert(this.get_label("nosenderwarning")),
        h.focus(),!1;
    e=d.val()?d.val():e.val()?e.val():f.val();
    if(!rcube_check_email(e.replace(/^\s+/,"").replace(/[\s,;]+$/,""),!0))return alert(this.get_label("norecipientwarning")),d.focus(),!1;
    for(var j in this.env.attachments)if("object"===typeof this.env.attachments[j]&&!this.env.attachments[j].complete)return alert(this.get_label("notuploadedwarning")),!1;if(""==g.val()){
        b=$('<div class="prompt">').html('<div class="message">'+this.get_label("nosubjectwarning")+"</div>").appendTo(document.body);
        var l=
        $("<input>").attr("type","text").attr("size",30).appendTo(b).val(this.get_label("nosubject")),d={};
        
        d[this.get_label("cancel")]=function(){
            g.focus();
            $(this).dialog("close")
            };
            
        d[this.get_label("sendmessage")]=function(){
            g.val(l.val());
            $(this).dialog("close");
            i.command(a,{
                nocheck:!0
                })
            };
            
        b.dialog({
            modal:!0,
            resizable:!1,
            buttons:d,
            close:function(){
                $(this).remove()
                }
            });
    l.select();
    return!1
    }
    this.stop_spellchecking();
window.tinyMCE&&(b=tinyMCE.get(this.env.composebody));
if(!b&&""==k.val()&&!confirm(this.get_label("nobodywarning")))return k.focus(),
    !1;
if(b){
    if(!b.getContent()&&!confirm(this.get_label("nobodywarning")))return b.focus(),!1;
    tinyMCE.triggerSave()
    }
    return!0
};

this.toggle_editor=function(a){
    if("html"==a.mode)this.display_spellcheck_controls(!1),this.plain2html($("#"+a.id).val(),a.id),tinyMCE.execCommand("mceAddControl",!1,a.id);
    else{
        var b=tinyMCE.get(a.id);
        b.plugins.spellchecker&&b.plugins.spellchecker.active&&b.execCommand("mceSpellCheck",!1);
        if(b=b.getContent()){
            if(!confirm(this.get_label("editorwarning")))return!1;
            this.html2plain(b,
                a.id)
            }
            tinyMCE.execCommand("mceRemoveControl",!1,a.id);
        this.display_spellcheck_controls(!0)
        }
        return!0
    };
    
this.stop_spellchecking=function(){
    var a;
    if(window.tinyMCE&&(a=tinyMCE.get(this.env.composebody)))a.plugins.spellchecker&&a.plugins.spellchecker.active&&a.execCommand("mceSpellCheck");
    else if((a=this.env.spellcheck)&&!this.spellcheck_ready)$(a.spell_span).trigger("click"),this.set_spellcheck_state("ready")
        };
        
this.display_spellcheck_controls=function(a){
    this.env.spellcheck&&(a||this.stop_spellchecking(),
        $(this.env.spellcheck.spell_container)[a?"show":"hide"]())
    };
    
this.set_spellcheck_state=function(a){
    this.spellcheck_ready="ready"==a||"no_error_found"==a;
    this.enable_command("spellcheck",this.spellcheck_ready)
    };
    
this.spellcheck_lang=function(){
    var a;
    if(window.tinyMCE&&(a=tinyMCE.get(this.env.composebody))&&a.plugins.spellchecker)return a.plugins.spellchecker.selectedLang;
    if(this.env.spellcheck)return GOOGIE_CUR_LANG
        };
        
this.spellcheck_resume=function(a,b){
    if(a){
        var d=tinyMCE.get(this.env.composebody),
        e=d.plugins.spellchecker;
        e.active=1;
        e._markWords(b);
        d.nodeChanged()
        }else{
        var e=this.env.spellcheck;
        e.prepare(!1,!0);
        e.processData(b)
        }
    };

this.set_draft_id=function(a){
    $("input[name='_draft_saveid']").val(a)
    };
    
this.auto_save_start=function(){
    this.env.draft_autosave&&(this.save_timer=self.setTimeout(function(){
        i.command("savedraft")
        },1E3*this.env.draft_autosave));
    this.busy=!1
    };
    
this.compose_field_hash=function(a){
    var b,d="",e=$("[name='_to']").val(),f=$("[name='_cc']").val(),h=$("[name='_bcc']").val(),
    g=$("[name='_subject']").val();
    e&&(d+=e+":");
    f&&(d+=f+":");
    h&&(d+=h+":");
    g&&(d+=g+":");
    d=window.tinyMCE&&(b=tinyMCE.get(this.env.composebody))?d+b.getContent():d+$("[name='_message']").val();
    if(this.env.attachments)for(var k in this.env.attachments)d+=k;a&&(this.cmp_hash=d);
    return d
    };
    
this.change_identity=function(a,b){
    if(!a||!a.options)return!1;
    b||(b=this.env.show_sig);
    var d,e=-1,f=a.options[a.selectedIndex].value,h=$("[name='_message']"),g=h.val(),k="1"==$("input[name='_is_html']").val(),j=this.env.identity;
    d=this.env.sig_above&&("reply"==this.env.compose_mode||"forward"==this.env.compose_mode)?"---":"-- ";
    this.env.signatures&&this.env.signatures[f]?(this.enable_command("insert-sig",!0),this.env.compose_commands.push("insert-sig")):this.enable_command("insert-sig",!1);
    if(k){
        if(b&&this.env.signatures&&(e=tinyMCE.get(this.env.composebody),h=e.dom.get("_rc_sig"),h||(j=e.getBody(),g=e.getDoc(),h=g.createElement("div"),h.setAttribute("id","_rc_sig"),this.env.sig_above?(e.getWin().focus(),e=e.selection.getNode(),
            "BODY"==e.nodeName?(j.insertBefore(h,j.firstChild),j.insertBefore(g.createElement("br"),j.firstChild)):(j.insertBefore(h,e.nextSibling),j.insertBefore(g.createElement("br"),e.nextSibling))):(bw.ie&&j.appendChild(g.createElement("br")),j.appendChild(h))),this.env.signatures[f]))this.env.signatures[f].is_html?(j=this.env.signatures[f].text,this.env.signatures[f].plain_text.match(/^--[ -]\r?\n/m)||(j=d+"<br />"+j)):(j=this.env.signatures[f].text,j.match(/^--[ -]\r?\n/m)||(j=d+"\n"+j),j="<pre>"+j+"</pre>"),
            h.innerHTML=j
            }else b&&j&&this.env.signatures&&this.env.signatures[j]&&(j=this.env.signatures[j].is_html?this.env.signatures[j].plain_text:this.env.signatures[j].text,j=j.replace(/\r\n/g,"\n"),j.match(/^--[ -]\n/m)||(j=d+"\n"+j),e=this.env.sig_above?g.indexOf(j):g.lastIndexOf(j),0<=e&&(g=g.substring(0,e)+g.substring(e+j.length,g.length))),b&&this.env.signatures&&this.env.signatures[f]?(j=this.env.signatures[f].is_html?this.env.signatures[f].plain_text:this.env.signatures[f].text,j=j.replace(/\r\n/g,
        "\n"),j.match(/^--[ -]\n/m)||(j=d+"\n"+j),this.env.sig_above?0<=e?(g=g.substring(0,e)+j+g.substring(e,g.length),d=e-1):(pos=this.get_caret_pos(h.get(0)))?(g=g.substring(0,pos)+"\n"+j+"\n\n"+g.substring(pos,g.length),d=pos):(d=0,g="\n\n"+j+"\n\n"+g.replace(/^[\r\n]+/,"")):(g=g.replace(/[\r\n]+$/,""),d=!this.env.top_posting&&g.length?g.length+1:0,g+="\n\n"+j)):d=this.env.top_posting?0:g.length,h.val(g),this.set_caret_pos(h.get(0),d);
    this.env.identity=f;
    return!0
    };
    
this.upload_file=function(a){
    if(!a)return!1;
    var b,d=0,e=$("input[type=file]",a).get(0),f=e.files?e.files.length:e.value?1:0;
    if(f){
        if(e.files&&this.env.max_filesize&&this.env.filesizeerror){
            for(b=0;b<f;b++)d+=e.files[b].size;
            if(d&&d>this.env.max_filesize){
                this.display_message(this.env.filesizeerror,"error");
                return
            }
        }
        b=this.async_upload_form(a,"upload",function(a){
        var b,d="";
        try{
            this.contentDocument?b=this.contentDocument:this.contentWindow&&(b=this.contentWindow.document),d=b.childNodes[0].innerHTML
            }catch(e){}
        if(!d.match(/add2attachment/)&&(!bw.opera||
            rcmail.env.uploadframe&&rcmail.env.uploadframe==a.data.ts))d.match(/display_message/)||rcmail.display_message(rcmail.get_label("fileuploaderror"),"error"),rcmail.remove_from_attachment_list(a.data.ts);
        bw.opera&&(rcmail.env.uploadframe=a.data.ts)
        });
    f="<span>"+this.get_label("uploading"+(1<f?"many":""))+"</span>";
    d=b.replace(/^rcmupload/,"");
    this.env.loadingicon&&(f='<img src="'+this.env.loadingicon+'" alt="" />'+f);
    this.env.cancelicon&&(f='<a title="'+this.get_label("cancel")+'" onclick="return rcmail.cancel_attachment_upload(\''+
        d+"', '"+b+'\');" href="#cancelupload"><img src="'+this.env.cancelicon+'" alt="" /></a>'+f);
    this.add2attachment_list(d,{
        name:"",
        html:f,
        complete:!1
        });
    this.env.upload_progress_time&&this.upload_progress_start("upload",d)
    }
    this.gui_objects.attachmentform=a;
return!0
};

this.add2attachment_list=function(a,b,d){
    if(!this.gui_objects.attachmentlist)return!1;
    var e,f=$("<li>").attr("id",a).html(b.html);
    d&&(e=document.getElementById(d))?f.replaceAll(e):f.appendTo(this.gui_objects.attachmentlist);
    d&&this.env.attachments[d]&&
    delete this.env.attachments[d];
    this.env.attachments[a]=b;
    return!0
    };
    
this.remove_from_attachment_list=function(a){
    delete this.env.attachments[a];
    $("#"+a).remove()
    };
    
this.remove_attachment=function(a){
    a&&this.env.attachments[a]&&this.http_post("remove-attachment",{
        _id:this.env.compose_id,
        _file:a
    });
    return!0
    };
    
this.cancel_attachment_upload=function(a,b){
    if(!a||!b)return!1;
    this.remove_from_attachment_list(a);
    $("iframe[name='"+b+"']").remove();
    return!1
    };
    
this.upload_progress_start=function(a,b){
    window.setTimeout(function(){
        rcmail.http_request(a,

        {
            _progress:b
        })
        },1E3*this.env.upload_progress_time)
    };
    
this.upload_progress_update=function(a){
    var b=$("#"+a.name+"> span");
    b.length&&a.text&&(b.text(a.text),a.done||this.upload_progress_start(a.action,a.name))
    };
    
this.add_contact=function(a){
    a&&this.http_post("addcontact","_address="+a);
    return!0
    };
    
this.qsearch=function(a){
    if(""!=a){
        var b=this.set_busy(!0,"searching");
        this.message_list?this.clear_message_list():this.contact_list&&this.list_contacts_clear();
        this.env.current_page=1;
        r=this.http_request("search",
            this.search_params(a)+(this.env.source?"&_source="+urlencode(this.env.source):"")+(this.env.group?"&_gid="+urlencode(this.env.group):""),b);
        this.env.qsearch={
            lock:b,
            request:r
        }
    }
};

this.search_params=function(a,b){
    var d,e=[],f=[],h=this.env.search_mods,g=this.env.mailbox;
    !b&&this.gui_objects.search_filter&&(b=this.gui_objects.search_filter.value);
    !a&&this.gui_objects.qsearchbox&&(a=this.gui_objects.qsearchbox.value);
    b&&e.push("_filter="+urlencode(b));
    if(a&&(e.push("_q="+urlencode(a)),h&&this.message_list&&
        (h=h[g]?h[g]:h["*"]),h)){
        for(d in h)f.push(d);e.push("_headers="+f.join(","))
        }
        g&&e.push("_mbox="+urlencode(g));
    return e.join("&")
    };
    
this.reset_qsearch=function(){
    this.gui_objects.qsearchbox&&(this.gui_objects.qsearchbox.value="");
    this.env.qsearch&&this.abort_request(this.env.qsearch);
    this.env.qsearch=null;
    this.env.search_request=null;
    this.env.search_id=null
    };
    
this.sent_successfully=function(a,b){
    this.display_message(b,a);
    window.setTimeout(function(){
        i.list_mailbox()
        },500)
    };
    
this.ksearch_keydown=function(a,
    b,d){
    this.ksearch_timer&&clearTimeout(this.ksearch_timer);
    var e=rcube_event.get_keycode(a),f=rcube_event.get_modifier(a);
    switch(e){
        case 38:case 40:
            if(!this.ksearch_pane)break;
            e=38==e?1:0;
            b=document.getElementById("rcmksearchSelected");
            b||(b=this.ksearch_pane.__ul.firstChild);
            b&&this.ksearch_select(e?b.previousSibling:b.nextSibling);
            return rcube_event.cancel(a);
        case 9:
            if(f==SHIFT_KEY||!this.ksearch_visible()){
            this.ksearch_hide();
            return
        }
        case 13:
            if(!this.ksearch_visible())return!1;
            this.insert_recipient(this.ksearch_selected);
            this.ksearch_hide();
            return rcube_event.cancel(a);
        case 27:
            this.ksearch_hide();
            return;
        case 37:case 39:
            if(f!=SHIFT_KEY)return
            }
            this.ksearch_timer=window.setTimeout(function(){
        i.ksearch_get_results(d)
        },200);
    this.ksearch_input=b;
    return!0
    };
    
this.ksearch_visible=function(){
    return null!==this.ksearch_selected&&void 0!==this.ksearch_selected&&this.ksearch_value
    };
    
this.ksearch_select=function(a){
    var b=$("#rcmksearchSelected");
    b[0]&&a&&b.removeAttr("id").removeClass("selected");
    a&&($(a).attr("id","rcmksearchSelected").addClass("selected"),
        this.ksearch_selected=a._rcm_id)
    };
    
this.insert_recipient=function(a){
    if(!(null===a||!this.env.contacts[a]||!this.ksearch_input)){
        var b=this.ksearch_input.value,d=this.get_caret_pos(this.ksearch_input),d=b.lastIndexOf(this.ksearch_value,d),e=!1,f="",h=b.substring(0,d),b=b.substring(d+this.ksearch_value.length,b.length);
        this.ksearch_destroy();
        "object"===typeof this.env.contacts[a]&&this.env.contacts[a].id?(f+=this.env.contacts[a].name+this.env.recipients_delimiter,this.group2expand=$.extend({},this.env.contacts[a]),
            this.group2expand.input=this.ksearch_input,this.http_request("mail/group-expand","_source="+urlencode(this.env.contacts[a].source)+"&_gid="+urlencode(this.env.contacts[a].id),!1)):"string"===typeof this.env.contacts[a]&&(f=this.env.contacts[a]+this.env.recipients_delimiter,e=!0);
        this.ksearch_input.value=h+f+b;
        d+=f.length;
        this.ksearch_input.setSelectionRange&&this.ksearch_input.setSelectionRange(d,d);
        e&&this.triggerEvent("autocomplete_insert",{
            field:this.ksearch_input,
            insert:f
        })
        }
    };

this.replace_group_recipients=
function(a,b){
    this.group2expand&&this.group2expand.id==a&&(this.group2expand.input.value=this.group2expand.input.value.replace(this.group2expand.name,b),this.triggerEvent("autocomplete_insert",{
        field:this.group2expand.input,
        insert:b
    }),this.group2expand=null)
    };
    
this.ksearch_get_results=function(a){
    var b=this.ksearch_input?this.ksearch_input.value:null;
    if(null!==b){
        this.ksearch_pane&&this.ksearch_pane.is(":visible")&&this.ksearch_pane.hide();
        var d=this.get_caret_pos(this.ksearch_input),e=b.lastIndexOf(this.env.recipients_separator,
            d-1),b=b.substring(e+1,d),e=this.env.autocomplete_min_length,d=this.ksearch_data,b=$.trim(b);
        if(b!=this.ksearch_value)if(this.ksearch_destroy(),b.length&&b.length<e)this.ksearch_info||(this.ksearch_info=this.display_message(this.get_label("autocompletechars").replace("$min",e)));
            else if(e=this.ksearch_value,this.ksearch_value=b,b.length&&(!e||!e.length||!(0==b.indexOf(e)&&(!d||!d.num)&&this.env.contacts&&!this.env.contacts.length))){
            var f,h,g,d=(new Date).getTime(),e=a&&a.threads?a.threads:1;
            f=a&&
            a.sources?a.sources:[];
            a=a&&a.action?a.action:"mail/autocomplete";
            this.ksearch_data={
                id:d,
                sources:f.slice(),
                action:a,
                locks:[],
                requests:[],
                num:f.length
                };
                
            for(f=0;f<e;f++){
                g=this.ksearch_data.sources.shift();
                if(1<e&&null===g)break;
                h=this.display_message(this.get_label("searching"),"loading");
                g=this.http_post(a,"_search="+urlencode(b)+"&_id="+d+(g?"&_source="+urlencode(g):""),h);
                this.ksearch_data.locks.push(h);
                this.ksearch_data.requests.push(g)
                }
            }
        }
};

this.ksearch_query_results=function(a,b,d){
    if(this.ksearch_value&&
        !(this.ksearch_input&&b!=this.ksearch_value)){
        var e,f,h,g,k,b=this.ksearch_value,j=this.ksearch_data,l=this.env.autocomplete_max?this.env.autocomplete_max:15;
        this.ksearch_pane||(h=$("<ul>"),this.ksearch_pane=$("<div>").attr("id","rcmKSearchpane").css({
            position:"absolute",
            "z-index":3E4
        }).append(h).appendTo(document.body),this.ksearch_pane.__ul=h[0]);
        h=this.ksearch_pane.__ul;
        d&&this.ksearch_pane.data("reqid")==d?l-=h.childNodes.length:(this.ksearch_pane.data("reqid",d),h.innerHTML="",this.env.contacts=
            [],e=$(this.ksearch_input).offset(),this.ksearch_pane.css({
                left:e.left+"px",
                top:e.top+this.ksearch_input.offsetHeight+"px",
                display:"none"
            }));
        if(a&&(f=a.length))for(e=0;e<f&&0<l;e++)k="object"===typeof a[e]?a[e].name:a[e],g=document.createElement("LI"),g.innerHTML=k.replace(RegExp("("+RegExp.escape(b)+")","ig"),"##$1%%").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/##([^%]+)%%/g,"<b>$1</b>"),g.onmouseover=function(){
            i.ksearch_select(this)
            },g.onmouseup=function(){
            i.ksearch_click(this)
            },g._rcm_id=
        this.env.contacts.length+e,h.appendChild(g),l-=1;
        h.childNodes.length&&(this.ksearch_pane.show(),this.env.contacts.length||($("li:first",h).attr("id","rcmksearchSelected").addClass("selected"),this.ksearch_selected=0));
        f&&(this.env.contacts=this.env.contacts.concat(a));
        if(j.id==d)if(j.num--,0<l&&j.sources.length){
            if(f=j.sources.shift())a=this.display_message(this.get_label("searching"),"loading"),d=this.http_post(j.action,"_search="+urlencode(b)+"&_id="+d+"&_source="+urlencode(f),a),this.ksearch_data.locks.push(a),
                this.ksearch_data.requests.push(d)
                }else l||(this.ksearch_msg||(this.ksearch_msg=this.display_message(this.get_label("autocompletemore"))),this.ksearch_abort())
            }
        };

this.ksearch_click=function(a){
    this.ksearch_input&&this.ksearch_input.focus();
    this.insert_recipient(a._rcm_id);
    this.ksearch_hide()
    };
    
this.ksearch_blur=function(){
    this.ksearch_timer&&clearTimeout(this.ksearch_timer);
    this.ksearch_input=null;
    this.ksearch_hide()
    };
    
this.ksearch_hide=function(){
    this.ksearch_selected=null;
    this.ksearch_value="";
    this.ksearch_pane&&
    this.ksearch_pane.hide();
    this.ksearch_destroy()
    };
    
this.ksearch_destroy=function(){
    this.ksearch_abort();
    this.ksearch_info&&this.hide_message(this.ksearch_info);
    this.ksearch_msg&&this.hide_message(this.ksearch_msg);
    this.ksearch_msg=this.ksearch_info=this.ksearch_data=null
    };
    
this.ksearch_abort=function(){
    var a,b,d=this.ksearch_data;
    if(d){
        a=0;
        for(b=d.locks.length;a<b;a++)this.abort_request({
            request:d.requests[a],
            lock:d.locks[a]
            })
        }
        };

this.contactlist_keypress=function(a){
    a.key_pressed==a.DELETE_KEY&&this.command("delete")
    };
this.contactlist_select=function(a){
    this.preview_timer&&clearTimeout(this.preview_timer);
    var b,d,e,f=this,h=!1;
    e=this.env.source?this.env.address_sources[this.env.source]:null;
    (d=a.get_single_selection())?this.preview_timer=window.setTimeout(function(){
        f.load_contact(d,"show")
        },200):this.env.contentframe&&this.show_contentframe(!1);
    if(a.selection.length)if(e)h=!e.readonly;else for(b in a.selection)if((e=(""+a.selection[b]).replace(/^[^-]+-/,""))&&this.env.address_sources[e]&&!this.env.address_sources[e].readonly){
        h=
        !0;
        break
    }
    this.enable_command("compose",this.env.group||0<a.selection.length);
    this.enable_command("edit",d&&h);
    this.enable_command("delete",a.selection.length&&h);
    return!1
    };
    
this.list_contacts=function(a,b,d){
    var e="",f=window;
    a||(a=this.env.source);
    if(d&&this.current_page==d&&a==this.env.source&&b==this.env.group)return!1;
    a!=this.env.source?(d=this.env.current_page=1,this.reset_qsearch()):b!=this.env.group&&(d=this.env.current_page=1);
    this.select_folder(this.env.search_id?"S"+this.env.search_id:b?
        "G"+a+b:a);
    this.env.source=a;
    this.env.group=b;
    this.gui_objects.contactslist?this.list_contacts_remote(a,b,d):(this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(f=window.frames[this.env.contentframe],e="&_framed=1"),b&&(e+="&_gid="+b),d&&(e+="&_page="+d),this.env.search_request&&(e+="&_search="+this.env.search_request),this.set_busy(!0,"loading"),this.location_href(this.env.comm_path+(a?"&_source="+urlencode(a):"")+e,f))
    };
    
this.list_contacts_remote=function(a,b,d){
    this.list_contacts_clear();
    var d=(a?"_source="+urlencode(a):"")+(d?(a?"&":"")+"_page="+d:""),e=this.set_busy(!0,"loading");
    this.env.source=a;
    (this.env.group=b)&&(d+="&_gid="+b);
    this.env.search_request&&(d+="&_search="+this.env.search_request);
    this.http_request("list",d,e)
    };
    
this.list_contacts_clear=function(){
    this.contact_list.clear(!0);
    this.show_contentframe(!1);
    this.enable_command("delete",!1);
    this.enable_command("compose",this.env.group?!0:!1)
    };
    
this.load_contact=function(a,b,d){
    var e="",f=window;
    if(this.env.contentframe&&
        window.frames&&window.frames[this.env.contentframe])e="&_framed=1",f=window.frames[this.env.contentframe],this.show_contentframe(!0),a||(this.contact_list.clear_selection(),this.enable_command("delete","compose",!1));
    else if(d)return!1;
    if(b&&(a||"add"==b)&&!this.drag_active)this.env.group&&(e+="&_gid="+urlencode(this.env.group)),this.location_href(this.env.comm_path+"&_action="+b+"&_source="+urlencode(this.env.source)+"&_cid="+urlencode(a)+e,f,!0);
    return!0
    };
    
this.group_member_change=function(a,b,d,
    e){
    var a="add"==a?"add":"del",f=this.display_message(this.get_label("add"==a?"addingmember":"removingmember"),"loading");
    this.http_post("group-"+a+"members","_cid="+urlencode(b)+"&_source="+urlencode(d)+"&_gid="+urlencode(e),f)
    };
    
this.copy_contact=function(a,b){
    a||(a=this.contact_list.get_selection().join(","));
    if("group"==b.type&&b.source==this.env.source)this.group_member_change("add",a,b.source,b.id);
    else if("group"==b.type&&!this.env.address_sources[b.source].readonly){
        var d=this.display_message(this.get_label("copyingcontact"),
            "loading");
        this.http_post("copy","_cid="+urlencode(a)+"&_source="+urlencode(this.env.source)+"&_to="+urlencode(b.source)+"&_togid="+urlencode(b.id)+(this.env.group?"&_gid="+urlencode(this.env.group):""),d)
        }else b.id!=this.env.source&&a&&this.env.address_sources[b.id]&&!this.env.address_sources[b.id].readonly&&(d=this.display_message(this.get_label("copyingcontact"),"loading"),this.http_post("copy","_cid="+urlencode(a)+"&_source="+urlencode(this.env.source)+"&_to="+urlencode(b.id)+(this.env.group?
        "&_gid="+urlencode(this.env.group):""),d))
    };
    
this.delete_contacts=function(){
    var a=this.contact_list.get_selection(),b=this.env.source&&this.env.address_sources[this.env.source].undelete;
    if(!(!a.length&&!this.env.cid||!b&&!confirm(this.get_label("deletecontactconfirm")))){
        var d,e=[],f="";
        if(this.env.cid)e.push(this.env.cid);
        else{
            for(d=0;d<a.length;d++)b=a[d],e.push(b),this.contact_list.remove_row(b,d==a.length-1);
            1==a.length&&this.show_contentframe(!1)
            }
            this.env.group&&(f+="&_gid="+urlencode(this.env.group));
        this.env.search_request&&(f+="&_search="+this.env.search_request);
        this.http_post("delete","_cid="+urlencode(e.join(","))+"&_source="+urlencode(this.env.source)+"&_from="+(this.env.action?this.env.action:"")+f,this.display_message(this.get_label("contactdeleting"),"loading"));
        return!0
        }
    };

this.update_contact_row=function(a,b,d,e){
    var f,h=this.contact_list,a=this.html_identifier(a);
    h.rows[a]||(a=a+"-"+e,d&&(d=d+"-"+e));
    if(h.rows[a]&&(f=h.rows[a].obj)){
        for(e=0;e<b.length;e++)f.cells[e]&&$(f.cells[e]).html(b[e]);
        d&&(d=this.html_identifier(d),f.id="rcmrow"+d,h.remove_row(a),h.init_row(f),h.selection[0]=d,f.style.display="")
        }
    };

this.add_contact_row=function(a,b){
    if(!this.gui_objects.contactslist)return!1;
    var d,e=this.contact_list,f=document.createElement("tr");
    f.id="rcmrow"+this.html_identifier(a);
    f.className="contact";
    e.in_selection(a)&&(f.className+=" selected");
    for(d in b)col=document.createElement("td"),col.className=(""+d).toLowerCase(),col.innerHTML=b[d],f.appendChild(col);e.insert_row(f);
    this.enable_command("export",
        0<e.rowcount)
    };
    
this.init_contact_form=function(){
    var a=this,b;
    this.set_photo_actions($("#ff_photo").val());
    for(b in this.env.coltypes)this.init_edit_field(b,null);$(".contactfieldgroup .row a.deletebutton").click(function(){
        a.delete_edit_field(this);
        return!1
        });
    $("select.addfieldmenu").change(function(){
        a.insert_edit_field($(this).val(),$(this).attr("rel"),this);
        this.selectedIndex=0
        });
    $.datepicker&&this.env.date_format&&($.datepicker.setDefaults({
        dateFormat:this.env.date_format,
        changeMonth:!0,
        changeYear:!0,
        yearRange:"-100:+10",
        showOtherMonths:!0,
        selectOtherMonths:!0,
        monthNamesShort:this.env.month_names,
        onSelect:function(a){
            $(this).focus().val(a)
            }
        }),$("input.datepicker").datepicker());
$("input[type='text']:visible").first().focus()
};

this.group_create=function(){
    this.add_input_row("contactgroup")
    };
    
this.group_rename=function(){
    if(this.env.group&&this.gui_objects.folderlist){
        if(!this.name_input){
            this.enable_command("list","listgroup",!1);
            this.name_input=$("<input>").attr("type","text").val(this.env.contactgroups["G"+
                this.env.source+this.env.group].name);
            this.name_input.bind("keydown",function(a){
                return rcmail.add_input_keydown(a)
                });
            this.env.group_renaming=!0;
            var a,b=this.get_folder_li(this.env.source+this.env.group,"rcmliG");
            b&&(a=b.firstChild)&&$(a).hide().before(this.name_input)
            }
            this.name_input.select().focus()
        }
    };

this.group_delete=function(){
    if(this.env.group&&confirm(this.get_label("deletegroupconfirm"))){
        var a=this.set_busy(!0,"groupdeleting");
        this.http_post("group-delete","_source="+urlencode(this.env.source)+
            "&_gid="+urlencode(this.env.group),a)
        }
    };

this.remove_group_item=function(a){
    var b,d="G"+a.source+a.id;
    if(b=this.get_folder_li(d))this.triggerEvent("group_delete",{
        source:a.source,
        id:a.id,
        li:b
    }),b.parentNode.removeChild(b),delete this.env.contactfolders[d],delete this.env.contactgroups[d];
    this.list_contacts(a.source,0)
    };
    
this.add_input_row=function(a){
    this.gui_objects.folderlist&&(this.name_input||(this.name_input=$("<input>").attr("type","text").data("tt",a),this.name_input.bind("keydown",function(a){
        return rcmail.add_input_keydown(a)
        }),
    this.name_input_li=$("<li>").addClass(a).append(this.name_input),this.name_input_li.insertAfter("contactsearch"==a?$("li:last",this.gui_objects.folderlist):this.get_folder_li(this.env.source))),this.name_input.select().focus())
    };
    
this.add_input_keydown=function(a){
    var b=rcube_event.get_keycode(a),d=$(a.target),a=d.data("tt");
    if(13==b){
        if(b=d.val())d=this.set_busy(!0,"loading"),"contactsearch"==a?this.http_post("search-create","_search="+urlencode(this.env.search_request)+"&_name="+urlencode(b),d):
            this.env.group_renaming?this.http_post("group-rename","_source="+urlencode(this.env.source)+"&_gid="+urlencode(this.env.group)+"&_name="+urlencode(b),d):this.http_post("group-create","_source="+urlencode(this.env.source)+"&_name="+urlencode(b),d);
        return!1
        }
        27==b&&this.reset_add_input();
    return!0
    };
    
this.reset_add_input=function(){
    this.name_input&&(this.env.group_renaming&&(this.name_input.parent().children().last().show(),this.env.group_renaming=!1),this.name_input.remove(),this.name_input_li&&this.name_input_li.remove(),
        this.name_input=this.name_input_li=null);
    this.enable_command("list","listgroup",!0)
    };
    
this.insert_contact_group=function(a){
    this.reset_add_input();
    a.type="group";
    var b="G"+a.source+a.id,d=$("<a>").attr("href","#").attr("rel",a.source+":"+a.id).click(function(){
        return rcmail.command("listgroup",a,this)
        }).html(a.name),d=$("<li>").attr({
        id:"rcmli"+this.html_identifier(b),
        "class":"contactgroup"
    }).append(d);
    this.env.contactfolders[b]=this.env.contactgroups[b]=a;
    this.add_contact_group_row(a,d);
    this.triggerEvent("group_insert",

    {
        id:a.id,
        source:a.source,
        name:a.name,
        li:d[0]
        })
    };
    
this.update_contact_group=function(a){
    this.reset_add_input();
    var b="G"+a.source+a.id,d=this.get_folder_li(b),e;
    if(d&&a.newid){
        e="G"+a.source+a.newid;
        var f=$.extend({},a);
        d.id="rcmli"+this.html_identifier(e);
        this.env.contactfolders[e]=this.env.contactfolders[b];
        this.env.contactfolders[e].id=a.newid;
        this.env.group=a.newid;
        delete this.env.contactfolders[b];
        delete this.env.contactgroups[b];
        f.id=a.newid;
        f.type="group";
        e=$("<a>").attr("href","#").attr("rel",
            a.source+":"+a.newid).click(function(){
            return rcmail.command("listgroup",f,this)
            }).html(a.name);
        $(d).children().replaceWith(e)
        }else if(d&&(e=d.firstChild)&&"a"==e.tagName.toLowerCase())e.innerHTML=a.name;
    this.env.contactfolders[b].name=this.env.contactgroups[b].name=a.name;
    this.add_contact_group_row(a,$(d),!0);
    this.triggerEvent("group_update",{
        id:a.id,
        source:a.source,
        name:a.name,
        li:d[0],
        newid:a.newid
        })
    };
    
this.add_contact_group_row=function(a,b,d){
    var e=a.name.toUpperCase(),f=this.get_folder_li(a.source),
    a="rcmliG"+this.html_identifier(a.source);
    d?(d=b.clone(!0),b.remove()):d=b;
    $('li[id^="'+a+'"]',this.gui_objects.folderlist).each(function(a,b){
        if(e>=$(this).text().toUpperCase())f=b;else return false
            });
    d.insertAfter(f)
    };
    
this.update_group_commands=function(){
    var a=""!=this.env.source?this.env.address_sources[this.env.source]:null;
    this.enable_command("group-create",a&&a.groups&&!a.readonly);
    this.enable_command("group-rename","group-delete",a&&a.groups&&this.env.group&&!a.readonly)
    };
    
this.init_edit_field=
function(a,b){
    b||(b=$(".ff_"+a));
    b.focus(function(){
        i.focus_textfield(this)
        }).blur(function(){
        i.blur_textfield(this)
        }).each(function(){
        this._placeholder=this.title=i.env.coltypes[a].label;
        i.blur_textfield(this)
        })
    };
    
this.insert_edit_field=function(a,b,d){
    var e=$("#ff_"+a);
    if(e.length)e.show().focus(),$(d).children('option[value="'+a+'"]').prop("disabled",!0);
    else if($(".ff_"+a),e=$("#contactsection"+b+" .contactcontroller"+a),e.length||(e=$("<fieldset>").addClass("contactfieldgroup contactcontroller"+
        a).insertAfter($("#contactsection"+b+" .contactfieldgroup").last())),e.length&&"FIELDSET"==e.get(0).nodeName){
        var f,b=this.env.coltypes[a],h=$("<div>").addClass("row"),g=$("<div>").addClass("contactfieldcontent data"),k=$("<div>").addClass("contactfieldlabel label");
        b.subtypes_select?k.html(b.subtypes_select):k.html(b.label);
        var j=1!=b.limit?"[]":"";
        if("text"==b.type||"date"==b.type)f=$("<input>").addClass("ff_"+a).attr({
            type:"text",
            name:"_"+a+j,
            size:b.size
            }).appendTo(g),this.init_edit_field(a,f),
            "date"==b.type&&$.datepicker&&f.datepicker();
        else if("composite"==b.type){
            var l,n,m=[],o=[];
            if(f=this.env[a+"_template"])for(l=0;l<f.length;l++)m.push(f[l][1]),o.push(f[l][2]);else for(l in b.childs)m.push(l);for(var p=0;p<m.length;p++)l=m[p],f=b.childs[l],f=$("<input>").addClass("ff_"+l).attr({
                type:"text",
                name:"_"+l+j,
                size:f.size
                }).appendTo(g),g.append(o[p]||" "),this.init_edit_field(l,f),n||(n=f);
            f=n
            }else if("select"==b.type){
            f=$("<select>").addClass("ff_"+a).attr("name","_"+a+j).appendTo(g);
            var q=
            f.attr("options");
            q[q.length]=new Option("---","");
            b.options&&$.each(b.options,function(a,b){
                q[q.length]=new Option(b,a)
                })
            }
            f&&($('<a href="#del"></a>').addClass("contactfieldbutton deletebutton").attr({
            title:this.get_label("delete"),
            rel:a
        }).html(this.env.delbutton).click(function(){
            i.delete_edit_field(this);
            return!1
            }).appendTo(g),h.append(k).append(g).appendTo(e.show()),f.first().focus(),b.count||(b.count=0),++b.count==b.limit&&b.limit&&$(d).children('option[value="'+a+'"]').prop("disabled",!0))
        }
    };
this.delete_edit_field=function(a){
    var b=$(a).attr("rel"),d=this.env.coltypes[b],e=$(a).parents("fieldset.contactfieldgroup"),f=e.parent().find("select.addfieldmenu");
    0>=--d.count&&d.visible?$(a).parent().children("input").val("").blur():($(a).parents("div.row").remove(),e.children("div.row").length||e.hide());
    f.length&&(a=f.children('option[value="'+b+'"]'),a.length?a.prop("disabled",!1):$("<option>").attr("value",b).html(d.label).appendTo(f),f.show())
    };
    
this.upload_contact_photo=function(a){
    a&&a.elements._photo.value&&
    (this.async_upload_form(a,"upload-photo",function(){
        rcmail.set_busy(!1,null,rcmail.photo_upload_id)
        }),this.photo_upload_id=this.set_busy(!0,"uploading"))
    };
    
this.replace_contact_photo=function(a){
    var b="-del-"==a?this.env.photo_placeholder:this.env.comm_path+"&_action=photo&_source="+this.env.source+"&_cid="+this.env.cid+"&_photo="+a;
    this.set_photo_actions(a);
    $(this.gui_objects.contactphoto).children("img").attr("src",b)
    };
    
this.photo_upload_end=function(){
    this.set_busy(!1,null,this.photo_upload_id);
    delete this.photo_upload_id
    };
    
this.set_photo_actions=function(a){
    var b,d=this.buttons["upload-photo"];
    for(b=0;d&&b<d.length;b++)$("#"+d[b].id).html(this.get_label("-del-"==a?"addphoto":"replacephoto"));
    $("#ff_photo").val(a);
    this.enable_command("upload-photo",this.env.coltypes.photo?!0:!1);
    this.enable_command("delete-photo",this.env.coltypes.photo&&"-del-"!=a)
    };
    
this.advanced_search=function(){
    var a="&_form=1",b=window;
    this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(a+="&_framed=1",
        b=window.frames[this.env.contentframe],this.contact_list.clear_selection());
    this.location_href(this.env.comm_path+"&_action=search"+a,b,!0);
    return!0
    };
    
this.unselect_directory=function(){
    this.select_folder("");
    this.enable_command("search-delete",!1)
    };
    
this.insert_saved_search=function(a,b){
    this.reset_add_input();
    var d="S"+b,e=$("<a>").attr("href","#").attr("rel",b).click(function(){
        return rcmail.command("listsearch",b,this)
        }).html(a),d=$("<li>").attr({
        id:"rcmli"+this.html_identifier(d),
        "class":"contactsearch"
    }).append(e),
    e={
        name:a,
        id:b,
        li:d[0]
        };
        
    this.add_saved_search_row(e,d);
    this.select_folder("S"+b);
    this.enable_command("search-delete",!0);
    this.env.search_id=b;
    this.triggerEvent("abook_search_insert",e)
    };
    
this.add_saved_search_row=function(a,b,d){
    var e,f=a.name.toUpperCase();
    d?(a=b.clone(!0),b.remove()):a=b;
    $('li[class~="contactsearch"]',this.gui_objects.folderlist).each(function(a,b){
        if(!e)e=this.previousSibling;
        if(f>=$(this).text().toUpperCase())e=b;else return false
            });
    e?a.insertAfter(e):a.appendTo(this.gui_objects.folderlist)
    };
this.search_create=function(){
    this.add_input_row("contactsearch")
    };
    
this.search_delete=function(){
    if(this.env.search_request){
        var a=this.set_busy(!0,"savedsearchdeleting");
        this.http_post("search-delete","_sid="+urlencode(this.env.search_id),a)
        }
    };

this.remove_search_item=function(a){
    var b;
    if(b=this.get_folder_li("S"+a))this.triggerEvent("search_delete",{
        id:a,
        li:b
    }),b.parentNode.removeChild(b);
    this.env.search_id=null;
    this.env.search_request=null;
    this.list_contacts_clear();
    this.reset_qsearch();
    this.enable_command("search-delete",
        "search-create",!1)
    };
    
this.listsearch=function(a){
    var b=this.set_busy(!0,"searching");
    this.contact_list&&this.list_contacts_clear();
    this.reset_qsearch();
    this.select_folder("S"+a);
    this.env.current_page=1;
    this.http_request("search","_sid="+urlencode(a),b)
    };
    
this.section_select=function(a){
    var a=a.get_single_selection(),b="",d=window;
    a&&(this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(b="&_framed=1",d=window.frames[this.env.contentframe]),this.location_href(this.env.comm_path+
        "&_action=edit-prefs&_section="+a+b,d,!0));
    return!0
    };
    
this.identity_select=function(a){
    var b;
    (b=a.get_single_selection())&&this.load_identity(b,"edit-identity")
    };
    
this.load_identity=function(a,b){
    if("edit-identity"==b&&(!a||a==this.env.iid))return!1;
    var d="",e=window;
    this.env.contentframe&&window.frames&&window.frames[this.env.contentframe]&&(d="&_framed=1",e=window.frames[this.env.contentframe],document.getElementById(this.env.contentframe).style.visibility="inherit");
    if(b&&(a||"add-identity"==b))this.set_busy(!0),
        this.location_href(this.env.comm_path+"&_action="+b+"&_iid="+a+d,e);
    return!0
    };
    
this.delete_identity=function(a){
    var b=this.identity_list.get_selection();
    if(b.length||this.env.iid)return a||(a=this.env.iid?this.env.iid:b[0]),this.goto_url("delete-identity","_iid="+a+"&_token="+this.env.request_token,!0),!0
        };
        
this.init_subscription_list=function(){
    var a=this;
    this.subscription_list=new rcube_list_widget(this.gui_objects.subscriptionlist,{
        multiselect:!1,
        draggable:!0,
        keyboard:!1,
        toggleselect:!0
        });
    this.subscription_list.addEventListener("select",
        function(b){
            a.subscription_select(b)
            });
    this.subscription_list.addEventListener("dragstart",function(){
        a.drag_active=!0
        });
    this.subscription_list.addEventListener("dragend",function(b){
        a.subscription_move_folder(b)
        });
    this.subscription_list.row_init=function(b){
        b.obj.onmouseover=function(){
            a.focus_subscription(b.id)
            };
            
        b.obj.onmouseout=function(){
            a.unfocus_subscription(b.id)
            }
        };
    
this.subscription_list.init();
$("#mailboxroot").mouseover(function(){
    a.focus_subscription(this.id)
    }).mouseout(function(){
    a.unfocus_subscription(this.id)
    })
};
this.focus_subscription=function(a){
    var b,d,e=RegExp.escape(this.env.delimiter),e=RegExp("["+e+"]?[^"+e+"]+$");
    if(this.drag_active&&this.env.mailbox&&(b=document.getElementById(a)))if(this.env.subscriptionrows[a]&&null!==(d=this.env.subscriptionrows[a][0])&&this.check_droptarget(d)&&!this.env.subscriptionrows[this.get_folder_row_id(this.env.mailbox)][2]&&d!=this.env.mailbox.replace(e,"")&&!d.match(RegExp("^"+RegExp.escape(this.env.mailbox+this.env.delimiter))))this.env.dstfolder=d,$(b).addClass("droptarget")
        };
this.unfocus_subscription=function(a){
    var b=$("#"+a);
    this.env.dstfolder=null;
    this.env.subscriptionrows[a]&&b[0]?b.removeClass("droptarget"):$(this.subscription_list.frame).removeClass("droptarget")
    };
    
this.subscription_select=function(a){
    var b,d;
    a&&(b=a.get_single_selection())&&(d=this.env.subscriptionrows["rcmrow"+b])?(this.env.mailbox=d[0],this.show_folder(d[0]),this.enable_command("delete-folder",!d[2])):(this.env.mailbox=null,this.show_contentframe(!1),this.enable_command("delete-folder","purge",
        !1))
    };
    
this.subscription_move_folder=function(){
    var a=RegExp.escape(this.env.delimiter);
    this.env.mailbox&&null!==this.env.dstfolder&&this.env.dstfolder!=this.env.mailbox&&this.env.dstfolder!=this.env.mailbox.replace(RegExp("["+a+"]?[^"+a+"]+$"),"")&&(a=this.env.mailbox.replace(RegExp("[^"+a+"]*["+a+"]","g"),""),a=""===this.env.dstfolder?a:this.env.dstfolder+this.env.delimiter+a,a!=this.env.mailbox&&(this.http_post("rename-folder","_folder_oldname="+urlencode(this.env.mailbox)+"&_folder_newname="+urlencode(a),
        this.set_busy(!0,"foldermoving")),this.subscription_list.draglayer.hide()));
    this.drag_active=!1;
    this.unfocus_subscription(this.get_folder_row_id(this.env.dstfolder))
    };
    
this.create_folder=function(){
    this.show_folder("",this.env.mailbox)
    };
    
this.delete_folder=function(a){
    if((a=this.env.subscriptionrows[this.get_folder_row_id(a?a:this.env.mailbox)][0])&&confirm(this.get_label("deletefolderconfirm"))){
        var b=this.set_busy(!0,"folderdeleting");
        this.http_post("delete-folder","_mbox="+urlencode(a),b)
        }
    };

this.add_folder_row=
function(a,b,d,e,f,h){
    if(!this.gui_objects.subscriptionlist)return!1;
    var g,k,j,i,n,m=[],o=[],p=this.gui_objects.subscriptionlist.tBodies[0];
    g=$("tr",p).get(1);
    var q="rcmrow"+(new Date).getTime();
    if(!g)return this.goto_url("folders"),!1;
    g=$(g).clone(!0);
    g.attr("id",q);
    g.attr("class",h);
    g.find("td:first").html(b);
    $('input[name="_subscribed[]"]',g).val(a).prop({
        checked:e?!0:!1,
        disabled:d?!0:!1
        });
    this.env.subscriptionrows[q]=[a,b,0];
    i=[];
    $.each(this.env.subscriptionrows,function(a,b){
        i.push(b)
        });
    i.sort(function(a,
        b){
        return a[0]<b[0]?-1:a[0]>b[0]?1:0
        });
    for(k in i)i[k][2]?(o.push(i[k][0]),j=i[k][0]+this.env.delimiter):j&&0==i[k][0].indexOf(j)?o.push(i[k][0]):(m.push(i[k][0]),j=null);for(k=0;k<o.length;k++)0==a.indexOf(o[k]+this.env.delimiter)&&(n=this.get_folder_row_id(o[k]));
    for(k=0;!n&&k<m.length;k++)k&&m[k]==a&&(n=this.get_folder_row_id(m[k-1]));
    n?$("#"+n).after(g):g.appendTo(p);
    this.subscription_list.clear_selection();
    f||this.init_subscription_list();
    g=g.get(0);
    g.scrollIntoView&&g.scrollIntoView();
    return g
    };
this.replace_folder_row=function(a,b,d,e,f){
    if(!this.gui_objects.subscriptionlist)return!1;
    var h,g,i,j,l=this.get_folder_row_id(a),n=RegExp("^"+RegExp.escape(a));
    h=$('input[name="_subscribed[]"]',$("#"+l)).prop("checked");
    var m=this.get_subfolders(a);
    this._remove_folder_row(l);
    e=$(this.add_folder_row(b,d,e,h,!0,f));
    if(d=m.length)j=a.split(this.env.delimiter).length-b.split(this.env.delimiter).length;
    for(a=0;a<d;a++)if(l=m[a],h=this.env.subscriptionrows[l][0],f=this.env.subscriptionrows[l][1],g=$("#"+
        l),i=g.clone(!0),g.remove(),e.after(i),e=i,h=h.replace(n,b),$('input[name="_subscribed[]"]',e).val(h),this.env.subscriptionrows[l][0]=h,0!=j){
        if(0<j)for(h=j;0<h;h--)f=f.replace(/^&nbsp;&nbsp;&nbsp;&nbsp;/,"");else for(h=j;0>h;h++)f="&nbsp;&nbsp;&nbsp;&nbsp;"+f;
        e.find("td:first").html(f);
        this.env.subscriptionrows[l][1]=f
        }
        this.init_subscription_list()
    };
    
this.remove_folder_row=function(a,b){
    var d,e,f=[];
    d=this.get_folder_row_id(a);
    b&&(f=this.get_subfolders(a));
    this._remove_folder_row(d);
    d=0;
    for(e=f.length;d<
        e;d++)this._remove_folder_row(f[d])
        };
        
this._remove_folder_row=function(a){
    this.subscription_list.remove_row(a.replace(/^rcmrow/,""));
    $("#"+a).remove();
    delete this.env.subscriptionrows[a]
};

this.get_subfolders=function(a){
    for(var b=[],d=RegExp("^"+RegExp.escape(a)+RegExp.escape(this.env.delimiter)),e=$("#"+this.get_folder_row_id(a)).get(0);e=e.nextSibling;)if(e.id)if(a=this.env.subscriptionrows[e.id][0],d.test(a))b.push(e.id);else break;return b
    };
    
this.subscribe=function(a){
    if(a){
        var b=this.display_message(this.get_label("foldersubscribing"),
            "loading");
        this.http_post("subscribe","_mbox="+urlencode(a),b)
        }
    };

this.unsubscribe=function(a){
    if(a){
        var b=this.display_message(this.get_label("folderunsubscribing"),"loading");
        this.http_post("unsubscribe","_mbox="+urlencode(a),b)
        }
    };

this.get_folder_row_id=function(a){
    var b,d=this.env.subscriptionrows;
    for(b in d)if(d[b]&&d[b][0]==a)break;return b
    };
    
this.show_folder=function(a,b,d){
    var e=window,a="&_action=edit-folder&_mbox="+urlencode(a);
    b&&(a+="&_path="+urlencode(b));
    this.env.contentframe&&window.frames&&
    window.frames[this.env.contentframe]&&(e=window.frames[this.env.contentframe],a+="&_framed=1");
    0<=(""+e.location.href).indexOf(a)&&!d?this.show_contentframe(!0):this.location_href(this.env.comm_path+a,e,!0)
    };
    
this.disable_subscription=function(a){
    (a=this.get_folder_row_id(a))&&$('input[name="_subscribed[]"]',$("#"+a)).prop("disabled",!0)
    };
    
this.folder_size=function(a){
    var b=this.set_busy(!0,"loading");
    this.http_post("folder-size","_mbox="+urlencode(a),b)
    };
    
this.folder_size_update=function(a){
    $("#folder-size").replaceWith(a)
    };
var s=function(a,b){
    var d=document.getElementById(b.id);
    if(d){
        var e=!1;
        "image"==b.type&&(d=d.parentNode,e=!0);
        d._command=a;
        d._id=b.id;
        b.sel&&(d.onmousedown=function(){
            return rcmail.button_sel(this._command,this._id)
            },d.onmouseup=function(){
            return rcmail.button_out(this._command,this._id)
            },e&&((new Image).src=b.sel));
        b.over&&(d.onmouseover=function(){
            return rcmail.button_over(this._command,this._id)
            },d.onmouseout=function(){
            return rcmail.button_out(this._command,this._id)
            },e&&((new Image).src=b.over))
        }
    };
this.set_page_buttons=function(){
    this.enable_command("nextpage","lastpage",this.env.pagecount>this.env.current_page);
    this.enable_command("previouspage","firstpage",1<this.env.current_page)
    };
    
this.init_buttons=function(){
    for(var a in this.buttons)if("string"===typeof a)for(var b=0;b<this.buttons[a].length;b++)s(a,this.buttons[a][b]);this.set_button(this.task,"sel")
    };
    
this.set_button=function(a,b){
    var d,e,f,h=this.buttons[a],g=h?h.length:0;
    for(d=0;d<g;d++)if(e=h[d],(f=document.getElementById(e.id))&&
        "image"==e.type&&!e.status?(e.pas=f._original_src?f._original_src:f.src,f.runtimeStyle&&f.runtimeStyle.filter&&f.runtimeStyle.filter.match(/src=['"]([^'"]+)['"]/)&&(e.pas=RegExp.$1)):f&&!e.status&&(e.pas=""+f.className),f&&"image"==e.type&&e[b]?(e.status=b,f.src=e[b]):f&&void 0!==e[b]&&(e.status=b,f.className=e[b]),f&&"input"==e.type)e.status=b,f.disabled=!b
        };
        
this.set_alttext=function(a,b){
    var d,e,f,h,g=this.buttons[a],i=g?g.length:0;
    for(d=0;d<i;d++)e=g[d],f=document.getElementById(e.id),"image"==
        e.type&&f?(f.setAttribute("alt",this.get_label(b)),(h=f.parentNode)&&"a"==h.tagName.toLowerCase()&&h.setAttribute("title",this.get_label(b))):f&&f.setAttribute("title",this.get_label(b))
        };
        
this.button_over=function(a,b){
    var d,e,f,h=this.buttons[a],g=h?h.length:0;
    for(d=0;d<g;d++)if(e=h[d],e.id==b&&"act"==e.status&&(f=document.getElementById(e.id))&&e.over)"image"==e.type?f.src=e.over:f.className=e.over
        };
        
this.button_sel=function(a,b){
    var d,e,f,h=this.buttons[a],g=h?h.length:0;
    for(d=0;d<g;d++)if(e=h[d],
        e.id==b&&"act"==e.status){
        if((f=document.getElementById(e.id))&&e.sel)"image"==e.type?f.src=e.sel:f.className=e.sel;
        this.buttons_sel[b]=a
        }
    };
    
this.button_out=function(a,b){
    var d,e,f,h=this.buttons[a],g=h?h.length:0;
    for(d=0;d<g;d++)if(e=h[d],e.id==b&&"act"==e.status&&(f=document.getElementById(e.id))&&e.act)"image"==e.type?f.src=e.act:f.className=e.act
        };
        
this.focus_textfield=function(a){
    a._hasfocus=!0;
    var b=$(a);
    (b.hasClass("placeholder")||b.val()==a._placeholder)&&b.val("").removeClass("placeholder").attr("spellcheck",
        !0)
    };
    
this.blur_textfield=function(a){
    a._hasfocus=!1;
    var b=$(a);
    a._placeholder&&(!b.val()||b.val()==a._placeholder)&&b.addClass("placeholder").attr("spellcheck",!1).val(a._placeholder)
    };
    
this.set_pagetitle=function(a){
    a&&document.title&&(document.title=a)
    };
    
this.display_message=function(a,b,d){
    if(this.is_framed())return parent.rcmail.display_message(a,b,d);
    if(!this.gui_objects.message)return"loading"!=b&&(this.pending_message=[a,b,d]),!1;
    var b=b?b:"notice",e=this,f=this.html_identifier(a),h=b+(new Date).getTime();
    d||(d=this.message_time*("error"==b||"warning"==b?2:1));
    "loading"==b&&(f="loading",d=1E3*this.env.request_timeout,a||(a=this.get_label("loading")));
    if(this.messages[f])return this.messages[f].obj&&this.messages[f].obj.html(a),"loading"==b&&this.messages[f].labels.push({
        id:h,
        msg:a
    }),this.messages[f].elements.push(h),window.setTimeout(function(){
        e.hide_message(h,b=="loading")
        },d),h;
    var g=$("<div>").addClass(b).html(a).data("key",f);
    $(this.gui_objects.message).append(g).show();
    this.messages[f]={
        obj:g,
        elements:[h]
        };
        
    "loading"==b?this.messages[f].labels=[{
        id:h,
        msg:a
    }]:g.click(function(){
        return e.hide_message(g)
        });
    0<d&&window.setTimeout(function(){
        e.hide_message(h,b=="loading")
        },d);
    return h
    };
    
this.hide_message=function(a,b){
    if(this.is_framed())return parent.rcmail.hide_message(a,b);
    var d,e,f,h,g=this.messages;
    if("object"===typeof a)$(a)[b?"fadeOut":"hide"](),h=$(a).data("key"),this.messages[h]&&delete this.messages[h];else for(d in g)for(e in g[d].elements)if(g[d]&&g[d].elements[e]==a)if(g[d].elements.splice(e,
        1),g[d].elements.length){
        if("loading"==d)for(f in g[d].labels)g[d].labels[f].id==a?delete g[d].labels[f]:h=g[d].labels[f].msg,g[d].obj.html(h)
            }else g[d].obj[b?"fadeOut":"hide"](),delete g[d]
        };
        
this.select_folder=function(a,b,d){
    if(this.gui_objects.folderlist){
        var e,f;
        (e=$("li.selected",this.gui_objects.folderlist))&&e.removeClass("selected").addClass("unfocused");
        (f=this.get_folder_li(a,b,d))&&$(f).removeClass("unfocused").addClass("selected");
        this.triggerEvent("selectfolder",{
            folder:a,
            prefix:b
        })
        }
    };
this.get_folder_li=function(a,b,d){
    b||(b="rcmli");
    return this.gui_objects.folderlist?(a=this.html_identifier(a,d),document.getElementById(b+a)):null
    };
    
this.set_message_coltypes=function(a,b){
    var d=this.message_list,e=d?d.list.tHead:null,f,h,g,i;
    this.env.coltypes=a;
    if(e){
        if(b){
            h=document.createElement("thead");
            g=document.createElement("tr");
            c=0;
            for(i=b.length;c<i;c++)f=document.createElement("td"),f.innerHTML=b[c].html,b[c].id&&(f.id=b[c].id),b[c].className&&(f.className=b[c].className),g.appendChild(f);
            h.appendChild(g);
            e.parentNode.replaceChild(h,e);
            e=h
            }
            g=0;
        for(i=this.env.coltypes.length;g<i;g++)if(h=this.env.coltypes[g],(f=e.rows[0].cells[g])&&("from"==h||"to"==h))f.id="rcm"+h,f.firstChild&&"a"==f.firstChild.tagName.toLowerCase()&&(f=f.firstChild,f.onclick=function(){
            return rcmail.command("sort",this.__col,this)
            },f.__col=h),f.innerHTML=this.get_label(h)
            }
            this.env.subject_col=null;
    this.env.flagged_col=null;
    this.env.status_col=null;
    if(0<=(g=$.inArray("subject",this.env.coltypes)))this.env.subject_col=
        g,d&&(d.subject_col=g);
    if(0<=(g=$.inArray("flag",this.env.coltypes)))this.env.flagged_col=g;
    if(0<=(g=$.inArray("status",this.env.coltypes)))this.env.status_col=g;
    d&&d.init_header()
    };
    
this.set_rowcount=function(a,b){
    if(b&&b!=this.env.mailbox)return!1;
    $(this.gui_objects.countdisplay).html(a);
    this.set_page_buttons()
    };
    
this.set_mailboxname=function(a){
    this.gui_objects.mailboxname&&a&&(this.gui_objects.mailboxname.innerHTML=a)
    };
    
this.set_quota=function(a){
    a&&this.gui_objects.quotadisplay&&("object"===typeof a&&
        "image"==a.type?this.percent_indicator(this.gui_objects.quotadisplay,a):$(this.gui_objects.quotadisplay).html(a))
    };
    
this.set_unread_count=function(a,b,d){
    if(!this.gui_objects.mailboxlist)return!1;
    this.env.unread_counts[a]=b;
    this.set_unread_count_display(a,d)
    };
    
this.set_unread_count_display=function(a,b){
    var d,e,f,h,g;
    if(f=this.get_folder_li(a,"",!0)){
        h=this.env.unread_counts[a]?this.env.unread_counts[a]:0;
        e=$(f).children("a").eq(0);
        d=e.children("span.unreadcount");
        !d.length&&h&&(d=$("<span>").addClass("unreadcount").appendTo(e));
        e=0;
        if((g=f.getElementsByTagName("div")[0])&&g.className.match(/collapsed/))for(var i in this.env.unread_counts)0==i.indexOf(a+this.env.delimiter)&&(e+=this.env.unread_counts[i]);h&&d.length?d.html(" ("+h+")"):d.length&&d.remove();
        d=RegExp(RegExp.escape(this.env.delimiter)+"[^"+RegExp.escape(this.env.delimiter)+"]+$");
        a.match(d)&&this.set_unread_count_display(a.replace(d,""),!1);
        0<h+e?$(f).addClass("unread"):$(f).removeClass("unread")
        }
        d=/^\([0-9]+\)\s+/i;
    b&&document.title&&(f="",f=""+document.title,
        f=h&&f.match(d)?f.replace(d,"("+h+") "):h?"("+h+") "+f:f.replace(d,""),this.set_pagetitle(f))
    };
    
this.toggle_prefer_html=function(a){
    $("#rcmfd_show_images").prop("disabled",!a.checked).val(0)
    };
    
this.toggle_preview_pane=function(a){
    $("#rcmfd_preview_pane_mark_read").prop("disabled",!a.checked)
    };
    
this.set_headers=function(a){
    this.gui_objects.all_headers_row&&this.gui_objects.all_headers_box&&a&&$(this.gui_objects.all_headers_box).html(a).show()
    };
    
this.load_headers=function(a){
    this.gui_objects.all_headers_row&&
    this.gui_objects.all_headers_box&&this.env.uid&&($(a).removeClass("show-headers").addClass("hide-headers"),$(this.gui_objects.all_headers_row).show(),a.onclick=function(){
        rcmail.hide_headers(a)
        },this.gui_objects.all_headers_box.innerHTML||this.http_post("headers","_uid="+this.env.uid,this.display_message(this.get_label("loading"),"loading")))
    };
    
this.hide_headers=function(a){
    this.gui_objects.all_headers_row&&this.gui_objects.all_headers_box&&($(a).removeClass("hide-headers").addClass("show-headers"),
        $(this.gui_objects.all_headers_row).hide(),a.onclick=function(){
            rcmail.load_headers(a)
            })
    };
    
this.percent_indicator=function(a,b){
    if(!b||!a)return!1;
    var d=b.width?b.width:this.env.indicator_width?this.env.indicator_width:100,e=b.height?b.height:this.env.indicator_height?this.env.indicator_height:14,f=b.percent?Math.abs(parseInt(b.percent)):0,h=parseInt(f/100*d),g=$(a).position();
    g.top=Math.max(0,g.top);
    g.left=Math.max(0,g.left);
    this.env.indicator_width=d;
    this.env.indicator_height=e;
    h>d&&(h=d,f=100);
    b.title&&(b.title=this.get_label("quota")+": "+b.title);
    var i=$("<div>");
    i.css({
        position:"absolute",
        top:g.top,
        left:g.left,
        width:d+"px",
        height:e+"px",
        zIndex:100,
        lineHeight:e+"px"
        }).attr("title",b.title).addClass("quota_text").html(f+"%");
    var j=$("<div>");
    j.css({
        position:"absolute",
        top:g.top+1,
        left:g.left+1,
        width:h+"px",
        height:e+"px",
        zIndex:99
    });
    h=$("<div>");
    h.css({
        position:"absolute",
        top:g.top+1,
        left:g.left+1,
        width:d+"px",
        height:e+"px",
        zIndex:98
    }).addClass("quota_bg");
    80<=f?(i.addClass(" quota_text_high"),
        j.addClass("quota_high")):55<=f?(i.addClass(" quota_text_mid"),j.addClass("quota_mid")):(i.addClass(" quota_text_low"),j.addClass("quota_low"));
    $(a).html("").append(j).append(h).append(i);
    $("#quotaimg").attr("title",b.title)
    };
    
this.html2plain=function(a,b){
    var d=this,e=this.set_busy(!0,"converting");
    this.log("HTTP POST: ?_task=utils&_action=html2text");
    $.ajax({
        type:"POST",
        url:"?_task=utils&_action=html2text",
        data:a,
        contentType:"application/octet-stream",
        error:function(a,b,g){
            d.http_error(a,b,g,e)
            },
        success:function(a){
            d.set_busy(!1,null,e);
            $("#"+b).val(a);
            d.log(a)
            }
        })
};

this.plain2html=function(a,b){
    var d=this.set_busy(!0,"converting"),a=a.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
    $("#"+b).val(a?"<pre>"+a+"</pre>":"");
    this.set_busy(!1,null,d)
    };
    
this.url=function(a,b){
    var d="string"===typeof b?"&"+b:"";
    if("string"!==typeof a)b=a;
    else if(!b||"object"!==typeof b)b={};
        
    b._action=a?a:this.env.action;
    var e=this.env.comm_path;
    b._action.match(/([a-z]+)\/([a-z0-9-_.]+)/)&&(b._action=
        RegExp.$2,e=e.replace(/\_task=[a-z]+/,"_task="+RegExp.$1));
    var f={},h;
    for(h in b)void 0!==b[h]&&null!==b[h]&&(f[h]=b[h]);return e+"&"+$.param(f)+d
    };
    
this.redirect=function(a,b){
    (b||null===b)&&this.set_busy(!0);
    this.is_framed()?parent.rcmail.redirect(a,b):this.location_href(a,window)
    };
    
this.goto_url=function(a,b){
    this.redirect(this.url(a,b))
    };
    
this.location_href=function(a,b,d){
    d&&this.lock_frame();
    bw.ie&&b==window?$("<a>").attr("href",a).appendTo(document.body).get(0).click():b.location.href=a
    };
    
this.http_request=
function(a,b,d){
    var e=this.url(a,b),a=this.triggerEvent("request"+a,b);
    if(void 0!==a){
        if(!1===a)return!1;
        b=a
        }
        e+="&_remote=1";
    this.log("HTTP GET: "+e);
    return $.ajax({
        type:"GET",
        url:e,
        data:{
            _unlock:d?d:0
            },
        dataType:"json",
        success:function(a){
            i.http_response(a)
            },
        error:function(a,b,e){
            rcmail.http_error(a,b,e,d)
            }
        })
};

this.http_post=function(a,b,d){
    var e=this.url(a);
    b&&"object"===typeof b?(b._remote=1,b._unlock=d?d:0):b+=(b?"&":"")+"_remote=1"+(d?"&_unlock="+d:"");
    a=this.triggerEvent("request"+a,b);
    if(void 0!==
        a){
        if(!1===a)return!1;
        b=a
        }
        this.log("HTTP POST: "+e);
    return $.ajax({
        type:"POST",
        url:e,
        data:b,
        dataType:"json",
        success:function(a){
            i.http_response(a)
            },
        error:function(a,b,e){
            rcmail.http_error(a,b,e,d)
            }
        })
};

this.abort_request=function(a){
    a.request&&a.request.abort();
    a.lock&&this.set_busy(!1,null,a.lock)
    };
    
this.http_response=function(a){
    if(a){
        a.unlock&&this.set_busy(!1);
        this.triggerEvent("responsebefore",{
            response:a
        });
        this.triggerEvent("responsebefore"+a.action,{
            response:a
        });
        a.env&&this.set_env(a.env);
        if("object"===
            typeof a.texts)for(var b in a.texts)"string"===typeof a.texts[b]&&this.add_label(b,a.texts[b]);a.exec&&(this.log(a.exec),eval(a.exec));
        if(a.callbacks&&a.callbacks.length)for(b=0;b<a.callbacks.length;b++)this.triggerEvent(a.callbacks[b][0],a.callbacks[b][1]);
        switch(a.action){
            case "delete":
                if("addressbook"==this.task){
                var d;
                b=this.contact_list.get_selection();
                d=!1;
                b&&this.contact_list.rows[b]&&(d=""==this.env.source?(d=(""+b).replace(/^[^-]+-/,""))&&this.env.address_sources[d]&&!this.env.address_sources[d].readonly:
                    !this.env.address_sources[this.env.source].readonly);
                this.enable_command("compose",b&&this.contact_list.rows[b]);
                this.enable_command("delete","edit",d);
                this.enable_command("export",this.contact_list&&0<this.contact_list.rowcount)
                }
                case "moveto":
                "show"==this.env.action?(this.enable_command(this.env.message_commands,!0),this.env.list_post||this.enable_command("reply-list",!1)):"addressbook"==this.task&&this.triggerEvent("listupdate",{
                folder:this.env.source,
                rowcount:this.contact_list.rowcount
                });
            case "purge":case "expunge":
                "mail"==
                this.task&&(this.env.messagecount||(this.env.contentframe&&this.show_contentframe(!1),this.enable_command(this.env.message_commands,"purge","expunge","select-all","select-none","sort","expand-all","expand-unread","collapse-all",!1)),this.message_list&&this.triggerEvent("listupdate",{
                    folder:this.env.mailbox,
                    rowcount:this.message_list.rowcount
                    }));
                break;
            case "check-recent":case "getunread":case "search":
                this.env.qsearch=null;
            case "list":
                if("mail"==this.task){
                if(this.enable_command("show","expunge","select-all",
                    "select-none","sort",0<this.env.messagecount),this.enable_command("purge",this.purge_mailbox_test()),this.enable_command("expand-all","expand-unread","collapse-all",this.env.threading&&this.env.messagecount),"list"==a.action||"search"==a.action)this.msglist_select(this.message_list),this.triggerEvent("listupdate",{
                    folder:this.env.mailbox,
                    rowcount:this.message_list.rowcount
                    })
                }else if("addressbook"==this.task&&(this.enable_command("export",this.contact_list&&0<this.contact_list.rowcount),"list"==a.action||
                "search"==a.action))this.enable_command("search-create",""==this.env.source),this.enable_command("search-delete",this.env.search_id),this.update_group_commands(),this.triggerEvent("listupdate",{
                folder:this.env.source,
                rowcount:this.contact_list.rowcount
                })
            }
            a.unlock&&this.hide_message(a.unlock);
        this.triggerEvent("responseafter",{
            response:a
        });
        this.triggerEvent("responseafter"+a.action,{
            response:a
        })
        }
    };

this.http_error=function(a,b,d,e){
    b=a.statusText;
    this.set_busy(!1,null,e);
    a.abort();
    a.status&&b&&this.display_message(this.get_label("servererror")+
        " ("+b+")","error")
    };
    
this.async_upload_form=function(a,b,d){
    var e=(new Date).getTime(),f="rcmupload"+e;
    if(this.env.upload_progress_name){
        var h=this.env.upload_progress_name,g=$("input[name="+h+"]",a);
        g.length||(g=$("<input>").attr({
            type:"hidden",
            name:h
        }),g.prependTo(a));
        g.val(e)
        }
        document.all?document.body.insertAdjacentHTML("BeforeEnd",'<iframe name="'+f+'" src="program/blank.gif" style="width:0;height:0;visibility:hidden;"></iframe>'):(h=document.createElement("iframe"),h.name=f,h.style.border="none",
        h.style.width=0,h.style.height=0,h.style.visibility="hidden",document.body.appendChild(h));
    $(f).bind("load",{
        ts:e
    },d);
    $(a).attr({
        target:f,
        action:this.url(b,{
            _id:this.env.compose_id||"",
            _uploadid:e
        }),
        method:"POST"
    }).attr(a.encoding?"encoding":"enctype","multipart/form-data").submit();
    return f
    };
    
this.start_keepalive=function(){
    this._int&&clearInterval(this._int);
    this.env.keep_alive&&!this.env.framed&&"mail"==this.task&&this.gui_objects.mailboxlist?this._int=setInterval(function(){
        i.check_for_recent(!1)
        },
    1E3*this.env.keep_alive):this.env.keep_alive&&!this.env.framed&&"login"!=this.task&&"print"!=this.env.action&&(this._int=setInterval(function(){
        i.keep_alive()
        },1E3*this.env.keep_alive))
    };
    
this.keep_alive=function(){
    this.busy||this.http_request("keep-alive")
    };
    
this.check_for_recent=function(a){
    if(!this.busy){
        var b,d="_mbox="+urlencode(this.env.mailbox);
        a&&(b=this.set_busy(!0,"checkingmail"),d+="&_refresh=1",this.start_keepalive());
        this.gui_objects.messagelist&&(d+="&_list=1");
        this.gui_objects.quotadisplay&&
        (d+="&_quota=1");
        this.env.search_request&&(d+="&_search="+this.env.search_request);
        this.http_request("check-recent",d,b)
        }
    };

this.get_single_uid=function(){
    return this.env.uid?this.env.uid:this.message_list?this.message_list.get_single_selection():null
    };
    
this.get_single_cid=function(){
    return this.env.cid?this.env.cid:this.contact_list?this.contact_list.get_single_selection():null
    };
    
this.get_caret_pos=function(a){
    if(void 0!==a.selectionEnd)return a.selectionEnd;
    if(document.selection&&document.selection.createRange){
        var b=
        document.selection.createRange();
        if(b.parentElement()!=a)return 0;
        var d=b.duplicate();
        "TEXTAREA"==a.tagName?d.moveToElementText(a):d.expand("textedit");
        d.setEndPoint("EndToStart",b);
        b=d.text.length;
        return b<=a.value.length?b:-1
        }
        return a.value.length
    };
    
this.set_caret_pos=function(a,b){
    if(a.setSelectionRange)a.setSelectionRange(b,b);
    else if(a.createTextRange){
        var d=a.createTextRange();
        d.collapse(!0);
        d.moveEnd("character",b);
        d.moveStart("character",b);
        d.select()
        }
    };

this.lock_form=function(a,b){
    if(a&&a.elements){
        var d,
        e,f;
        b&&(this.disabled_form_elements=[]);
        d=0;
        for(e=a.elements.length;d<e;d++)if(f=a.elements[d],"hidden"!=f.type)if(b&&f.disabled)this.disabled_form_elements.push(f);
            else if(b||this.disabled_form_elements&&0>$.inArray(f,this.disabled_form_elements))f.disabled=b
            }
        }
}
rcube_webmail.long_subject_title=function(i,s){
    if(!i.title){
        var a=$(i);
        a.width()+15*s>a.parent().width()&&(i.title=a.html())
        }
    };
rcube_webmail.long_subject_title_ie=function(i,s){
    if(!i.title){
        var a=$(i),b=$.trim(a.text()),d=$("<span>").text(b).css({
            position:"absolute",
            "float":"left",
            visibility:"hidden",
            "font-size":a.css("font-size"),
            "font-weight":a.css("font-weight")
            }).appendTo($("body")),e=d.width();
        d.remove();
        e+15*s>a.width()&&(i.title=b)
        }
    };

rcube_webmail.prototype.addEventListener=rcube_event_engine.prototype.addEventListener;
rcube_webmail.prototype.removeEventListener=rcube_event_engine.prototype.removeEventListener;
rcube_webmail.prototype.triggerEvent=rcube_event_engine.prototype.triggerEvent;
