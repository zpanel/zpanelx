var row_class="even";
function showDetails(a,b,c,e,g,p,n,f){
    var d=$(n);
    d.toggleClass("selected");
    if(d.hasClass("selected")){
        d.hasClass("struct_img")&&d.attr("src",pmaThemeImage+"new_struct_selected.jpg");
        d.hasClass("data_img")&&d.attr("src",pmaThemeImage+"new_data_selected.jpg")
        }else{
        d.hasClass("struct_img")&&d.attr("src",pmaThemeImage+"new_struct.jpg");
        d.hasClass("data_img")&&d.attr("src",pmaThemeImage+"new_data.jpg")
        }
        n=document.getElementById("list").getElementsByTagName("table")[0].getElementsByTagName("tbody")[0];
    row_class=
    row_class=="even"?"odd":"even";
    if(d.hasClass("selected")){
        d=document.createElement("tr");
        d.setAttribute("class",row_class);
        d.className=row_class;
        d.setAttribute("id",a);
        a=document.createElement("td");
        a.align="center";
        a.innerHTML=f;
        d.appendChild(a);
        a=document.createElement("td");
        a.align="center";
        f=document.createElement("td");
        f.align="center";
        var h=document.createElement("td");
        h.align="center";
        var i=document.createElement("td");
        i.align="center";
        var j=document.createElement("td");
        j.align="center";
        var k=
        document.createElement("td");
        k.align="center";
        var l=document.createElement("td");
        l.align="center";
        var m=document.createElement("td");
        m.align="center";
        var o=document.createElement("img");
        o.src=pmaThemeImage+"s_success.png";
        if(b==""&&c==""&&e==""){
            a.appendChild(o);
            f.innerHTML="--";
            h.innerHTML="--";
            i.innerHTML="--";
            k.innerHTML="--";
            j.innerHTML="--";
            l.innerHTML="--";
            m.innerHTML="--"
            }else if(b==""&&e==""){
            a.innerHTML="--";
            f.innerHTML="--";
            h.innerHTML="--";
            i.innerHTML="--";
            j.innerHTML="--";
            k.innerHTML=
            "--";
            l.innerHTML="--";
            m.innerHTML=c
            }else if(e==""){
            a.innerHTML="--";
            f.innerHTML="--";
            h.innerHTML="--";
            i.innerHTML="--";
            j.innerHTML="--";
            k.innerHTML="--";
            l.innerHTML=b;
            m.innerHTML=c
            }else{
            a.innerHTML="--";
            f.innerHTML=c;
            h.innerHTML=e;
            i.innerHTML=b;
            k.innerHTML=p;
            j.innerHTML=g;
            l.innerHTML="--";
            m.innerHTML="--"
            }
            d.appendChild(a);
        d.appendChild(f);
        d.appendChild(h);
        d.appendChild(i);
        d.appendChild(k);
        d.appendChild(j);
        d.appendChild(l);
        d.appendChild(m);
        n.appendChild(d)
        }else{
        b=n.getElementsByTagName("tr");
        for(c=
            c=0;c<b.length;c++)b[c].id==a&&b[c].parentNode.removeChild(b[c]);
        for(c=0;c<b.length;c++){
            row_class_element=b[c].getAttribute("class");
            if(row_class_element=="even"){
                b[c].setAttribute("class","odd");
                b[c].className="odd"
                }else{
                b[c].setAttribute("class","even");
                b[c].className="even"
                }
            }
        }
}
function ApplySelectedChanges(a){
    var b=document.getElementById("list").getElementsByTagName("table")[0].getElementsByTagName("tbody")[0].getElementsByTagName("tr"),c=b.length,e,g="?token="+a+"&Table_ids=1";
    for(e=0;e<c;e++){
        g+="&";
        g+=e+"="+b[e].id
        }
        b=document.getElementById("delete_rows");
    g+=b.checked?"&checked=true":"&checked=false";
    location.href+=a;
    location.href+=g
    }
function validateSourceOrTarget(a){
    var b=true;
    if($("#"+a+"_type").val()!="cur")if($("input[name='"+a+"_username']").val()==""||$("input[name='"+a+"_pass']").val()==""||$("input[name='"+a+"_db']").val()==""||$("input[name='"+a+"_host']").val()==""&&$("input[name='"+a+"_socket']").val()=="")b=false;
    return b
    }
    function validateConnectionParams(){
    var a=true;
    if(!validateSourceOrTarget("src")||!validateSourceOrTarget("trg"))a=false;
    a||alert(PMA_messages.strFormEmpty);
    return a
    }
function hideOrDisplayServerFields(a,b){
    $tbody=a.closest("tbody");
    if(b=="cur"){
        $tbody.children(".current-server").css("display","");
        $tbody.children(".remote-server").css("display","none")
        }else if(b=="rmt"){
        $tbody.children(".current-server").css("display","none");
        $tbody.children(".remote-server").css("display","")
        }else{
        $tbody.children(".current-server").css("display","none");
        $tbody.children(".remote-server").css("display","");
        var c=b.split("||||");
        $tbody.find(".server-host").val(c[0]);
        $tbody.find(".server-port").val(c[1]);
        $tbody.find(".server-socket").val(c[2]);
        $tbody.find(".server-user").val(c[3]);
        $tbody.find(".server-pass").val("");
        $tbody.find(".server-db").val(c[4])
        }
    }
$(document).ready(function(){
    $(".server_selector").change(function(a){
        var b=$(a.target).val();
        hideOrDisplayServerFields($(a.target),b)
        });
    $(".server_selector").each(function(){
        var a=$(this).val();
        hideOrDisplayServerFields($(this),a)
        });
    $(".struct_img").hover(function(){
        var a=$(this);
        a.addClass("hover");
        a.hasClass("selected")?a.attr("src",pmaThemeImage+"new_struct_selected_hovered.jpg"):a.attr("src",pmaThemeImage+"new_struct_hovered.jpg")
        },function(){
        var a=$(this);
        a.removeClass("hover");
        a.hasClass("selected")?
        a.attr("src",pmaThemeImage+"new_struct_selected.jpg"):a.attr("src",pmaThemeImage+"new_struct.jpg")
        });
    $(".data_img").hover(function(){
        var a=$(this);
        a.addClass("hover");
        a.hasClass("selected")?a.attr("src",pmaThemeImage+"new_data_selected_hovered.jpg"):a.attr("src",pmaThemeImage+"new_data_hovered.jpg")
        },function(){
        var a=$(this);
        a.removeClass("hover");
        a.hasClass("selected")?a.attr("src",pmaThemeImage+"new_data_selected.jpg"):a.attr("src",pmaThemeImage+"new_data.jpg")
        });
    $("#buttonGo").click(function(a){
        validateConnectionParams()||
        a.preventDefault()
        })
    });
