<?php

namespace App\Controlador;

use App\Helper\Controlador;
use App\Helper;
use App\Helper\Vista;
use App\Modelo\Estado;


class Estados extends Base {

    protected  function accion_index(){
        $estados = Estado::listar_estados();
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'estados')))->pre_render();
    }

    public function  accion_alta(){
        $estados = Estado::obtener($this->request->query('id'));
        if ($this->request->post('estados') == 'alta') {
            $estados->estado      = !empty($temp = $this->request->post('estado')) ?  $temp : null;
            $estados->descripcion = !empty($temp = $this->request->post('descripcion')) ?  $temp : null;
            if ($estados->validar()) {
                $resultado = $estados->alta();
                if ($resultado > 0) {
                    $this->mensajeria->agregar(
                        "AVISO:El Registro fué ingresado de forma exitosa.",
                        \FMT\Mensajeria::TIPO_AVISO,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/estados/index");
                    $this->redirect($redirect);
                } else {
                    $this->mensajeria->agregar(
                        "ERROR: Hubo un error en el alta.",
                        \FMT\Mensajeria::TIPO_ERROR,
                        $this->clase
                    );
                    $redirect = Vista::get_url("index.php/estados/index");
                    $this->redirect($redirect);
                }
            } else {
                $err    = $estados->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }

                if (Estado::$FLAG) {
                    $areaCargado =  Estado::obtenerPorNombre($estados->estado);
                    $redirect = Vista::get_url("index.php/estados/activar/{$areaCargado->id}");
                    $this->redirect($redirect);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'estados')))->pre_render();
    }

    public function  accion_modificacion(){
        $estados = Estado::obtener($this->request->query('id'));
        if ($this->request->post('estados') == 'modificacion') {
            $estados->estado      = !empty($temp = $this->request->post('estado')) ?  $temp : null;
            $estados->descripcion = !empty($temp = $this->request->post('descripcion')) ?  $temp : null;
            if ($estados->validar()) {
                $estados->modificacion();
                $this->mensajeria->agregar(
                    "AVISO:El Registro fué modificado de forma exitosa.",
                    \FMT\Mensajeria::TIPO_AVISO,
                    $this->clase
                );
                $redirect = Vista::get_url("index.php/estados/index");
                $this->redirect($redirect);
            } else {
                $err    = $estados->errores;
                foreach ($err as $text) {
                    $this->mensajeria->agregar($text, \FMT\Mensajeria::TIPO_ERROR, $this->clase);
                }
            }
        }

        $vista = $this->vista;
        (new Vista($this->vista_default, compact('vista', 'estados')))->pre_render();
    }

    public function  accion_baja(){
        $estados = Estado::obtener($this->request->query('id'));
        if ($estados->id) {
            if ($this->request->post('confirmar')) {
                $estados->baja();
                $this->mensajeria->agregar('AVISO:El Registro se eliminó de forma exitosa.', \FMT\Mensajeria::TIPO_AVISO, $this->clase, 'index');
                $redirect = Vista::get_url('index.php/estados/index');
                $this->redirect($redirect);
            }
        } else {
            $redirect = Vista::get_url('index.php/estados/index');
            $this->redirect($redirect);
        }
        $vista = $this->vista;
        (new Vista($this->vista_default, compact('estados', 'vista')))->pre_render();
    }

    protected function accion_activar(){
        $estados = Estado::obtener($this->request->query('id'));
        if (!empty($estados->id)) {
            if ($this->request->post('confirmar')) {
                if ($estados->id) {
                    if ($estados->activar()) {
                        $this->mensajeria->agregar(
                            " El estado <strong>{$estados->estado}</strong> se ha reactivado correctamente.",
                            \FMT\Mensajeria::TIPO_AVISO,
                            $this->clase
                        );
                    } else {
                        $this->mensajeria->agregar(
                            "No se ha podido reactivar el estado <strong>{$estados->estado}</strong>.",
                            \FMT\Mensajeria::TIPO_ERROR,
                            $this->clase
                        );
                    }
                    $redirect = Vista::get_url("index.php/estados/index/");
                    $this->redirect($redirect);
                }
            }
        }
        $vista = $this->vista;
        (new Helper\Vista($this->vista_default, compact('vista', 'estados')))->pre_render();
    }
}
