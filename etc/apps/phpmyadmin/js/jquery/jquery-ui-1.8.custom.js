jQuery.ui||function(b){
    b.ui={
        version:"1.8",
        plugin:{
            add:function(c,f,d){
                c=b.ui[c].prototype;
                for(var a in d){
                    c.plugins[a]=c.plugins[a]||[];
                    c.plugins[a].push([f,d[a]])
                    }
                },
        call:function(c,f,d){
            if((f=c.plugins[f])&&c.element[0].parentNode)for(var a=0;a<f.length;a++)c.options[f[a][0]]&&f[a][1].apply(c.element,d)
                }
            },
contains:function(c,f){
    return document.compareDocumentPosition?c.compareDocumentPosition(f)&16:c!==f&&c.contains(f)
    },
hasScroll:function(c,f){
    if(b(c).css("overflow")=="hidden")return false;
    var d=
    f&&f=="left"?"scrollLeft":"scrollTop",a=false;
    if(c[d]>0)return true;
    c[d]=1;
    a=c[d]>0;
    c[d]=0;
    return a
    },
isOverAxis:function(c,f,d){
    return c>f&&c<f+d
    },
isOver:function(c,f,d,a,e,g){
    return b.ui.isOverAxis(c,d,e)&&b.ui.isOverAxis(f,a,g)
    },
keyCode:{
    BACKSPACE:8,
    CAPS_LOCK:20,
    COMMA:188,
    CONTROL:17,
    DELETE:46,
    DOWN:40,
    END:35,
    ENTER:13,
    ESCAPE:27,
    HOME:36,
    INSERT:45,
    LEFT:37,
    NUMPAD_ADD:107,
    NUMPAD_DECIMAL:110,
    NUMPAD_DIVIDE:111,
    NUMPAD_ENTER:108,
    NUMPAD_MULTIPLY:106,
    NUMPAD_SUBTRACT:109,
    PAGE_DOWN:34,
    PAGE_UP:33,
    PERIOD:190,
    RIGHT:39,
    SHIFT:16,
    SPACE:32,
    TAB:9,
    UP:38
}
};

b.fn.extend({
    _focus:b.fn.focus,
    focus:function(c,f){
        return typeof c==="number"?this.each(function(){
            var d=this;
            setTimeout(function(){
                b(d).focus();
                f&&f.call(d)
                },c)
            }):this._focus.apply(this,arguments)
        },
    enableSelection:function(){
        return this.attr("unselectable","off").css("MozUserSelect","").unbind("selectstart.ui")
        },
    disableSelection:function(){
        return this.attr("unselectable","on").css("MozUserSelect","none").bind("selectstart.ui",function(){
            return false
            })
        },
    scrollParent:function(){
        var c;
        c=b.browser.msie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){
            return/(relative|absolute|fixed)/.test(b.curCSS(this,"position",1))&&/(auto|scroll)/.test(b.curCSS(this,"overflow",1)+b.curCSS(this,"overflow-y",1)+b.curCSS(this,"overflow-x",1))
            }).eq(0):this.parents().filter(function(){
            return/(auto|scroll)/.test(b.curCSS(this,"overflow",1)+b.curCSS(this,"overflow-y",1)+b.curCSS(this,"overflow-x",1))
            }).eq(0);
        return/fixed/.test(this.css("position"))||
        !c.length?b(document):c
        },
    zIndex:function(c){
        if(c!==undefined)return this.css("zIndex",c);
        if(this.length){
            c=b(this[0]);
            for(var f;c.length&&c[0]!==document;){
                f=c.css("position");
                if(f=="absolute"||f=="relative"||f=="fixed"){
                    f=parseInt(c.css("zIndex"));
                    if(!isNaN(f)&&f!=0)return f
                        }
                        c=c.parent()
                }
            }
            return 0
    }
});
b.extend(b.expr[":"],{
    data:function(c,f,d){
        return!!b.data(c,d[3])
        },
    focusable:function(c){
        var f=c.nodeName.toLowerCase(),d=b.attr(c,"tabindex");
        return(/input|select|textarea|button|object/.test(f)?!c.disabled:
            "a"==f||"area"==f?c.href||!isNaN(d):!isNaN(d))&&!b(c)["area"==f?"parents":"closest"](":hidden").length
        },
    tabbable:function(c){
        var f=b.attr(c,"tabindex");
        return(isNaN(f)||f>=0)&&b(c).is(":focusable")
        }
    })
}(jQuery);
(function(b){
    var c=b.fn.remove;
    b.fn.remove=function(f,d){
        return this.each(function(){
            if(!d)if(!f||b.filter(f,[this]).length)b("*",this).add(this).each(function(){
                b(this).triggerHandler("remove")
                });
            return c.call(b(this),f,d)
            })
        };
        
    b.widget=function(f,d,a){
        var e=f.split(".")[0],g;
        f=f.split(".")[1];
        g=e+"-"+f;
        if(!a){
            a=d;
            d=b.Widget
            }
            b.expr[":"][g]=function(i){
            return!!b.data(i,f)
            };
            
        b[e]=b[e]||{};
        
        b[e][f]=function(i,k){
            arguments.length&&this._createWidget(i,k)
            };
            
        d=new d;
        d.options=b.extend({},d.options);
        b[e][f].prototype=
        b.extend(true,d,{
            namespace:e,
            widgetName:f,
            widgetEventPrefix:b[e][f].prototype.widgetEventPrefix||f,
            widgetBaseClass:g
        },a);
        b.widget.bridge(f,b[e][f])
        };
        
    b.widget.bridge=function(f,d){
        b.fn[f]=function(a){
            var e=typeof a==="string",g=Array.prototype.slice.call(arguments,1),i=this;
            a=!e&&g.length?b.extend.apply(null,[true,a].concat(g)):a;
            if(e&&a.substring(0,1)==="_")return i;
            e?this.each(function(){
                var k=b.data(this,f),j=k&&b.isFunction(k[a])?k[a].apply(k,g):k;
                if(j!==k&&j!==undefined){
                    i=j;
                    return false
                    }
                }):this.each(function(){
            var k=
            b.data(this,f);
            if(k){
                a&&k.option(a);
                k._init()
                }else b.data(this,f,new d(a,this))
                });
        return i
        }
    };

b.Widget=function(f,d){
    arguments.length&&this._createWidget(f,d)
    };
    
b.Widget.prototype={
    widgetName:"widget",
    widgetEventPrefix:"",
    options:{
        disabled:false
    },
    _createWidget:function(f,d){
        this.element=b(d).data(this.widgetName,this);
        this.options=b.extend(true,{},this.options,b.metadata&&b.metadata.get(d)[this.widgetName],f);
        var a=this;
        this.element.bind("remove."+this.widgetName,function(){
            a.destroy()
            });
        this._create();
        this._init()
        },
    _create:function(){},
    _init:function(){},
    destroy:function(){
        this.element.unbind("."+this.widgetName).removeData(this.widgetName);
        this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled "+this.namespace+"-state-disabled")
        },
    widget:function(){
        return this.element
        },
    option:function(f,d){
        var a=f,e=this;
        if(arguments.length===0)return b.extend({},e.options);
        if(typeof f==="string"){
            if(d===undefined)return this.options[f];
            a={};
            
            a[f]=d
            }
            b.each(a,
            function(g,i){
                e._setOption(g,i)
                });
        return e
        },
    _setOption:function(f,d){
        this.options[f]=d;
        if(f==="disabled")this.widget()[d?"addClass":"removeClass"](this.widgetBaseClass+"-disabled "+this.namespace+"-state-disabled").attr("aria-disabled",d);
        return this
        },
    enable:function(){
        return this._setOption("disabled",false)
        },
    disable:function(){
        return this._setOption("disabled",true)
        },
    _trigger:function(f,d,a){
        var e=this.options[f];
        d=b.Event(d);
        d.type=(f===this.widgetEventPrefix?f:this.widgetEventPrefix+f).toLowerCase();
        a=a||{};
        
        if(d.originalEvent){
            f=b.event.props.length;
            for(var g;f;){
                g=b.event.props[--f];
                d[g]=d.originalEvent[g]
                }
            }
            this.element.trigger(d,a);
    return!(b.isFunction(e)&&e.call(this.element[0],d,a)===false||d.isDefaultPrevented())
    }
}
})(jQuery);
(function(b){
    b.widget("ui.mouse",{
        options:{
            cancel:":input,option",
            distance:1,
            delay:0
        },
        _mouseInit:function(){
            var c=this;
            this.element.bind("mousedown."+this.widgetName,function(f){
                return c._mouseDown(f)
                }).bind("click."+this.widgetName,function(f){
                if(c._preventClickEvent){
                    c._preventClickEvent=false;
                    f.stopImmediatePropagation();
                    return false
                    }
                });
        this.started=false
        },
    _mouseDestroy:function(){
        this.element.unbind("."+this.widgetName)
        },
    _mouseDown:function(c){
        c.originalEvent=c.originalEvent||{};
        
        if(!c.originalEvent.mouseHandled){
            this._mouseStarted&&
            this._mouseUp(c);
            this._mouseDownEvent=c;
            var f=this,d=c.which==1,a=typeof this.options.cancel=="string"?b(c.target).parents().add(c.target).filter(this.options.cancel).length:false;
            if(!d||a||!this._mouseCapture(c))return true;
            this.mouseDelayMet=!this.options.delay;
            if(!this.mouseDelayMet)this._mouseDelayTimer=setTimeout(function(){
                f.mouseDelayMet=true
                },this.options.delay);
            if(this._mouseDistanceMet(c)&&this._mouseDelayMet(c)){
                this._mouseStarted=this._mouseStart(c)!==false;
                if(!this._mouseStarted){
                    c.preventDefault();
                    return true
                    }
                }
            this._mouseMoveDelegate=function(e){
            return f._mouseMove(e)
            };
            
        this._mouseUpDelegate=function(e){
            return f._mouseUp(e)
            };
            
        b(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate);
        b.browser.safari||c.preventDefault();
        return c.originalEvent.mouseHandled=true
        }
    },
_mouseMove:function(c){
    if(b.browser.msie&&!c.button)return this._mouseUp(c);
    if(this._mouseStarted){
        this._mouseDrag(c);
        return c.preventDefault()
        }
        if(this._mouseDistanceMet(c)&&
        this._mouseDelayMet(c))(this._mouseStarted=this._mouseStart(this._mouseDownEvent,c)!==false)?this._mouseDrag(c):this._mouseUp(c);
    return!this._mouseStarted
    },
_mouseUp:function(c){
    b(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate);
    if(this._mouseStarted){
        this._mouseStarted=false;
        this._preventClickEvent=c.target==this._mouseDownEvent.target;
        this._mouseStop(c)
        }
        return false
    },
_mouseDistanceMet:function(c){
    return Math.max(Math.abs(this._mouseDownEvent.pageX-
        c.pageX),Math.abs(this._mouseDownEvent.pageY-c.pageY))>=this.options.distance
    },
_mouseDelayMet:function(){
    return this.mouseDelayMet
    },
_mouseStart:function(){},
    _mouseDrag:function(){},
    _mouseStop:function(){},
    _mouseCapture:function(){
    return true
    }
})
})(jQuery);
(function(b){
    b.ui=b.ui||{};
    
    var c=/left|center|right/,f=/top|center|bottom/,d=b.fn.position,a=b.fn.offset;
    b.fn.position=function(e){
        if(!e||!e.of)return d.apply(this,arguments);
        e=b.extend({},e);
        var g=b(e.of),i=(e.collision||"flip").split(" "),k=e.offset?e.offset.split(" "):[0,0],j,h,l;
        if(e.of.nodeType===9){
            j=g.width();
            h=g.height();
            l={
                top:0,
                left:0
            }
        }else if(e.of.scrollTo&&e.of.document){
        j=g.width();
        h=g.height();
        l={
            top:g.scrollTop(),
            left:g.scrollLeft()
            }
        }else if(e.of.preventDefault){
    e.at="left top";
    j=h=
    0;
    l={
        top:e.of.pageY,
        left:e.of.pageX
        }
    }else{
    j=g.outerWidth();
    h=g.outerHeight();
    l=g.offset()
    }
    b.each(["my","at"],function(){
    var m=(e[this]||"").split(" ");
    if(m.length===1)m=c.test(m[0])?m.concat(["center"]):f.test(m[0])?["center"].concat(m):["center","center"];
    m[0]=c.test(m[0])?m[0]:"center";
    m[1]=f.test(m[1])?m[1]:"center";
    e[this]=m
    });
if(i.length===1)i[1]=i[0];
    k[0]=parseInt(k[0],10)||0;
    if(k.length===1)k[1]=k[0];
    k[1]=parseInt(k[1],10)||0;
    if(e.at[0]==="right")l.left+=j;
    else if(e.at[0]==="center")l.left+=
    j/2;
if(e.at[1]==="bottom")l.top+=h;
    else if(e.at[1]==="center")l.top+=h/2;
    l.left+=k[0];
    l.top+=k[1];
    return this.each(function(){
    var m=b(this),n=m.outerWidth(),o=m.outerHeight(),p=b.extend({},l);
    if(e.my[0]==="right")p.left-=n;
    else if(e.my[0]==="center")p.left-=n/2;
    if(e.my[1]==="bottom")p.top-=o;
    else if(e.my[1]==="center")p.top-=o/2;
    b.each(["left","top"],function(q,r){
        b.ui.position[i[q]]&&b.ui.position[i[q]][r](p,{
            targetWidth:j,
            targetHeight:h,
            elemWidth:n,
            elemHeight:o,
            offset:k,
            my:e.my,
            at:e.at
            })
        });
    b.fn.bgiframe&&
    m.bgiframe();
    m.offset(b.extend(p,{
        using:e.using
        }))
    })
};

b.ui.position={
    fit:{
        left:function(e,g){
            var i=b(window);
            i=e.left+g.elemWidth-i.width()-i.scrollLeft();
            e.left=i>0?e.left-i:Math.max(0,e.left)
            },
        top:function(e,g){
            var i=b(window);
            i=e.top+g.elemHeight-i.height()-i.scrollTop();
            e.top=i>0?e.top-i:Math.max(0,e.top)
            }
        },
flip:{
    left:function(e,g){
        if(g.at[0]!=="center"){
            var i=b(window);
            i=e.left+g.elemWidth-i.width()-i.scrollLeft();
            var k=g.my[0]==="left"?-g.elemWidth:g.my[0]==="right"?g.elemWidth:0,j=-2*g.offset[0];
            e.left+=e.left<0?k+g.targetWidth+j:i>0?k-g.targetWidth+j:0
            }
        },
top:function(e,g){
    if(g.at[1]!=="center"){
        var i=b(window);
        i=e.top+g.elemHeight-i.height()-i.scrollTop();
        var k=g.my[1]==="top"?-g.elemHeight:g.my[1]==="bottom"?g.elemHeight:0,j=g.at[1]==="top"?g.targetHeight:-g.targetHeight,h=-2*g.offset[1];
        e.top+=e.top<0?k+g.targetHeight+h:i>0?k+j+h:0
        }
    }
}
};

if(!b.offset.setOffset){
    b.offset.setOffset=function(e,g){
        if(/static/.test(b.curCSS(e,"position")))e.style.position="relative";
        var i=b(e),k=i.offset(),j=
        parseInt(b.curCSS(e,"top",true),10)||0,h=parseInt(b.curCSS(e,"left",true),10)||0;
        k={
            top:g.top-k.top+j,
            left:g.left-k.left+h
            };
            
        "using"in g?g.using.call(e,k):i.css(k)
        };
        
    b.fn.offset=function(e){
        var g=this[0];
        if(!g||!g.ownerDocument)return null;
        if(e)return this.each(function(){
            b.offset.setOffset(this,e)
            });
        return a.call(this)
        }
    }
})(jQuery);
(function(b){
    b.widget("ui.draggable",b.ui.mouse,{
        widgetEventPrefix:"drag",
        options:{
            addClasses:true,
            appendTo:"parent",
            axis:false,
            connectToSortable:false,
            containment:false,
            cursor:"auto",
            cursorAt:false,
            grid:false,
            handle:false,
            helper:"original",
            iframeFix:false,
            opacity:false,
            refreshPositions:false,
            revert:false,
            revertDuration:500,
            scope:"default",
            scroll:true,
            scrollSensitivity:20,
            scrollSpeed:20,
            snap:false,
            snapMode:"both",
            snapTolerance:20,
            stack:false,
            zIndex:false
        },
        _create:function(){
            if(this.options.helper==
                "original"&&!/^(?:r|a|f)/.test(this.element.css("position")))this.element[0].style.position="relative";
            this.options.addClasses&&this.element.addClass("ui-draggable");
            this.options.disabled&&this.element.addClass("ui-draggable-disabled");
            this._mouseInit()
            },
        destroy:function(){
            if(this.element.data("draggable")){
                this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled");
                this._mouseDestroy();
                return this
                }
            },
    _mouseCapture:function(c){
        var f=
        this.options;
        if(this.helper||f.disabled||b(c.target).is(".ui-resizable-handle"))return false;
        this.handle=this._getHandle(c);
        if(!this.handle)return false;
        return true
        },
    _mouseStart:function(c){
        var f=this.options;
        this.helper=this._createHelper(c);
        this._cacheHelperProportions();
        if(b.ui.ddmanager)b.ui.ddmanager.current=this;
        this._cacheMargins();
        this.cssPosition=this.helper.css("position");
        this.scrollParent=this.helper.scrollParent();
        this.offset=this.positionAbs=this.element.offset();
        this.offset={
            top:this.offset.top-
            this.margins.top,
            left:this.offset.left-this.margins.left
            };
            
        b.extend(this.offset,{
            click:{
                left:c.pageX-this.offset.left,
                top:c.pageY-this.offset.top
                },
            parent:this._getParentOffset(),
            relative:this._getRelativeOffset()
            });
        this.originalPosition=this.position=this._generatePosition(c);
        this.originalPageX=c.pageX;
        this.originalPageY=c.pageY;
        f.cursorAt&&this._adjustOffsetFromHelper(f.cursorAt);
        f.containment&&this._setContainment();
        if(this._trigger("start",c)===false){
            this._clear();
            return false
            }
            this._cacheHelperProportions();
        b.ui.ddmanager&&!f.dropBehaviour&&b.ui.ddmanager.prepareOffsets(this,c);
        this.helper.addClass("ui-draggable-dragging");
        this._mouseDrag(c,true);
        return true
        },
    _mouseDrag:function(c,f){
        this.position=this._generatePosition(c);
        this.positionAbs=this._convertPositionTo("absolute");
        if(!f){
            var d=this._uiHash();
            if(this._trigger("drag",c,d)===false){
                this._mouseUp({});
                return false
                }
                this.position=d.position
            }
            if(!this.options.axis||this.options.axis!="y")this.helper[0].style.left=this.position.left+"px";
        if(!this.options.axis||
            this.options.axis!="x")this.helper[0].style.top=this.position.top+"px";
        b.ui.ddmanager&&b.ui.ddmanager.drag(this,c);
        return false
        },
    _mouseStop:function(c){
        var f=false;
        if(b.ui.ddmanager&&!this.options.dropBehaviour)f=b.ui.ddmanager.drop(this,c);
        if(this.dropped){
            f=this.dropped;
            this.dropped=false
            }
            if(!this.element[0]||!this.element[0].parentNode)return false;
        if(this.options.revert=="invalid"&&!f||this.options.revert=="valid"&&f||this.options.revert===true||b.isFunction(this.options.revert)&&this.options.revert.call(this.element,
            f)){
            var d=this;
            b(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){
                d._trigger("stop",c)!==false&&d._clear()
                })
            }else this._trigger("stop",c)!==false&&this._clear();
        return false
        },
    cancel:function(){
        this.helper.is(".ui-draggable-dragging")?this._mouseUp({}):this._clear();
        return this
        },
    _getHandle:function(c){
        var f=!this.options.handle||!b(this.options.handle,this.element).length?true:false;
        b(this.options.handle,this.element).find("*").andSelf().each(function(){
            if(this==
                c.target)f=true
                });
        return f
        },
    _createHelper:function(c){
        var f=this.options;
        c=b.isFunction(f.helper)?b(f.helper.apply(this.element[0],[c])):f.helper=="clone"?this.element.clone():this.element;
        c.parents("body").length||c.appendTo(f.appendTo=="parent"?this.element[0].parentNode:f.appendTo);
        c[0]!=this.element[0]&&!/(fixed|absolute)/.test(c.css("position"))&&c.css("position","absolute");
        return c
        },
    _adjustOffsetFromHelper:function(c){
        if(typeof c=="string")c=c.split(" ");
        if(b.isArray(c))c={
            left:+c[0],
            top:+c[1]||
            0
            };
            
        if("left"in c)this.offset.click.left=c.left+this.margins.left;
        if("right"in c)this.offset.click.left=this.helperProportions.width-c.right+this.margins.left;
        if("top"in c)this.offset.click.top=c.top+this.margins.top;
        if("bottom"in c)this.offset.click.top=this.helperProportions.height-c.bottom+this.margins.top
            },
    _getParentOffset:function(){
        this.offsetParent=this.helper.offsetParent();
        var c=this.offsetParent.offset();
        if(this.cssPosition=="absolute"&&this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],
            this.offsetParent[0])){
            c.left+=this.scrollParent.scrollLeft();
            c.top+=this.scrollParent.scrollTop()
            }
            if(this.offsetParent[0]==document.body||this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()=="html"&&b.browser.msie)c={
            top:0,
            left:0
        };
        
        return{
            top:c.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),
            left:c.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)
            }
        },
    _getRelativeOffset:function(){
        if(this.cssPosition=="relative"){
            var c=this.element.position();
            return{
                top:c.top-
                (parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),
                left:c.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()
                }
            }else return{
        top:0,
        left:0
    }
    },
_cacheMargins:function(){
    this.margins={
        left:parseInt(this.element.css("marginLeft"),10)||0,
        top:parseInt(this.element.css("marginTop"),10)||0
        }
    },
_cacheHelperProportions:function(){
    this.helperProportions={
        width:this.helper.outerWidth(),
        height:this.helper.outerHeight()
        }
    },
_setContainment:function(){
    var c=this.options;
    if(c.containment==
        "parent")c.containment=this.helper[0].parentNode;
    if(c.containment=="document"||c.containment=="window")this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,b(c.containment=="document"?document:window).width()-this.helperProportions.width-this.margins.left,(b(c.containment=="document"?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];
    if(!/^(document|window|parent)$/.test(c.containment)&&
        c.containment.constructor!=Array){
        var f=b(c.containment)[0];
        if(f){
            c=b(c.containment).offset();
            var d=b(f).css("overflow")!="hidden";
            this.containment=[c.left+(parseInt(b(f).css("borderLeftWidth"),10)||0)+(parseInt(b(f).css("paddingLeft"),10)||0)-this.margins.left,c.top+(parseInt(b(f).css("borderTopWidth"),10)||0)+(parseInt(b(f).css("paddingTop"),10)||0)-this.margins.top,c.left+(d?Math.max(f.scrollWidth,f.offsetWidth):f.offsetWidth)-(parseInt(b(f).css("borderLeftWidth"),10)||0)-(parseInt(b(f).css("paddingRight"),
                10)||0)-this.helperProportions.width-this.margins.left,c.top+(d?Math.max(f.scrollHeight,f.offsetHeight):f.offsetHeight)-(parseInt(b(f).css("borderTopWidth"),10)||0)-(parseInt(b(f).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top]
            }
        }else if(c.containment.constructor==Array)this.containment=c.containment
    },
_convertPositionTo:function(c,f){
    if(!f)f=this.position;
    var d=c=="absolute"?1:-1,a=this.cssPosition=="absolute"&&!(this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],
        this.offsetParent[0]))?this.offsetParent:this.scrollParent,e=/(html|body)/i.test(a[0].tagName);
    return{
        top:f.top+this.offset.relative.top*d+this.offset.parent.top*d-(b.browser.safari&&b.browser.version<526&&this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollTop():e?0:a.scrollTop())*d),
        left:f.left+this.offset.relative.left*d+this.offset.parent.left*d-(b.browser.safari&&b.browser.version<526&&this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():
            e?0:a.scrollLeft())*d)
        }
    },
_generatePosition:function(c){
    var f=this.options,d=this.cssPosition=="absolute"&&!(this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,a=/(html|body)/i.test(d[0].tagName),e=c.pageX,g=c.pageY;
    if(this.originalPosition){
        if(this.containment){
            if(c.pageX-this.offset.click.left<this.containment[0])e=this.containment[0]+this.offset.click.left;
            if(c.pageY-this.offset.click.top<this.containment[1])g=this.containment[1]+
                this.offset.click.top;
            if(c.pageX-this.offset.click.left>this.containment[2])e=this.containment[2]+this.offset.click.left;
            if(c.pageY-this.offset.click.top>this.containment[3])g=this.containment[3]+this.offset.click.top
                }
                if(f.grid){
            g=this.originalPageY+Math.round((g-this.originalPageY)/f.grid[1])*f.grid[1];
            g=this.containment?!(g-this.offset.click.top<this.containment[1]||g-this.offset.click.top>this.containment[3])?g:!(g-this.offset.click.top<this.containment[1])?g-f.grid[1]:g+f.grid[1]:g;
            e=this.originalPageX+
            Math.round((e-this.originalPageX)/f.grid[0])*f.grid[0];
            e=this.containment?!(e-this.offset.click.left<this.containment[0]||e-this.offset.click.left>this.containment[2])?e:!(e-this.offset.click.left<this.containment[0])?e-f.grid[0]:e+f.grid[0]:e
            }
        }
    return{
    top:g-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+(b.browser.safari&&b.browser.version<526&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollTop():a?0:d.scrollTop()),
    left:e-this.offset.click.left-
    this.offset.relative.left-this.offset.parent.left+(b.browser.safari&&b.browser.version<526&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():a?0:d.scrollLeft())
    }
},
_clear:function(){
    this.helper.removeClass("ui-draggable-dragging");
    this.helper[0]!=this.element[0]&&!this.cancelHelperRemoval&&this.helper.remove();
    this.helper=null;
    this.cancelHelperRemoval=false
    },
_trigger:function(c,f,d){
    d=d||this._uiHash();
    b.ui.plugin.call(this,c,[f,d]);
    if(c=="drag")this.positionAbs=
        this._convertPositionTo("absolute");
    return b.Widget.prototype._trigger.call(this,c,f,d)
    },
plugins:{},
_uiHash:function(){
    return{
        helper:this.helper,
        position:this.position,
        originalPosition:this.originalPosition,
        offset:this.positionAbs
        }
    }
});
b.extend(b.ui.draggable,{
    version:"1.8"
});
b.ui.plugin.add("draggable","connectToSortable",{
    start:function(c,f){
        var d=b(this).data("draggable"),a=d.options,e=b.extend({},f,{
            item:d.element
            });
        d.sortables=[];
        b(a.connectToSortable).each(function(){
            var g=b.data(this,"sortable");
            if(g&&!g.options.disabled){
                d.sortables.push({
                    instance:g,
                    shouldRevert:g.options.revert
                    });
                g._refreshItems();
                g._trigger("activate",c,e)
                }
            })
    },
stop:function(c,f){
    var d=b(this).data("draggable"),a=b.extend({},f,{
        item:d.element
        });
    b.each(d.sortables,function(){
        if(this.instance.isOver){
            this.instance.isOver=0;
            d.cancelHelperRemoval=true;
            this.instance.cancelHelperRemoval=false;
            if(this.shouldRevert)this.instance.options.revert=true;
            this.instance._mouseStop(c);
            this.instance.options.helper=this.instance.options._helper;
            d.options.helper=="original"&&this.instance.currentItem.css({
                top:"auto",
                left:"auto"
            })
            }else{
            this.instance.cancelHelperRemoval=false;
            this.instance._trigger("deactivate",c,a)
            }
        })
},
drag:function(c,f){
    var d=b(this).data("draggable"),a=this;
    b.each(d.sortables,function(){
        this.instance.positionAbs=d.positionAbs;
        this.instance.helperProportions=d.helperProportions;
        this.instance.offset.click=d.offset.click;
        if(this.instance._intersectsWith(this.instance.containerCache)){
            if(!this.instance.isOver){
                this.instance.isOver=
                1;
                this.instance.currentItem=b(a).clone().appendTo(this.instance.element).data("sortable-item",true);
                this.instance.options._helper=this.instance.options.helper;
                this.instance.options.helper=function(){
                    return f.helper[0]
                    };
                    
                c.target=this.instance.currentItem[0];
                this.instance._mouseCapture(c,true);
                this.instance._mouseStart(c,true,true);
                this.instance.offset.click.top=d.offset.click.top;
                this.instance.offset.click.left=d.offset.click.left;
                this.instance.offset.parent.left-=d.offset.parent.left-this.instance.offset.parent.left;
                this.instance.offset.parent.top-=d.offset.parent.top-this.instance.offset.parent.top;
                d._trigger("toSortable",c);
                d.dropped=this.instance.element;
                d.currentItem=d.element;
                this.instance.fromOutside=d
                }
                this.instance.currentItem&&this.instance._mouseDrag(c)
            }else if(this.instance.isOver){
            this.instance.isOver=0;
            this.instance.cancelHelperRemoval=true;
            this.instance.options.revert=false;
            this.instance._trigger("out",c,this.instance._uiHash(this.instance));
            this.instance._mouseStop(c,true);
            this.instance.options.helper=
            this.instance.options._helper;
            this.instance.currentItem.remove();
            this.instance.placeholder&&this.instance.placeholder.remove();
            d._trigger("fromSortable",c);
            d.dropped=false
            }
        })
}
});
b.ui.plugin.add("draggable","cursor",{
    start:function(){
        var c=b("body"),f=b(this).data("draggable").options;
        if(c.css("cursor"))f._cursor=c.css("cursor");
        c.css("cursor",f.cursor)
        },
    stop:function(){
        var c=b(this).data("draggable").options;
        c._cursor&&b("body").css("cursor",c._cursor)
        }
    });
b.ui.plugin.add("draggable","iframeFix",{
    start:function(){
        var c=
        b(this).data("draggable").options;
        b(c.iframeFix===true?"iframe":c.iframeFix).each(function(){
            b('<div class="ui-draggable-iframeFix" style="background: #fff;"></div>').css({
                width:this.offsetWidth+"px",
                height:this.offsetHeight+"px",
                position:"absolute",
                opacity:"0.001",
                zIndex:1E3
            }).css(b(this).offset()).appendTo("body")
            })
        },
    stop:function(){
        b("div.ui-draggable-iframeFix").each(function(){
            this.parentNode.removeChild(this)
            })
        }
    });
b.ui.plugin.add("draggable","opacity",{
    start:function(c,f){
        var d=b(f.helper),
        a=b(this).data("draggable").options;
        if(d.css("opacity"))a._opacity=d.css("opacity");
        d.css("opacity",a.opacity)
        },
    stop:function(c,f){
        var d=b(this).data("draggable").options;
        d._opacity&&b(f.helper).css("opacity",d._opacity)
        }
    });
