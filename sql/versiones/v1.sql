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
  `tabla_nombre` enum('usuarios','medicos','estados','enfermeras','articulos','consultas_medicas', 'documentos_adjuntos', 'personas') DEFAULT NULL,
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `nombre` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `cantidad_dias_norma` INT(11) NOT NULL,
  `periodo_norma` tinyint(1),
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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
  `nombre` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `cantidad_dias_norma` INT(11) NOT NULL,
  `periodo_norma` tinyint(1),
  `borrado` TINYINT(1) NULL DEFAULT '0', 
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

USE `{{{db_log}}}`;



CREATE TABLE IF NOT EXISTS `documentos_adjuntos` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_documento_adjunto` int(11) unsigned NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `id_consulta_medica` int(11) NOT NULL,
  `fecha_alta` int(11) NOT NULL,
  `borrado` TINYINT(1) NULL DEFAULT '0', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`documentos_adjuntos_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `documentos_adjuntos_tg_insert` AFTER INSERT ON `documentos_adjuntos` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'documentos_adjuntos');
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `consultas_medicas` (  
#CAMPOS DE SEGUIMIENTO
  `id` bigint(20) unsigned  NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) unsigned  NOT NULL,
  `fecha_operacion` timestamp NOT NULL,
  `tipo_operacion` varchar(1) NOT NULL,
#CAMPOS DE LA TABLA TRACKEADA CON SU CAMPO "id" RENOMBRADO PARA EVITAR DUPLICIDAD
  `id_consulta_medica` int(11) unsigned NOT NULL,
  `id_estado` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `id_interviniente` int(11) NOT NULL,
  `cuit` VARCHAR(11) NOT NULL,
  `nombre_apellido` varchar(250) NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_intervencion` date NOT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NULL,
  `fecha_regreso_trabajo` date NULL,
  `fecha_nueva_revision` date NULL,
  `borrado` TINYINT(1) NULL DEFAULT '0', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_log}}}`.`consultas_medicas_tg_insert`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_insert` AFTER INSERT ON `consultas_medicas` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'consultas_medicas');
END $$
DELIMITER ;


USE `{{{db_app}}}`;

CREATE TABLE IF NOT EXISTS `documentos_adjuntos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) NOT NULL,
  `id_consulta_medica` int(11) NOT NULL,
  `fecha_alta` int(11) NOT NULL,
  `borrado` TINYINT(1) NULL DEFAULT '0', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla documentos_adjuntos
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`documentos_adjuntos_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `documentos_adjuntos_tg_alta` AFTER INSERT ON `documentos_adjuntos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.documentos_adjuntos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_documento_adjunto`,`nombre`,`id_consulta_medica`,`fecha_alta`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.id_consulta_medica,NEW.fecha_alta,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`documentos_adjuntos_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `documentos_adjuntos_tg_modificacion` AFTER UPDATE ON `documentos_adjuntos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.documentos_adjuntos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_documento_adjunto`,`nombre`,`id_consulta_medica`,`fecha_alta`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.id_consulta_medica,NEW.fecha_alta,NEW.borrado);
END $$
DELIMITER ;

CREATE TABLE IF NOT EXISTS `consultas_medicas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_estado` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `id_interviniente` int(11) NOT NULL,
  `cuit` VARCHAR(11) NOT NULL,
  `nombre_apellido` varchar(250) NOT NULL,
  `fecha_alta` date NOT NULL,
  `fecha_intervencion` date NOT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NULL,
  `fecha_regreso_trabajo` date NULL,
  `fecha_nueva_revision` date NULL,
  `borrado` TINYINT(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Triggers para la tabla consultas_medicas
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_alta`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_alta` AFTER INSERT ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`cuit`,`nombre_apellido`,`fecha_alta`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.cuit,NEW.nombre_apellido,NEW.fecha_alta,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END $$
DELIMITER ;
-- TRIGGER EJEMPLO
DELIMITER $$
DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_modificacion`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_modificacion` AFTER UPDATE ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`cuit`,`nombre_apellido`,`fecha_alta`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.cuit,NEW.nombre_apellido,NEW.fecha_alta,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END $$
DELIMITER ;



--02122021_consultas_medicas.sql

USE `{{{db_app}}}`;

ALTER TABLE `consultas_medicas` 
DROP COLUMN `nombre_apellido`,
DROP COLUMN `cuit`;


ALTER TABLE `consultas_medicas` 
ADD COLUMN `tipo_interviniente` TINYINT(1) NOT NULL AFTER `id_interviniente`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_alta` AFTER INSERT ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_modificacion` AFTER UPDATE ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;


USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`consultas_medicas` 
DROP COLUMN `nombre_apellido`,
DROP COLUMN `cuit`;

ALTER TABLE `{{{db_log}}}`.`consultas_medicas` 
ADD COLUMN `tipo_interviniente` TINYINT(1) NOT NULL AFTER `id_interviniente`;

--02122021_personas


USE `{{{db_log}}}`;

CREATE TABLE `personas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tipo_operacion` varchar(1) NOT NULL,
  `id_persona` int(11) unsigned NOT NULL,
  `cuit` int(11) NOT NULL,
  `apellido_nombre` varchar(250) NOT NULL,
  `apto` tinyint(1) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `grupo_sanguineo` tinyint(1) NOT NULL,
  `modalidad_vinculacion` varchar(250) NOT NULL,
  `fecha_apto` date NOT NULL,
  `borrado` varchar(45) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TRIGGER IF EXISTS `{{{db_log}}}`.`personas_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_insert` AFTER INSERT ON `personas` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'personas');
