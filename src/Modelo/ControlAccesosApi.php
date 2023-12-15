<?php
namespace App\Modelo;

class ControlAccesosApi extends \FMT\ApiCURL {

 	static private $ERRORES = false;


	static public function get_datos($id_dependencia) {  
			$config	= \FMT\Configuracion::instancia();
			$api = static::getInstance();
			$return = $api->consulta('GET', '/get_email_rca/'.$id_dependencia);
			if($api->getStatusCode() != '200'){
			static::setErrores($return['mensajes']);
			return false;
			}
			return $return['data'];
		}

    static protected function setErrores($data=false){
        static::$ERRORES = $data;
    }
    
    static public function getErrores(){
        return static::$ERRORES;
    }
	
}