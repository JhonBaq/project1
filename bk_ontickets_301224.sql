-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para on_tickets
CREATE DATABASE IF NOT EXISTS `on_tickets` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `on_tickets`;

-- Volcando estructura para tabla on_tickets.tempuser
CREATE TABLE IF NOT EXISTS `tempuser` (
  `id` int NOT NULL,
  `nombre_completo` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `fecha_creacion` varchar(50) DEFAULT NULL,
  `fecha_actualizacion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla on_tickets.tempuser: ~0 rows (aproximadamente)

-- Volcando estructura para tabla on_tickets.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `descripcion` text NOT NULL,
  `categoria` enum('hardware','software','redes') NOT NULL,
  `estado` enum('abierto','en_proceso','cerrado') NOT NULL,
  `prioridad` enum('critica','alta','media','baja') NOT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `reportado_por` varchar(255) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla on_tickets.tickets: ~0 rows (aproximadamente)

-- Volcando estructura para tabla on_tickets.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla on_tickets.users: ~4 rows (aproximadamente)
REPLACE INTO `users` (`id`, `nombre_completo`, `estado`, `email`, `password`, `rol`, `imagen`, `creado_en`, `actualizado_en`) VALUES
	(3, 'Fabián Sánchez', 'activo', 'fsanchezl@proton.me', '$2y$10$XG8BRpCdeW8NWJBQ25RqTuwn/ts8Q6RpDATM5Xj7/YL3.QiS24jqa', 'administrador', 'img_67731ca041cd7.jpg', '2024-12-28 05:48:59', '2024-12-30 22:20:16'),
	(4, 'Julian Flores', 'activo', 'jflores@proton.me', '$2y$10$GoHfs8jhVBqngSjeWvyeRufYJRPvl9viBor0IyCsYQ9nrSByTxLom', 'agente', 'img_6772f0e0bf2a3.png', '2024-12-30 15:54:50', '2024-12-30 19:13:36');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
