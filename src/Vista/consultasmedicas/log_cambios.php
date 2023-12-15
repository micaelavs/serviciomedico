<?php
use \FMT\Template;
use \App\Helper\Vista;
$config = FMT\Configuracion::instancia();
$vars_template['URL_BASE'] = Vista::get_url();
$vars_vista['SUBTITULO'] = 'Historial de cambios.';
$vars_template['USUARIOS'] = \FMT\Helper\Template::select_block($new_users);
$vars_template['DNI'] = \FMT\Helper\Template::select_block($personas);
$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/consultasmedicas/log_cambios_exportar_excel/"); 
$historia_clinica = new Template(TEMPLATE_PATH . '/consultasmedicas/log_cambios.html', $vars_template, ['CLEAN' => false]);
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('/consultasmedicas/log_cambios.js');
$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$endpoint_cdn = $config['app']['endpoint_cdn'];
$base_url = \App\Helper\Vista::get_url();
$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
var \$base_url        = "{$base_url}";
JS;

$vars_vista['CONTENT'] = "{$historia_clinica}";
$vista->add_to_var('vars', $vars_vista);