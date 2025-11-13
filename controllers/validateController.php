<?php
    require_once 'lib/User.php';
    require_once 'lib/Mailer.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: panel");
        exit();
    }

    global $token_param; 
    $token_action = $token_param; 
    
    $message = "";
    $userModel = new User();
    $mailer = new Mailer();

    if ($token_action) {
        $user = $userModel->findByTokenAction($token_action, false, true); 

        if ($user) {
            if ($userModel->activateUser($user['id'])) {
                $subject = "App-Estación: Cuenta Activada";
                $body = "Hola {$user['nombres']},<br><br>¡Felicidades! Su cuenta en App-Estación ha sido activada y ya puede iniciar sesión.";
                $mailer->sendEmail($user['email'], $user['nombres'], $subject, $body);

                header("Location: ../login");
                exit();
            } else {
                 $message = "Error al activar la cuenta. Por favor, contacte soporte.";
            }

        } else {
            $message = "El token de validación no es válido o la cuenta ya fue validada.";
        }
    } else {
        $message = "Token de validación faltante en la URL.";
    }

    $tpl = new poroto("message"); 
    $vars = [
        "PROYECT_SECTION" => "Validación de Cuenta",
        "MESSAGE" => $message
    ];
    $tpl->setVars($vars);
    $tpl->print();
?>