<?php

/** @var $vista */
/** @var $enfermeras */


$vars_vista['SUBTITULO']		= 'Modificar  Enfermeras';
$vars_template['OPERACION']		= 'modificacion';
$vars_template['MATRICULA']= !empty($enfermeras->matricula) ? $enfermeras->matricula:'';
$vars_template['NOMBRE']= !empty($enfermeras->nombre) ? $enfermeras->nombre:'';
$vars_template['APELLIDO']= !empty($enfermeras->apellido) ? $enfermeras->apellido:'';
$vars_template['ARCHIVO']= !empty($enfermeras->firma) ? $enfermeras->firma:'Ninguno';

$vars_template['CANCELAR'] = \App\Helper\Vista::get_url('index.php/enfermeras/index');

$template = (new \FMT\Template(VISTAS_PATH.'/templates/enfermeras/'.'modificacion.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['CONTENT'] = "$template";
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('doc-adjuntos.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('fileinput.min.js');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('documentos.css');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('fileinput.min.css');

$vars_vista['JS'][]['JS_CODE'] = <<<JS
        
JS;
$vista->add_to_var('vars',$vars_vista);

return true;
?>
