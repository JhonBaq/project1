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
    $stmt = $pdo->query("DESCRIBE tickets");
    $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener todos los registros de tickets
    $query = $pdo->query("SELECT * FROM tickets");
    $tickets = $query->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los nombres de las columnas
    $column_names = array_column($columnas, 'Field');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

$n_ticket = "T-" . date("dmY_His");
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
                            <h4 class="card-title">Registrar o Actualizar Incidencia</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form form-vertical" method="post" action="../core/functions.php" enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- Campo oculto para el ID (solo se usa en actualización) -->
                                            <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

                                            <!-- Descripción / Incidencia -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group has-icon-left">
                                                    <label for="descripcion">Descripción / Incidencia</label>
                                                    <div class="position-relative">
                                                        <textarea class="form-control" id="descripcion" name="descripcion" rows="1" placeholder="Escriba la descripción"><?php echo isset($descripcion) ? htmlspecialchars($descripcion) : ''; ?></textarea>
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-megaphone"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Adjuntar imagen -->
                                            <div class="col-12 col-md-6">
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

                                            <!-- Categoría -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="categoria">Categoría</label>
                                                    <select class="choices form-select" name="categoria" id="categoria">
                                                        <optgroup label="Técnicas generales">
                                                            <option value="Hardware" <?php echo isset($categoria) && $categoria == 'Hardware' ? 'selected' : ''; ?>>Hardware</option>
                                                            <option value="Software" <?php echo isset($categoria) && $categoria == 'Software' ? 'selected' : ''; ?>>Software</option>
                                                            <option value="Redes" <?php echo isset($categoria) && $categoria == 'Redes' ? 'selected' : ''; ?>>Redes</option>
                                                            <option value="Seguridad informática" <?php echo isset($categoria) && $categoria == 'Seguridad informática' ? 'selected' : ''; ?>>Seguridad informática</option>
                                                            <option value="Bases de datos" <?php echo isset($categoria) && $categoria == 'Bases de datos' ? 'selected' : ''; ?>>Bases de datos</option>
                                                            <option value="Telefonía/VoIP" <?php echo isset($categoria) && $categoria == 'Telefonía/VoIP' ? 'selected' : ''; ?>>Telefonía/VoIP</option>
                                                            <option value="Correo electrónico" <?php echo isset($categoria) && $categoria == 'Correo electrónico' ? 'selected' : ''; ?>>Correo electrónico</option>
                                                            <option value="Impresión / Impresora" <?php echo isset($categoria) && $categoria == 'Impresión / Impresora' ? 'selected' : ''; ?>>Impresión / Impresora</option>
                                                            <option value="Almacenamiento / Storage" <?php echo isset($categoria) && $categoria == 'Almacenamiento / Storage' ? 'selected' : ''; ?>>Almacenamiento / Storage</option>
                                                            <option value="Sistemas operativos" <?php echo isset($categoria) && $categoria == 'Sistemas operativos' ? 'selected' : ''; ?>>Sistemas operativos</option>
                                                        </optgroup>
                                                        <optgroup label="Soporte y usuario">
                                                            <option value="Configuración de usuario" <?php echo isset($categoria) && $categoria == 'Configuración de usuario' ? 'selected' : ''; ?>>Configuración de usuario</option>
                                                            <option value="Permisos y accesos" <?php echo isset($categoria) && $categoria == 'Permisos y accesos' ? 'selected' : ''; ?>>Permisos y accesos</option>
                                                            <option value="Capacitación" <?php echo isset($categoria) && $categoria == 'Capacitación' ? 'selected' : ''; ?>>Capacitación</option>
                                                            <option value="Instalación de software" <?php echo isset($categoria) && $categoria == 'Instalación de software' ? 'selected' : ''; ?>>Instalación de software</option>
                                                            <option value="Consultas generales" <?php echo isset($categoria) && $categoria == 'Consultas generales' ? 'selected' : ''; ?>>Consultas generales</option>
                                                        </optgroup>
                                                        <optgroup label="Infraestructura">
                                                            <option value="Servidores" <?php echo isset($categoria) && $categoria == 'Servidores' ? 'selected' : ''; ?>>Servidores</option>
                                                            <option value="Virtualización" <?php echo isset($categoria) && $categoria == 'Virtualización' ? 'selected' : ''; ?>>Virtualización</option>
                                                            <option value="Copias de seguridad (backups)" <?php echo isset($categoria) && $categoria == 'Copias de seguridad (backups)' ? 'selected' : ''; ?>>Copias de seguridad (backups)</option>
                                                            <option value="Equipos en sitio" <?php echo isset($categoria) && $categoria == 'Equipos en sitio' ? 'selected' : ''; ?>>Equipos en sitio</option>
                                                        </optgroup>
                                                        <optgroup label="Seguimiento y gestión">
                                                            <option value="Documentación" <?php echo isset($categoria) && $categoria == 'Documentación' ? 'selected' : ''; ?>>Documentación</option>
                                                            <option value="Políticas de TI" <?php echo isset($categoria) && $categoria == 'Políticas de TI' ? 'selected' : ''; ?>>Políticas de TI</option>
                                                            <option value="Proyectos internos" <?php echo isset($categoria) && $categoria == 'Proyectos internos' ? 'selected' : ''; ?>>Proyectos internos</option>
                                                            <option value="Monitoreo" <?php echo isset($categoria) && $categoria == 'Monitoreo' ? 'selected' : ''; ?>>Monitoreo</option>
                                                            <option value="Auditorías" <?php echo isset($categoria) && $categoria == 'Auditorías' ? 'selected' : ''; ?>>Auditorías</option>
                                                        </optgroup>
                                                        <optgroup label="Específicas de proyectos">
                                                            <option value="Desarrollo" <?php echo isset($categoria) && $categoria == 'Desarrollo' ? 'selected' : ''; ?>>Desarrollo</option>
                                                            <option value="Implementaciones" <?php echo isset($categoria) && $categoria == 'Implementaciones' ? 'selected' : ''; ?>>Implementaciones</option>
                                                            <option value="Migraciones" <?php echo isset($categoria) && $categoria == 'Migraciones' ? 'selected' : ''; ?>>Migraciones</option>
                                                            <option value="Actualizaciones" <?php echo isset($categoria) && $categoria == 'Actualizaciones' ? 'selected' : ''; ?>>Actualizaciones</option>
                                                        </optgroup>
                                                    </select>

                                                </div>
                                            </div>

                                            <!-- Estado -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="estado">Estado</label>
                                                    <select class="choices form-select" name="estado" id="estado">
                                                        <option value="abierto" <?php echo isset($estado) && $estado == 'abierto' ? 'selected' : ''; ?>>Abierto</option>
                                                        <option value="en proceso" <?php echo isset($estado) && $estado == 'en proceso' ? 'selected' : ''; ?>>En proceso</option>
                                                        <option value="pendiente" <?php echo isset($estado) && $estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                                        <option value="espera usuario" <?php echo isset($estado) && $estado == 'espera usuario' ? 'selected' : ''; ?>>En espera del usuario</option>
                                                        <option value="espera proveedor" <?php echo isset($estado) && $estado == 'espera proveedor' ? 'selected' : ''; ?>>En espera del proveedor</option>
                                                        <option value="resuelto" <?php echo isset($estado) && $estado == 'resuelto' ? 'selected' : ''; ?>>Resuelto</option>
                                                        <option value="cerrado" <?php echo isset($estado) && $estado == 'cerrado' ? 'selected' : ''; ?>>Cerrado</option>
                                                        <option value="cancelado" <?php echo isset($estado) && $estado == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                                        <option value="reabierto" <?php echo isset($estado) && $estado == 'reabierto' ? 'selected' : ''; ?>>Reabierto</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Prioridad -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="prioridad">Prioridad</label>
                                                    <select class="choices form-select" name="prioridad" id="prioridad">
                                                        <option value="critica" <?php echo isset($prioridad) && $prioridad == 'critica' ? 'selected' : ''; ?>>Crítica</option>
                                                        <option value="alta" <?php echo isset($prioridad) && $prioridad == 'alta' ? 'selected' : ''; ?>>Alta</option>
                                                        <option value="media" <?php echo isset($prioridad) && $prioridad == 'media' ? 'selected' : ''; ?>>Media</option>
                                                        <option value="baja" <?php echo isset($prioridad) && $prioridad == 'baja' ? 'selected' : ''; ?>>Baja</option>
                                                        <option value="sin_prioridad" <?php echo isset($prioridad) && $prioridad == 'sin_prioridad' ? 'selected' : ''; ?>>Sin prioridad</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <!-- Fecha de cierre -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="fecha_cierre">Fecha cierre</label>
                                                    <input type="date" class="form-control mb-3 flatpickr-no-config" name="fecha_cierre" id="fecha_cierre" placeholder="Seleciona la fecha y hora de cierre" value="<?php echo isset($fecha_cierre) ? $fecha_cierre : ''; ?>">
                                                </div>
                                            </div>

                                            <!-- Reportado por -->
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="reportado_por">Reportado por</label>
                                                    <input type="text" class="form-control" name="reportado_por" id="reportado_por" value="<?php echo isset($reportado_por) ? htmlspecialchars($reportado_por) : ''; ?>" placeholder="Email del usuario solicitante">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group has-icon-left">
                                                    <label for="area">Área</label>
                                                    <div class="form-group">
                                                        <select class="choices form-select" name="area" id="area">
                                                            <!-- Áreas generales -->
                                                            <option value="administracion" <?php echo isset($area) && $area == 'administracion' ? 'selected' : ''; ?>>Administración</option>
                                                            <option value="recursos humanos" <?php echo isset($area) && $area == 'recursos humanos' ? 'selected' : ''; ?>>Recursos Humanos</option>
                                                            <option value="sistemas" <?php echo isset($area) && $area == 'sistemas' ? 'selected' : ''; ?>>Sistemas</option>
                                                            <option value="finanzas" <?php echo isset($area) && $area == 'finanzas' ? 'selected' : ''; ?>>Finanzas</option>
                                                            <option value="contabilidad" <?php echo isset($area) && $area == 'contabilidad' ? 'selected' : ''; ?>>Contabilidad</option>
                                                            <option value="operaciones" <?php echo isset($area) && $area == 'operaciones' ? 'selected' : ''; ?>>Operaciones</option>
                                                            <option value="logistica" <?php echo isset($area) && $area == 'logistica' ? 'selected' : ''; ?>>Logística</option>
                                                            <option value="calidad" <?php echo isset($area) && $area == 'calidad' ? 'selected' : ''; ?>>Calidad</option>
                                                            <option value="compras" <?php echo isset($area) && $area == 'compras' ? 'selected' : ''; ?>>Compras</option>
                                                            <option value="legal" <?php echo isset($area) && $area == 'legal' ? 'selected' : ''; ?>>Legal</option>
                                                            <option value="marketing" <?php echo isset($area) && $area == 'marketing' ? 'selected' : ''; ?>>Marketing</option>
                                                            <option value="control gestion" <?php echo isset($area) && $area == 'control gestion' ? 'selected' : ''; ?>>Control de Gestión</option>
                                                            <!-- Áreas específicas de una agroindustria con enfoque en frutos -->
                                                            <option value="produccion agricola" <?php echo isset($area) && $area == 'produccion agricola' ? 'selected' : ''; ?>>Producción Agrícola</option>
                                                            <option value="gestion cosecha" <?php echo isset($area) && $area == 'gestion cosecha' ? 'selected' : ''; ?>>Gestión de Cosechas</option>
                                                            <option value="investigacion agronomica" <?php echo isset($area) && $area == 'investigacion agronomica' ? 'selected' : ''; ?>>Investigación Agronómica</option>
                                                            <option value="control plagas" <?php echo isset($area) && $area == 'control_plagas' ? 'selected' : ''; ?>>Control de Plagas</option>
                                                            <option value="mantenimiento maquinaria" <?php echo isset($area) && $area == 'mantenimiento maquinaria' ? 'selected' : ''; ?>>Mantenimiento de Maquinaria</option>
                                                            <option value="almacenamiento" <?php echo isset($area) && $area == 'almacenamiento' ? 'selected' : ''; ?>>Almacenamiento y Logística</option>
                                                            <option value="procesamiento frutos" <?php echo isset($area) && $area == 'procesamiento frutos' ? 'selected' : ''; ?>>Procesamiento de Frutos</option>
                                                            <option value="exportaciones" <?php echo isset($area) && $area == 'exportaciones' ? 'selected' : ''; ?>>Exportaciones</option>
                                                            <option value="medio ambiente" <?php echo isset($area) && $area == 'medio ambiente' ? 'selected' : ''; ?>>Medio Ambiente y Sostenibilidad</option>
                                                            <option value="gestion riego" <?php echo isset($area) && $area == 'gestion riego' ? 'selected' : ''; ?>>Gestión de Riego</option>
                                                            <option value="sig" <?php echo isset($area) && $area == 'sig' ? 'selected' : ''; ?>>Sistemas de Información Geográfica (SIG)</option>
                                                            <option value="comercial" <?php echo isset($area) && $area == 'comercial' ? 'selected' : ''; ?>>Comercial</option>
                                                            <option value="otros" <?php echo isset($area) && $area == 'otros' ? 'selected' : ''; ?>>Otros</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="atendido_por">Atendido por</label>
                                                    <input type="text" class="form-control" name="atendido_por" id="atendido_por" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" placeholder="Agente asignado" disabled>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group has-icon-left">
                                                    <label for="mobile-id-icon">N° Ticket</label>
                                                    <div class="position-relative">
                                                        <input type="text" class="form-control"
                                                            value="<?php echo isset($n_ticket) ? $n_ticket : ""; ?>" id="mobile-id-icon"
                                                            name="n_ticket" disabled>
                                                        <div class="form-control-icon">
                                                            <i class="bi bi-ticket-perforated"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Botones de acción -->
                                            <div class="col-12 d-flex justify-content-end">
                                                <?php if ($id): ?>
                                                    <button type="submit" name="btn_update_ticket" class="btn btn-secondary me-1 mb-1">Actualizar</button>
                                                    <a href="view_tickets.php" class="btn btn-light me-1 mb-1">Volver</a>
                                                <?php else: ?>
                                                    <button type="submit" name="btn_create_ticket" class="btn btn-primary me-1 mb-1">Registrar</button>
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
                            // Mostrar los datos de los tickets
                            foreach ($tickets as $row) {
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
    <input type='hidden' name='id' value='" . isset($row['n_ticket']) ? $row['n_ticket'] : "" . "'>
    <input type='hidden' name='action' value='edit'>
    <button type='submit' name='btn_edit_tickets' class='btn btn-sm btn-warning' title='Editar'>
        <i class='bi bi-pencil-square'></i>
    </button>
</form>

<!-- Formulario para eliminar -->
<form action='../core/functions.php' method='POST' style='display:inline-block;' onsubmit='return confirm(\"¿Estás seguro de eliminar este ticket?\")'>
    <input type='hidden' name='id' value='" . $row['n_ticket'] . "'>
    <input type='hidden' name='action' value='delete'>
    <button type='submit' name='btn_delete_tickets' class='btn btn-sm btn-danger' title='Eliminar'>
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