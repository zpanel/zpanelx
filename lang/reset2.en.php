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
        $phpmailer->Subject = "Hosting Panel Password Reset";
        $phpmailer->Body = "Hi " . $result['ac_user_vc'] . ",
            
        You or somebody pretending to be you has requested a password reset link to be sent for your web hosting control panel login at: " . ctrl_options::GetOption('cp_url') . "
            
        If you wish to proceed with the password reset on your account please use this link below to be taken to the password reset page.
            
        http://" . ctrl_options::GetOption('zpanel_domain') . "/reset2.php?resetkey=" . $randomkey . "
            
        ";
        $phpmailer->AddAddress($result['ac_email_vc']);
        $phpmailer->SendEmail();
        runtime_hook::Execute('OnRequestForgotPassword');
    }
}
?>