<?php

namespace App\Controlador;

use App\Helper\Controlador;
use App\Helper\Vista;
use App\Modelo\Medico;


class Medicos extends Base{

    protected  function accion_index(){
        $medicos = Medico::listar_medicos();
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'medicos')))->pre_render();
    }

    public function  accion_alta(){
        $medicos = Medico::obtener($this->request->query('id'));
        if ($this->request->post('medicos') == 'alta') {
            $date_time = gmdate('YmdHis');
            $medicos->firma = !empty($_FILES['archivo']) ?  $_FILES['archivo'] : null; 
            if(!empty($medicos->firma['name'])){
                foreach ($medicos->firma as $key => $value) {
                    if($key=='name'){
                        $medicos->firma[$key] = $date_time.'_'.$value;
                   }
                }
            }else{
                $medicos->firma = null;
            } 
            $medicos->matricula = !empty($temp = $this->request->post('matricula')) ?  $temp : null;
            $medicos->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            $medicos->apellido = !empty($temp = $this->request->post('apellido')) ?  $temp : null;
            if ($medicos->validar()) {

                if($medico = Medico::obtener_por_nombre_si_borrado($medicos->matricula)){
                    $this->mantener_info($medico);
                    $this->redirect(Vista::get_url("index.php/medicos/reactivar"));
                }

                $resultado = $medicos->alta();
                if ($resultado > 0) {
                    $this->mensajeria->agregar(
                        "AVISO:El Registro fué ingresado de forma exitosa.",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/medicos/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "ERROR: Hubo un error en el alta.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/medicos/index");
                    $this->redirect($redirect);
                }
            } else {
                $err    = $medicos->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'medicos')))->pre_render();
    }

    public function  accion_modificacion(){
        $medicos = Medico::obtener($this->request->query('id'));
        if ($this->request->post('medicos') == 'modificacion') {
            $date_time = gmdate('YmdHis');
            $medicos->firma = !empty($_FILES['archivo']) ?  $_FILES['archivo'] : $medicos->firma; 
            if(!empty($medicos->firma['name'])){
                foreach ($medicos->firma as $key => $value) {
                   if($key=='name'){
                    $medicos->firma[$key] = $date_time.'_'.$value;
                   }
                }
            }else{
                 $medicos->firma = null;
            }
            $medicos->matricula = !empty($temp = $this->request->post('matricula')) ?  $temp : null;
            $medicos->nombre = !empty($temp = $this->request->post('nombre')) ?  $temp : null;
            $medicos->apellido = !empty($temp = $this->request->post('apellido')) ?  $temp : null;
            if ($medicos->validar()) {
                $medicos->modificacion();
                $this->mensajeria->agregar(
                    "AVISO:El Registro fué modificado de forma exitosa.",
                    \FMT\Mensajeria::TIPO_AVISO,
                    $this->clase
                );
                $redirect = Vista::get_url("index.php/medicos/index");
                $this->redirect($redirect);
            } else {
                $err    = $medicos->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'medicos')))->pre_render();
    }

    public function  accion_baja(){
        $medicos = Medico::obtener($this->request->query('id'));
        if ($medicos->id) {
            if ($this->request->post('confirmar')) {
                $medicos->baja();
                $this->mensajeria->agregar('AVISO:El Registro se eliminó de forma exitosa.', \FMT\Mensajeria::TIPO_AVISO, $this->clase, 'index');
                $redirect = Vista::get_url('index.php/medicos/index');
                $this->redirect($redirect);
            }
        } else {
            $redirect = Vista::get_url('index.php/medicos/index');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('medicos', 'vista')))->pre_render();
    }

     protected function accion_reactivar() {
        if ($this->request->is_post() && $this->request->post('confirmar')) {
            $medico= Medico::obtener($this->request->post('id_registro'));
            if($medico->reactivar()){
                $this->mensajeria->agregar('AVISO:La El Médico se reactivó de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
                $this->redirect(Vista::get_url('index.php/medicos/index'));
            }
        }
        $medico = (($recup = $this->recuperar_info()) && $recup instanceof Medico)? $recup : Medico::obtener();

        if(!$medico->id) {
            $this->mensajeria->agregar('AVISO:Id de Médico no encontrado.', \FMT\Mensajeria::TIPO_ERROR, $this->clase, 'index');
            $this->redirect(Vista::get_url('index.php/medicos/index'));
        }
        $vista = $this->vista;
        (new Vista(VISTAS_PATH.'/medicos/reactivar.php',compact('medico','vista')))->pre_render();
    }
}
