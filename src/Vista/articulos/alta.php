<?php

/** @var $vista */
/** @var $articulos */
/** @var $periodo_por_norma */

$vars_vista['SUBTITULO']		= 'Alta de Articulos';
$vars_template['OPERACION']		= 'alta';
$vars_template['NOMBRE']= !empty($articulos->nombre) ? $articulos->nombre:'';
$vars_template['DESCRIPCION']= !empty($articulos->descripcion) ? $articulos->descripcion:'';
$vars_template['CANTIDAD_DIAS_NORMA']= !empty($articulos->cantidad_dias_norma) ? $articulos->cantidad_dias_norma:'';
$vars_template['PERIODO_POR_NORMA']= \FMT\Helper\Template::select_block($periodo_por_norma,$articulos->periodo_norma);


$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/articulos/index');

$template = (new \FMT\Template(VISTAS_PATH.'/templates/articulos/'.'alta.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['CONTENT'] = "$template";


$vars_vista['JS'][]['JS_CODE'] = <<<JS
        
JS;
$vista->add_to_var('vars',$vars_vista);

return true;
?>
