<?php
use App\Helper\Vista;
/**
 * @var \App\Modelo\Enfermera $enfermeras
 * @var $vista
 */

$vars_vista['SUBTITULO'] = 'Reactivar "Enfermera"';
$vars_template['TEXTO_AVISO'] = '';
$vars_template['ARTICULO'] = 'La ';
$vars_template['CONTROL'] = 'Enfermera con matrícula:';
$vars_template['NOMBRE'] =  $enfermeras->matricula;
$vars_template['ID'] =  $enfermeras->id;
$vars_template['CANCELAR'] = Vista::get_url('index.php/enfermeras/index');
$template = (new \FMT\Template(VISTAS_PATH.'/widgets/confirmacion_reactivar.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars',$vars_vista);

return true;