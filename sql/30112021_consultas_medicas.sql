USE `{{{db_app}}}`;

ALTER TABLE `consultas_medicas` CHANGE `cuit` `cuit` VARCHAR(11) NOT NULL;

USE `{{{db_log}}}`;

ALTER TABLE `consultas_medicas` CHANGE `cuit` `cuit` VARCHAR(11) NOT NULL;

