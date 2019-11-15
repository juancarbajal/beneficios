<?php
require __DIR__ . './../vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

date_default_timezone_set('America/Lima');

$token = "iuny3e7s278edboqyuo3xu1236e1";
# Constants
$type_dni = "1";
$type_passport = "2";

# Connection to the database
$host = getenv('OPENSHIFT_MYSQL_DB_HOST');
$username = getenv('OPENSHIFT_MYSQL_DB_USERNAME');
$password = getenv('OPENSHIFT_MYSQL_DB_PASSWORD');
$db = getenv('OPENSHIFT_APP_NAME');

$conn = mysqli_connect($host, $username, $password, $db);

// Check connection
if (!$conn) {
    printf("Conexión fallida: %s\n", mysqli_connect_errno());
    exit;
} else {
    mysqli_set_charset($conn, "utf8");
}

#Helpers

function get_type($type)
{
    if ($type == 1) {
        return 'DNI';
    }

    if ($type == 2) {
        return 'Pasaporte';
    }

    return 'No indico';

}

function verify_document($document, $conn)
{
    $query = "select * from BNF4_LandingClientesColaboradores where documento='" . $document . "'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        return false;
    }

    return true;
}

function get_user_by_document($document, $conn)
{
    $query = "select * from BNF4_LandingClientesColaboradores where documento='" . $document . "'";

    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $user = array(
            'name' => $row['Nombres_Apellidos'],
            'telephone' => $row['Telefonos'],
            'email' => $row['Email'],
            'specialist' => $row['Especialista'],
            'created' => $row['Creado'],
            'type' => $row['Tipo'],
            'id' => $row['id'],
            'document' => $row['Documento']
        );
    }

    return $user;
}

function exists_affiliate($name, $telephone, $conn)
{
    $date = date("Y-m-d H:i:s");
    $new_date = strtotime('-30 day', strtotime($date));
    $new_date = date('Y-m-d H:i:s', $new_date);

    $query = "select * from BNF4_LandingReferidos where Nombres_Apellidos = '" . $name . "' and Telefonos = '" . $telephone . "' and Fecha_referencia > '" . $new_date . "'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        return false;
    }

    return true;
}

# Registrar usuario (op: 1)| POST

function registerClient($conn)
{
    $data = $_POST;
    $document_exists = verify_document($data['document'], $conn);
    $data['specialist'] = isset($data['specialist']) ? $data['specialist'] : "";

    if (!$document_exists) {
        $data['created'] = date("Y-m-d H:i:s");
        $data['type'] = '1';
        $data['Apellido'] = '';
        $nuevo_usuario = true;

        $query = "INSERT INTO `BNF4_LandingClientesColaboradores` (`Nombres_Apellidos`, `Telefonos`, `Email`, `Especialista`, `Creado`, `Documento`, `Tipo`)
                    VALUES ('" . $data['name'] . "','" . $data['telephone'] . "','" . $data['email'] . "','" . $data['specialist'] . "','" . $data['created'] . "','" . $data['document'] . "','" . $data['type'] . "')";
        $save = mysqli_query($conn, $query);

        $cliente_id = mysqli_insert_id($conn);
        if ($save) {
            $query = "SELECT * FROM `BNF_Cliente` WHERE NumeroDocumento = '" . $data['document'] . "';";
            $result2 = mysqli_query($conn, $query);
            $num_rows = mysqli_num_rows($result2);

            if ($num_rows > 0) {
                while ($row = mysqli_fetch_assoc($result2)) {
                    $id = $row['id'];
                }
                $nuevo_usuario = false;
            } else {
                $save2 = "INSERT INTO `BNF_Cliente`(`BNF_TipoDocumento_id`,`Nombre`,`Apellido`,`NumeroDocumento`) VALUES(3, '" . $data['name'] . "', '" . $data['Apellido'] . "', '" . $data['document'] . "')";
                mysqli_query($conn, $save2);
                $id = mysqli_insert_id($conn);

                $save7 = "INSERT INTO `BNF_Preguntas` (`BNF_Cliente_id`) VALUES ('" . $id . "');";
                mysqli_query($conn, $save7);
            }

            $query = "select * from BNF_EmpresaSegmentoCliente where BNF_Cliente_id = '" . $id . "' AND BNF_EmpresaSegmento_id = 318";
            $result3 = mysqli_query($conn, $query);
            $num_rows = mysqli_num_rows($result3);

            if ($num_rows == 0) {
                $save3 = "INSERT INTO `BNF_EmpresaSegmentoCliente` (`BNF_EmpresaSegmento_id`,`BNF_Cliente_id`,`Eliminado`) VALUES(318,'" . $id . "',0)";
                mysqli_query($conn, $save3);

                $save4 = "INSERT INTO `BNF_EmpresaClienteCliente` (`BNF_Empresa_id`,`BNF_Cliente_id`,`Estado`,`Eliminado`) VALUES(369,'" . $id . "','Activo',0)";
                mysqli_query($conn, $save4);
            }

            $result = array('result' => 'success', 'id' => $cliente_id, 'new' => $nuevo_usuario);
        } else {
            $result = array('result' => 'warning', 'message' => 'No se pudo ingrear al cliente: ' . mysqli_error($conn));
        }
    } else {
        $user = get_user_by_document($data['document'], $conn);
        $query = "UPDATE `BNF4_LandingClientesColaboradores` SET Nombres_Apellidos = '" . $data['name'] . "', Telefonos = '" . $data['telephone'] . "', Email = '" . $data['email'] . "', Especialista = '" . $data['specialist'] . "' WHERE id = '" . $user['id'] . "'";

        $save = mysqli_query($conn, $query);
        if ($save) {
            $result = array('result' => 'success', 'id' => $user['id'], 'new' => false);
        } else {
            $result = array('result' => 'warning', 'message' => 'No se pudo ingrear al cliente: ' . mysqli_error($conn));
        }
    }

    return $result;
}

