<?php
$rol = App\Modelo\AppRoles::obtener_rol();
$vars_vista['SUBTITULO']		= 'Modificar Consulta medica';
$vars_template['OPERACION']		= 'modificacion';
$vars_template['CUIT'] = !empty($persona->cuit) ? $persona->cuit : '';
$vars_template['DNI'] = !empty($persona->dni) ? $persona->dni : '';
$vars_template['APELLIDO_NOMBRE'] = !empty($persona->apellido_nombre) ? $persona->apellido_nombre : '';
$vars_template['FECHA_NACIMIENTO'] = !empty($temp = $persona->fecha_nacimiento) ? $temp->format('d/m/Y') : '';
$vars_template['APTO'] = \FMT\Helper\Template::select_block($apto_medico,$persona->apto);
$vars_template['TIPO_APTO'] = \FMT\Helper\Template::select_block($tipo_aptos,$persona->tipo_apto);
$vars_template['EDAD'] = !empty($persona->edad) ? $persona->edad : '';
$vars_template['FECHA_APTO'] = !empty($temp = $persona->fecha_apto) ? $temp->format('d/m/Y') : '';
$vars_template['MODALIDAD_VINCULACION'] = !empty($persona->modalidad_vinculacion) ? $persona->modalidad_vinculacion :'';
$vars_template['GRUPO'] = \FMT\Helper\Template::select_block($grupo_sanguineo,$persona->grupo_sanguineo);
$vars_template['FECHA_INTERVENCION'] = !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('d/m/Y H:i') : '';
$vars_template['ARTICULO'] = \FMT\Helper\Template::select_block($articulos,$consultamedica->id_articulo);
$vars_template['INTERVINIENTE'] = \FMT\Helper\Template::select_block($intervinientes);
$vars_template['FECHA_DESDE'] = !empty($temp = $consultamedica->fecha_desde) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_HASTA'] = !empty($temp = $consultamedica->fecha_hasta) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_REGRESO'] = !empty($temp = $consultamedica->fecha_regreso_trabajo) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_NUEVA_REVISION'] = !empty($temp = $consultamedica->fecha_nueva_revision) ? $temp->format('d/m/Y') : '';
if($rol === App\Modelo\AppRoles::ROL_ADMINISTRACION || $rol === App\Modelo\AppRoles::ROL_RRHH){
	$vars_template['OBSERVACION_INTERVENCION'] = 'INFORMACIÓN RESTRINGIDA';
	$vars_template['DISABLED_ONSERVACION'] = 'disabled';
}else{
	$vars_template['OBSERVACION_INTERVENCION'] = !empty($consultamedica->observacion) ? $consultamedica->observacion : '';
	$vars_template['DISABLED_ONSERVACION'] = '';
}

$vars_template['MEDICO_TRATANTE'] = !empty($consultamedica->medico_tratante) ? $consultamedica->medico_tratante : '';
$vars_template['TELEFONO_MEDICO_TRATANTE'] = !empty($consultamedica->telefono_contacto_tratante) ? $consultamedica->telefono_contacto_tratante : '';
$vars_template['DOCUMENTOS_VER'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/ver_historial/'.$consultamedica->id);
$vars_template['HISTORIA_CLINICA'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/historia_clinica/'.$persona->dni);
$vars_template['BOTON_APTO'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/aptoMedico/'.$persona->dni);

$vars_template['BOTON_COMPROBANTE'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/comprobanteMedico/'.$consultamedica->id);
//CARGO datos provenientes de sigarhu, solo a modo informativo//
//DISCAPACIDAD
$vars_template['TIPO'] = (isset($datos_agente['datos_discapacidad']) && !empty($datos_agente['datos_discapacidad']['discapacidad'])) ? 
	$datos_agente['datos_discapacidad']['discapacidad'] :'-' ;

$vars_template['CUD'] = !empty($datos_agente['datos_discapacidad']['cud']) ? $datos_agente['datos_discapacidad']['cud'] :'-' ;
$vars_template['FECHA_VENCIMIENTO'] = !empty($datos_agente['datos_discapacidad']['fecha_vencimiento']) ? $datos_agente['datos_discapacidad']['fecha_vencimiento'] :'-' ;
$vars_template['OBSERVACION_DISCAPACIDAD'] = !empty($value['datos_discapacidad']['observaciones']) ? $value['datos_discapacidad']['observaciones'] :'-' ;

//DOMICILIO
$vars_template['CALLE'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['calle'])) ? 
	$datos_agente['domicilio']['calle'] :'-' ;

$vars_template['NUMERO'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['numero'])) ? 
	$datos_agente['domicilio']['numero'] :'-' ;

$vars_template['PISO'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['piso'])) ? 
	$datos_agente['domicilio']['piso'] :'-' ;

$vars_template['DPTO'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['depto'])) ? 
	$datos_agente['domicilio']['depto'] :'-' ;

$vars_template['PROVINCIA'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['pcia'])) ? 
	$datos_agente['domicilio']['pcia'] :'-' ;

$vars_template['LOCALIDAD'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['localidad'])) ? 
	$datos_agente['domicilio']['localidad'] :'-' ;

$vars_template['CODIGO_POSTAL'] = (isset($datos_agente['domicilio']) && !empty($datos_agente['domicilio']['cod_postal'])) ? 
	$datos_agente['domicilio']['cod_postal'] :'-' ;

