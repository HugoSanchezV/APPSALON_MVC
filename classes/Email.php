<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion()
    {
        //  Crear el objeto
        $email = new PHPMailer();
        $email->isSMTP();
        $email->Host = 'smtp.mailtrap.io';
        $email->SMTPAuth = true;
        $email->Port = 2525;
        $email->Username = $_ENV['EMAIL_USER'];
        $email->Password = $_ENV['EMAIL_PASS'];

        $email->setFrom('cuentas@appsalon.com');
        $email->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $email->Subject = 'Confirma tu Cuenta';


        // Agregar HTML
        $email->isHTML(true);
        $email->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p> <strong> Hola </strong>" . $this->nombre .
            " Has creado tu cuenta en AppSalon, Solo debes confirmarla precionando el siguiente enlace </p>";
        $contenido .= "<p> Presiona aqui: <a href='http://localhost:3000/confirmar-cuenta?token=" . $this->token . "':> Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $email->Body = $contenido;

        $email->send();
    }

    public function enviarInstrucciones()
    {
        //  Crear el objeto de email
        $email = new PHPMailer();
        $email->isSMTP();
        $email->Host = 'smtp.mailtrap.io';
        $email->SMTPAuth = true;
        $email->Port = 2525;
        $email->Username = $_ENV['EMAIL_USER'];
        $email->Password = $_ENV['EMAIL_PASS'];

        $email->setFrom('cuentas@appsalon.com');
        $email->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $email->Subject = 'Reestablece tu password';


        // Agregar HTML
        $email->isHTML(true);
        $email->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p> <strong> Hola </strong>" . $this->nombre .
            " Has solicitado reestablecer tu password en AppSalon, Solo debes ingresar al siguiente enlace </p>";
        $contenido .= "<p> Presiona aqui: <a href='http://localhost:3000/recuperar?token=" . $this->token . "':> Reestablece tu password</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta puedes ignorar este mensaje</p>";
        $contenido .= "</html>";

        $email->Body = $contenido;

        $email->send();
    }
}
