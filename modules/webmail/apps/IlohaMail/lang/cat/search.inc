<?php
/********************************************************
        lang/eng/search.inc
        (C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

        This file is part of IlohaMail, and released under GPL.
        See COPYING, or http://www.fsf.org/copyleft/gpl.html

        Translated: David Gimeno i Ayuso info@sima-pc.com

********************************************************/

        //TRANSLATORS:  Uncomment next line for all non-English languages!!
        include("../lang/eng/search.inc");

        //TRANSLATORS:
        //in the follow arrays, translate key, not value
        //i.e.  translate "From" not "FROM".
        $search_fields=array("seleccionar"=>"-"
                            ,"De"=>"FROM"
                            ,"A"=>"TO"
                            ,"Tema"=>"SUBJECT"
                            ,"Cos del missatge"=>"BODY");
        $search_dates=array("no hi fa res"=>"ignore"
                            ,"&eacute;s a"=>"ON"
                            ,"&eacute; despr&eacute;s"=>"SINCE"
                            ,"&eacute; abans"=>"BEFORE");

        $search_str["title"] = "Cercar";
        $search_str["str"] = "Cercar a la carpeta %folders <br>\n";
        $search_str["str"] .= "on el camp %fields contingui %value <br>\n";
        $search_str["str"] .= "i la data %dateops %d/%m/%y <br>\n";
        $search_str["submit"] = "cercar";
?>
