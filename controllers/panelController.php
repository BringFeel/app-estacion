<?php 

    require_once 'lib/DB.php';
    require_once 'lib/utils.php';

    // Track client info on access (for every visitor, registered or not)
    try {
        $db = DB::getInstance();
        $device = get_device_info();
        $ip = $device['ip'];

        $lat = null; $lon = null; $country = null;
        if ($ip) {
            $resp = @file_get_contents("http://ipwho.is/" . urlencode($ip));
            if ($resp !== false) {
                $json = json_decode($resp, true);
                if (is_array($json)) {
                    $lat = isset($json['latitude']) ? (string)$json['latitude'] : null;
                    $lon = isset($json['longitude']) ? (string)$json['longitude'] : null;
                    $country = isset($json['country']) ? (string)$json['country'] : null;
                }
            }
        }

        $token = bin2hex(random_bytes(16));
        $sql = "INSERT INTO tracker (token, ip, latitud, longitud, pais, navegador, sistema, add_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $db->execute($sql, [
            $token,
            (string)$ip,
            $lat,
            $lon,
            $country,
            (string)$device['browser'],
            (string)$device['os']
        ]);
    } catch (Exception $e) {
        // silent fail: do not block panel rendering
    }

    // carga la vista
    $tpl = new poroto("panel");

    // // vector con variables para la vista
    $vars = ["PROYECT_SECTION" => "Panel"];

    // reemplaza las variables de la vista con los valores del vector
    $tpl->setVars($vars);
    
    // imprime la vista en la página
    $tpl->print();

 ?>