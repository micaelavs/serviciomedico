<?php
namespace App\Modelo;

class SigarhuApi extends \FMT\ApiCURL {
	use SigarhuTrait;

/**
 * Obtener informacion del agente (empleado), a partir del cuit o el id.
 * Si se combina con el metodo `SigarhuApi::contiene()` se puede filtrar la informacion vinculada, para economizar bits de datos.
 * @param string	$cuit	- Cuit del agente que se quiere consultar.
 * @param boolean	$by_id	- Si es true el parametro `$cuit` sera un `id`
 *
 * @return object
*/
	static public function getAgente($cuit=null,$by_id=false) {
		static $cache_cuit	= null;
		static $cache_by_id	= null;
		static $return		= [];
		static $cache_contiene	= null;
		$contiene				= static::getContiene();
		if($cache_cuit === $cuit && $cache_by_id === $by_id && !empty($return['data']) && $cache_contiene === $contiene){
			return $return['data'];
		} else {
			$cache_cuit		= $cuit;
			$cache_by_id	= $by_id;
			$cache_contiene	= $contiene;
		}
		$api = static::getInstance();
		$api->setQuery([
			'contiene'	=> $cache_contiene,
			'by_id'		=> $by_id,
		]);

		$return 		= $api->consulta('GET', "/agente/{$cuit}");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		$return['data']	= static::arrayToObject($return['data']);
		return $return['data'];
	}

	static public function getParametricos($params=null){
		static $cache_params	= null;
		static $return			= [];
		if($cache_params === $params && !empty($return['data'])){
			return $return['data'];
		} else {
			$cache_params	= $params;
		}
		if(is_array($params)){
			static::contiene($params);
		}
		$api = static::getInstance();
		$api->setQuery([
			'contiene'	=> static::getContiene(),
		]);

		$return = $api->consulta('GET', "/parametricos");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		return $return['data'];
	}

/**
 * Obtener informacion del agente (empleado), a partir del cuit o el id.
 * Si se combina con el metodo `SigarhuApi::contiene()` se puede filtrar la informacion vinculada, para economizar bits de datos.
 * @param string	$cuit	- Cuit del agente que se quiere consultar.
 * @param boolean	$by_id	- Si es true el parametro `$cuit` sera un `id`
 *
 * @return array
*/
	static public function searchAgentes($params=array()) {
		static $cache_params	= null;
		static $return			= [];
		if($cache_params === $params && !empty($return['data'])){
			return $return['data'];
		} else {
			$cache_params	= $params;
		}
		$paramsDefault	= [
			'cuit'				=> null,
			'nombre_apellido'	=> null,
			'estado' 			=> null,
			'agente_activo'		=> null,
			'limit_1'			=> null,
		];
		$params	= array_merge($paramsDefault, $params);
		if(empty($params['cuit']) && empty($params['nombre_apellido'])){
			return [];
		}

		$api = static::getInstance();
		$api->setQuery([
			'params'	=> $params,
		]);

		$return = $api->consulta('GET', "/search_agentes");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		return $return['data'];
	}

	static public function getDependencia($id=null){
		static $cache_id	= null;
		static $return			= [];
		if(!empty($id)){
			return [];
		}
		if($cache_id === $id && !empty($return['data'])){
			return $return['data'];
		} else {
			$cache_id	= $id;
		}
		$api = static::getInstance();
		$return 		= $api->consulta('GET', "/dependencias/{$cache_id}");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		$return['data']	= static::arrayToObject($return['data']);
		return $return['data'];
	}

