<?php

namespace App\Controlador;

use App\Helper\Controlador;
use App\Helper\Vista;
use App\Modelo\Enfermera;


class Enfermeras extends  Controlador
{

    protected  function accion_index(){

        $enfermeras=Enfermera::listar_enfermeras();

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista','enfermeras')))->pre_render();
    }

    public function  accion_alta(){

        $enfermeras = Enfermera::obtener($this->request->query('id'));

        if($this->request->post('enfermeras') == 'alta') {
            $date_time = gmdate('YmdHis');
            $enfermeras->firma = !empty($_FILES['archivo']) ?  $_FILES['archivo'] : null; 
            if(!empty($enfermeras->firma['name'])){
                foreach ($enfermeras->firma as $key => $value) {
                   if($key=='name'){
                    $enfermeras->firma[$key] = $date_time.'_'.$value;
                   }
                }
            }else{
                $enfermeras->firma = null;
            }
            $enfermeras->matricula= !empty($temp=$this->request->post('matricula')) ?  $temp : null;
            $enfermeras->nombre= !empty($temp=$this->request->post('nombre')) ?  $temp : null;
            $enfermeras->apellido= !empty($temp=$this->request->post('apellido')) ?  $temp : null;


            if($enfermeras->validar()){

                if($enfermera=Enfermera::obtener_por_nombre_si_borrado($enfermeras->matricula)){
                    $this->mantener_info($enfermera);
                    $this->redirect(Vista::get_url("index.php/enfermeras/reactivar"));
                }
                
                
                $resultado=$enfermeras->alta();

                    if($resultado>0){
                        $this->mensajeria->agregar(
                            "AVISO:El Registro fué ingresado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
                        $redirect =Vista::get_url("index.php/enfermeras/index");
                        $this->redirect($redirect);
                    }else{
                        $this->mensajeria->agregar(
                            "ERROR: Hubo un error en el alta.",\FMT\Mensajeria::TIPO_ERROR,$this->clase);
                        $redirect =Vista::get_url("index.php/enfermeras/index");
                        $this->redirect($redirect);
                    }

            }else {
                $err	= $enfermeras->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }

        }

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista', 'enfermeras' )))->pre_render();
    }


    public function  accion_modificacion(){
        $enfermeras = Enfermera::obtener($this->request->query('id'));

        if($this->request->post('enfermeras') == 'modificacion') {
            $date_time = gmdate('YmdHis');
            $enfermeras->firma = !empty($_FILES['archivo']) ?  $_FILES['archivo'] : $enfermeras->firma; 
            if(!empty($enfermeras->firma['name'])){
                foreach ($enfermeras->firma as $key => $value) {
                    if($key=='name'){
                    $enfermeras->firma[$key] = $date_time.'_'.$value;
                    }
                }
            }else{
                $enfermeras->firma = null;
            }
           
            $enfermeras->matricula= !empty($temp=$this->request->post('matricula')) ?  $temp : null;
            $enfermeras->nombre= !empty($temp=$this->request->post('nombre')) ?  $temp : null;
            $enfermeras->apellido= !empty($temp=$this->request->post('apellido')) ?  $temp : null;


            if($enfermeras->validar()){

                $enfermeras->modificacion();

                    $this->mensajeria->agregar(
                        "AVISO:El Registro fué modificado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
                    $redirect =Vista::get_url("index.php/enfermeras/index");
                    $this->redirect($redirect);


            }else {
                $err	= $enfermeras->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }

        }

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista', 'enfermeras' )))->pre_render();
    }

    public function  accion_baja(){
        $enfermeras = Enfermera::obtener($this->request->query('id'));

        if($enfermeras->id){
            if ($this->request->post('confirmar')) {

                    $enfermeras->baja();
                    $this->mensajeria->agregar('AVISO:El Registro se eliminó de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
                    $redirect = Vista::get_url('index.php/enfermeras/index');
                    $this->redirect($redirect);
            }

        } else {
            $redirect = Vista::get_url('index.php/enfermeras/index');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default,compact('enfermeras', 'vista')))->pre_render();
    }

    protected function accion_reactivar() {

        if ($this->request->is_post() && $this->request->post('confirmar')) {
            $enfermeras= Enfermera::obtener($this->request->post('id_registro'));
            if($enfermeras->reactivar()){
                $this->mensajeria->agregar('AVISO:La Enfermera se reactivó de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
                $this->redirect(Vista::get_url('index.php/enfermeras/index'));
            }
        }
        $enfermeras = (($recup = $this->recuperar_info()) && $recup instanceof Enfermera)? $recup : Enfermera::obtener();

        if(!$enfermeras->id) {
            $this->mensajeria->agregar('AVISO:Id de Enferma no encontrado.', \FMT\Mensajeria::TIPO_ERROR, $this->clase, 'index');
            $this->redirect(Vista::get_url('index.php/enfermeras/index'));
        }
        $vista = $this->vista;
        (new Vista(VISTAS_PATH.'/enfermeras/reactivar.php',compact('enfermeras','vista')))->pre_render();
    }

}