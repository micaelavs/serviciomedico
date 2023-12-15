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