b.ui.plugin.add("draggable","scroll",{
    start:function(){
        var c=b(this).data("draggable");
        if(c.scrollParent[0]!=document&&c.scrollParent[0].tagName!="HTML")c.overflowOffset=c.scrollParent.offset()
            },
    drag:function(c){
        var f=b(this).data("draggable"),d=f.options,a=false;
        if(f.scrollParent[0]!=
            document&&f.scrollParent[0].tagName!="HTML"){
            if(!d.axis||d.axis!="x")if(f.overflowOffset.top+f.scrollParent[0].offsetHeight-c.pageY<d.scrollSensitivity)f.scrollParent[0].scrollTop=a=f.scrollParent[0].scrollTop+d.scrollSpeed;
                else if(c.pageY-f.overflowOffset.top<d.scrollSensitivity)f.scrollParent[0].scrollTop=a=f.scrollParent[0].scrollTop-d.scrollSpeed;
            if(!d.axis||d.axis!="y")if(f.overflowOffset.left+f.scrollParent[0].offsetWidth-c.pageX<d.scrollSensitivity)f.scrollParent[0].scrollLeft=a=f.scrollParent[0].scrollLeft+
                d.scrollSpeed;
            else if(c.pageX-f.overflowOffset.left<d.scrollSensitivity)f.scrollParent[0].scrollLeft=a=f.scrollParent[0].scrollLeft-d.scrollSpeed
                }else{
            if(!d.axis||d.axis!="x")if(c.pageY-b(document).scrollTop()<d.scrollSensitivity)a=b(document).scrollTop(b(document).scrollTop()-d.scrollSpeed);
                else if(b(window).height()-(c.pageY-b(document).scrollTop())<d.scrollSensitivity)a=b(document).scrollTop(b(document).scrollTop()+d.scrollSpeed);
            if(!d.axis||d.axis!="y")if(c.pageX-b(document).scrollLeft()<d.scrollSensitivity)a=
                b(document).scrollLeft(b(document).scrollLeft()-d.scrollSpeed);
            else if(b(window).width()-(c.pageX-b(document).scrollLeft())<d.scrollSensitivity)a=b(document).scrollLeft(b(document).scrollLeft()+d.scrollSpeed)
                }
                a!==false&&b.ui.ddmanager&&!d.dropBehaviour&&b.ui.ddmanager.prepareOffsets(f,c)
        }
    });
