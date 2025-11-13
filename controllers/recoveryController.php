<?php
    require_once 'lib/User.php';
    require_once 'lib/Mailer.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: panel");
        exit();
    }

    $message = "";
    $userModel = new User();
    $mailer = new Mailer();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        $user = $userModel->findByEmail($email);

        if ($user) {
            $new_token_action = User::generateToken();
            $userModel->startRecovery($user['id'], $new_token_action);

            $reset_link = $_ENV['PROYECT_URL'] . "/reset/" . $new_token_action;
            $subject = "App-Estación: Restablecer Contraseña";
            $body = "Hola {$user['nombres']},<br><br>Hemos recibido una solicitud para restablecer su contraseña.<br> 
                     Para continuar, haga click en el siguiente botón:<br>
                     <a href='$reset_link' style='background-color: #2196F3; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 5px;'>Restablecer Contraseña</a>";
            $mailer->sendEmail($email, $user['nombres'], $subject, $body);

            $message = "Se ha enviado un correo electrónico con las instrucciones para restablecer su contraseña. Revise su bandeja de entrada.";
        } else {
            $message = "El email '{$email}' no se encuentra registrado. <a href='register' style='color: wheat;'>registrarse aquí</a>";
        }
    }

    $tpl = new poroto("recovery"); 
    $vars = [
        "PROYECT_SECTION" => "Recuperar Contraseña",
        "MESSAGE" => $message
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>