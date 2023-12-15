USE `{{{db_log}}}`;

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('usuarios','medicos','estados','enfermeras','articulos','consultas_medicas','documentos_adjuntos') NULL DEFAULT NULL ;

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
  `cuit` int(11) NOT NULL,
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
  `cuit` int(11) NOT NULL,
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