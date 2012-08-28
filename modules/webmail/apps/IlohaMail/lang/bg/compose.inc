<?php
/********************************************************
	lang/eng/compose.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

//TRANSLATORS:  Uncomment next line for all non-English languages!!
include("../lang/eng/compose.inc");

$composeHStrings[0] = "Тема";
$composeHStrings[1] = "От";
$composeHStrings[2] = "За";
$composeHStrings[3] = "CC";
$composeHStrings[4] = "BCC";
$composeHStrings[5] = "Дата";


$composeStrings[0] = "Състави писмо";
$composeStrings[1] = "Изпрати писмото";
$composeStrings[2] = "Прикрепи";
$composeStrings[3] = "Добави подписа";
$composeStrings[4] = "Прикрепи";
$composeStrings[5] = "Контакти";
$composeStrings[6] = "Скрий контактите";
$composeStrings[7] = "Тяло на писмото:";
$composeStrings[8] = "Добави";
$composeStrings[9] = "On %d, %s wrote:\n";
$composeStrings[10] = "--- Original Message ---\n";
$composeStrings[11] = "Затвори прозореца";

$composeStrings[12] = "Провери правописа";
$composeStrings[13] = "Поправи правописа";
$composeStrings[14] = "Без промяна";
$composeStrings[15] = "Възможни правописни грешки";
$composeStrings[16] = "Промени в правописа";
$composeStrings[17] = "игнорирай";
$composeStrings[18] = "изтрий";

$composeStrings["title"] = "Състави писмо";

$composeErrors[0] = "Темата на писмото е празна. Моля въведете тема или натиснете \"".$composeStrings[1]."\" отново за да го изпратите.";
$composeErrors[1] = "\"От\" полето е празно.  Моля въведете вашия email адрес.";
$composeErrors[2] = "\"До\" полето е празно.  Моля посочете адреса на получателя.";
$composeErrors[3] = "Качването пропадна";
$composeErrors[4] = "Не са получени никакви файлове.";
$composeErrors[5] = "За предотвратяване на спам, вие може да изпратите само до %1 човека (%2 общо за сесия) всеки %3 секунди.";

$composeErrors[6] = "Не са намерени грешки в правописа.";
$composeErrors[7] = "Проверката на правописа не е достъпна за този език.";
$composeErrors[8] = "(%s речник се използва)";
?>