END$$
DELIMITER ;


USE `{{{db_app}}}`;

CREATE TABLE `personas` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cuit` INT(11) NOT NULL,
  `apellido_nombre` VARCHAR(250) NOT NULL,
  `apto` TINYINT(1) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `grupo_sanguineo` TINYINT(1) NOT NULL,
  `modalidad_vinculacion` VARCHAR(250) NOT NULL,
  `fecha_apto` DATE NOT NULL,
  `borrado` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`));

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_alta` AFTER INSERT ON `personas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`cuit`,`apellido_nombre`,`apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_modificacion` AFTER UPDATE ON `personas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`cuit`,`apellido_nombre`,`apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

--09122021_consultas_medicas

USE `{{{db_log}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_alta` `fecha_alta_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

USE `{{{db_app}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_alta` `fecha_alta_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_alta` AFTER INSERT ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_modificacion` AFTER UPDATE ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;



ALTER TABLE `{{{db_log}}}`.`consultas_medicas` 
ADD COLUMN `id_persona` INT(11) NOT NULL AFTER `id_consulta_medica`;

ALTER TABLE `{{{db_app}}}`.`consultas_medicas` 
ADD COLUMN `id_persona` INT(11) NOT NULL AFTER `id`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_alta` AFTER INSERT ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_persona`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_persona,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_modificacion` AFTER UPDATE ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_persona`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_persona,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.borrado);
END$$
DELIMITER ;



ALTER TABLE `{{{db_log}}}`.`consultas_medicas` 
ADD COLUMN `observacion` TEXT NULL DEFAULT NULL AFTER `fecha_nueva_revision`,
ADD COLUMN `medico_tratante` VARCHAR(255) NULL DEFAULT NULL AFTER `observacion`,
ADD COLUMN `telefono_contacto_tratante` VARCHAR(20) NULL DEFAULT NULL AFTER `medico_tratante`;

ALTER TABLE `{{{db_app}}}`.`consultas_medicas` 
ADD COLUMN `observacion` TEXT NULL DEFAULT NULL AFTER `fecha_nueva_revision`,
ADD COLUMN `medico_tratante` VARCHAR(255) NULL DEFAULT NULL AFTER `observacion`,
ADD COLUMN `telefono_contacto_tratante` VARCHAR(20) NULL DEFAULT NULL AFTER `medico_tratante`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_alta` AFTER INSERT ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_persona`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`observacion`,`medico_tratante`,`telefono_contacto_tratante`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_persona,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.observacion, NEW.medico_tratante, NEW.telefono_contacto_tratante, NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`consultas_medicas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `consultas_medicas_tg_modificacion` AFTER UPDATE ON `consultas_medicas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.consultas_medicas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_consulta_medica`,`id_persona`,`id_estado`,`id_articulo`,`id_interviniente`,`tipo_interviniente`,`fecha_alta_operacion`,`fecha_intervencion`,`fecha_desde`,`fecha_hasta`,`fecha_regreso_trabajo`,`fecha_nueva_revision`,`observacion`,`medico_tratante`,`telefono_contacto_tratante`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.id_persona,NEW.id_estado,NEW.id_articulo,NEW.id_interviniente,NEW.tipo_interviniente,NEW.fecha_alta_operacion,NEW.fecha_intervencion,NEW.fecha_desde,NEW.fecha_hasta,NEW.fecha_regreso_trabajo,NEW.fecha_nueva_revision,NEW.observacion, New.medico_tratante, NEW.telefono_contacto_tratante, NEW.borrado);
END$$
DELIMITER ;

--09122021_documentos_adjuntos


ALTER TABLE `{{{db_log}}}`.`documentos_adjuntos` 
CHANGE COLUMN `fecha_alta` `fecha_alta_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `{{{db_app}}}`.`documentos_adjuntos` 
CHANGE COLUMN `fecha_alta` `fecha_alta_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`documentos_adjuntos_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `documentos_adjuntos_tg_alta` AFTER INSERT ON `documentos_adjuntos` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.documentos_adjuntos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_documento_adjunto`,`nombre`,`id_consulta_medica`,`fecha_alta_operacion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.nombre,NEW.id_consulta_medica,NEW.fecha_alta_operacion,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`documentos_adjuntos_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `documentos_adjuntos_tg_modificacion` AFTER UPDATE ON `documentos_adjuntos` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.documentos_adjuntos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_documento_adjunto`,`nombre`,`id_consulta_medica`,`fecha_alta_operacion`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.nombre,NEW.id_consulta_medica,NEW.fecha_alta_operacion,NEW.borrado);
END$$
DELIMITER ;

