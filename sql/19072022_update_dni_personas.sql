USE `{{{db_app}}}`;
UPDATE personas SET dni = SUBSTRING(cuit, 3, 8) WHERE cuit IS NOT NULL;