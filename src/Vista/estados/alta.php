<?php

/** @var $vista */
/** @var $estados */


$vars_vista['SUBTITULO']        = 'Alta de Estados';
$vars_template['OPERACION']     = 'alta';
$vars_template['ESTADO']        = !empty($estados->estado) ? $estados->estado : '';
$vars_template['DESCRIPCION']   = !empty($estados->descripcion) ? $estados->descripcion : '';
$vars_template['CANCELAR']      = \App\Helper\Vista::get_url('index.php/estados/index');

$template = (new \FMT\Template(VISTAS_PATH . '/templates/estados/' . 'alta.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CSS_FILES'][]    = ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . '/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]    = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]    = ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'] . "/datatables/defaults.js"];
$vars_vista['CONTENT'] = "$template";
$vars_vista['JS'][]['JS_CODE'] = <<<JS

JS;
$vista->add_to_var('vars', $vars_vista);

return true;
