<?php


class Language
{


	function folder_language($popup, $language, $type)
	{
		// Change the folder names, depending on the language

		if ( $language == "italiano" && !$type )
		{
		    $popup = str_replace('>Inbox<', '>Posta in Arrivo<', $popup);
		    $popup = str_replace('>Sent<', '>Posta Inviata<', $popup);
		    $popup = str_replace('>Trash<', '>Cestino<', $popup);
		    $popup = str_replace('>Drafts<', '>Posta Archiviata<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>cancellare selezionato<', $popup);

		    return $popup;
		}

		if ( $language == "italiano" && $type )
		{
		    $popup =  str_replace('Inbox', 'Posta in Arrivo', $popup);
		    $popup =  str_replace('Sent', 'Posta Inviata', $popup);
		    $popup =  str_replace('Trash', 'Cestino', $popup);
		    $popup =  str_replace('Drafts', 'Posta Archiviata', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    $popup =  str_replace('Erase Selected', 'cancellare selezionato', $popup);

		    return $popup;
		}

		if ( $language == "french" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Inbox<', $popup);
		    $popup =  str_replace('>Sent<', '>Sent<', $popup);
		    $popup =  str_replace('>Trash<', '>Recyclage<', $popup);
		    $popup =  str_replace('>Drafts<', '>Brouillons<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>effacer choisi<', $popup);

		    return $popup;
		}

		if ( $language == "french" && $type )
		{
		    $popup =  str_replace('Inbox', 'Inbox', $popup);
		    $popup =  str_replace('Sent', 'Sent', $popup);
		    $popup =  str_replace('Trash', 'Recyclage', $popup);
		    $popup =  str_replace('Drafts', 'Brouillons', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    $popup =  str_replace('Erase Selected', 'effacer choisi', $popup);

		    return $popup;
		}

		if ( $language == "japanese" && $type )
		{
		    $popup =  str_replace('Inbox', '受信箱', $popup);
		    $popup =  str_replace('Sent', '送信済', $popup);
		    $popup =  str_replace('Trash', 'ゴミ箱', $popup);
		    $popup =  str_replace('Drafts', '下書き', $popup);
		    $popup =  str_replace('Spam', '迷惑メール', $popup);
		    $popup =  str_replace('Erase Selected', '選ばれる消しなさい', $popup);

		    return $popup;
		}

		if ( $language == "japanese" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>受信箱<', $popup);
		    $popup =  str_replace('>Sent<', '>送信済<', $popup);
		    $popup =  str_replace('>Trash<', '>ゴミ箱<', $popup);
		    $popup =  str_replace('>Drafts<', '>下書き<', $popup);
		    $popup =  str_replace('>Spam<', '>迷惑メール<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>選ばれる消しなさい<', $popup);

		    return $popup;
		}

		if ( $language == "espanol" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Inbox<', $popup);
		    $popup =  str_replace('>Sent<', '>Enviados<', $popup);
		    $popup =  str_replace('>Trash<', '>Papelera<', $popup);
		    $popup =  str_replace('>Drafts<', '>Borrador<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>borrar seleccionado<', $popup);

		    return $popup;
		}

		if ( $language == "espanol" && $type )
		{
		    $popup =  str_replace('Inbox', 'Inbox', $popup);
		    $popup =  str_replace('Sent', 'Enviados', $popup);
		    $popup =  str_replace('Trash', 'Papelera', $popup);
		    $popup =  str_replace('Drafts', 'Borrador', $popup);
		    //$popup =  str_replace('Spam', '?????', $popup);
		    //$popup =  str_replace('Erase Selected', 'borrar seleccionado', $popup);

		    return $popup;
		}

		if ( $language == "arabic" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>صندوق الوارد<', $popup);
		    $popup =  str_replace('>Sent<', '>المرسل<', $popup);
		    $popup =  str_replace('>Trash<', '>المحذوفات<', $popup);
		    $popup =  str_replace('>Drafts<', '>المحفوظات<', $popup);
		    $popup =  str_replace('>Spam<', '>بريد اعلاني<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>تمحو مختارة<', $popup);

		    return $popup;
		}

		if ( $language == "arabic" && $type )
		{
		    $popup =  str_replace('Inbox', 'صندوق الوارد', $popup);
		    $popup =  str_replace('Sent', 'المرسل', $popup);
		    $popup =  str_replace('Trash', 'المحذوفات', $popup);
		    $popup =  str_replace('Drafts', 'المحفوظات', $popup);
		    $popup =  str_replace('Spam', 'بريد اعلاني', $popup);
		    $popup =  str_replace('Erase Selected', 'تمحو مختارة', $popup);

		    return $popup;
		}

		if ( $language == "danish" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Indbakke<', $popup);
		    $popup =  str_replace('>Sent<', '>Sendt<', $popup);
		    $popup =  str_replace('>Trash<', '>Affald<', $popup);
		    $popup =  str_replace('>Drafts<', '>Udkast<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "danish" && $type )
		{
		    $popup =  str_replace('Inbox', 'Indbakke', $popup);
		    $popup =  str_replace('Sent', 'Sendt', $popup);
		    $popup =  str_replace('Trash', 'Affald', $popup);
		    $popup =  str_replace('Drafts', 'Udkast', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}

		if ( $language == "dutch" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>PostvakIN<', $popup);
		    $popup =  str_replace('>Sent<', '>Verstuurd<', $popup);
		    $popup =  str_replace('>Trash<', '>Verwijderde items<', $popup);
		    $popup =  str_replace('>Drafts<', '>Concepten<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "dutch" && $type )
		{
		    $popup =  str_replace('Inbox', 'PostvakIN', $popup);
		    $popup =  str_replace('Sent', 'Verstuurd', $popup);
		    $popup =  str_replace('Trash', 'Verwijderde items', $popup);
		    $popup =  str_replace('Drafts', 'Concepten', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}

		if ( $language == "finnish" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Saapuneet<', $popup);
		    $popup =  str_replace('>Sent<', '>Lähetetyt<', $popup);
		    $popup =  str_replace('>Trash<', '>Roskakori<', $popup);
		    $popup =  str_replace('>Drafts<', '>Keskeneräiset<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "finnish" && $type )
		{
		    $popup =  str_replace('Inbox', 'Saapuneet', $popup);
		    $popup =  str_replace('Sent', 'Lähetetyt', $popup);
		    $popup =  str_replace('Trash', 'Roskakori', $popup);
		    $popup =  str_replace('Drafts', 'Keskeneräiset', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}

		if ( $language == "german" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Posteingang<', $popup);
		    $popup =  str_replace('>Sent<', '>Gesendete Nachrichten<', $popup);
		    $popup =  str_replace('>Trash<', '>Gelöschte Nachrichten<', $popup);
		    $popup =  str_replace('>Drafts<', '>Entwürfe<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>Markierte löschen<', $popup);

		    return $popup;
		}

		if ( $language == "german" && $type )
		{
		    $popup =  str_replace('Inbox', 'Posteingang', $popup);
		    $popup =  str_replace('Sent', 'Gesendete Nachrichten', $popup);
		    $popup =  str_replace('Trash', 'Gelöschte Nachrichten', $popup);
		    $popup =  str_replace('Drafts', 'Entwürfe', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    $popup =  str_replace('Erase Selected', 'Markierte löschen', $popup);

		    return $popup;
		}

		if ( $language == "hungarian" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Bejövő<', $popup);
		    $popup =  str_replace('>Sent<', '>Elküldött<', $popup);
		    $popup =  str_replace('>Trash<', '>Szemét<', $popup);
		    $popup =  str_replace('>Drafts<', '>Piszkozat<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "hungarian" && $type )
		{
		    $popup =  str_replace('Inbox', 'Bejövő', $popup);
		    $popup =  str_replace('Sent', 'Elküldött', $popup);
		    $popup =  str_replace('Trash', 'Szemét', $popup);
		    $popup =  str_replace('Drafts', 'Piszkozat', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}

		if ( $language == "polish" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Odebrane<', $popup);
		    $popup =  str_replace('>Sent<', '>Wysłane<', $popup);
		    $popup =  str_replace('>Trash<', '>Kosz<', $popup);
		    $popup =  str_replace('>Drafts<', '>Szkice<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "polish" && $type )
		{
		    $popup =  str_replace('Inbox', 'Odebrane', $popup);
		    $popup =  str_replace('Sent', 'Wysłane', $popup);
		    $popup =  str_replace('Trash', 'Kosz', $popup);
		    $popup =  str_replace('Drafts', 'Szkice', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}
		if ( $language == "portuguese" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Entrada<', $popup);
		    $popup =  str_replace('>Sent<', '>Enviadas<', $popup);
		    $popup =  str_replace('>Trash<', '>Lixeira<', $popup);
		    $popup =  str_replace('>Drafts<', '>Rascunhos<', $popup);
		    //$popup =  str_replace('>Spam<', '>Spam<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>apagar selecionado<', $popup);

		    return $popup;
		}

		if ( $language == "portuguese" && $type )
		{
		    $popup =  str_replace('Inbox', 'Entrada', $popup);
		    $popup =  str_replace('Sent', 'Enviadas', $popup);
		    $popup =  str_replace('Trash', 'Lixeira', $popup);
		    $popup =  str_replace('Drafts', 'Rascunhos', $popup);
		    //$popup =  str_replace('Spam', 'Spam', $popup);
		    $popup =  str_replace('Erase Selected', 'apagar selecionado', $popup);

		    return $popup;
		}

		if ( $language == "russian" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Входящие<', $popup);
		    $popup =  str_replace('>Sent<', '>Отправленые<', $popup);
		    $popup =  str_replace('>Trash<', '>Корзина<', $popup);
		    $popup =  str_replace('>Drafts<', '>Черновики<', $popup);
		    $popup =  str_replace('>Spam<', '>Спам<', $popup);
		    $popup =  str_replace('>Erase Selected<', '>стереть отдельные<', $popup);

		    return $popup;
		}

		if ( $language == "russian" && $type )
		{
		    $popup =  str_replace('Inbox', 'Входящие', $popup);
		    $popup =  str_replace('Sent', 'Отправленые', $popup);
		    $popup =  str_replace('Trash', 'Корзина', $popup);
		    $popup =  str_replace('Drafts', 'Черновики', $popup);
		    $popup =  str_replace('Spam', 'Спам', $popup);
		    $popup =  str_replace('Erase Selected', 'стереть отдельные', $popup);

		    return $popup;
		}
		if ( $language == "swedish" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>Inkorgen<', $popup);
		    $popup =  str_replace('>Sent<', '>Skickat<', $popup);
		    $popup =  str_replace('>Trash<', '>Borttaget<', $popup);
		    $popup =  str_replace('>Drafts<', '>Utkast<', $popup);
		    $popup =  str_replace('>Spam<', '>Skräppost<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "swedish" && $type )
		{
		    $popup =  str_replace('Inbox', 'Inkorgen', $popup);
		    $popup =  str_replace('Sent', 'Skickat', $popup);
		    $popup =  str_replace('Trash', 'Borttaget', $popup);
		    $popup =  str_replace('Drafts', 'Utkast', $popup);
		    $popup =  str_replace('Spam', 'Skräppost', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}
		if ( $language == "thai" && !$type )
		{
		    $popup =  str_replace('>Inbox<', '>รายการขาเข้า<', $popup);
		    $popup =  str_replace('>Sent<', '>รายการที่ถูกส่ง<', $popup);
		    $popup =  str_replace('>Trash<', '>รายการที่ถูกลบ<', $popup);
		    $popup =  str_replace('>Drafts<', '>แบบร่าง<', $popup);
		    $popup =  str_replace('>Spam<', '>รายการ Spam<', $popup);
		    //$popup =  str_replace('>Erase Selected<', '>Erase Selected<', $popup);

		    return $popup;
		}

		if ( $language == "thai" && $type )
		{
		    $popup =  str_replace('Inbox', 'รายการขาเข้า', $popup);
		    $popup =  str_replace('Sent', 'รายการที่ถูกส่ง', $popup);
		    $popup =  str_replace('Trash', 'รายการที่ถูกลบ', $popup);
		    $popup =  str_replace('Drafts', 'แบบร่าง', $popup);
		    $popup =  str_replace('Spam', 'รายการ Spam', $popup);
		    //$popup =  str_replace('Erase Selected', 'Erase Selected', $popup);

		    return $popup;
		}

		return $popup;
	}


	function translateAbookField($field, $lang)
	{
		/**
		 * The translation map
		 *
		 * The map takes the form:
		 *
		 * $translations = array('language-name'     => array('fieldname' => 'tranlation', ... ),
		 *                        another-langname'  => array('fieldname' => 'tranlation', ... )
		 * );
		 *
		 */
		$translations = array(

			'italiano' => array(

				'UserEmail' => 'Indirizzo Email',
				'UserEmail2' => 'Email2',
				'UserEmail3' => 'Email3',
				'UserEmail4' => 'Email4',
				'UserEmail5' => 'Email5',
				'UserFirstName' => 'Nome',
				'UserMiddleName' => 'Secondo Nome',
				'UserLastName' => 'Cognome',
				'UserTitle' => 'Titolo',
				'UserGender' => 'Sesso',
				'UserDOB' => 'Data di nascita',
				'UserHomeAddress' => 'Indirizzo',
				'UserHomeCity' => 'Città',
				'UserHomeState' => 'Stato/Provincia',
				'UserHomeZip' => 'CAP',
				'UserHomeCountry' => 'Nazione',
				'UserHomePhone' => 'Telephono de casa',
				'UserHomeMobile' => 'Nr. Cellulare:',
				'UserHomeFax' => 'Fax di casa',
				'UserURL' => 'URL',
				'UserWorkCompany' => 'Work Company',
				'UserWorkTitle' => 'Work Title',
				'UserWorkDept' => 'Work Dept',
				'UserWorkOffice' => 'Work Office',
				'UserWorkAddress' => 'Work Address',
				'UserWorkCity' => 'Work City',
				'UserWorkState' => 'Work State',
				'UserWorkZip' => 'Work Zip',
				'UserWorkCountry' => 'Work Country',
				'UserWorkPhone' => 'Work Phone',
				'UserWorkMobile' => 'Work Mobile',
				'UserWorkFax' => 'Work Fax',
				'UserInfo' => 'Info',
				'UserPgpKey' => 'PGP Key'
			),

			'english' => array(

				'UserEmail' => 'Email',
				'UserEmail2' => 'Email2',
				'UserEmail3' => 'Email3',
				'UserEmail4' => 'Email4',
				'UserEmail5' => 'Email5',
				'UserFirstName' => 'First Name',
				'UserMiddleName' => 'Middle Name',
				'UserLastName' => 'Last Name',
				'UserTitle' => 'Title',
				'UserGender' => 'Gender',
				'UserDOB' => 'Date of Birth',
				'UserHomeAddress' => 'Home Address',
				'UserHomeCity' => 'Home City',
				'UserHomeState' => 'Home State',
				'UserHomeZip' => 'Home Zip',
				'UserHomeCountry' => 'Home Country',
				'UserHomePhone' => 'Home Phone',
				'UserHomeMobile' => 'Home Mobile',
				'UserHomeFax' => 'Home Fax',
				'UserURL' => 'URL',
				'UserWorkCompany' => 'Work Company',
				'UserWorkTitle' => 'Work Title',
				'UserWorkDept' => 'Work Dept',
				'UserWorkOffice' => 'Work Office',
				'UserWorkAddress' => 'Work Address',
				'UserWorkCity' => 'Work City',
				'UserWorkState' => 'Work State',
				'UserWorkZip' => 'Work Zip',
				'UserWorkCountry' => 'Work Country',
				'UserWorkPhone' => 'Work Phone',
				'UserWorkMobile' => 'Work Mobile',
				'UserWorkFax' => 'Work Fax',
				'UserInfo' => 'Info',
				'UserPgpKey' => 'PGP Key'
			)
		);

		// Default to English
		if (!isset($translations[$lang])) {
			$lang = 'english';
		}

		// Return the translated version if it exists
		// otherwise just return the original string
		if (isset($translations[$lang][$field])) {
			return $translations[$lang][$field];
		}

		return $field;
	}
	
	
	function getErrorMessage($error, $lang)
	{
		
		$errors = array(
			'change_pass_not_allowed' => 
				array(
					'english' => 'You are not permitted to change your password'
				)
			);
			
		if (!isset($errors[$error])) {
			return '';
		}
		
		if (!isset($errors[$error][$lang])) {
			$lang = 'english';
		}
		
		return $errors[$error][$lang];
	}
}

?>