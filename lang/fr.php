<?php
if (isset($_POST['inForgotPassword'])) {
    $randomkey = sha1(microtime());
    $forgotPass = $_POST['inForgotPassword'];
    $sth = $zdbh->prepare("SELECT ac_id_pk, ac_user_vc, ac_email_vc  FROM x_accounts WHERE ac_email_vc = :forgotPass");
    $sth->bindParam(':forgotPass', $forgotPass);
    $sth->execute();
    $rows = $sth->fetchAll();
    if ($rows) {
        $result = $rows['0'];
        $zdbh->exec("UPDATE x_accounts SET ac_resethash_tx = '" . $randomkey . "' WHERE ac_id_pk=" . $result['ac_id_pk'] . "");

        $phpmailer = new sys_email();
                $phpmailer->Subject = "Restaurer le mot de passe du panel";
        $phpmailer->Body = "Salut " . $result['ac_user_vc'] . ",
            
        Vous ou quelqu'un se faisant passer pour vous a demand� un lien pour r�initialiser le mot de passe de connexion pour votre panel d' h�bergement web � l'adresse suivante: http://" . ctrl_options::GetOption('zpanel_domain') . "
            
        Si vous souhaitez proc�der � la r�initialisation du mot de passe sur votre compte s'il vous pla�t utiliser le lien ci-dessous pour �tre redirig� vers la page de r�initialisation de mot de passe.
            
        http://" . ctrl_options::GetOption('zpanel_domain') . "/?resetkey=" . $randomkey . "
            
        ";
        $phpmailer->AddAddress($result['ac_email_vc']);
        $phpmailer->SendEmail();
        runtime_hook::Execute('OnRequestForgotPassword');
    }
}
?>