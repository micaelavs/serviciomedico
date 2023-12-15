<?php

$vars_template['TEXTO_AVISO'] = 'Dará de baja  ';
$vars_template['ARTICULO'] = 'el registro';
$vars_vista['SUBTITULO'] = 'Baja de Consultas Medicas';
$vars_template['CONTROL'] = ' de consulta medica:';
$vars_template['NOMBRE'] = ' Paciente CUIT: '. $persona->cuit.'. ' . $persona->apellido_nombre. ' con fecha de Intervención: ' . $consultamedica->fecha_intervencion->format('d-m-Y') . ' del Artículo: ' .$articulo->nombre;
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/index');
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . '/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/defaults.js"];
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
