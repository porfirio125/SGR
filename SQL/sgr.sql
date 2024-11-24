-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 25-11-2024 a las 00:01:57
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sgr`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_requerimientos`
--

CREATE TABLE `historial_requerimientos` (
  `id_historial` int(11) NOT NULL,
  `id_requerimiento` int(11) DEFAULT NULL,
  `id_usuario` int(50) DEFAULT NULL,
  `id_oficina` int(11) DEFAULT NULL,
  `fecha_revision` datetime DEFAULT NULL,
  `comentario` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `id_tipo_requerimiento` int(11) DEFAULT NULL,
  `id_tipo_documento` int(11) DEFAULT NULL,
  `tiempo` datetime DEFAULT NULL,
  `id_oficina_derivada` int(50) DEFAULT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Historial detallado de los requerimientos, incluyendo estado y tipo de documento';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `oficinas`
--

CREATE TABLE `oficinas` (
  `id_oficina` int(11) NOT NULL,
  `nombre_oficina` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `oficinas`
--

INSERT INTO `oficinas` (`id_oficina`, `nombre_oficina`) VALUES
(1, 'Oficina 1'),
(2, 'Oficina 2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requerimientos`
--

CREATE TABLE `requerimientos` (
  `id_requerimiento` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_oficina` int(50) DEFAULT NULL,
  `id_tipo_requerimiento` int(11) DEFAULT NULL,
  `id_tipo_documento` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabla principal de requerimientos que incluye referencias a tipo de documento y flujo de oficinas';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_documento`
--

CREATE TABLE `tipos_documento` (
  `id_tipo_documento` int(11) NOT NULL,
  `nombre_tipo_documento` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Catálogo de tipos de documentos disponibles (e.g., Solicitud, Acta)';

--
-- Volcado de datos para la tabla `tipos_documento`
--

INSERT INTO `tipos_documento` (`id_tipo_documento`, `nombre_tipo_documento`) VALUES
(1, 'Solicitud'),
(2, 'Acta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_requerimiento`
--

CREATE TABLE `tipos_requerimiento` (
  `id_tipo_requerimiento` int(11) NOT NULL,
  `nombre_tipo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_requerimiento`
--

INSERT INTO `tipos_requerimiento` (`id_tipo_requerimiento`, `nombre_tipo`) VALUES
(1, 'Bienes de obra'),
(2, 'Bienes por plan de trabajo'),
(3, 'Servicios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(200) NOT NULL,
  `id_oficina` int(11) DEFAULT NULL,
  `cargo` enum('admin','usuario') DEFAULT NULL,
  `contrasena` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `id_oficina`, `cargo`, `contrasena`) VALUES
(6, 'Administrador', NULL, 'sa@gmail.com', 1, 'admin', '$2y$10$XPDztfVflegx04c1yD2DbO00HK707H/CWQ3krEX0TSdFQJ/w1JIQy'),
(8, 'Prueba 01', '.', 'samoporfirio125@gmail.com', 2, 'usuario', '$2y$10$ofx5.wYHRv3yM6tJrmbR3uLXXJyWfw9wpLwocwerQMSKcruHlfHEy');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial_requerimientos`
--
ALTER TABLE `historial_requerimientos`
  ADD PRIMARY KEY (`id_historial`),
  ADD KEY `id_requerimiento` (`id_requerimiento`),
  ADD KEY `id_oficina` (`id_oficina`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_requermiento` (`id_tipo_requerimiento`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `oficinas`
--
ALTER TABLE `oficinas`
  ADD PRIMARY KEY (`id_oficina`);

--
-- Indices de la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  ADD PRIMARY KEY (`id_requerimiento`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_tipo_requerimiento` (`id_tipo_requerimiento`),
  ADD KEY `id_oficina` (`id_oficina`),
  ADD KEY `id_tipo_documento` (`id_tipo_documento`);

--
-- Indices de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  ADD PRIMARY KEY (`id_tipo_documento`);

--
-- Indices de la tabla `tipos_requerimiento`
--
ALTER TABLE `tipos_requerimiento`
  ADD PRIMARY KEY (`id_tipo_requerimiento`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre` (`nombre`,`apellido`),
  ADD KEY `id_oficina` (`id_oficina`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `historial_requerimientos`
--
ALTER TABLE `historial_requerimientos`
  MODIFY `id_historial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `oficinas`
--
ALTER TABLE `oficinas`
  MODIFY `id_oficina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  MODIFY `id_requerimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT de la tabla `tipos_documento`
--
ALTER TABLE `tipos_documento`
  MODIFY `id_tipo_documento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipos_requerimiento`
--
ALTER TABLE `tipos_requerimiento`
  MODIFY `id_tipo_requerimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historial_requerimientos`
--
ALTER TABLE `historial_requerimientos`
  ADD CONSTRAINT `historial_requerimientos_fk_tipo_documento` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`),
  ADD CONSTRAINT `historial_requerimientos_ibfk_1` FOREIGN KEY (`id_requerimiento`) REFERENCES `requerimientos` (`id_requerimiento`),
  ADD CONSTRAINT `historial_requerimientos_ibfk_2` FOREIGN KEY (`id_oficina`) REFERENCES `oficinas` (`id_oficina`),
  ADD CONSTRAINT `historial_requerimientos_ibfk_4` FOREIGN KEY (`id_tipo_requerimiento`) REFERENCES `tipos_requerimiento` (`id_tipo_requerimiento`);

--
-- Filtros para la tabla `requerimientos`
--
ALTER TABLE `requerimientos`
  ADD CONSTRAINT `requerimientos_fk_tipo_documento` FOREIGN KEY (`id_tipo_documento`) REFERENCES `tipos_documento` (`id_tipo_documento`),
  ADD CONSTRAINT `requerimientos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `requerimientos_ibfk_2` FOREIGN KEY (`id_tipo_requerimiento`) REFERENCES `tipos_requerimiento` (`id_tipo_requerimiento`),
  ADD CONSTRAINT `requerimientos_ibfk_4` FOREIGN KEY (`id_oficina`) REFERENCES `oficinas` (`id_oficina`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_oficina`) REFERENCES `oficinas` (`id_oficina`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