	static public function getConvenios($id_modalidad_vinculacion=null,$id_situacion_revista=null){
		static $cache_vinc	= null;
		static $cache_revi	= null;
		static $return		= [];
		if($cache_vinc === $id_modalidad_vinculacion && $cache_revi === $id_situacion_revista && !empty($return['data'])){
			return $return['data'];
		}else{
			$cache_vinc	= $id_modalidad_vinculacion;
			$cache_revi	= $id_situacion_revista;
		}
		if(!(is_numeric($id_modalidad_vinculacion) && is_numeric($id_situacion_revista))) {
			return [];
		}

		$api = static::getInstance();
		$api->setQuery([
			'id_modalidad_vinculacion'	=> $id_modalidad_vinculacion,
			'id_situacion_revista'		=> $id_situacion_revista,
		]);

		$return = $api->consulta('GET', "/convenios");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return false;
		// }
		if(empty($return['data'])){
			return [];
		}
		return $return['data'];
	}
/**
 * Crea o actualiza la informacion de un agente en Sigarhu.
 *
 * @param object $legajo	- Legajo con la estructura identica a la devuelta por SigarhuApi::getAgente()
 * @param string $accion	- Limitar el comportamiento a una accion concreta. E.j.: 'modificacion_estado'
 *
 * @return object	- Devuelve lo mismo que recibe pero con las modificaciones pertinentes.
*/
	static public function actualizarAgente($legajo=null, $accion=null){
		$config	= \FMT\Configuracion::instancia();
		if(empty($config['app']['actualizar_sigarhu'])){
			return $legajo;
		}
		$api = static::getInstance();
	
		$return 		= $api->consulta('POST', "/createEmpleado/{$legajo->cuit}", ['data' => json_encode($legajo)]);
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($return['mensajes']);
		// 	return false;
		// }
		if(empty($return['data'])){
			return [];
		}
		return $return['data'];
	}

	static public function getResponsablesContrato($dependencia_id=null){
		static $cache_dependencia_id = null;
		static $return		 		 = [];
		if($cache_dependencia_id == $dependencia_id && !empty($return['data'])){
			return $return['data'];
		} else {
			$cache_dependencia_id = $dependencia_id;
		}
		$api			= static::getInstance();
		$return	= $api->consulta('GET', "/responsables_contrato/{$cache_dependencia_id}");
		// if($api->getStatusCode() >= 300){
		// 	static::setErrores($cache_return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		return $return['data'];
	}

/**
 * Devuelve los datos de Unidad Retributiva (montos y cantidades maximas).
 * Si no se pasan valores devuelve un array de objetos.
 *
 * @param int $id_funcion	- Opcional. El Nivel para modalidad 1109
 * @param int $id_nivel		- Opcional. El Grado para modalidad 1109
 * @return array|object
 */
	static public function getConvenioUR($id_funcion=null, $id_nivel=null, $fecha_filtro=false){
		static $cache_id_funcion= null;
		static $cache_id_nivel	= null;
		static $return			= [];
		if($cache_id_funcion === $id_funcion && $cache_id_nivel === $id_nivel && !empty($return['data'])){
			return $return['data'];
		} else {
			$cache_id_funcion	= $id_funcion;
			$cache_id_nivel		= $id_nivel;
		}
		if(empty($id_funcion) || empty($id_nivel)){
			$funcion_nivel	= '';
		} else {
			$funcion_nivel	= $id_funcion.'-'.$id_nivel;
		}
		$api = static::getInstance();
		if($fecha_filtro){
			$fecha_filtro	= ($fecha_filtro instanceof \DateTime) ? $fecha_filtro->format('Y-m-d') : $fecha_filtro;
			$api->setQuery([
				'fecha_filtro'	=> $fecha_filtro,
			]);
		}
		$return 		= $api->consulta('GET', "/convenio_ur/{$funcion_nivel}");
		// if($api->getStatusCode() >= 300 || empty($return['data'])){
		// 	static::setErrores($return['mensajes']);
		// 	return [];
		// }
		if(empty($return['data'])){
			return [];
		}
		foreach ($return['data'] as &$value) {
			$value	= static::arrayToObject($value);
		}
		return $return['data'] = static::arrayToObject($return['data']);
	}

/**
 * Recibe el objecto devuelto por ::getConvenioUR() modificado.
 *
 * @param object $data
 * @return object - Devuelve el objeto modificado.
 */
	static public function actualizarConvenioUR($data){
		$config	= \FMT\Configuracion::instancia();
		if(empty($config['app']['actualizar_sigarhu'])){
			static::setErrores(['El sistema SIGECO no esta autorizado a actualizar datos en SIGARHU']);
			return false;
		}
		if(!is_object($data) || empty($data->id_nivel) || empty($data->id_grado)){
			return false;
		}
		$funcion_nivel	= $data->id_nivel.'-'.$data->id_grado;

		$api = static::getInstance();
		$return 		= $api->consulta('POST', "/convenio_ur/{$funcion_nivel}", ['data'=>json_encode($data)]);
		if($api->getStatusCode() >= 300){
			static::setErrores($return['mensajes']);
			return false;
		}
		$return['data']	= static::arrayToObject($return['data']);
		return $return['data'];
	}
}