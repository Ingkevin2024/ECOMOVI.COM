-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 30-05-2025 a las 17:53:29
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ecomovi`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canjeos`
--

DROP TABLE IF EXISTS `canjeos`;
CREATE TABLE IF NOT EXISTS `canjeos` (
  `id_canjeo` int NOT NULL AUTO_INCREMENT,
  `plac_veh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom_reco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_hora` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `redimido` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'No',
  PRIMARY KEY (`id_canjeo`),
  KEY `plac_veh` (`plac_veh`),
  KEY `nom_reco` (`nom_reco`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canjeos`
--

INSERT INTO `canjeos` (`id_canjeo`, `plac_veh`, `nom_reco`, `fecha`, `fecha_hora`, `redimido`) VALUES
(26, 'GTU-61H', 'KIT DE HERRAMIENTAS MOTO', '2025-05-26 18:10:02', '2025-05-26 16:37:06', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movilidad`
--

DROP TABLE IF EXISTS `movilidad`;
CREATE TABLE IF NOT EXISTS `movilidad` (
  `id_mov` int NOT NULL AUTO_INCREMENT,
  `departamento` varchar(20) NOT NULL,
  `municipio` varchar(40) NOT NULL,
  `plac_veh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_inicial` date DEFAULT NULL,
  `hora_inicial` time DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `hora_final` time DEFAULT NULL,
  `foto_inicial` varchar(255) DEFAULT NULL,
  `foto_final` varchar(255) DEFAULT NULL,
  `puntos` int DEFAULT '0',
  PRIMARY KEY (`id_mov`),
  KEY `fk_movilidad_plac_veh` (`plac_veh`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `movilidad`
--

INSERT INTO `movilidad` (`id_mov`, `departamento`, `municipio`, `plac_veh`, `fecha_inicial`, `hora_inicial`, `fecha_final`, `hora_final`, `foto_inicial`, `foto_final`, `puntos`) VALUES
(49, 'Antioquia', 'Medellín', 'QBW-59D', '2025-05-30', '12:52:48', '2025-05-30', '12:53:08', 'C:\\wamp64\\www\\ecomivi/uploads/img_6839f07078719.jpeg', 'C:\\wamp64\\www\\ecomivi/uploads/final_6839f0840bda6.jpeg', 200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recompensa`
--

DROP TABLE IF EXISTS `recompensa`;
CREATE TABLE IF NOT EXISTS `recompensa` (
  `nom_reco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `puntos` int NOT NULL,
  `imagen_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`nom_reco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recompensa`
--

INSERT INTO `recompensa` (`nom_reco`, `descripcion`, `puntos`, `imagen_url`, `disponible`, `estado`) VALUES
('BONO FALABELLA ', 'Bono Falabella', 300, 'uploads/concuros2_05.png', 0, 'activo'),
('BONO REGALO', 'Disfruta del bono comprando lo que quieras', 500, 'uploads/BONO-REGALO-150MIL.jpg', 20, 'activo'),
('KIT DE HERRAMIENTAS MOTO', 'Kit de herramientas para moto', 200, 'uploads/D_NQ_NP_875218-MCO74109422924_012024-O.webp', 3, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `supervisor`
--

DROP TABLE IF EXISTS `supervisor`;
CREATE TABLE IF NOT EXISTS `supervisor` (
  `nombre` varchar(100) NOT NULL,
  `tipo_id` varchar(2) NOT NULL,
  `num_doc_usu` varchar(20) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `num_doc_usu` (`num_doc_usu`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `supervisor`
--

INSERT INTO `supervisor` (`nombre`, `tipo_id`, `num_doc_usu`, `telefono`, `correo`, `password`, `rol`, `fecha_registro`) VALUES
('MARIO SANCHEZ', 'cc', '1107976486', '3132493379', 'gio@gmail.com', '$2y$10$8x0NiFTI7Al6mm00GfP4AOka5JjN4Ikjof3Dc5oNC8c9iSrZBMr0q', 'Supervisor', '2025-05-26 18:17:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `nom_usu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `apell_usu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `tipo_documento` varchar(20) NOT NULL,
  `num_doc_usu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','usuario','supervisor') NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`num_doc_usu`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`nom_usu`, `apell_usu`, `fecha_nacimiento`, `tipo_documento`, `num_doc_usu`, `direccion`, `email`, `telefono`, `contrasena`, `rol`, `fecha_registro`) VALUES
('Ana', 'Gonzalez', '1998-10-12', 'Cédula', '1007647711', 'Salado', 'natalia.lozano124@gmail.com', '3123456787', '$2y$10$1xjIcjZoFUp2AfdfaboRr.IbYunNiOEUkOt4syGQDJT3hbyTIpJEy', 'usuario', '2025-05-16 17:58:11'),
('Brayan ', 'Lozano', '2000-06-10', 'Cédula', '1107978276', 'Salado ', 'brayanstivenlozanocoronado@gmail.com', '3227429738', '$2y$10$2h.y5mEhstDB4mg4zO/Xf.x3TtSgKmnd14wCs2x0sBzh5kL3zVlje', 'usuario', '2025-05-15 19:43:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `plac_veh` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tip_veh` enum('carro','moto','camion') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tarj_prop_veh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tecno_m` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto_tecno` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `soat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `foto_soat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mar_veh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `lin_veh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `color_veh` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `num_motor_veh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `clase_veh` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `combus_veh` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `capaci_veh` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `num_chasis_veh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `model_veh` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `vehicle_photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `puntos_totales` int DEFAULT '0',
  `num_doc_usu` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`plac_veh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`plac_veh`, `tip_veh`, `tarj_prop_veh`, `tecno_m`, `foto_tecno`, `soat`, `foto_soat`, `mar_veh`, `lin_veh`, `color_veh`, `num_motor_veh`, `clase_veh`, `combus_veh`, `capaci_veh`, `num_chasis_veh`, `model_veh`, `vehicle_photo`, `puntos_totales`, `num_doc_usu`) VALUES
('QBW-59D', 'moto', '544K33', '4356336432', 'uploads/6830b754e1fa8_WhatsApp Image 2025-05-12 at 1.55.54 PM.jpeg', '4GRBRG', 'uploads/6830b754e22a4_imagencarro.jpeg', 'YAMAHA', '23434565', 'NEGRO', 'T434030', 'MOTOCICLETA', 'Gasolina', '2 ', 'Y50330', 'HHHG', NULL, 0, '1107978276');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `movilidad`
--
ALTER TABLE `movilidad`
  ADD CONSTRAINT `fk_movilidad_plac_veh` FOREIGN KEY (`plac_veh`) REFERENCES `vehiculos` (`plac_veh`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
