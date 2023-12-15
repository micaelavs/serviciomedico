
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