b.ui.plugin.add("draggable","snap",{
    start:function(){
        var c=b(this).data("draggable"),f=c.options;
        c.snapElements=[];
        b(f.snap.constructor!=String?f.snap.items||":data(draggable)":f.snap).each(function(){
            var d=b(this),
            a=d.offset();
            this!=c.element[0]&&c.snapElements.push({
                item:this,
                width:d.outerWidth(),
                height:d.outerHeight(),
                top:a.top,
                left:a.left
                })
            })
        },
    drag:function(c,f){
        for(var d=b(this).data("draggable"),a=d.options,e=a.snapTolerance,g=f.offset.left,i=g+d.helperProportions.width,k=f.offset.top,j=k+d.helperProportions.height,h=d.snapElements.length-1;h>=0;h--){
            var l=d.snapElements[h].left,m=l+d.snapElements[h].width,n=d.snapElements[h].top,o=n+d.snapElements[h].height;
            if(l-e<g&&g<m+e&&n-e<k&&k<o+e||l-e<g&&g<m+e&&
                n-e<j&&j<o+e||l-e<i&&i<m+e&&n-e<k&&k<o+e||l-e<i&&i<m+e&&n-e<j&&j<o+e){
                if(a.snapMode!="inner"){
                    var p=Math.abs(n-j)<=e,q=Math.abs(o-k)<=e,r=Math.abs(l-i)<=e,s=Math.abs(m-g)<=e;
                    if(p)f.position.top=d._convertPositionTo("relative",{
                        top:n-d.helperProportions.height,
                        left:0
                    }).top-d.margins.top;
                    if(q)f.position.top=d._convertPositionTo("relative",{
                        top:o,
                        left:0
                    }).top-d.margins.top;
                    if(r)f.position.left=d._convertPositionTo("relative",{
                        top:0,
                        left:l-d.helperProportions.width
                        }).left-d.margins.left;
                    if(s)f.position.left=
                        d._convertPositionTo("relative",{
                            top:0,
                            left:m
                        }).left-d.margins.left
                        }
                        var u=p||q||r||s;
                if(a.snapMode!="outer"){
                    p=Math.abs(n-k)<=e;
                    q=Math.abs(o-j)<=e;
                    r=Math.abs(l-g)<=e;
                    s=Math.abs(m-i)<=e;
                    if(p)f.position.top=d._convertPositionTo("relative",{
                        top:n,
                        left:0
                    }).top-d.margins.top;
                    if(q)f.position.top=d._convertPositionTo("relative",{
                        top:o-d.helperProportions.height,
                        left:0
                    }).top-d.margins.top;
                    if(r)f.position.left=d._convertPositionTo("relative",{
                        top:0,
                        left:l
                    }).left-d.margins.left;
                    if(s)f.position.left=d._convertPositionTo("relative",

                    {
                        top:0,
                        left:m-d.helperProportions.width
                        }).left-d.margins.left
                    }
                    if(!d.snapElements[h].snapping&&(p||q||r||s||u))d.options.snap.snap&&d.options.snap.snap.call(d.element,c,b.extend(d._uiHash(),{
                    snapItem:d.snapElements[h].item
                    }));
                d.snapElements[h].snapping=p||q||r||s||u
                }else{
                d.snapElements[h].snapping&&d.options.snap.release&&d.options.snap.release.call(d.element,c,b.extend(d._uiHash(),{
                    snapItem:d.snapElements[h].item
                    }));
                d.snapElements[h].snapping=false
                }
            }
        }
});
b.ui.plugin.add("draggable","stack",{
    start:function(){
        var c=
        b(this).data("draggable").options;
        c=b.makeArray(b(c.stack)).sort(function(d,a){
            return(parseInt(b(d).css("zIndex"),10)||0)-(parseInt(b(a).css("zIndex"),10)||0)
            });
        if(c.length){
            var f=parseInt(c[0].style.zIndex)||0;
            b(c).each(function(d){
                this.style.zIndex=f+d
                });
            this[0].style.zIndex=f+c.length
            }
        }
});
b.ui.plugin.add("draggable","zIndex",{
    start:function(c,f){
        var d=b(f.helper),a=b(this).data("draggable").options;
        if(d.css("zIndex"))a._zIndex=d.css("zIndex");
        d.css("zIndex",a.zIndex)
        },
    stop:function(c,f){
        var d=
        b(this).data("draggable").options;
        d._zIndex&&b(f.helper).css("zIndex",d._zIndex)
        }
    })
})(jQuery);
(function(b){
    b.widget("ui.droppable",{
        widgetEventPrefix:"drop",
        options:{
            accept:"*",
            activeClass:false,
            addClasses:true,
            greedy:false,
            hoverClass:false,
            scope:"default",
            tolerance:"intersect"
        },
        _create:function(){
            var c=this.options,f=c.accept;
            this.isover=0;
            this.isout=1;
            this.accept=b.isFunction(f)?f:function(d){
                return d.is(f)
                };
                
            this.proportions={
                width:this.element[0].offsetWidth,
                height:this.element[0].offsetHeight
                };
                
            b.ui.ddmanager.droppables[c.scope]=b.ui.ddmanager.droppables[c.scope]||[];
            b.ui.ddmanager.droppables[c.scope].push(this);
            c.addClasses&&this.element.addClass("ui-droppable")
            },
        destroy:function(){
            for(var c=b.ui.ddmanager.droppables[this.options.scope],f=0;f<c.length;f++)c[f]==this&&c.splice(f,1);
            this.element.removeClass("ui-droppable ui-droppable-disabled").removeData("droppable").unbind(".droppable");
            return this
            },
        _setOption:function(c,f){
            if(c=="accept")this.accept=b.isFunction(f)?f:function(d){
                return d.is(f)
                };
                
            b.Widget.prototype._setOption.apply(this,arguments)
            },
        _activate:function(c){
            var f=b.ui.ddmanager.current;
            this.options.activeClass&&
            this.element.addClass(this.options.activeClass);
            f&&this._trigger("activate",c,this.ui(f))
            },
        _deactivate:function(c){
            var f=b.ui.ddmanager.current;
            this.options.activeClass&&this.element.removeClass(this.options.activeClass);
            f&&this._trigger("deactivate",c,this.ui(f))
            },
        _over:function(c){
            var f=b.ui.ddmanager.current;
            if(!(!f||(f.currentItem||f.element)[0]==this.element[0]))if(this.accept.call(this.element[0],f.currentItem||f.element)){
                this.options.hoverClass&&this.element.addClass(this.options.hoverClass);
                this._trigger("over",c,this.ui(f))
                }
            },
    _out:function(c){
        var f=b.ui.ddmanager.current;
        if(!(!f||(f.currentItem||f.element)[0]==this.element[0]))if(this.accept.call(this.element[0],f.currentItem||f.element)){
            this.options.hoverClass&&this.element.removeClass(this.options.hoverClass);
            this._trigger("out",c,this.ui(f))
            }
        },
    _drop:function(c,f){
        var d=f||b.ui.ddmanager.current;
        if(!d||(d.currentItem||d.element)[0]==this.element[0])return false;
        var a=false;
        this.element.find(":data(droppable)").not(".ui-draggable-dragging").each(function(){
            var e=
            b.data(this,"droppable");
            if(e.options.greedy&&!e.options.disabled&&e.options.scope==d.options.scope&&e.accept.call(e.element[0],d.currentItem||d.element)&&b.ui.intersect(d,b.extend(e,{
                offset:e.element.offset()
                }),e.options.tolerance)){
                a=true;
                return false
                }
            });
    if(a)return false;
    if(this.accept.call(this.element[0],d.currentItem||d.element)){
        this.options.activeClass&&this.element.removeClass(this.options.activeClass);
        this.options.hoverClass&&this.element.removeClass(this.options.hoverClass);
        this._trigger("drop",
            c,this.ui(d));
        return this.element
        }
        return false
    },
ui:function(c){
    return{
        draggable:c.currentItem||c.element,
        helper:c.helper,
        position:c.position,
        offset:c.positionAbs
        }
    }
});
b.extend(b.ui.droppable,{
    version:"1.8"
});
b.ui.intersect=function(c,f,d){
    if(!f.offset)return false;
    var a=(c.positionAbs||c.position.absolute).left,e=a+c.helperProportions.width,g=(c.positionAbs||c.position.absolute).top,i=g+c.helperProportions.height,k=f.offset.left,j=k+f.proportions.width,h=f.offset.top,l=h+f.proportions.height;
    switch(d){
        case "fit":
            return k<
            a&&e<j&&h<g&&i<l;
        case "intersect":
            return k<a+c.helperProportions.width/2&&e-c.helperProportions.width/2<j&&h<g+c.helperProportions.height/2&&i-c.helperProportions.height/2<l;
        case "pointer":
            return b.ui.isOver((c.positionAbs||c.position.absolute).top+(c.clickOffset||c.offset.click).top,(c.positionAbs||c.position.absolute).left+(c.clickOffset||c.offset.click).left,h,k,f.proportions.height,f.proportions.width);
        case "touch":
            return(g>=h&&g<=l||i>=h&&i<=l||g<h&&i>l)&&(a>=k&&a<=j||e>=k&&e<=j||a<k&&e>j);
        default:
            return false
            }
        };
b.ui.ddmanager={
    current:null,
    droppables:{
        "default":[]
    },
    prepareOffsets:function(c,f){
        var d=b.ui.ddmanager.droppables[c.options.scope]||[],a=f?f.type:null,e=(c.currentItem||c.element).find(":data(droppable)").andSelf(),g=0;
            a:for(;g<d.length;g++)if(!(d[g].options.disabled||c&&!d[g].accept.call(d[g].element[0],c.currentItem||c.element))){
            for(var i=0;i<e.length;i++)if(e[i]==d[g].element[0]){
                d[g].proportions.height=0;
                continue a
            }
            d[g].visible=d[g].element.css("display")!="none";
            if(d[g].visible){
                d[g].offset=
                d[g].element.offset();
                d[g].proportions={
                    width:d[g].element[0].offsetWidth,
                    height:d[g].element[0].offsetHeight
                    };
                    
                a=="mousedown"&&d[g]._activate.call(d[g],f)
                }
            }
        },
drop:function(c,f){
    var d=false;
    b.each(b.ui.ddmanager.droppables[c.options.scope]||[],function(){
        if(this.options){
            if(!this.options.disabled&&this.visible&&b.ui.intersect(c,this,this.options.tolerance))d=d||this._drop.call(this,f);
            if(!this.options.disabled&&this.visible&&this.accept.call(this.element[0],c.currentItem||c.element)){
                this.isout=1;
                this.isover=0;
                this._deactivate.call(this,f)
                }
            }
    });
return d
},
drag:function(c,f){
    c.options.refreshPositions&&b.ui.ddmanager.prepareOffsets(c,f);
    b.each(b.ui.ddmanager.droppables[c.options.scope]||[],function(){
        if(!(this.options.disabled||this.greedyChild||!this.visible)){
            var d=b.ui.intersect(c,this,this.options.tolerance);
            if(d=!d&&this.isover==1?"isout":d&&this.isover==0?"isover":null){
                var a;
                if(this.options.greedy){
                    var e=this.element.parents(":data(droppable):eq(0)");
                    if(e.length){
                        a=b.data(e[0],"droppable");
                        a.greedyChild=d=="isover"?1:0
                        }
                    }
                if(a&&d=="isover"){
                a.isover=0;
                a.isout=1;
                a._out.call(a,f)
                }
                this[d]=1;
            this[d=="isout"?"isover":"isout"]=0;
            this[d=="isover"?"_over":"_out"].call(this,f);
            if(a&&d=="isout"){
                a.isout=0;
                a.isover=1;
                a._over.call(a,f)
                }
            }
    }
})
}
}
})(jQuery);
(function(b){
    b.widget("ui.resizable",b.ui.mouse,{
        widgetEventPrefix:"resize",
        options:{
            alsoResize:false,
            animate:false,
            animateDuration:"slow",
            animateEasing:"swing",
            aspectRatio:false,
            autoHide:false,
            containment:false,
            ghost:false,
            grid:false,
            handles:"e,s,se",
            helper:false,
            maxHeight:null,
            maxWidth:null,
            minHeight:10,
            minWidth:10,
            zIndex:1E3
        },
        _create:function(){
            var d=this,a=this.options;
            this.element.addClass("ui-resizable");
            b.extend(this,{
                _aspectRatio:!!a.aspectRatio,
                aspectRatio:a.aspectRatio,
                originalElement:this.element,
                _proportionallyResizeElements:[],
                _helper:a.helper||a.ghost||a.animate?a.helper||"ui-resizable-helper":null
                });
            if(this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i)){
                /relative/.test(this.element.css("position"))&&b.browser.opera&&this.element.css({
                    position:"relative",
                    top:"auto",
                    left:"auto"
                });
                this.element.wrap(b('<div class="ui-wrapper" style="overflow: hidden;"></div>').css({
                    position:this.element.css("position"),
                    width:this.element.outerWidth(),
                    height:this.element.outerHeight(),
                    top:this.element.css("top"),
                    left:this.element.css("left")
                    }));
                this.element=this.element.parent().data("resizable",this.element.data("resizable"));
                this.elementIsWrapper=true;
                this.element.css({
                    marginLeft:this.originalElement.css("marginLeft"),
                    marginTop:this.originalElement.css("marginTop"),
                    marginRight:this.originalElement.css("marginRight"),
                    marginBottom:this.originalElement.css("marginBottom")
                    });
                this.originalElement.css({
                    marginLeft:0,
                    marginTop:0,
                    marginRight:0,
                    marginBottom:0
                });
                this.originalResizeStyle=
                this.originalElement.css("resize");
                this.originalElement.css("resize","none");
                this._proportionallyResizeElements.push(this.originalElement.css({
                    position:"static",
                    zoom:1,
                    display:"block"
                }));
                this.originalElement.css({
                    margin:this.originalElement.css("margin")
                    });
                this._proportionallyResize()
                }
                this.handles=a.handles||(!b(".ui-resizable-handle",this.element).length?"e,s,se":{
                n:".ui-resizable-n",
                e:".ui-resizable-e",
                s:".ui-resizable-s",
                w:".ui-resizable-w",
                se:".ui-resizable-se",
                sw:".ui-resizable-sw",
                ne:".ui-resizable-ne",
                nw:".ui-resizable-nw"
            });
            if(this.handles.constructor==String){
                if(this.handles=="all")this.handles="n,e,s,w,se,sw,ne,nw";
                var e=this.handles.split(",");
                this.handles={};
                
                for(var g=0;g<e.length;g++){
                    var i=b.trim(e[g]),k=b('<div class="ui-resizable-handle '+("ui-resizable-"+i)+'"></div>');
                    /sw|se|ne|nw/.test(i)&&k.css({
                        zIndex:++a.zIndex
                        });
                    "se"==i&&k.addClass("ui-icon ui-icon-gripsmall-diagonal-se");
                    this.handles[i]=".ui-resizable-"+i;
                    this.element.append(k)
                    }
                }
                this._renderAxis=function(j){
            j=j||this.element;
            for(var h in this.handles){
                if(this.handles[h].constructor==
                    String)this.handles[h]=b(this.handles[h],this.element).show();
                if(this.elementIsWrapper&&this.originalElement[0].nodeName.match(/textarea|input|select|button/i)){
                    var l=b(this.handles[h],this.element),m=0;
                    m=/sw|ne|nw|se|n|s/.test(h)?l.outerHeight():l.outerWidth();
                    l=["padding",/ne|nw|n/.test(h)?"Top":/se|sw|s/.test(h)?"Bottom":/^e$/.test(h)?"Right":"Left"].join("");
                    j.css(l,m);
                    this._proportionallyResize()
                    }
                    b(this.handles[h])
                }
            };
            
    this._renderAxis(this.element);
        this._handles=b(".ui-resizable-handle",this.element).disableSelection();
        this._handles.mouseover(function(){
            if(!d.resizing){
                if(this.className)var j=this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
                d.axis=j&&j[1]?j[1]:"se"
                }
            });
    if(a.autoHide){
        this._handles.hide();
        b(this.element).addClass("ui-resizable-autohide").hover(function(){
            b(this).removeClass("ui-resizable-autohide");
            d._handles.show()
            },function(){
            if(!d.resizing){
                b(this).addClass("ui-resizable-autohide");
                d._handles.hide()
                }
            })
    }
    this._mouseInit()
    },
destroy:function(){
    this._mouseDestroy();
    var d=function(e){
        b(e).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").unbind(".resizable").find(".ui-resizable-handle").remove()
        };
    if(this.elementIsWrapper){
        d(this.element);
        var a=this.element;
        a.after(this.originalElement.css({
            position:a.css("position"),
            width:a.outerWidth(),
            height:a.outerHeight(),
            top:a.css("top"),
            left:a.css("left")
            })).remove()
        }
        this.originalElement.css("resize",this.originalResizeStyle);
    d(this.originalElement);
    return this
    },
_mouseCapture:function(d){
    var a=false,e;
    for(e in this.handles)if(b(this.handles[e])[0]==d.target)a=true;return!this.options.disabled&&a
    },
_mouseStart:function(d){
    var a=this.options,e=this.element.position(),
    g=this.element;
    this.resizing=true;
    this.documentScroll={
        top:b(document).scrollTop(),
        left:b(document).scrollLeft()
        };
        
    if(g.is(".ui-draggable")||/absolute/.test(g.css("position")))g.css({
        position:"absolute",
        top:e.top,
        left:e.left
        });
    b.browser.opera&&/relative/.test(g.css("position"))&&g.css({
        position:"relative",
        top:"auto",
        left:"auto"
    });
    this._renderProxy();
    e=c(this.helper.css("left"));
    var i=c(this.helper.css("top"));
    if(a.containment){
        e+=b(a.containment).scrollLeft()||0;
        i+=b(a.containment).scrollTop()||0
        }
        this.offset=
    this.helper.offset();
    this.position={
        left:e,
        top:i
    };
    
    this.size=this._helper?{
        width:g.outerWidth(),
        height:g.outerHeight()
        }:{
        width:g.width(),
        height:g.height()
        };
        
    this.originalSize=this._helper?{
        width:g.outerWidth(),
        height:g.outerHeight()
        }:{
        width:g.width(),
        height:g.height()
        };
        
    this.originalPosition={
        left:e,
        top:i
    };
    
    this.sizeDiff={
        width:g.outerWidth()-g.width(),
        height:g.outerHeight()-g.height()
        };
        
    this.originalMousePosition={
        left:d.pageX,
        top:d.pageY
        };
        
    this.aspectRatio=typeof a.aspectRatio=="number"?a.aspectRatio:
    this.originalSize.width/this.originalSize.height||1;
    a=b(".ui-resizable-"+this.axis).css("cursor");
    b("body").css("cursor",a=="auto"?this.axis+"-resize":a);
    g.addClass("ui-resizable-resizing");
    this._propagate("start",d);
    return true
    },
_mouseDrag:function(d){
    var a=this.helper,e=this.originalMousePosition,g=this._change[this.axis];
    if(!g)return false;
    e=g.apply(this,[d,d.pageX-e.left||0,d.pageY-e.top||0]);
    if(this._aspectRatio||d.shiftKey)e=this._updateRatio(e,d);
    e=this._respectSize(e,d);
    this._propagate("resize",
        d);
    a.css({
        top:this.position.top+"px",
        left:this.position.left+"px",
        width:this.size.width+"px",
        height:this.size.height+"px"
        });
    !this._helper&&this._proportionallyResizeElements.length&&this._proportionallyResize();
    this._updateCache(e);
    this._trigger("resize",d,this.ui());
    return false
    },
_mouseStop:function(d){
    this.resizing=false;
    var a=this.options;
    if(this._helper){
        var e=this._proportionallyResizeElements,g=e.length&&/textarea/i.test(e[0].nodeName);
        e=g&&b.ui.hasScroll(e[0],"left")?0:this.sizeDiff.height;
        g={
            width:this.size.width-(g?0:this.sizeDiff.width),
            height:this.size.height-e
            };
            
        e=parseInt(this.element.css("left"),10)+(this.position.left-this.originalPosition.left)||null;
        var i=parseInt(this.element.css("top"),10)+(this.position.top-this.originalPosition.top)||null;
        a.animate||this.element.css(b.extend(g,{
            top:i,
            left:e
        }));
        this.helper.height(this.size.height);
        this.helper.width(this.size.width);
        this._helper&&!a.animate&&this._proportionallyResize()
        }
        b("body").css("cursor","auto");
    this.element.removeClass("ui-resizable-resizing");
    this._propagate("stop",d);
    this._helper&&this.helper.remove();
    return false
    },
_updateCache:function(d){
    this.offset=this.helper.offset();
    if(f(d.left))this.position.left=d.left;
    if(f(d.top))this.position.top=d.top;
    if(f(d.height))this.size.height=d.height;
    if(f(d.width))this.size.width=d.width
        },
_updateRatio:function(d){
    var a=this.position,e=this.size,g=this.axis;
    if(d.height)d.width=e.height*this.aspectRatio;
    else if(d.width)d.height=e.width/this.aspectRatio;
    if(g=="sw"){
        d.left=a.left+(e.width-d.width);
        d.top=
        null
        }
        if(g=="nw"){
        d.top=a.top+(e.height-d.height);
        d.left=a.left+(e.width-d.width)
        }
        return d
    },
_respectSize:function(d){
    var a=this.options,e=this.axis,g=f(d.width)&&a.maxWidth&&a.maxWidth<d.width,i=f(d.height)&&a.maxHeight&&a.maxHeight<d.height,k=f(d.width)&&a.minWidth&&a.minWidth>d.width,j=f(d.height)&&a.minHeight&&a.minHeight>d.height;
    if(k)d.width=a.minWidth;
    if(j)d.height=a.minHeight;
    if(g)d.width=a.maxWidth;
    if(i)d.height=a.maxHeight;
    var h=this.originalPosition.left+this.originalSize.width,l=this.position.top+
    this.size.height,m=/sw|nw|w/.test(e);
    e=/nw|ne|n/.test(e);
    if(k&&m)d.left=h-a.minWidth;
    if(g&&m)d.left=h-a.maxWidth;
    if(j&&e)d.top=l-a.minHeight;
    if(i&&e)d.top=l-a.maxHeight;
    if((a=!d.width&&!d.height)&&!d.left&&d.top)d.top=null;
    else if(a&&!d.top&&d.left)d.left=null;
    return d
    },
_proportionallyResize:function(){
    if(this._proportionallyResizeElements.length)for(var d=this.helper||this.element,a=0;a<this._proportionallyResizeElements.length;a++){
        var e=this._proportionallyResizeElements[a];
        if(!this.borderDif){
            var g=
            [e.css("borderTopWidth"),e.css("borderRightWidth"),e.css("borderBottomWidth"),e.css("borderLeftWidth")],i=[e.css("paddingTop"),e.css("paddingRight"),e.css("paddingBottom"),e.css("paddingLeft")];
            this.borderDif=b.map(g,function(k,j){
                var h=parseInt(k,10)||0,l=parseInt(i[j],10)||0;
                return h+l
                })
            }
            b.browser.msie&&(b(d).is(":hidden")||b(d).parents(":hidden").length)||e.css({
            height:d.height()-this.borderDif[0]-this.borderDif[2]||0,
            width:d.width()-this.borderDif[1]-this.borderDif[3]||0
            })
        }
    },
_renderProxy:function(){
    var d=
    this.options;
    this.elementOffset=this.element.offset();
    if(this._helper){
        this.helper=this.helper||b('<div style="overflow:hidden;"></div>');
        var a=b.browser.msie&&b.browser.version<7,e=a?1:0;
        a=a?2:-1;
        this.helper.addClass(this._helper).css({
            width:this.element.outerWidth()+a,
            height:this.element.outerHeight()+a,
            position:"absolute",
            left:this.elementOffset.left-e+"px",
            top:this.elementOffset.top-e+"px",
            zIndex:++d.zIndex
            });
        this.helper.appendTo("body").disableSelection()
        }else this.helper=this.element
        },
_change:{
    e:function(d,
        a){
        return{
            width:this.originalSize.width+a
            }
        },
w:function(d,a){
    return{
        left:this.originalPosition.left+a,
        width:this.originalSize.width-a
        }
    },
n:function(d,a,e){
    return{
        top:this.originalPosition.top+e,
        height:this.originalSize.height-e
        }
    },
s:function(d,a,e){
    return{
        height:this.originalSize.height+e
        }
    },
se:function(d,a,e){
    return b.extend(this._change.s.apply(this,arguments),this._change.e.apply(this,[d,a,e]))
    },
sw:function(d,a,e){
    return b.extend(this._change.s.apply(this,arguments),this._change.w.apply(this,[d,a,
        e]))
    },
ne:function(d,a,e){
    return b.extend(this._change.n.apply(this,arguments),this._change.e.apply(this,[d,a,e]))
    },
nw:function(d,a,e){
    return b.extend(this._change.n.apply(this,arguments),this._change.w.apply(this,[d,a,e]))
    }
},
_propagate:function(d,a){
    b.ui.plugin.call(this,d,[a,this.ui()]);
    d!="resize"&&this._trigger(d,a,this.ui())
    },
plugins:{},
ui:function(){
    return{
        originalElement:this.originalElement,
        element:this.element,
        helper:this.helper,
        position:this.position,
        size:this.size,
        originalSize:this.originalSize,
        originalPosition:this.originalPosition
        }
    }
});
b.extend(b.ui.resizable,{
    version:"1.8"
});
b.ui.plugin.add("resizable","alsoResize",{
    start:function(){
        var d=b(this).data("resizable").options,a=function(e){
            b(e).each(function(){
                b(this).data("resizable-alsoresize",{
                    width:parseInt(b(this).width(),10),
                    height:parseInt(b(this).height(),10),
                    left:parseInt(b(this).css("left"),10),
                    top:parseInt(b(this).css("top"),10)
                    })
                })
            };
            
        if(typeof d.alsoResize=="object"&&!d.alsoResize.parentNode)if(d.alsoResize.length){
            d.alsoResize=
            d.alsoResize[0];
            a(d.alsoResize)
            }else b.each(d.alsoResize,function(e){
            a(e)
            });else a(d.alsoResize)
            },
    resize:function(){
        var d=b(this).data("resizable"),a=d.options,e=d.originalSize,g=d.originalPosition,i={
            height:d.size.height-e.height||0,
            width:d.size.width-e.width||0,
            top:d.position.top-g.top||0,
            left:d.position.left-g.left||0
            },k=function(j,h){
            b(j).each(function(){
                var l=b(this),m=b(this).data("resizable-alsoresize"),n={};
                
                b.each((h&&h.length?h:["width","height","top","left"])||["width","height","top","left"],
                    function(o,p){
                        var q=(m[p]||0)+(i[p]||0);
                        if(q&&q>=0)n[p]=q||null
                            });
                if(/relative/.test(l.css("position"))&&b.browser.opera){
                    d._revertToRelativePosition=true;
                    l.css({
                        position:"absolute",
                        top:"auto",
                        left:"auto"
                    })
                    }
                    l.css(n)
                })
            };
            
        typeof a.alsoResize=="object"&&!a.alsoResize.nodeType?b.each(a.alsoResize,function(j,h){
            k(j,h)
            }):k(a.alsoResize)
        },
    stop:function(){
        var d=b(this).data("resizable");
        if(d._revertToRelativePosition&&b.browser.opera){
            d._revertToRelativePosition=false;
            el.css({
                position:"relative"
            })
            }
            b(this).removeData("resizable-alsoresize-start")
        }
    });
b.ui.plugin.add("resizable","animate",{
    stop:function(d){
        var a=b(this).data("resizable"),e=a.options,g=a._proportionallyResizeElements,i=g.length&&/textarea/i.test(g[0].nodeName),k=i&&b.ui.hasScroll(g[0],"left")?0:a.sizeDiff.height;
        i={
            width:a.size.width-(i?0:a.sizeDiff.width),
            height:a.size.height-k
            };
            
        k=parseInt(a.element.css("left"),10)+(a.position.left-a.originalPosition.left)||null;
        var j=parseInt(a.element.css("top"),10)+(a.position.top-a.originalPosition.top)||null;
        a.element.animate(b.extend(i,j&&
            k?{
                top:j,
                left:k
            }:{}),{
            duration:e.animateDuration,
            easing:e.animateEasing,
            step:function(){
                var h={
                    width:parseInt(a.element.css("width"),10),
                    height:parseInt(a.element.css("height"),10),
                    top:parseInt(a.element.css("top"),10),
                    left:parseInt(a.element.css("left"),10)
                    };
                    
                g&&g.length&&b(g[0]).css({
                    width:h.width,
                    height:h.height
                    });
                a._updateCache(h);
                a._propagate("resize",d)
                }
            })
    }
});
b.ui.plugin.add("resizable","containment",{
    start:function(){
        var d=b(this).data("resizable"),a=d.element,e=d.options.containment;
        if(a=e instanceof
            b?e.get(0):/parent/.test(e)?a.parent().get(0):e){
            d.containerElement=b(a);
            if(/document/.test(e)||e==document){
                d.containerOffset={
                    left:0,
                    top:0
                };
                
                d.containerPosition={
                    left:0,
                    top:0
                };
                
                d.parentData={
                    element:b(document),
                    left:0,
                    top:0,
                    width:b(document).width(),
                    height:b(document).height()||document.body.parentNode.scrollHeight
                    }
                }else{
            var g=b(a),i=[];
            b(["Top","Right","Left","Bottom"]).each(function(h,l){
                i[h]=c(g.css("padding"+l))
                });
            d.containerOffset=g.offset();
            d.containerPosition=g.position();
            d.containerSize={
                height:g.innerHeight()-
                i[3],
                width:g.innerWidth()-i[1]
                };
                
            e=d.containerOffset;
            var k=d.containerSize.height,j=d.containerSize.width;
            j=b.ui.hasScroll(a,"left")?a.scrollWidth:j;
            k=b.ui.hasScroll(a)?a.scrollHeight:k;
            d.parentData={
                element:a,
                left:e.left,
                top:e.top,
                width:j,
                height:k
            }
        }
    }
},
resize:function(d){
    var a=b(this).data("resizable"),e=a.options,g=a.containerOffset,i=a.position;
    d=a._aspectRatio||d.shiftKey;
    var k={
        top:0,
        left:0
    },j=a.containerElement;
    if(j[0]!=document&&/static/.test(j.css("position")))k=g;
    if(i.left<(a._helper?g.left:
        0)){
        a.size.width+=a._helper?a.position.left-g.left:a.position.left-k.left;
        if(d)a.size.height=a.size.width/e.aspectRatio;
        a.position.left=e.helper?g.left:0
        }
        if(i.top<(a._helper?g.top:0)){
        a.size.height+=a._helper?a.position.top-g.top:a.position.top;
        if(d)a.size.width=a.size.height*e.aspectRatio;
        a.position.top=a._helper?g.top:0
        }
        a.offset.left=a.parentData.left+a.position.left;
    a.offset.top=a.parentData.top+a.position.top;
    e=Math.abs((a._helper?a.offset.left-k.left:a.offset.left-k.left)+a.sizeDiff.width);
    g=
    Math.abs((a._helper?a.offset.top-k.top:a.offset.top-g.top)+a.sizeDiff.height);
    i=a.containerElement.get(0)==a.element.parent().get(0);
    k=/relative|absolute/.test(a.containerElement.css("position"));
    if(i&&k)e-=a.parentData.left;
    if(e+a.size.width>=a.parentData.width){
        a.size.width=a.parentData.width-e;
        if(d)a.size.height=a.size.width/a.aspectRatio
            }
            if(g+a.size.height>=a.parentData.height){
        a.size.height=a.parentData.height-g;
        if(d)a.size.width=a.size.height*a.aspectRatio
            }
        },
stop:function(){
    var d=b(this).data("resizable"),
    a=d.options,e=d.containerOffset,g=d.containerPosition,i=d.containerElement,k=b(d.helper),j=k.offset(),h=k.outerWidth()-d.sizeDiff.width;
    k=k.outerHeight()-d.sizeDiff.height;
    d._helper&&!a.animate&&/relative/.test(i.css("position"))&&b(this).css({
        left:j.left-g.left-e.left,
        width:h,
        height:k
    });
    d._helper&&!a.animate&&/static/.test(i.css("position"))&&b(this).css({
        left:j.left-g.left-e.left,
        width:h,
        height:k
    })
    }
});
b.ui.plugin.add("resizable","ghost",{
    start:function(){
        var d=b(this).data("resizable"),a=d.options,
        e=d.size;
        d.ghost=d.originalElement.clone();
        d.ghost.css({
            opacity:0.25,
            display:"block",
            position:"relative",
            height:e.height,
            width:e.width,
            margin:0,
            left:0,
            top:0
        }).addClass("ui-resizable-ghost").addClass(typeof a.ghost=="string"?a.ghost:"");
        d.ghost.appendTo(d.helper)
        },
    resize:function(){
        var d=b(this).data("resizable");
        d.ghost&&d.ghost.css({
            position:"relative",
            height:d.size.height,
            width:d.size.width
            })
        },
    stop:function(){
        var d=b(this).data("resizable");
        d.ghost&&d.helper&&d.helper.get(0).removeChild(d.ghost.get(0))
        }
    });
b.ui.plugin.add("resizable","grid",{
    resize:function(){
        var d=b(this).data("resizable"),a=d.options,e=d.size,g=d.originalSize,i=d.originalPosition,k=d.axis;
        a.grid=typeof a.grid=="number"?[a.grid,a.grid]:a.grid;
        var j=Math.round((e.width-g.width)/(a.grid[0]||1))*(a.grid[0]||1);
        a=Math.round((e.height-g.height)/(a.grid[1]||1))*(a.grid[1]||1);
        if(/^(se|s|e)$/.test(k)){
            d.size.width=g.width+j;
            d.size.height=g.height+a
            }else if(/^(ne)$/.test(k)){
            d.size.width=g.width+j;
            d.size.height=g.height+a;
            d.position.top=i.top-
            a
            }else{
            if(/^(sw)$/.test(k)){
                d.size.width=g.width+j;
                d.size.height=g.height+a
                }else{
                d.size.width=g.width+j;
                d.size.height=g.height+a;
                d.position.top=i.top-a
                }
                d.position.left=i.left-j
            }
        }
});
var c=function(d){
    return parseInt(d,10)||0
    },f=function(d){
    return!isNaN(parseInt(d,10))
    }
})(jQuery);
(function(b){
    b.widget("ui.selectable",b.ui.mouse,{
        options:{
            appendTo:"body",
            autoRefresh:true,
            distance:0,
            filter:"*",
            tolerance:"touch"
        },
        _create:function(){
            var c=this;
            this.element.addClass("ui-selectable");
            this.dragged=false;
            var f;
            this.refresh=function(){
                f=b(c.options.filter,c.element[0]);
                f.each(function(){
                    var d=b(this),a=d.offset();
                    b.data(this,"selectable-item",{
                        element:this,
                        $element:d,
                        left:a.left,
                        top:a.top,
                        right:a.left+d.outerWidth(),
                        bottom:a.top+d.outerHeight(),
                        startselected:false,
                        selected:d.hasClass("ui-selected"),
                        selecting:d.hasClass("ui-selecting"),
                        unselecting:d.hasClass("ui-unselecting")
                        })
                    })
                };
                
            this.refresh();
            this.selectees=f.addClass("ui-selectee");
            this._mouseInit();
            this.helper=b(document.createElement("div")).css({
                border:"1px dotted black"
            }).addClass("ui-selectable-helper")
            },
        destroy:function(){
            this.selectees.removeClass("ui-selectee").removeData("selectable-item");
            this.element.removeClass("ui-selectable ui-selectable-disabled").removeData("selectable").unbind(".selectable");
            this._mouseDestroy();
            return this
            },
        _mouseStart:function(c){
            var f=this;
            this.opos=[c.pageX,c.pageY];
            if(!this.options.disabled){
                var d=this.options;
                this.selectees=b(d.filter,this.element[0]);
                this._trigger("start",c);
                b(d.appendTo).append(this.helper);
                this.helper.css({
                    "z-index":100,
                    position:"absolute",
                    left:c.clientX,
                    top:c.clientY,
                    width:0,
                    height:0
                });
                d.autoRefresh&&this.refresh();
                this.selectees.filter(".ui-selected").each(function(){
                    var a=b.data(this,"selectable-item");
                    a.startselected=true;
                    if(!c.metaKey){
                        a.$element.removeClass("ui-selected");
                        a.selected=false;
                        a.$element.addClass("ui-unselecting");
                        a.unselecting=true;
                        f._trigger("unselecting",c,{
                            unselecting:a.element
                            })
                        }
                    });
            b(c.target).parents().andSelf().each(function(){
                var a=b.data(this,"selectable-item");
                if(a){
                    a.$element.removeClass("ui-unselecting").addClass("ui-selecting");
                    a.unselecting=false;
                    a.selecting=true;
                    a.selected=true;
                    f._trigger("selecting",c,{
                        selecting:a.element
                        });
                    return false
                    }
                })
        }
    },
_mouseDrag:function(c){
    var f=this;
    this.dragged=true;
    if(!this.options.disabled){
        var d=this.options,
        a=this.opos[0],e=this.opos[1],g=c.pageX,i=c.pageY;
        if(a>g){
            var k=g;
            g=a;
            a=k
            }
            if(e>i){
            k=i;
            i=e;
            e=k
            }
            this.helper.css({
            left:a,
            top:e,
            width:g-a,
            height:i-e
            });
        this.selectees.each(function(){
            var j=b.data(this,"selectable-item");
            if(!(!j||j.element==f.element[0])){
                var h=false;
                if(d.tolerance=="touch")h=!(j.left>g||j.right<a||j.top>i||j.bottom<e);
                else if(d.tolerance=="fit")h=j.left>a&&j.right<g&&j.top>e&&j.bottom<i;
                if(h){
                    if(j.selected){
                        j.$element.removeClass("ui-selected");
                        j.selected=false
                        }
                        if(j.unselecting){
                        j.$element.removeClass("ui-unselecting");
                        j.unselecting=false
                        }
                        if(!j.selecting){
                        j.$element.addClass("ui-selecting");
                        j.selecting=true;
                        f._trigger("selecting",c,{
                            selecting:j.element
                            })
                        }
                    }else{
                if(j.selecting)if(c.metaKey&&j.startselected){
                    j.$element.removeClass("ui-selecting");
                    j.selecting=false;
                    j.$element.addClass("ui-selected");
                    j.selected=true
                    }else{
                    j.$element.removeClass("ui-selecting");
                    j.selecting=false;
                    if(j.startselected){
                        j.$element.addClass("ui-unselecting");
                        j.unselecting=true
                        }
                        f._trigger("unselecting",c,{
                        unselecting:j.element
                        })
                    }
                    if(j.selected)if(!c.metaKey&&
                    !j.startselected){
                    j.$element.removeClass("ui-selected");
                    j.selected=false;
                    j.$element.addClass("ui-unselecting");
                    j.unselecting=true;
                    f._trigger("unselecting",c,{
                        unselecting:j.element
                        })
                    }
                }
            }
    });
return false
}
},
_mouseStop:function(c){
    var f=this;
    this.dragged=false;
    b(".ui-unselecting",this.element[0]).each(function(){
        var d=b.data(this,"selectable-item");
        d.$element.removeClass("ui-unselecting");
        d.unselecting=false;
        d.startselected=false;
        f._trigger("unselected",c,{
            unselected:d.element
            })
        });
    b(".ui-selecting",this.element[0]).each(function(){
        var d=
        b.data(this,"selectable-item");
        d.$element.removeClass("ui-selecting").addClass("ui-selected");
        d.selecting=false;
        d.selected=true;
        d.startselected=true;
        f._trigger("selected",c,{
            selected:d.element
            })
        });
    this._trigger("stop",c);
    this.helper.remove();
    return false
    }
});
b.extend(b.ui.selectable,{
    version:"1.8"
})
})(jQuery);
(function(b){
    b.widget("ui.sortable",b.ui.mouse,{
        widgetEventPrefix:"sort",
        options:{
            appendTo:"parent",
            axis:false,
            connectWith:false,
            containment:false,
            cursor:"auto",
            cursorAt:false,
            dropOnEmpty:true,
            forcePlaceholderSize:false,
            forceHelperSize:false,
            grid:false,
            handle:false,
            helper:"original",
            items:"> *",
            opacity:false,
            placeholder:false,
            revert:false,
            scroll:true,
            scrollSensitivity:20,
            scrollSpeed:20,
            scope:"default",
            tolerance:"intersect",
            zIndex:1E3
        },
        _create:function(){
            this.containerCache={};
            
            this.element.addClass("ui-sortable");
            this.refresh();
            this.floating=this.items.length?/left|right/.test(this.items[0].item.css("float")):false;
            this.offset=this.element.offset();
            this._mouseInit()
            },
        destroy:function(){
            this.element.removeClass("ui-sortable ui-sortable-disabled").removeData("sortable").unbind(".sortable");
            this._mouseDestroy();
            for(var c=this.items.length-1;c>=0;c--)this.items[c].item.removeData("sortable-item");
            return this
            },
        _mouseCapture:function(c,f){
            if(this.reverting)return false;
            if(this.options.disabled||this.options.type==
                "static")return false;
            this._refreshItems(c);
            var d=null,a=this;
            b(c.target).parents().each(function(){
                if(b.data(this,"sortable-item")==a){
                    d=b(this);
                    return false
                    }
                });
        if(b.data(c.target,"sortable-item")==a)d=b(c.target);
        if(!d)return false;
        if(this.options.handle&&!f){
            var e=false;
            b(this.options.handle,d).find("*").andSelf().each(function(){
                if(this==c.target)e=true
                    });
            if(!e)return false
                }
                this.currentItem=d;
        this._removeCurrentsFromItems();
        return true
        },
    _mouseStart:function(c,f,d){
        f=this.options;
        this.currentContainer=
        this;
        this.refreshPositions();
        this.helper=this._createHelper(c);
        this._cacheHelperProportions();
        this._cacheMargins();
        this.scrollParent=this.helper.scrollParent();
        this.offset=this.currentItem.offset();
        this.offset={
            top:this.offset.top-this.margins.top,
            left:this.offset.left-this.margins.left
            };
            
        this.helper.css("position","absolute");
        this.cssPosition=this.helper.css("position");
        b.extend(this.offset,{
            click:{
                left:c.pageX-this.offset.left,
                top:c.pageY-this.offset.top
                },
            parent:this._getParentOffset(),
            relative:this._getRelativeOffset()
            });
        this.originalPosition=this._generatePosition(c);
        this.originalPageX=c.pageX;
        this.originalPageY=c.pageY;
        f.cursorAt&&this._adjustOffsetFromHelper(f.cursorAt);
        this.domPosition={
            prev:this.currentItem.prev()[0],
            parent:this.currentItem.parent()[0]
            };
            
        this.helper[0]!=this.currentItem[0]&&this.currentItem.hide();
        this._createPlaceholder();
        f.containment&&this._setContainment();
        if(f.cursor){
            if(b("body").css("cursor"))this._storedCursor=b("body").css("cursor");
            b("body").css("cursor",f.cursor)
            }
            if(f.opacity){
            if(this.helper.css("opacity"))this._storedOpacity=
                this.helper.css("opacity");
            this.helper.css("opacity",f.opacity)
            }
            if(f.zIndex){
            if(this.helper.css("zIndex"))this._storedZIndex=this.helper.css("zIndex");
            this.helper.css("zIndex",f.zIndex)
            }
            if(this.scrollParent[0]!=document&&this.scrollParent[0].tagName!="HTML")this.overflowOffset=this.scrollParent.offset();
        this._trigger("start",c,this._uiHash());
        this._preserveHelperProportions||this._cacheHelperProportions();
        if(!d)for(d=this.containers.length-1;d>=0;d--)this.containers[d]._trigger("activate",c,this._uiHash(this));
        if(b.ui.ddmanager)b.ui.ddmanager.current=this;
        b.ui.ddmanager&&!f.dropBehaviour&&b.ui.ddmanager.prepareOffsets(this,c);
        this.dragging=true;
        this.helper.addClass("ui-sortable-helper");
        this._mouseDrag(c);
        return true
        },
    _mouseDrag:function(c){
        this.position=this._generatePosition(c);
        this.positionAbs=this._convertPositionTo("absolute");
        if(!this.lastPositionAbs)this.lastPositionAbs=this.positionAbs;
        if(this.options.scroll){
            var f=this.options,d=false;
            if(this.scrollParent[0]!=document&&this.scrollParent[0].tagName!=
                "HTML"){
                if(this.overflowOffset.top+this.scrollParent[0].offsetHeight-c.pageY<f.scrollSensitivity)this.scrollParent[0].scrollTop=d=this.scrollParent[0].scrollTop+f.scrollSpeed;
                else if(c.pageY-this.overflowOffset.top<f.scrollSensitivity)this.scrollParent[0].scrollTop=d=this.scrollParent[0].scrollTop-f.scrollSpeed;
                if(this.overflowOffset.left+this.scrollParent[0].offsetWidth-c.pageX<f.scrollSensitivity)this.scrollParent[0].scrollLeft=d=this.scrollParent[0].scrollLeft+f.scrollSpeed;
                else if(c.pageX-this.overflowOffset.left<
                    f.scrollSensitivity)this.scrollParent[0].scrollLeft=d=this.scrollParent[0].scrollLeft-f.scrollSpeed
                    }else{
                if(c.pageY-b(document).scrollTop()<f.scrollSensitivity)d=b(document).scrollTop(b(document).scrollTop()-f.scrollSpeed);
                else if(b(window).height()-(c.pageY-b(document).scrollTop())<f.scrollSensitivity)d=b(document).scrollTop(b(document).scrollTop()+f.scrollSpeed);
                if(c.pageX-b(document).scrollLeft()<f.scrollSensitivity)d=b(document).scrollLeft(b(document).scrollLeft()-f.scrollSpeed);
                else if(b(window).width()-
                    (c.pageX-b(document).scrollLeft())<f.scrollSensitivity)d=b(document).scrollLeft(b(document).scrollLeft()+f.scrollSpeed)
                    }
                    d!==false&&b.ui.ddmanager&&!f.dropBehaviour&&b.ui.ddmanager.prepareOffsets(this,c)
            }
            this.positionAbs=this._convertPositionTo("absolute");
        if(!this.options.axis||this.options.axis!="y")this.helper[0].style.left=this.position.left+"px";
        if(!this.options.axis||this.options.axis!="x")this.helper[0].style.top=this.position.top+"px";
        for(f=this.items.length-1;f>=0;f--){
            d=this.items[f];
            var a=
            d.item[0],e=this._intersectsWithPointer(d);
            if(e)if(a!=this.currentItem[0]&&this.placeholder[e==1?"next":"prev"]()[0]!=a&&!b.ui.contains(this.placeholder[0],a)&&(this.options.type=="semi-dynamic"?!b.ui.contains(this.element[0],a):true)){
                this.direction=e==1?"down":"up";
                if(this.options.tolerance=="pointer"||this._intersectsWithSides(d))this._rearrange(c,d);else break;
                this._trigger("change",c,this._uiHash());
                break
            }
            }
            this._contactContainers(c);
        b.ui.ddmanager&&b.ui.ddmanager.drag(this,c);
        this._trigger("sort",
        c,this._uiHash());
    this.lastPositionAbs=this.positionAbs;
    return false
    },
    _mouseStop:function(c,f){
        if(c){
            b.ui.ddmanager&&!this.options.dropBehaviour&&b.ui.ddmanager.drop(this,c);
            if(this.options.revert){
                var d=this,a=d.placeholder.offset();
                d.reverting=true;
                b(this.helper).animate({
                    left:a.left-this.offset.parent.left-d.margins.left+(this.offsetParent[0]==document.body?0:this.offsetParent[0].scrollLeft),
                    top:a.top-this.offset.parent.top-d.margins.top+(this.offsetParent[0]==document.body?0:this.offsetParent[0].scrollTop)
                    },
                parseInt(this.options.revert,10)||500,function(){
                    d._clear(c)
                    })
                }else this._clear(c,f);
            return false
            }
        },
cancel:function(){
    if(this.dragging){
        this._mouseUp();
        this.options.helper=="original"?this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper"):this.currentItem.show();
        for(var c=this.containers.length-1;c>=0;c--){
            this.containers[c]._trigger("deactivate",null,this._uiHash(this));
            if(this.containers[c].containerCache.over){
                this.containers[c]._trigger("out",null,this._uiHash(this));
                this.containers[c].containerCache.over=
                0
                }
            }
        }
    this.placeholder[0].parentNode&&this.placeholder[0].parentNode.removeChild(this.placeholder[0]);
    this.options.helper!="original"&&this.helper&&this.helper[0].parentNode&&this.helper.remove();
    b.extend(this,{
    helper:null,
    dragging:false,
    reverting:false,
    _noFinalSort:null
});
this.domPosition.prev?b(this.domPosition.prev).after(this.currentItem):b(this.domPosition.parent).prepend(this.currentItem);
    return this
    },
serialize:function(c){
    var f=this._getItemsAsjQuery(c&&c.connected),d=[];
    c=c||{};
    
    b(f).each(function(){
        var a=
        (b(c.item||this).attr(c.attribute||"id")||"").match(c.expression||/(.+)[-=_](.+)/);
        if(a)d.push((c.key||a[1]+"[]")+"="+(c.key&&c.expression?a[1]:a[2]))
            });
    return d.join("&")
    },
toArray:function(c){
    var f=this._getItemsAsjQuery(c&&c.connected),d=[];
    c=c||{};
    
    f.each(function(){
        d.push(b(c.item||this).attr(c.attribute||"id")||"")
        });
    return d
    },
_intersectsWith:function(c){
    var f=this.positionAbs.left,d=f+this.helperProportions.width,a=this.positionAbs.top,e=a+this.helperProportions.height,g=c.left,i=g+c.width,k=
    c.top,j=k+c.height,h=this.offset.click.top,l=this.offset.click.left;
    h=a+h>k&&a+h<j&&f+l>g&&f+l<i;
    return this.options.tolerance=="pointer"||this.options.forcePointerForContainers||this.options.tolerance!="pointer"&&this.helperProportions[this.floating?"width":"height"]>c[this.floating?"width":"height"]?h:g<f+this.helperProportions.width/2&&d-this.helperProportions.width/2<i&&k<a+this.helperProportions.height/2&&e-this.helperProportions.height/2<j
    },
_intersectsWithPointer:function(c){
    var f=b.ui.isOverAxis(this.positionAbs.top+
        this.offset.click.top,c.top,c.height);
    c=b.ui.isOverAxis(this.positionAbs.left+this.offset.click.left,c.left,c.width);
    f=f&&c;
    c=this._getDragVerticalDirection();
    var d=this._getDragHorizontalDirection();
    if(!f)return false;
    return this.floating?d&&d=="right"||c=="down"?2:1:c&&(c=="down"?2:1)
    },
_intersectsWithSides:function(c){
    var f=b.ui.isOverAxis(this.positionAbs.top+this.offset.click.top,c.top+c.height/2,c.height);
    c=b.ui.isOverAxis(this.positionAbs.left+this.offset.click.left,c.left+c.width/2,c.width);
    var d=this._getDragVerticalDirection(),a=this._getDragHorizontalDirection();
    return this.floating&&a?a=="right"&&c||a=="left"&&!c:d&&(d=="down"&&f||d=="up"&&!f)
    },
_getDragVerticalDirection:function(){
    var c=this.positionAbs.top-this.lastPositionAbs.top;
    return c!=0&&(c>0?"down":"up")
    },
_getDragHorizontalDirection:function(){
    var c=this.positionAbs.left-this.lastPositionAbs.left;
    return c!=0&&(c>0?"right":"left")
    },
refresh:function(c){
    this._refreshItems(c);
    this.refreshPositions();
    return this
    },
_connectWith:function(){
    var c=
    this.options;
    return c.connectWith.constructor==String?[c.connectWith]:c.connectWith
    },
_getItemsAsjQuery:function(c){
    var f=[],d=[],a=this._connectWith();
    if(a&&c)for(c=a.length-1;c>=0;c--)for(var e=b(a[c]),g=e.length-1;g>=0;g--){
        var i=b.data(e[g],"sortable");
        if(i&&i!=this&&!i.options.disabled)d.push([b.isFunction(i.options.items)?i.options.items.call(i.element):b(i.options.items,i.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),i])
            }
            d.push([b.isFunction(this.options.items)?this.options.items.call(this.element,
        null,{
            options:this.options,
            item:this.currentItem
            }):b(this.options.items,this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"),this]);
    for(c=d.length-1;c>=0;c--)d[c][0].each(function(){
        f.push(this)
        });
    return b(f)
    },
_removeCurrentsFromItems:function(){
    for(var c=this.currentItem.find(":data(sortable-item)"),f=0;f<this.items.length;f++)for(var d=0;d<c.length;d++)c[d]==this.items[f].item[0]&&this.items.splice(f,1)
        },
_refreshItems:function(c){
    this.items=[];
    this.containers=[this];
    var f=this.items,
    d=[[b.isFunction(this.options.items)?this.options.items.call(this.element[0],c,{
        item:this.currentItem
        }):b(this.options.items,this.element),this]],a=this._connectWith();
    if(a)for(var e=a.length-1;e>=0;e--)for(var g=b(a[e]),i=g.length-1;i>=0;i--){
        var k=b.data(g[i],"sortable");
        if(k&&k!=this&&!k.options.disabled){
            d.push([b.isFunction(k.options.items)?k.options.items.call(k.element[0],c,{
                item:this.currentItem
                }):b(k.options.items,k.element),k]);
            this.containers.push(k)
            }
        }
    for(e=d.length-1;e>=0;e--){
    c=d[e][1];
    a=d[e][0];
    i=0;
    for(g=a.length;i<g;i++){
        k=b(a[i]);
        k.data("sortable-item",c);
        f.push({
            item:k,
            instance:c,
            width:0,
            height:0,
            left:0,
            top:0
        })
        }
    }
},
refreshPositions:function(c){
    if(this.offsetParent&&this.helper)this.offset.parent=this._getParentOffset();
    for(var f=this.items.length-1;f>=0;f--){
        var d=this.items[f],a=this.options.toleranceElement?b(this.options.toleranceElement,d.item):d.item;
        if(!c){
            d.width=a.outerWidth();
            d.height=a.outerHeight()
            }
            a=a.offset();
        d.left=a.left;
        d.top=a.top
        }
        if(this.options.custom&&this.options.custom.refreshContainers)this.options.custom.refreshContainers.call(this);
    else for(f=this.containers.length-1;f>=0;f--){
        a=this.containers[f].element.offset();
        this.containers[f].containerCache.left=a.left;
        this.containers[f].containerCache.top=a.top;
        this.containers[f].containerCache.width=this.containers[f].element.outerWidth();
        this.containers[f].containerCache.height=this.containers[f].element.outerHeight()
        }
        return this
    },
_createPlaceholder:function(c){
    var f=c||this,d=f.options;
    if(!d.placeholder||d.placeholder.constructor==String){
        var a=d.placeholder;
        d.placeholder={
            element:function(){
                var e=
                b(document.createElement(f.currentItem[0].nodeName)).addClass(a||f.currentItem[0].className+" ui-sortable-placeholder").removeClass("ui-sortable-helper")[0];
                if(!a)e.style.visibility="hidden";
                return e
                },
            update:function(e,g){
                if(!(a&&!d.forcePlaceholderSize)){
                    g.height()||g.height(f.currentItem.innerHeight()-parseInt(f.currentItem.css("paddingTop")||0,10)-parseInt(f.currentItem.css("paddingBottom")||0,10));
                    g.width()||g.width(f.currentItem.innerWidth()-parseInt(f.currentItem.css("paddingLeft")||0,10)-parseInt(f.currentItem.css("paddingRight")||
                        0,10))
                    }
                }
        }
}
f.placeholder=b(d.placeholder.element.call(f.element,f.currentItem));
f.currentItem.after(f.placeholder);
d.placeholder.update(f,f.placeholder)
},
_contactContainers:function(c){
    for(var f=null,d=null,a=this.containers.length-1;a>=0;a--)if(!b.ui.contains(this.currentItem[0],this.containers[a].element[0]))if(this._intersectsWith(this.containers[a].containerCache)){
        if(!(f&&b.ui.contains(this.containers[a].element[0],f.element[0]))){
            f=this.containers[a];
            d=a
            }
        }else if(this.containers[a].containerCache.over){
        this.containers[a]._trigger("out",
            c,this._uiHash(this));
        this.containers[a].containerCache.over=0
        }
        if(f)if(this.containers.length===1){
    this.containers[d]._trigger("over",c,this._uiHash(this));
    this.containers[d].containerCache.over=1
    }else if(this.currentContainer!=this.containers[d]){
    f=1E4;
    a=null;
    for(var e=this.positionAbs[this.containers[d].floating?"left":"top"],g=this.items.length-1;g>=0;g--)if(b.ui.contains(this.containers[d].element[0],this.items[g].item[0])){
        var i=this.items[g][this.containers[d].floating?"left":"top"];
        if(Math.abs(i-
            e)<f){
            f=Math.abs(i-e);
            a=this.items[g]
            }
        }
    if(a||this.options.dropOnEmpty){
    this.currentContainer=this.containers[d];
    a?this._rearrange(c,a,null,true):this._rearrange(c,null,this.containers[d].element,true);
    this._trigger("change",c,this._uiHash());
    this.containers[d]._trigger("change",c,this._uiHash(this));
    this.options.placeholder.update(this.currentContainer,this.placeholder);
    this.containers[d]._trigger("over",c,this._uiHash(this));
    this.containers[d].containerCache.over=1
    }
}
},
_createHelper:function(c){
    var f=
    this.options;
    c=b.isFunction(f.helper)?b(f.helper.apply(this.element[0],[c,this.currentItem])):f.helper=="clone"?this.currentItem.clone():this.currentItem;
    c.parents("body").length||b(f.appendTo!="parent"?f.appendTo:this.currentItem[0].parentNode)[0].appendChild(c[0]);
    if(c[0]==this.currentItem[0])this._storedCSS={
        width:this.currentItem[0].style.width,
        height:this.currentItem[0].style.height,
        position:this.currentItem.css("position"),
        top:this.currentItem.css("top"),
        left:this.currentItem.css("left")
        };
        
    if(c[0].style.width==
        ""||f.forceHelperSize)c.width(this.currentItem.width());
    if(c[0].style.height==""||f.forceHelperSize)c.height(this.currentItem.height());
    return c
    },
_adjustOffsetFromHelper:function(c){
    if(typeof c=="string")c=c.split(" ");
    if(b.isArray(c))c={
        left:+c[0],
        top:+c[1]||0
        };
        
    if("left"in c)this.offset.click.left=c.left+this.margins.left;
    if("right"in c)this.offset.click.left=this.helperProportions.width-c.right+this.margins.left;
    if("top"in c)this.offset.click.top=c.top+this.margins.top;
    if("bottom"in c)this.offset.click.top=
        this.helperProportions.height-c.bottom+this.margins.top
        },
_getParentOffset:function(){
    this.offsetParent=this.helper.offsetParent();
    var c=this.offsetParent.offset();
    if(this.cssPosition=="absolute"&&this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],this.offsetParent[0])){
        c.left+=this.scrollParent.scrollLeft();
        c.top+=this.scrollParent.scrollTop()
        }
        if(this.offsetParent[0]==document.body||this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()=="html"&&b.browser.msie)c=

        {
        top:0,
        left:0
    };
    
    return{
        top:c.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),
        left:c.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)
        }
    },
_getRelativeOffset:function(){
    if(this.cssPosition=="relative"){
        var c=this.currentItem.position();
        return{
            top:c.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),
            left:c.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()
            }
        }else return{
    top:0,
    left:0
}
},
_cacheMargins:function(){
    this.margins={
        left:parseInt(this.currentItem.css("marginLeft"),
            10)||0,
        top:parseInt(this.currentItem.css("marginTop"),10)||0
        }
    },
_cacheHelperProportions:function(){
    this.helperProportions={
        width:this.helper.outerWidth(),
        height:this.helper.outerHeight()
        }
    },
_setContainment:function(){
    var c=this.options;
    if(c.containment=="parent")c.containment=this.helper[0].parentNode;
    if(c.containment=="document"||c.containment=="window")this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,b(c.containment=="document"?
        document:window).width()-this.helperProportions.width-this.margins.left,(b(c.containment=="document"?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];
    if(!/^(document|window|parent)$/.test(c.containment)){
        var f=b(c.containment)[0];
        c=b(c.containment).offset();
        var d=b(f).css("overflow")!="hidden";
        this.containment=[c.left+(parseInt(b(f).css("borderLeftWidth"),10)||0)+(parseInt(b(f).css("paddingLeft"),10)||0)-this.margins.left,c.top+(parseInt(b(f).css("borderTopWidth"),
            10)||0)+(parseInt(b(f).css("paddingTop"),10)||0)-this.margins.top,c.left+(d?Math.max(f.scrollWidth,f.offsetWidth):f.offsetWidth)-(parseInt(b(f).css("borderLeftWidth"),10)||0)-(parseInt(b(f).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,c.top+(d?Math.max(f.scrollHeight,f.offsetHeight):f.offsetHeight)-(parseInt(b(f).css("borderTopWidth"),10)||0)-(parseInt(b(f).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top]
        }
    },
_convertPositionTo:function(c,f){
    if(!f)f=
        this.position;
    var d=c=="absolute"?1:-1,a=this.cssPosition=="absolute"&&!(this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,e=/(html|body)/i.test(a[0].tagName);
    return{
        top:f.top+this.offset.relative.top*d+this.offset.parent.top*d-(b.browser.safari&&this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollTop():e?0:a.scrollTop())*d),
        left:f.left+this.offset.relative.left*d+this.offset.parent.left*d-(b.browser.safari&&
            this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():e?0:a.scrollLeft())*d)
        }
    },
_generatePosition:function(c){
    var f=this.options,d=this.cssPosition=="absolute"&&!(this.scrollParent[0]!=document&&b.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,a=/(html|body)/i.test(d[0].tagName);
    if(this.cssPosition=="relative"&&!(this.scrollParent[0]!=document&&this.scrollParent[0]!=this.offsetParent[0]))this.offset.relative=this._getRelativeOffset();
    var e=c.pageX,g=c.pageY;
    if(this.originalPosition){
        if(this.containment){
            if(c.pageX-this.offset.click.left<this.containment[0])e=this.containment[0]+this.offset.click.left;
            if(c.pageY-this.offset.click.top<this.containment[1])g=this.containment[1]+this.offset.click.top;
            if(c.pageX-this.offset.click.left>this.containment[2])e=this.containment[2]+this.offset.click.left;
            if(c.pageY-this.offset.click.top>this.containment[3])g=this.containment[3]+this.offset.click.top
                }
                if(f.grid){
            g=this.originalPageY+Math.round((g-
                this.originalPageY)/f.grid[1])*f.grid[1];
            g=this.containment?!(g-this.offset.click.top<this.containment[1]||g-this.offset.click.top>this.containment[3])?g:!(g-this.offset.click.top<this.containment[1])?g-f.grid[1]:g+f.grid[1]:g;
            e=this.originalPageX+Math.round((e-this.originalPageX)/f.grid[0])*f.grid[0];
            e=this.containment?!(e-this.offset.click.left<this.containment[0]||e-this.offset.click.left>this.containment[2])?e:!(e-this.offset.click.left<this.containment[0])?e-f.grid[0]:e+f.grid[0]:e
            }
        }
    return{
    top:g-
    this.offset.click.top-this.offset.relative.top-this.offset.parent.top+(b.browser.safari&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollTop():a?0:d.scrollTop()),
    left:e-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+(b.browser.safari&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():a?0:d.scrollLeft())
    }
},
_rearrange:function(c,f,d,a){
    d?d[0].appendChild(this.placeholder[0]):f.item[0].parentNode.insertBefore(this.placeholder[0],
        this.direction=="down"?f.item[0]:f.item[0].nextSibling);
    this.counter=this.counter?++this.counter:1;
    var e=this,g=this.counter;
    window.setTimeout(function(){
        g==e.counter&&e.refreshPositions(!a)
        },0)
    },
_clear:function(c,f){
    this.reverting=false;
    var d=[];
    !this._noFinalSort&&this.currentItem[0].parentNode&&this.placeholder.before(this.currentItem);
    this._noFinalSort=null;
    if(this.helper[0]==this.currentItem[0]){
        for(var a in this._storedCSS)if(this._storedCSS[a]=="auto"||this._storedCSS[a]=="static")this._storedCSS[a]=
            "";this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")
        }else this.currentItem.show();
    this.fromOutside&&!f&&d.push(function(e){
        this._trigger("receive",e,this._uiHash(this.fromOutside))
        });
    if((this.fromOutside||this.domPosition.prev!=this.currentItem.prev().not(".ui-sortable-helper")[0]||this.domPosition.parent!=this.currentItem.parent()[0])&&!f)d.push(function(e){
        this._trigger("update",e,this._uiHash())
        });
    if(!b.ui.contains(this.element[0],this.currentItem[0])){
        f||d.push(function(e){
            this._trigger("remove",
                e,this._uiHash())
            });
        for(a=this.containers.length-1;a>=0;a--)if(b.ui.contains(this.containers[a].element[0],this.currentItem[0])&&!f){
            d.push(function(e){
                return function(g){
                    e._trigger("receive",g,this._uiHash(this))
                    }
                }.call(this,this.containers[a]));
        d.push(function(e){
            return function(g){
                e._trigger("update",g,this._uiHash(this))
                }
            }.call(this,this.containers[a]))
        }
    }
for(a=this.containers.length-1;a>=0;a--){
    f||d.push(function(e){
        return function(g){
            e._trigger("deactivate",g,this._uiHash(this))
            }
        }.call(this,
        this.containers[a]));
if(this.containers[a].containerCache.over){
    d.push(function(e){
        return function(g){
            e._trigger("out",g,this._uiHash(this))
            }
        }.call(this,this.containers[a]));
this.containers[a].containerCache.over=0
}
}
this._storedCursor&&b("body").css("cursor",this._storedCursor);
this._storedOpacity&&this.helper.css("opacity",this._storedOpacity);
if(this._storedZIndex)this.helper.css("zIndex",this._storedZIndex=="auto"?"":this._storedZIndex);
this.dragging=false;
if(this.cancelHelperRemoval){
    if(!f){
        this._trigger("beforeStop",
            c,this._uiHash());
        for(a=0;a<d.length;a++)d[a].call(this,c);
        this._trigger("stop",c,this._uiHash())
        }
        return false
    }
    f||this._trigger("beforeStop",c,this._uiHash());
this.placeholder[0].parentNode.removeChild(this.placeholder[0]);
this.helper[0]!=this.currentItem[0]&&this.helper.remove();
this.helper=null;
if(!f){
    for(a=0;a<d.length;a++)d[a].call(this,c);
    this._trigger("stop",c,this._uiHash())
    }
    this.fromOutside=false;
return true
},
_trigger:function(){
    b.Widget.prototype._trigger.apply(this,arguments)===false&&this.cancel()
    },
_uiHash:function(c){
    var f=c||this;
    return{
        helper:f.helper,
        placeholder:f.placeholder||b([]),
        position:f.position,
        originalPosition:f.originalPosition,
        offset:f.positionAbs,
        item:f.currentItem,
        sender:c?c.element:null
        }
    }
});
b.extend(b.ui.sortable,{
    version:"1.8"
})
})(jQuery);
(function(b){
    b.widget("ui.accordion",{
        options:{
            active:0,
            animated:"slide",
            autoHeight:true,
            clearStyle:false,
            collapsible:false,
            event:"click",
            fillSpace:false,
            header:"> li > :first-child,> :not(li):even",
            icons:{
                header:"ui-icon-triangle-1-e",
                headerSelected:"ui-icon-triangle-1-s"
            },
            navigation:false,
            navigationFilter:function(){
                return this.href.toLowerCase()==location.href.toLowerCase()
                }
            },
    _create:function(){
        var c=this.options,f=this;
        this.running=0;
        this.element.addClass("ui-accordion ui-widget ui-helper-reset");
        this.element[0].nodeName=="UL"&&this.element.children("li").addClass("ui-accordion-li-fix");
        this.headers=this.element.find(c.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all").bind("mouseenter.accordion",function(){
            b(this).addClass("ui-state-hover")
            }).bind("mouseleave.accordion",function(){
            b(this).removeClass("ui-state-hover")
            }).bind("focus.accordion",function(){
            b(this).addClass("ui-state-focus")
            }).bind("blur.accordion",function(){
            b(this).removeClass("ui-state-focus")
            });
        this.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom");
        if(c.navigation){
            var d=this.element.find("a").filter(c.navigationFilter);
            if(d.length){
                var a=d.closest(".ui-accordion-header");
                this.active=a.length?a:d.closest(".ui-accordion-content").prev()
                }
            }
        this.active=this._findActive(this.active||c.active).toggleClass("ui-state-default").toggleClass("ui-state-active").toggleClass("ui-corner-all").toggleClass("ui-corner-top");
        this.active.next().addClass("ui-accordion-content-active");
        this._createIcons();
        b.browser.msie&&this.element.find("a").css("zoom","1");
        this.resize();
        this.element.attr("role","tablist");
        this.headers.attr("role","tab").bind("keydown",function(e){
            return f._keydown(e)
            }).next().attr("role","tabpanel");
        this.headers.not(this.active||"").attr("aria-expanded","false").attr("tabIndex","-1").next().hide();
        this.active.length?this.active.attr("aria-expanded","true").attr("tabIndex","0"):this.headers.eq(0).attr("tabIndex","0");
        b.browser.safari||this.headers.find("a").attr("tabIndex",
            "-1");
        c.event&&this.headers.bind(c.event+".accordion",function(e){
            f._clickHandler.call(f,e,this);
            e.preventDefault()
            })
        },
    _createIcons:function(){
        var c=this.options;
        if(c.icons){
            b("<span/>").addClass("ui-icon "+c.icons.header).prependTo(this.headers);
            this.active.find(".ui-icon").toggleClass(c.icons.header).toggleClass(c.icons.headerSelected);
            this.element.addClass("ui-accordion-icons")
            }
        },
_destroyIcons:function(){
    this.headers.children(".ui-icon").remove();
    this.element.removeClass("ui-accordion-icons")
    },
destroy:function(){
    var c=this.options;
    this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role").unbind(".accordion").removeData("accordion");
    this.headers.unbind(".accordion").removeClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-corner-top").removeAttr("role").removeAttr("aria-expanded").removeAttr("tabindex");
    this.headers.find("a").removeAttr("tabindex");
    this._destroyIcons();
    var f=this.headers.next().css("display","").removeAttr("role").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active");
    if(c.autoHeight||c.fillHeight)f.css("height","");
    return this
    },
_setOption:function(c,f){
    b.Widget.prototype._setOption.apply(this,arguments);
    c=="active"&&this.activate(f);
    if(c=="icons"){
        this._destroyIcons();
        f&&this._createIcons()
        }
    },
_keydown:function(c){
    var f=b.ui.keyCode;
    if(!(this.options.disabled||c.altKey||c.ctrlKey)){
        var d=this.headers.length,a=this.headers.index(c.target),e=false;
        switch(c.keyCode){
            case f.RIGHT:case f.DOWN:
                e=this.headers[(a+1)%d];
                break;
            case f.LEFT:case f.UP:
                e=this.headers[(a-1+d)%
                d];
                break;
            case f.SPACE:case f.ENTER:
                this._clickHandler({
                target:c.target
                },c.target);
            c.preventDefault()
                }
                if(e){
            b(c.target).attr("tabIndex","-1");
            b(e).attr("tabIndex","0");
            e.focus();
            return false
            }
            return true
        }
    },
resize:function(){
    var c=this.options,f;
    if(c.fillSpace){
        if(b.browser.msie){
            var d=this.element.parent().css("overflow");
            this.element.parent().css("overflow","hidden")
            }
            f=this.element.parent().height();
        b.browser.msie&&this.element.parent().css("overflow",d);
        this.headers.each(function(){
            f-=b(this).outerHeight(true)
            });
        this.headers.next().each(function(){
            b(this).height(Math.max(0,f-b(this).innerHeight()+b(this).height()))
            }).css("overflow","auto")
        }else if(c.autoHeight){
        f=0;
        this.headers.next().each(function(){
            f=Math.max(f,b(this).height())
            }).height(f)
        }
        return this
    },
activate:function(c){
    this.options.active=c;
    c=this._findActive(c)[0];
    this._clickHandler({
        target:c
    },c);
    return this
    },
_findActive:function(c){
    return c?typeof c=="number"?this.headers.filter(":eq("+c+")"):this.headers.not(this.headers.not(c)):c===false?b([]):
    this.headers.filter(":eq(0)")
    },
_clickHandler:function(c,f){
    var d=this.options;
    if(!d.disabled)if(c.target){
        var a=b(c.currentTarget||f),e=a[0]==this.active[0];
        d.active=d.collapsible&&e?false:b(".ui-accordion-header",this.element).index(a);
        if(!(this.running||!d.collapsible&&e)){
            this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").find(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header);
            if(!e){
                a.removeClass("ui-state-default ui-corner-all").addClass("ui-state-active ui-corner-top").find(".ui-icon").removeClass(d.icons.header).addClass(d.icons.headerSelected);
                a.next().addClass("ui-accordion-content-active")
                }
                k=a.next();
            g=this.active.next();
            i={
                options:d,
                newHeader:e&&d.collapsible?b([]):a,
                oldHeader:this.active,
                newContent:e&&d.collapsible?b([]):k,
                oldContent:g
            };
            
            d=this.headers.index(this.active[0])>this.headers.index(a[0]);
            this.active=e?b([]):a;
            this._toggle(k,g,i,e,d)
            }
        }else if(d.collapsible){
        this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").find(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header);
        this.active.next().addClass("ui-accordion-content-active");
        var g=this.active.next(),i={
            options:d,
            newHeader:b([]),
            oldHeader:d.active,
            newContent:b([]),
            oldContent:g
        },k=this.active=b([]);
        this._toggle(k,g,i)
        }
    },
_toggle:function(c,f,d,a,e){
    var g=this.options,i=this;
    this.toShow=c;
    this.toHide=f;
    this.data=d;
    var k=function(){
        if(i)return i._completed.apply(i,arguments)
            };
            
    this._trigger("changestart",null,this.data);
    this.running=f.size()===0?c.size():f.size();
    if(g.animated){
        d={};
        
        d=g.collapsible&&a?{
            toShow:b([]),
            toHide:f,
            complete:k,
            down:e,
            autoHeight:g.autoHeight||g.fillSpace
            }:{
            toShow:c,
            toHide:f,
            complete:k,
            down:e,
            autoHeight:g.autoHeight||g.fillSpace
            };
            
        if(!g.proxied)g.proxied=g.animated;
        if(!g.proxiedDuration)g.proxiedDuration=g.duration;
        g.animated=b.isFunction(g.proxied)?g.proxied(d):g.proxied;
        g.duration=b.isFunction(g.proxiedDuration)?g.proxiedDuration(d):g.proxiedDuration;
        a=b.ui.accordion.animations;
        var j=g.duration,h=g.animated;
        if(h&&!a[h]&&!b.easing[h])h="slide";
        a[h]||(a[h]=function(l){
            this.slide(l,{
                easing:h,
                duration:j||700
                })
            });
        a[h](d)
        }else{
        if(g.collapsible&&a)c.toggle();
        else{
            f.hide();
            c.show()
            }
            k(true)
        }
        f.prev().attr("aria-expanded","false").attr("tabIndex","-1").blur();
    c.prev().attr("aria-expanded","true").attr("tabIndex","0").focus()
    },
_completed:function(c){
    var f=this.options;
    this.running=c?0:--this.running;
    if(!this.running){
        f.clearStyle&&this.toShow.add(this.toHide).css({
            height:"",
            overflow:""
        });
        this.toHide.removeClass("ui-accordion-content-active");
        this._trigger("change",null,this.data)
        }
    }
});
b.extend(b.ui.accordion,

{
    version:"1.8",
    animations:{
        slide:function(c,f){
            c=b.extend({
                easing:"swing",
                duration:300
            },c,f);
            if(c.toHide.size())if(c.toShow.size()){
                var d=c.toShow.css("overflow"),a=0,e={},g={},i,k=c.toShow;
                i=k[0].style.width;
                k.width(parseInt(k.parent().width(),10)-parseInt(k.css("paddingLeft"),10)-parseInt(k.css("paddingRight"),10)-(parseInt(k.css("borderLeftWidth"),10)||0)-(parseInt(k.css("borderRightWidth"),10)||0));
                b.each(["height","paddingTop","paddingBottom"],function(j,h){
                    g[h]="hide";
                    var l=(""+b.css(c.toShow[0],
                        h)).match(/^([\d+-.]+)(.*)$/);
                    e[h]={
                        value:l[1],
                        unit:l[2]||"px"
                        }
                    });
            c.toShow.css({
                height:0,
                overflow:"hidden"
            }).show();
                c.toHide.filter(":hidden").each(c.complete).end().filter(":visible").animate(g,{
                step:function(j,h){
                    if(h.prop=="height")a=h.end-h.start===0?0:(h.now-h.start)/(h.end-h.start);
                    c.toShow[0].style[h.prop]=a*e[h.prop].value+e[h.prop].unit
                    },
                duration:c.duration,
                easing:c.easing,
                complete:function(){
                    c.autoHeight||c.toShow.css("height","");
                    c.toShow.css("width",i);
                    c.toShow.css({
                        overflow:d
                    });
                    c.complete()
                    }
                })
            }else c.toHide.animate({
            height:"hide"
        },
        c);else c.toShow.animate({
        height:"show"
    },c)
    },
bounceslide:function(c){
    this.slide(c,{
        easing:c.down?"easeOutBounce":"swing",
        duration:c.down?1E3:200
        })
    }
}
})
})(jQuery);
(function(b){
    b.widget("ui.autocomplete",{
        options:{
            minLength:1,
            delay:300
        },
        _create:function(){
            var c=this,f=this.element[0].ownerDocument;
            this.element.addClass("ui-autocomplete-input").attr("autocomplete","off").attr({
                role:"textbox",
                "aria-autocomplete":"list",
                "aria-haspopup":"true"
            }).bind("keydown.autocomplete",function(d){
                var a=b.ui.keyCode;
                switch(d.keyCode){
                    case a.PAGE_UP:
                        c._move("previousPage",d);
                        break;
                    case a.PAGE_DOWN:
                        c._move("nextPage",d);
                        break;
                    case a.UP:
                        c._move("previous",d);
                        d.preventDefault();
                        break;
                    case a.DOWN:
                        c._move("next",d);
                        d.preventDefault();
                        break;
                    case a.ENTER:
                        c.menu.active&&d.preventDefault();
                    case a.TAB:
                        if(!c.menu.active)break;
                        c.menu.select();
                        break;
                    case a.ESCAPE:
                        c.element.val(c.term);
                        c.close(d);
                        break;
                    case a.SHIFT:case a.CONTROL:case 18:
                        break;
                    default:
                        clearTimeout(c.searching);
                        c.searching=setTimeout(function(){
                        c.search(null,d)
                        },c.options.delay)
                    }
                    }).bind("focus.autocomplete",function(){
            c.previous=c.element.val()
            }).bind("blur.autocomplete",function(d){
            clearTimeout(c.searching);
            c.closing=
            setTimeout(function(){
                c.close(d)
                },150)
            });
        this._initSource();
        this.response=function(){
            return c._response.apply(c,arguments)
            };
            
        this.menu=b("<ul></ul>").addClass("ui-autocomplete").appendTo("body",f).menu({
            focus:function(d,a){
                var e=a.item.data("item.autocomplete");
                false!==c._trigger("focus",null,{
                    item:e
                })&&c.element.val(e.value)
                },
            selected:function(d,a){
                var e=a.item.data("item.autocomplete");
                false!==c._trigger("select",d,{
                    item:e
                })&&c.element.val(e.value);
                c.close(d);
                c.previous=c.element.val();
                c.element[0]!==
                f.activeElement&&c.element.focus()
                },
            blur:function(){
                c.menu.element.is(":visible")&&c.element.val(c.term)
                }
            }).zIndex(this.element.zIndex()+1).css({
        top:0,
        left:0
    }).hide().data("menu");
        b.fn.bgiframe&&this.menu.element.bgiframe()
        },
    destroy:function(){
        this.element.removeClass("ui-autocomplete-input ui-widget ui-widget-content").removeAttr("autocomplete").removeAttr("role").removeAttr("aria-autocomplete").removeAttr("aria-haspopup");
        this.menu.element.remove();
        b.Widget.prototype.destroy.call(this)
        },
    _setOption:function(c){
        b.Widget.prototype._setOption.apply(this,
            arguments);
        c==="source"&&this._initSource()
        },
    _initSource:function(){
        var c,f;
        if(b.isArray(this.options.source)){
            c=this.options.source;
            this.source=function(d,a){
                var e=RegExp(b.ui.autocomplete.escapeRegex(d.term),"i");
                a(b.grep(c,function(g){
                    return e.test(g.label||g.value||g)
                    }))
                }
            }else if(typeof this.options.source==="string"){
        f=this.options.source;
        this.source=function(d,a){
            b.getJSON(f,d,a)
            }
        }else this.source=this.options.source
    },
search:function(c,f){
    c=c!=null?c:this.element.val();
    if(c.length<this.options.minLength)return this.close(f);
    clearTimeout(this.closing);
    if(this._trigger("search")!==false)return this._search(c)
        },
_search:function(c){
    this.term=this.element.addClass("ui-autocomplete-loading").val();
    this.source({
        term:c
    },this.response)
    },
_response:function(c){
    if(c.length){
        c=this._normalize(c);
        this._suggest(c);
        this._trigger("open")
        }else this.close();
    this.element.removeClass("ui-autocomplete-loading")
    },
close:function(c){
    clearTimeout(this.closing);
    if(this.menu.element.is(":visible")){
        this._trigger("close",c);
        this.menu.element.hide();
        this.menu.deactivate()
        }
        this.previous!==this.element.val()&&this._trigger("change",c)
    },
_normalize:function(c){
    if(c.length&&c[0].label&&c[0].value)return c;
    return b.map(c,function(f){
        if(typeof f==="string")return{
            label:f,
            value:f
        };
        
        return b.extend({
            label:f.label||f.value,
            value:f.value||f.label
            },f)
        })
    },
_suggest:function(c){
    var f=this.menu.element.empty().zIndex(this.element.zIndex()+1),d;
    this._renderMenu(f,c);
    this.menu.deactivate();
    this.menu.refresh();
    this.menu.element.show().position({
        my:"left top",
        at:"left bottom",
        of:this.element,
        collision:"none"
    });
    c=f.width("").width();
    d=this.element.width();
    f.width(Math.max(c,d))
    },
_renderMenu:function(c,f){
    var d=this;
    b.each(f,function(a,e){
        d._renderItem(c,e)
        })
    },
_renderItem:function(c,f){
    return b("<li></li>").data("item.autocomplete",f).append("<a>"+f.label+"</a>").appendTo(c)
    },
_move:function(c,f){
    if(this.menu.element.is(":visible"))if(this.menu.first()&&/^previous/.test(c)||this.menu.last()&&/^next/.test(c)){
        this.element.val(this.term);
        this.menu.deactivate()
        }else this.menu[c]();
    else this.search(null,f)
        },
widget:function(){
    return this.menu.element
    }
});
b.extend(b.ui.autocomplete,{
    escapeRegex:function(c){
        return c.replace(/([\^\$\(\)\[\]\{\}\*\.\+\?\|\\])/gi,"\\$1")
        }
    })
})(jQuery);
(function(b){
    b.widget("ui.menu",{
        _create:function(){
            var c=this;
            this.element.addClass("ui-menu ui-widget ui-widget-content ui-corner-all").attr({
                role:"listbox",
                "aria-activedescendant":"ui-active-menuitem"
            }).click(function(f){
                f.preventDefault();
                c.select()
                });
            this.refresh()
            },
        refresh:function(){
            var c=this;
            this.element.children("li:not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role","menuitem").children("a").addClass("ui-corner-all").attr("tabindex",-1).mouseenter(function(){
                c.activate(b(this).parent())
                }).mouseleave(function(){
                c.deactivate()
                })
            },
        activate:function(c){
            this.deactivate();
            if(this.hasScroll()){
                var f=c.offset().top-this.element.offset().top,d=this.element.attr("scrollTop"),a=this.element.height();
                if(f<0)this.element.attr("scrollTop",d+f);else f>a&&this.element.attr("scrollTop",d+f-a+c.height())
                    }
                    this.active=c.eq(0).children("a").addClass("ui-state-hover").attr("id","ui-active-menuitem").end();
            this._trigger("focus",null,{
                item:c
            })
            },
        deactivate:function(){
            if(this.active){
                this.active.children("a").removeClass("ui-state-hover").removeAttr("id");
                this._trigger("blur");
                this.active=null
                }
            },
    next:function(){
        this.move("next","li:first")
        },
    previous:function(){
        this.move("prev","li:last")
        },
    first:function(){
        return this.active&&!this.active.prev().length
        },
    last:function(){
        return this.active&&!this.active.next().length
        },
    move:function(c,f){
        if(this.active){
            var d=this.active[c]();
            d.length?this.activate(d):this.activate(this.element.children(f))
            }else this.activate(this.element.children(f))
            },
    nextPage:function(){
        if(this.hasScroll())if(!this.active||this.last())this.activate(this.element.children(":first"));
            else{
                var c=this.active.offset().top,f=this.element.height(),d=this.element.children("li").filter(function(){
                    var a=b(this).offset().top-c-f+b(this).height();
                    return a<10&&a>-10
                    });
                d.length||(d=this.element.children(":last"));
                this.activate(d)
                }else this.activate(this.element.children(!this.active||this.last()?":first":":last"))
            },
    previousPage:function(){
        if(this.hasScroll())if(!this.active||this.first())this.activate(this.element.children(":last"));
            else{
            var c=this.active.offset().top,f=this.element.height();
            result=this.element.children("li").filter(function(){
                var d=b(this).offset().top-c+f-b(this).height();
                return d<10&&d>-10
                });
            result.length||(result=this.element.children(":first"));
            this.activate(result)
            }else this.activate(this.element.children(!this.active||this.first()?":last":":first"))
            },
    hasScroll:function(){
        return this.element.height()<this.element.attr("scrollHeight")
        },
    select:function(){
        this._trigger("selected",null,{
            item:this.active
            })
        }
    })
})(jQuery);
(function(b){
    var c,f=function(a){
        b(":ui-button",a.target.form).each(function(){
            var e=b(this).data("button");
            setTimeout(function(){
                e.refresh()
                },1)
            })
        },d=function(a){
        var e=a.name,g=a.form,i=b([]);
        if(e)i=g?b(g).find("[name='"+e+"']"):b("[name='"+e+"']",a.ownerDocument).filter(function(){
            return!this.form
            });
        return i
        };
        
    b.widget("ui.button",{
        options:{
            text:true,
            label:null,
            icons:{
                primary:null,
                secondary:null
            }
        },
    _create:function(){
        this.element.closest("form").unbind("reset.button").bind("reset.button",f);
        this._determineButtonType();
        this.hasTitle=!!this.buttonElement.attr("title");
        var a=this,e=this.options,g=this.type==="checkbox"||this.type==="radio",i="ui-state-hover"+(!g?" ui-state-active":"");
        if(e.label===null)e.label=this.buttonElement.html();
        if(this.element.is(":disabled"))e.disabled=true;
        this.buttonElement.addClass("ui-button ui-widget ui-state-default ui-corner-all").attr("role","button").bind("mouseenter.button",function(){
            if(!e.disabled){
                b(this).addClass("ui-state-hover");
                this===c&&b(this).addClass("ui-state-active")
                }
            }).bind("mouseleave.button",
        function(){
            e.disabled||b(this).removeClass(i)
            }).bind("focus.button",function(){
        b(this).addClass("ui-state-focus")
        }).bind("blur.button",function(){
        b(this).removeClass("ui-state-focus")
        });
    g&&this.element.bind("change.button",function(){
        a.refresh()
        });
    if(this.type==="checkbox")this.buttonElement.bind("click.button",function(){
        if(e.disabled)return false;
        b(this).toggleClass("ui-state-active");
        a.buttonElement.attr("aria-pressed",a.element[0].checked)
        });
    else if(this.type==="radio")this.buttonElement.bind("click.button",
        function(){
            if(e.disabled)return false;
            b(this).addClass("ui-state-active");
            a.buttonElement.attr("aria-pressed",true);
            var k=a.element[0];
            d(k).not(k).map(function(){
                return b(this).button("widget")[0]
                }).removeClass("ui-state-active").attr("aria-pressed",false)
            });
    else{
        this.buttonElement.bind("mousedown.button",function(){
            if(e.disabled)return false;
            b(this).addClass("ui-state-active");
            c=this;
            b(document).one("mouseup",function(){
                c=null
                })
            }).bind("mouseup.button",function(){
            if(e.disabled)return false;
            b(this).removeClass("ui-state-active")
            }).bind("keydown.button",
            function(k){
                if(e.disabled)return false;
                if(k.keyCode==b.ui.keyCode.SPACE||k.keyCode==b.ui.keyCode.ENTER)b(this).addClass("ui-state-active")
                    }).bind("keyup.button",function(){
            b(this).removeClass("ui-state-active")
            });
        this.buttonElement.is("a")&&this.buttonElement.keyup(function(k){
            k.keyCode===b.ui.keyCode.SPACE&&b(this).click()
            })
        }
        this._setOption("disabled",e.disabled)
        },
    _determineButtonType:function(){
        this.type=this.element.is(":checkbox")?"checkbox":this.element.is(":radio")?"radio":this.element.is("input")?
        "input":"button";
        if(this.type==="checkbox"||this.type==="radio"){
            this.buttonElement=this.element.parents().last().find("[for="+this.element.attr("id")+"]");
            this.element.addClass("ui-helper-hidden-accessible");
            var a=this.element.is(":checked");
            a&&this.buttonElement.addClass("ui-state-active");
            this.buttonElement.attr("aria-pressed",a)
            }else this.buttonElement=this.element
            },
    widget:function(){
        return this.buttonElement
        },
    destroy:function(){
        this.element.removeClass("ui-helper-hidden-accessible");
        this.buttonElement.removeClass("ui-button ui-widget ui-state-default ui-corner-all ui-state-hover ui-state-active ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon ui-button-text-only").removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html());
        this.hasTitle||this.buttonElement.removeAttr("title");
        b.Widget.prototype.destroy.call(this)
        },
    _setOption:function(a,e){
        b.Widget.prototype._setOption.apply(this,arguments);
        if(a==="disabled")e?this.element.attr("disabled",true):this.element.removeAttr("disabled");
        this._resetButton()
        },
    refresh:function(){
        var a=this.element.is(":disabled");
        a!==this.options.disabled&&this._setOption("disabled",a);
        if(this.type==="radio")d(this.element[0]).each(function(){
            b(this).is(":checked")?b(this).button("widget").addClass("ui-state-active").attr("aria-pressed",
                true):b(this).button("widget").removeClass("ui-state-active").attr("aria-pressed",false)
            });
        else if(this.type==="checkbox")this.element.is(":checked")?this.buttonElement.addClass("ui-state-active").attr("aria-pressed",true):this.buttonElement.removeClass("ui-state-active").attr("aria-pressed",false)
            },
    _resetButton:function(){
        if(this.type==="input")this.options.label&&this.element.val(this.options.label);
        else{
            var a=this.buttonElement,e=b("<span></span>").addClass("ui-button-text").html(this.options.label).appendTo(a.empty()).text(),
            g=this.options.icons,i=g.primary&&g.secondary;
            if(g.primary||g.secondary){
                a.addClass("ui-button-text-icon"+(i?"s":""));
                g.primary&&a.prepend("<span class='ui-button-icon-primary ui-icon "+g.primary+"'></span>");
                g.secondary&&a.append("<span class='ui-button-icon-secondary ui-icon "+g.secondary+"'></span>");
                if(!this.options.text){
                    a.addClass(i?"ui-button-icons-only":"ui-button-icon-only").removeClass("ui-button-text-icons ui-button-text-icon");
                    this.hasTitle||a.attr("title",e)
                    }
                }else a.addClass("ui-button-text-only")
            }
        }
});
b.widget("ui.buttonset",{
    _create:function(){
        this.element.addClass("ui-buttonset");
        this._init()
        },
    _init:function(){
        this.refresh()
        },
    _setOption:function(a,e){
        a==="disabled"&&this.buttons.button("option",a,e);
        b.Widget.prototype._setOption.apply(this,arguments)
        },
    refresh:function(){
        this.buttons=this.element.find(":button, :submit, :reset, :checkbox, :radio, a, :data(button)").filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function(){
            return b(this).button("widget")[0]
            }).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass("ui-corner-left").end().filter(":last").addClass("ui-corner-right").end().end()
        },
    destroy:function(){
        this.element.removeClass("ui-buttonset");
        this.buttons.map(function(){
            return b(this).button("widget")[0]
            }).removeClass("ui-corner-left ui-corner-right").end().button("destroy");
        b.Widget.prototype.destroy.call(this)
        }
    })
})(jQuery);
(function(b){
    b.widget("ui.dialog",{
        options:{
            autoOpen:true,
            buttons:{},
            closeOnEscape:true,
            closeText:"close",
            dialogClass:"",
            draggable:true,
            hide:null,
            height:"auto",
            maxHeight:false,
            maxWidth:false,
            minHeight:150,
            minWidth:150,
            modal:false,
            position:"center",
            resizable:true,
            show:null,
            stack:true,
            title:"",
            width:300,
            zIndex:1E3
        },
        _create:function(){
            this.originalTitle=this.element.attr("title");
            var c=this,f=c.options,d=f.title||c.originalTitle||"&#160;",a=b.ui.dialog.getTitleId(c.element),e=(c.uiDialog=b("<div></div>")).appendTo(document.body).hide().addClass("ui-dialog ui-widget ui-widget-content ui-corner-all "+
                f.dialogClass).css({
                zIndex:f.zIndex
                }).attr("tabIndex",-1).css("outline",0).keydown(function(k){
                if(f.closeOnEscape&&k.keyCode&&k.keyCode===b.ui.keyCode.ESCAPE){
                    c.close(k);
                    k.preventDefault()
                    }
                }).attr({
            role:"dialog",
            "aria-labelledby":a
        }).mousedown(function(k){
            c.moveToTop(false,k)
            });
        c.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(e);
        var g=(c.uiDialogTitlebar=b("<div></div>")).addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(e),
        i=b('<a href="#"></a>').addClass("ui-dialog-titlebar-close ui-corner-all").attr("role","button").hover(function(){
            i.addClass("ui-state-hover")
            },function(){
            i.removeClass("ui-state-hover")
            }).focus(function(){
            i.addClass("ui-state-focus")
            }).blur(function(){
            i.removeClass("ui-state-focus")
            }).click(function(k){
            c.close(k);
            return false
            }).appendTo(g);
        (c.uiDialogTitlebarCloseText=b("<span></span>")).addClass("ui-icon ui-icon-closethick").text(f.closeText).appendTo(i);
        b("<span></span>").addClass("ui-dialog-title").attr("id",
            a).html(d).prependTo(g);
        if(b.isFunction(f.beforeclose)&&!b.isFunction(f.beforeClose))f.beforeClose=f.beforeclose;
        g.find("*").add(g).disableSelection();
        f.draggable&&b.fn.draggable&&c._makeDraggable();
        f.resizable&&b.fn.resizable&&c._makeResizable();
        c._createButtons(f.buttons);
        c._isOpen=false;
        b.fn.bgiframe&&e.bgiframe()
        },
    _init:function(){
        this.options.autoOpen&&this.open()
        },
    destroy:function(){
        this.overlay&&this.overlay.destroy();
        this.uiDialog.hide();
        this.element.unbind(".dialog").removeData("dialog").removeClass("ui-dialog-content ui-widget-content").hide().appendTo("body");
        this.uiDialog.remove();
        this.originalTitle&&this.element.attr("title",this.originalTitle);
        return this
        },
    widget:function(){
        return this.uiDialog
        },
    close:function(c){
        var f=this,d;
        if(false!==f._trigger("beforeClose",c)){
            f.overlay&&f.overlay.destroy();
            f.uiDialog.unbind("keypress.ui-dialog");
            f._isOpen=false;
            if(f.options.hide)f.uiDialog.hide(f.options.hide,function(){
                f._trigger("close",c)
                });
            else{
                f.uiDialog.hide();
                f._trigger("close",c)
                }
                b.ui.dialog.overlay.resize();
            if(f.options.modal){
                d=0;
                b(".ui-dialog").each(function(){
                    if(this!==
                        f.uiDialog[0])d=Math.max(d,b(this).css("z-index"))
                        });
                b.ui.dialog.maxZ=d
                }
                return f
            }
        },
    isOpen:function(){
        return this._isOpen
        },
    moveToTop:function(c,f){
        var d=this.options;
        if(d.modal&&!c||!d.stack&&!d.modal)return this._trigger("focus",f);
        if(d.zIndex>b.ui.dialog.maxZ)b.ui.dialog.maxZ=d.zIndex;
        if(this.overlay){
            b.ui.dialog.maxZ+=1;
            this.overlay.$el.css("z-index",b.ui.dialog.overlay.maxZ=b.ui.dialog.maxZ)
            }
            d={
            scrollTop:this.element.attr("scrollTop"),
            scrollLeft:this.element.attr("scrollLeft")
            };
            
        b.ui.dialog.maxZ+=
        1;
        this.uiDialog.css("z-index",b.ui.dialog.maxZ);
        this.element.attr(d);
        this._trigger("focus",f);
        return this
        },
    open:function(){
        if(!this._isOpen){
            var c=this.options,f=this.uiDialog;
            this.overlay=c.modal?new b.ui.dialog.overlay(this):null;
            f.next().length&&f.appendTo("body");
            this._size();
            this._position(c.position);
            f.show(c.show);
            this.moveToTop(true);
            c.modal&&f.bind("keypress.ui-dialog",function(d){
                if(d.keyCode===b.ui.keyCode.TAB){
                    var a=b(":tabbable",this),e=a.filter(":first");
                    a=a.filter(":last");
                    if(d.target===
                        a[0]&&!d.shiftKey){
                        e.focus(1);
                        return false
                        }else if(d.target===e[0]&&d.shiftKey){
                        a.focus(1);
                        return false
                        }
                    }
            });
    b([]).add(f.find(".ui-dialog-content :tabbable:first")).add(f.find(".ui-dialog-buttonpane :tabbable:first")).add(f).filter(":first").focus();
    this._trigger("open");
    this._isOpen=true;
    return this
    }
},
_createButtons:function(c){
    var f=this,d=false,a=b("<div></div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix");
    f.uiDialog.find(".ui-dialog-buttonpane").remove();
    typeof c==="object"&&
    c!==null&&b.each(c,function(){
        return!(d=true)
        });
    if(d){
        b.each(c,function(e,g){
            var i=b('<button type="button"></button>').text(e).click(function(){
                g.apply(f.element[0],arguments)
                }).appendTo(a);
            b.fn.button&&i.button()
            });
        a.appendTo(f.uiDialog)
        }
    },
_makeDraggable:function(){
    function c(g){
        return{
            position:g.position,
            offset:g.offset
            }
        }
    var f=this,d=f.options,a=b(document),e;
f.uiDialog.draggable({
    cancel:".ui-dialog-content, .ui-dialog-titlebar-close",
    handle:".ui-dialog-titlebar",
    containment:"document",
    start:function(g,
        i){
        e=d.height==="auto"?"auto":b(this).height();
        b(this).height(b(this).height()).addClass("ui-dialog-dragging");
        f._trigger("dragStart",g,c(i))
        },
    drag:function(g,i){
        f._trigger("drag",g,c(i))
        },
    stop:function(g,i){
        d.position=[i.position.left-a.scrollLeft(),i.position.top-a.scrollTop()];
        b(this).removeClass("ui-dialog-dragging").height(e);
        f._trigger("dragStop",g,c(i));
        b.ui.dialog.overlay.resize()
        }
    })
},
_makeResizable:function(c){
    function f(g){
        return{
            originalPosition:g.originalPosition,
            originalSize:g.originalSize,
            position:g.position,
            size:g.size
            }
        }
    c=c===undefined?this.options.resizable:c;
var d=this,a=d.options,e=d.uiDialog.css("position");
c=typeof c==="string"?c:"n,e,s,w,se,sw,ne,nw";
d.uiDialog.resizable({
    cancel:".ui-dialog-content",
    containment:"document",
    alsoResize:d.element,
    maxWidth:a.maxWidth,
    maxHeight:a.maxHeight,
    minWidth:a.minWidth,
    minHeight:d._minHeight(),
    handles:c,
    start:function(g,i){
        b(this).addClass("ui-dialog-resizing");
        d._trigger("resizeStart",g,f(i))
        },
    resize:function(g,i){
        d._trigger("resize",g,f(i))
        },
    stop:function(g,i){
        b(this).removeClass("ui-dialog-resizing");
        a.height=b(this).height();
        a.width=b(this).width();
        d._trigger("resizeStop",g,f(i));
        b.ui.dialog.overlay.resize()
        }
    }).css("position",e).find(".ui-resizable-se").addClass("ui-icon ui-icon-grip-diagonal-se")
},
_minHeight:function(){
    var c=this.options;
    return c.height==="auto"?c.minHeight:Math.min(c.minHeight,c.height)
    },
_position:function(c){
    var f=[],d=[0,0];
    c=c||b.ui.dialog.prototype.options.position;
    if(typeof c==="string"||typeof c==="object"&&
        "0"in c){
        f=c.split?c.split(" "):[c[0],c[1]];
        if(f.length===1)f[1]=f[0];
        b.each(["left","top"],function(a,e){
            if(+f[a]===f[a]){
                d[a]=f[a];
                f[a]=e
                }
            })
    }else if(typeof c==="object"){
    if("left"in c){
        f[0]="left";
        d[0]=c.left
        }else if("right"in c){
        f[0]="right";
        d[0]=-c.right
        }
        if("top"in c){
        f[1]="top";
        d[1]=c.top
        }else if("bottom"in c){
        f[1]="bottom";
        d[1]=-c.bottom
        }
    }(c=this.uiDialog.is(":visible"))||this.uiDialog.show();
this.uiDialog.css({
    top:0,
    left:0
}).position({
    my:f.join(" "),
    at:f.join(" "),
    offset:d.join(" "),
    of:window,
    collision:"fit",
    using:function(a){
        var e=b(this).css(a).offset().top;
        e<0&&b(this).css("top",a.top-e)
        }
    });
c||this.uiDialog.hide()
},
_setOption:function(c,f){
    var d=this.uiDialog,a=d.is(":data(resizable)"),e=false;
    switch(c){
        case "beforeclose":
            c="beforeClose";
            break;
        case "buttons":
            this._createButtons(f);
            break;
        case "closeText":
            this.uiDialogTitlebarCloseText.text(""+f);
            break;
        case "dialogClass":
            d.removeClass(this.options.dialogClass).addClass("ui-dialog ui-widget ui-widget-content ui-corner-all "+f);
            break;
        case "disabled":
            f?
            d.addClass("ui-dialog-disabled"):d.removeClass("ui-dialog-disabled");
            break;
        case "draggable":
            f?this._makeDraggable():d.draggable("destroy");
            break;
        case "height":
            e=true;
            break;
        case "maxHeight":
            a&&d.resizable("option","maxHeight",f);
            e=true;
            break;
        case "maxWidth":
            a&&d.resizable("option","maxWidth",f);
            e=true;
            break;
        case "minHeight":
            a&&d.resizable("option","minHeight",f);
            e=true;
            break;
        case "minWidth":
            a&&d.resizable("option","minWidth",f);
            e=true;
            break;
        case "position":
            this._position(f);
            break;
        case "resizable":
            a&&
            !f&&d.resizable("destroy");
            a&&typeof f==="string"&&d.resizable("option","handles",f);
            !a&&f!==false&&this._makeResizable(f);
            break;
        case "title":
            b(".ui-dialog-title",this.uiDialogTitlebar).html(""+(f||"&#160;"));
            break;
        case "width":
            e=true
            }
            b.Widget.prototype._setOption.apply(this,arguments);
    e&&this._size()
    },
_size:function(){
    var c=this.options,f;
    this.element.css("width","auto").hide();
    f=this.uiDialog.css({
        height:"auto",
        width:c.width
        }).height();
    this.element.css(c.height==="auto"?{
        minHeight:Math.max(c.minHeight-
            f,0),
        height:"auto"
    }:{
        minHeight:0,
        height:Math.max(c.height-f,0)
        }).show();
    this.uiDialog.is(":data(resizable)")&&this.uiDialog.resizable("option","minHeight",this._minHeight())
    }
});
b.extend(b.ui.dialog,{
    version:"1.8",
    uuid:0,
    maxZ:0,
    getTitleId:function(c){
        c=c.attr("id");
        if(!c){
            this.uuid+=1;
            c=this.uuid
            }
            return"ui-dialog-title-"+c
        },
    overlay:function(c){
        this.$el=b.ui.dialog.overlay.create(c)
        }
    });
b.extend(b.ui.dialog.overlay,{
    instances:[],
    oldInstances:[],
    maxZ:0,
    events:b.map("focus,mousedown,mouseup,keydown,keypress,click".split(","),
        function(c){
            return c+".dialog-overlay"
            }).join(" "),
    create:function(c){
        if(this.instances.length===0){
            setTimeout(function(){
                b.ui.dialog.overlay.instances.length&&b(document).bind(b.ui.dialog.overlay.events,function(d){
                    return b(d.target).zIndex()>=b.ui.dialog.overlay.maxZ
                    })
                },1);
            b(document).bind("keydown.dialog-overlay",function(d){
                if(c.options.closeOnEscape&&d.keyCode&&d.keyCode===b.ui.keyCode.ESCAPE){
                    c.close(d);
                    d.preventDefault()
                    }
                });
        b(window).bind("resize.dialog-overlay",b.ui.dialog.overlay.resize)
        }
        var f=
    (this.oldInstances.pop()||b("<div></div>").addClass("ui-widget-overlay")).appendTo(document.body).css({
        width:this.width(),
        height:this.height()
        });
    b.fn.bgiframe&&f.bgiframe();
    this.instances.push(f);
    return f
    },
destroy:function(c){
    this.oldInstances.push(this.instances.splice(b.inArray(c,this.instances),1)[0]);
    this.instances.length===0&&b([document,window]).unbind(".dialog-overlay");
    c.remove();
    var f=0;
    b.each(this.instances,function(){
        f=Math.max(f,this.css("z-index"))
        });
    this.maxZ=f
    },
height:function(){
    var c,
    f;
    if(b.browser.msie&&b.browser.version<7){
        c=Math.max(document.documentElement.scrollHeight,document.body.scrollHeight);
        f=Math.max(document.documentElement.offsetHeight,document.body.offsetHeight);
        return c<f?b(window).height()+"px":c+"px"
        }else return b(document).height()+"px"
        },
width:function(){
    var c,f;
    if(b.browser.msie&&b.browser.version<7){
        c=Math.max(document.documentElement.scrollWidth,document.body.scrollWidth);
        f=Math.max(document.documentElement.offsetWidth,document.body.offsetWidth);
        return c<
        f?b(window).width()+"px":c+"px"
        }else return b(document).width()+"px"
        },
resize:function(){
    var c=b([]);
    b.each(b.ui.dialog.overlay.instances,function(){
        c=c.add(this)
        });
    c.css({
        width:0,
        height:0
    }).css({
        width:b.ui.dialog.overlay.width(),
        height:b.ui.dialog.overlay.height()
        })
    }
});
b.extend(b.ui.dialog.overlay.prototype,{
    destroy:function(){
        b.ui.dialog.overlay.destroy(this.$el)
        }
    })
})(jQuery);
(function(b){
    b.widget("ui.slider",b.ui.mouse,{
        widgetEventPrefix:"slide",
        options:{
            animate:false,
            distance:0,
            max:100,
            min:0,
            orientation:"horizontal",
            range:false,
            step:1,
            value:0,
            values:null
        },
        _create:function(){
            var c=this,f=this.options;
            this._mouseSliding=this._keySliding=false;
            this._animateOff=true;
            this._handleIndex=null;
            this._detectOrientation();
            this._mouseInit();
            this.element.addClass("ui-slider ui-slider-"+this.orientation+" ui-widget ui-widget-content ui-corner-all");
            f.disabled&&this.element.addClass("ui-slider-disabled ui-disabled");
            this.range=b([]);
            if(f.range){
                if(f.range===true){
                    this.range=b("<div></div>");
                    if(!f.values)f.values=[this._valueMin(),this._valueMin()];
                    if(f.values.length&&f.values.length!=2)f.values=[f.values[0],f.values[0]]
                        }else this.range=b("<div></div>");
                this.range.appendTo(this.element).addClass("ui-slider-range");
                if(f.range=="min"||f.range=="max")this.range.addClass("ui-slider-range-"+f.range);
                this.range.addClass("ui-widget-header")
                }
                b(".ui-slider-handle",this.element).length==0&&b('<a href="#"></a>').appendTo(this.element).addClass("ui-slider-handle");
            if(f.values&&f.values.length)for(;b(".ui-slider-handle",this.element).length<f.values.length;)b('<a href="#"></a>').appendTo(this.element).addClass("ui-slider-handle");
            this.handles=b(".ui-slider-handle",this.element).addClass("ui-state-default ui-corner-all");
            this.handle=this.handles.eq(0);
            this.handles.add(this.range).filter("a").click(function(d){
                d.preventDefault()
                }).hover(function(){
                f.disabled||b(this).addClass("ui-state-hover")
                },function(){
                b(this).removeClass("ui-state-hover")
                }).focus(function(){
                if(f.disabled)b(this).blur();
                else{
                    b(".ui-slider .ui-state-focus").removeClass("ui-state-focus");
                    b(this).addClass("ui-state-focus")
                    }
                }).blur(function(){
            b(this).removeClass("ui-state-focus")
            });
        this.handles.each(function(d){
            b(this).data("index.ui-slider-handle",d)
            });
        this.handles.keydown(function(d){
            var a=true,e=b(this).data("index.ui-slider-handle");
            if(!c.options.disabled){
                switch(d.keyCode){
                    case b.ui.keyCode.HOME:case b.ui.keyCode.END:case b.ui.keyCode.PAGE_UP:case b.ui.keyCode.PAGE_DOWN:case b.ui.keyCode.UP:case b.ui.keyCode.RIGHT:case b.ui.keyCode.DOWN:case b.ui.keyCode.LEFT:
                        a=
                        false;
                        if(!c._keySliding){
                            c._keySliding=true;
                            b(this).addClass("ui-state-active");
                            c._start(d,e)
                            }
                        }
                    var g,i,k=c._step();
            g=c.options.values&&c.options.values.length?i=c.values(e):i=c.value();
            switch(d.keyCode){
                case b.ui.keyCode.HOME:
                    i=c._valueMin();
                    break;
                case b.ui.keyCode.END:
                    i=c._valueMax();
                    break;
                case b.ui.keyCode.PAGE_UP:
                    i=g+(c._valueMax()-c._valueMin())/5;
                    break;
                case b.ui.keyCode.PAGE_DOWN:
                    i=g-(c._valueMax()-c._valueMin())/5;
                    break;
                case b.ui.keyCode.UP:case b.ui.keyCode.RIGHT:
                    if(g==c._valueMax())return;
                    i=g+k;
                    break;
                case b.ui.keyCode.DOWN:case b.ui.keyCode.LEFT:
                    if(g==c._valueMin())return;
                    i=g-k
                    }
                    c._slide(d,e,i);
            return a
            }
        }).keyup(function(d){
        var a=b(this).data("index.ui-slider-handle");
        if(c._keySliding){
            c._keySliding=false;
            c._stop(d,a);
            c._change(d,a);
            b(this).removeClass("ui-state-active")
            }
        });
this._refreshValue();
    this._animateOff=false
    },
destroy:function(){
    this.handles.remove();
    this.range.remove();
    this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-slider-disabled ui-widget ui-widget-content ui-corner-all").removeData("slider").unbind(".slider");
    this._mouseDestroy();
    return this
    },
_mouseCapture:function(c){
    var f=this.options;
    if(f.disabled)return false;
    this.elementSize={
        width:this.element.outerWidth(),
        height:this.element.outerHeight()
        };
        
    this.elementOffset=this.element.offset();
    var d={
        x:c.pageX,
        y:c.pageY
        },a=this._normValueFromMouse(d),e=this._valueMax()-this._valueMin()+1,g,i=this,k;
    this.handles.each(function(j){
        var h=Math.abs(a-i.values(j));
        if(e>h){
            e=h;
            g=b(this);
            k=j
            }
        });
if(f.range==true&&this.values(1)==f.min)g=b(this.handles[++k]);
    this._start(c,
    k);
this._mouseSliding=true;
i._handleIndex=k;
g.addClass("ui-state-active").focus();
    f=g.offset();
    this._clickOffset=!b(c.target).parents().andSelf().is(".ui-slider-handle")?{
    left:0,
    top:0
}:{
    left:c.pageX-f.left-g.width()/2,
    top:c.pageY-f.top-g.height()/2-(parseInt(g.css("borderTopWidth"),10)||0)-(parseInt(g.css("borderBottomWidth"),10)||0)+(parseInt(g.css("marginTop"),10)||0)
    };
    
a=this._normValueFromMouse(d);
    this._slide(c,k,a);
    return this._animateOff=true
    },
_mouseStart:function(){
    return true
    },
_mouseDrag:function(c){
    var f=
    this._normValueFromMouse({
        x:c.pageX,
        y:c.pageY
        });
    this._slide(c,this._handleIndex,f);
    return false
    },
_mouseStop:function(c){
    this.handles.removeClass("ui-state-active");
    this._mouseSliding=false;
    this._stop(c,this._handleIndex);
    this._change(c,this._handleIndex);
    this._clickOffset=this._handleIndex=null;
    return this._animateOff=false
    },
_detectOrientation:function(){
    this.orientation=this.options.orientation=="vertical"?"vertical":"horizontal"
    },
_normValueFromMouse:function(c){
    var f;
    if("horizontal"==this.orientation){
        f=
        this.elementSize.width;
        c=c.x-this.elementOffset.left-(this._clickOffset?this._clickOffset.left:0)
        }else{
        f=this.elementSize.height;
        c=c.y-this.elementOffset.top-(this._clickOffset?this._clickOffset.top:0)
        }
        f=c/f;
    if(f>1)f=1;
    if(f<0)f=0;
    if("vertical"==this.orientation)f=1-f;
    c=this._valueMax()-this._valueMin();
    c=f*c;
    f=c%this.options.step;
    c=this._valueMin()+c-f;
    if(f>this.options.step/2)c+=this.options.step;
    return parseFloat(c.toFixed(5))
    },
_start:function(c,f){
    var d={
        handle:this.handles[f],
        value:this.value()
        };
    if(this.options.values&&this.options.values.length){
        d.value=this.values(f);
        d.values=this.values()
        }
        this._trigger("start",c,d)
    },
_slide:function(c,f,d){
    if(this.options.values&&this.options.values.length){
        var a=this.values(f?0:1);
        if(this.options.values.length==2&&this.options.range===true&&(f==0&&d>a||f==1&&d<a))d=a;
        if(d!=this.values(f)){
            a=this.values();
            a[f]=d;
            c=this._trigger("slide",c,{
                handle:this.handles[f],
                value:d,
                values:a
            });
            this.values(f?0:1);
            c!==false&&this.values(f,d,true)
            }
        }else if(d!=this.value()){
    c=
    this._trigger("slide",c,{
        handle:this.handles[f],
        value:d
    });
    c!==false&&this.value(d)
    }
},
_stop:function(c,f){
    var d={
        handle:this.handles[f],
        value:this.value()
        };
        
    if(this.options.values&&this.options.values.length){
        d.value=this.values(f);
        d.values=this.values()
        }
        this._trigger("stop",c,d)
    },
_change:function(c,f){
    if(!this._keySliding&&!this._mouseSliding){
        var d={
            handle:this.handles[f],
            value:this.value()
            };
            
        if(this.options.values&&this.options.values.length){
            d.value=this.values(f);
            d.values=this.values()
            }
            this._trigger("change",
            c,d)
        }
    },
value:function(c){
    if(arguments.length){
        this.options.value=this._trimValue(c);
        this._refreshValue();
        this._change(null,0)
        }
        return this._value()
    },
values:function(c,f){
    if(arguments.length>1){
        this.options.values[c]=this._trimValue(f);
        this._refreshValue();
        this._change(null,c)
        }
        if(arguments.length)if(b.isArray(arguments[0])){
        for(var d=this.options.values,a=arguments[0],e=0,g=d.length;e<g;e++){
            d[e]=this._trimValue(a[e]);
            this._change(null,e)
            }
            this._refreshValue()
        }else return this.options.values&&this.options.values.length?
        this._values(c):this.value();else return this._values()
        },
_setOption:function(c,f){
    var d,a=0;
    if(jQuery.isArray(this.options.values))a=this.options.values.length;
    b.Widget.prototype._setOption.apply(this,arguments);
    switch(c){
        case "disabled":
            if(f){
            this.handles.filter(".ui-state-focus").blur();
            this.handles.removeClass("ui-state-hover");
            this.handles.attr("disabled","disabled");
            this.element.addClass("ui-disabled")
            }else{
            this.handles.removeAttr("disabled");
            this.element.removeClass("ui-disabled")
            }
            case "orientation":
            this._detectOrientation();
            this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-"+this.orientation);
            this._refreshValue();
            break;
        case "value":
            this._animateOff=true;
            this._refreshValue();
            this._change(null,0);
            this._animateOff=false;
            break;
        case "values":
            this._animateOff=true;
            this._refreshValue();
            for(d=0;d<a;d++)this._change(null,d);
            this._animateOff=false
            }
        },
_step:function(){
    return this.options.step
    },
_value:function(){
    var c=this.options.value;
    return c=this._trimValue(c)
    },
_values:function(c){
    if(arguments.length){
        var f=
        this.options.values[c];
        return f=this._trimValue(f)
        }else{
        f=this.options.values.slice();
        for(var d=0,a=f.length;d<a;d++)f[d]=this._trimValue(f[d]);
        return f
        }
    },
_trimValue:function(c){
    if(c<this._valueMin())c=this._valueMin();
    if(c>this._valueMax())c=this._valueMax();
    return c
    },
_valueMin:function(){
    return this.options.min
    },
_valueMax:function(){
    return this.options.max
    },
_refreshValue:function(){
    var c=this.options.range,f=this.options,d=this,a=!this._animateOff?f.animate:false;
    if(this.options.values&&this.options.values.length)this.handles.each(function(k){
        var j=
        (d.values(k)-d._valueMin())/(d._valueMax()-d._valueMin())*100,h={};
        
        h[d.orientation=="horizontal"?"left":"bottom"]=j+"%";
        b(this).stop(1,1)[a?"animate":"css"](h,f.animate);
        if(d.options.range===true)if(d.orientation=="horizontal"){
            k==0&&d.range.stop(1,1)[a?"animate":"css"]({
                left:j+"%"
                },f.animate);
            k==1&&d.range[a?"animate":"css"]({
                width:j-lastValPercent+"%"
                },{
                queue:false,
                duration:f.animate
                })
            }else{
            k==0&&d.range.stop(1,1)[a?"animate":"css"]({
                bottom:j+"%"
                },f.animate);
            k==1&&d.range[a?"animate":"css"]({
                height:j-
                lastValPercent+"%"
                },{
                queue:false,
                duration:f.animate
                })
            }
            lastValPercent=j
        });
    else{
        var e=this.value(),g=this._valueMin(),i=this._valueMax();
        e=i!=g?(e-g)/(i-g)*100:0;
        g={};
        
        g[d.orientation=="horizontal"?"left":"bottom"]=e+"%";
        this.handle.stop(1,1)[a?"animate":"css"](g,f.animate);
        c=="min"&&this.orientation=="horizontal"&&this.range.stop(1,1)[a?"animate":"css"]({
            width:e+"%"
            },f.animate);
        c=="max"&&this.orientation=="horizontal"&&this.range[a?"animate":"css"]({
            width:100-e+"%"
            },{
            queue:false,
            duration:f.animate
            });
        c=="min"&&this.orientation=="vertical"&&this.range.stop(1,1)[a?"animate":"css"]({
            height:e+"%"
            },f.animate);
        c=="max"&&this.orientation=="vertical"&&this.range[a?"animate":"css"]({
            height:100-e+"%"
            },{
            queue:false,
            duration:f.animate
            })
        }
    }
});
b.extend(b.ui.slider,{
    version:"1.8"
})
})(jQuery);
(function(b){
    var c=0,f=0;
    b.widget("ui.tabs",{
        options:{
            add:null,
            ajaxOptions:null,
            cache:false,
            cookie:null,
            collapsible:false,
            disable:null,
            disabled:[],
            enable:null,
            event:"click",
            fx:null,
            idPrefix:"ui-tabs-",
            load:null,
            panelTemplate:"<div></div>",
            remove:null,
            select:null,
            show:null,
            spinner:"<em>Loading&#8230;</em>",
            tabTemplate:'<li><a href="#{href}"><span>#{label}</span></a></li>'
        },
        _create:function(){
            this._tabify(true)
            },
        _setOption:function(d,a){
            if(d=="selected")this.options.collapsible&&a==this.options.selected||
                this.select(a);
            else{
                this.options[d]=a;
                this._tabify()
                }
            },
    _tabId:function(d){
        return d.title&&d.title.replace(/\s/g,"_").replace(/[^A-Za-z0-9\-_:\.]/g,"")||this.options.idPrefix+ ++c
        },
    _sanitizeSelector:function(d){
        return d.replace(/:/g,"\\:")
        },
    _cookie:function(){
        var d=this.cookie||(this.cookie=this.options.cookie.name||"ui-tabs-"+ ++f);
        return b.cookie.apply(null,[d].concat(b.makeArray(arguments)))
        },
    _ui:function(d,a){
        return{
            tab:d,
            panel:a,
            index:this.anchors.index(d)
            }
        },
    _cleanup:function(){
        this.lis.filter(".ui-state-processing").removeClass("ui-state-processing").find("span:data(label.tabs)").each(function(){
            var d=
            b(this);
            d.html(d.data("label.tabs")).removeData("label.tabs")
            })
        },
    _tabify:function(d){
        function a(o,p){
            o.css({
                display:""
            });
            !b.support.opacity&&p.opacity&&o[0].style.removeAttribute("filter")
            }
            this.list=this.element.find("ol,ul").eq(0);
        this.lis=b("li:has(a[href])",this.list);
        this.anchors=this.lis.map(function(){
            return b("a",this)[0]
            });
        this.panels=b([]);
        var e=this,g=this.options,i=/^#.+/;
        this.anchors.each(function(o,p){
            var q=b(p).attr("href"),r=q.split("#")[0],s;
            if(r&&(r===location.toString().split("#")[0]||
                (s=b("base")[0])&&r===s.href)){
                q=p.hash;
                p.href=q
                }
                if(i.test(q))e.panels=e.panels.add(e._sanitizeSelector(q));
            else if(q!="#"){
                b.data(p,"href.tabs",q);
                b.data(p,"load.tabs",q.replace(/#.*$/,""));
                q=e._tabId(p);
                p.href="#"+q;
                r=b("#"+q);
                if(!r.length){
                    r=b(g.panelTemplate).attr("id",q).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").insertAfter(e.panels[o-1]||e.list);
                    r.data("destroy.tabs",true)
                    }
                    e.panels=e.panels.add(r)
                }else g.disabled.push(o)
                });
        if(d){
            this.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all");
            this.list.addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all");
            this.lis.addClass("ui-state-default ui-corner-top");
            this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom");
            if(g.selected===undefined){
                location.hash&&this.anchors.each(function(o,p){
                    if(p.hash==location.hash){
                        g.selected=o;
                        return false
                        }
                    });
            if(typeof g.selected!="number"&&g.cookie)g.selected=parseInt(e._cookie(),10);
            if(typeof g.selected!="number"&&this.lis.filter(".ui-tabs-selected").length)g.selected=
                this.lis.index(this.lis.filter(".ui-tabs-selected"));
            g.selected=g.selected||(this.lis.length?0:-1)
            }else if(g.selected===null)g.selected=-1;
        g.selected=g.selected>=0&&this.anchors[g.selected]||g.selected<0?g.selected:0;
        g.disabled=b.unique(g.disabled.concat(b.map(this.lis.filter(".ui-state-disabled"),function(o){
            return e.lis.index(o)
            }))).sort();
        b.inArray(g.selected,g.disabled)!=-1&&g.disabled.splice(b.inArray(g.selected,g.disabled),1);
        this.panels.addClass("ui-tabs-hide");
        this.lis.removeClass("ui-tabs-selected ui-state-active");
        if(g.selected>=0&&this.anchors.length){
            this.panels.eq(g.selected).removeClass("ui-tabs-hide");
            this.lis.eq(g.selected).addClass("ui-tabs-selected ui-state-active");
            e.element.queue("tabs",function(){
                e._trigger("show",null,e._ui(e.anchors[g.selected],e.panels[g.selected]))
                });
            this.load(g.selected)
            }
            b(window).bind("unload",function(){
            e.lis.add(e.anchors).unbind(".tabs");
            e.lis=e.anchors=e.panels=null
            })
        }else g.selected=this.lis.index(this.lis.filter(".ui-tabs-selected"));
    this.element[g.collapsible?"addClass":
    "removeClass"]("ui-tabs-collapsible");
    g.cookie&&this._cookie(g.selected,g.cookie);
    d=0;
    for(var k;k=this.lis[d];d++)b(k)[b.inArray(d,g.disabled)!=-1&&!b(k).hasClass("ui-tabs-selected")?"addClass":"removeClass"]("ui-state-disabled");
    g.cache===false&&this.anchors.removeData("cache.tabs");
    this.lis.add(this.anchors).unbind(".tabs");
    if(g.event!="mouseover"){
        var j=function(o,p){
            p.is(":not(.ui-state-disabled)")&&p.addClass("ui-state-"+o)
            };
            
        this.lis.bind("mouseover.tabs",function(){
            j("hover",b(this))
            });
        this.lis.bind("mouseout.tabs",
            function(){
                b(this).removeClass("ui-state-hover")
                });
        this.anchors.bind("focus.tabs",function(){
            j("focus",b(this).closest("li"))
            });
        this.anchors.bind("blur.tabs",function(){
            b(this).closest("li").removeClass("ui-state-focus")
            })
        }
        var h,l;
    if(g.fx)if(b.isArray(g.fx)){
        h=g.fx[0];
        l=g.fx[1]
        }else h=l=g.fx;
    var m=l?function(o,p){
        b(o).closest("li").addClass("ui-tabs-selected ui-state-active");
        p.hide().removeClass("ui-tabs-hide").animate(l,l.duration||"normal",function(){
            a(p,l);
            e._trigger("show",null,e._ui(o,p[0]))
            })
        }:
    function(o,p){
        b(o).closest("li").addClass("ui-tabs-selected ui-state-active");
        p.removeClass("ui-tabs-hide");
        e._trigger("show",null,e._ui(o,p[0]))
        },n=h?function(o,p){
        p.animate(h,h.duration||"normal",function(){
            e.lis.removeClass("ui-tabs-selected ui-state-active");
            p.addClass("ui-tabs-hide");
            a(p,h);
            e.element.dequeue("tabs")
            })
        }:function(o,p){
        e.lis.removeClass("ui-tabs-selected ui-state-active");
        p.addClass("ui-tabs-hide");
        e.element.dequeue("tabs")
        };
        
    this.anchors.bind(g.event+".tabs",function(){
        var o=this,
        p=b(this).closest("li"),q=e.panels.filter(":not(.ui-tabs-hide)"),r=b(e._sanitizeSelector(this.hash));
        if(p.hasClass("ui-tabs-selected")&&!g.collapsible||p.hasClass("ui-state-disabled")||p.hasClass("ui-state-processing")||e._trigger("select",null,e._ui(this,r[0]))===false){
            this.blur();
            return false
            }
            g.selected=e.anchors.index(this);
        e.abort();
        if(g.collapsible)if(p.hasClass("ui-tabs-selected")){
            g.selected=-1;
            g.cookie&&e._cookie(g.selected,g.cookie);
            e.element.queue("tabs",function(){
                n(o,q)
                }).dequeue("tabs");
            this.blur();
            return false
            }else if(!q.length){
            g.cookie&&e._cookie(g.selected,g.cookie);
            e.element.queue("tabs",function(){
                m(o,r)
                });
            e.load(e.anchors.index(this));
            this.blur();
            return false
            }
            g.cookie&&e._cookie(g.selected,g.cookie);
        if(r.length){
            q.length&&e.element.queue("tabs",function(){
                n(o,q)
                });
            e.element.queue("tabs",function(){
                m(o,r)
                });
            e.load(e.anchors.index(this))
            }else throw"jQuery UI Tabs: Mismatching fragment identifier.";
        b.browser.msie&&this.blur()
        });
    this.anchors.bind("click.tabs",function(){
        return false
        })
    },
destroy:function(){
    var d=this.options;
    this.abort();
    this.element.unbind(".tabs").removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible").removeData("tabs");
    this.list.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all");
    this.anchors.each(function(){
        var a=b.data(this,"href.tabs");
        if(a)this.href=a;
        var e=b(this).unbind(".tabs");
        b.each(["href","load","cache"],function(g,i){
            e.removeData(i+".tabs")
            })
        });
    this.lis.unbind(".tabs").add(this.panels).each(function(){
        b.data(this,
            "destroy.tabs")?b(this).remove():b(this).removeClass("ui-state-default ui-corner-top ui-tabs-selected ui-state-active ui-state-hover ui-state-focus ui-state-disabled ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide")
        });
    d.cookie&&this._cookie(null,d.cookie);
    return this
    },
add:function(d,a,e){
    if(e===undefined)e=this.anchors.length;
    var g=this,i=this.options;
    a=b(i.tabTemplate.replace(/#\{href\}/g,d).replace(/#\{label\}/g,a));
    d=!d.indexOf("#")?d.replace("#",""):this._tabId(b("a",a)[0]);
    a.addClass("ui-state-default ui-corner-top").data("destroy.tabs",
        true);
    var k=b("#"+d);
    k.length||(k=b(i.panelTemplate).attr("id",d).data("destroy.tabs",true));
    k.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide");
    if(e>=this.lis.length){
        a.appendTo(this.list);
        k.appendTo(this.list[0].parentNode)
        }else{
        a.insertBefore(this.lis[e]);
        k.insertBefore(this.panels[e])
        }
        i.disabled=b.map(i.disabled,function(j){
        return j>=e?++j:j
        });
    this._tabify();
    if(this.anchors.length==1){
        i.selected=0;
        a.addClass("ui-tabs-selected ui-state-active");
        k.removeClass("ui-tabs-hide");
        this.element.queue("tabs",function(){
            g._trigger("show",null,g._ui(g.anchors[0],g.panels[0]))
            });
        this.load(0)
        }
        this._trigger("add",null,this._ui(this.anchors[e],this.panels[e]));
    return this
    },
remove:function(d){
    var a=this.options,e=this.lis.eq(d).remove(),g=this.panels.eq(d).remove();
    if(e.hasClass("ui-tabs-selected")&&this.anchors.length>1)this.select(d+(d+1<this.anchors.length?1:-1));
    a.disabled=b.map(b.grep(a.disabled,function(i){
        return i!=d
        }),function(i){
        return i>=d?--i:i
        });
    this._tabify();
    this._trigger("remove",
        null,this._ui(e.find("a")[0],g[0]));
    return this
    },
enable:function(d){
    var a=this.options;
    if(b.inArray(d,a.disabled)!=-1){
        this.lis.eq(d).removeClass("ui-state-disabled");
        a.disabled=b.grep(a.disabled,function(e){
            return e!=d
            });
        this._trigger("enable",null,this._ui(this.anchors[d],this.panels[d]));
        return this
        }
    },
disable:function(d){
    var a=this.options;
    if(d!=a.selected){
        this.lis.eq(d).addClass("ui-state-disabled");
        a.disabled.push(d);
        a.disabled.sort();
        this._trigger("disable",null,this._ui(this.anchors[d],this.panels[d]))
        }
        return this
    },
select:function(d){
    if(typeof d=="string")d=this.anchors.index(this.anchors.filter("[href$="+d+"]"));
    else if(d===null)d=-1;
    if(d==-1&&this.options.collapsible)d=this.options.selected;
    this.anchors.eq(d).trigger(this.options.event+".tabs");
    return this
    },
load:function(d){
    var a=this,e=this.options,g=this.anchors.eq(d)[0],i=b.data(g,"load.tabs");
    this.abort();
    if(!i||this.element.queue("tabs").length!==0&&b.data(g,"cache.tabs"))this.element.dequeue("tabs");
    else{
        this.lis.eq(d).addClass("ui-state-processing");
        if(e.spinner){
            var k=b("span",g);
            k.data("label.tabs",k.html()).html(e.spinner)
            }
            this.xhr=b.ajax(b.extend({},e.ajaxOptions,{
            url:i,
            success:function(j,h){
                b(a._sanitizeSelector(g.hash)).html(j);
                a._cleanup();
                e.cache&&b.data(g,"cache.tabs",true);
                a._trigger("load",null,a._ui(a.anchors[d],a.panels[d]));
                try{
                    e.ajaxOptions.success(j,h)
                    }catch(l){}
            },
        error:function(j,h){
            a._cleanup();
            a._trigger("load",null,a._ui(a.anchors[d],a.panels[d]));
            try{
                e.ajaxOptions.error(j,h,d,g)
                }catch(l){}
        }
        }));
a.element.dequeue("tabs");
return this
}
},
abort:function(){
    this.element.queue([]);
    this.panels.stop(false,true);
    this.element.queue("tabs",this.element.queue("tabs").splice(-2,2));
    if(this.xhr){
        this.xhr.abort();
        delete this.xhr
        }
        this._cleanup();
    return this
    },
url:function(d,a){
    this.anchors.eq(d).removeData("cache.tabs").data("load.tabs",a);
    return this
    },
length:function(){
    return this.anchors.length
    }
});
b.extend(b.ui.tabs,{
    version:"1.8"
});
b.extend(b.ui.tabs.prototype,{
    rotation:null,
    rotate:function(d,a){
        var e=this,g=this.options,i=e._rotate||(e._rotate=
            function(j){
                clearTimeout(e.rotation);
                e.rotation=setTimeout(function(){
                    var h=g.selected;
                    e.select(++h<e.anchors.length?h:0)
                    },d);
                j&&j.stopPropagation()
                }),k=e._unrotate||(e._unrotate=!a?function(j){
            j.clientX&&e.rotate(null)
            }:function(){
            t=g.selected;
            i()
            });
        if(d){
            this.element.bind("tabsshow",i);
            this.anchors.bind(g.event+".tabs",k);
            i()
            }else{
            clearTimeout(e.rotation);
            this.element.unbind("tabsshow",i);
            this.anchors.unbind(g.event+".tabs",k);
            delete this._rotate;
            delete this._unrotate
            }
            return this
        }
    })
})(jQuery);
(function(b){
    function c(){
        this.debug=false;
        this._curInst=null;
        this._keyEvent=false;
        this._disabledInputs=[];
        this._inDialog=this._datepickerShowing=false;
        this._mainDivId="ui-datepicker-div";
        this._inlineClass="ui-datepicker-inline";
        this._appendClass="ui-datepicker-append";
        this._triggerClass="ui-datepicker-trigger";
        this._dialogClass="ui-datepicker-dialog";
        this._disableClass="ui-datepicker-disabled";
        this._unselectableClass="ui-datepicker-unselectable";
        this._currentClass="ui-datepicker-current-day";
        this._dayOverClass=
        "ui-datepicker-days-cell-over";
        this.regional=[];
        this.regional[""]={
            closeText:"Done",
            prevText:"Prev",
            nextText:"Next",
            currentText:"Today",
            monthNames:["January","February","March","April","May","June","July","August","September","October","November","December"],
            monthNamesShort:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
            dayNames:["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
            dayNamesShort:["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
            dayNamesMin:["Su",
            "Mo","Tu","We","Th","Fr","Sa"],
            weekHeader:"Wk",
            dateFormat:"mm/dd/yy",
            firstDay:0,
            isRTL:false,
            showMonthAfterYear:false,
            yearSuffix:""
        };
        
        this._defaults={
            showOn:"focus",
            showAnim:"show",
            showOptions:{},
            defaultDate:null,
            appendText:"",
            buttonText:"...",
            buttonImage:"",
            buttonImageOnly:false,
            hideIfNoPrevNext:false,
            navigationAsDateFormat:false,
            gotoCurrent:false,
            changeMonth:false,
            changeYear:false,
            yearRange:"c-10:c+10",
            showOtherMonths:false,
            selectOtherMonths:false,
            showWeek:false,
            calculateWeek:this.iso8601Week,
            shortYearCutoff:"+10",
            minDate:null,
            maxDate:null,
            duration:"_default",
            beforeShowDay:null,
            beforeShow:null,
            onSelect:null,
            onChangeMonthYear:null,
            onClose:null,
            numberOfMonths:1,
            showCurrentAtPos:0,
            stepMonths:1,
            stepBigMonths:12,
            altField:"",
            altFormat:"",
            constrainInput:true,
            showButtonPanel:false,
            autoSize:false
        };
        
        b.extend(this._defaults,this.regional[""]);
        this.dpDiv=b('<div id="'+this._mainDivId+'" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ui-helper-hidden-accessible"></div>')
        }
        function f(a,e){
        b.extend(a,
            e);
        for(var g in e)if(e[g]==null||e[g]==undefined)a[g]=e[g];return a
        }
        b.extend(b.ui,{
        datepicker:{
            version:"1.8"
        }
    });
var d=(new Date).getTime();
    b.extend(c.prototype,{
    markerClassName:"hasDatepicker",
    log:function(){
        this.debug&&console.log.apply("",arguments)
        },
    _widgetDatepicker:function(){
        return this.dpDiv
        },
    setDefaults:function(a){
        f(this._defaults,a||{});
        return this
        },
    _attachDatepicker:function(a,e){
        var g=null,i;
        for(i in this._defaults){
            var k=a.getAttribute("date:"+i);
            if(k){
                g=g||{};
                
                try{
                    g[i]=eval(k)
                    }catch(j){
                    g[i]=
                    k
                    }
                }
        }
            i=a.nodeName.toLowerCase();
    k=i=="div"||i=="span";
    if(!a.id)a.id="dp"+ ++this.uuid;
    var h=this._newInst(b(a),k);
    h.settings=b.extend({},e||{},g||{});
    if(i=="input")this._connectDatepicker(a,h);else k&&this._inlineDatepicker(a,h)
    },
_newInst:function(a,e){
    return{
        id:a[0].id.replace(/([^A-Za-z0-9_])/g,"\\\\$1"),
        input:a,
        selectedDay:0,
        selectedMonth:0,
        selectedYear:0,
        drawMonth:0,
        drawYear:0,
        inline:e,
        dpDiv:!e?this.dpDiv:b('<div class="'+this._inlineClass+' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>')
        }
    },
_connectDatepicker:function(a,e){
    var g=b(a);
    e.append=b([]);
    e.trigger=b([]);
    if(!g.hasClass(this.markerClassName)){
        this._attachments(g,e);
        g.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker",function(i,k,j){
            e.settings[k]=j
            }).bind("getData.datepicker",function(i,k){
            return this._get(e,k)
            });
        this._autoSize(e);
        b.data(a,"datepicker",e)
        }
    },
_attachments:function(a,e){
    var g=this._get(e,"appendText"),i=this._get(e,"isRTL");
    e.append&&
    e.append.remove();
    if(g){
        e.append=b('<span class="'+this._appendClass+'">'+g+"</span>");
        a[i?"before":"after"](e.append)
        }
        a.unbind("focus",this._showDatepicker);
    e.trigger&&e.trigger.remove();
    g=this._get(e,"showOn");
    if(g=="focus"||g=="both")a.focus(this._showDatepicker);
    if(g=="button"||g=="both"){
        g=this._get(e,"buttonText");
        var k=this._get(e,"buttonImage");
        e.trigger=b(this._get(e,"buttonImageOnly")?b("<img/>").addClass(this._triggerClass).attr({
            src:k,
            alt:g,
            title:g
        }):b('<button type="button"></button>').addClass(this._triggerClass).html(k==
            ""?g:b("<img/>").attr({
                src:k,
                alt:g,
                title:g
            })));
        a[i?"before":"after"](e.trigger);
        e.trigger.click(function(){
            b.datepicker._datepickerShowing&&b.datepicker._lastInput==a[0]?b.datepicker._hideDatepicker():b.datepicker._showDatepicker(a[0]);
            return false
            })
        }
    },
_autoSize:function(a){
    if(this._get(a,"autoSize")&&!a.inline){
        var e=new Date(2009,11,20),g=this._get(a,"dateFormat");
        if(g.match(/[DM]/)){
            var i=function(k){
                for(var j=0,h=0,l=0;l<k.length;l++)if(k[l].length>j){
                    j=k[l].length;
                    h=l
                    }
                    return h
                };
                
            e.setMonth(i(this._get(a,
                g.match(/MM/)?"monthNames":"monthNamesShort")));
            e.setDate(i(this._get(a,g.match(/DD/)?"dayNames":"dayNamesShort"))+20-e.getDay())
            }
            a.input.attr("size",this._formatDate(a,e).length)
        }
    },
_inlineDatepicker:function(a,e){
    var g=b(a);
    if(!g.hasClass(this.markerClassName)){
        g.addClass(this.markerClassName).append(e.dpDiv).bind("setData.datepicker",function(i,k,j){
            e.settings[k]=j
            }).bind("getData.datepicker",function(i,k){
            return this._get(e,k)
            });
        b.data(a,"datepicker",e);
        this._setDate(e,this._getDefaultDate(e),
            true);
        this._updateDatepicker(e);
        this._updateAlternate(e)
        }
    },
_dialogDatepicker:function(a,e,g,i,k){
    a=this._dialogInst;
    if(!a){
        a="dp"+ ++this.uuid;
        this._dialogInput=b('<input type="text" id="'+a+'" style="position: absolute; top: -100px; width: 0px; z-index: -10;"/>');
        this._dialogInput.keydown(this._doKeyDown);
        b("body").append(this._dialogInput);
        a=this._dialogInst=this._newInst(this._dialogInput,false);
        a.settings={};
        
        b.data(this._dialogInput[0],"datepicker",a)
        }
        f(a.settings,i||{});
    e=e&&e.constructor==Date?
    this._formatDate(a,e):e;
    this._dialogInput.val(e);
    this._pos=k?k.length?k:[k.pageX,k.pageY]:null;
    if(!this._pos)this._pos=[document.documentElement.clientWidth/2-100+(document.documentElement.scrollLeft||document.body.scrollLeft),document.documentElement.clientHeight/2-150+(document.documentElement.scrollTop||document.body.scrollTop)];
    this._dialogInput.css("left",this._pos[0]+20+"px").css("top",this._pos[1]+"px");
    a.settings.onSelect=g;
    this._inDialog=true;
    this.dpDiv.addClass(this._dialogClass);
    this._showDatepicker(this._dialogInput[0]);
    b.blockUI&&b.blockUI(this.dpDiv);
    b.data(this._dialogInput[0],"datepicker",a);
    return this
    },
_destroyDatepicker:function(a){
    var e=b(a),g=b.data(a,"datepicker");
    if(e.hasClass(this.markerClassName)){
        var i=a.nodeName.toLowerCase();
        b.removeData(a,"datepicker");
        if(i=="input"){
            g.append.remove();
            g.trigger.remove();
            e.removeClass(this.markerClassName).unbind("focus",this._showDatepicker).unbind("keydown",this._doKeyDown).unbind("keypress",this._doKeyPress).unbind("keyup",this._doKeyUp)
            }else if(i=="div"||i=="span")e.removeClass(this.markerClassName).empty()
            }
        },
_enableDatepicker:function(a){
    var e=b(a),g=b.data(a,"datepicker");
    if(e.hasClass(this.markerClassName)){
        var i=a.nodeName.toLowerCase();
        if(i=="input"){
            a.disabled=false;
            g.trigger.filter("button").each(function(){
                this.disabled=false
                }).end().filter("img").css({
                opacity:"1.0",
                cursor:""
            })
            }else if(i=="div"||i=="span")e.children("."+this._inlineClass).children().removeClass("ui-state-disabled");
        this._disabledInputs=b.map(this._disabledInputs,function(k){
            return k==a?null:k
            })
        }
    },
_disableDatepicker:function(a){
    var e=
    b(a),g=b.data(a,"datepicker");
    if(e.hasClass(this.markerClassName)){
        var i=a.nodeName.toLowerCase();
        if(i=="input"){
            a.disabled=true;
            g.trigger.filter("button").each(function(){
                this.disabled=true
                }).end().filter("img").css({
                opacity:"0.5",
                cursor:"default"
            })
            }else if(i=="div"||i=="span")e.children("."+this._inlineClass).children().addClass("ui-state-disabled");
        this._disabledInputs=b.map(this._disabledInputs,function(k){
            return k==a?null:k
            });
        this._disabledInputs[this._disabledInputs.length]=a
        }
    },
_isDisabledDatepicker:function(a){
    if(!a)return false;
    for(var e=0;e<this._disabledInputs.length;e++)if(this._disabledInputs[e]==a)return true;return false
    },
_getInst:function(a){
    try{
        return b.data(a,"datepicker")
        }catch(e){
        throw"Missing instance data for this datepicker";
    }
},
_optionDatepicker:function(a,e,g){
    var i=this._getInst(a);
    if(arguments.length==2&&typeof e=="string")return e=="defaults"?b.extend({},b.datepicker._defaults):i?e=="all"?b.extend({},i.settings):this._get(i,e):null;
    var k=e||{};
    
    if(typeof e=="string"){
        k={};
        
        k[e]=g
        }
        if(i){
        this._curInst==i&&
        this._hideDatepicker();
        var j=this._getDateDatepicker(a,true);
        f(i.settings,k);
        this._attachments(b(a),i);
        this._autoSize(i);
        this._setDateDatepicker(a,j);
        this._updateDatepicker(i)
        }
    },
_changeDatepicker:function(a,e,g){
    this._optionDatepicker(a,e,g)
    },
_refreshDatepicker:function(a){
    (a=this._getInst(a))&&this._updateDatepicker(a)
    },
_setDateDatepicker:function(a,e){
    var g=this._getInst(a);
    if(g){
        this._setDate(g,e);
        this._updateDatepicker(g);
        this._updateAlternate(g)
        }
    },
_getDateDatepicker:function(a,e){
    var g=this._getInst(a);
    g&&!g.inline&&this._setDateFromField(g,e);
    return g?this._getDate(g):null
    },
_doKeyDown:function(a){
    var e=b.datepicker._getInst(a.target),g=true,i=e.dpDiv.is(".ui-datepicker-rtl");
    e._keyEvent=true;
    if(b.datepicker._datepickerShowing)switch(a.keyCode){
        case 9:
            b.datepicker._hideDatepicker();
            g=false;
            break;
        case 13:
            g=b("td."+b.datepicker._dayOverClass,e.dpDiv).add(b("td."+b.datepicker._currentClass,e.dpDiv));
            g[0]?b.datepicker._selectDay(a.target,e.selectedMonth,e.selectedYear,g[0]):b.datepicker._hideDatepicker();
            return false;
        case 27:
            b.datepicker._hideDatepicker();
            break;
        case 33:
            b.datepicker._adjustDate(a.target,a.ctrlKey?-b.datepicker._get(e,"stepBigMonths"):-b.datepicker._get(e,"stepMonths"),"M");
            break;
        case 34:
            b.datepicker._adjustDate(a.target,a.ctrlKey?+b.datepicker._get(e,"stepBigMonths"):+b.datepicker._get(e,"stepMonths"),"M");
            break;
        case 35:
            if(a.ctrlKey||a.metaKey)b.datepicker._clearDate(a.target);
            g=a.ctrlKey||a.metaKey;
            break;
        case 36:
            if(a.ctrlKey||a.metaKey)b.datepicker._gotoToday(a.target);
            g=a.ctrlKey||
            a.metaKey;
            break;
        case 37:
            if(a.ctrlKey||a.metaKey)b.datepicker._adjustDate(a.target,i?+1:-1,"D");
            g=a.ctrlKey||a.metaKey;
            if(a.originalEvent.altKey)b.datepicker._adjustDate(a.target,a.ctrlKey?-b.datepicker._get(e,"stepBigMonths"):-b.datepicker._get(e,"stepMonths"),"M");
            break;
        case 38:
            if(a.ctrlKey||a.metaKey)b.datepicker._adjustDate(a.target,-7,"D");
            g=a.ctrlKey||a.metaKey;
            break;
        case 39:
            if(a.ctrlKey||a.metaKey)b.datepicker._adjustDate(a.target,i?-1:+1,"D");
            g=a.ctrlKey||a.metaKey;
            if(a.originalEvent.altKey)b.datepicker._adjustDate(a.target,
            a.ctrlKey?+b.datepicker._get(e,"stepBigMonths"):+b.datepicker._get(e,"stepMonths"),"M");
        break;
        case 40:
            if(a.ctrlKey||a.metaKey)b.datepicker._adjustDate(a.target,+7,"D");
            g=a.ctrlKey||a.metaKey;
            break;
        default:
            g=false
            }else if(a.keyCode==36&&a.ctrlKey)b.datepicker._showDatepicker(this);else g=false;
    if(g){
        a.preventDefault();
        a.stopPropagation()
        }
    },
_doKeyPress:function(a){
    var e=b.datepicker._getInst(a.target);
    if(b.datepicker._get(e,"constrainInput")){
        e=b.datepicker._possibleChars(b.datepicker._get(e,"dateFormat"));
        var g=String.fromCharCode(a.charCode==undefined?a.keyCode:a.charCode);
        return a.ctrlKey||g<" "||!e||e.indexOf(g)>-1
        }
    },
_doKeyUp:function(a){
    a=b.datepicker._getInst(a.target);
    if(a.input.val()!=a.lastVal)try{
        if(b.datepicker.parseDate(b.datepicker._get(a,"dateFormat"),a.input?a.input.val():null,b.datepicker._getFormatConfig(a))){
            b.datepicker._setDateFromField(a);
            b.datepicker._updateAlternate(a);
            b.datepicker._updateDatepicker(a)
            }
        }catch(e){
        b.datepicker.log(e)
        }
        return true
},
_showDatepicker:function(a){
    a=a.target||
    a;
    if(a.nodeName.toLowerCase()!="input")a=b("input",a.parentNode)[0];
    if(!(b.datepicker._isDisabledDatepicker(a)||b.datepicker._lastInput==a)){
        var e=b.datepicker._getInst(a);
        b.datepicker._curInst&&b.datepicker._curInst!=e&&b.datepicker._curInst.dpDiv.stop(true,true);
        var g=b.datepicker._get(e,"beforeShow");
        f(e.settings,g?g.apply(a,[a,e]):{});
        e.lastVal=null;
        b.datepicker._lastInput=a;
        b.datepicker._setDateFromField(e);
        if(b.datepicker._inDialog)a.value="";
        if(!b.datepicker._pos){
            b.datepicker._pos=b.datepicker._findPos(a);
            b.datepicker._pos[1]+=a.offsetHeight
            }
            var i=false;
        b(a).parents().each(function(){
            i|=b(this).css("position")=="fixed";
            return!i
            });
        if(i&&b.browser.opera){
            b.datepicker._pos[0]-=document.documentElement.scrollLeft;
            b.datepicker._pos[1]-=document.documentElement.scrollTop
            }
            g={
            left:b.datepicker._pos[0],
            top:b.datepicker._pos[1]
            };
            
        b.datepicker._pos=null;
        e.dpDiv.css({
            position:"absolute",
            display:"block",
            top:"-1000px"
        });
        b.datepicker._updateDatepicker(e);
        g=b.datepicker._checkOffset(e,g,i);
        e.dpDiv.css({
            position:b.datepicker._inDialog&&
            b.blockUI?"static":i?"fixed":"absolute",
            display:"none",
            left:g.left+"px",
            top:g.top+"px"
            });
        if(!e.inline){
            g=b.datepicker._get(e,"showAnim");
            var k=b.datepicker._get(e,"duration"),j=function(){
                b.datepicker._datepickerShowing=true;
                var h=b.datepicker._getBorders(e.dpDiv);
                e.dpDiv.find("iframe.ui-datepicker-cover").css({
                    left:-h[0],
                    top:-h[1],
                    width:e.dpDiv.outerWidth(),
                    height:e.dpDiv.outerHeight()
                    })
                };
                
            e.dpDiv.zIndex(b(a).zIndex()+1);
            b.effects&&b.effects[g]?e.dpDiv.show(g,b.datepicker._get(e,"showOptions"),k,
                j):e.dpDiv[g||"show"](g?k:null,j);
            if(!g||!k)j();
            e.input.is(":visible")&&!e.input.is(":disabled")&&e.input.focus();
            b.datepicker._curInst=e
            }
        }
},
_updateDatepicker:function(a){
    var e=this,g=b.datepicker._getBorders(a.dpDiv);
    a.dpDiv.empty().append(this._generateHTML(a)).find("iframe.ui-datepicker-cover").css({
        left:-g[0],
        top:-g[1],
        width:a.dpDiv.outerWidth(),
        height:a.dpDiv.outerHeight()
        }).end().find("button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a").bind("mouseout",function(){
        b(this).removeClass("ui-state-hover");
        this.className.indexOf("ui-datepicker-prev")!=-1&&b(this).removeClass("ui-datepicker-prev-hover");
        this.className.indexOf("ui-datepicker-next")!=-1&&b(this).removeClass("ui-datepicker-next-hover")
        }).bind("mouseover",function(){
        if(!e._isDisabledDatepicker(a.inline?a.dpDiv.parent()[0]:a.input[0])){
            b(this).parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover");
            b(this).addClass("ui-state-hover");
            this.className.indexOf("ui-datepicker-prev")!=-1&&b(this).addClass("ui-datepicker-prev-hover");
            this.className.indexOf("ui-datepicker-next")!=-1&&b(this).addClass("ui-datepicker-next-hover")
            }
        }).end().find("."+this._dayOverClass+" a").trigger("mouseover").end();
g=this._getNumberOfMonths(a);
var i=g[1];
i>1?a.dpDiv.addClass("ui-datepicker-multi-"+i).css("width",17*i+"em"):a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width("");
a.dpDiv[(g[0]!=1||g[1]!=1?"add":"remove")+"Class"]("ui-datepicker-multi");
a.dpDiv[(this._get(a,"isRTL")?"add":"remove")+"Class"]("ui-datepicker-rtl");
a==b.datepicker._curInst&&b.datepicker._datepickerShowing&&a.input&&a.input.is(":visible")&&!a.input.is(":disabled")&&a.input.focus()
},
_getBorders:function(a){
    var e=function(g){
        return{
            thin:1,
            medium:2,
            thick:3
        }
        [g]||g
        };
        
    return[parseFloat(e(a.css("border-left-width"))),parseFloat(e(a.css("border-top-width")))]
    },
_checkOffset:function(a,e,g){
    var i=a.dpDiv.outerWidth(),k=a.dpDiv.outerHeight(),j=a.input?a.input.outerWidth():0,h=a.input?a.input.outerHeight():0,l=document.documentElement.clientWidth+b(document).scrollLeft(),
    m=document.documentElement.clientHeight+b(document).scrollTop();
    e.left-=this._get(a,"isRTL")?i-j:0;
    e.left-=g&&e.left==a.input.offset().left?b(document).scrollLeft():0;
    e.top-=g&&e.top==a.input.offset().top+h?b(document).scrollTop():0;
    e.left-=Math.min(e.left,e.left+i>l&&l>i?Math.abs(e.left+i-l):0);
    e.top-=Math.min(e.top,e.top+k>m&&m>k?Math.abs(k+h):0);
    return e
    },
_findPos:function(a){
    for(var e=this._get(this._getInst(a),"isRTL");a&&(a.type=="hidden"||a.nodeType!=1);)a=a[e?"previousSibling":"nextSibling"];
    a=b(a).offset();
    return[a.left,a.top]
    },
_hideDatepicker:function(a){
    var e=this._curInst;
    if(!(!e||a&&e!=b.data(a,"datepicker")))if(this._datepickerShowing){
        a=this._get(e,"showAnim");
        var g=this._get(e,"duration"),i=function(){
            b.datepicker._tidyDialog(e);
            this._curInst=null
            };
            
        b.effects&&b.effects[a]?e.dpDiv.hide(a,b.datepicker._get(e,"showOptions"),g,i):e.dpDiv[a=="slideDown"?"slideUp":a=="fadeIn"?"fadeOut":"hide"](a?g:null,i);
        a||i();
        if(a=this._get(e,"onClose"))a.apply(e.input?e.input[0]:null,[e.input?e.input.val():
            "",e]);
        this._datepickerShowing=false;
        this._lastInput=null;
        if(this._inDialog){
            this._dialogInput.css({
                position:"absolute",
                left:"0",
                top:"-100px"
            });
            if(b.blockUI){
                b.unblockUI();
                b("body").append(this.dpDiv)
                }
            }
        this._inDialog=false
    }
    },
_tidyDialog:function(a){
    a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")
    },
_checkExternalClick:function(a){
    if(b.datepicker._curInst){
        a=b(a.target);
        a[0].id!=b.datepicker._mainDivId&&a.parents("#"+b.datepicker._mainDivId).length==0&&!a.hasClass(b.datepicker.markerClassName)&&
        !a.hasClass(b.datepicker._triggerClass)&&b.datepicker._datepickerShowing&&!(b.datepicker._inDialog&&b.blockUI)&&b.datepicker._hideDatepicker()
        }
    },
_adjustDate:function(a,e,g){
    a=b(a);
    var i=this._getInst(a[0]);
    if(!this._isDisabledDatepicker(a[0])){
        this._adjustInstDate(i,e+(g=="M"?this._get(i,"showCurrentAtPos"):0),g);
        this._updateDatepicker(i)
        }
    },
_gotoToday:function(a){
    a=b(a);
    var e=this._getInst(a[0]);
    if(this._get(e,"gotoCurrent")&&e.currentDay){
        e.selectedDay=e.currentDay;
        e.drawMonth=e.selectedMonth=e.currentMonth;
        e.drawYear=e.selectedYear=e.currentYear
        }else{
        var g=new Date;
        e.selectedDay=g.getDate();
        e.drawMonth=e.selectedMonth=g.getMonth();
        e.drawYear=e.selectedYear=g.getFullYear()
        }
        this._notifyChange(e);
    this._adjustDate(a)
    },
_selectMonthYear:function(a,e,g){
    a=b(a);
    var i=this._getInst(a[0]);
    i._selectingMonthYear=false;
    i["selected"+(g=="M"?"Month":"Year")]=i["draw"+(g=="M"?"Month":"Year")]=parseInt(e.options[e.selectedIndex].value,10);
    this._notifyChange(i);
    this._adjustDate(a)
    },
_clickMonthYear:function(a){
    a=this._getInst(b(a)[0]);
    a.input&&a._selectingMonthYear&&!b.browser.msie&&a.input.focus();
    a._selectingMonthYear=!a._selectingMonthYear
    },
_selectDay:function(a,e,g,i){
    var k=b(a);
    if(!(b(i).hasClass(this._unselectableClass)||this._isDisabledDatepicker(k[0]))){
        k=this._getInst(k[0]);
        k.selectedDay=k.currentDay=b("a",i).html();
        k.selectedMonth=k.currentMonth=e;
        k.selectedYear=k.currentYear=g;
        this._selectDate(a,this._formatDate(k,k.currentDay,k.currentMonth,k.currentYear))
        }
    },
_clearDate:function(a){
    a=b(a);
    this._getInst(a[0]);
    this._selectDate(a,
        "")
    },
_selectDate:function(a,e){
    var g=this._getInst(b(a)[0]);
    e=e!=null?e:this._formatDate(g);
    g.input&&g.input.val(e);
    this._updateAlternate(g);
    var i=this._get(g,"onSelect");
    if(i)i.apply(g.input?g.input[0]:null,[e,g]);else g.input&&g.input.trigger("change");
    if(g.inline)this._updateDatepicker(g);
    else{
        this._hideDatepicker();
        this._lastInput=g.input[0];
        typeof g.input[0]!="object"&&g.input.focus();
        this._lastInput=null
        }
    },
_updateAlternate:function(a){
    var e=this._get(a,"altField");
    if(e){
        var g=this._get(a,"altFormat")||
        this._get(a,"dateFormat"),i=this._getDate(a),k=this.formatDate(g,i,this._getFormatConfig(a));
        b(e).each(function(){
            b(this).val(k)
            })
        }
    },
noWeekends:function(a){
    a=a.getDay();
    return[a>0&&a<6,""]
    },
iso8601Week:function(a){
    a=new Date(a.getTime());
    a.setDate(a.getDate()+4-(a.getDay()||7));
    var e=a.getTime();
    a.setMonth(0);
    a.setDate(1);
    return Math.floor(Math.round((e-a)/864E5)/7)+1
    },
parseDate:function(a,e,g){
    if(a==null||e==null)throw"Invalid arguments";
    e=typeof e=="object"?e.toString():e+"";
    if(e=="")return null;
    for(var i=(g?g.shortYearCutoff:null)||this._defaults.shortYearCutoff,k=(g?g.dayNamesShort:null)||this._defaults.dayNamesShort,j=(g?g.dayNames:null)||this._defaults.dayNames,h=(g?g.monthNamesShort:null)||this._defaults.monthNamesShort,l=(g?g.monthNames:null)||this._defaults.monthNames,m=g=-1,n=-1,o=-1,p=false,q=function(v){
        (v=C+1<a.length&&a.charAt(C+1)==v)&&C++;
        return v
        },r=function(v){
        q(v);
        v=RegExp("^\\d{1,"+(v=="@"?14:v=="!"?20:v=="y"?4:v=="o"?3:2)+"}");
        v=e.substring(x).match(v);
        if(!v)throw"Missing number at position "+
            x;
        x+=v[0].length;
        return parseInt(v[0],10)
        },s=function(v,A,I){
        v=q(v)?I:A;
        for(A=0;A<v.length;A++)if(e.substr(x,v[A].length)==v[A]){
            x+=v[A].length;
            return A+1
            }
            throw"Unknown name at position "+x;
    },u=function(){
        if(e.charAt(x)!=a.charAt(C))throw"Unexpected literal at position "+x;
        x++
    },x=0,C=0;C<a.length;C++)if(p)if(a.charAt(C)=="'"&&!q("'"))p=false;else u();else switch(a.charAt(C)){
        case "d":
            n=r("d");
            break;
        case "D":
            s("D",k,j);
            break;
        case "o":
            o=r("o");
            break;
        case "m":
            m=r("m");
            break;
        case "M":
            m=s("M",h,l);
            break;
        case "y":
            g=r("y");
            break;
        case "@":
            var z=new Date(r("@"));
            g=z.getFullYear();
            m=z.getMonth()+1;
            n=z.getDate();
            break;
        case "!":
            z=new Date((r("!")-this._ticksTo1970)/1E4);
            g=z.getFullYear();
            m=z.getMonth()+1;
            n=z.getDate();
            break;
        case "'":
            if(q("'"))u();else p=true;
            break;
        default:
            u()
            }
            if(g==-1)g=(new Date).getFullYear();
    else if(g<100)g+=(new Date).getFullYear()-(new Date).getFullYear()%100+(g<=i?0:-100);
    if(o>-1){
        m=1;
        n=o;
        do{
            i=this._getDaysInMonth(g,m-1);
            if(n<=i)break;
            m++;
            n-=i
            }while(1)
    }
    z=this._daylightSavingAdjust(new Date(g,
        m-1,n));
    if(z.getFullYear()!=g||z.getMonth()+1!=m||z.getDate()!=n)throw"Invalid date";
    return z
    },
ATOM:"yy-mm-dd",
COOKIE:"D, dd M yy",
ISO_8601:"yy-mm-dd",
RFC_822:"D, d M y",
RFC_850:"DD, dd-M-y",
RFC_1036:"D, d M y",
RFC_1123:"D, d M yy",
RFC_2822:"D, d M yy",
RSS:"D, d M y",
TICKS:"!",
TIMESTAMP:"@",
W3C:"yy-mm-dd",
_ticksTo1970:(718685+Math.floor(492.5)-Math.floor(19.7)+Math.floor(4.925))*24*60*60*1E7,
formatDate:function(a,e,g){
    if(!e)return"";
    var i=(g?g.dayNamesShort:null)||this._defaults.dayNamesShort,k=(g?
        g.dayNames:null)||this._defaults.dayNames,j=(g?g.monthNamesShort:null)||this._defaults.monthNamesShort;
    g=(g?g.monthNames:null)||this._defaults.monthNames;
    var h=function(q){
        (q=p+1<a.length&&a.charAt(p+1)==q)&&p++;
        return q
        },l=function(q,r,s){
        r=""+r;
        if(h(q))for(;r.length<s;)r="0"+r;
        return r
        },m=function(q,r,s,u){
        return h(q)?u[r]:s[r]
        },n="",o=false;
    if(e)for(var p=0;p<a.length;p++)if(o)if(a.charAt(p)=="'"&&!h("'"))o=false;else n+=a.charAt(p);else switch(a.charAt(p)){
        case "d":
            n+=l("d",e.getDate(),2);
            break;
        case "D":
            n+=m("D",e.getDay(),i,k);
            break;
        case "o":
            n+=l("o",(e.getTime()-(new Date(e.getFullYear(),0,0)).getTime())/864E5,3);
            break;
        case "m":
            n+=l("m",e.getMonth()+1,2);
            break;
        case "M":
            n+=m("M",e.getMonth(),j,g);
            break;
        case "y":
            n+=h("y")?e.getFullYear():(e.getYear()%100<10?"0":"")+e.getYear()%100;
            break;
        case "@":
            n+=e.getTime();
            break;
        case "!":
            n+=e.getTime()*1E4+this._ticksTo1970;
            break;
        case "'":
            if(h("'"))n+="'";else o=true;
            break;
        default:
            n+=a.charAt(p)
            }
            return n
    },
_possibleChars:function(a){
    for(var e="",g=false,
        i=function(j){
            (j=k+1<a.length&&a.charAt(k+1)==j)&&k++;
            return j
            },k=0;k<a.length;k++)if(g)if(a.charAt(k)=="'"&&!i("'"))g=false;else e+=a.charAt(k);else switch(a.charAt(k)){
        case "d":case "m":case "y":case "@":
            e+="0123456789";
            break;
        case "D":case "M":
            return null;
        case "'":
            if(i("'"))e+="'";else g=true;
            break;
        default:
            e+=a.charAt(k)
            }
            return e
    },
_get:function(a,e){
    return a.settings[e]!==undefined?a.settings[e]:this._defaults[e]
    },
_setDateFromField:function(a,e){
    if(a.input.val()!=a.lastVal){
        var g=this._get(a,"dateFormat"),
        i=a.lastVal=a.input?a.input.val():null,k,j;
        k=j=this._getDefaultDate(a);
        var h=this._getFormatConfig(a);
        try{
            k=this.parseDate(g,i,h)||j
            }catch(l){
            this.log(l);
            i=e?"":i
            }
            a.selectedDay=k.getDate();
        a.drawMonth=a.selectedMonth=k.getMonth();
        a.drawYear=a.selectedYear=k.getFullYear();
        a.currentDay=i?k.getDate():0;
        a.currentMonth=i?k.getMonth():0;
        a.currentYear=i?k.getFullYear():0;
        this._adjustInstDate(a)
        }
    },
_getDefaultDate:function(a){
    return this._restrictMinMax(a,this._determineDate(a,this._get(a,"defaultDate"),new Date))
    },
_determineDate:function(a,e,g){
    var i=function(j){
        var h=new Date;
        h.setDate(h.getDate()+j);
        return h
        },k=function(j){
        try{
            return b.datepicker.parseDate(b.datepicker._get(a,"dateFormat"),j,b.datepicker._getFormatConfig(a))
            }catch(h){}
        var l=(j.toLowerCase().match(/^c/)?b.datepicker._getDate(a):null)||new Date,m=l.getFullYear(),n=l.getMonth();
        l=l.getDate();
        for(var o=/([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,p=o.exec(j);p;){
            switch(p[2]||"d"){
                case "d":case "D":
                    l+=parseInt(p[1],10);
                    break;
                case "w":case "W":
                    l+=parseInt(p[1],
                    10)*7;
                break;
                case "m":case "M":
                    n+=parseInt(p[1],10);
                    l=Math.min(l,b.datepicker._getDaysInMonth(m,n));
                    break;
                case "y":case "Y":
                    m+=parseInt(p[1],10);
                    l=Math.min(l,b.datepicker._getDaysInMonth(m,n))
                    }
                    p=o.exec(j)
            }
            return new Date(m,n,l)
        };
        
    if(e=(e=e==null?g:typeof e=="string"?k(e):typeof e=="number"?isNaN(e)?g:i(e):e)&&e.toString()=="Invalid Date"?g:e){
        e.setHours(0);
        e.setMinutes(0);
        e.setSeconds(0);
        e.setMilliseconds(0)
        }
        return this._daylightSavingAdjust(e)
    },
_daylightSavingAdjust:function(a){
    if(!a)return null;
    a.setHours(a.getHours()>
        12?a.getHours()+2:0);
    return a
    },
_setDate:function(a,e,g){
    var i=!e,k=a.selectedMonth,j=a.selectedYear;
    e=this._restrictMinMax(a,this._determineDate(a,e,new Date));
    a.selectedDay=a.currentDay=e.getDate();
    a.drawMonth=a.selectedMonth=a.currentMonth=e.getMonth();
    a.drawYear=a.selectedYear=a.currentYear=e.getFullYear();
    if((k!=a.selectedMonth||j!=a.selectedYear)&&!g)this._notifyChange(a);
    this._adjustInstDate(a);
    if(a.input)a.input.val(i?"":this._formatDate(a))
        },
_getDate:function(a){
    return!a.currentYear||a.input&&
    a.input.val()==""?null:this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay))
    },
_generateHTML:function(a){
    var e=new Date;
    e=this._daylightSavingAdjust(new Date(e.getFullYear(),e.getMonth(),e.getDate()));
    var g=this._get(a,"isRTL"),i=this._get(a,"showButtonPanel"),k=this._get(a,"hideIfNoPrevNext"),j=this._get(a,"navigationAsDateFormat"),h=this._getNumberOfMonths(a),l=this._get(a,"showCurrentAtPos"),m=this._get(a,"stepMonths"),n=h[0]!=1||h[1]!=1,o=this._daylightSavingAdjust(!a.currentDay?
        new Date(9999,9,9):new Date(a.currentYear,a.currentMonth,a.currentDay)),p=this._getMinMaxDate(a,"min"),q=this._getMinMaxDate(a,"max");
    l=a.drawMonth-l;
    var r=a.drawYear;
    if(l<0){
        l+=12;
        r--
    }
    if(q){
        var s=this._daylightSavingAdjust(new Date(q.getFullYear(),q.getMonth()-h[0]*h[1]+1,q.getDate()));
        for(s=p&&s<p?p:s;this._daylightSavingAdjust(new Date(r,l,1))>s;){
            l--;
            if(l<0){
                l=11;
                r--
            }
        }
        }
    a.drawMonth=l;
a.drawYear=r;
s=this._get(a,"prevText");
s=!j?s:this.formatDate(s,this._daylightSavingAdjust(new Date(r,l-m,1)),this._getFormatConfig(a));
s=this._canAdjustMonth(a,-1,r,l)?'<a class="ui-datepicker-prev ui-corner-all" onclick="DP_jQuery_'+d+".datepicker._adjustDate('#"+a.id+"', -"+m+", 'M');\" title=\""+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(g?"e":"w")+'">'+s+"</span></a>":k?"":'<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="'+s+'"><span class="ui-icon ui-icon-circle-triangle-'+(g?"e":"w")+'">'+s+"</span></a>";
var u=this._get(a,"nextText");
u=!j?u:this.formatDate(u,this._daylightSavingAdjust(new Date(r,
    l+m,1)),this._getFormatConfig(a));
k=this._canAdjustMonth(a,+1,r,l)?'<a class="ui-datepicker-next ui-corner-all" onclick="DP_jQuery_'+d+".datepicker._adjustDate('#"+a.id+"', +"+m+", 'M');\" title=\""+u+'"><span class="ui-icon ui-icon-circle-triangle-'+(g?"w":"e")+'">'+u+"</span></a>":k?"":'<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="'+u+'"><span class="ui-icon ui-icon-circle-triangle-'+(g?"w":"e")+'">'+u+"</span></a>";
m=this._get(a,"currentText");
u=this._get(a,"gotoCurrent")&&
a.currentDay?o:e;
m=!j?m:this.formatDate(m,u,this._getFormatConfig(a));
j=!a.inline?'<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" onclick="DP_jQuery_'+d+'.datepicker._hideDatepicker();">'+this._get(a,"closeText")+"</button>":"";
i=i?'<div class="ui-datepicker-buttonpane ui-widget-content">'+(g?j:"")+(this._isInRange(a,u)?'<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" onclick="DP_jQuery_'+
    d+".datepicker._gotoToday('#"+a.id+"');\">"+m+"</button>":"")+(g?"":j)+"</div>":"";
j=parseInt(this._get(a,"firstDay"),10);
j=isNaN(j)?0:j;
m=this._get(a,"showWeek");
u=this._get(a,"dayNames");
this._get(a,"dayNamesShort");
var x=this._get(a,"dayNamesMin"),C=this._get(a,"monthNames"),z=this._get(a,"monthNamesShort"),v=this._get(a,"beforeShowDay"),A=this._get(a,"showOtherMonths"),I=this._get(a,"selectOtherMonths");
this._get(a,"calculateWeek");
for(var L=this._getDefaultDate(a),J="",F=0;F<h[0];F++){
    for(var M=
        "",G=0;G<h[1];G++){
        var N=this._daylightSavingAdjust(new Date(r,l,a.selectedDay)),y=" ui-corner-all",B="";
        if(n){
            B+='<div class="ui-datepicker-group';
            if(h[1]>1)switch(G){
                case 0:
                    B+=" ui-datepicker-group-first";
                    y=" ui-corner-"+(g?"right":"left");
                    break;
                case h[1]-1:
                    B+=" ui-datepicker-group-last";
                    y=" ui-corner-"+(g?"left":"right");
                    break;
                default:
                    B+=" ui-datepicker-group-middle";
                    y=""
                    }
                    B+='">'
            }
            B+='<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix'+y+'">'+(/all|left/.test(y)&&F==0?g?k:s:"")+
        (/all|right/.test(y)&&F==0?g?s:k:"")+this._generateMonthYearHeader(a,l,r,p,q,F>0||G>0,C,z)+'</div><table class="ui-datepicker-calendar"><thead><tr>';
        var D=m?'<th class="ui-datepicker-week-col">'+this._get(a,"weekHeader")+"</th>":"";
        for(y=0;y<7;y++){
            var w=(y+j)%7;
            D+="<th"+((y+j+6)%7>=5?' class="ui-datepicker-week-end"':"")+'><span title="'+u[w]+'">'+x[w]+"</span></th>"
            }
            B+=D+"</tr></thead><tbody>";
        D=this._getDaysInMonth(r,l);
        if(r==a.selectedYear&&l==a.selectedMonth)a.selectedDay=Math.min(a.selectedDay,
            D);
        y=(this._getFirstDayOfMonth(r,l)-j+7)%7;
        D=n?6:Math.ceil((y+D)/7);
        w=this._daylightSavingAdjust(new Date(r,l,1-y));
        for(var O=0;O<D;O++){
            B+="<tr>";
            var P=!m?"":'<td class="ui-datepicker-week-col">'+this._get(a,"calculateWeek")(w)+"</td>";
            for(y=0;y<7;y++){
                var H=v?v.apply(a.input?a.input[0]:null,[w]):[true,""],E=w.getMonth()!=l,K=E&&!I||!H[0]||p&&w<p||q&&w>q;
                P+='<td class="'+((y+j+6)%7>=5?" ui-datepicker-week-end":"")+(E?" ui-datepicker-other-month":"")+(w.getTime()==N.getTime()&&l==a.selectedMonth&&
                    a._keyEvent||L.getTime()==w.getTime()&&L.getTime()==N.getTime()?" "+this._dayOverClass:"")+(K?" "+this._unselectableClass+" ui-state-disabled":"")+(E&&!A?"":" "+H[1]+(w.getTime()==o.getTime()?" "+this._currentClass:"")+(w.getTime()==e.getTime()?" ui-datepicker-today":""))+'"'+((!E||A)&&H[2]?' title="'+H[2]+'"':"")+(K?"":' onclick="DP_jQuery_'+d+".datepicker._selectDay('#"+a.id+"',"+w.getMonth()+","+w.getFullYear()+', this);return false;"')+">"+(E&&!A?"&#xa0;":K?'<span class="ui-state-default">'+w.getDate()+
                    "</span>":'<a class="ui-state-default'+(w.getTime()==e.getTime()?" ui-state-highlight":"")+(w.getTime()==o.getTime()?" ui-state-active":"")+(E?" ui-priority-secondary":"")+'" href="#">'+w.getDate()+"</a>")+"</td>";
                w.setDate(w.getDate()+1);
                w=this._daylightSavingAdjust(w)
                }
                B+=P+"</tr>"
            }
            l++;
        if(l>11){
            l=0;
            r++
        }
        B+="</tbody></table>"+(n?"</div>"+(h[0]>0&&G==h[1]-1?'<div class="ui-datepicker-row-break"></div>':""):"");
        M+=B
        }
        J+=M
    }
    J+=i+(b.browser.msie&&parseInt(b.browser.version,10)<7&&!a.inline?'<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>':
    "");
a._keyEvent=false;
return J
},
_generateMonthYearHeader:function(a,e,g,i,k,j,h,l){
    var m=this._get(a,"changeMonth"),n=this._get(a,"changeYear"),o=this._get(a,"showMonthAfterYear"),p='<div class="ui-datepicker-title">',q="";
    if(j||!m)q+='<span class="ui-datepicker-month">'+h[e]+"</span>";
    else{
        h=i&&i.getFullYear()==g;
        var r=k&&k.getFullYear()==g;
        q+='<select class="ui-datepicker-month" onchange="DP_jQuery_'+d+".datepicker._selectMonthYear('#"+a.id+"', this, 'M');\" onclick=\"DP_jQuery_"+d+".datepicker._clickMonthYear('#"+
        a.id+"');\">";
        for(var s=0;s<12;s++)if((!h||s>=i.getMonth())&&(!r||s<=k.getMonth()))q+='<option value="'+s+'"'+(s==e?' selected="selected"':"")+">"+l[s]+"</option>";q+="</select>"
        }
        o||(p+=q+(j||!(m&&n)?"&#xa0;":""));
    if(j||!n)p+='<span class="ui-datepicker-year">'+g+"</span>";
    else{
        l=this._get(a,"yearRange").split(":");
        var u=(new Date).getFullYear();
        h=function(x){
            x=x.match(/c[+-].*/)?g+parseInt(x.substring(1),10):x.match(/[+-].*/)?u+parseInt(x,10):parseInt(x,10);
            return isNaN(x)?u:x
            };
            
        e=h(l[0]);
        l=Math.max(e,
            h(l[1]||""));
        e=i?Math.max(e,i.getFullYear()):e;
        l=k?Math.min(l,k.getFullYear()):l;
        for(p+='<select class="ui-datepicker-year" onchange="DP_jQuery_'+d+".datepicker._selectMonthYear('#"+a.id+"', this, 'Y');\" onclick=\"DP_jQuery_"+d+".datepicker._clickMonthYear('#"+a.id+"');\">";e<=l;e++)p+='<option value="'+e+'"'+(e==g?' selected="selected"':"")+">"+e+"</option>";
        p+="</select>"
        }
        p+=this._get(a,"yearSuffix");
    if(o)p+=(j||!(m&&n)?"&#xa0;":"")+q;
    p+="</div>";
    return p
    },
_adjustInstDate:function(a,e,g){
    var i=
    a.drawYear+(g=="Y"?e:0),k=a.drawMonth+(g=="M"?e:0);
    e=Math.min(a.selectedDay,this._getDaysInMonth(i,k))+(g=="D"?e:0);
    i=this._restrictMinMax(a,this._daylightSavingAdjust(new Date(i,k,e)));
    a.selectedDay=i.getDate();
    a.drawMonth=a.selectedMonth=i.getMonth();
    a.drawYear=a.selectedYear=i.getFullYear();
    if(g=="M"||g=="Y")this._notifyChange(a)
        },
_restrictMinMax:function(a,e){
    var g=this._getMinMaxDate(a,"min"),i=this._getMinMaxDate(a,"max");
    e=g&&e<g?g:e;
    return e=i&&e>i?i:e
    },
_notifyChange:function(a){
    var e=this._get(a,
        "onChangeMonthYear");
    if(e)e.apply(a.input?a.input[0]:null,[a.selectedYear,a.selectedMonth+1,a])
        },
_getNumberOfMonths:function(a){
    a=this._get(a,"numberOfMonths");
    return a==null?[1,1]:typeof a=="number"?[1,a]:a
    },
_getMinMaxDate:function(a,e){
    return this._determineDate(a,this._get(a,e+"Date"),null)
    },
_getDaysInMonth:function(a,e){
    return 32-(new Date(a,e,32)).getDate()
    },
_getFirstDayOfMonth:function(a,e){
    return(new Date(a,e,1)).getDay()
    },
_canAdjustMonth:function(a,e,g,i){
    var k=this._getNumberOfMonths(a);
    g=this._daylightSavingAdjust(new Date(g,i+(e<0?e:k[0]*k[1]),1));
    e<0&&g.setDate(this._getDaysInMonth(g.getFullYear(),g.getMonth()));
    return this._isInRange(a,g)
    },
_isInRange:function(a,e){
    var g=this._getMinMaxDate(a,"min"),i=this._getMinMaxDate(a,"max");
    return(!g||e.getTime()>=g.getTime())&&(!i||e.getTime()<=i.getTime())
    },
_getFormatConfig:function(a){
    var e=this._get(a,"shortYearCutoff");
    e=typeof e!="string"?e:(new Date).getFullYear()%100+parseInt(e,10);
    return{
        shortYearCutoff:e,
        dayNamesShort:this._get(a,
            "dayNamesShort"),
        dayNames:this._get(a,"dayNames"),
        monthNamesShort:this._get(a,"monthNamesShort"),
        monthNames:this._get(a,"monthNames")
        }
    },
_formatDate:function(a,e,g,i){
    if(!e){
        a.currentDay=a.selectedDay;
        a.currentMonth=a.selectedMonth;
        a.currentYear=a.selectedYear
        }
        e=e?typeof e=="object"?e:this._daylightSavingAdjust(new Date(i,g,e)):this._daylightSavingAdjust(new Date(a.currentYear,a.currentMonth,a.currentDay));
    return this.formatDate(this._get(a,"dateFormat"),e,this._getFormatConfig(a))
    }
});
b.fn.datepicker=
function(a){
    if(!b.datepicker.initialized){
        b(document).mousedown(b.datepicker._checkExternalClick).find("body").append(b.datepicker.dpDiv);
        b.datepicker.initialized=true
        }
        var e=Array.prototype.slice.call(arguments,1);
    if(typeof a=="string"&&(a=="isDisabled"||a=="getDate"||a=="widget"))return b.datepicker["_"+a+"Datepicker"].apply(b.datepicker,[this[0]].concat(e));
    if(a=="option"&&arguments.length==2&&typeof arguments[1]=="string")return b.datepicker["_"+a+"Datepicker"].apply(b.datepicker,[this[0]].concat(e));
    return this.each(function(){
        typeof a=="string"?b.datepicker["_"+a+"Datepicker"].apply(b.datepicker,[this].concat(e)):b.datepicker._attachDatepicker(this,a)
        })
    };
    
b.datepicker=new c;
b.datepicker.initialized=false;
b.datepicker.uuid=(new Date).getTime();
b.datepicker.version="1.8";
window["DP_jQuery_"+d]=b
})(jQuery);
(function(b){
    b.widget("ui.progressbar",{
        options:{
            value:0
        },
        _create:function(){
            this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({
                role:"progressbar",
                "aria-valuemin":this._valueMin(),
                "aria-valuemax":this._valueMax(),
                "aria-valuenow":this._value()
                });
            this.valueDiv=b("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element);
            this._refreshValue()
            },
        destroy:function(){
            this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow");
            this.valueDiv.remove();
            b.Widget.prototype.destroy.apply(this,arguments)
            },
        value:function(c){
            if(c===undefined)return this._value();
            this._setOption("value",c);
            return this
            },
        _setOption:function(c,f){
            switch(c){
                case "value":
                    this.options.value=f;
                    this._refreshValue();
                    this._trigger("change")
                    }
                    b.Widget.prototype._setOption.apply(this,arguments)
            },
        _value:function(){
            var c=this.options.value;
            if(typeof c!=="number")c=0;
            if(c<this._valueMin())c=this._valueMin();
            if(c>this._valueMax())c=this._valueMax();
            return c
            },
        _valueMin:function(){
            return 0
            },
        _valueMax:function(){
            return 100
            },
        _refreshValue:function(){
            var c=this.value();
            this.valueDiv[c===this._valueMax()?"addClass":"removeClass"]("ui-corner-right").width(c+"%");
            this.element.attr("aria-valuenow",c)
            }
        });
b.extend(b.ui.progressbar,{
    version:"1.8"
})
})(jQuery);
jQuery.effects||function(b){
    function c(j){
        var h;
        if(j&&j.constructor==Array&&j.length==3)return j;
        if(h=/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(j))return[parseInt(h[1],10),parseInt(h[2],10),parseInt(h[3],10)];
        if(h=/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(j))return[parseFloat(h[1])*2.55,parseFloat(h[2])*2.55,parseFloat(h[3])*2.55];
        if(h=/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(j))return[parseInt(h[1],
            16),parseInt(h[2],16),parseInt(h[3],16)];
        if(h=/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(j))return[parseInt(h[1]+h[1],16),parseInt(h[2]+h[2],16),parseInt(h[3]+h[3],16)];
        if(/rgba\(0, 0, 0, 0\)/.exec(j))return g.transparent;
        return g[b.trim(j).toLowerCase()]
        }
        function f(){
        var j=document.defaultView?document.defaultView.getComputedStyle(this,null):this.currentStyle,h={},l,m;
        if(j&&j.length&&j[0]&&j[j[0]])for(var n=j.length;n--;){
            l=j[n];
            if(typeof j[l]=="string"){
                m=l.replace(/\-(\w)/g,function(o,p){
                    return p.toUpperCase()
                    });
                h[m]=j[l]
                }
            }else for(l in j)if(typeof j[l]==="string")h[l]=j[l];return h
    }
    function d(j){
    var h,l;
    for(h in j){
        l=j[h];
        if(l==null||b.isFunction(l)||h in k||/scrollbar/.test(h)||!/color/i.test(h)&&isNaN(parseFloat(l)))delete j[h]
    }
    return j
    }
    function a(j,h){
    var l={
        _:0
    },m;
    for(m in h)if(j[m]!=h[m])l[m]=h[m];return l
    }
    function e(j,h,l,m){
    if(typeof j=="object"){
        m=h;
        l=null;
        h=j;
        j=h.effect
        }
        if(b.isFunction(h)){
        m=h;
        l=null;
        h={}
    }
    if(b.isFunction(l)){
    m=l;
    l=null
    }
    if(typeof h=="number"||b.fx.speeds[h]){
    m=l;
    l=h;
    h={}
}
h=h||{};
l=l||h.duration;
l=b.fx.off?0:typeof l=="number"?l:b.fx.speeds[l]||b.fx.speeds._default;
m=m||h.complete;
return[j,h,l,m]
}
b.effects={};

b.each(["backgroundColor","borderBottomColor","borderLeftColor","borderRightColor","borderTopColor","color","outlineColor"],function(j,h){
    b.fx.step[h]=function(l){
        if(!l.colorInit){
            var m;
            m=l.elem;
            var n=h,o;
            do{
                o=b.curCSS(m,n);
                if(o!=""&&o!="transparent"||b.nodeName(m,"body"))break;
                n="backgroundColor"
                }while(m=m.parentNode);
            m=c(o);
            l.start=m;
            l.end=c(l.end);
            l.colorInit=true
            }
            l.elem.style[h]=
        "rgb("+Math.max(Math.min(parseInt(l.pos*(l.end[0]-l.start[0])+l.start[0],10),255),0)+","+Math.max(Math.min(parseInt(l.pos*(l.end[1]-l.start[1])+l.start[1],10),255),0)+","+Math.max(Math.min(parseInt(l.pos*(l.end[2]-l.start[2])+l.start[2],10),255),0)+")"
        }
    });
var g={
    aqua:[0,255,255],
    azure:[240,255,255],
    beige:[245,245,220],
    black:[0,0,0],
    blue:[0,0,255],
    brown:[165,42,42],
    cyan:[0,255,255],
    darkblue:[0,0,139],
    darkcyan:[0,139,139],
    darkgrey:[169,169,169],
    darkgreen:[0,100,0],
    darkkhaki:[189,183,107],
    darkmagenta:[139,
    0,139],
    darkolivegreen:[85,107,47],
    darkorange:[255,140,0],
    darkorchid:[153,50,204],
    darkred:[139,0,0],
    darksalmon:[233,150,122],
    darkviolet:[148,0,211],
    fuchsia:[255,0,255],
    gold:[255,215,0],
    green:[0,128,0],
    indigo:[75,0,130],
    khaki:[240,230,140],
    lightblue:[173,216,230],
    lightcyan:[224,255,255],
    lightgreen:[144,238,144],
    lightgrey:[211,211,211],
    lightpink:[255,182,193],
    lightyellow:[255,255,224],
    lime:[0,255,0],
    magenta:[255,0,255],
    maroon:[128,0,0],
    navy:[0,0,128],
    olive:[128,128,0],
    orange:[255,165,0],
    pink:[255,192,
    203],
    purple:[128,0,128],
    violet:[128,0,128],
    red:[255,0,0],
    silver:[192,192,192],
    white:[255,255,255],
    yellow:[255,255,0],
    transparent:[255,255,255]
    },i=["add","remove","toggle"],k={
    border:1,
    borderBottom:1,
    borderColor:1,
    borderLeft:1,
    borderRight:1,
    borderTop:1,
    borderWidth:1,
    margin:1,
    padding:1
};

b.effects.animateClass=function(j,h,l,m){
    if(b.isFunction(l)){
        m=l;
        l=null
        }
        return this.each(function(){
        var n=b(this),o=n.attr("style")||" ",p=d(f.call(this)),q,r=n.attr("className");
        b.each(i,function(s,u){
            j[u]&&n[u+"Class"](j[u])
            });
        q=d(f.call(this));
        n.attr("className",r);
        n.animate(a(p,q),h,l,function(){
            b.each(i,function(s,u){
                j[u]&&n[u+"Class"](j[u])
                });
            if(typeof n.attr("style")=="object"){
                n.attr("style").cssText="";
                n.attr("style").cssText=o
                }else n.attr("style",o);
            m&&m.apply(this,arguments)
            })
        })
    };
    
b.fn.extend({
    _addClass:b.fn.addClass,
    addClass:function(j,h,l,m){
        return h?b.effects.animateClass.apply(this,[{
            add:j
        },h,l,m]):this._addClass(j)
        },
    _removeClass:b.fn.removeClass,
    removeClass:function(j,h,l,m){
        return h?b.effects.animateClass.apply(this,
            [{
                remove:j
            },h,l,m]):this._removeClass(j)
        },
    _toggleClass:b.fn.toggleClass,
    toggleClass:function(j,h,l,m,n){
        return typeof h=="boolean"||h===undefined?l?b.effects.animateClass.apply(this,[h?{
            add:j
        }:{
            remove:j
        },l,m,n]):this._toggleClass(j,h):b.effects.animateClass.apply(this,[{
            toggle:j
        },h,l,m])
        },
    switchClass:function(j,h,l,m,n){
        return b.effects.animateClass.apply(this,[{
            add:h,
            remove:j
        },l,m,n])
        }
    });
b.extend(b.effects,{
    version:"1.8",
    save:function(j,h){
        for(var l=0;l<h.length;l++)h[l]!==null&&j.data("ec.storage."+
            h[l],j[0].style[h[l]])
        },
    restore:function(j,h){
        for(var l=0;l<h.length;l++)h[l]!==null&&j.css(h[l],j.data("ec.storage."+h[l]))
            },
    setMode:function(j,h){
        if(h=="toggle")h=j.is(":hidden")?"show":"hide";
        return h
        },
    getBaseline:function(j,h){
        var l,m;
        switch(j[0]){
            case "top":
                l=0;
                break;
            case "middle":
                l=0.5;
                break;
            case "bottom":
                l=1;
                break;
            default:
                l=j[0]/h.height
                }
                switch(j[1]){
            case "left":
                m=0;
                break;
            case "center":
                m=0.5;
                break;
            case "right":
                m=1;
                break;
            default:
                m=j[1]/h.width
                }
                return{
            x:m,
            y:l
        }
    },
createWrapper:function(j){
    if(j.parent().is(".ui-effects-wrapper"))return j.parent();
    var h={
        width:j.outerWidth(true),
        height:j.outerHeight(true),
        "float":j.css("float")
        },l=b("<div></div>").addClass("ui-effects-wrapper").css({
        fontSize:"100%",
        background:"transparent",
        border:"none",
        margin:0,
        padding:0
    });
    j.wrap(l);
    l=j.parent();
    if(j.css("position")=="static"){
        l.css({
            position:"relative"
        });
        j.css({
            position:"relative"
        })
        }else{
        b.extend(h,{
            position:j.css("position"),
            zIndex:j.css("z-index")
            });
        b.each(["top","left","bottom","right"],function(m,n){
            h[n]=j.css(n);
            if(isNaN(parseInt(h[n],10)))h[n]="auto"
                });
        j.css({
            position:"relative",
            top:0,
            left:0
        })
        }
        return l.css(h).show()
    },
removeWrapper:function(j){
    if(j.parent().is(".ui-effects-wrapper"))return j.parent().replaceWith(j);
    return j
    },
setTransition:function(j,h,l,m){
    m=m||{};
    
    b.each(h,function(n,o){
        unit=j.cssUnit(o);
        if(unit[0]>0)m[o]=unit[0]*l+unit[1]
            });
    return m
    }
});
b.fn.extend({
    effect:function(j){
        var h=e.apply(this,arguments);
        h={
            options:h[1],
            duration:h[2],
            callback:h[3]
            };
            
        var l=b.effects[j];
        return l&&!b.fx.off?l.call(this,h):this
        },
    _show:b.fn.show,
    show:function(j){
        if(!j||
            typeof j=="number"||b.fx.speeds[j])return this._show.apply(this,arguments);
        else{
            var h=e.apply(this,arguments);
            h[1].mode="show";
            return this.effect.apply(this,h)
            }
        },
_hide:b.fn.hide,
hide:function(j){
    if(!j||typeof j=="number"||b.fx.speeds[j])return this._hide.apply(this,arguments);
    else{
        var h=e.apply(this,arguments);
        h[1].mode="hide";
        return this.effect.apply(this,h)
        }
    },
__toggle:b.fn.toggle,
toggle:function(j){
    if(!j||typeof j=="number"||b.fx.speeds[j]||typeof j=="boolean"||b.isFunction(j))return this.__toggle.apply(this,
        arguments);
    else{
        var h=e.apply(this,arguments);
        h[1].mode="toggle";
        return this.effect.apply(this,h)
        }
    },
cssUnit:function(j){
    var h=this.css(j),l=[];
    b.each(["em","px","%","pt"],function(m,n){
        if(h.indexOf(n)>0)l=[parseFloat(h),n]
            });
    return l
    }
});
b.easing.jswing=b.easing.swing;
b.extend(b.easing,{
    def:"easeOutQuad",
    swing:function(j,h,l,m,n){
        return b.easing[b.easing.def](j,h,l,m,n)
        },
    easeInQuad:function(j,h,l,m,n){
        return m*(h/=n)*h+l
        },
    easeOutQuad:function(j,h,l,m,n){
        return-m*(h/=n)*(h-2)+l
        },
    easeInOutQuad:function(j,
        h,l,m,n){
        if((h/=n/2)<1)return m/2*h*h+l;
        return-m/2*(--h*(h-2)-1)+l
        },
    easeInCubic:function(j,h,l,m,n){
        return m*(h/=n)*h*h+l
        },
    easeOutCubic:function(j,h,l,m,n){
        return m*((h=h/n-1)*h*h+1)+l
        },
    easeInOutCubic:function(j,h,l,m,n){
        if((h/=n/2)<1)return m/2*h*h*h+l;
        return m/2*((h-=2)*h*h+2)+l
        },
    easeInQuart:function(j,h,l,m,n){
        return m*(h/=n)*h*h*h+l
        },
    easeOutQuart:function(j,h,l,m,n){
        return-m*((h=h/n-1)*h*h*h-1)+l
        },
    easeInOutQuart:function(j,h,l,m,n){
        if((h/=n/2)<1)return m/2*h*h*h*h+l;
        return-m/2*((h-=2)*h*h*h-2)+
        l
        },
    easeInQuint:function(j,h,l,m,n){
        return m*(h/=n)*h*h*h*h+l
        },
    easeOutQuint:function(j,h,l,m,n){
        return m*((h=h/n-1)*h*h*h*h+1)+l
        },
    easeInOutQuint:function(j,h,l,m,n){
        if((h/=n/2)<1)return m/2*h*h*h*h*h+l;
        return m/2*((h-=2)*h*h*h*h+2)+l
        },
    easeInSine:function(j,h,l,m,n){
        return-m*Math.cos(h/n*(Math.PI/2))+m+l
        },
    easeOutSine:function(j,h,l,m,n){
        return m*Math.sin(h/n*(Math.PI/2))+l
        },
    easeInOutSine:function(j,h,l,m,n){
        return-m/2*(Math.cos(Math.PI*h/n)-1)+l
        },
    easeInExpo:function(j,h,l,m,n){
        return h==0?l:m*Math.pow(2,
            10*(h/n-1))+l
        },
    easeOutExpo:function(j,h,l,m,n){
        return h==n?l+m:m*(-Math.pow(2,-10*h/n)+1)+l
        },
    easeInOutExpo:function(j,h,l,m,n){
        if(h==0)return l;
        if(h==n)return l+m;
        if((h/=n/2)<1)return m/2*Math.pow(2,10*(h-1))+l;
        return m/2*(-Math.pow(2,-10*--h)+2)+l
        },
    easeInCirc:function(j,h,l,m,n){
        return-m*(Math.sqrt(1-(h/=n)*h)-1)+l
        },
    easeOutCirc:function(j,h,l,m,n){
        return m*Math.sqrt(1-(h=h/n-1)*h)+l
        },
    easeInOutCirc:function(j,h,l,m,n){
        if((h/=n/2)<1)return-m/2*(Math.sqrt(1-h*h)-1)+l;
        return m/2*(Math.sqrt(1-(h-=2)*
            h)+1)+l
        },
    easeInElastic:function(j,h,l,m,n){
        j=1.70158;
        var o=0,p=m;
        if(h==0)return l;
        if((h/=n)==1)return l+m;
        o||(o=n*0.3);
        if(p<Math.abs(m)){
            p=m;
            j=o/4
            }else j=o/(2*Math.PI)*Math.asin(m/p);
        return-(p*Math.pow(2,10*(h-=1))*Math.sin((h*n-j)*2*Math.PI/o))+l
        },
    easeOutElastic:function(j,h,l,m,n){
        j=1.70158;
        var o=0,p=m;
        if(h==0)return l;
        if((h/=n)==1)return l+m;
        o||(o=n*0.3);
        if(p<Math.abs(m)){
            p=m;
            j=o/4
            }else j=o/(2*Math.PI)*Math.asin(m/p);
        return p*Math.pow(2,-10*h)*Math.sin((h*n-j)*2*Math.PI/o)+m+l
        },
    easeInOutElastic:function(j,
        h,l,m,n){
        j=1.70158;
        var o=0,p=m;
        if(h==0)return l;
        if((h/=n/2)==2)return l+m;
        o||(o=n*0.3*1.5);
        if(p<Math.abs(m)){
            p=m;
            j=o/4
            }else j=o/(2*Math.PI)*Math.asin(m/p);
        if(h<1)return-0.5*p*Math.pow(2,10*(h-=1))*Math.sin((h*n-j)*2*Math.PI/o)+l;
        return p*Math.pow(2,-10*(h-=1))*Math.sin((h*n-j)*2*Math.PI/o)*0.5+m+l
        },
    easeInBack:function(j,h,l,m,n,o){
        if(o==undefined)o=1.70158;
        return m*(h/=n)*h*((o+1)*h-o)+l
        },
    easeOutBack:function(j,h,l,m,n,o){
        if(o==undefined)o=1.70158;
        return m*((h=h/n-1)*h*((o+1)*h+o)+1)+l
        },
    easeInOutBack:function(j,
        h,l,m,n,o){
        if(o==undefined)o=1.70158;
        if((h/=n/2)<1)return m/2*h*h*(((o*=1.525)+1)*h-o)+l;
        return m/2*((h-=2)*h*(((o*=1.525)+1)*h+o)+2)+l
        },
    easeInBounce:function(j,h,l,m,n){
        return m-b.easing.easeOutBounce(j,n-h,0,m,n)+l
        },
    easeOutBounce:function(j,h,l,m,n){
        return(h/=n)<1/2.75?m*7.5625*h*h+l:h<2/2.75?m*(7.5625*(h-=1.5/2.75)*h+0.75)+l:h<2.5/2.75?m*(7.5625*(h-=2.25/2.75)*h+0.9375)+l:m*(7.5625*(h-=2.625/2.75)*h+0.984375)+l
        },
    easeInOutBounce:function(j,h,l,m,n){
        if(h<n/2)return b.easing.easeInBounce(j,h*2,0,
            m,n)*0.5+l;
        return b.easing.easeOutBounce(j,h*2-n,0,m,n)*0.5+m*0.5+l
        }
    })
}(jQuery);
(function(b){
    b.effects.blind=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left"],a=b.effects.setMode(f,c.options.mode||"hide"),e=c.options.direction||"vertical";
            b.effects.save(f,d);
            f.show();
            var g=b.effects.createWrapper(f).css({
                overflow:"hidden"
            }),i=e=="vertical"?"height":"width";
            e=e=="vertical"?g.height():g.width();
            a=="show"&&g.css(i,0);
            var k={};
            
            k[i]=a=="show"?e:0;
            g.animate(k,c.duration,c.options.easing,function(){
                a=="hide"&&f.hide();
                b.effects.restore(f,d);
                b.effects.removeWrapper(f);
                c.callback&&c.callback.apply(f[0],arguments);
                f.dequeue()
                })
            })
        }
    })(jQuery);
(function(b){
    b.effects.bounce=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left"],a=b.effects.setMode(f,c.options.mode||"effect"),e=c.options.direction||"up",g=c.options.distance||20,i=c.options.times||5,k=c.duration||250;
            /show|hide/.test(a)&&d.push("opacity");
            b.effects.save(f,d);
            f.show();
            b.effects.createWrapper(f);
            var j=e=="up"||e=="down"?"top":"left";
            e=e=="up"||e=="left"?"pos":"neg";
            g=c.options.distance||(j=="top"?f.outerHeight({
                margin:true
            })/3:f.outerWidth({
                margin:true
            })/
            3);
            if(a=="show")f.css("opacity",0).css(j,e=="pos"?-g:g);
            if(a=="hide")g/=i*2;
            a!="hide"&&i--;
            if(a=="show"){
                var h={
                    opacity:1
                };
                
                h[j]=(e=="pos"?"+=":"-=")+g;
                f.animate(h,k/2,c.options.easing);
                g/=2;
                i--
            }
            for(h=0;h<i;h++){
                var l={},m={};
                
                l[j]=(e=="pos"?"-=":"+=")+g;
                m[j]=(e=="pos"?"+=":"-=")+g;
                f.animate(l,k/2,c.options.easing).animate(m,k/2,c.options.easing);
                g=a=="hide"?g*2:g/2
                }
                if(a=="hide"){
                h={
                    opacity:0
                };
                
                h[j]=(e=="pos"?"-=":"+=")+g;
                f.animate(h,k/2,c.options.easing,function(){
                    f.hide();
                    b.effects.restore(f,d);
                    b.effects.removeWrapper(f);
                    c.callback&&c.callback.apply(this,arguments)
                    })
                }else{
                l={};
                
                m={};
                
                l[j]=(e=="pos"?"-=":"+=")+g;
                m[j]=(e=="pos"?"+=":"-=")+g;
                f.animate(l,k/2,c.options.easing).animate(m,k/2,c.options.easing,function(){
                    b.effects.restore(f,d);
                    b.effects.removeWrapper(f);
                    c.callback&&c.callback.apply(this,arguments)
                    })
                }
                f.queue("fx",function(){
                f.dequeue()
                });
            f.dequeue()
            })
        }
    })(jQuery);
