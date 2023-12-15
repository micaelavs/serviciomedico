<?php
namespace App\Api;

use App\Modelo;

class Api extends \FMT\Controlador {
	protected function accion_index(){
		$info	= [
			'No existe la información solicitada.',
			'Los metodos disponibles son: /ejemplos',
		];
		$this->json->setData();
		$this->json->setMensajes($info);
		$this->json->setError();
		$this->json->render();
	}

/**
 * Metodo para ejemplificar la escritura de un API
 * - Permite consultar por CUIT o por ID.
 * - El comportamiento cambia segun el metodo de consulta, *GET* para obtener informacion. *POST* y *PUT* para crear o actualizar datos.
 *
 * Ejemplo de endpoint: url/api.php/ejemplos/1234
 *
 * @return void
*/
	protected function accion_ejemplos(){
		$param_get_1= $this->request->query('id');
		$data		= (object)['id' => null];
		switch ($this->request->method()) {
			case 'GET':
				$data	= (object)['id' => $param_get_1];
				break;
			case 'POST':
				$data	= (object)['id' => $param_get_1];
				break;
			case 'PUT':
				$data	= (object)['id' => $param_get_1];
				break;
			case 'DELETE':
				break;
		}
		if(empty($data->id)){
			$this->json->setError();
			$this->json->setMensajes(['Data no encontrada, pase un parametro.']);
		}
		$data	= json_decode(json_encode($data), true);
		$this->json->setData($data);
		$this->json->render();
	}

/**
 * Convierte un array a objecto en forma recursiva. Si algun indice es de tipo string y contiene la palabra *fecha* lo combierte a objecto DateTime::
 *
 * @param array		$data	- Informacion a convertir
 * @param bool		$como_objeto - Default: true. Devuelve un objeto o un array si es es `false`
 * @return array|object
 */
	static private function arrayToObject(&$data = null, $como_objeto = true)
	{
		foreach ($data as $attr => &$val) {
			if (is_array($val) && count($val) == 0) {
				$data[$attr]	= array();
				continue;
			}
			if (is_string($attr) && !is_array($val)) {
				$data[$attr]	= $val;
			} else if (is_string($attr) && preg_match('/fecha/i', $attr) && is_array($val) && !empty($val)) {
                $tmp    = \DateTime::createFromFormat('Y-m-d H:i:s', $val['date']);
                $tmp	= !empty($tmp) ? $tmp : \DateTime::createFromFormat('Y-m-d H:i:s.u', $val['date']); // arreglo para mantener compatibilidad entre versiones 5.5 y 5.6 o mayor de PHP

				$data[$attr]	= !empty($tmp) ? $tmp : \DateTime::createFromFormat('Y-m-d H:i:s.u', $val['date'].' 0:00:00.000000');
			} else if (is_array($val)) {
				if (is_array($val)) {
					$aux	= array_keys($val);
					$indice_numerico	= is_numeric(array_pop($aux));
				} else {
					$indice_numerico	= false;
				}

				$data[$attr]	= static::arrayToObject($val, !$indice_numerico);
			}
		}
		return ($como_objeto) ? (object) $data : $data;
	}
}
