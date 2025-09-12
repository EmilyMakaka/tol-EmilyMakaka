<?php
// Global/SendMail.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendMail {
    /**
     * Send a welcome email (personalized)
     * @param string $name  - recipient's name
     * @param string $email - recipient's email address
     * @return bool|string  true on success, error string on failure
     */
    public function SendMail($name, $email) { 
        global $conf;

        // Create PHPMailer instance
        $mailer = new PHPMailer(true);

        try {
            // SMTP config
            $mailer->isSMTP();
            $mailer->Host       = $conf['smtp_host'];
            $mailer->SMTPAuth   = true;
            $mailer->Username   = $conf['smtp_user'];
            $mailer->Password   = $conf['smtp_pass'];
            $mailer->SMTPSecure = $conf['smtp_secure'];
            $mailer->Port       = $conf['smtp_port'];

            // From/To
            $mailer->setFrom($conf['from_email'], $conf['app_name']);
            $mailer->addAddress($email, $name);

            // Content
            $mailer->isHTML(true);
            $mailer->Subject = 'Welcome to ' . $conf['app_name'];

            $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
            $mailer->Body    = "<p>Hello {$safeName},</p>
                                <p>Welcome to <strong>{$conf['app_name']}</strong>. 
                                To complete your registration please <a href='#'>Click Here</a>.</p>
                                <p>Regards,<br/>Systems Admin</p>";
            $mailer->AltBody = "Hello {$name},\nWelcome to {$conf['app_name']}. Visit the site to complete registration.";

            $mailer->send();
            return true;
        } catch (Exception $e) {
            // Return PHPMailer error for debugging
            return $mailer->ErrorInfo;
        }
    }
}
