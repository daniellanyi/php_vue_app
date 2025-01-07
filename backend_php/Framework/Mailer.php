<?php
    namespace Framework;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    

    class Mailer {
        public $mail;

        public function __construct($config) 
        {
            $this->mail = new PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = $config['host'];
            $this->mail->SMTPAuth = $config['auth'];
            $this->mail->SMTPSecure = $config['tls'] ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Password = $config['appPassword'];
            $this->mail->Port = $config['port'];
            $this->mail->Username = $config['username'];
            $this->mail->setFrom($config['senderEmail'], $config['senderName']);
            $this->mail->CharSet = $config['charSet'];
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        
        }

        public function addAttachment() {

        }

        public function addContent($subject = '', $body = '', $altBody = '', $isHTML = true) {
            $this->mail->isHTML($isHTML);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody;
        }

        public function addAddress($recipientEmail, $recipientName = '', $replyTo = null) {
            $this->mail->addAddress($recipientEmail, $recipientName);
            if ($replyTo) {
                $this->mail->addReplyTo($replyTo);
            }

        }

        public function sendEmail( ) {
            $this->mail->send();
        }
    }
?>