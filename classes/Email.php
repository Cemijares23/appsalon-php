<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email {

    protected $nombre;
    protected $email;
    protected $token;

    public function __construct($nombre, $email, $token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        // New phpmailer instance
        $mail = new PHPMailer();

        // Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'sandbox.smtp.mailtrap.io';             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '46f08abb377ae4';                       //SMTP username
        $mail->Password   = 'cb3cbfbe2b515b';                       //SMTP password
        $mail->Port       = 2525;    

        //Recipients
        $mail->setFrom('appsalon@domain.com', 'Chris');
        $mail->addAddress($this->nombre . '@email.net', $this->nombre);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Confirma tu cuenta!';
        $contenido = "<html>";
        $contenido = "
            <h4>Hola <strong>". $this->nombre ."</strong>.</h4>
            <p>Has creado exitosamente tu cuenta de AppSalon. Para confirmarla haz click en el siguiente enlace:</p>
            <a href='http://localhost:3000/confirmar-cuenta?token=". $this->token ."'>Confirmar Cuenta</a>
            <p>Si no solicitaste una cuenta en AppSalon, puedes ignorar o eliminar este mensaje</p>
        ";
        $contenido .= "</html>";
        $mail->Body    = $contenido;

        $mail->send();
    }

    public function enviarInstrucciones() {
        // New phpmailer instance
        $mail = new PHPMailer();

        // Server settings
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'sandbox.smtp.mailtrap.io';             //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = '46f08abb377ae4';                       //SMTP username
        $mail->Password   = 'cb3cbfbe2b515b';                       //SMTP password
        $mail->Port       = 2525;    

        //Recipients
        $mail->setFrom('appsalon@domain.com', 'Chris');
        $mail->addAddress($this->nombre . '@email.net', $this->nombre);     //Add a recipient

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Reestablece tu contraseña';
        $contenido = "<html>";
        $contenido = "
            <h4>Hola <strong>". $this->nombre ."</strong>.</h4>
            <p>Has solicitado reestablecer tu contraseña de AppSalon. Para hacerlo haz click en el siguiente enlace:</p>
            <a href='http://localhost:3000/recuperar?token=". $this->token ."'>Reestablecer Contraseña</a>
            <p>Si no solicitaste este cambio, puedes ignorar o eliminar este mensaje</p>
        ";
        $contenido .= "</html>";
        $mail->Body    = $contenido;

        $mail->send();
    }

}