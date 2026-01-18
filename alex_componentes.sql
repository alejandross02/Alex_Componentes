-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-01-2026 a las 21:50:42
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
-- Base de datos: `alex_componentes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(16) NOT NULL,
  `correo_electronico` varchar(50) NOT NULL,
  `contraseña` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin`
--

INSERT INTO `admin` (`id_admin`, `nombre`, `correo_electronico`, `contraseña`) VALUES
(1, 'admin_alex', 'administrador@gmail.com', '$2y$10$V.Qxg9lpUZsAPrQ.0cn3HOtN1eoo.c4pHREjRJ1kNeP62u.3CpkHy');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) UNSIGNED NOT NULL,
  `correo_electronico` varchar(50) NOT NULL,
  `nombre` varchar(16) NOT NULL,
  `apellidos` varchar(16) NOT NULL,
  `telefono` int(9) NOT NULL,
  `contraseña` varchar(64) NOT NULL,
  `direccion` varchar(50) NOT NULL,
  `cod_postal` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `correo_electronico`, `nombre`, `apellidos`, `telefono`, `contraseña`, `direccion`, `cod_postal`) VALUES
(11, 'sandra@gmail.com', 'Sandra', 'Sanchez', 666666666, '$2y$10$vAGW/ylLAXHxx/1q6DgABeGn3l8qhBFuptKqUMmZj6RnM0NrTv13.', 'Calle Moron', 41530),
(12, 'maesekevin@maese.com', 'Kevin', 'Linux', 0, '$2y$10$CuZHLFw30X/bh7AVAmYr0OWcoOPK3oP.tXW7d7oeJNKgL072daGmK', '', 0),
(14, 'alejandrosantoyo2002@gmail.com', 'Alejandro', 'Santoyo Sánchez', 0, '$2y$10$saKsqGmLd1rwEbceQs9te.SL/ABlGje43Fq8RlD3uxewTQWUre86m', '', 0),
(16, 'ramon@ozein.es', 'Ramon', 'fgsrfg', 0, '$2y$10$vQ1cLc1NKf8b9Mxd6ohWFOQd.Jch6ZGDIYK05gNjFxA7SqvJargti', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historico_stock`
--

CREATE TABLE `historico_stock` (
  `id_historico` int(11) UNSIGNED NOT NULL,
  `id_producto` int(11) UNSIGNED NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_admin` int(11) UNSIGNED NOT NULL,
  `cantidad_cambio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historico_stock`
--

INSERT INTO `historico_stock` (`id_historico`, `id_producto`, `fecha`, `id_admin`, `cantidad_cambio`) VALUES
(7, 1, '2025-12-06 21:41:54', 1, 5),
(9, 1, '2025-12-10 20:05:44', 1, -1),
(10, 2, '2025-12-10 20:05:50', 1, 2),
(11, 5, '2025-12-10 20:05:56', 1, 2),
(12, 6, '2025-12-10 20:05:59', 1, -4),
(13, 6, '2025-12-10 20:07:00', 1, 0),
(14, 6, '2025-12-10 20:07:09', 1, 0),
(15, 6, '2025-12-10 20:08:44', 1, 0),
(16, 1, '2025-12-12 14:57:05', 1, 6),
(17, 1, '2025-12-17 17:08:57', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_pedido`
--

CREATE TABLE `linea_pedido` (
  `id_linea` int(10) UNSIGNED NOT NULL,
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `id_producto` int(11) UNSIGNED NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unidad` float(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `linea_pedido`
--

INSERT INTO `linea_pedido` (`id_linea`, `id_pedido`, `id_producto`, `cantidad`, `precio_unidad`) VALUES
(11, 1, 1, 2, 399.00),
(12, 1, 2, 1, 355.00),
(13, 2, 3, 1, 699.00),
(14, 2, 6, 1, 150.00),
(15, 2, 8, 1, 195.00),
(21, 5, 2, 6, 355.00),
(22, 5, 3, 5, 699.00),
(23, 6, 1, 3, 399.00),
(24, 6, 2, 7, 355.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id_pedido` int(10) UNSIGNED NOT NULL,
  `fecha_pedido` date NOT NULL,
  `precio_total` float UNSIGNED NOT NULL,
  `id_cliente` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id_pedido`, `fecha_pedido`, `precio_total`, `id_cliente`) VALUES
(1, '2025-12-12', 1153, 11),
(2, '2025-12-12', 1044, 11),
(5, '2025-12-12', 5625, 11),
(6, '2026-01-03', 3682, 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio_unidad` float(11,2) NOT NULL,
  `descripcion_producto` varchar(100) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL,
  `ruta_imagen` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `nombre`, `precio_unidad`, `descripcion_producto`, `stock`, `ruta_imagen`) VALUES
(1, 'MSI NVIDIA RTX 4080', 399.00, 'Tarjeta gráfica de gama alta basada en la arquitectura Ada Lovelace, diseñada para ofrecer un rendim', 24, 'media/RTX4080.jpg'),
(2, 'Intel Core i7 14700K', 355.00, 'Procesador de escritorio de gama alta de 14ª generación (Raptor Lake Refresh), conocido por su excel', 15, 'media/i7.jpg'),
(3, 'Ryzen 9 9950X3D', 699.00, 'Procesador de escritorio de gama alta basado en la arquitectura Zen 5 con tecnología 3D V-Cache, dis', 23, 'media/ryzen.jpg'),
(4, 'Torre PC NZXT H5', 245.00, 'Caja de PC de formato semitorre (mid-tower) ATX conocida por su diseño minimalista y elegante, panel', 50, 'media/caja.jpg'),
(5, 'Gigabyte Radeon RX 9060 XT', 402.00, 'Tarjeta gráfica de gama media-alta basada en la arquitectura AMD RDNA 4, diseñada para ofrecer un ex', 52, 'media/radeon.jpg'),
(6, 'NZXT Kraken Elite 240 RGB', 150.00, 'Es un sistema de refrigeración líquida todo en uno (AIO) de 240 mm conocido por su excelente rendimi', 36, 'media/liquida.jpg'),
(7, 'RAM Corsair Vengeance RGB Pro SL White DDR4 3200 2x16GB', 210.00, 'Es un kit de memoria RAM de alto rendimiento que combina una estética llamativa con una velocidad só', 95, 'media/ram.jpg'),
(8, 'Corsair RMe Series RM1000e ATX 3.1 1000W', 195.00, 'Fuente de alimentación totalmente modular y silenciosa, con certificación 80 Plus Gold y Cybenetics ', 41, 'media/fuente.jpg'),
(9, 'ASUS ROG STRIX Z890-A GAMING WIFI', 410.00, 'Placa base de gama alta con formato ATX diseñada para gamers y entusiastas que buscan el máximo rend', 21, 'media/placa.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `correo_electrónico` (`correo_electronico`);

--
-- Indices de la tabla `historico_stock`
--
ALTER TABLE `historico_stock`
  ADD PRIMARY KEY (`id_historico`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `linea_pedido`
--
ALTER TABLE `linea_pedido`
  ADD PRIMARY KEY (`id_linea`),
  ADD KEY `id_pedido` (`id_pedido`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `precio_unidad` (`precio_unidad`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id_pedido`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `precio_unidad` (`precio_unidad`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `historico_stock`
--
ALTER TABLE `historico_stock`
  MODIFY `id_historico` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `linea_pedido`
--
ALTER TABLE `linea_pedido`
  MODIFY `id_linea` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id_pedido` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `historico_stock`
--
ALTER TABLE `historico_stock`
  ADD CONSTRAINT `historico_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `historico_stock_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `linea_pedido`
--
ALTER TABLE `linea_pedido`
  ADD CONSTRAINT `linea_pedido_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `linea_pedido_ibfk_2` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `linea_pedido_ibfk_3` FOREIGN KEY (`precio_unidad`) REFERENCES `productos` (`precio_unidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