(function(b){
    b.effects.clip=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left","height","width"],a=b.effects.setMode(f,c.options.mode||"hide"),e=c.options.direction||"vertical";
            b.effects.save(f,d);
            f.show();
            var g=b.effects.createWrapper(f).css({
                overflow:"hidden"
            });
            g=f[0].tagName=="IMG"?g:f;
            var i={
                size:e=="vertical"?"height":"width",
                position:e=="vertical"?"top":"left"
                };
                
            e=e=="vertical"?g.height():g.width();
            if(a=="show"){
                g.css(i.size,0);
                g.css(i.position,e/2)
                }
                var k={};
            
            k[i.size]=
            a=="show"?e:0;
            k[i.position]=a=="show"?0:e/2;
            g.animate(k,{
                queue:false,
                duration:c.duration,
                easing:c.options.easing,
                complete:function(){
                    a=="hide"&&f.hide();
                    b.effects.restore(f,d);
                    b.effects.removeWrapper(f);
                    c.callback&&c.callback.apply(f[0],arguments);
                    f.dequeue()
                    }
                })
        })
    }
})(jQuery);
(function(b){
    b.effects.drop=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left","opacity"],a=b.effects.setMode(f,c.options.mode||"hide"),e=c.options.direction||"left";
            b.effects.save(f,d);
            f.show();
            b.effects.createWrapper(f);
            var g=e=="up"||e=="down"?"top":"left";
            e=e=="up"||e=="left"?"pos":"neg";
            var i=c.options.distance||(g=="top"?f.outerHeight({
                margin:true
            })/2:f.outerWidth({
                margin:true
            })/2);
            if(a=="show")f.css("opacity",0).css(g,e=="pos"?-i:i);
            var k={
                opacity:a=="show"?1:
                0
                };
                
            k[g]=(a=="show"?e=="pos"?"+=":"-=":e=="pos"?"-=":"+=")+i;
            f.animate(k,{
                queue:false,
                duration:c.duration,
                easing:c.options.easing,
                complete:function(){
                    a=="hide"&&f.hide();
                    b.effects.restore(f,d);
                    b.effects.removeWrapper(f);
                    c.callback&&c.callback.apply(this,arguments);
                    f.dequeue()
                    }
                })
        })
    }
})(jQuery);
(function(b){
    b.effects.explode=function(c){
        return this.queue(function(){
            var f=c.options.pieces?Math.round(Math.sqrt(c.options.pieces)):3,d=c.options.pieces?Math.round(Math.sqrt(c.options.pieces)):3;
            c.options.mode=c.options.mode=="toggle"?b(this).is(":visible")?"hide":"show":c.options.mode;
            var a=b(this).show().css("visibility","hidden"),e=a.offset();
            e.top-=parseInt(a.css("marginTop"),10)||0;
            e.left-=parseInt(a.css("marginLeft"),10)||0;
            for(var g=a.outerWidth(true),i=a.outerHeight(true),k=0;k<f;k++)for(var j=
                0;j<d;j++)a.clone().appendTo("body").wrap("<div></div>").css({
                position:"absolute",
                visibility:"visible",
                left:-j*(g/d),
                top:-k*(i/f)
                }).parent().addClass("ui-effects-explode").css({
                position:"absolute",
                overflow:"hidden",
                width:g/d,
                height:i/f,
                left:e.left+j*(g/d)+(c.options.mode=="show"?(j-Math.floor(d/2))*(g/d):0),
                top:e.top+k*(i/f)+(c.options.mode=="show"?(k-Math.floor(f/2))*(i/f):0),
                opacity:c.options.mode=="show"?0:1
                }).animate({
                left:e.left+j*(g/d)+(c.options.mode=="show"?0:(j-Math.floor(d/2))*(g/d)),
                top:e.top+
                k*(i/f)+(c.options.mode=="show"?0:(k-Math.floor(f/2))*(i/f)),
                opacity:c.options.mode=="show"?1:0
                },c.duration||500);
            setTimeout(function(){
                c.options.mode=="show"?a.css({
                    visibility:"visible"
                }):a.css({
                    visibility:"visible"
                }).hide();
                c.callback&&c.callback.apply(a[0]);
                a.dequeue();
                b("div.ui-effects-explode").remove()
                },c.duration||500)
            })
        }
    })(jQuery);
