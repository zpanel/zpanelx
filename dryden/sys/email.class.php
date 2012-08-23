<?php

/**
 * Email class used for sending out emails from ZPanel. This class extends on the PHPMailer library included in etc/lib/PHPMailer!
 * @package zpanelx
 * @subpackage dryden -> sys
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
require './etc/lib/PHPMailer/class.phpmailer.php';

class sys_email extends PHPMailer {

    /**
     * Sends the email with the contents of the object (Body etc. set using the parant calls in phpMailer!)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return boolean 
     */
    public function SendEmail() {
        $this->Mailer = ctrl_options::GetOption('mailer_type');
        $this->From = ctrl_options::GetOption('email_from_address');
        $this->FromName = ctrl_options::GetOption('email_from_name');
        if (ctrl_options::GetOption('email_smtp') <> 'false') {
            $this->IsSMTP();
            if (ctrl_options::GetOption('smtp_auth') <> 'false') {
                $this->SMTPAuth = true;
                $this->Username = ctrl_options::GetOption('smtp_username');
                $this->Password = ctrl_options::GetOption('smtp_password');
            }
            if (ctrl_options::GetOption('smtp_secure') <> 'false') {
                $this->SMTPSecure = ctrl_options::GetOption('smtp_secure');
            }
            $this->Host = ctrl_options::GetOption('smtp_server');
            $this->Port = ctrl_options::GetOption('smtp_port');
        }

        ob_start();
        $send_resault = $this->Send();
        $error = ob_get_contents();
        ob_clean();
        if ($send_resault) {
            runtime_hook::Execute('OnSuccessfulSendEmail');
            return true;
        } else {
            $logger = new debug_logger();
            $logger->method = ctrl_options::GetOption('logmode');
            $logger->logcode = "061";
            $logger->detail = 'Error sending email (using sys_email): ' . $error . '';
            $logger->writeLog();
            runtime_hook::Execute('OnFailedSendEmail');
            return false;
        }
    }

}

?>
