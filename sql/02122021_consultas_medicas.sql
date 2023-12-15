
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


