
USE `{{{db_log}}}`;

ALTER TABLE `comprobantes_enviados` 
ADD COLUMN `fecha_operacion_control` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() AFTER `email`;


USE `{{{db_app}}}`;

ALTER TABLE `comprobantes_enviados` 
ADD COLUMN `fecha_operacion_control` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() AFTER `email`;

/*triggers*/

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`comprobantes_enviados_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `comprobantes_enviados_tg_alta` AFTER INSERT ON `comprobantes_enviados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.comprobantes_enviados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_comprobante_enviado`,`id_consulta_medica`,`enviado`,`email`,`fecha_operacion_control`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_consulta_medica,NEW.enviado,NEW.email,NEW.fecha_operacion_control);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`comprobantes_enviados_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `comprobantes_enviados_tg_modificacion` AFTER UPDATE ON `comprobantes_enviados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.comprobantes_enviados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_comprobante_enviado`,`id_consulta_medica`,`enviado`,`email`,`fecha_operacion_control`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"M",OLD.id,NEW.id_consulta_medica,NEW.enviado,NEW.email,NEW.fecha_operacion_control);
END$$
DELIMITER ;


