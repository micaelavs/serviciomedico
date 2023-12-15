<?php

use \FMT\Helper\Template;
use FMT\Vista;

$vars_vista['SUBTITULO'] = 'Reactivar Estado';
$vars_template['CONTROL'] = 'Estado';
$vars_template['ARTICULO'] = 'El';
$vars_template['TEXTO_AVISO'] = 'Reactivará';
$vars_template['NOMBRE'] = $estados->estado;
$vars_template['TEXTO_EXTRA'] = '.<br/>Al reactivarla volverá a visualizarla en el listado';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/estados/index/estados->id");
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
