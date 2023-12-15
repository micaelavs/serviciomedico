<?php

use \FMT\Helper\Template;
use FMT\Vista;

$vars_vista['SUBTITULO'] = 'Reactivar Artículo';
$vars_template['CONTROL'] = 'Artículo';
$vars_template['ARTICULO'] = 'El';
$vars_template['TEXTO_AVISO'] = 'Reactivará';
$vars_template['NOMBRE'] = $articulo->nombre;
$vars_template['TEXTO_EXTRA'] = '.<br/>Al reactivarlo volverá a visualizarla en el listado';
$vars_template['CANCELAR'] = \App\Helper\Vista::get_url("index.php/articulos/index/articulo->id");
$template = (new \FMT\Template(VISTAS_PATH . '/widgets/confirmacion.html', $vars_template, ['CLEAN' => false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars', $vars_vista);
return true;
