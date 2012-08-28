<?php
/********************************************************
	lang/tr/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	Turkish Translation by Ufuk Yuzereroglu <ufuk@yuzereroglu.com>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/compose.inc");

$composeHStrings[0] = "Konu";
$composeHStrings[1] = "Kimden";
$composeHStrings[2] = "Kime";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "Tarih";


$composeStrings[0] = "Mesaj Olustur";
$composeStrings[1] = "Mesaji Gönder";
$composeStrings[2] = "Dosya Yükle";
$composeStrings[3] = "Imzami ekle";
$composeStrings[4] = "Dosya Ekle";
$composeStrings[5] = "Adresler";
$composeStrings[6] = "Adresleri sakla";
$composeStrings[7] = "Mesaj:";
$composeStrings[8] = "Ekle";
$composeStrings[9] = "%d tarihinde %s söyle bir mesaj yazmis:\n";
$composeStrings[10] = "-------------------------------\n";
$composeStrings[11] = "Pencereyi Kapat";

$composeStrings[12] = "Hecelemeyi Kontrol Et";
$composeStrings[13] = "Hecelemeyi Düzelt";
$composeStrings[14] = "Degisiklik Yapma";
$composeStrings[15] = "Muhtemel Heceleme Hatalari";
$composeStrings[16] = "Degistirilen Hecelemeler";
$composeStrings[17] = "dikkate alma";
$composeStrings[18] = "sil";

$composeStrings["title"] = "Mesaj Olustur";

$composeErrors[0] = "Konu alani bos. Konu girin ya da \"".$composeStrings[1]."\" tusuna basin.";
$composeErrors[1] = "\"from\" alani bos. Lutfen kendi e-posta adresinizi girin.";
$composeErrors[2] = "\"To\" alani bos. Lütfen mesajin gonderilecegi e-posta adresini girin.";
$composeErrors[3] = "Dosya yükleme basarisiz oldu.";
$composeErrors[4] = "Dosya alinamadi.";
$composeErrors[5] = "Spam engelleme sebebiyle, her %3 saniyede sadece %1 insana (her session icin toplam %2) posta gonderebilirsiniz.";

$composeErrors[6] = "Heceleme hatasi bulunamadi.";
$composeErrors[7] = "Bu dil icin heceleme kontrolü mevcut degil.";
$composeErrors[8] = "(%s sözlügü kullanildi)";
?>
