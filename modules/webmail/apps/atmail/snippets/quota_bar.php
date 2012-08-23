<?php

if ($pref['quota_bar']) {
    $mail = new GetMail(
    	array(
			'Username' => $atmail->username,
			'Pop3host' => $atmail->pop3host,
			'Password' => $auth->password,
			'Mode'     => $atmail->Mode,
			'Type'     => $atmail->MailType
		)
    );
    
    list($var['usedquota'], $var['totalquota']) = $mail->getquota();

    // The size of our quota in Kb
    if (!$var['totalquota']) {
		$var['totalquota'] = $atmail->UserQuota;
		$var['totalquota'] = sprintf("%2.0f", $var['totalquota']);
    }

    if ($var['usedquota'] > 0 && $var['totalquota'] > 0) {
		$var['used'] = ( $var['usedquota'] / $var['totalquota']) * 100;

        if ($var['used'] < 1)
            $var['used'] = '1';
        else
            $var['used'] = round($var['used'], 2);
    }

    $var['used_percent'] = $var['used']? $var['used'] : '1';
}
