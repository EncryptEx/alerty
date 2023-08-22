-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 09-08-2022 a las 10:08:41
-- Versión del servidor: 10.5.16-MariaDB-1:10.5.16+maria~focal
-- Versión de PHP: 7.4.30
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;


/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;


/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;


/*!40101 SET NAMES utf8mb4 */
;

--
-- Base de datos: `encryptex_alerty`
--
-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `action-logs`
--
CREATE TABLE `action-logs` (
    `id` int(11) NOT NULL,
    `triggerId` int(11) NOT NULL,
    `timestamp` int(11) NOT NULL,
    `extraData` text DEFAULT NULL,
    `logFilename` varchar(255) DEFAULT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `action-types`
--
CREATE TABLE `action-types` (
    `id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Volcado de datos para la tabla `action-types`
--
INSERT INTO `action-types` (`id`, `name`)
    VALUES (1, 'email (useful to send alerts)');

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `triggers`
--
CREATE TABLE `triggers` (
    `id` int(11) NOT NULL,
    `stringUrl` varchar(256) NOT NULL,
    `name` varchar(255) NOT NULL,
    `actionType` int(11) NOT NULL,
    `triggerOwner` int(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `users`
--
CREATE TABLE `users` (
    `id` int(11) NOT NULL,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(256) NOT NULL,
    `status` int(11) NOT NULL) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
-- Índices para tablas volcadas
--
--
-- Indices de la tabla `action-logs`
--
ALTER TABLE `action-logs`
    ADD PRIMARY KEY (`id`),
    ADD KEY `triggerId` (`triggerId`);

--
-- Indices de la tabla `action-types`
--
ALTER TABLE `action-types`
    ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `triggers`
--
ALTER TABLE `triggers`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `stringUrl_2` (`stringUrl`),
    ADD KEY `stringUrl` (`stringUrl`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--
--
-- AUTO_INCREMENT de la tabla `action-logs`
--
ALTER TABLE `action-logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `action-types`
--
ALTER TABLE `action-types` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `triggers`
--
ALTER TABLE `triggers` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;


/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;


/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

