<?php
    require_once 'lib/User.php';
    require_once 'lib/Mailer.php';
    require_once 'lib/utils.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: panel");
        exit();
    }

    $error_message = "";
    $userModel = new User();
    $mailer = new Mailer();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        if ($email === 'admin-estacion' && $password === 'admin1234') {
            $_SESSION['is_admin'] = true;
            header("Location: administrator");
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Credenciales no válidas.";
            exit();
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $user = $userModel->findByEmail($email);
        $device = get_device_info();

        if ($user) {
            if (User::verifyPassword($password, $user['contraseña'])) {
                if ($user['bloqueado'] == 1 || $user['recupero'] == 1) {
                    $error_message = "Su usuario está bloqueado o en proceso de recuperación. Revise su correo electrónico.";
                } elseif ($user['activo'] == 0) {
                    $error_message = "Su usuario aún no se ha validado. Revise su bandeja de entrada.";
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_token'] = $user['token'];

                    $block_link = $_ENV['PROYECT_URL'] . "/blocked/" . $user['token'];
                    $subject = "App-Estación: Inicio de sesión exitoso";
                    $body = "Hola {$user['nombres']},<br><br>Hemos detectado un inicio de sesión exitoso.<br>
                             Detalles: IP: {$device['ip']}, OS: {$device['os']}, Navegador: {$device['browser']}.
                             <br><br>Si no fuiste tú, por favor <a href='$block_link' style='color: red;'>BLOQUEA TU CUENTA AQUÍ</a>.";
                    $mailer->sendEmail($user['email'], $user['nombres'], $subject, $body);

                    header("Location: panel");
                    exit();
                }
            } else {
                $error_message = "Credenciales no válidas.";
                
                $block_link = $_ENV['PROYECT_URL'] . "/blocked/" . $user['token'];
                $subject = "App-Estación: Intento de acceso inválido";
                $body = "Hola {$user['nombres']},<br><br>Se detectó un intento de acceso con contraseña inválida.<br>
                         Detalles: IP: {$device['ip']}.
                         <br><br>Si no fuiste tú, por favor <a href='$block_link' style='color: red;'>BLOQUEA TU CUENTA AQUÍ</a>.";
                $mailer->sendEmail($user['email'], $user['nombres'], $subject, $body);
            }
        } else {
            $error_message = "Credenciales no válidas.";
        }
    }

    $tpl = new poroto("login");
    $vars = [
        "PROYECT_SECTION" => "Acceder",
        "ERROR_MESSAGE" => $error_message
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>