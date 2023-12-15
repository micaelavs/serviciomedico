
USE `{{{db_log}}}`;

ALTER TABLE `enfermeras` 
ADD COLUMN `firma` VARCHAR(250) NULL DEFAULT NULL AFTER `apellido`;

/*db*/
USE `{{{db_app}}}`;

ALTER TABLE `enfermeras` 
ADD COLUMN `firma` VARCHAR(250) NULL DEFAULT NULL AFTER `apellido`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`enfermeras_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `enfermeras_tg_alta` AFTER INSERT ON `enfermeras` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.enfermeras(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_enfermera`,`matricula`,`nombre`,`apellido`,`firma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.firma,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`enfermeras_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `enfermeras_tg_modificacion` AFTER UPDATE ON `enfermeras` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.enfermeras(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_enfermera`,`matricula`,`nombre`,`apellido`,`firma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.firma,NEW.borrado);
END$$
DELIMITER ;


/*medicos*/

/*log*/

USE `{{{db_log}}}`;

ALTER TABLE `medicos` 
ADD COLUMN `firma` VARCHAR(250) NULL DEFAULT NULL AFTER `apellido`;


/*db*/

USE `{{{db_app}}}`;

ALTER TABLE `medicos` 
ADD COLUMN `firma` VARCHAR(250) NULL DEFAULT NULL AFTER `apellido`;

DROP TRIGGER IF EXISTS `{{{db_app}}}`.`medicos_tg_alta`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `medicos_tg_alta` AFTER INSERT ON `medicos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.medicos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_medico`,`matricula`,`nombre`,`apellido`,`firma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),"A",NEW.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.firma,NEW.borrado);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS `medicos_tg_modificacion`;

DELIMITER $$
USE `{{{db_app}}}`$$
CREATE DEFINER=`{{{user_mysql}}}`@`%` TRIGGER `medicos_tg_modificacion` AFTER UPDATE ON `medicos` FOR EACH ROW
BEGIN
INSERT INTO {{{db_log}}}.medicos(`id_usuario`,`fecha_operacion`,`tipo_operacion`,`id_medico`,`matricula`,`nombre`,`apellido`,`firma`,`borrado`)
VALUES (@id_usuario,CURRENT_TIMESTAMP(),IF(NEW.borrado = 1, "B", "M"),OLD.id,NEW.matricula,NEW.nombre,NEW.apellido,NEW.firma,NEW.borrado);
END$$
DELIMITER ;

-- INSERT Obligatorio en las versiones, no asi en los script de desarrollo.
INSERT INTO  `{{{db_app}}}`.db_version VALUES('4.0', now());
INSERT INTO  `{{{db_log}}}`.db_version VALUES('4.0', now());