<?php

use \FMT\Helper\Template;
use \Dompdf\Dompdf;
use \Dompdf\Options;
use App\Modelo;

$vars_template                    = [];
$vars_template    = [
    'IMG_PATH'              => \App\Helper\Vista::get_url("img") . '/dgrrhh.png',
    'TITLE_FILE'            => $file_nombre,
    'BASE_URL'              => BASE_PATH,
    'FECHA_SOLICITUD'       => !empty($temp = $persona->fecha_apto) ? $temp->format('d-m-Y') : '',
    'APELLIDO_NOMBRE'       => $persona->apellido_nombre,
    'DNI'                   => $persona->dni,
    'TIPO_APTO'				=> !empty($persona->tipo_apto) ? Modelo\Persona::$TIPO_APTO[$persona->tipo_apto]['nombre'] : ''
];

$vars_vista['CONTENT'] = "";
$vista->add_to_var('vars', $vars_vista);

$template_html  = new \FMT\Template(TEMPLATE_PATH . '/consultasmedicas/aptoMedico.html', $vars_template, ['CLEAN' => true]);

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf    = new Dompdf($options);
$dompdf->loadHtml($template_html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream($file_nombre);
exit;
