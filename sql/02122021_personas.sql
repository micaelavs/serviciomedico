
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('usuarios', 'medicos', 'estados', 'enfermeras', 'articulos', 'consultas_medicas', 'documentos_adjuntos', 'personas') NULL DEFAULT NULL ;


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




 

