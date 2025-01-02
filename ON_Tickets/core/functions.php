<?php
require __DIR__ . '/../core/connection.php'; // Conexión a la base de datos
session_start();

function logout()
{
    // Destruir todas las variables de sesión
    session_unset();
    // Destruir la sesión
    session_destroy();
    // Redirigir al login
    header("Location: ../index.php");
    exit();
}

// Verificar si se solicitó una acción
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'logout':
            logout();
            break;
            // Puedes agregar más casos aquí si hay más acciones
    }
}

function create_users($pdo)
{
    // Iniciar sesión para usar variables de sesión
    session_start();

    // Obtener datos de entrada
    $nombre_completo = $_POST['nombre_completo'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $rol = $_POST['rol'] ?? null;
    $imagen = $_FILES['imagen'] ?? null;

    try {
        // Manejar la subida de la imagen (si se proporciona)
        $imagen_nombre = null;
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $imagen_nombre = uniqid('img_') . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($imagen['tmp_name'], $upload_dir . $imagen_nombre)) {
                throw new Exception("error al subir la imagen.");
            }
        }

        // Preparar la consulta para insertar los datos
        $sql = "
            INSERT INTO users (nombre_completo, estado, email, password, rol" .
            ($imagen_nombre ? ", imagen" : "") . ")
            VALUES (:nombre_completo, :estado, :email, :password, :rol" .
            ($imagen_nombre ? ", :imagen" : "") . ")
        ";

        // Ejecutar la consulta con los parámetros
        $params = [
            ':nombre_completo' => $nombre_completo,
            ':estado' => $estado,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':rol' => $rol
        ];

        // Agregar el parámetro de imagen si fue subida
        if ($imagen_nombre) {
            $params[':imagen'] = $imagen_nombre;
        }

        // Preparar y ejecutar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Almacenar mensaje de éxito en la sesión
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'datos registrados correctamente.';

        // Redirigir con el mensaje de éxito almacenado en la sesión
        header("Location: ../views/view_users.php");
        exit;
    } catch (Exception $e) {
        // Almacenar mensaje de error en la sesión
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error: ' . $e->getMessage();

        // Redirigir con el mensaje de error almacenado en la sesión
        header("Location: ../views/view_users.php");
        exit;
    }
}

function update_users($pdo){
    // Obtener datos de entrada
    $id = $_POST['id'] ?? null;
    $nombre_completo = $_POST['nombre_completo'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $rol = $_POST['rol'] ?? null;
    $imagen = $_FILES['imagen'] ?? null;

    try {
        // Manejar la subida de la imagen
        $imagen_nombre = null;
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $imagen_nombre = uniqid('img_') . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($imagen['tmp_name'], $upload_dir . $imagen_nombre)) {
                throw new Exception("error al subir la imagen.");
            }
        }

        // Preparar la consulta de actualización
        $sql = "
            UPDATE users
            SET nombre_completo = :nombre_completo,
                estado = :estado,
                email = :email,
                rol = :rol";

        // Añadir la imagen si se ha subido una nueva
        if ($imagen_nombre) {
            $sql .= ", imagen = :imagen";
        }

        // Añadir la contraseña si se ha proporcionado una nueva
        if ($password) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE id = :id";

        // Preparar la declaración
        $stmt = $pdo->prepare($sql);

        // Vincular los parámetros
        $params = [
            ':nombre_completo' => $nombre_completo,
            ':estado' => $estado,
            ':email' => $email,
            ':rol' => $rol,
            ':id' => $id
        ];

        // Vincular imagen y contraseña si es necesario
        if ($imagen_nombre) {
            $params[':imagen'] = $imagen_nombre;
        }

        if ($password) {
            $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        // Ejecutar la declaración
        $stmt->execute($params);

        // Almacenar mensaje de éxito en la sesión
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'datos actualizados correctamente.';

        // Redirigir a la página de vista de users
        header("Location: ../views/view_users.php");
        exit;
    } catch (Exception $e) {
        // Almacenar el error en la sesión
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'error: ' . $e->getMessage();
        header("Location: ../views/view_users.php");
        exit;
    }
}


function delete_users($pdo)
{
    // Iniciar sesión para utilizar variables de sesión
    session_start();

    // Verificar si el botón 'btn_delete_users' fue presionado
    if (isset($_POST['btn_delete_users'])) {
        // Obtener el ID del agente
        $id = $_POST['id'] ?? null;

        // Validar que se haya proporcionado el ID
        if (!$id) {
            // Almacenar mensaje de error en la sesión
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'id no proporcionado.';
            header("Location: ../views/view_users.php");
            exit;
        }

        try {
            // Preparar la consulta para eliminar el agente por su ID
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");

            // Ejecutar la consulta
            $stmt->execute([':id' => $id]);

            // Verificar si se eliminó algún registro
            if ($stmt->rowCount() > 0) {
                // Almacenar mensaje de éxito en la sesión
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'datos eliminados correctamente.';
            } else {
                // Almacenar mensaje de error si no se encontró el agente
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'datos no encontrados.';
            }

            // Redirigir a la página de vista de users
            header("Location: ../views/view_users.php");
            exit;
        } catch (Exception $e) {
            // Almacenar el error en la sesión
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'error: ' . $e->getMessage();
            header("Location: ../views/view_users.php");
            exit;
        }
    } else {
        // Si el botón no fue presionado, redirigir
        header("Location: ../views/view_users.php");
        exit;
    }
}



// Verificar si se solicitó crear un agente
if (isset($_POST['btn_create_users'])) {
    create_users($pdo);
}

// Verificar si se solicitó actualizar un agente
if (isset($_POST['btn_update_users'])) {
    update_users($pdo);
}

// Verificar si se solicitó eliminar un agente
if (isset($_POST['btn_delete_users'])) {
    delete_users($pdo);
}

function create_ticket($pdo)
{
    // Iniciar sesión para usar variables de sesión
    session_start();

    // Obtener datos de entrada
    $descripcion = $_POST['descripcion'] ?? null;
    $categoria = $_POST['categoria'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $prioridad = $_POST['prioridad'] ?? null;
    $fecha_cierre = $_POST['fecha_cierre'] ?? null;
    $reportado_por = $_POST['reportado_por'] ?? null;
    $area = $_POST['area'] ?? null;
    $atendido_por = $_POST['atendido_por'] ?? null;
    $n_ticket = $_POST['n_ticket'] ?? null;
    $imagen = $_FILES['imagen'] ?? null;

    try {
        // Manejar la subida de la imagen (si se proporciona)
        $imagen_nombre = null;
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $imagen_nombre = uniqid('img_') . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($imagen['tmp_name'], $upload_dir . $imagen_nombre)) {
                throw new Exception("Error al subir la imagen.");
            }
        }

        // Preparar la consulta para insertar los datos
        $sql = "
            INSERT INTO tickets (descripcion, categoria, estado, prioridad, fecha_cierre, reportado_por, area, atendido_por, n_ticket" . 
            ($imagen_nombre ? ", imagen" : "") . ")
            VALUES (:descripcion, :categoria, :estado, :prioridad, :fecha_cierre, :reportado_por, :area, :atendido_por, :n_ticket" . 
            ($imagen_nombre ? ", :imagen" : "") . ")
        ";

        // Ejecutar la consulta con los parámetros
        $params = [
            ':descripcion' => $descripcion,
            ':categoria' => $categoria,
            ':estado' => $estado,
            ':prioridad' => $prioridad,
            ':fecha_cierre' => $fecha_cierre,
            ':reportado_por' => $reportado_por,
            ':area' => $area,
            ':atendido_por' => $atendido_por,
            ':n_ticket' => $n_ticket
        ];

        // Agregar el parámetro de imagen si fue subida
        if ($imagen_nombre) {
            $params[':imagen'] = $imagen_nombre;
        }

        // Preparar y ejecutar la consulta
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Almacenar mensaje de éxito en la sesión
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Ticket registrado correctamente.';

        // Redirigir con el mensaje de éxito almacenado en la sesión
        header("Location: ../views/view_tickets_manual.php");
        exit;
    } catch (Exception $e) {
        // Almacenar mensaje de error en la sesión
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Error: ' . $e->getMessage();

        // Redirigir con el mensaje de error almacenado en la sesión
        header("Location: ../views/view_tickets_manual.php");
        exit;
    }
}


// Verificar si se solicitó crear un agente
if (isset($_POST['btn_create_ticket'])) {
    create_ticket($pdo);
}



// Redirigir si no hay acción definida
header('Location: view_users.php?status=error&message=' . urlencode('acción no válida.'));
exit;
