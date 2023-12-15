<?php

use \FMT\Template;
use \App\Helper\Vista;
$rol = App\Modelo\AppRoles::obtener_rol();
$config = FMT\Configuracion::instancia();
$vars_template['URL_BASE'] = Vista::get_url();
$vars_vista['SUBTITULO'] = 'Historial de archivos adjuntos.';
$opciones_apto	= json_encode([
	'apto_si' => App\Modelo\Persona::APTO_SI,
	'apto_no' =>  App\Modelo\Persona::APTO_NO
], JSON_UNESCAPED_UNICODE);

$roles = json_encode([
	'medico' => App\Modelo\AppRoles::ROL_MEDICO,
	'enfermera' => App\Modelo\AppRoles::ROL_ENFERMERA,
	'rrhh'		=> App\Modelo\AppRoles::ROL_RRHH,
	'administrador' => App\Modelo\AppRoles::ROL_ADMINISTRACION
], JSON_UNESCAPED_UNICODE);

$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/consultasmedicas/exportar_adjuntos_excel");

$ver_historial = new Template(TEMPLATE_PATH . '/consultasmedicas/ver_historial.html', $vars_template, ['CLEAN' => false]);

$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('/consultasmedicas/consultasmedicas.js');

$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$endpoint_cdn = $config['app']['endpoint_cdn'];
$base_url = \App\Helper\Vista::get_url();
$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
var \$base_url        = "{$base_url}";
var \$id_consulta     = "{$id_consulta}";
var \$opciones_apto   = {$opciones_apto};
var \$roles   = {$roles};
var \$rol_actual = {$rol};
JS;

$vars_vista['CONTENT'] = "{$ver_historial}";
$vista->add_to_var('vars', $vars_vista);
