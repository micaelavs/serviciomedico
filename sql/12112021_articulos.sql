
#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.


USE `{{{db_app}}}`;
ALTER TABLE `articulos` CHANGE `nombre` `nombre` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `articulos` CHANGE `borrado` `borrado` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `articulos` CHANGE `cantidad_dias_norma` `cantidad_dias_norma` INT(11) NOT NULL;

USE `{{{db_log}}}`;
ALTER TABLE `articulos` CHANGE `nombre` `nombre` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `articulos` CHANGE `borrado` `borrado` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `articulos` CHANGE `cantidad_dias_norma` `cantidad_dias_norma` INT(11) NOT NULL;