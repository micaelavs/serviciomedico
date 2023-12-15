
/**db_log**/

ALTER TABLE `{{{db_log}}}`.`_registros_abm` 
CHANGE COLUMN `tabla_nombre` `tabla_nombre` ENUM('usuarios', 'medicos', 'estados', 'enfermeras', 'articulos', 'consultas_medicas', 'documentos_adjuntos', 'personas', 'comprobantes_enviados') NULL DEFAULT NULL ;

CREATE TABLE `{{{db_log}}}`.`comprobantes_enviados` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) UNSIGNED NOT NULL,
  `fecha_operacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  `tipo_operacion` VARCHAR(1) NOT NULL,
  `id_comprobante_enviado` INT(11) NOT NULL,
  `id_consulta_medica` INT(11) NOT NULL,
  `enviado` TINYINT(1) NULL DEFAULT 0,
  `email` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

DROP TRIGGER IF EXISTS `{{{db_log}}}`.`comprobantes_enviados_tg_insert`;

DELIMITER $$
USE `{{{db_log}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `comprobantes_enviados_tg_insert` AFTER INSERT ON `comprobantes_enviados` FOR EACH ROW BEGIN
    INSERT INTO _registros_abm(`id_tabla`, `id_usuario`, `fecha_operacion`, `tipo_operacion`, `tabla_nombre`) VALUES (NEW.id,NEW.id_usuario,NEW.fecha_operacion,NEW.tipo_operacion,'comprobantes_enviados');
END$$
DELIMITER ;


/*db*/

CREATE TABLE `{{{db_app}}}`.`comprobantes_enviados` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_consulta_medica` INT(11) UNSIGNED NOT NULL,
  `enviado` TINYINT(1) NULL DEFAULT 0,
  `email` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`comprobantes_enviados_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `comprobantes_enviados_tg_alta` AFTER INSERT ON `comprobantes_enviados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.comprobantes_enviados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_comprobante_enviado`,`id_consulta_medica`,`enviado`,`email`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_consulta_medica,NEW.enviado,NEW.email);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`comprobantes_enviados_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `comprobantes_enviados_tg_modificacion` AFTER UPDATE ON `comprobantes_enviados` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.comprobantes_enviados(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_comprobante_enviado`,`id_consulta_medica`,`enviado`,`email`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"M",OLD.id,NEW.id_consulta_medica,NEW.enviado,NEW.email);
END$$
DELIMITER ;

