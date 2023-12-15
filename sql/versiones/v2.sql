
ALTER TABLE `{{{db_log}}}`.`personas` 
ADD COLUMN `dni` VARCHAR(10) NOT NULL AFTER `id_sigarhu`,
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL ;

ALTER TABLE `{{{db_app}}}`.`personas` 
ADD COLUMN `dni` VARCHAR(10) NOT NULL AFTER `id_sigarhu`,
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NULL ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_alta` AFTER INSERT ON `personas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`dni`,`cuit`,`apellido_nombre`,`apto`,`tipo_apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.id_sigarhu,NEW.dni,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.tipo_apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`personas_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `personas_tg_modificacion` AFTER UPDATE ON `personas` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.personas(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_persona`,`id_sigarhu`,`dni`,`cuit`,`apellido_nombre`,`apto`,`tipo_apto`,`fecha_nacimiento`,`grupo_sanguineo`,`modalidad_vinculacion`,`fecha_apto`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id, NEW.id_sigarhu,NEW.dni,NEW.cuit,NEW.apellido_nombre,NEW.apto,NEW.tipo_apto,NEW.fecha_nacimiento,NEW.grupo_sanguineo,NEW.modalidad_vinculacion,NEW.fecha_apto,NEW.borrado);
END$$
DELIMITER ;

USE `{{{db_app}}}`;
UPDATE personas SET dni = SUBSTRING(cuit, 3, 8) WHERE cuit IS NOT NULL;

USE `{{{db_log}}}`;
UPDATE personas SET dni = SUBSTRING(cuit, 3, 8) WHERE cuit IS NOT NULL;

-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO  `{{{db_app}}}`.db_version VALUES('2.0', now());
INSERT INTO  `{{{db_log}}}`.db_version VALUES('2.0', now());