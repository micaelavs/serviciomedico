<?php

use App\Helper\Vista;
/**
 * @var \App\Modelo\Medico $medico
 * @var $vista
 */

$vars_vista['SUBTITULO'] = 'Reactivar "Médico"';
$vars_template['TEXTO_AVISO'] = '';
$vars_template['ARTICULO'] = 'El ';
$vars_template['CONTROL'] = 'Médico con matrícula:';
$vars_template['NOMBRE'] =  $medico->matricula;
$vars_template['ID'] =  $medico->id;
$vars_template['CANCELAR'] = Vista::get_url('index.php/medicos/index');
$template = (new \FMT\Template(VISTAS_PATH.'/widgets/confirmacion_reactivar.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CONTENT'] = "$template";
$vista->add_to_var('vars',$vars_vista);

return true;