(function(b){
    b.effects.fold=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left"],a=b.effects.setMode(f,c.options.mode||"hide"),e=c.options.size||15,g=!!c.options.horizFirst,i=c.duration?c.duration/2:b.fx.speeds._default/2;
            b.effects.save(f,d);
            f.show();
            var k=b.effects.createWrapper(f).css({
                overflow:"hidden"
            }),j=a=="show"!=g,h=j?["width","height"]:["height","width"];
            j=j?[k.width(),k.height()]:[k.height(),k.width()];
            var l=/([0-9]+)%/.exec(e);
            if(l)e=parseInt(l[1],10)/100*
                j[a=="hide"?0:1];
            if(a=="show")k.css(g?{
                height:0,
                width:e
            }:{
                height:e,
                width:0
            });
            g={};
            
            l={};
            
            g[h[0]]=a=="show"?j[0]:e;
            l[h[1]]=a=="show"?j[1]:0;
            k.animate(g,i,c.options.easing).animate(l,i,c.options.easing,function(){
                a=="hide"&&f.hide();
                b.effects.restore(f,d);
                b.effects.removeWrapper(f);
                c.callback&&c.callback.apply(f[0],arguments);
                f.dequeue()
                })
            })
        }
    })(jQuery);
(function(b){
    b.effects.highlight=function(c){
        return this.queue(function(){
            var f=b(this),d=["backgroundImage","backgroundColor","opacity"],a=b.effects.setMode(f,c.options.mode||"show"),e={
                backgroundColor:f.css("backgroundColor")
                };
                
            if(a=="hide")e.opacity=0;
            b.effects.save(f,d);
            f.show().css({
                backgroundImage:"none",
                backgroundColor:c.options.color||"#ffff99"
                }).animate(e,{
                queue:false,
                duration:c.duration,
                easing:c.options.easing,
                complete:function(){
                    a=="hide"&&f.hide();
                    b.effects.restore(f,d);
                    a=="show"&&!b.support.opacity&&
                    this.style.removeAttribute("filter");
                    c.callback&&c.callback.apply(this,arguments);
                    f.dequeue()
                    }
                })
        })
    }
})(jQuery);
(function(b){
    b.effects.pulsate=function(c){
        return this.queue(function(){
            var f=b(this),d=b.effects.setMode(f,c.options.mode||"show");
            times=(c.options.times||5)*2-1;
            duration=c.duration?c.duration/2:b.fx.speeds._default/2;
            isVisible=f.is(":visible");
            animateTo=0;
            if(!isVisible){
                f.css("opacity",0).show();
                animateTo=1
                }
                if(d=="hide"&&isVisible||d=="show"&&!isVisible)times--;
            for(d=0;d<times;d++){
                f.animate({
                    opacity:animateTo
                },duration,c.options.easing);
                animateTo=(animateTo+1)%2
                }
                f.animate({
                opacity:animateTo
            },duration,
            c.options.easing,function(){
                animateTo==0&&f.hide();
                c.callback&&c.callback.apply(this,arguments)
                });
            f.queue("fx",function(){
                f.dequeue()
                }).dequeue()
            })
        }
    })(jQuery);
