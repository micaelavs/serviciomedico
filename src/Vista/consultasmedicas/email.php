<?php
use \FMT\Helper\Template;

$vars_template	= [
    
    'DATA_IMG'  => 'data:image/png;base64,'. base64_encode(file_get_contents(BASE_PATH.'/public/img/min_transporte.png')),
	'CABECERA'	=> $cabecera,	
	'FOOTER'  => "Sistema Servicio Médico - Ministerio de Transporte",
    'DATOS_PACIENTE'=>  $datos_paciente
  
];

$template_html	= new \FMT\Template(TEMPLATE_PATH.'/consultasmedicas/email.html',$vars_template,['CLEAN'=>true]);

echo $template_html;
