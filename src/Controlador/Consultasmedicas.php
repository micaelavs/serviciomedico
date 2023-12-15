<?php

namespace App\Controlador;

use App\Modelo;
use App\Helper\Controlador;
use App\Helper\Vista;
use App\Modelo\Consultamedica;
use Endroid\QrCode\QrCode;
use App\Helper;

class Consultasmedicas extends Base{

    protected function accion_index() {
        $articulos = Modelo\Articulo::lista_articulos();
        $estados = Modelo\Estado::lista_estados();
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'articulos', 'estados')))->pre_render();
    }

    protected function accion_actualizarApto(){
        if($this->request->is_ajax()){
            $data = $this->_get_apto();
            $this->json->setData($data);
            $this->json->render();
            exit;
        }
    }

    private function _get_apto(){
        $aptos = Modelo\Persona::$APTO;
        return $aptos;
    }

    protected function accion_actualizarGrupo(){
        if($this->request->is_ajax()){
            $data = $this->_get_grupo();
            $this->json->setData($data);
            $this->json->render();
            exit;
        }
    }

    private function _get_grupo(){
        $grupo = Modelo\Persona::$GRUPO_SANGUINEO;
        return $grupo;
    }
   
    protected function accion_actualizarTipoApto(){
        if($this->request->is_ajax()){
            $data = $this->_get_tipo_apto();
            $this->json->setData($data);
            $this->json->render();
            exit;
        }
    }

    private function _get_tipo_apto(){
        $tipo_aptos = Modelo\Persona::$TIPO_APTO;
        return $tipo_aptos;
    }

    protected function accion_traer_tipo_apto_segun_apto(){
        if($this->request->is_ajax()){
            $data = $this->_get_tipo_apto_segun_apto($this->request->post('id_apto'));
            $this->json->setData($data);
            $this->json->render();
            exit;
        }
    }

    private function _get_tipo_apto_segun_apto($id_apto = null){
        $tipo_aptos = [];
        if($id_apto == Modelo\Persona::APTO_SI){
            $tipo_aptos[Modelo\Persona::A] = Modelo\Persona::$TIPO_APTO[Modelo\Persona::A];
            $tipo_aptos[Modelo\Persona::B_C_PREEXISTENCIA] = Modelo\Persona::$TIPO_APTO[Modelo\Persona::B_C_PREEXISTENCIA];
            $tipo_aptos[Modelo\Persona::C_C_PREEXISTENCIA] = Modelo\Persona::$TIPO_APTO[Modelo\Persona::C_C_PREEXISTENCIA];
            $tipo_aptos[Modelo\Persona::D_C_PREEXISTENCIA] = Modelo\Persona::$TIPO_APTO[Modelo\Persona::D_C_PREEXISTENCIA];
        }elseif($id_apto == Modelo\Persona::APTO_NO){
            $tipo_aptos[Modelo\Persona::NO_APTO] = Modelo\Persona::$TIPO_APTO[Modelo\Persona::NO_APTO];
        }
       
        return $tipo_aptos;
    }

    protected function accion_ver_historial() {
        $id_consulta = $this->request->query('id');
        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista', 'id_consulta')))->pre_render();
    }

    protected function accion_historia_clinica(){
        $persona = Modelo\Persona::obtenerPorDNI($this->request->query('id'));
        $articulos = Modelo\Articulo::lista_articulos();
        $estados = Modelo\Estado::lista_estados();
        $enfermeras = Modelo\Enfermera::lista_enfermeras();
        $medicos = Modelo\Medico::lista_medicos();
        $intervinientes = array_merge($medicos,$enfermeras);
        $vista   = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'persona', 'articulos', 'estados','intervinientes')))->pre_render();
    }

    protected function accion_log_cambios(){
        $usuarios = \FMT\Usuarios::getUsuarios();
        $new_users = [];
        foreach ($usuarios as $user) {
              $new_users[$user['idUsuario']] = ['id'=> $user['idUsuario'],'nombre'=> $user['user'].' - '.$user['apellido'] .'  '.$user['nombre'], 'borrado' => 0];
         } 
        $personas = Modelo\Persona::lista_personas();
        $vista   = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'personas', 'new_users')))->pre_render();
    }

    public function  accion_alta(){
        $apto_medico = Modelo\Persona::$APTO;
        $tipo_aptos = Modelo\Persona::$TIPO_APTO;
        $grupo_sanguineo = Modelo\Persona::$GRUPO_SANGUINEO;
        $consultamedica = Consultamedica::obtener($this->request->query('id'));
        $persona = Modelo\Persona::obtener($consultamedica->id_persona);
        $articulos = Modelo\Articulo::lista_articulos();
        $enfermeras = Modelo\Enfermera::lista_enfermeras();
        $medicos = Modelo\Medico::lista_medicos();
        $intervinientes = array_merge($medicos,$enfermeras);
        $estados = Modelo\Estado::lista_estados();
        $documentos = [];
        
        if ($this->request->post('consultasmedicas') == 'alta') {
            if(!empty($_FILES['documento'])){

                foreach ($_FILES['documento'] as $clave => $valor) {

                    foreach ($valor as $clave2 => $valor2) {

                        foreach ($valor2 as $clave3 => $valor3) { 
                   
                        $documentos[$clave3][$clave] = $valor3['doc'];
                        }
                    
                    }
                
                }
            }
            
            $consultamedica->adjuntos  = !empty($documentos) ? $documentos : null;
            $persona = Modelo\Persona::obtenerPorDNI($this->request->post('dni')); 
            if(!empty($persona->fecha_nacimiento)){
                $fecha_nacimiento = $persona->fecha_nacimiento->format('Y-m-d');
                $nacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                $ahora = \DateTime::createFromFormat('Y-m-d',date("Y-m-d"));
                $diferencia = $ahora->diff($nacimiento);
                $edad = $diferencia->format("%y");
                $persona->edad = $edad;
            }

            $consultamedica->id_persona = $persona->id;
            $consultamedica->id_estado = !empty($this->request->post('estado')) ? $this->request->post('estado') : null;
            $consultamedica->id_articulo = !empty($this->request->post('articulo')) ? $this->request->post('articulo') : null;
            if(!empty($this->request->post('interviniente'))){
                $id_interviniente = substr($this->request->post('interviniente'), 0, 1);
                $tipo = substr($this->request->post('interviniente'), 1, 1);
                if($tipo=='M'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::MEDICO;
                }else if($tipo=='E'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::ENFERMERA;
                }
            }
            $consultamedica->id_interviniente = !empty($id_interviniente) ?  $id_interviniente : null;
            $consultamedica->tipo_interviniente = !empty($tipo_interviniente) ? $tipo_interviniente : null;
            $consultamedica->fecha_intervencion = !empty($temporal = $this->request->post('fecha_intervencion')) ?  \DateTime::createFromFormat('d/m/Y H:i', $temporal) : null;
            $consultamedica->fecha_desde = !empty($temporal = $this->request->post('fecha_desde')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_hasta = !empty($temporal = $this->request->post('fecha_hasta')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_regreso_trabajo =  !empty($temporal = $this->request->post('fecha_regreso')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_nueva_revision = !empty($temporal = $this->request->post('fecha_nueva_revision')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->observacion = !empty($this->request->post('observacion_intervencion')) ? $this->request->post('observacion_intervencion') : null;
            $consultamedica->medico_tratante = !empty($this->request->post('medico_tratante')) ? $this->request->post('medico_tratante') : null;
            $consultamedica->telefono_contacto_tratante = !empty($this->request->post('telefono_medico_tratante')) ? $this->request->post('telefono_medico_tratante') : null;
            if ($consultamedica->validar()) {
                $resultado = $consultamedica->alta();
                if ($resultado > 0) {
                    $this->mensajeria->agregar(
                        "AVISO: La Consulta médica fué ingresada de forma exitosa.",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/consultasmedicas/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "ERROR: No se pudo dar de alta la Consulta médica.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/consultasmedicas/index");
                    $this->redirect($redirect);
                }
            } else {
                $err    = $consultamedica->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'consultamedica','persona','apto_medico','tipo_aptos','grupo_sanguineo', 'articulos', 'intervinientes', 'estados')))->pre_render();
    }

    public function accion_persona_alta(){
        if($this->request->is_ajax()){
            $persona = Modelo\Persona::obtener();
            $persona->dni = !empty($this->request->post('dni')) ? $this->request->post('dni') : null;
            $persona->cuit = !empty($this->request->post('cuit')) ? $this->request->post('cuit') : null;
            $persona->apellido_nombre = !empty($this->request->post('apellido_nombre')) ? $this->request->post('apellido_nombre') :null;
            $persona->apto = !empty($this->request->post('apto')) ? $this->request->post('apto') : null;
            $persona->tipo_apto = !empty($this->request->post('tipo_apto')) ? $this->request->post('tipo_apto') : null;
            $persona->fecha_nacimiento = !empty($temporal = $this->request->post('fecha_nac')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $persona->grupo_sanguineo =  !empty($this->request->post('grupo')) ? $this->request->post('grupo') : null;
            $persona->modalidad_vinculacion =  !empty($this->request->post('modalidad_vinculacion')) ? $this->request->post('modalidad_vinculacion') : null;
            $persona->fecha_apto = !empty($temporal = $this->request->post('fecha_apto')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $mensaje = array();
            //busco por dni para ver si ya se ingreso
            $personabusqueda = Modelo\Persona::obtenerPorDNI($this->request->post('dni'));
            if(empty($personabusqueda->id)){//si no hay persona, lo inserto
                if ($persona->validar()) {
                    $res = $persona->alta();
                    if ($res > 0) {
                        $mensaje[] = ['mensaje'=> 'La Persona se ha registrado de forma exitosa.'];
                    } else {
                        $mensaje[] = ['error'=> 'Hubo un error al dar de alta a la Persona.'];
                    }
                }else{

                    $err    = $persona->errores;
                    $mensaje[] = ['errores' =>  $err];

                }
            }else{//si ya existe actualizo los datos y aviso que fue actualizada
                $persona->id =  $personabusqueda->id;
                if ($persona->validar()) {
                    $res = $persona->modificacion();
                    if ($res > 0) {
                        $mensaje[] = ['error_existe'=> 'La Persona ya se encuentra registrada en el sistema, se han actualizaron los datos.'];
                    } else {
                        $mensaje[] = ['error'=> 'No se ha modificado la Persona.'];
                    }
                }else{

                    $err    = $persona->errores;
                    $mensaje[] = ['errores' =>  $err];

                }

            }

            $this->json->setData($mensaje);
            $this->json->render();

        }
    }

    public function accion_persona_actualizar(){
       if($this->request->is_ajax()){
            $persona = Modelo\Persona::obtener();
            $persona->dni = !empty($this->request->post('dni')) ? $this->request->post('dni') : null;
            $persona->cuit = !empty($this->request->post('cuit')) ? $this->request->post('cuit') : null;
            $persona->apellido_nombre = !empty($this->request->post('apellido_nombre')) ? $this->request->post('apellido_nombre') :null;
            $persona->apto = !empty($this->request->post('apto')) ? $this->request->post('apto') : null;
            $persona->tipo_apto = !empty($this->request->post('tipo_apto')) ? $this->request->post('tipo_apto') : null;
            $persona->fecha_nacimiento = !empty($temporal = $this->request->post('fecha_nac')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $persona->grupo_sanguineo =  !empty($this->request->post('grupo')) ? $this->request->post('grupo') : null;
            $persona->modalidad_vinculacion =  !empty($this->request->post('modalidad_vinculacion')) ? $this->request->post('modalidad_vinculacion') : null;
            $persona->fecha_apto = !empty($temporal = $this->request->post('fecha_apto')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $persona->id_sigarhu = !empty($this->request->post('id_sigarhu')) ? $this->request->post('id_sigarhu') : null;
            $mensaje = array();
            if ($persona->validar()) {
                $res = $persona->modificacion_sigarhu();
                if ($res > 0) {
                    $mensaje[] = ['mensaje'=> 'La Persona se ha modificado de forma exitosa.'];
                } else {
                    $mensaje[] = ['error'=> 'No se ha modificado la Persona.'];
                }
            }else{

                $err    = $persona->errores;
                $mensaje[] = ['errores' =>  $err];
            }

            $this->json->setData($mensaje);
            $this->json->render();

        }

    }

    public function  accion_modificacion(){
        $consultamedica = Consultamedica::obtener($this->request->query('id'));
        $apto_medico = Modelo\Persona::$APTO;
        $tipo_aptos = Modelo\Persona::$TIPO_APTO;
        $grupo_sanguineo = Modelo\Persona::$GRUPO_SANGUINEO;
        $persona = Modelo\Persona::obtener($consultamedica->id_persona);
        $envio_comprobante = Consultamedica::consultar_comprobante_enviado($consultamedica->id);

        if(!empty($persona->fecha_nacimiento)){
                $fecha_nacimiento = $persona->fecha_nacimiento->format('Y-m-d');
                $nacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                $ahora = \DateTime::createFromFormat('Y-m-d',date("Y-m-d"));
                $diferencia = $ahora->diff($nacimiento);
                $edad = $diferencia->format("%y");
                $persona->edad = $edad;
        }
        $parametricos   = Modelo\SigarhuApi::getParametricos(['modalidad_vinculacion', 'situacion_revista', 'tipo_telefono', 'obras_sociales','tipo_discapacidad', 'ubicacion_regiones']);
        Modelo\SigarhuApi::contiene(['situacion_escalafonaria','persona', 'empleado_salud']);   
        $datos_agente = [];  
        //habría que hacer una consulta a sigarhu para traer los datos de teléfono, dirección, datos de discapacidad etc, los propios de sigarhu 
        /*DATOS SE SIGARHU*/
        $agente =  Modelo\SigarhuApi::getAgente($persona->cuit);
        if(!empty($agente->id)){ //SI EL AGENTE ESTA EN SIGARHU ME TRAIGO TODOS LOS DATOS NUEVAMENTE
            $fecha_nacimiento = $agente->persona->fecha_nac->format('Y-m-d');
            $nacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
            $ahora = \DateTime::createFromFormat('Y-m-d',date("Y-m-d"));
            $diferencia = $ahora->diff($nacimiento);
            $edad = $diferencia->format("%y");
            
            $cuit = !empty($agente->cuit) ? $agente->cuit : '-';
            $dni =  !empty($agente->persona->documento) ? $agente->persona->documento : '-';
            $apellido_nombre = (!empty($agente->persona->nombre) && !empty($agente->persona->apellido)) ? $agente->persona->apellido.' '.$agente->persona->nombre : ''; 
            $fecha_nacimiento = !empty($agente->persona->fecha_nac) ? $agente->persona->fecha_nac->format('d-m-Y') : '-';
            $modalidad_vinculacion = !empty($agente->situacion_escalafonaria->id_modalidad_vinculacion) ? $parametricos['modalidad_vinculacion'][$agente->situacion_escalafonaria->id_modalidad_vinculacion]['nombre'] : '-';

            //datos personales
            $datos_agente['cuit'] = $cuit;
            $datos_agente['dni'] = $dni;
            $datos_agente['apellido_nombre'] = $apellido_nombre;
            $datos_agente['fecha_nacimiento'] = $fecha_nacimiento;
            $datos_agente['edad'] = $edad;
            $datos_agente['modalidad_vinculacion'] = $modalidad_vinculacion;
            //datos discapacidad
            $discapacidad = !empty($agente->persona->discapacidad->id_tipo_discapacidad) ? $parametricos['tipo_discapacidad'][$agente->persona->discapacidad->id_tipo_discapacidad]['nombre'] : '-';
            $cud = !empty($agente->persona->discapacidad->cud) ? $agente->persona->discapacidad->cud : '-';
            $fecha_vencimiento = !empty($agente->persona->discapacidad->fecha_vencimiento) ? $agente->persona->discapacidad->fecha_vencimiento->format('d-m-Y') : '-';
            $observaciones_discapacidad = !empty($agente->persona->discapacidad->observaciones) ? $agente->persona->discapacidad->observaciones : '-';
            $datos_agente['datos_discapacidad'] = ['discapacidad' => $discapacidad, 'cud' => $cud, 'fecha_vencimiento' =>  $fecha_vencimiento, 'observaciones' => $observaciones_discapacidad];
            //domicilio
            $domicilio_calle = !empty($agente->persona->domicilio->calle) ? $agente->persona->domicilio->calle : '-';
            $domicilio_numero = !empty($agente->persona->domicilio->numero) ? $agente->persona->domicilio->numero : '-';
            $domicilio_piso = !empty($agente->persona->domicilio->piso) ? $agente->persona->domicilio->piso : '-';
            $domicilio_depto = !empty($agente->persona->domicilio->depto) ? $agente->persona->domicilio->depto : '-';
            $domicilio_cod_postal = !empty($agente->persona->domicilio->cod_postal) ? $agente->persona->domicilio->cod_postal: '-';
            $parametricos['ubicacion_regiones']         = (array)json_decode(json_encode(\FMT\Ubicaciones::get_regiones('AR')));
            $domicilio_pcia = !empty($agente->persona->domicilio->id_provincia) ? $parametricos['ubicacion_regiones'][$agente->persona->domicilio->id_provincia]->nombre : '-';
            $parametricos['ubicacion_localidades']     = !empty($agente->persona->domicilio->id_provincia)
                                                        ? json_decode(json_encode(\FMT\Ubicaciones::get_localidades($agente->persona->domicilio->id_provincia)), JSON_UNESCAPED_UNICODE) : [];
            $domicilio_localidad = !empty($agente->persona->domicilio->id_localidad) ? $parametricos['ubicacion_localidades'][$agente->persona->domicilio->id_localidad]['nombre'] : '-';  
            //la localidad no machea con ninguna de las localidades
            $domicilio_localidad = empty($domicilio_localidad) ? '-' : $domicilio_localidad;
           
            $datos_agente['domicilio'] = ['calle' => $domicilio_calle, 'numero'=> $domicilio_numero, 'piso' => $domicilio_piso, 'depto' => $domicilio_depto, 'cod_postal' => $domicilio_cod_postal, 'pcia' => $domicilio_pcia, 'localidad' => $domicilio_localidad];
          
            $telefonos = [];
             if(!empty($agente->persona->telefonos)){
                    foreach ($agente->persona->telefonos as $key => $telefono) {
                        $telefonos[$key] = ['tipo_telefono' => $parametricos['tipo_telefono'][$telefono->id_tipo_telefono]['nombre'], 'telefono' => $telefono->telefono];

                    }
            }
           
            $datos_agente['telefonos'] = $telefonos;
            //obra social
            $obra_social= !empty($agente->empleado_salud->id_obra_social) ? $parametricos['obras_sociales'][$agente->empleado_salud->id_obra_social]['nombre'] : '-';
            $datos_agente['obra_social'] = $obra_social;
            $datos_agente['id_agente']   =  $agente->id;
        }

        /*FIN DE DATOS DE SIGARHU*/
        if(!empty($persona->fecha_nacimiento)){
            $fecha_nacimiento = $persona->fecha_nacimiento->format('Y-m-d');
            $nacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
            $ahora = \DateTime::createFromFormat('Y-m-d',date("Y-m-d"));
            $diferencia = $ahora->diff($nacimiento);
            $edad = $diferencia->format("%y");
            $persona->edad = $edad;
        }
       
        $articulos = Modelo\Articulo::lista_articulos();
        $enfermeras = Modelo\Enfermera::lista_enfermeras();
        $medicos = Modelo\Medico::lista_medicos();
        $intervinientes = array_merge($medicos,$enfermeras);
        $estados = Modelo\Estado::lista_estados();
        $documentos = [];
        if ($this->request->post('consultasmedicas') == 'modificacion') { 
              if(!empty($_FILES['documento'])){

                foreach ($_FILES['documento'] as $clave => $valor) {

                    foreach ($valor as $clave2 => $valor2) {

                        foreach ($valor2 as $clave3 => $valor3) { 
                   
                        $documentos[$clave3][$clave] = $valor3['doc'];
                        }
                    
                    }
                
                }
            }
            $consultamedica->adjuntos  = !empty($documentos) ? $documentos : null;
            $consultamedica->id_persona = $persona->id;
            $consultamedica->fecha_intervencion    = !empty($temporal = $this->request->post('fecha_intervencion')) ?  \DateTime::createFromFormat('d/m/Y H:i', $temporal) : null;
            $consultamedica->id_articulo =  !empty($this->request->post('articulo')) ? $this->request->post('articulo') : null;
            $consultamedica->id_estado =  !empty($this->request->post('estado')) ? $this->request->post('estado') : null;
             if(!empty($this->request->post('interviniente'))){
                $id_interviniente = substr($this->request->post('interviniente'), 0, 1);
                $tipo = substr($this->request->post('interviniente'), 1, 1);
                if($tipo=='M'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::MEDICO;
                }else if($tipo=='E'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::ENFERMERA;
                }
            }
            $consultamedica->id_interviniente = !empty($id_interviniente) ?  $id_interviniente : null;
            $consultamedica->tipo_interviniente = !empty($tipo_interviniente) ? $tipo_interviniente : null;
            $consultamedica->fecha_desde = !empty($temporal = $this->request->post('fecha_desde')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_hasta = !empty($temporal = $this->request->post('fecha_hasta')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_regreso_trabajo = !empty($temporal = $this->request->post('fecha_regreso')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->fecha_nueva_revision = !empty($temporal = $this->request->post('fecha_nueva_revision')) ?  \DateTime::createFromFormat('d/m/Y', $temporal) : null;
            $consultamedica->observacion =  !empty($this->request->post('observacion_intervencion')) ? $this->request->post('observacion_intervencion') : null;
            $consultamedica->medico_tratante =  !empty($this->request->post('medico_tratante')) ? $this->request->post('medico_tratante') : null;
            $consultamedica->telefono_contacto_tratante = !empty($this->request->post('telefono_medico_tratante')) ? $this->request->post('telefono_medico_tratante') : null;
            if ($consultamedica->validar()) {
                $consultamedica->modificacion();
                $this->mensajeria->agregar(
                    "AVISO: La Consulta Médica fue modificada de forma exitosa.",
                    \FMT\Mensajeria::TIPO_AVISO,
                    $this->clase
                );
                $redirect = Vista::get_url("index.php/consultasmedicas/index");
                $this->redirect($redirect);
            } else {
                $err    = $consultamedica->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'consultamedica','grupo_sanguineo','persona','articulos','intervinientes', 'estados', 'apto_medico','tipo_aptos','datos_agente', 'envio_comprobante')))->pre_render();
    }

    public function accion_baja(){
        $consultamedica = Consultamedica::obtener($this->request->query('id'));
        $persona = \App\Modelo\Persona::obtener($consultamedica->id_persona);
        $articulo = \App\Modelo\Articulo::obtener($consultamedica->id_articulo);
        if ($consultamedica->id) {
            if ($this->request->post('confirmar')) {
                $result = $consultamedica->baja();
                if($result){
                    $this->mensajeria->agregar('AVISO: La Consulta Médica se ha registrado de forma exitosa.', \FMT\Mensajeria::TIPO_AVISO, $this->clase, 'index');
                    $redirect = Vista::get_url('index.php/consultasmedicas/index');
                    $this->redirect($redirect);
                }else{
                    $this->mensajeria->agregar('AVISO:No se ha podido eliminar la Consulta Médica.', \FMT\Mensajeria::TIPO_ERROR, $this->clase, 'index');
                    $redirect = Vista::get_url('index.php/consultasmedicas/index');
                    $this->redirect($redirect);
                }
            }
        } else {
            $redirect = Vista::get_url('index.php/consultasmedicas/index');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('consultamedica', 'persona', 'articulo','vista')))->pre_render();
    }

    protected function accion_buscarAgente(){
        if($this->request->is_ajax()){
            $parametricos   = Modelo\SigarhuApi::getParametricos(['modalidad_vinculacion', 'situacion_revista', 'tipo_telefono', 'obras_sociales','tipo_discapacidad', 'ubicacion_regiones']);
            Modelo\SigarhuApi::contiene(['situacion_escalafonaria','persona', 'empleado_salud', 'dependencia', 'ubicacion']);
            $datos_agente = [];
            //le ponemos cuit al dni, porque se busca a través de un like en la bd
            $params['cuit'] = $this->request->post('dni');
            $agente_array = Modelo\SigarhuApi::searchAgentes($params);
            $agente = !empty($agente_array['0']) ? Modelo\SigarhuApi::getAgente($agente_array['0']['cuit']) : null;
            if(!empty($agente->id)){
                $fecha_nacimiento = $agente->persona->fecha_nac->format('Y-m-d');
                $nacimiento = \DateTime::createFromFormat('Y-m-d', $fecha_nacimiento);
                $ahora = \DateTime::createFromFormat('Y-m-d',date("Y-m-d"));
                $diferencia = $ahora->diff($nacimiento);
                $edad = $diferencia->format("%y");
                $dni =  !empty($agente->persona->documento) ? $agente->persona->documento : '-';
                $cuit = !empty($agente->cuit) ? $agente->cuit : '-';
                $apellido_nombre = (!empty($agente->persona->nombre) && !empty($agente->persona->apellido)) ? $agente->persona->apellido.' '.$agente->persona->nombre : '';
                $fecha_nacimiento = !empty($agente->persona->fecha_nac) ? $agente->persona->fecha_nac->format('d-m-Y') : '-';
                $modalidad_vinculacion = !empty($agente->situacion_escalafonaria->id_modalidad_vinculacion) ? $parametricos['modalidad_vinculacion'][$agente->situacion_escalafonaria->id_modalidad_vinculacion]['nombre'] : '-';
                //datos personales
                $datos_agente[] = ['cuit' => $cuit];
                $datos_agente[] = ['dni'  => $dni];
                $datos_agente[] = ['apellido_nombre' =>  $apellido_nombre];
                $datos_agente[] = ['fecha_nacimiento' => $fecha_nacimiento];
                $datos_agente[] = ['edad' => $edad];
                $datos_agente[] = ['modalidad_vinculacion' => $modalidad_vinculacion];
                //datos discapacidad
                $discapacidad = !empty($agente->persona->discapacidad->id_tipo_discapacidad) ? $parametricos['tipo_discapacidad'][$agente->persona->discapacidad->id_tipo_discapacidad]['nombre'] : '-';

                $cud = !empty($agente->persona->discapacidad->cud) ? $agente->persona->discapacidad->cud : '-';
                $fecha_vencimiento = !empty($agente->persona->discapacidad->fecha_vencimiento) ? $agente->persona->discapacidad->fecha_vencimiento->format('d-m-Y') : '-';
                $observaciones_discapacidad = !empty($agente->persona->discapacidad->observaciones) ? $agente->persona->discapacidad->observaciones : '-';
                $datos_agente[] = ['datos_discapacidad'=> ['discapacidad' => $discapacidad, 'cud' => $cud, 'fecha_vencimiento' =>  $fecha_vencimiento, 'observaciones' => $observaciones_discapacidad]];
                //domicilio
                $domicilio_calle = !empty($agente->persona->domicilio->calle) ? $agente->persona->domicilio->calle : '-';
                $domicilio_numero = !empty($agente->persona->domicilio->numero) ? $agente->persona->domicilio->numero : '-';
                $domicilio_piso = !empty($agente->persona->domicilio->piso) ? $agente->persona->domicilio->piso : '-';
                $domicilio_depto = !empty($agente->persona->domicilio->depto) ? $agente->persona->domicilio->depto : '-';
                $domicilio_cod_postal = !empty($agente->persona->domicilio->cod_postal) ? $agente->persona->domicilio->cod_postal: '-';
                $parametricos['ubicacion_regiones']         = (array)json_decode(json_encode(\FMT\Ubicaciones::get_regiones('AR')));
                $domicilio_pcia = !empty($agente->persona->domicilio->id_provincia) ? $parametricos['ubicacion_regiones'][$agente->persona->domicilio->id_provincia]->nombre : '-';
                $parametricos['ubicacion_localidades']     = !empty($agente->persona->domicilio->id_provincia)
                                                            ? json_decode(json_encode(\FMT\Ubicaciones::get_localidades($agente->persona->domicilio->id_provincia)), JSON_UNESCAPED_UNICODE) : [];
                $domicilio_localidad = !empty($agente->persona->domicilio->id_localidad) ? $parametricos['ubicacion_localidades'][$agente->persona->domicilio->id_localidad]['nombre'] : '-';
                //la localidad no machea con ninguna de las localidades
                $domicilio_localidad = empty($domicilio_localidad) ? '-' : $domicilio_localidad;

                $datos_agente[] = ['domicilio'=> ['calle' => $domicilio_calle, 'numero'=> $domicilio_numero, 'piso' => $domicilio_piso, 'depto' => $domicilio_depto, 'cod_postal' => $domicilio_cod_postal, 'pcia' => $domicilio_pcia, 'localidad' => $domicilio_localidad]];
                //teléfonos
                $telefonos =[];
                if(!empty($agente->persona->telefonos)){
                    foreach ($agente->persona->telefonos as $key => $telefono) {
                        $telefonos[$key] = ['tipo_telefono' => $parametricos['tipo_telefono'][$telefono->id_tipo_telefono]['nombre'], 'telefono' => $telefono->telefono];

                    }
                }
                $datos_agente[] = $telefonos;
                //obra social
                $obra_social= !empty($agente->empleado_salud->id_obra_social) ? $parametricos['obras_sociales'][$agente->empleado_salud->id_obra_social]['nombre'] : '-';
                $datos_agente[] = ['obra_social' => $obra_social];
                $datos_agente[] = ['id_agente' => $agente->id];
                //busco en el sistema si está la persona
                $persona = Modelo\Persona::obtenerPorDNI($this->request->post('dni'));
                if(empty($persona->id)){//si no hay persona, lo inserto
                $persona->id_sigarhu = $agente->id;
                $persona->dni = $dni;
                $persona->cuit = $cuit;
                $persona->apellido_nombre = $apellido_nombre;
                $persona->apto = null;
                $persona->tipo_apto = null;
                $fecha_nac = \DateTime::createFromFormat('d-m-Y', $fecha_nacimiento)->format('Y-m-d');
                $persona->fecha_nacimiento = $fecha_nac;
                $persona->grupo_sanguineo = null;
                $persona->modalidad_vinculacion = $modalidad_vinculacion;
                $persona->fecha_apto = null;
                $res = $persona->alta();
                    if ($res > 0) {
                        $persona->id = $res;
                        $datos_agente[] = ['mensaje_alta' => 'La persona se dio de alta correctamente en el sistema.'];
                        $datos_agente[] = ['id_persona' => $persona->id];

                    }else{
                        $datos_agente[] = ['mensaje_error_alta' => 'No se pudo dar de alta a la Persona en el sistema.'];
                    }

                }else{
                    $datos_agente[] = ['mensaje_aviso_alta' => 'La persona ya se encuentra registrada en el sistema.'];
                    //si ya se encuentra devuelvo los datos para completar los campos no obligatorios
                    //pero antes, por si ya se encuentra almacenada la persona en servicio médico, actualizo los datos por si se llegan a ser modificado en sigarhu
                    //modifico solo lo que viene de sigarhu
                    $persona->id_sigarhu = $agente->id;
                    $persona->cuit = $cuit;
                    $persona->dni = $dni;
                    $persona->apellido_nombre = $apellido_nombre;
                    $fecha_nac = \DateTime::createFromFormat('d-m-Y', $fecha_nacimiento)->format('Y-m-d');
                    $persona->fecha_nacimiento = $fecha_nac;
                    $persona->modalidad_vinculacion = $modalidad_vinculacion;
                    $res = $persona->modificacion_sigarhu();
                    $datos_agente[] =['datos_persona'=> $persona];

                }

            }else{
                //no está en sigarhu pero lo busco en el sistema serv medico para traer los datos
                $datos_agente[] = ['mensaje_error' => "No hay información para mostrar proveniente de Sigarhu."];
                $persona = Modelo\Persona::obtenerPorDNI($this->request->post('dni'));
                if(!empty($persona->id)){
                     $datos_agente[] = ['datos_persona' => $persona];
                }


            }

            $this->json->setData($datos_agente);
            $this->json->render();

        }


    }


    protected function accion_ajax_consultas_medicas(){
        $dataTable_columns    = $this->request->query('columns');
        $orders    = [];
        foreach ($orden = (array)$this->request->query('order') as $i => $val) {
            $orders[]    = [
                'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
                    ? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
                'dir'    => !empty($tmp = $orden[$i]['dir'])
                ? $tmp    :    'desc',
            ];
        }
        $date  = [];

        var_dump($this->request->query('search')); exit;
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->query('search')['value'];
        }
        $params    = [
            'order'        => $orders,
            'start'        => !empty($tmp = $this->request->query('start'))
            ? $tmp : 0,
            'lenght'    => !empty($tmp = $this->request->query('length'))
            ? $tmp : 10,
            'search'    => !empty($search)
                ? $search : '',
            'filtros'   => [
                'dni'                  => $this->request->query('dni_filtro'),
                'fecha_intervencion'    => $this->request->query('fecha_intervencion_filtro'),
                'fecha_desde'           => $this->request->query('fecha_desde_filtro'),
                'fecha_hasta'           => $this->request->query('fecha_hasta_filtro'),
                'id_estado'             => $this->request->query('estado_filtro'),
                'id_articulo'           => $this->request->query('articulo_filtro')
            ],
        ];

        $data =  Modelo\Consultamedica::listar_consultas_medicas($params);
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();


    }

    protected function accion_ajax_archivos_adjuntos(){
        $id_consulta = $this->request->query('id_consulta');
        $dataTable_columns    = $this->request->query('columns');
        $orders    = [];
        foreach ($orden = (array)$this->request->query('order') as $i => $val) {
            $orders[]    = [
                'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
                    ? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
                'dir'    => !empty($tmp = $orden[$i]['dir'])
                ? $tmp    :    'desc',
            ];
        }
        $date  = [];
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->query('search')['value'];
        }
        $params    = [
            'order'        => $orders,
            'start'        => !empty($tmp = $this->request->query('start'))
            ? $tmp : 0,
            'lenght'    => !empty($tmp = $this->request->query('length'))
            ? $tmp : 10,
            'search'    => !empty($search)
                ? $search : '',
            'filtros'   => [
            ],
            'id_consulta' => $id_consulta,
        ];

        $data =  Modelo\Consultamedica::listar_archivos_adjuntos($params);
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();


    }

    protected function accion_ajax_historia_clinica(){
        $dni = $this->request->query('dni');
        $dataTable_columns    = $this->request->query('columns');
        $orders    = [];
        foreach ($orden = (array)$this->request->query('order') as $i => $val) {
            $orders[]    = [
                'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
                    ? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
                'dir'    => !empty($tmp = $orden[$i]['dir'])
                ? $tmp    :    'desc',
            ];
        }
        $date  = [];
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->query('search')['value'];
        }
        $params    = [
            'order'        => $orders,
            'start'        => !empty($tmp = $this->request->query('start'))
            ? $tmp : 0,
            'lenght'    => !empty($tmp = $this->request->query('length'))
            ? $tmp : 10,
            'search'    => !empty($search)
                ? $search : '',
            'filtros'   => [
                'id_estado'             => $this->request->query('estado_filtro'),
                'id_articulo'           => $this->request->query('articulo_filtro'),
                'id_interviniente'      => $this->request->query('interviniente_filtro')
            ],
            'dni' => $dni,
        ];
        
        $data =  Modelo\Consultamedica::listar_historia_clinica($params); 
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();


    }

    protected function accion_ajax_log_cambios(){
        $dataTable_columns    = $this->request->query('columns');
        $orders    = [];
        foreach ($orden = (array)$this->request->query('order') as $i => $val) {
            $orders[]    = [
                'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
                    ? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
                'dir'    => !empty($tmp = $orden[$i]['dir'])
                ? $tmp    :    'desc',
            ];
        }
        $date  = [];
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->query('search')['value'];
        }
        $params    = [
            'order'        => $orders,
            'start'        => !empty($tmp = $this->request->query('start'))
            ? $tmp : 0,
            'lenght'    => !empty($tmp = $this->request->query('length'))
            ? $tmp : 10,
            'search'    => !empty($search)
                ? $search : '',
            'filtros'   => [
                'dni'                  => $this->request->query('dni_filtro'),
                'usuario'               => $this->request->query('usuario_filtro')
            ],
        ];

        $data =  Modelo\Consultamedica::listar_log_cambios($params);
        $rol = Modelo\AppRoles::obtener_rol();
        foreach ($data['data'] as &$value) {
            if($rol === Modelo\AppRoles::ROL_ADMINISTRACION  || $rol === Modelo\AppRoles::ROL_RRHH){
                $value->observacion = "INFORMACIÓN RESTRINGIDA.";
            }
           
        }
     
        foreach ($data['data'] as $key => &$value) {
            $result = preg_replace("/\d{14}_/", "", $value->doc_adjuntos); 
            $value->doc_adjuntos = $result;
               
        }
        
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();


    }

    protected function accion_log_cambios_exportar_excel() {
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'historial_cambios'.date('Ymd_His');

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->post('search'), $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->post('search'));
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->post('search');
        }

        $params = [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'    => !empty($search) ? $search : '',
            'start'     => '',
            'lenght'    => '',
            'filtros'   => [
                'dni'       => $this->request->post('dni'),
                'usuario'    => $this->request->post('usuario')
            ],
        ];
     
        $titulos = [
            'fecha_operacion'       =>'Fecha de Operación',
            'nombre_usuario'        =>'Nombre de Usuario',
            'apellido_nombre_usu'   =>'Apellido y Nombre Usuario',
            'tipo_operacion'        =>'Tipo Operación',
            'numero_consulta'       =>'Nº consulta',
            'dni'                   =>'DNI',
            'cuit'                  =>'CUIT',
            'apellido_nombre_pers'  =>'Apellido y Nombre Agente',
            'estado'                =>'Estado',
            'articulo'              =>'Artículo',
            'interviniente'         =>'Interviniente',
            'fecha_intervencion'    =>'Fecha Intervención',
            'fecha_desde'           =>'Fecha Desde',
            'fecha_hasta'           =>'Fecha Hasta',
            'fecha_regreso_trabajo' =>'Fecha Regresa a Trabajar',
            'fecha_nueva_revision'  =>'Fecha Nueva Revisión',
            'observacion'           =>'Observaciones',
            'medico_tratante'       =>'Médico Tratante',
            'contacto_tratante'     =>'Contacto Tratante',
            'doc_adjuntos'          =>'Documentos'

        ];

        $data = Modelo\Consultamedica::listar_log_cambios_excel($params);
        array_walk($data,function (&$value) {
            unset($value['id']);

        });

        foreach ($data as $key => &$value) {
            $result = preg_replace("/\d{14}_/", "", $value['doc_adjuntos']); 
            $value['doc_adjuntos'] = $result;
               
        }

        $rol = Modelo\AppRoles::obtener_rol();
        foreach ($data as &$value) {
            if($rol === Modelo\AppRoles::ROL_ADMINISTRACION  || $rol === Modelo\AppRoles::ROL_RRHH){
                $value["observacion"] = "INFORMACIÓN RESTRINGIDA.";
            }
           
        }

        $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

        (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }
   
    protected function accion_ver_adjunto(){
        $archivo = Modelo\Consultamedica::obtener_archivo($this->request->query('id'));
        $doc_content = preg_replace("/\d{14}_/", "", $archivo['nombre']);
        $doc = BASE_PATH.'/uploads/intervencion/'.$archivo['nombre'];
        header("Content-Disposition:inline;filename=".$doc_content."");
        $doc_ingresado = explode('.',$doc);
        $extension = $doc_ingresado[1];
        switch ($extension) {
            case 'jpg':
                header("Content-type: image/jpeg");  
                readfile($doc);  
                break;
             case 'jpeg':
                header("Content-type: image/jpeg");  
                readfile($doc);  
                break;
            case 'pdf':
                header("Content-type: application/pdf");
                readfile($doc);  
                break;
        }    
    }

    public function  accion_baja_adjunto(){
        $archivo = Consultamedica::obtener_archivo($this->request->query('id'));
        $consultamedica = Consultamedica::obtener($archivo['id_consulta_medica']);
        if ($archivo['id']) {
            if ($this->request->post('confirmar')) {
                $result = $consultamedica->baja_archivo($archivo['id']);
                if($result>0){
                    $this->mensajeria->agregar('AVISO:El Archivo se eliminó de forma exitosa.', \FMT\Mensajeria::TIPO_AVISO, $this->clase, 'ver_historial');
                    $redirect = Vista::get_url('index.php/consultasmedicas/ver_historial/'.$consultamedica->id);
                    $this->redirect($redirect);
                }else{
                    $this->mensajeria->agregar('AVISO:No se ha podido eliminar el Archivo.', \FMT\Mensajeria::TIPO_ERROR, $this->clase, 'ver_historial');
                    $redirect = Vista::get_url('index.php/consultasmedicas/ver_historial'.$consultamedica->id);
                    $this->redirect($redirect);
                }
            }
        } else {
            $redirect = Vista::get_url('index.php/consultasmedicas/ver_historial');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'archivo')))->pre_render();
    }

    protected function accion_exportar_excel() {
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'consulta_medica'.date('Ymd_His');

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->post('search'), $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->post('search'));
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->post('search');
        }

        $params = [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'    => !empty($search) ? $search : '',
            'start'     => '',
            'lenght'    => '',
            'filtros'   => [
                'dni'                   => $this->request->post('dni'),
                'fecha_intervencion'    => $this->request->post('fecha_intervencion'),
                'id_estado'             => $this->request->post('estado'),
                'id_articulo'           => $this->request->post('articulo'),
                'fecha_desde'           => $this->request->post('fecha_desde'), 
                'fecha_hasta'           => $this->request->post('fecha_hasta')
            ],
        ];
     
        $titulos = [
            'id'                    =>'Nº Consulta',
            'estado'                =>'Estado',
            'fecha_intervencion'    =>'Fecha de Intervención',
            'articulo'              =>'Artículo',
            'dni'                   =>'DNI',
            'cuit'                  =>'Cuit',
            'apellido_nombre'       =>'Apellido y Nombre',
            'fecha_nacimiento'      =>'Fecha Nac.',
            'grupo_sanguineo'       =>'G. Sanguíneo',
            'modalidad_vinculacion' =>'Modalidad de Vinculación',
            'apto'                  =>'Apto',
            'tipo_apto'             =>'Tipo Apto',
            'fecha_apto'            =>'Fecha Apto',
            'nombre_interviniente'  =>'Médico / Enfermera interviniente',
            'fecha_desde'           =>'Fecha Desde',
            'fecha_hasta'           =>'Fecha Hasta',
            'fecha_regreso_trabajo' =>'Regresa a Trabajar',
            'fecha_nueva_revision'  =>'Nueva Revisión',
            'observacion'           =>'Observación',
            'medico_tratante'       =>'Médico Tratante',
            'telefono_contacto_tratante' =>'Tel. Contacto',
            'doc_adjuntos'               =>'Adjuntos'


        ];

        $data = Modelo\Consultamedica::listar_consultas_medicas_excel($params);

        foreach ($data as $key => &$value) {
            $result = preg_replace("/\d{14}_/", "", $value['doc_adjuntos']); 
            $value['doc_adjuntos'] = $result;
               
        }

        $rol = Modelo\AppRoles::obtener_rol();
        foreach ($data as &$value) {
            if($rol === Modelo\AppRoles::ROL_ADMINISTRACION  || $rol === Modelo\AppRoles::ROL_RRHH){
                $value["observacion"] = "INFORMACIÓN RESTRINGIDA.";
            }
           
        }

        $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

        (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }
    
    protected function accion_aptoMedico(){ 
		$vista				= $this->vista;
        $persona           = Modelo\Persona::obtenerPorDNI($this->request->query('id'));
        $file_nombre = 'Apto_medico';
          
        (new Vista($this->vista_default, compact('vista','file_nombre', 'persona')))->pre_render();
    }

    protected function accion_qrCode() {
        $consultamedica           = Modelo\Consultamedica::obtener($this->request->query('id'));
        if(!empty($consultamedica->id)){
            $persona  = Modelo\Persona::obtener($consultamedica->id_persona);
            if($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::MEDICO){
                $fecha_atencion = !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('d/m/Y') : '';
                $medico =  Modelo\Medico::obtener($consultamedica->id_interviniente);
                $nombre_interviniente = $medico->nombre.' '.$medico->apellido;
                $matricula = $medico->matricula;
            }elseif($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::ENFERMERA){
                $fecha_atencion = !empty($temp = $consultamedica->fecha_intervencion) ? $temp->format('d/m/Y') : '';
                $enfermera =  Modelo\Enfermera::obtener($consultamedica->id_interviniente);
                $nombre_interviniente = $enfermera->nombre.' '.$enfermera->apellido;
                $matricula = $enfermera->matricula;
            }
        }
        

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista','nombre_interviniente','matricula','persona','fecha_atencion')))->pre_render();
    }

    private function _traer_firma($id_interviniente = null, $flag_interviniente = null){
        if(!empty($id_interviniente) && !empty($flag_interviniente)){
            if($flag_interviniente == \App\Modelo\Consultamedica::MEDICO){
                $archivo = Modelo\Medico::obtener_firma_medico($id_interviniente);
            }elseif($flag_interviniente == \App\Modelo\Consultamedica::ENFERMERA){
                $archivo = Modelo\Enfermera::obtener_firma_enfermera($id_interviniente);
            }
            $doc = $archivo['firma'];
            return $doc;
        }
    
    return null;
    }


    protected function accion_comprobanteMedico(){
        $vista        = $this->vista;
        $consultamedica	= Modelo\Consultamedica::obtener($this->request->query('id'));
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $file_nombre = 'Comprobante_medico';
        if(!is_dir(BASE_PATH.'/uploads/intervencion')){
            $resp = mkdir(BASE_PATH.'/uploads/intervencion', 0775, false);
        }
        if(!is_dir(BASE_PATH.'/uploads/firma')){
            $resp = mkdir(BASE_PATH.'/uploads/firma', 0775, false);
        }
        //traer firma
        if(!empty($consultamedica->id)){
            $articulo=Modelo\Articulo::obtener($consultamedica->id_articulo);
            $nombre_articulo = $articulo->nombre;
            $persona  = Modelo\Persona::obtener($consultamedica->id_persona);

            if($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::MEDICO){
                $es_medico = \App\Modelo\Consultamedica::MEDICO;
                $interviniente =  Modelo\Medico::obtener($consultamedica->id_interviniente);
                $firma_adjunta = $this->_traer_firma($interviniente->id,$es_medico);
            }elseif($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::ENFERMERA){
                $es_enfermera = \App\Modelo\Consultamedica::ENFERMERA;
                $interviniente =  Modelo\Enfermera::obtener($consultamedica->id_interviniente);
                $firma_adjunta = $this->_traer_firma($interviniente->id, $es_enfermera);
               
            }

            if(!empty($firma_adjunta)){
                $firma = BASE_PATH.'/uploads/firma/'.$firma_adjunta;
                $textqr= \App\Helper\Vista::get_url('index.php/consultasmedicas/qrCode/'.$consultamedica->id);
                $sizeqr='100';
                $qrCode = new QrCode($textqr);
                $qrCode->setSize($sizeqr);
                $image= $qrCode->writeString();
                

            }else{
                $this->mensajeria->agregar(
                    "AVISO: No se encuentra la firma cargada, no se pudo validar el comprobante, comuníquese con el Administrador.",
                    \FMT\Mensajeria::TIPO_ERROR,
                    $this->clase
                );  

                $redirect = Vista::get_url('index.php/consultasmedicas/modificacion/'.$consultamedica->id);
                $this->redirect($redirect);
   
            }  

        }

        (new Vista($this->vista_default, compact('vista', 'file_nombre', 'consultamedica', 'persona','nombre_articulo','image','user', 'firma')))->pre_render();
    }

    protected function accion_enviarComprobante(){
        $data = [];
        $respuesta = false;
        $data_api= [];
        $config = \FMT\Configuracion::instancia();
        
        if($this->request->is_ajax()){
            $dni_paciente = $this->request->post('dni_paciente');
            $id_consulta = $this->request->post('id_consulta');
            
            $consultamedica =  Consultamedica::obtener($id_consulta);
            if($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::MEDICO){
                $es_medico = \App\Modelo\Consultamedica::MEDICO;
                $interviniente =  Modelo\Medico::obtener($consultamedica->id_interviniente);
                $firma_adjunta = $this->_traer_firma($interviniente->id,$es_medico);
            }elseif($consultamedica->tipo_interviniente==\App\Modelo\Consultamedica::ENFERMERA){
                $es_enfermera = \App\Modelo\Consultamedica::ENFERMERA;
                $interviniente =  Modelo\Enfermera::obtener($consultamedica->id_interviniente);
                $firma_adjunta = $this->_traer_firma($interviniente->id, $es_enfermera);
               
            }

            $persona  = Modelo\Persona::obtenerPorDNI($dni_paciente);
            //Lo ponemos asi para buscar en sigarhu el agente (persona) con su cuit
            $params['cuit'] = $dni_paciente;
            $agente_array = Modelo\SigarhuApi::searchAgentes($params);
            $persona_sigarhu = !empty($agente_array['0']) ? Modelo\SigarhuApi::getAgente($agente_array['0']['cuit']) : null;

            $id_dependencia = !empty($persona_sigarhu) ? ($persona_sigarhu->dependencia->id_dependencia) : null;                  
            
            $flag_enviado= Consultamedica::consultar_comprobante_enviado($id_consulta); 

            if($flag_enviado == \App\Modelo\Consultamedica::NO_ENVIADO && !empty($firma_adjunta)){
                $data_api   = Modelo\ControlAccesosApi::get_datos($id_dependencia);
                if($data_api){ 
                    if(!empty($data_api['emails'])){

                        $email_rrhh = $config['email']['email_rrhh']; //se debe poner el mail correcto al pasaje a prod
                        
                        array_push($data_api['emails'], $email_rrhh);
                    
                        if(!empty($persona) && !empty($persona_sigarhu->email)){
                        
                            array_push($data_api['emails'], $persona_sigarhu->email);
                        }
                       
                        $respuesta = $this->_enviarEmail($data_api,$persona, $id_consulta);

                        if($respuesta){
                            $data['envio_ok'] = ['Aviso: El correo ha sido enviado correctamente al RCA y a RRHH.'];
                        }else{
                            $data['error'] = ['Error: El correo no ha podido ser enviado.']; 
                        }
                    }elseif(!empty($data_api['info'])){
                        //si por alguna razon no encontró los mails de RCA, por ej: Los Rca no tienen cargada la dependencia por sistema (en C. de acceso) o por error
                        //muestra el mensaje de info y envia el mail al RRHH y al Agente si es que tiene mail.
                        $data['error_4'] = [$data_api['info'].'  Se envió el correo a RRHH.']; 

                        $email_rrhh = $config['email']['email_rrhh'];

                        $data_api['emails'] = [$email_rrhh];

                        if(!empty($persona) && !empty($persona_sigarhu->email)){
                        
                            array_push($data_api['emails'], $persona_sigarhu->email);
                        }
                        
                        $respuesta = $this->_enviarEmail($data_api,$persona, $id_consulta);

                    }
                   
                }else{
                        //no existe el rca porque la persona no es empleado.
                        $data['error_3'] = ['Aviso: No existe RCA. Se ha envíado el correo a RRHH. Entregue una copia al Agente y al RCA.'];
                       
                        $email_rrhh = $config['email']['email_rrhh'];
                        
                        $data_api['emails'] = [$email_rrhh];
                       
                        $respuesta = $this->_enviarEmail($data_api,$persona, $id_consulta);

                }
               
            }elseif($flag_enviado == \App\Modelo\Consultamedica::ENVIADO){//si ya se envio el correo = 1

                 $data['error_2'] = ['Aviso: Sólo se puede descargar el comprobante médico.'];
            }

            $this->json->setData($data);
            $this->json->render();
            exit;
        }    
    }
  
    private function _enviarEmail($data_api = [], $persona = null,$id_consulta = null){ 
        //para que de tiempo a que se genere el pdf y se guarde el archivo, para luego ir a buscarlo
        sleep(4);
        if(!empty($persona) && !empty($id_consulta)){
            $cabecera = date("d/m/Y");
            $config = \FMT\Configuracion::instancia();
            
            $datos_paciente = $persona->apellido_nombre. ' - DNI: '.$persona->dni; 
            $asunto = 'Servicio de Atencion Médica - '.$persona->apellido_nombre;
            $nombre = $persona->dni.'_'.$id_consulta.'_Comprobante_medico.pdf';
            $path = BASE_PATH.'/uploads/intervencion/'.$nombre;

            $mail = new \App\Helper\Email();
            $mail->set_asunto($asunto);
            $mail->set_contenido(VISTAS_PATH.'/consultasmedicas/email.php', compact('cabecera','datos_paciente'));
            
            
            if($path && $nombre){
                $mail->add_attachment($path, $nombre); 
            }

            $enviados = $mail->batch($data_api['emails']); 
          
            if($enviados['enviados_total']>=1){
                Modelo\Consultamedica::comprobante_enviado_alta($id_consulta,$data_api['emails']);
                return true;

            }elseif($enviados['enviados_total']<1){
                return false;
            }
        }
       
       return false;
        
    }
  

    protected  function accion_resumenempleado(){

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista')))->pre_render();
    }

    protected function  accion_get_resumen_consultas_medicas_empleados(){

        $resumen_consultas_medicas_empleados=Consultamedica::get_resumen_consultas_medicas_empleados();

        if (!empty($resumen_consultas_medicas_empleados)){
            $data	= [
                'resumen_empleados'=>$resumen_consultas_medicas_empleados,
                'status'=>200
            ];
        }else{
            $data	= [
                'resumen_empleados'=>[],
                'status'=>204
            ];
        }

        (new Vista(VISTAS_PATH.'/json_response.php',compact('data')))->pre_render();
    }

    protected  function  accion_busqueda_avanzada_consultasmedicas(){
        $dni=!empty($temp=$this->request->post('dni')) ?  $temp : null;
        $apellido_nombre=!empty($temp=$this->request->post('apellido_nombre')) ?  $temp : null;

        $resumen_consultas_medicas_empleados=Consultamedica::busqueda_avanzada_consultasmedicas($dni,$apellido_nombre);

        if (!empty($resumen_consultas_medicas_empleados)){
            $data	= [
                'resumen_empleados'=>$resumen_consultas_medicas_empleados,
                'status'=>200
            ];
        }else{
            $data	= [
                'resumen_empleados'=>[],
                'status'=>204
            ];
        }

        (new Vista(VISTAS_PATH.'/json_response.php',compact('data')))->pre_render();
    }

    protected function accion_resumen_eventos(){
        $persona = Modelo\Persona::obtenerPorDNI($this->request->query('id'));
        $vista   = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'persona')))->pre_render();
    }

    protected function accion_ajax_resumen_eventos(){
        $dni = $this->request->query('dni');
        $dataTable_columns    = $this->request->query('columns');
        $orders    = [];
        foreach ($orden = (array)$this->request->query('order') as $i => $val) {
            $orders[]    = [
                'campo'    => (!empty($tmp = $orden[$i]) && !empty($dataTable_columns) && is_array($dataTable_columns[0]))
                    ? $dataTable_columns[(int)$tmp['column']]['data']    :    'id',
                'dir'    => !empty($tmp = $orden[$i]['dir'])
                ? $tmp    :    'desc',
            ];
        }
        $date  = [];
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->query('search')['value'], $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->query('search')['value']);
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->query('search')['value'];
        }
        $params    = [
            'order'        => $orders,
            'start'        => !empty($tmp = $this->request->query('start'))
            ? $tmp : 0,
            'lenght'    => !empty($tmp = $this->request->query('length'))
            ? $tmp : 10,
            'search'    => !empty($search)
                ? $search : '',
            'filtros'   => [],
            'dni' => $dni,
        ];
        $data =  Modelo\Consultamedica::listar_resumen_eventos($params);
        $datos['draw']    = (int) $this->request->query('draw');
        (new Vista(VISTAS_PATH . '/json_response.php', compact('data')))->pre_render();
    }

    protected function accion_exportar_historia_clinica_excel() {
        $dni= $this->request->query('id');
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'historia_clinica'.date('Ymd_His');
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->post('search'), $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->post('search'));
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->post('search');
        }

        $params = [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'    => !empty($search) ? $search : '',
            'start'     => '',
            'lenght'    => '',
            'filtros'   => [
                'id_estado'             => $this->request->post('estado'),
                'id_articulo'           => $this->request->post('articulo'),
                'id_interviniente'      => $this->request->post('interviniente'),
            ],
             'dni' => $dni
        ];
        
        $titulos = [
            'numero_consulta'   => 'Nº consulta',
            'apellido_nombre'    => 'Apellido y Nombre',
            'dni'    => 'DNI',
            'tipo_operacion'    => 'Tipo de Operación',
            'fecha_operacion'   => 'Fecha de Operación',
            'fecha_intervencion'=> 'Fecha de Intervención',
            'articulo'          => 'Artículo',
            'estado'            => 'Estado',
            'interviniente'     => 'Interviniente',
            'fecha_desde'       => 'Fecha Desde',
            'fecha_hasta'       => 'Fecha Hasta',
            'fecha_regreso_trabajo' => 'Fecha Regreso Trabajo',
            'fecha_nueva_revision'  => 'Fecha Nueva Revisión',
            'observacion'           => 'Observaciones',
            'doc_adjuntos'          => 'Adjuntos'

        ];

        $data = Modelo\Consultamedica::listar_historia_clinica_excel($params);
        array_walk($data,function (&$value) {
            unset($value['id']);
         
        });

        $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

        (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }   

    protected function accion_exportar_excel_resumen_eventos() {
        $dni= $this->request->query('id');
        $user = Modelo\Usuario::obtenerUsuarioLogueado();
        $nombre = 'consulta_medica'.date('Ymd_His');

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $this->request->post('search'), $date)) {
            $el_resto = \preg_replace('/^\d{2}\/\d{2}\/\d{4}/', '', $this->request->post('search'));
            $search = \DateTime::createFromFormat('d/m/Y', $date[0])->format('Y-m-d') . $el_resto;
        } else {
            $search = $this->request->post('search');
        }

        $params = [
            'order' => [!empty($this->request->post('campo_sort')) ? [
                'campo'=> $this->request->post('campo_sort'),
                'dir' => $this->request->post('dir')
            ] : ''],
            'search'    => !empty($search) ? $search : '',
            'start'     => '',
            'lenght'    => '',
            'filtros'   => [
            ],
            'dni' => $dni
        ];
       
        $titulos = [
            'dni'                   =>'DNI',
            'cuit'                  =>'CUIT',
            'apellido_nombre'       =>'Apellido y Nombre',
            'articulo'              =>'Artículo',
            'cantidad_dias_norma'   =>'Cantidad de dias segun ley',
            'periodo_norma'         =>'Frecuencia según norma',
            'dias_tomados'          =>'Días tomados',
            'flag_alerta'           =>'Alerta'
        ];

        $data = Modelo\Consultamedica::listar_resumen_eventos_excel($params);
        foreach ($data as $key => &$value) {
            if($value['flag_alerta']==1){
                $value['flag_alerta'] = 'CORRECTA';
            }else{
                  $value['flag_alerta'] = 'INCORRECTA';
            }
           
        }
        array_walk($data,function (&$value) {
            unset($value['id']);

        });

        $data[] = ['Usuario' => 'Usuario Logueado: '. $user->nombre.' '. $user->apellido];

        (new Vista(VISTAS_PATH.'/csv_response.php',compact('nombre', 'titulos', 'data')))->render();
    }

}
