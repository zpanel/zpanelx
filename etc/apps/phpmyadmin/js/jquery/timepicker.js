(function(a){
    function g(){}
    a.extend(a.datepicker._defaults,{
        dateFormat:"yy-mm-dd",
        changeMonth:true,
        changeYear:true,
        stepSeconds:1,
        stepMinutes:1,
        stepHours:1,
        time24h:false,
        showTime:false,
        altTimeField:"",
        hourText:"Hour",
        minuteText:"Minute",
        secondText:"Second"
    });
    a.extend(a.datepicker.regional[""],{
        hourText:"Hour",
        minuteText:"Minute",
        secondText:"Second"
    });
    a.datepicker._connectDatepickerOverride=a.datepicker._connectDatepicker;
    a.datepicker._connectDatepicker=function(c,b){
        a.datepicker._connectDatepickerOverride(c,
            b);
        if(this._get(b,"showTime"))b.settings.showButtonPanel=true;
        var d=this._get(b,"showOn");
        if(d=="button"||d=="both"){
            b.trigger.unbind("click");
            b.trigger.click(function(){
                a.datepicker._datepickerShowing&&a.datepicker._lastInput==c?a.datepicker._hideDatepicker(null):a.datepicker._showDatepicker(c);
                return false
                })
            }
        };
    
a.datepicker._showDatepickerOverride=a.datepicker._showDatepicker;
a.datepicker._showDatepicker=function(c){
    a.datepicker._showDatepickerOverride(c);
    c=c.target||c;
    if(c.nodeName.toLowerCase()!=
        "input")c=a("input",c.parentNode)[0];
    if(!a.datepicker._isDisabledDatepicker(c)){
        var b=a.datepicker._getInst(c);
        a.datepicker._get(b,"showTime")&&a.timepicker.show(c)
        }
    };

a.datepicker._checkExternalClickOverride=a.datepicker._checkExternalClick;
a.datepicker._checkExternalClick=function(c){
    a.datepicker._curInst&&a(c.target).parents("#"+a.timepicker._mainDivId).length==0&&a.datepicker._checkExternalClickOverride(c)
    };
    
a.datepicker._hideDatepickerOverride=a.datepicker._hideDatepicker;
a.datepicker._hideDatepicker=
function(c,b){
    var d=this._curInst;
    if(!(!d||c&&d!=a.data(c,PROP_NAME))){
        var e=this._get(d,"showTime");
        if(c===undefined&&e){
            if(d.input){
                d.input.val(this._formatDate(d));
                d.input.trigger("change")
                }
                this._updateAlternate(d);
            e&&a.timepicker.update(this._formatDate(d))
            }
            a.datepicker._hideDatepickerOverride(c,b);
        e&&a.timepicker.hide()
        }
    };

a.datepicker._selectDate=function(c,b){
    var d=this._getInst(a(c)[0]),e=this._get(d,"showTime");
    b=b!=null?b:this._formatDate(d);
    if(!e){
        d.input&&d.input.val(b);
        this._updateAlternate(d)
        }
        var f=
    this._get(d,"onSelect");
    if(f)f.apply(d.input?d.input[0]:null,[b,d]);else d.input&&!e&&d.input.trigger("change");
    if(d.inline)this._updateDatepicker(d);
    else if(!d.stayOpen)if(e)this._updateDatepicker(d);
        else{
        this._hideDatepicker(null,this._get(d,"duration"));
        this._lastInput=d.input[0];
        typeof d.input[0]!="object"&&d.input[0].focus();
        this._lastInput=null
        }
    };
    
a.datepicker._updateDatepickerOverride=a.datepicker._updateDatepicker;
a.datepicker._updateDatepicker=function(c){
    a.datepicker._updateDatepickerOverride(c);
    a.timepicker.resize()
    };
    
g.prototype={
    init:function(){
        this._mainDivId="ui-timepicker-div";
        this._orgSecond=this._orgMinute=this._orgHour=this._orgValue=this._inputId=null;
        this._scolonPos=this._colonPos=-1;
        this._visible=false;
        this.tpDiv=a('<div id="'+this._mainDivId+'" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all ui-helper-hidden-accessible" style="width: 170px; display: none; position: absolute;"></div>');
        this._generateHtml()
        },
    show:function(c){
        var b=a.datepicker._getInst(c);
        this._time24h=a.datepicker._get(b,"time24h");
        this._altTimeField=a.datepicker._get(b,"altTimeField");
        var d=parseInt(a.datepicker._get(b,"stepSeconds"),10)||1,e=parseInt(a.datepicker._get(b,"stepMinutes"),10)||1,f=parseInt(a.datepicker._get(b,"stepHours"),10)||1;
        if(60%d!=0)d=1;
        if(60%e!=0)e=1;
        if(24%f!=0)f=1;
        a("#hourSlider").slider("option","max",24-f);
        a("#hourSlider").slider("option","step",f);
        a("#minuteSlider").slider("option","max",60-e);
        a("#minuteSlider").slider("option","step",e);
        a("#secondSlider").slider("option",
            "max",60-d);
        a("#secondSlider").slider("option","step",d);
        a(".hour_text").html(a.datepicker._get(b,"hourText"));
        a(".minute_text").html(a.datepicker._get(b,"minuteText"));
        a(".second_text").html(a.datepicker._get(b,"secondText"));
        this._inputId=c.id;
        if(!this._visible){
            this._parseTime();
            this._orgValue=a("#"+this._inputId).val()
            }
            this.resize();
        a("#"+this._mainDivId).show();
        this._visible=true;
        c=a("#"+a.datepicker._mainDivId);
        b=c.position();
        d=(window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth)+
        a(document).scrollLeft();
        e=this.tpDiv.offset().left+this.tpDiv.outerWidth();
        if(e>d){
            c.css("left",b.left-(e-d)-5);
            this.tpDiv.css("left",c.offset().left+c.outerWidth()+"px")
            }
        },
update:function(c){
    var b=a("#"+this._mainDivId+" span.fragHours").text()+":"+a("#"+this._mainDivId+" span.fragMinutes").text()+":"+a("#"+this._mainDivId+" span.fragSeconds").text();
    this._time24h||(b+=" "+a("#"+this._mainDivId+" span.fragAmpm").text());
    a("#"+this._inputId).val();
    a("#"+this._inputId).val(c+" "+b);
    this._altTimeField&&
    a(this._altTimeField).each(function(){
        a(this).val(b)
        })
    },
hide:function(){
    this._visible=false;
    a("#"+this._mainDivId).hide()
    },
resize:function(){
    var c=a("#"+a.datepicker._mainDivId),b=c.position(),d=a("#"+a.datepicker._mainDivId+" > div.ui-datepicker-header:first-child").height();
    a("#"+this._mainDivId+" > div.ui-datepicker-header:first-child").css("height",d);
    this.tpDiv.css({
        height:c.height(),
        top:b.top,
        left:b.left+c.outerWidth()+"px"
        });
    a("#hourSlider").css("height",this.tpDiv.height()-3.5*d);
    a("#minuteSlider").css("height",
        this.tpDiv.height()-3.5*d);
    a("#secondSlider").css("height",this.tpDiv.height()-3.5*d)
    },
_generateHtml:function(){
    var c="";
    c+='<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix ui-corner-all">';
    c+='<div class="ui-datepicker-title" style="margin:0">';
    c+='<span class="fragHours">08</span><span class="delim">:</span><span class="fragMinutes">45</span>:</span><span class="fragSeconds">45</span> <span class="fragAmpm"></span></div></div><table>';
    c+="<tr><th>";
    c+='<span class="hour_text">Hour</span>';
    c+="</th><th>";
    c+='<span class="minute_text">Minute</span>';
    c+="</th><th>";
    c+='<span class="second_text">Second</span>';
    c+="</th></tr>";
    c+='<tr><td align="center"><div id="hourSlider" class="slider"></div></td><td align="center"><div id="minuteSlider" class="slider"></div></td><td align="center"><div id="secondSlider" class="slider"></div></td></tr>';
    c+="</table>";
    this.tpDiv.empty().append(c);
    a("body").append(this.tpDiv);
    var b=this;
    a("#hourSlider").slider({
        orientation:"vertical",
        range:"min",
        min:0,
        max:23,
        step:1,
        slide:function(d,e){
            b._writeTime("hour",e.value)
            },
        stop:function(){
            a("#"+b._inputId).focus()
            }
        });
a("#minuteSlider").slider({
    orientation:"vertical",
    range:"min",
    min:0,
    max:59,
    step:1,
    slide:function(d,e){
        b._writeTime("minute",e.value)
        },
    stop:function(){
        a("#"+b._inputId).focus()
        }
    });
a("#secondSlider").slider({
    orientation:"vertical",
    range:"min",
    min:0,
    max:59,
    step:1,
    slide:function(d,e){
        b._writeTime("second",e.value)
        },
    stop:function(){
        a("#"+b._inputId).focus()
        }
    });
a("#hourSlider > a").css("padding",
    0);
a("#minuteSlider > a").css("padding",0);
a("#secondSlider > a").css("padding",0)
},
_writeTime:function(c,b){
    if(c=="hour"){
        if(this._time24h)a("#"+this._mainDivId+" span.fragAmpm").text("");
        else{
            if(b<12)a("#"+this._mainDivId+" span.fragAmpm").text("am");
            else{
                a("#"+this._mainDivId+" span.fragAmpm").text("pm");
                b-=12
                }
                if(b==0)b=12
                }
                if(b<10)b="0"+b;
        a("#"+this._mainDivId+" span.fragHours").text(b)
        }
        if(c=="minute"){
        if(b<10)b="0"+b;
        a("#"+this._mainDivId+" span.fragMinutes").text(b)
        }
        if(c=="second"){
        if(b<10)b=
            "0"+b;
        a("#"+this._mainDivId+" span.fragSeconds").text(b)
        }
    },
_parseTime:function(){
    var c=a("#"+this._inputId).val();
    this._colonPos=c.search(":");
    var b=0,d=0,e=0,f="";
    if(this._colonPos!=-1){
        this._scolonPos=c.substring(this._colonPos+1).search(":");
        d=parseInt(c.substr(this._colonPos-2,2),10);
        b=parseInt(c.substr(this._colonPos+1,2),10);
        if(this._scolonPos!=-1){
            this._scolonPos+=this._colonPos+1;
            e=parseInt(c.substr(this._scolonPos+1,2),10);
            f=jQuery.trim(c.substr(this._scolonPos+3,3))
            }else f=jQuery.trim(c.substr(this._colonPos+
            3,3))
        }
        f=f.toLowerCase();
    if(f!="am"&&f!="pm")f="";
    if(d<0)d=0;
    if(b<0)b=0;
    if(d>23)d=23;
    if(b>59)b=59;
    if(f=="pm"&&d<12)d+=12;
    if(f=="am"&&d==12)d=0;
    this._setTime("hour",d);
    this._setTime("minute",b);
    this._setTime("second",e);
    this._orgHour=d;
    this._orgMinute=b;
    this._orgSecond=e
    },
_setTime:function(c,b){
    if(isNaN(b))b=0;
    if(b<0)b=0;
    if(b>23&&c=="hour")b=23;
    if(b>59&&c=="minute")b=59;
    if(b>59&&c=="second")b=59;
    c=="hour"&&a("#hourSlider").slider("value",b);
    c=="minute"&&a("#minuteSlider").slider("value",b);
    c=="second"&&
    a("#secondSlider").slider("value",b);
    this._writeTime(c,b)
    }
};

a.timepicker=new g;
a("document").ready(function(){
    a.timepicker.init()
    })
})(jQuery);
