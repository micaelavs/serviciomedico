/*Se inserta este artículo para la emisión del apto médico*/
USE `{{{db_app}}}`;
set @id_usuario = 9999;
INSERT INTO `articulos` (`nombre`, `descripcion`, `cantidad_dias_norma`, `periodo_norma`) VALUES ('Apto (Emisión certificado)', 'Para emisión de certificado apto', '0', '4');