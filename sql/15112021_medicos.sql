#SIEMPRE SE ACTUALIZARA LA VERSIÓN DE LAS DOS DBs AUNQUE NO SE HAGAN CAMBIOS.
#REMPLAZAR ANTES DE EJECUTAR
# {{{user_mysql}}}  = REEMPLAZAR POR NOMBRE USER QUE EJECUTA.
# {{{db_log}}}      = REEMPLAZAR POR NOMBRE DB LOG.
# {{{db_app}}}      = REEMPLAZAR POR NOMBRE DB APP.

USE `{{{db_app}}}`;
ALTER TABLE `medicos` CHANGE `borrado` `borrado` TINYINT(1) NULL DEFAULT '0';

USE `{{{db_log}}}`;
ALTER TABLE `medicos` CHANGE `borrado` `borrado` TINYINT(1) NULL DEFAULT '0';