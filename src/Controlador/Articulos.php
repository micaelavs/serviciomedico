<?php

namespace App\Controlador;

use App\Helper\Controlador;
use App\Helper;
use App\Helper\Vista;
use App\Modelo\Articulo;

class Articulos extends  Controlador
{

    protected  function accion_index(){

        $articulos=Articulo::listar_articulos();
        $periodo_por_norma=Articulo::getParam('PERIODO_POR_NORMA');
        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista','articulos','periodo_por_norma')))->pre_render();
    }

    public function  accion_alta(){

        $articulos = Articulo::obtener($this->request->query('id'));
        $periodo_por_norma=Articulo::getParam('PERIODO_POR_NORMA');

        if($this->request->post('articulos') == 'alta') {
            $articulos->nombre= !empty($temp=$this->request->post('nombre')) ?  $temp : null;
            $articulos->descripcion= !empty($temp=$this->request->post('descripcion')) ?  $temp : null;
            $articulos->cantidad_dias_norma= !empty($temp=$this->request->post('cantidad_dias_norma')) ?  $temp : null;
            $articulos->periodo_norma= !empty($temp=$this->request->post('periodo_norma')) ?  $temp : null;


            if($articulos->validar()){

                $resultado=$articulos->alta();

                if($resultado>0){
                    $this->mensajeria->agregar(
                        "AVISO:El Registro fué ingresado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
                    $redirect =Vista::get_url("index.php/articulos/index");
                    $this->redirect($redirect);
                }else{
                    $this->mensajeria->agregar(
                        "ERROR: Hubo un error en el alta.",\FMT\Mensajeria::TIPO_ERROR,$this->clase);
                    $redirect =Vista::get_url("index.php/articulos/index");
                    $this->redirect($redirect);
                }

            }else {
                $err	= $articulos->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

                 if (Articulo::$FLAG) {
                    $articuloCargado =  Articulo::obtenerPorNombre($articulos->nombre);
                    $redirect = Vista::get_url("index.php/articulos/activar/{$articuloCargado->id}");
                    $this->redirect($redirect);
                }
            }

        }

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista', 'articulos' ,'periodo_por_norma')))->pre_render();
    }


    public function  accion_modificacion(){
        $articulos = Articulo::obtener($this->request->query('id'));
        $periodo_por_norma=Articulo::getParam('PERIODO_POR_NORMA');

        if($this->request->post('articulos') == 'modificacion') {

            $articulos->nombre= !empty($temp=$this->request->post('nombre')) ?  $temp : null;
            $articulos->descripcion= !empty($temp=$this->request->post('descripcion')) ?  $temp : null;
            $articulos->cantidad_dias_norma= !empty($temp=$this->request->post('cantidad_dias_norma')) ?  $temp : null;
            $articulos->periodo_norma= !empty($temp=$this->request->post('periodo_norma')) ?  $temp : null;

            if($articulos->validar()){

                $articulos->modificacion();

                $this->mensajeria->agregar(
                    "AVISO:El Registro fué modificado de forma exitosa.",\FMT\Mensajeria::TIPO_AVISO,$this->clase);
                $redirect =Vista::get_url("index.php/articulos/index");
                $this->redirect($redirect);


            }else {
                $err	= $articulos->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }

        }

        $vista = $this->vista;
        (new Vista($this->vista_default,compact('vista', 'articulos','periodo_por_norma' )))->pre_render();
    }

    public function  accion_baja(){
        $articulos = Articulo::obtener($this->request->query('id'));

        if($articulos->id){
            if ($this->request->post('confirmar')) {

                $articulos->baja();
                $this->mensajeria->agregar('AVISO:El Registro se eliminó de forma exitosa.',\FMT\Mensajeria::TIPO_AVISO,$this->clase,'index');
                $redirect = Vista::get_url('index.php/articulos/index');
                $this->redirect($redirect);
            }

        } else {
            $redirect = Vista::get_url('index.php/articulos/index');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default,compact('articulos', 'vista')))->pre_render();
    }

    protected function accion_activar(){
        $articulo = Articulo::obtener($this->request->query('id'));
        if (!empty($articulo->id)) {
            if ($this->request->post('confirmar')) {
                if ($articulo->id) {
                    if ($articulo->activar()) {
                        $this->mensajeria->agregar(
                            " El artículo <strong>{$articulo->nombre}</strong> se ha reactivado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido reactivar el artículo <strong>{$articulo->nombre}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/articulos/index/");
                    $this->redirect($redirect);
                }
            }
        }
        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'articulo')))->pre_render();
    }


}