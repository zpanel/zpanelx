var imageMap={
    mouseMoved:function(a,c){
        if(this.imageMap){
            for(var f=a.pageX-c.offsetLeft,g=a.pageY-c.offsetTop,h=false,b=0;b<this.imageMap.length;b++){
                for(var k=this.imageMap[b].n,l=this.imageMap[b].v,j=0,d=0;d<this.imageMap[b].p.length;d++){
                    var e,i;
                    if(d==this.imageMap[b].p.length-1){
                        e=d;
                        i=0
                        }else{
                        e=d;
                        i=d+1
                        }
                        e=this.getDeterminant(this.imageMap[b].p[e][0],this.imageMap[b].p[e][1],this.imageMap[b].p[i][0],this.imageMap[b].p[i][1],f,g);
                    j+=e>0?1:-1
                    }
                    if(Math.abs(j)==this.imageMap[b].p.length){
                    h=true;
                    if(this.currentKey!=
                        b){
                        this.tooltip.show();
                        this.tooltip.title(k);
                        this.tooltip.text(l);
                        this.currentKey=b
                        }
                        this.tooltip.move(f+20,g+20)
                    }
                }
            if(!h&&this.currentKey!=-1){
            this.tooltip.hide();
            this.currentKey=-1
            }
        }
},
getDeterminant:function(a,c,f,g,h,b){
    return f*b-h*g-(a*b-h*c)+(a*g-f*c)
    },
loadImageMap:function(a){
    this.imageMap=JSON.parse(a);
    for(key in this.imageMap);
    },
init:function(){
    this.tooltip.init();
    $("div#chart").bind("mousemove",function(a){
        imageMap.mouseMoved(a,this)
        });
    this.tooltip.attach("div#chart");
    this.currentKey=-1
    },
tooltip:{
    init:function(){
        this.el=$("<div></div>");
        this.el.css("position","absolute");
        this.el.css("font-family","tahoma");
        this.el.css("background-color","#373737");
        this.el.css("color","#BEBEBE");
        this.el.css("padding","3px");
        var a=$("<p></p>");
        a.attr("id","title");
        a.css("margin","0px");
        a.css("padding","3px");
        a.css("background-color","#606060");
        a.css("text-align","center");
        a.html("Title");
        this.el.append(a);
        a=$("<p></p>");
        a.attr("id","text");
        a.css("margin","0");
        a.html("Text");
        this.el.append(a);
        this.hide()
        },
    attach:function(a){
        $(a).prepend(this.el)
        },
    move:function(a,c){
        this.el.css("margin-left",a);
        this.el.css("margin-top",c)
        },
    hide:function(){
        this.el.css("display","none")
        },
    show:function(){
        this.el.css("display","block")
        },
    title:function(a){
        this.el.find("p#title").html(a)
        },
    text:function(a){
        this.el.find("p#text").html(a.replace(/;/g,"<br />"))
        }
    }
};

$(document).ready(function(){
    imageMap.init()
    });
