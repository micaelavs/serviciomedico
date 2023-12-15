<?php
use \FMT\Template;
use \App\Helper\Vista;

$config = FMT\Configuracion::instancia();
$vars_template['URL_BASE'] = Vista::get_url();
$vars_vista['SUBTITULO'] = 'Historia Clínica del Agente.';
$vars_template['DNI'] = !empty($persona->dni) ? $persona->dni : '';
$vars_template['APELLIDO_NOMBRE'] = !empty($persona->apellido_nombre) ? $persona->apellido_nombre : '';
$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/consultasmedicas/exportar_historia_clinica_excel/".$persona->dni); 
$vars_template['ARTICULOS'] = \FMT\Helper\Template::select_block($articulos);
$vars_template['ESTADOS'] = \FMT\Helper\Template::select_block($estados);
$vars_template['INTERVINIENTES'] = \FMT\Helper\Template::select_block($intervinientes);
$historia_clinica = new Template(TEMPLATE_PATH . '/consultasmedicas/historia_clinica.html', $vars_template, ['CLEAN' => false]);

$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('/consultasmedicas/historia_clinica.js');

$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$dni = $persona->dni;
$endpoint_cdn = $config['app']['endpoint_cdn'];
$base_url = \App\Helper\Vista::get_url();
$vars_vista['JS'][]['JS_CODE']    = <<<JS
var \$endpoint_cdn    = "{$endpoint_cdn}";
var \$base_url        = "{$base_url}";
var \$dni        = "{$dni}";
JS;

$vars_vista['CONTENT'] = "{$historia_clinica}";
$vista->add_to_var('vars', $vars_vista);