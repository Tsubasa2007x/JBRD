-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-07-2026 a las 20:33:09
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
-- Base de datos: `jbrd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenidos`
--

CREATE TABLE `contenidos` (
  `id_contenido` int(11) NOT NULL COMMENT 'Identificador unico del contenido',
  `id_leccion` int(11) NOT NULL COMMENT 'Lección a la que pertenece el contenido',
  `nombre_contenido` varchar(100) NOT NULL COMMENT 'Nombre o titulo del contenido',
  `descripcion` text DEFAULT NULL COMMENT 'Descripcion detallada del contenido',
  `orden` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden de presentación dentro de la lección ',
  `video_id` varchar(20) DEFAULT NULL COMMENT 'ID del video asociado (ej. YouTube)',
  `conceptos` text DEFAULT NULL COMMENT 'Conceptos clave tratados en el contenido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contenidos`
--

INSERT INTO `contenidos` (`id_contenido`, `id_leccion`, `nombre_contenido`, `descripcion`, `orden`, `video_id`, `conceptos`) VALUES
(3, 1, 'Riesgo vs Rentabilidad', 'Relacion entre el riesgo que asumes y la ganancia que puedes obtener.', 3, 'qQXmZGv5CKE', NULL),
(4, 2, 'Que es Bitcoin?', 'Historia y funcionamiento de la primera criptomoneda.', 1, NULL, NULL),
(5, 2, 'Blockchain explicado', 'Como funciona la tecnologia detras de las criptos.', 2, NULL, NULL),
(6, 2, 'Principales criptomonedas', 'Bitcoin, Ethereum, BNB y otras monedas relevantes.', 3, NULL, NULL),
(7, 3, 'Mercados financieros', 'Que son y como funcionan los mercados donde se opera.', 1, NULL, NULL),
(8, 3, 'Tipos de ordenes', 'Ordenes de mercado, limite, stop-loss y take-profit.', 2, NULL, NULL),
(9, 3, 'Tu primera operacion', 'Simulacion paso a paso de una operacion de compra.', 3, NULL, NULL),
(10, 4, 'Velas japonesas', 'Aprende a interpretar los graficos de velas.', 1, NULL, NULL),
(11, 4, 'Soporte y resistencia', 'Niveles clave en los graficos y como usarlos.', 2, NULL, NULL),
(12, 4, 'Indicadores basicos', 'RSI, MACD y medias moviles explicados de forma simple.', 3, NULL, NULL),
(13, 5, 'Stop-loss y Take-profit', 'Como limitar perdidas y asegurar ganancias automaticamente.', 1, NULL, NULL),
(14, 5, 'Diversificacion', 'Por que no poner todos los huevos en una sola canasta.', 2, NULL, NULL),
(15, 5, 'Tamano de posicion', 'Como calcular cuanto arriesgar en cada operacion.', 3, NULL, NULL),
(16, 1, '?Qu? es invertir?', 'Conceptos b?sicos de la inversi?n, diferencias entre ahorrar e invertir y c?mo funciona el crecimiento del capital.', 1, 'r0QOwztN81s', NULL),
(17, 1, 'Tipos de inversi?n', 'Conoce las principales alternativas de inversi?n: acciones, bonos, fondos, bienes ra?ces y criptomonedas.', 2, 'de0fdxV5TWY', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas`
--

CREATE TABLE `cuentas` (
  `id_cuenta` int(11) NOT NULL COMMENT 'Identificador unico de la cuenta',
  `id_usuario` int(11) NOT NULL COMMENT 'Usuario propietario de la cuenta',
  `usuario` varchar(50) NOT NULL COMMENT 'Nombre de usuario (login)',
  `password` varchar(255) NOT NULL COMMENT 'Contraseña encriptada del usuario',
  `creditos` int(11) NOT NULL DEFAULT 100 COMMENT 'Creditos disponibles en la cuenta',
  `experiencia` int(11) NOT NULL DEFAULT 0 COMMENT 'Puntos de experiencia acumulados',
  `estado` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Estado de la cuenta (1=activa, 0=inactiva)',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de creacion de la cuenta',
  `id_rol` int(11) NOT NULL DEFAULT 1 COMMENT 'Rol asignado a la cuenta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cuentas`
--

INSERT INTO `cuentas` (`id_cuenta`, `id_usuario`, `usuario`, `password`, `creditos`, `experiencia`, `estado`, `fecha_registro`, `id_rol`) VALUES
(4, 4, 'Steven', '$2y$10$KISIdQmWR7qY49tvOZ8FYehvr8zW1HSXxudPZbkAWDtYy0KlT0Zra', 100, 0, 1, '2026-06-30 07:31:20', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_misiones`
--

CREATE TABLE `cuenta_misiones` (
  `id_cuenta_mision` int(11) NOT NULL COMMENT 'Identificador único del registro cuenta-misión',
  `id_cuenta` int(11) NOT NULL COMMENT 'Cuenta que tiene asignada la misión',
  `id_mision` int(11) NOT NULL COMMENT 'Misión asignada a la cuenta',
  `completada` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la misión fue completada',
  `fecha_completada` datetime DEFAULT NULL COMMENT 'Fecha en que se completa la misión',
  `reclamada` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la recompensa fue reclamada',
  `fecha_reclamo` datetime DEFAULT NULL COMMENT 'Fecha en que se reclama la recompensa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lecciones`
--

CREATE TABLE `lecciones` (
  `id_leccion` int(11) NOT NULL COMMENT 'Identificador único de la lección',
  `nombre_leccion` varchar(100) NOT NULL COMMENT 'Nombre de la lección',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de la lección',
  `recompensa_creditos` int(11) NOT NULL DEFAULT 50 COMMENT 'Créditos otorgados al completar la lección',
  `recompensa_experiencia` int(11) NOT NULL DEFAULT 100 COMMENT 'Experiencia otorgada al completar la lección',
  `orden` int(11) NOT NULL DEFAULT 1 COMMENT 'Orden de presentación de la lección'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `lecciones`
--

INSERT INTO `lecciones` (`id_leccion`, `nombre_leccion`, `descripcion`, `recompensa_creditos`, `recompensa_experiencia`, `orden`) VALUES
(1, 'Que es la inversion?', 'Aprende los conceptos basicos de la inversion y por que es importante para tu futuro financiero.', 50, 100, 1),
(2, 'Introduccion a las criptomonedas', 'Descubre que son las criptomonedas, como funcionan y cuales son las mas importantes del mercado.', 75, 150, 2),
(3, 'Fundamentos del Trading', 'Conoce los principios basicos del trading: tipos de ordenes, gestion del riesgo y analisis basico.', 100, 200, 3),
(4, 'Analisis Tecnico', 'Aprende a leer graficas, identificar tendencias y usar indicadores para tomar mejores decisiones.', 125, 250, 4),
(5, 'Gestion del Riesgo', 'Descubre como proteger tu capital usando stop-loss, diversificacion y otras estrategias clave.', 150, 300, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `misiones`
--

CREATE TABLE `misiones` (
  `id_mision` int(11) NOT NULL COMMENT 'Identificador único de la misión',
  `nombre_mision` varchar(100) NOT NULL COMMENT 'Nombre de la misión',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción de la misión',
  `recompensa_creditos` int(11) NOT NULL DEFAULT 50 COMMENT 'Créditos otorgados al completar la misión',
  `recompensa_experiencia` int(11) NOT NULL DEFAULT 150 COMMENT 'Experiencia otorgada al completar la misión',
  `meta` int(11) NOT NULL DEFAULT 1 COMMENT 'Meta u objetivo numérico a alcanzar para completar la misión',
  `tipo` varchar(50) NOT NULL DEFAULT 'lecciones' COMMENT 'Tipo de misión (ej. lecciones, preguntas, simulaciones)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `misiones`
--

INSERT INTO `misiones` (`id_mision`, `nombre_mision`, `descripcion`, `recompensa_creditos`, `recompensa_experiencia`, `meta`, `tipo`) VALUES
(1, 'Primeros pasos', 'Completa tu primera leccion.', 100, 200, 1, 'lecciones'),
(2, 'En camino', 'Completa 3 lecciones.', 250, 400, 3, 'lecciones'),
(3, 'Estudiante dedicado', 'Completa todas las lecciones disponibles.', 500, 800, 5, 'lecciones'),
(4, 'Simulador novato', 'Realiza tu primera simulacion de inversion.', 150, 250, 1, 'simulaciones'),
(5, 'Trader en practica', 'Realiza 5 simulaciones de inversion.', 300, 500, 5, 'simulaciones'),
(6, 'Aprende y ponlo en pr?ctica', 'Completa una lecci?n y pract?cala en el simulador', 400, 0, 1, 'practica'),
(7, 'Marat?n de aprendizaje', 'Acumula experiencia completando lecciones', 0, 600, 1, 'leccion');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_pregunta` int(11) NOT NULL COMMENT 'Identificador único de la pregunta',
  `id_leccion` int(11) NOT NULL COMMENT 'Lección a la que pertenece la pregunta',
  `pregunta` text NOT NULL COMMENT 'Texto de la pregunta',
  `opcion_a` varchar(255) NOT NULL COMMENT 'Texto de la opción de respuesta A',
  `opcion_b` varchar(255) NOT NULL COMMENT 'Texto de la opción de respuesta B',
  `opcion_c` varchar(255) NOT NULL COMMENT 'Texto de la opción de respuesta C',
  `opcion_d` varchar(255) NOT NULL COMMENT 'Texto de la opción de respuesta D',
  `respuesta_correcta` enum('A','B','C','D') NOT NULL COMMENT 'Opción correcta de la pregunta',
  `orden` int(11) NOT NULL COMMENT 'Orden de la pregunta dentro de la lección'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id_pregunta`, `id_leccion`, `pregunta`, `opcion_a`, `opcion_b`, `opcion_c`, `opcion_d`, `respuesta_correcta`, `orden`) VALUES
(1, 1, '?Cu?l es la principal diferencia entre ahorrar e invertir?', 'Ahorrar siempre genera m?s ganancias que invertir', 'Invertir implica asumir un riesgo a cambio de un posible mayor rendimiento, mientras que ahorrar prioriza la seguridad del dinero', 'Ahorrar e invertir son sin?nimos, no hay diferencia', 'Invertir solo se puede hacer con grandes cantidades de dinero', 'B', 1),
(2, 1, '?Qu? significa que el capital \"crece\" cuando se invierte?', 'Que el dinero se multiplica autom?ticamente sin ning?n riesgo', 'Que el dinero genera rendimientos adicionales con el tiempo, gracias a intereses o ganancias reinvertidas', 'Que el banco regala dinero extra por mantener una cuenta de ahorros', 'Que el valor del dinero siempre se mantiene igual', 'B', 2),
(3, 1, 'Si guardas tu dinero en una alcanc?a durante 10 a?os sin moverlo, ?qu? le pasa a su valor real frente a la inflaci?n?', 'Aumenta porque el dinero gana intereses solo', 'Se mantiene exactamente igual', 'Generalmente pierde poder de compra debido a la inflaci?n', 'Se duplica autom?ticamente', 'C', 3),
(4, 1, '?Cu?l de las siguientes NO es una alternativa de inversi?n mencionada en la lecci?n?', 'Acciones', 'Bonos', 'Bienes ra?ces', 'Loter?a', 'D', 4),
(5, 1, 'Las acciones representan...', 'Un pr?stamo que le haces a una empresa', 'Una parte de la propiedad de una empresa', 'Una moneda digital descentralizada', 'Un terreno o propiedad f?sica', 'B', 5),
(6, 1, '?Qu? son los bonos en el contexto de inversi?n?', 'Acciones de una empresa tecnol?gica', 'Instrumentos de deuda mediante los cuales el inversionista presta dinero a cambio de intereses', 'Una forma de criptomoneda', 'Bienes ra?ces fraccionados', 'B', 6),
(7, 1, 'Las criptomonedas se caracterizan principalmente por...', 'Ser inversiones completamente seguras y sin riesgo', 'Estar respaldadas directamente por el gobierno colombiano', 'Ser activos digitales descentralizados con alta volatilidad', 'Garantizar siempre ganancias a corto plazo', 'C', 7),
(8, 1, 'En general, ?c?mo es la relaci?n entre riesgo y ganancia potencial en una inversi?n?', 'A menor riesgo, mayor ganancia potencial', 'El riesgo y la ganancia potencial no tienen relaci?n', 'A mayor riesgo asumido, mayor es la ganancia potencial (pero tambi?n la posible p?rdida)', 'El riesgo solo afecta las inversiones en bienes ra?ces', 'C', 8),
(9, 1, '?Cu?l de estas inversiones se considera generalmente de menor riesgo?', 'Criptomonedas', 'Acciones de empresas nuevas', 'Bonos del gobierno', 'Bienes ra?ces en zonas inestables', 'C', 9),
(10, 1, 'Si una persona busca seguridad y no quiere arriesgar su dinero, ?qu? estrategia deber?a seguir?', 'Invertir todo en criptomonedas para maximizar ganancias r?pidas', 'Optar por alternativas de bajo riesgo, como ahorrar o invertir en instrumentos m?s estables (bonos)', 'Invertir todo en acciones de empresas emergentes', 'No importa la estrategia, el riesgo es igual en todas las inversiones', 'B', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_lecciones`
--

CREATE TABLE `progreso_lecciones` (
  `id_progreso` int(11) NOT NULL COMMENT 'Identificador único del progreso',
  `id_cuenta` int(11) NOT NULL COMMENT 'Cuenta que avanza en la lección',
  `id_leccion` int(11) NOT NULL COMMENT 'Lección correspondiente al progreso',
  `completado` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la lección fue completada',
  `fecha_completada` datetime DEFAULT NULL COMMENT 'Fecha en que se completa la lección'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL COMMENT 'Identificador unico del rol',
  `nombre_rol` varchar(50) NOT NULL COMMENT 'Nombre del rol (ej. principiante, experto)',
  `experiencia_minima` int(11) NOT NULL DEFAULT 0 COMMENT 'Experiencia mínima requerida para alcanzar el rol',
  `experiencia_maxima` int(11) NOT NULL DEFAULT 999999 COMMENT 'Experiencia máxima permitida dentro del rol',
  `descripcion` text DEFAULT NULL COMMENT 'Descripción del rol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `experiencia_minima`, `experiencia_maxima`, `descripcion`) VALUES
(1, 'Explorador Digital', 0, 499, 'Estas dando tus primeros pasos en finanzas e inversiones.'),
(2, 'Inversionista Novato', 500, 1499, 'Comprende los conceptos basicos de inversion y gestion del dinero.'),
(3, 'Analista de Mercados', 1500, 2999, 'Analiza tendencias, graficos y movimientos de los mercados.'),
(4, 'Estratega Financiero', 3000, 4999, 'Disena estrategias de inversion y evalua oportunidades con criterio.'),
(5, 'Maestro del Trading', 5000, 999999, 'Domina los conceptos de trading e inversiones digitales.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `simulaciones`
--

CREATE TABLE `simulaciones` (
  `id_simulacion` int(11) NOT NULL COMMENT 'Identificador único de la simulacion',
  `id_cuenta` int(11) NOT NULL COMMENT 'Cuenta que realiza la simulación',
  `activo` varchar(50) NOT NULL COMMENT 'Activo financiero simulado (ej. divisa, acción)',
  `tipo` varchar(10) NOT NULL COMMENT 'Tipo de operación (ej. compra, venta)',
  `monto` decimal(15,2) NOT NULL COMMENT 'Monto invertido en la operación',
  `precio_entrada` decimal(15,2) NOT NULL COMMENT 'Precio del activo al abrir la operación',
  `precio_cierre` decimal(15,2) DEFAULT NULL COMMENT 'Precio del activo al cerrar la operación',
  `resultado` decimal(15,2) DEFAULT NULL COMMENT 'Resultado (ganancia o perdida) de la operacion',
  `estado` varchar(10) NOT NULL DEFAULT 'abierta' COMMENT 'Estado de la simulación (abierta/cerrada)',
  `fecha_apertura` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Fecha de apertura de la operación',
  `fecha_cierre` datetime DEFAULT NULL COMMENT 'Fecha de cierre de la operación'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL COMMENT 'Identificador único del usuario',
  `nombre` varchar(50) NOT NULL COMMENT 'Nombre del usuario',
  `apellido` varchar(50) NOT NULL COMMENT 'Apellido del usuario',
  `correo` varchar(100) NOT NULL COMMENT 'Correo electronico del usuario',
  `telefono` varchar(20) DEFAULT NULL COMMENT 'Numero de teléfono de contacto',
  `tipo_documento` varchar(20) NOT NULL COMMENT 'Tipo de documento de identidad (ej. CC, TI, CE)',
  `numero_documento` varchar(30) NOT NULL COMMENT 'Numero del documento de identidad',
  `fecha_nacimiento` date DEFAULT NULL COMMENT 'Fecha de nacimiento del usuario',
  `fecha_expedicion` date DEFAULT NULL COMMENT 'Fecha de expedicion del documento de identidad'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `correo`, `telefono`, `tipo_documento`, `numero_documento`, `fecha_nacimiento`, `fecha_expedicion`) VALUES
(4, 'Brian Steven', 'Londoño Moreno', 'brianlondono013@gmail.com', '3222147838', 'CC', '1014666525', '2007-04-13', '2025-04-14');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contenidos`
--
ALTER TABLE `contenidos`
  ADD PRIMARY KEY (`id_contenido`),
  ADD KEY `id_leccion` (`id_leccion`);

--
-- Indices de la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `cuenta_misiones`
--
ALTER TABLE `cuenta_misiones`
  ADD PRIMARY KEY (`id_cuenta_mision`),
  ADD KEY `id_cuenta` (`id_cuenta`),
  ADD KEY `id_mision` (`id_mision`);

--
-- Indices de la tabla `lecciones`
--
ALTER TABLE `lecciones`
  ADD PRIMARY KEY (`id_leccion`);

--
-- Indices de la tabla `misiones`
--
ALTER TABLE `misiones`
  ADD PRIMARY KEY (`id_mision`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`),
  ADD KEY `id_leccion` (`id_leccion`);

--
-- Indices de la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  ADD PRIMARY KEY (`id_progreso`),
  ADD UNIQUE KEY `uq_cuenta_leccion` (`id_cuenta`,`id_leccion`),
  ADD KEY `id_cuenta` (`id_cuenta`),
  ADD KEY `id_leccion` (`id_leccion`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `simulaciones`
--
ALTER TABLE `simulaciones`
  ADD PRIMARY KEY (`id_simulacion`),
  ADD KEY `id_cuenta` (`id_cuenta`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contenidos`
--
ALTER TABLE `contenidos`
  ADD CONSTRAINT `contenidos_ibfk_1` FOREIGN KEY (`id_leccion`) REFERENCES `lecciones` (`id_leccion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cuentas`
--
ALTER TABLE `cuentas`
  ADD CONSTRAINT `cuentas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuentas_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `cuenta_misiones`
--
ALTER TABLE `cuenta_misiones`
  ADD CONSTRAINT `cuenta_misiones_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE,
  ADD CONSTRAINT `cuenta_misiones_ibfk_2` FOREIGN KEY (`id_mision`) REFERENCES `misiones` (`id_mision`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`id_leccion`) REFERENCES `lecciones` (`id_leccion`);

--
-- Filtros para la tabla `progreso_lecciones`
--
ALTER TABLE `progreso_lecciones`
  ADD CONSTRAINT `progreso_lecciones_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE,
  ADD CONSTRAINT `progreso_lecciones_ibfk_2` FOREIGN KEY (`id_leccion`) REFERENCES `lecciones` (`id_leccion`) ON DELETE CASCADE;

--
-- Filtros para la tabla `simulaciones`
--
ALTER TABLE `simulaciones`
  ADD CONSTRAINT `simulaciones_ibfk_1` FOREIGN KEY (`id_cuenta`) REFERENCES `cuentas` (`id_cuenta`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
