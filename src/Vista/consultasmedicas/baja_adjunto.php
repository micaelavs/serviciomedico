<?php

$vars_template['TEXTO_AVISO'] = 'Dará de baja  ';
$vars_template['ARTICULO'] = 'el documento';
$vars_vista['SUBTITULO'] = 'Baja de Archivos Adjuntos';
$vars_template['CONTROL'] = 'Adjunto:';
$vars_template['NOMBRE'] = preg_replace("/\d{14}_/", "", $archivo['nombre']);
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/ver_historial/'.$archivo['id_consulta_medica']);
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . '/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/defaults.js"];
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