#Registrar afiliado (op: 2)| POST

function registerAffiliate($conn)
{
    $data = $_POST;
    $data['date_ref'] = date("Y-m-d H:i:s");

    $query = "INSERT INTO `BNF4_LandingReferidos` (Nombres_Apellidos, Telefonos, Fecha_referencia, cliente_id) VALUES ('" . $data['name'] . "', '" . $data['telephone'] . "', '" . $data['date_ref'] . "', '" . $data['client_id'] . "');";

    $save = mysqli_query($conn, $query);

    if ($save) {
        $result = array('result' => 'success', 'id' => mysqli_insert_id($conn));
    } else {
        $result = array('result' => 'warning', 'message' => 'No se pudo ingrear al Afiliado: ' . mysqli_error($conn));
    }

    return $result;

}

#Verificar si existe afiliado (op: 3)| GET

function verifyAffiliate($conn)
{
    $data = $_POST;
    $exist = exists_affiliate($data['name'], $data['telephone'], $conn);

    if ($exist) {
        $result = array('result' => 'warning', 'id' => 'Ya se encuentra afiliado');
    } else {
        $result = array('result' => 'success', 'id' => 'No se encuentra afiliado');
    }
    return $result;
}

# Obtener usuario (op: 5)| GET

function getUser($conn)
{
    $document = $_GET['document'];

    $query = "SELECT * FROM BNF4_LandingClientesColaboradores WHERE Documento = '" . $document . "'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        $user = array('result' => 'warning', 'message' => 'No se encontro ningun cliente con ese Documento');
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $user = array(
                'name' => $row['Nombres_Apellidos'],
                'telephone' => isset($row['Telefonos']) ? $row['Telefonos'] : "",
                'email' => isset($row['Email']) ? $row['Email'] : "",
                'specialist' => isset($row['Especialista']) ? $row['Especialista'] : "",
                'created' => isset($row['Creado']) ? $row['Creado'] : "",
                'type' => isset($row['Tipo']) ? $row['Tipo'] : "",
                'document' => isset($row['Documento']) ? $row['Documento'] : ""
            );
        }

    }

    return $user;

}

function getClients($conn)
{
    $clients = array();
    $query = "SELECT * FROM `BNF4_LandingClientesColaboradores`";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        printf("Error: %s\n", mysqli_error($conn));
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        $user = array('result' => 'warning', 'message' => 'No se encontro ningun cliente con ese Documento');
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $user = array(
                'id' => $row['id'],
                'name' => $row['Nombres_Apellidos'],
                'telephone' => $row['Telefonos'],
                'email' => $row['Email'],
                'specialist' => $row['Especialista'],
                'created' => $row['Creado'],
                'type' => get_type($row['Tipo']),
                'document' => $row['Documento'],
                'affiliates' => getAffiliates($row['id'], $conn)
            );
            array_push($clients, $user);
        }

    }

    return $clients;
}

function getAffiliates($client_id, $conn)
{
    $affiliates = array();
    $query = "SELECT * FROM `BNF4_LandingReferidos` WHERE cliente_id = '" . $client_id . "'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        $affiliate = array();
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $affiliate = array(
                'name' => $row['Nombres_Apellidos'],
                'telephone' => $row['Telefonos'],
                'date' => $row['Fecha_referencia'],
            );
            array_push($affiliates, $affiliate);
        }

    }

    return $affiliates;
}

function export($conn)
{
    $data = getClients($conn);
    return $data;
}

# Enviar correo Cliente (op: 6)

function sendMailClient()
{
    $data = $_POST;

    $email = $data['email'];
    $title = "Bienvenido al programa de beneficios de Verisure";

    sendClientTemplate($email, $title);

    $result = array('result' => 'success');

    return $result;
}

# Enviar Correo Admin (op: 7)| GET

function sendMailAdmin($conn)
{
    $data = $_POST;
    $query = "SELECT * FROM `BNF_Configuraciones_Referidos` WHERE Campo = 'correo_admin'";
    $result_mail = mysqli_query($conn, $query);

    $correo_admin = "";

    while ($row = mysqli_fetch_assoc($result_mail)) {
        $correo_admin = $row['Atributo'];
    }

    if (!empty($correo_admin)) {
        $email = $correo_admin;
        $title = "Referido Programa Beneficios / Verisure";
        sendAdminTemplate($email, $title, $data);
    }

    $result = array('result' => 'success');

    return $result;
}