(function(b){
    b.effects.puff=function(c){
        return this.queue(function(){
            var f=b(this),d=b.effects.setMode(f,c.options.mode||"hide"),a=parseInt(c.options.percent,10)||150,e=a/100,g={
                height:f.height(),
                width:f.width()
                };
                
            b.extend(c.options,{
                fade:true,
                mode:d,
                percent:d=="hide"?a:100,
                from:d=="hide"?g:{
                    height:g.height*e,
                    width:g.width*e
                    }
                });
        f.effect("scale",c.options,c.duration,c.callback);
            f.dequeue()
            })
    };
    
b.effects.scale=function(c){
    return this.queue(function(){
        var f=b(this),d=b.extend(true,{},c.options),a=b.effects.setMode(f,
            c.options.mode||"effect"),e=parseInt(c.options.percent,10)||(parseInt(c.options.percent,10)==0?0:a=="hide"?0:100),g=c.options.direction||"both",i=c.options.origin;
        if(a!="effect"){
            d.origin=i||["middle","center"];
            d.restore=true
            }
            i={
            height:f.height(),
            width:f.width()
            };
            
        f.from=c.options.from||(a=="show"?{
            height:0,
            width:0
        }:i);
        e={
            y:g!="horizontal"?e/100:1,
            x:g!="vertical"?e/100:1
            };
            
        f.to={
            height:i.height*e.y,
            width:i.width*e.x
            };
            
        if(c.options.fade){
            if(a=="show"){
                f.from.opacity=0;
                f.to.opacity=1
                }
                if(a=="hide"){
                f.from.opacity=
                1;
                f.to.opacity=0
                }
            }
        d.from=f.from;
    d.to=f.to;
    d.mode=a;
    f.effect("size",d,c.duration,c.callback);
        f.dequeue()
        })
};

b.effects.size=function(c){
    return this.queue(function(){
        var f=b(this),d=["position","top","left","width","height","overflow","opacity"],a=["position","top","left","overflow","opacity"],e=["width","height","overflow"],g=["fontSize"],i=["borderTopWidth","borderBottomWidth","paddingTop","paddingBottom"],k=["borderLeftWidth","borderRightWidth","paddingLeft","paddingRight"],j=b.effects.setMode(f,
            c.options.mode||"effect"),h=c.options.restore||false,l=c.options.scale||"both",m=c.options.origin,n={
            height:f.height(),
            width:f.width()
            };
            
        f.from=c.options.from||n;
        f.to=c.options.to||n;
        if(m){
            m=b.effects.getBaseline(m,n);
            f.from.top=(n.height-f.from.height)*m.y;
            f.from.left=(n.width-f.from.width)*m.x;
            f.to.top=(n.height-f.to.height)*m.y;
            f.to.left=(n.width-f.to.width)*m.x
            }
            var o={
            from:{
                y:f.from.height/n.height,
                x:f.from.width/n.width
                },
            to:{
                y:f.to.height/n.height,
                x:f.to.width/n.width
                }
            };
        
    if(l=="box"||l=="both"){
        if(o.from.y!=
            o.to.y){
            d=d.concat(i);
            f.from=b.effects.setTransition(f,i,o.from.y,f.from);
            f.to=b.effects.setTransition(f,i,o.to.y,f.to)
            }
            if(o.from.x!=o.to.x){
            d=d.concat(k);
            f.from=b.effects.setTransition(f,k,o.from.x,f.from);
            f.to=b.effects.setTransition(f,k,o.to.x,f.to)
            }
        }
    if(l=="content"||l=="both")if(o.from.y!=o.to.y){
        d=d.concat(g);
        f.from=b.effects.setTransition(f,g,o.from.y,f.from);
        f.to=b.effects.setTransition(f,g,o.to.y,f.to)
        }
        b.effects.save(f,h?d:a);
    f.show();
    b.effects.createWrapper(f);
    f.css("overflow","hidden").css(f.from);
    if(l=="content"||l=="both"){
        i=i.concat(["marginTop","marginBottom"]).concat(g);
        k=k.concat(["marginLeft","marginRight"]);
        e=d.concat(i).concat(k);
        f.find("*[width]").each(function(){
            child=b(this);
            h&&b.effects.save(child,e);
            var p={
                height:child.height(),
                width:child.width()
                };
                
            child.from={
                height:p.height*o.from.y,
                width:p.width*o.from.x
                };
                
            child.to={
                height:p.height*o.to.y,
                width:p.width*o.to.x
                };
                
            if(o.from.y!=o.to.y){
                child.from=b.effects.setTransition(child,i,o.from.y,child.from);
                child.to=b.effects.setTransition(child,
                    i,o.to.y,child.to)
                }
                if(o.from.x!=o.to.x){
                child.from=b.effects.setTransition(child,k,o.from.x,child.from);
                child.to=b.effects.setTransition(child,k,o.to.x,child.to)
                }
                child.css(child.from);
            child.animate(child.to,c.duration,c.options.easing,function(){
                h&&b.effects.restore(child,e)
                })
            })
        }
        f.animate(f.to,{
        queue:false,
        duration:c.duration,
        easing:c.options.easing,
        complete:function(){
            f.to.opacity===0&&f.css("opacity",f.from.opacity);
            j=="hide"&&f.hide();
            b.effects.restore(f,h?d:a);
            b.effects.removeWrapper(f);
            c.callback&&
            c.callback.apply(this,arguments);
            f.dequeue()
            }
        })
})
}
})(jQuery);
(function(b){
    b.effects.shake=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left"];
            b.effects.setMode(f,c.options.mode||"effect");
            var a=c.options.direction||"left",e=c.options.distance||20,g=c.options.times||3,i=c.duration||c.options.duration||140;
            b.effects.save(f,d);
            f.show();
            b.effects.createWrapper(f);
            var k=a=="up"||a=="down"?"top":"left",j=a=="up"||a=="left"?"pos":"neg";
            a={};
            
            var h={},l={};
            
            a[k]=(j=="pos"?"-=":"+=")+e;
            h[k]=(j=="pos"?"+=":"-=")+e*2;
            l[k]=(j=="pos"?"-=":"+=")+
            e*2;
            f.animate(a,i,c.options.easing);
            for(e=1;e<g;e++)f.animate(h,i,c.options.easing).animate(l,i,c.options.easing);
            f.animate(h,i,c.options.easing).animate(a,i/2,c.options.easing,function(){
                b.effects.restore(f,d);
                b.effects.removeWrapper(f);
                c.callback&&c.callback.apply(this,arguments)
                });
            f.queue("fx",function(){
                f.dequeue()
                });
            f.dequeue()
            })
        }
    })(jQuery);
