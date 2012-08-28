<?php
include("../lang/eng/prefs.inc"); // <-- do not delete
/********************************************************
	lang/hu/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

$prefHeaderStrings[0] = "Beállítások";
$prefHeaderStrings[1] = "Általános";
$prefHeaderStrings[2] = "Szinek";
$prefHeaderStrings[3] = "Azonosítók";

											
$prefsStrings["0.0"] = "Személyes adatok";
$prefsStrings["0.1"] = "Név: ";
$prefsStrings["0.2"] = "Email: ";
$prefsStrings["0.3"] = "A \"%s\" oldal megváltoztatható.";
$prefsStrings["0.4"] = "Nincs megadva";
$prefsStrings["1.0"] = "Nemzetközi";
$prefsStrings["1.1"] = "Nyelv: ";
$prefsStrings["1.2"] = "Karakterkészlet: ";
$prefsStrings["1.3"] = "Idözóna: ";
$prefsStrings["1.4"] = "Óra beállítás: ";
$prefsStrings["1.5"] = array(12=>"12 órás", 24=>"24 órás");
$prefsStrings["2.0"] = "Megjelenítési választások";
$prefsStrings["2.1"] = "Mutasson ";
$prefsStrings["2.2"] = " üzenetet";
$prefsStrings["2.3"] = "Méret mutatása";
$prefsStrings["2.4"] = "Sorrend: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Rendezés: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Mappák listájának mutatása";
$prefsStrings["2.9"] = "Mappa lista szélessége";
$prefsStrings["2.10"] = "Új üzenetek keresése %n másodpercenként";
$prefsStrings["2.11"] = "Eszköztár mutatása: %m";
$prefsStrings["2.12"] = array("b"=>"lent", "t"=>"fent", "bt"=>"lent és fent is");
$prefsStrings["2.13"] = "Oszlopok átrendezése: ";
$prefsStrings["2.14"] = "kattints ide";
$prefsStrings["3.0"] = "Automatikus mentés beállítása";
$prefsStrings["3.1"] = "Küldött üzenetek mentése.";
$prefsStrings["3.2"] = "Küldött üzenetek mentése az alábbi mappában: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "szemetes automatikus ürítése ";
$prefsStrings["3.5"] = "Törölt üzenetek áthelyezése: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Alap-mappa: ";
$prefsStrings["3.8"] = "Egyéb";
$prefsStrings["3.9"] = "Feliratkozás nélküli mappák elrejtése";
$prefsStrings["3.10"] = "Kvóta mutatása %m";
$prefsStrings["3.11"] = array(""=>"ne mutassa", "m"=>"mutassa az üzeneteknél", "f"=>"mutassa a mappáknál");
$prefsStrings["4.0"] = "Üzenet megtekintési választások";
$prefsStrings["4.1"] = "Keretben megtekinteni";
$prefsStrings["4.2"] = "Idézés szinezése";
$prefsStrings["4.3"] = "Ez a választás lelassítja a rendszert.";
$prefsStrings["4.4"] = "URL cimekböl linkek készítése";
$prefsStrings["4.5"] = "HTML üzenet mutatása ";
$prefsStrings["4.6"] = "Kép belsö mutatása";
$prefsStrings["4.7"] = "Navigációs linkek mutatása";
$prefsStrings["4.8"] = "Ne jelölje ki a leveleket visszlépéskor";
$prefsStrings["5.0"] = "Aláírás";
$prefsStrings["5.1"] = "Alapértelmezett aláírás hozzáadása";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Új üzenet";
$prefsStrings["6.1"] = "Címjegyzék mutatása";
$prefsStrings["6.2"] = "CC/BCC mezõk mutatása";
$prefsStrings["6.3"] = "Bezárás küldés után";
$prefsStrings["6.4"] = "Üzenet szerkesztés ugyanabban az ablakban";
$prefsStrings["7.0.0"] = "Lemez és mapa beállítások";
$prefsStrings["7.0.1"] = "Kvóta beállítások";
$prefsStrings["7.1"] = "Olvasatlan üzenetek számának mutatása";
$prefsStrings["7.2"] = "Mappa lista frissítése %n másodpercenként";


$sort_fields=array("DATE"=>"Dátum", "SUBJECT"=>"Téma", "SIZE"=>"Méret");
$sort_orders=array("ASC"=>"Felfele", "DESC"=>"Lefele");

$prefsButtonStrings = array( 0=> "Naprakész állapotba hoz", 1=>"Alkalmaz", 2=> "Töröl", 3=> "Alapértelmezett értékek");

$prefs_new_user = "Ugy tünik, hogy elöször jelentkezett be... Üdvözöljük!<br>";
$prefs_new_user .= "Kérjük, hogy menjen végig az alábbi beállításokon, majd nyomja meg az \"Alkalmaz\" gombot!";
?>