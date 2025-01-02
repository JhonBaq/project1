<?php
#require __DIR__ . '/../core/connection.php'; // Conexión a la base de datos
// Contar el total de usuarios en la tabla "users"
$q_users = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
$total_users = $q_users->fetch(PDO::FETCH_ASSOC)['total_users'];

// Contar el total de usuarios activos en la tabla "users"
$q_estado = $pdo->query("SELECT COUNT(*) AS total_active_users FROM users WHERE estado = 'activo'");
$total_active_users = $q_estado->fetch(PDO::FETCH_ASSOC)['total_active_users'];

// Contar el total de usuarios con rol de "agente" en la tabla "users"
$q_agentes = $pdo->query("SELECT COUNT(*) AS total_agentes FROM users WHERE rol = 'agente'");
$total_agentes = $q_agentes->fetch(PDO::FETCH_ASSOC)['total_agentes'];

// Contar el total de usuarios con rol de "cliente" en la tabla "users"
$q_clientes = $pdo->query("SELECT COUNT(*) AS total_clientes FROM users WHERE rol = 'cliente'");
$total_clientes = $q_clientes->fetch(PDO::FETCH_ASSOC)['total_clientes'];
