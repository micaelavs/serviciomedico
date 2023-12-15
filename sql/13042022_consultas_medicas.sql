
USE `{{{db_log}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_intervencion` `fecha_intervencion` DATETIME NOT NULL ;


USE `{{{db_app}}}`;

ALTER TABLE `consultas_medicas` 
CHANGE COLUMN `fecha_intervencion` `fecha_intervencion` DATETIME NOT NULL ;
