<?php
require __DIR__ . '/../templates/header.php'; // Incluir el encabezado de la página
#require __DIR__ . '/../core/extra_querys.php'; // Consultas adicionales

// Verificar si la sesión está activa
if (!isset($_SESSION['user_id'])) {
    // Si no está autenticado, redirigir al login
    header("Location: ../index.php");
    exit;
}

// Comprobar si se ha pasado un ID en la URL para editar
$id = isset($_POST['id']) ? htmlspecialchars($_POST['id']) : '';

if ($id) {
    try {
        // Consulta para obtener los datos del agente por su ID
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $agente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no se encuentra el agente
        if (!$agente) {
            header("Location: view_users.php?message=" . urlencode("Agente no encontrado") . "&status=error");
            exit;
        }

        // Asignar los datos a las variables con isset para verificar si están definidos
        $nombre_completo = $agente['nombre_completo'] ?? '';
        $estado = $agente['estado'] ?? '';
        $email = $agente['email'] ?? '';
        $password = ''; // No mostramos la contraseña en el formulario por seguridad
        $rol = $agente['rol'] ?? '';
        $imagen = $agente['imagen'] ?? '';
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} else {
    // Inicializar variables vacías si no se está editando
    $nombre_completo = '';
    $estado = '';
    $email = '';
    $password = '';
    $rol = '';
    $imagen = '';
}

// Verificar si hay un mensaje de sesión
if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
    // Mostrar el mensaje
    $status = $_SESSION['status'];
    $message = $_SESSION['message'];

    // Mostrar un mensaje en función del tipo de status
    $alert = "<div class='$status'>$message</div>";

    // Limpiar el mensaje de la sesión después de mostrarlo
    unset($_SESSION['status']);
    unset($_SESSION['message']);
}

// Realizamos la consulta para obtener los nombres de las columnas
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener todos los registros de users
    $query = $pdo->query("SELECT * FROM users");
    $users = $query->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los nombres de las columnas
    $column_names = array_column($columnas, 'Field');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

?>
<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tickets</h3>
                    <p class="text-subtitle text-muted">Agrege , edite y elimine accesos al sistema, bajo roles específicos.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">CRUD</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tickets</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Contenedor de la imagen -->
                                    <div class="col-2 text-center">
                                        <img src="../assets/static/images/meeting.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-10">
                                        <h6 class="text-muted font-semibold mb-1"># Usuarios totales</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo isset($total_users) ? $total_users : ""; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Contenedor de la imagen -->
                                    <div class="col-2 text-center">
                                        <img src="../assets/static/images/business-man.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-10">
                                        <h6 class="text-muted font-semibold mb-1"># Agentes</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo isset($total_agentes) ? $total_agentes : ""; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Contenedor de la imagen -->
                                    <div class="col-2 text-center">
                                        <img src="../assets/static/images/client.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-10">
                                        <h6 class="text-muted font-semibold mb-1"># Clientes</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo isset($total_clientes) ? $total_clientes : ""; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Contenedor de la imagen -->
                                    <div class="col-2 text-center">
                                        <img src="../assets/static/images/switch-on.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-10">
                                        <h6 class="text-muted font-semibold mb-1"># Usuarios activos</h6>
                                        <h6 class="font-extrabold mb-0"><?php echo isset($total_active_users) ? $total_active_users : ""; ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- // Basic Vertical form layout section end -->
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Mostrar Usuarios</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <?php
                                // Mostrar las cabeceras de la tabla dinámicamente
                                foreach ($column_names as $col_name) {
                                    $col_label = ucfirst(strtoupper(str_replace('_', ' ', $col_name))); // Convertir _ a espacio y capitalizar
                                    echo "<th>$col_label</th>"; // Mostrar nombre de columna
                                }
                                ?>
                                <th>OPCIONES</th> <!-- Columna fija para acciones -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Mostrar los datos de los users
                            foreach ($users as $row) {
                                echo "<tr>";
                                foreach ($row as $col_name => $col_value) {
                                    // Si el valor es nulo, mostrar un texto amigable
                                    $value = htmlspecialchars($col_value ?? '---', ENT_QUOTES, 'UTF-8');
                                    echo "<td>$value</td>";
                                }
                                // Mostrar las acciones (editar y eliminar)
                                echo "<td>
<!-- Formulario para editar -->
<form action='' method='POST' style='display:inline-block;'>
    <input type='hidden' name='id' value='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'>
    <input type='hidden' name='action' value='edit'>
    <button type='submit' name='btn_edit_users' class='btn btn-sm btn-warning' title='Editar'>
        <i class='bi bi-pencil-square'></i>
    </button>
</form>

<!-- Formulario para eliminar -->
<form action='../core/functions.php' method='POST' style='display:inline-block;' onsubmit='return confirm(\"¿Estás seguro de eliminar este agente?\")'>
    <input type='hidden' name='id' value='" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'>
    <input type='hidden' name='action' value='delete'>
    <button type='submit' name='btn_delete_users' class='btn btn-sm btn-danger' title='Eliminar'>
        <i class='bi bi-trash'></i>
    </button>
</form>
</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </section>
    </div>
    <?php
    require __DIR__ . '/../templates/footer.php'; // Incluir el pie de página
    ?>