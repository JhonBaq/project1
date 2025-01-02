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
                    <h3>Usuarios</h3>
                    <p class="text-subtitle text-muted">Agrege , edite y elimine accesos al sistema, bajo roles específicos.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">CRUD</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
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
                                    <div class="col-4 text-center">
                                        <img src="../assets/static/images/meeting.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-8">
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
                                    <div class="col-4 text-center">
                                        <img src="../assets/static/images/business-man.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-8">
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
                                    <div class="col-4 text-center">
                                        <img src="../assets/static/images/client.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-8">
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
                                    <div class="col-4 text-center">
                                        <img src="../assets/static/images/switch-on.png" alt="Ícono Tickets"
                                            style="width: 48px; height: 48px;">
                                    </div>
                                    <!-- Contenedor del texto -->
                                    <div class="col-8">
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

        <!-- Basic Vertical form layout section start -->
        <section id="basic-vertical-layouts">
            <div class="row match-height">
                <div class="col-md-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Registrar o Actualizar Datos</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form form-vertical" method="post" action="../core/functions.php" enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- Campo oculto para el ID (solo se usa en actualización) -->
                                            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

                                            <!-- Nombre Completo -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group has-icon-left">
                                                    <label for="nombre-completo">Nombre completo</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control"
                                                            placeholder="Ingrese los nombres y apellidos"
                                                            id="nombre-completo" name="nombre_completo"
                                                            value="<?php echo isset($nombre_completo) ? htmlspecialchars($nombre_completo) : ''; ?>">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-person-add"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Rol -->
                                            <div class="col-12 col-md-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="rol">Rol</label>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="rol" id="rol">
                                                            <option value="cliente" <?php echo isset($rol) && $rol == 'cliente' ? 'selected' : ''; ?>>cliente</option>
                                                            <option value="agente" <?php echo isset($rol) && $rol == 'agente' ? 'selected' : ''; ?>>agente</option>
                                                            <option value="administrador" <?php echo isset($rol) && $rol == 'administrador' ? 'selected' : ''; ?>>administrador</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Estado -->
                                            <div class="col-12 col-md-3">
                                                <div class="form-group has-icon-left">
                                                    <label for="estado">Estado</label>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="estado" id="estado">
                                                            <option value="activo" <?php echo isset($estado) && $estado == 'activo' ? 'selected' : ''; ?>>activo</option>
                                                            <option value="inactivo" <?php echo isset($estado) && $estado == 'inactivo' ? 'selected' : ''; ?>>inactivo</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Email -->
                                            <div class="col-12 col-md-4">
                                                <div class="form-group has-icon-left">
                                                    <label for="email">Email</label>
                                                    <div class="position-relative">
                                                        <input type="email" class="form-control"
                                                            placeholder="Ingrese el correo electrónico"
                                                            id="email" name="email"
                                                            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-envelope"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Contraseña -->
                                            <div class="col-12 col-md-4">
                                                <div class="form-group has-icon-left">
                                                    <label for="password">Contraseña</label>
                                                    <div class="position-relative">
                                                        <input type="password" class="form-control"
                                                            placeholder="Ingrese la nueva contraseña (dejar vacío si no desea cambiarla)"
                                                            id="password" name="password">
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-safe"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Adjuntar imagen -->
                                            <div class="col-12 col-md-4">
                                                <div class="form-group">
                                                    <label for="imagen">Adjuntar imagen (opcional)</label>
                                                    <div class="position-relative">
                                                        <input class="form-control" type="file" id="imagen" name="imagen">
                                                    </div>
                                                    <!-- Mostrar la imagen actual, si existe -->
                                                    <?php if (isset($imagen) && !empty($imagen)): ?>
                                                        <small>Imagen actual:</small>
                                                        <img src="../uploads/<?php echo htmlspecialchars($imagen); ?>" alt="Imagen actual"
                                                            style="max-width: 100px; display: block; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px;">
                                                        <!-- Input oculto para enviar el nombre de la imagen actual al servidor -->
                                                        <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($imagen); ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Botones de acción -->
                                            <div class="col-12 d-flex justify-content-end">
                                                <?php if ($id): ?>
                                                    <button type="submit" name="btn_update_users" class="btn btn-secondary me-1 mb-1">Actualizar</button>
                                                    <!-- Botón de volver -->
                                                    <a href="view_users.php" class="btn btn-light me-1 mb-1">Volver</a>
                                                <?php else: ?>
                                                    <button type="submit" name="btn_create_users" class="btn btn-primary me-1 mb-1">Agregar</button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        echo isset($alert) ? $alert : "";
        ?>
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