
ALTER TABLE `{{{db_log}}}`.`personas` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NOT NULL ;

ALTER TABLE `{{{db_app}}}`.`personas` 
CHANGE COLUMN `cuit` `cuit` DECIMAL(11,0) NOT NULL ;
