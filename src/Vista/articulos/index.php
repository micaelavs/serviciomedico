<?php
use FMT\Helper\Arr;
use App\Helper\Vista;
/**
 * $vars_template |Variable de configuracion para el template de la funcionalidad que se esta desarrollando.
 * $vars_vista  |Variable de configuracion para el template general. Llega a la vista por medio de la variable "vista"
 * propagada por la clase Vista.
 **/

/** @var  $vars_template */
/** @var  $vista */
/** @var  $articulos */
/** @var  $periodo_por_norma */

$vars_template = [];
$vars_vista['SUBTITULO'] = 'Lista de Articulos';
$vars_template['TITULOS'] = [
    ['TITULO' => 'Nombre'],
    ['TITULO' => 'Descripcion'],
    ['TITULO' => 'Cantidad de días por norma'],
    ['TITULO' => 'Periodo por norma'],
    ['TITULO'=>'Acciones']

];

foreach ($articulos as $td) {

    $vars_template['ROW'][] =
        ['COL' => [
            ['CONT'=>$td['nombre']],
            ['CONT'=>$td['descripcion']],
            ['CONT'=>$td['cantidad_dias_norma']],
            ['CONT'=>Arr::get($periodo_por_norma, $td['periodo_norma']) ? $periodo_por_norma[$td['periodo_norma']]['nombre'] : ''],
            ['CONT'=>[Vista::renderAccionesModifBaja('Articulos',$td['id'])],]
        ]
        ];
}
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('articulos/articulos.js');
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];

$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/dataTables.buttons.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/jszip/2.5.0/jszip.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/buttons.bootstrap.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/extensions/buttons/1.2.4/js/buttons.html5.min.js"];


$vars_vista['JS'][]['JS_CODE'] = <<<JS
	var \$endpoint_cdn = '{$vista->getSystemConfig()['app']['endpoint_cdn']}';
JS;
$vars_template['NUEVO'] = \App\Helper\Vista::nuevoRegistroAbm('Articulos');
$vars_template['URL_BASE'] = \App\Helper\Vista::get_url();
$vars_template['LINK'] = \App\Helper\Vista::get_url('index.php/articulos/alta');
$vars_template['DATOS_TABLA'][] = new \FMT\Template(TEMPLATE_PATH . '/tabla.html', $vars_template, ['CLEAN' => false]);

$articulos_template = new \FMT\Template(TEMPLATE_PATH . '/articulos/index'.'.html', $vars_template, ['CLEAN' => false]);

$vars_vista['CONTENT'] = "{$articulos_template}";

//Hace la composicion del template base con el funcional.
$vista->add_to_var('vars', $vars_vista);
return true;
