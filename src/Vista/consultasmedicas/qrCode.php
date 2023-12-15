<?php
use \Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', TRUE);
$options->set('isHtml5ParserEnabled', TRUE);
$dompdf	= new Dompdf($options);
$dompdf = new Dompdf();

$vars_template    = [
    'TITLE_FILE'    => "validacion_qr.pdf",   
    'IMG_CHECK'     => base64_encode(file_get_contents(\App\Helper\Vista::get_url("img") . '/check.png')),
    'BASE_URL'      => BASE_PATH,
    'FECHA_ATENCION'	=> $fecha_atencion,
    'NOMBRE_INTERVINIENTE' 	=> $nombre_interviniente,
    'MATRICULA'				=> $matricula,
    'DNI_PERSONA'			=> $persona->dni,
    'APELLIDO_NOMBRE'		=> $persona->apellido_nombre
];

$html = new FMT\Template(VISTAS_PATH.'/templates/consultasmedicas/qrCode.html', $vars_template,['CLEAN'=>true]);
$dompdf->loadHtml("$html");
$dompdf->render();
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=validacion_qr.pdf");
echo $dompdf->output();

exit;