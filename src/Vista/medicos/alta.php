<?php

/** @var $vista */
/** @var $medicoss */


$vars_vista['SUBTITULO']		= 'Alta de Medicos';
$vars_template['OPERACION']		= 'alta';
$vars_template['MATRICULA']     = !empty($medicos->matricula) ? $medicos->matricula:'';
$vars_template['NOMBRE']        = !empty($medicos->nombre) ? $medicos->nombre:'';
$vars_template['APELLIDO']      = !empty($medicos->apellido) ? $medicos->apellido:'';
$vars_template['CANCELAR']      = \App\Helper\Vista::get_url('index.php/medicos/index');

$template = (new \FMT\Template(VISTAS_PATH.'/templates/medicos/'.'alta.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('doc-adjuntos.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('fileinput.min.js');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('documentos.css');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('fileinput.min.css');
$vars_vista['CONTENT'] = "$template";
$vars_vista['JS'][]['JS_CODE'] = <<<JS

JS;
$vista->add_to_var('vars',$vars_vista);

return true;
