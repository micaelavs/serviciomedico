<?php
$rol = App\Modelo\AppRoles::obtener_rol();
$vars_vista['SUBTITULO']		= 'Alta de Consultas Médicas';
$vars_template['OPERACION']		= 'alta';
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
$vars_template['FECHA_INTERVENCION'] = !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('d/m/Y') : '';
$vars_template['ARTICULO'] = \FMT\Helper\Template::select_block($articulos,$consultamedica->id_articulo);
$vars_template['INTERVINIENTE'] = \FMT\Helper\Template::select_block($intervinientes);
$vars_template['FECHA_DESDE'] = !empty($temp = $consultamedica->fecha_desde) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_HASTA'] = !empty($temp = $consultamedica->fecha_hasta) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_REGRESO'] = !empty($temp = $consultamedica->fecha_regreso_trabajo) ? $temp->format('d/m/Y') : '';
$vars_template['FECHA_NUEVA_REVISION'] = !empty($temp = $consultamedica->fecha_nueva_revision) ? $temp->format('d/m/Y') : '';

$no_enviado= \App\Modelo\Consultamedica::NO_ENVIADO;
$flag_enviado = $no_enviado;

$opciones_apto	= json_encode([
	'apto_si' => App\Modelo\Persona::APTO_SI,
	'apto_no' =>  App\Modelo\Persona::APTO_NO
], JSON_UNESCAPED_UNICODE);

$roles = json_encode([
	'medico' => App\Modelo\AppRoles::ROL_MEDICO,
	'enfermera' => App\Modelo\AppRoles::ROL_ENFERMERA,
	'rrhh'		=> App\Modelo\AppRoles::ROL_RRHH,
	'administrador' => App\Modelo\AppRoles::ROL_ADMINISTRACION
], JSON_UNESCAPED_UNICODE);

if($rol === App\Modelo\AppRoles::ROL_ADMINISTRACION || $rol === App\Modelo\AppRoles::ROL_RRHH){
	$vars_template['OBSERVACION_INTERVENCION'] = 'INFORMACIÓN RESTRINGIDA';
	$vars_template['DISABLED_ONSERVACION'] = 'disabled';
}else{
	$vars_template['OBSERVACION_INTERVENCION'] = !empty($consultamedica->observacion) ? $consultamedica->observacion : '';
	$vars_template['DISABLED_ONSERVACION'] = '';
}
$vars_template['MEDICO_TRATANTE'] = !empty($consultamedica->medico_tratante) ? $consultamedica->medico_tratante : '';
$vars_template['TELEFONO_MEDICO_TRATANTE'] = !empty($consultamedica->telefono_contacto_tratante) ? $consultamedica->telefono_contacto_tratante : '';
$vars_template['HISTORIA_CLINICA'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/historia_clinica/'.$persona->dni);
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
if (!empty($persona->dni)) {
	$vars_template['RESUMEN'] = \App\Helper\Vista::get_url('index.php/consultasmedicas/resumen_eventos/' . $persona->dni);
} else {
	$vars_template['RESUMEN'] ='';
}

$vars_template['FLAG'] = 0; //flag modificación
$vars_template['DISABLED_UPDATE'] = '';
$vars_template['DISABLED_HISTORIAL'] = 'disabled'; //boton ver historial de adjuntos 
$vars_template['DISABLED_EVENTOS'] = 'disabled';
$vars_template['DISABLED_HISTORIA_CLINICA'] = 'disabled';
$vars_template['ESTADO']= \FMT\Helper\Template::select_block($estados,$consultamedica->id_estado);
$vars_template['CANCELAR']      = \App\Helper\Vista::get_url('index.php/consultasmedicas/index');
$template = (new \FMT\Template(VISTAS_PATH.'/templates/consultasmedicas/'.'alta.html', $vars_template,['CLEAN'=>false]));
$vars_vista['CSS_FILES'][]	= ['CSS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn'].'/datatables/1.10.12/datatables.min.css'];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/1.10.12/datatables.min.js"];
$vars_vista['JS_FILES'][]	= ['JS_FILE' => $vista->getSystemConfig()['app']['endpoint_cdn']."/datatables/defaults.js"];
$vars_vista['JS_FOOTER'][]['JS_SCRIPT'] =   \App\Helper\Vista::get_url('bootstrap-typeahead.min.js');
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

JS;
$vista->add_to_var('vars',$vars_vista);

return true;
