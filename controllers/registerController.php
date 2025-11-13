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
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $repeat_password = filter_input(INPUT_POST, 'repeat_password', FILTER_SANITIZE_STRING);
        $nombres = filter_input(INPUT_POST, 'nombres', FILTER_SANITIZE_STRING);

        if ($password !== $repeat_password) {
            $message = "Las contraseñas no coinciden.";
        } else {
            $existingUser = $userModel->findByEmail($email);
            
            if ($existingUser) {
                $message = "El email '{$email}' ya corresponde a un usuario. ¿Desea <a href='login' style='color: wheat;'>iniciar sesión</a>?";
            } else {
                if ($userModel->registerUser($email, $nombres, $password)) {

                    $user = $userModel->findByEmail($email); 
                    $token_action = $user['token_action'];

                    $validate_link = $_ENV['PROYECT_URL'] . "/validate/" . $token_action;
                    $subject = "App-Estación: ¡Bienvenido! Activa tu cuenta";
                    $body = "Hola {$nombres},<br><br>Gracias por registrarte. Para activar tu cuenta, haz click en el siguiente botón:<br>
                             <a href='$validate_link' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block; border-radius: 5px;'>Activar Cuenta</a>";
                    $mailer->sendEmail($email, $nombres, $subject, $body);

                    $message = "¡Registro exitoso! Revise su correo electrónico para activar su cuenta.";
                } else {
                    $message = "Error interno al registrar el usuario. Intente nuevamente.";
                }
            }
        }
    }

    $tpl = new poroto("register");
    $vars = [
        "PROYECT_SECTION" => "Registrarse",
        "MESSAGE" => $message
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>