--15122021_personas

ALTER TABLE `{{{db_log}}}`.`personas` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NOT NULL ;

ALTER TABLE `{{{db_app}}}`.`personas` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NOT NULL ;

--04012022_personas

USE `{{{db_log}}}`;

ALTER TABLE `personas` 
ADD COLUMN `id_sigarhu` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `id_persona`;

USE `{{{db_app}}}`;

ALTER TABLE `personas` 
ADD COLUMN `id_sigarhu` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `id`;


DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_alta` AFTER INSERT ON `personas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`cuit`,`apellido_nombre`,`apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_sigarhu,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_modificacion` AFTER UPDATE ON `personas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`cuit`,`apellido_nombre`,`apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id, NEW.id_sigarhu, NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

--05012022-personas


ALTER TABLE `{{{db_log}}}`.`personas` 
CHANGE COLUMN `apto` `apto` TINYINT(1) NULL DEFAULT NULL ,
CHANGE COLUMN `fecha_nacimiento` `fecha_nacimiento` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `grupo_sanguineo` `grupo_sanguineo` TINYINT(1) NULL DEFAULT NULL ,
CHANGE COLUMN `modalidad_vinculacion` `modalidad_vinculacion` VARCHAR(250) NULL DEFAULT NULL ,
CHANGE COLUMN `fecha_apto` `fecha_apto` DATE NULL DEFAULT NULL ;

ALTER TABLE `{{{db_app}}}`.`personas` 
CHANGE COLUMN `apto` `apto` TINYINT(1) NULL DEFAULT NULL ,
CHANGE COLUMN `fecha_nacimiento` `fecha_nacimiento` DATE NULL DEFAULT NULL ,
CHANGE COLUMN `grupo_sanguineo` `grupo_sanguineo` TINYINT(1) NULL DEFAULT NULL ,
CHANGE COLUMN `modalidad_vinculacion` `modalidad_vinculacion` VARCHAR(250) NULL DEFAULT NULL ,
CHANGE COLUMN `fecha_apto` `fecha_apto` DATE NULL DEFAULT NULL ;


USE `{{{db_log}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_intervencion` `fecha_intervencion` DATETIME NOT NULL ;


USE `{{{db_app}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_intervencion` `fecha_intervencion` DATETIME NOT NULL ;

USE `{{{db_log}}}`;

ALTER TABLE `personas` 
ADD COLUMN `tipo_apto` TINYINT(1) NULL DEFAULT NULL AFTER `apto`;

USE `{{{db_app}}}`;

ALTER TABLE `personas` 
ADD COLUMN `tipo_apto` TINYINT(1) NULL DEFAULT NULL AFTER `apto`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_alta` AFTER INSERT ON `personas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`cuit`,`apellido_nombre`,`apto`,`tipo_apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_sigarhu,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.tipo_apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_modificacion` AFTER UPDATE ON `personas` FOR EACH ROW
BEGIN
INSERT INTO `{{{db_log}}}`.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`cuit`,`apellido_nombre`,`apto`,`tipo_apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id, NEW.id_sigarhu, NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.tipo_apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

/*Se inserta este artículo para la emisión del apto médico*/
USE `{{{db_app}}}`;
set @id_usuario = 9999;
INSERT INTO `articulos` (`nombre`, `descripcion`, `cantidad_dias_norma`, `periodo_norma`) VALUES ('Apto (Emisión certificado)', 'Para emisión de certificado apto', '0', '4');


-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO {{{db_app}}}.db_version VALUES('1.0', now());
INSERT INTO {{{db_log}}}.db_version VALUES('1.0', now());
