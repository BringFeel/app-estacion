<?php
// Por alguna razón tengo que importar ambos si o si
require_once 'PHPMailer/src/Exception.php'; 
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        try {
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com'; 
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['EMAIL_USER']; 
            $this->mail->Password   = $_ENV['EMAIL_PASSWORD']; 
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port       = 465;
            $this->mail->CharSet    = 'UTF-8';
            $this->mail->setFrom($_ENV['EMAIL_USER'], $_ENV['PROYECT_NAME']);
        } catch (Exception $e) {
            echo"Error de configuración de Mailer: " . $e->getMessage();
        }
    }

    public function sendEmail($toEmail, $toName, $subject, $body) {
        try {
            $this->mail->clearAllRecipients(); 
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->AltBody = strip_tags($body);
            return $this->mail->send();
        } catch (Exception $e) {
            echo"Error al enviar email a $toEmail: " . $e;
            return false;
        }
    }
}