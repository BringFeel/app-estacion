<?php
    require_once 'lib/User.php';
    require_once 'lib/Mailer.php';
    require_once 'lib/utils.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: panel");
        exit();
    }

    global $token_param;
    $token_action = $token_param; 

    $message = "";
    $show_form = false;
    $userModel = new User();
    $mailer = new Mailer();

    if ($token_action) {
        $user = $userModel->findByTokenAction($token_action, true, false); 

        if ($user) {
            $show_form = true;
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                $repeat_password = filter_input(INPUT_POST, 'repeat_password', FILTER_SANITIZE_STRING);

                if ($password !== $repeat_password) {
                    $message = "Las contraseñas no coinciden.";
                } else {
                    $userModel->resetPassword($user['id'], $password);
                    $device = get_device_info();

                    $block_link = $_ENV['PROYECT_URL'] . "/blocked/" . $user['token'];
                    $subject = "App-Estación: Contraseña Restablecida con Éxito";
                    $body = "Hola {$user['nombres']},<br><br>Su contraseña ha sido restablecida con éxito.<br>
                             Detalles: IP: {$device['ip']}, OS: {$device['os']}, Navegador: {$device['browser']}.
                             <br><br>Si no realizaste este cambio, por favor <a href='$block_link' style='color: red;'>BLOQUEA TU CUENTA AQUÍ</a>.";
                    $mailer->sendEmail($user['email'], $user['nombres'], $subject, $body);

                    header("Location: ../login");
                    exit();
                }
            }
        } else {
            $message = "El link de restablecimiento no es válido o ya ha sido utilizado.";
        }
    } else {
        $message = "Token de restablecimiento faltante en la URL.";
    }

    $tpl = new poroto("reset");
    $vars = [
        "PROYECT_SECTION" => "Restablecer Contraseña",
        "MESSAGE" => $message,
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>