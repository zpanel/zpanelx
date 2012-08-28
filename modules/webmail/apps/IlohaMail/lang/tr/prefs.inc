<?php
/********************************************************
	lang/tr/prefs.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	Turkish Translation by Ufuk Yuzereroglu <ufuk@yuzereroglu.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/prefs.inc");

$prefHeaderStrings[0] = "Tercihler";
$prefHeaderStrings[1] = "Genel";
$prefHeaderStrings[3] = "Kimlikler";
$prefHeaderStrings[2] = "Gorunum";

											
$prefsStrings["0.0"] = "Varsayilan Kimlik";
$prefsStrings["0.1"] = "Isim: ";
$prefsStrings["0.2"] = "E-posta adresi: ";
$prefsStrings["0.3"] = "\"%s\" sayfasinda degisiklik yapildi.";
$prefsStrings["0.4"] = "Tanimsiz";
$prefsStrings["1.0"] = "Uluslararasi seçenekler";
$prefsStrings["1.1"] = "Arayüz: ";
$prefsStrings["1.2"] = "Karakter Seti: ";
$prefsStrings["1.3"] = "Zaman dilimi: ";
$prefsStrings["1.4"] = "Zaman seçenegi: ";
$prefsStrings["1.5"] = array(12=>"12 saatlik", 24=>"24 saatlik");
$prefsStrings["2.0"] = "Görüntü seçeneklerini göster";
$prefsStrings["2.1"] = " e kadar göster";
$prefsStrings["2.2"] = " elemanlar";
$prefsStrings["2.3"] = "Boyutu göster";
$prefsStrings["2.4"] = "Sirala: ";
$prefsStrings["2.5"] = "";
$prefsStrings["2.6"] = "Siralama kriteri: ";
$prefsStrings["2.7"] = "";
$prefsStrings["2.8"] = "Klasör listesini göster";
$prefsStrings["2.9"] = "Klasör listesi genisligi";
$prefsStrings["2.10"] = "Her %n saniyede yeni mesajlari kontrol et";
$prefsStrings["2.11"] = "Arac çubugunu %m de göster";
$prefsStrings["2.12"] = array("b"=>"alt", "t"=>"üst", "bt"=>"üst ve alt");
$prefsStrings["2.13"] = "Kolonlari tekrar düzenle: ";
$prefsStrings["2.14"] = "buraya tiklayin";
$prefsStrings["3.0"] = "Otomatik kaydetme seçenekleri";
$prefsStrings["3.1"] = "Gönderilenleri kaydet.";
$prefsStrings["3.2"] = "Gönderilen mesajlari klasöre kaydet: ";
$prefsStrings["3.3"] = "";
$prefsStrings["3.4"] = "Çöp kutusunu otomatik bosalt";
$prefsStrings["3.5"] = "Silinen mesajlari klasöre tasi: ";
$prefsStrings["3.6"] = "";
$prefsStrings["3.7"] = "Ana dizin: ";
$prefsStrings["3.8"] = "Diger";
$prefsStrings["3.9"] = "Kaydedilmeyen klasörleri sakla.";
$prefsStrings["3.10"] = "Kotayi goster %m";
$prefsStrings["3.11"] = array(""=>"gösterme", "m"=>"mesaj listesinde", "f"=>"klasör listesinde");
$prefsStrings["4.0"] = "Mesaj Görüntuleme Secenekleri";
$prefsStrings["4.1"] = "Çerçeve içinde göster";
$prefsStrings["4.2"] = "Alintilari renklendir";
$prefsStrings["4.3"] = "Bu seçenegi seçmek sisteminiz yavaslatabilir.";
$prefsStrings["4.4"] = "URL'leri linke dönüstür";
$prefsStrings["4.5"] = "HTML mesajlarini göster";
$prefsStrings["4.6"] = "Resimleri mesaj icinde göster";
$prefsStrings["4.7"] = "Navigasyon linklerini göster";
$prefsStrings["4.8"] = "Ilerlerken mesaji isaretleme.";
$prefsStrings["5.0"] = "Imza";
$prefsStrings["5.1"] = "Imzayi göster";
$prefsStrings["5.2"] = "";
$prefsStrings["6.0"] = "Yeni";
$prefsStrings["6.1"] = "Adres defterini göster";
$prefsStrings["6.2"] = "CC/BCC alanlarini göster";
$prefsStrings["6.3"] = "Gönderdikten sonra kapat";
$prefsStrings["6.4"] = "Çerçeve içinde olustur";
$prefsStrings["7.0.0"] = "Disk &amp; Klasör Secenekleri";
$prefsStrings["7.0.1"] = "Kota seçenekleri";
$prefsStrings["7.1"] = "Okunmayan sayisini göster";
$prefsStrings["7.2"] = "Klasör listesini her %n saniyede yenile";

$pref_col_title = "Kolonlari Yeniden Duzenle";
$pref_colstr["order"] = "Sirala";
$pref_colstr["field"] = "Alan";
$pref_col_label['c'] = "Onay Kutusu";
$pref_col_label['a'] = "Ekler";
$pref_col_label['m'] = "Etiket/durum";

$sort_fields=array("DATE"=>"Tarih", "SUBJECT"=>"Konu", "SIZE"=>"Boyut");
$sort_orders=array("ASC"=>"Büyüyen", "DESC"=>"Küçülen");

$prefsButtonStrings = array( 0=> "Güncelle", 1=>"Uygula", 2=> "Iptal", 3=> "Varsayilanlar");

$prefs_new_user = "Sisteme ilk defa girdiniz... Hosgeldiniz!<br>";
$prefs_new_user .= "Asagidaki ayarlari yapip \"Apply\" tusuna basiniz.";
?>