(function(b){
    b.effects.slide=function(c){
        return this.queue(function(){
            var f=b(this),d=["position","top","left"],a=b.effects.setMode(f,c.options.mode||"show"),e=c.options.direction||"left";
            b.effects.save(f,d);
            f.show();
            b.effects.createWrapper(f).css({
                overflow:"hidden"
            });
            var g=e=="up"||e=="down"?"top":"left";
            e=e=="up"||e=="left"?"pos":"neg";
            var i=c.options.distance||(g=="top"?f.outerHeight({
                margin:true
            }):f.outerWidth({
                margin:true
            }));
            if(a=="show")f.css(g,e=="pos"?-i:i);
            var k={};
            
            k[g]=(a=="show"?e=="pos"?
                "+=":"-=":e=="pos"?"-=":"+=")+i;
            f.animate(k,{
                queue:false,
                duration:c.duration,
                easing:c.options.easing,
                complete:function(){
                    a=="hide"&&f.hide();
                    b.effects.restore(f,d);
                    b.effects.removeWrapper(f);
                    c.callback&&c.callback.apply(this,arguments);
                    f.dequeue()
                    }
                })
        })
    }
})(jQuery);
(function(b){
    b.effects.transfer=function(c){
        return this.queue(function(){
            var f=b(this),d=b(c.options.to),a=d.offset();
            d={
                top:a.top,
                left:a.left,
                height:d.innerHeight(),
                width:d.innerWidth()
                };
                
            a=f.offset();
            var e=b('<div class="ui-effects-transfer"></div>').appendTo(document.body).addClass(c.options.className).css({
                top:a.top,
                left:a.left,
                height:f.innerHeight(),
                width:f.innerWidth(),
                position:"absolute"
            }).animate(d,c.duration,c.options.easing,function(){
                e.remove();
                c.callback&&c.callback.apply(f[0],arguments);
                f.dequeue()
                })
            })
        }
    })(jQuery);
