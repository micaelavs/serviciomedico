
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

