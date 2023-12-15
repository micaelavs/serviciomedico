<?php
use \FMT\Template;
use \App\Helper\Vista;

$config = FMT\Configuracion::instancia();
$vars_template['URL_BASE'] = Vista::get_url();
$vars_vista['SUBTITULO'] = 'Resumen de Eventos del Agente';
$vars_template['BOTON_EXCEL'] = \App\Helper\Vista::get_url("index.php/consultasmedicas/exportar_excel_resumen_eventos/".$persona->dni);
$vars_template['DNI']            = $persona->dni;
$vars_template['APELLIDO_NOMBRE'] = $persona->apellido_nombre;
$eventos  = new Template(TEMPLATE_PATH . '/consultasmedicas/resumen_eventos.html', $vars_template,  ['CLEAN' => false]);
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = Vista::get_url('consultasmedicas/resumeneventos.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('script.js');
$vars_vista['CSS_FILES'][]  = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . '/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]   = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/defaults.js"];
$dni = $persona->dni;
$endpoint_cdn = $config['app']['endpoint_cdn'];
$base_url = \App\Helper\Vista::get_url();
$vars_vista['JS'][]['JS_CODE'] = <<<JS
    var \$endpoint_cdn  = "{$endpoint_cdn}";
    var \$base_url      = "{$base_url}";
    var \$dni          = "{$dni}";
JS;
$vars_vista['CONTENT'][] = "{$eventos}";
//Hace la composicion del template base con el funcional.
$vista->add_to_var('vars', $vars_vista);
