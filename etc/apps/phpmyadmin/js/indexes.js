function checkIndexName(){
    if(typeof document.forms.index_frm=="undefined")return false;
    var a=document.forms.index_frm.elements["index[Key_name]"],b=document.forms.index_frm.elements["index[Index_type]"];
    if(b.options[0].value=="PRIMARY"&&b.options[0].selected){
        document.forms.index_frm.elements["index[Key_name]"].value="PRIMARY";
        if(typeof a.disabled!="undefined")document.forms.index_frm.elements["index[Key_name]"].disabled=true
            }else{
        if(a.value=="PRIMARY")document.forms.index_frm.elements["index[Key_name]"].value=
            "";
        if(typeof a.disabled!="undefined")document.forms.index_frm.elements["index[Key_name]"].disabled=false
            }
            return true
    }
    onload=checkIndexName;