# Obtener banner (op: 8)| GET

function getBanner($conn)
{
    $image = null;
    $query = "SELECT * FROM `BNF_Configuraciones_Referidos` WHERE Tipo = 'imagen'";

    $result = mysqli_query($conn, $query);

    if (!$result) {
        return false;
    }

    $num_rows = mysqli_num_rows($result);

    if ($num_rows == 0) {
        $image = array('result' => 'warning', 'message' => 'No se encontro ningun banner');
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['Campo'] == 'banner_link') {
                $image[] = array(
                    'url' => !empty($row['Atributo']) ? $row['Atributo'] : '',
                );
            } elseif ($row['Campo'] == 'banner') {
                $image[] = array(
                    'banner' => $row['Atributo'],
                );
            } elseif ($row['Campo'] == 'popup') {
                $image[] = array(
                    'popup' => $row['Atributo'],
                );
            }
        }
    }

    return $image;
}

# Configuracion Mail

function configMail()
{
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.mandrillapp.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'favio@janaq.com';
    $mail->Password = 'ymAzjeWAdLObuPZbE41q_Q';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';
    return $mail;
}

function sendClientTemplate($to, $subject = 'Example')
{
    $mail = configMail();

    $mail->setFrom('admin@beneficios.com', 'Beneficios.pe');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = '<table><tr><td><h1>BIENVENIDO</h1></td></tr>' .
        '<tr><td><p>
            Estás a punto de descubrir un mundo de beneficios increíbles para disfrutar de lo que más te gusta con
            descuentos insuperables.
        </p></td></tr>' .
        '<tr><td><p>
                Eso no es todo, a partir de ahora podrás recomendar (amigos, familiares, vecinos, clientes, etc.)
                nuestro producto y por cada referido que instale una ALARMA VERISURE te beneficiarás con grandes
                premios.
            </p></td></tr>' .
        '<tr><td><p>
                Para ingresar a nuestra página de beneficios <a href="' . getenv('URL_VERISURE') . '">' . getenv('URL_VERISURE')
        . '</a>deberás identificarte con tu numero de documento (DNI,CEX o RUC) según sea el caso.
            </p></td></tr>' .
        '<tr><td><p>El equipo de Beneficios VERISURE</p></td></tr></table>';
    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

function sendAdminTemplate($to, $subject = 'Example', $data = array())
{
    $mail = configMail();

    $mail->setFrom('admin@beneficios.com', 'Beneficios.pe');
    $mail->addAddress($to);

    $body = '<h1>Información del usuario</h1>';

    if (isset($data["members"]) && count($data["members"]) > 0) {
        $body = $body . "<ul>";
        foreach ($data["members"] as $item) {
            $body = $body . "<li> Nombre del Referido: " . $item['name'] . "</li>";
            $body = $body . "<li> Teléfono del Referido: " . $item['telephone'] . "</li>";
        }
        $body = $body . "</ul>";
    }

    $body = $body . '<br><ul>';
    $body = $body . "<li>Nombre de quien refiere: " . $data['name'] . "</li>";
    $body = $body . "<li>Teléfono de quien refiere: " . $data['telephone'] . "</li>";
    $body = $body . "<li>Número de Documento: " . $data['document'] . "</li>";
    if (isset($data['specialist']) and !empty($data['specialist'])) {
        $body = $body . "<li>Nombre del especialista: " . $data['specialist'] . "</li>";
    }
    $body = $body . "</ul>";

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}

#Token
#=======

/* Metodos
   =======
1) Registrar cliente: 
	
	http://localhost:8000/app/api.php?op=1

2) Registrar afiliado: 
	
	http://localhost:8000/app/api.php?op=2

3) Obtener usuario por DNI: 
	
	http://localhost:8000/app/api.php?op=5

4) Descargar

	http://localhost:8000/app/api.php?op=4
	http://localhost:8000/app/api.php?op=4&extra=download

5) Verificar afiliado

	http://localhost:8000/app/api.php?op=3
*/

$op = $_GET['op'];
$extra = isset($_GET['extra']) ? $_GET['extra'] : null;

if ($op == 1) {
    $result = registerClient($conn);
} elseif ($op == 2) {
    $result = registerAffiliate($conn);
} elseif ($op == 3) {
    $result = verifyAffiliate($conn);
} elseif ($op == 4) {
    $result = export($conn);
} elseif ($op == 5) {
    $result = getUser($conn);
} elseif ($op == 6) {
    $result = sendMailClient();
} elseif ($op == 7) {
    $result = sendMailAdmin($conn);
} elseif ($op == 8) {
    $result = getBanner($conn);
} else {
    $result = array('result' => 'error', 'message' => 'Opcion invalida');
}

if ($extra == 'download') {
    header('Content-disposition: attachment; filename=file.json');
    header('Content-type: application/json');
}

echo json_encode($result);


