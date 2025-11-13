<?php
    require_once 'lib/User.php';
    require_once 'lib/Mailer.php';

    global $token_param; 
    $token = $token_param; 
    
    $message = "";
    $userModel = new User();
    $mailer = new Mailer();

    if ($token) {
        $user = $userModel->findByToken($token);

        if ($user) {
            $new_token_action = User::generateToken();
            if ($userModel->blockUser($user['id'], $new_token_action)) {

                $reset_link = $_ENV['PROYECT_URL'] . "/reset/" . $new_token_action;
                $subject = "App-Estación: ¡ALERTA! Cuenta Bloqueada por Seguridad";
                $body = "Hola {$user['nombres']},<br><br>Su usuario ha sido 'bloqueado por seguridad'.<br> 
                         Para restablecer su contraseña y reactivar su cuenta, haga click en el siguiente enlace:<br>
                         <a href='$reset_link' style='background-color: #F44336; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 5px;'>Cambiar Contraseña</a>";
                $mailer->sendEmail($user['email'], $user['nombres'], $subject, $body);

                $message = "¡Atención! Su usuario ha sido bloqueado. Revise su correo electrónico para restablecer la contraseña.";

            } else {
                 $message = "Error al bloquear el usuario. Intente nuevamente.";
            }
        } else {
            $message = "El token de bloqueo no corresponde a un usuario.";
        }
    } else {
        $message = "Token de bloqueo faltante.";
    }

    $tpl = new poroto("message");
    $vars = [
        "PROYECT_SECTION" => "Bloqueo de Cuenta",
        "MESSAGE" => $message
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>