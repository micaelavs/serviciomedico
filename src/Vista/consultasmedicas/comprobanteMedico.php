<?php

use \FMT\Helper\Template;
use \Dompdf\Dompdf;
use \Dompdf\Options;

//$dias = \FMT\Informacion_fecha::cantidad_dias_habiles($consultamedica->fecha_desde, $consultamedica->fecha_hasta);
$dias = $consultamedica->fecha_desde->diff($consultamedica->fecha_hasta);
$dias = $dias->days + 1;

setlocale(LC_ALL, 'spanish');
$fecha_actual = strftime('%d de %B del %Y'); 
$hora_actual =  strftime("%H:%M");
$vars_template                    = [];

$vars_template    = [
    'IMG_PATH'              => \App\Helper\Vista::get_url("img") . '/dgrrhh.png',   
    'TITLE_FILE'            => $file_nombre,
    'BASE_URL'              => BASE_PATH,
    'FECHA_ACTUAL'          => $fecha_actual,
    'DIA'                   => !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('d/m/Y') : '',
    'HORA'                  => !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('H:i') : ''. ' hs',
    'APELLIDO_NOMBRE_AGENTE' => $persona->apellido_nombre,
    'DNI'                   => $persona->dni,
    'CONDICION_LABORAL'     => $persona->modalidad_vinculacion,
    'N_DIAS_LICENCIA'       => $dias,
    'ARTICULO'              => $nombre_articulo,
    'FECHA_DESDE'           => !empty($temp = $consultamedica->fecha_desde) ? $temp->format('d/m/Y') : '',
    'FECHA_HASTA'           => !empty($temp = $consultamedica->fecha_hasta) ? $temp->format('d/m/Y') : '',
    'FECHA_REINCORPORACION' => !empty($temp = $consultamedica->fecha_regreso_trabajo) ? $temp->format('d/m/Y') : (!empty($consultamedica->fecha_hasta) ? date('d/m/Y',strtotime(\FMT\Informacion_fecha::dias_habiles_hasta_fecha($consultamedica->fecha_hasta,1))) : ''),
    'NUMERO_CONSULTA'       => !empty($consultamedica->id) ? $consultamedica->id : '',
     'QR'                   => base64_encode($image),
    'FIRMA'                 => "data:image/jpg;base64," . base64_encode(file_get_contents($firma)),
    'USUARIO_EMAIL'         => $user->email,
    'USUARIO_NOMBRE'        => $user->nombre. ' '. $user->apellido
];

$vars_vista['CONTENT'] = "";
$vista->add_to_var('vars', $vars_vista);

$template_html  = new \FMT\Template(TEMPLATE_PATH . '/consultasmedicas/comprobanteMedico.html', $vars_template, ['CLEAN' => true]);
$nombre = $persona->dni.'_'.$consultamedica->id.'_'.$file_nombre.'.pdf';
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf    = new Dompdf($options);
$dompdf->loadHtml($template_html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream($file_nombre);
  
$output = $dompdf->output();
$path = BASE_PATH.'/uploads/intervencion/'.$nombre;
file_put_contents($path, $output);
exit;
