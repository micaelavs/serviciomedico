#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.

CREATE DATABASE  IF NOT EXISTS `{{{db_log}}}` DEFAULT CHARACTER SET utf8 ;
USE `{{{db_log}}}`;

--
-- Tabla que indexa todas las operacion que entran en el log `_registros_abm`
-- NOTA: cada vez que se agregue una tabla al esquema debe actualizarse el campo tabla_nombre
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `_registros_abm` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_operacion` char(1) DEFAULT NULL,
  `id_tabla` bigint(20) unsigned NOT NULL,
  `tabla_nombre` enum('usuarios','medicos','estados','enfermeras','articulos') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fecha_operacion` (`fecha_operacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TABLA EJEMPLO
CREATE TABLE IF NOT EXISTS `estados` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_estado` int(11) unsigned NOT NULL,
  `estado` varchar(10) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`estados_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_insert` AFTER INSERT ON `estados` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'estados');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `medicos` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_medico` int(11) unsigned NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`medicos_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `medicos_tg_insert` AFTER INSERT ON `medicos` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'medicos');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `enfermeras` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_enfermera` int(11) unsigned NOT NULL,
  `matricula` varchar(10) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`enfermeras_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `enfermeras_tg_insert` AFTER INSERT ON `enfermeras` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'enfermeras');
END $$
DELIMITER ;


CREATE TABLE IF NOT EXISTS `articulos` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_articulo` int(11) unsigned NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `cantidad_dias_norma` int(5) NOT NULL,
  `periodo_norma` tinyint(1),
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Trigger para indexacion
-- TRIGGER EJEMPLO
--
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`articulos_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `articulos_tg_insert` AFTER INSERT ON `articulos` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'articulos');
END $$
DELIMITER ;

--
-- NOTA: Aunque el manejo y configuracion de usuarios es externo a la aplicacion, deben registrarse todos lo cambios generados
-- TABLA OBLIGATORIA
--

CREATE TABLE IF NOT EXISTS  `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned DEFAULT NULL,
  `fecha_operacion` timestamp NULL DEFAULT NULL,
  `tipo_operacion` varchar(1) DEFAULT NULL,
  `id_usuario_panel` int(10) unsigned NOT NULL,
  `id_rol` int(10) unsigned NOT NULL,
  `username` varchar(30) NOT NULL,
  `metadata` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- TRIGGER OBLIGATORIO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`usuarios_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `usuarios_tg_insert` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'usuarios');
END $$
DELIMITER ;



CREATE DATABASE  IF NOT EXISTS `{{{db_app}}}` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `{{{db_app}}}`;

--
-- Tabla de registro de version
-- TABLA OBLIGATORIA
--
CREATE TABLE IF NOT EXISTS  `db_version` (
  `version` mediumint(5) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- TABLA EJEMPLO
CREATE TABLE IF NOT EXISTS `estados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `estado` varchar(10) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla estados
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`estados_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_alta` AFTER INSERT ON `estados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.estados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_estado`,`estado`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.estado,NEW.descripcion,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`estados_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `estados_tg_modificacion` AFTER UPDATE ON `estados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.estados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_estado`,`estado`,`descripcion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.estado,NEW.descripcion,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `medicos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matricula` varchar(10) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla estados
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`medicos_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `medicos_tg_alta` AFTER INSERT ON `medicos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.medicos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_medico`,`matricula`,`nombre`,`apellido`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`medicos_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `medicos_tg_modificacion` AFTER UPDATE ON `medicos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.medicos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_medico`,`matricula`,`nombre`,`apellido`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `enfermeras` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matricula` varchar(10) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla estados
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`enfermeras_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `enfermeras_tg_alta` AFTER INSERT ON `enfermeras` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.enfermeras(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_enfermera`,`matricula`,`nombre`,`apellido`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`enfermeras_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `enfermeras_tg_modificacion` AFTER UPDATE ON `enfermeras` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.enfermeras(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_enfermera`,`matricula`,`nombre`,`apellido`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `articulos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `cantidad_dias_norma` int(5) NOT NULL,
  `periodo_norma` tinyint(1),
  `borrado` tinyint(1), 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla estados
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`articulos_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `articulos_tg_alta` AFTER INSERT ON `articulos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.articulos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_articulo`,`nombre`,`descripcion`,`cantidad_dias_norma`,`periodo_norma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.descripcion,NEW.cantidad_dias_norma,NEW.periodo_norma,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`articulos_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `articulos_tg_modificacion` AFTER UPDATE ON `articulos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.articulos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_articulo`,`nombre`,`descripcion`,`cantidad_dias_norma`,`periodo_norma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.descripcion,NEW.cantidad_dias_norma,NEW.periodo_norma,NEW.borrado);
END $$
DELIMITER ;


-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO {{{db_app}}}.db_version VALUES('1.0', now());
INSERT INTO {{{db_log}}}.db_version VALUES('1.0', now());
