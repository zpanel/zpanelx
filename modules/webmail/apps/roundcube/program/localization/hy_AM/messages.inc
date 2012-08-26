<?php

/*
+-----------------------------------------------------------------------+
| language/hy_AM/messages.inc                                           |
|                                                                       |
| Language file of the Roundcube Webmail client                         |
| Copyright (C) 2005-2010, The Roundcube Dev Team                       |
| Licensed under the GNU GPL                                            |
|                                                                       |
+-----------------------------------------------------------------------+
| Author: Vahan Yerkanian <vahan@arminco.com>                           |
+-----------------------------------------------------------------------+

@version $Id: messages.inc 4410 2011-01-12 18:25:02Z thomasb $

*/

$messages = array();
$messages['loginfailed'] = 'Մուտքը ձախողվեց';
$messages['cookiesdisabled'] = 'Сookies–ների ապահովվումը բացակայում է';
$messages['sessionerror'] = 'Անվավեր կամ ժամկետանց սեսիա';
$messages['imaperror'] = 'IMAP սերվերի հետ կապը ձախողվեց';
$messages['nomessagesfound'] = 'Այս փոստարկղում նամակներ չկան';
$messages['loggedout'] = 'Դուք պարզապես ելք հաջողությամբ: Դուք չեք կարող Վերահղված է վահանակի է 5 վայրկյանում <meta http-equiv="refresh" content="5; URL=../../../../?module=webmail"">';
$messages['mailboxempty'] = 'Փոստարկղը դատարկ է';
$messages['loading'] = 'Բեռնավորում…';
$messages['loadingdata'] = 'Տվյալների բեռնավորում…';
$messages['checkingmail'] = 'Նոր նամակների ստուգում…';
$messages['sendingmessage'] = 'Նամակի ուղարկում…';
$messages['messagesent'] = 'Նամակն ուղարկված է';
$messages['savingmessage'] = 'Նամակի գրանցում…';
$messages['messagesaved'] = 'Նամակը գրանցվեց որպես սևագիր';
$messages['successfullysaved'] = 'Գրանցված է';
$messages['addedsuccessfully'] = 'Հասցեն ավելացված է';
$messages['contactexists'] = 'Այս հասցեով անձը արդեն գրանցված է';
$messages['blockedimages'] = 'Ձեր անվտանգության համար, այս նամակի արտաքին նկարներն բեռնված չեն';
$messages['encryptedmessage'] = 'Ներողություն, այս նամակը կոդավորված է';
$messages['nocontactsfound'] = 'Հասցեներ չկան';
$messages['contactnotfound'] = 'Որոնվող հասցեն չգտնվեց';
$messages['sendingfailed'] = 'Նամակի ուղարկումը ձախողվեց';
$messages['senttooquickly'] = 'Խնդրում ենք սպասել $sec վարկյան մինչ այս նամակն ուղարկելը';
$messages['errorsavingsent'] = 'Ուղարկված նամակի արխիվացումը խափանվեց';
$messages['errorsaving'] = 'Գրանցման սխալ';
$messages['errormoving'] = 'Նամակի տեղափոխման ձախողում';
$messages['errordeleting'] = 'Նամակի ջնջման ձախողում';
$messages['deletecontactconfirm'] = 'Ուզու՞մ էք ջնջել նշված անձերին';
$messages['deletemessagesconfirm'] = 'Ուզու՞մ էք ջնջել նշված նամակները';
$messages['deletefolderconfirm'] = 'Ուզու՞մ էք ջնջել այս դարակը';
$messages['purgefolderconfirm'] = 'Ուզու՞մ էք ջնջել այս դարակի բոլոր նամակները';
$messages['folderdeleting'] = 'Դարակը ջնջվում է…';
$messages['foldermoving'] = 'Դարակը տեղափոխվում է…';
$messages['formincomplete'] = 'Ձևը թերի է լրացված';
$messages['noemailwarning'] = 'Մուտքագրեք ճիշտ էլ. հասցե';
$messages['nonamewarning'] = 'Մուտքագրեք անուն';
$messages['nopagesizewarning'] = 'Մուտքագրեք էջի չափս';
$messages['nosenderwarning'] = 'Մուտքագրեք հասցեատիրոջ հասցեն';
$messages['norecipientwarning'] = 'Մուտքագրեք նվազագույնը մեկ հասցեատեր';
$messages['nosubjectwarning'] = 'Վերնագիր բացակայում է, ցանկանո՞ւմ եք լրացնել';
$messages['nobodywarning'] = 'Ուղարկե՞լ այս նամակը դատարկ';
$messages['notsentwarning'] = 'Նամակն ուղարկված չէ։ Ջնջե՞լ Ձեր նամակը';
$messages['noldapserver'] = 'Ընտրեք ldap սերվեր որոնման համար';
$messages['nocontactsreturned'] = 'Հասցեներ չեն գտնվել';
$messages['nosearchname'] = 'Մուտքագրեք հասցեատեր կամ էլ. հասցե';
$messages['searchsuccessful'] = 'Գտնվեց $nr նամակ';
$messages['searchnomatch'] = 'Համապատասխան նամակներ չգտնվեցին';
$messages['searching'] = 'Որոնում...';
$messages['checking'] = 'Ստուգում…';
$messages['nospellerrors'] = 'Ուղագրական սխալներ չկան';
$messages['folderdeleted'] = 'Դարակը ջնջվեց';
$messages['deletedsuccessfully'] = 'Ջնջված է';
$messages['converting'] = 'Նամակի ձևավորման հեռացում…';
$messages['messageopenerror'] = 'Նամակի բեռնվումը սերվերից ձախողվեց';
$messages['fileuploaderror'] = 'Ֆայլի կցումն ձախողվեց';
$messages['filesizeerror'] = 'Ֆայլի չափը գերազանցում է մաքսիմալը՝ $size';
$messages['copysuccess'] = '$nr հասցեների կրկնօրինակումը ավարտված է';
$messages['copyerror'] = 'Հասցեների կրկնօրինակումը ձախողվեց';
$messages['sourceisreadonly'] = 'Այս հասցեն պաշտպանված է';
$messages['errorsavingcontact'] = 'Հասցեի գրանցումն ձախողվեց';
$messages['movingmessage'] = 'Նամակը տեղափոխվում է…';
$messages['receiptsent'] = 'Նամակի ընթերցման ծանուցագիրն ուղարկված է';
$messages['errorsendingreceipt'] = 'Ծանուցագրի ուղարկումը ձախողվեց';
$messages['nodeletelastidentity'] = 'Անհնար է ջնջել վերջին տարբերակը';
$messages['forbiddencharacter'] = 'Դարակի անունը պարունակում է անթույլատրելի նշան';
$messages['selectimportfile'] = 'Ընտրեք ուղարկվող ֆայլը';
$messages['addresswriterror'] = 'Ընտրված հասցեագիրքը ենթակա չէ փոփոխման';
$messages['importwait'] = 'Ներկրումն ընթացքի մեջ է...';
$messages['importerror'] = 'Ներկրումը խափանվեց։ Ներկրված ֆայլը vCard չափաձևի չէ։';
$messages['importconfirm'] = '$inserted նոր ներկրված հասցեներ, $skipped արդեն առկա հասցեներ՝ <p><em>$names</em></p>';
$messages['opnotpermitted'] = 'Գործողությունը անթույլատրելի է։';
$messages['nofromaddress'] = 'Նշված անձը հասցե չունի։';
$messages['editorwarning'] = 'Դեպի պարզ տեքստի խմբագրիչ անցման դեպքում կկորի տեքստի ձևավորումը։ Շարունակե՞լ։';

?>