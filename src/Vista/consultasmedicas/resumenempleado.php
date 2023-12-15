<?php

use App\Helper\Vista;
use \FMT\Helper\Template;
/**
 * $vars_template |Variable de configuracion para el template de la funcionalidad que se esta desarrollando.
 * $vars_vista  |Variable de configuracion para el template general. Llega a la vista por medio de la variable "vista"
 * propagada por la clase Vista.
 **/

/** @var  $vista */


$vars_template = [];
$vars_vista['SUBTITULO'] = 'Resumen intervenciones - empleados';

$vars_vista['CSS_FILES'][] = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/plugins/sorting/datetime-moment.js"];

$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/dataTables.buttons.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/jszip/2.5.0/jszip.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/buttons.bootstrap.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/buttons.html5.min.js"];



$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = Vista::get_url('consultasmedicas/resumenempleado.js');
$url_base = Vista::get_url();
$vars_vista['JS'][]['JS_CODE'] = <<<JS
	let \$endpoint_cdn = '{$vista->getSystemConfig()['app']['endpoint_cdn']}';
    let base_url        = "{$url_base}"
JS;

$vars_vista['DATABLE_JS'] = Vista::get_url('dataTables.rowGroup.min.js');
$vars_vista['DATABLE_CSS'] = Vista::get_url('rowGroup.dataTables.min.css');
$vars_template['URL_BASE'] = Vista::get_url();
$reportes  = new \FMT\Template(TEMPLATE_PATH . '/consultasmedicas/resumenempleado'.'.html', $vars_template,  ['CLEAN' => false]);

$vars_vista['CONTENT'][] = "{$reportes}";

//Hace la composicion del template base con el funcional.
$vista->add_to_var('vars', $vars_vista);
return true;