//TELEFONOS (hasta2)
$vars_template['TIPO_TEL_1'] = (isset($datos_agente['telefonos'][0]) && !empty($datos_agente['telefonos'][0]['tipo_telefono'])) ? 
	$datos_agente['telefonos'][0]['tipo_telefono'] :'-' ;	
$vars_template['NUMERO_TEL_1'] = (isset($datos_agente['telefonos'][0]) && !empty($datos_agente['telefonos'][0]['numero'])) ? 
	$datos_agente['telefonos'][0]['numero'] :'-' ;
	//SEGUNDO
$vars_template['TIPO_TEL_2'] = (isset($datos_agente['telefonos'][1]) && !empty($datos_agente['telefonos'][1]['tipo_telefono'])) ? 
	$datos_agente['telefonos'][1]['tipo_telefono'] :'-' ;	
$vars_template['NUMERO_TEL_2'] = (isset($datos_agente['telefonos'][1]) && !empty($datos_agente['telefonos'][1]['numero'])) ? 
	$datos_agente['telefonos'][1]['numero'] :'-' ;
//OBRA SOCIAL
$vars_template['OBRA_SOCIAL'] = (isset($datos_agente['obra_social']) && !empty($datos_agente['obra_social'])) ? 
	$datos_agente['obra_social'] :'-' ;

if(!empty($consultamedica->tipo_interviniente)){
	if($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::MEDICO){
		$vars_template['INTERVINIENTE']= \FMT\Helper\Template::select_block($intervinientes,$consultamedica->id_interviniente.'M');
	}else if($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::ENFERMERA){
		$vars_template['INTERVINIENTE']= \FMT\Helper\Template::select_block($intervinientes,$consultamedica->id_interviniente.'E');
	}
}
//habilitar o deshabilitar los botones según el rol
if($rol === App\Modelo\AppRoles::ROL_MEDICO || $rol === App\Modelo\AppRoles::ROL_ENFERMERA){
	$vars_template['DISABLED'] = '';
}else{
	$vars_template['DISABLED'] = 'disabled';
}
$enviado = \App\Modelo\Consultamedica::ENVIADO;
$no_enviado= \App\Modelo\Consultamedica::NO_ENVIADO;
//envio_comprobante va a ser 1 si existe (si se envio el mail) si no se envio el mail, le pongo 0
$flag_enviado = !empty($envio_comprobante) ? $envio_comprobante : $no_enviado;
$dni_paciente = !empty($persona->dni) ? $persona->dni : ''; 
$id_consulta = !empty($consultamedica->id) ? $consultamedica->id : ''; 

$roles = json_encode([
	'medico' => App\Modelo\AppRoles::ROL_MEDICO,
	'enfermera' => App\Modelo\AppRoles::ROL_ENFERMERA,
	'rrhh'		=> App\Modelo\AppRoles::ROL_RRHH,
	'administrador' => App\Modelo\AppRoles::ROL_ADMINISTRACION
], JSON_UNESCAPED_UNICODE);

$opciones_apto	= json_encode([
	'apto_si' => App\Modelo\Persona::APTO_SI,
	'apto_no' =>  App\Modelo\Persona::APTO_NO
], JSON_UNESCAPED_UNICODE);
$vars_template['FLAG'] = 1; //lo cargo con true para indicar que estoy en la modificación
$vars_template['DISABLED_UPDATE'] = 'disabled';
$vars_template['DISABLED_HISTORIAL'] = ''; //boton vet historial de adjuntos 
$vars_template['DISABLED_EVENTOS'] = 'disabled';
$vars_template['DISABLED_HISTORIA_CLINICA'] = 'disabled';
$vars_template['RESUMEN'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/resumen_eventos/' . $persona->dni);//url resumen de eventos
$vars_template['ESTADO']= \FMT\Helper\Template::select_block($estados,$consultamedica->id_estado);
$vars_template['CANCELAR']      = \App\Helper\Vista::get_url('index.php/consultasmedicas/index');
$template = (new \FMT\Template(VISTAS_PATH.'/templates/consultasmedicas/'.'alta.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] = \App\Helper\Vista::get_url('bootstrap-typeahead.min.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  =  \App\Helper\Vista::get_url('script.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']   = \App\Helper\Vista::get_url('/consultasmedicas/consultasmedicas.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']   =\App\Helper\Vista::get_url().'/js/sweetalert2/sweetalert2.all.min.js';
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']   =\App\Helper\Vista::get_url().'/js/redirect.js';
//$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('/consultasmedicas/doc-adjuntos.js');
$vars_vista['JS_FOOTER'][]['JS_SCRIPT']  = \App\Helper\Vista::get_url('fileinput.min.js');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('documentos.css');
$vars_vista['CSS_FILES'][]['CSS_FILE'] = \App\Helper\Vista::get_url('fileinput.min.css');
$base_url = \App\Helper\Vista::get_url('index.php');
$endpoint_cdn = $vista->getSystemConfig()['app']['endpoint_cdn'];
$vars_vista['CONTENT'] = "$template";
$vars_vista['JS'][]['JS_CODE']	= <<<JS
	var \$base_url = "{$base_url}";
	var \$endpoint_cdn = '{$endpoint_cdn}';
	var \$opciones_apto = {$opciones_apto};
	var \$roles = {$roles};
	var \$rol_actual = {$rol};
	var \$flag_enviado = {$flag_enviado};
	var \$dni_paciente = {$dni_paciente};
	var \$id_consulta = {$id_consulta};
	var \$no_enviado = {$no_enviado};
	var \$enviado = {$enviado}

JS;
$vista->add_to_var('vars',$vars_vista);

return true;