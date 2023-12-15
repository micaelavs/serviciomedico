<?php
namespace App\Helper;

/**
* Esta redefinicion de la clase Controlador incorpora la automatización de la selección del template base, de manera tal que el desarrrollador solo tendra que generar el template compoenente que cubra las necesidades de la funcionalida a desarrollar.
*
* A tal fin la cabecera, menu, pie y librerias comunes (css,js) son definidas antes del render final.
*
* La impresion en palntalla de la mensajeria tambien se abstrae de las incumbencias del desarrollador. 
*
* Incorpora la propiedad "_user" para el manejo del roles y permisos dentro de la aplicación. 
*
* Favorece por medio de la propiedad "vista_default" el ordenamiento de las vistas por controladores.
*/


use App\Helper\Util;
use App\Helper\Vista;
use App\Modelo\Usuario;
use App\Modelo\AppRoles;

class Controlador extends \FMT\Controlador {
/** @var array */
	public static $errores = [];
/** @var string */
	public static $metodo;
/** @var Usuario */
	protected $_user;
/** @var Usuario */
	protected $vista_default;

    public function procesar()
    {
        if($this->existe_accion()) {
	        if($this->antes()){
	            $this->{'accion_'.$this->accion}();
	        }
    	}
        $this->despues(); 
        $this->mostrar_vista();
    }

/** @return bool */
	 protected function antes() {
	 	$this->vista_default	= VISTAS_PATH . "/".Util::underscore($this->clase)."/{$this->accion}.php";
	 	$this->vista = new Vista(VISTAS_PATH.'/base.php',['vars'=>[]]);
	 	static::$metodo	= $_SERVER['REQUEST_METHOD'];
		$this->_user	= Usuario::obtenerUsuarioLogueado();
	 	return parent::antes();
	}

	protected function despues() {
		(new Vista(VISTAS_PATH.'/widgets/head.php',['user' => $this->_user,'vista' =>$this->vista]))->pre_render(); 		
		(new Vista(VISTAS_PATH.'/widgets/menu.php',['vista' =>$this->vista]))->pre_render();
		$this->mostrar_errores();
	}

	protected function existe_accion() {
		$metodos = get_class_methods($this);
        if(!in_array('accion_'.$this->accion, $metodos)) {
			$this->_user	= Usuario::obtenerUsuarioLogueado();
			$vista = new Vista(VISTAS_PATH.'/widgets/error_no_encontrado.php');
			$this->vista = new Vista(VISTAS_PATH.'/base.php',['vars'=>['CONTENT' => "$vista"]]);
			return false;
		} else {
			return true;
		}
	}


	protected function mostrar_errores(){

        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $vars = array();
        }else{
            $vars="";
        }



	    $error = $this->mensajeria->obtener();
	    $errors = '';
	    $avisos = '';
	    foreach ($error as $value) {
	    	switch ($value['tipo']) {
	    		case \FMT\Mensajeria::TIPO_ERROR:
	    			$errors .= (empty($errors)) ? $value['mensaje'] : '<br> <i class="fa fa-times-circle" aria-hidden="true"></i>
 '.$value['mensaje'];
	    			break;
	    		case \FMT\Mensajeria::TIPO_AVISO:
	    			$avisos .= (empty($avisos)) ? $value['mensaje'] : '<br> <i class="fa fa-check" aria-hidden="true"></i>
 '.$value['mensaje'];
	    			break;
	    	}
		}
		if($errors)
			$vars['ERROR'][]['MSJ'] = $errors;
		if($avisos)
			$vars['AVISO'][]['MSJ'] = $avisos;
		$this->vista->add_to_var('vars', $vars);
	}


	public function set_query($key,$value) {
		$this->request->query($key,$value);
	}


    /**
         * Permite almacenar estructuras de datos o cualquier información que se necesite consultar luego, en una
         * re-dirección u otro evento.
         * @param mixed $info
       */
    protected function mantener_info($info) {
            $_SESSION["informacion_preservada"] = serialize($info);
        }

    /**
     * Permite recuperar la información almacenada mediante el método mantener_info
     * @return mixed|null
     */
    protected function recuperar_info() {
        if (isset($_SESSION["informacion_preservada"])) {
            $info = unserialize($_SESSION["informacion_preservada"]);
            unset($_SESSION["informacion_preservada"]);

            return $info;
        }

        return null;
    }

}