<?php

namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;

class Estado extends Modelo
{

    /**@var int */
    public $id;
    /**@var Varchar**/
    public $estado;
    /**@var Varchar**/
    public $descripcion;
    /**@var int */
    public $borrado;

    static public $FLAG   = false;

    public static function obtener($id = null){
        $obj    = new static;
        if ($id === null) {
            return static::arrayToObject();
        }
        $sql_params    = [
            ':id'    => $id,
        ];
        $campos    = implode(',', [
            'id', 'estado', 'descripcion', 'borrado'
        ]);
        $sql    = <<<SQL
			SELECT {$campos}
			FROM estados
			WHERE id = :id
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    public static  function listar_estados(){
        $sql_params    = [];
        $sql    = <<<SQL
		SELECT id,estado,descripcion FROM estados WHERE borrado=0
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (empty($res)) {
            return [];
        }
        return $res;
    }


    public function alta(){
        $campos    = [
            'estado', 'descripcion'
        ];
        $sql_params    = [];
        foreach ($campos as $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
        }

        $sql    = 'INSERT INTO estados(' . implode(',', $campos) . ') VALUES (:' . implode(',:', $campos) . ')';
        $query = new Conexiones();
        $res    = $query->consulta(Conexiones::INSERT, $sql, $sql_params);


        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Estado';
            Logger::event('alta', $datos);
        } else {
            $this->errores = $query->errorCode;
        }
        return $res;
    }

    public function modificacion(){
        $campos    = [
            'estado', 'descripcion'
        ];

        $sql_params    = [
            ':id'    => $this->id,
        ];
        foreach ($campos as $key => $campo) {
            $sql_params[':' . $campo]    = $this->{$campo};
            unset($campos[$key]);
            $campos[$campo]    = $campo . ' = :' . $campo;
        }

        $sql    = 'UPDATE estados SET ' . implode(',', $campos) . ' WHERE id = :id';
        $query = new Conexiones();
        $res    = $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Estado';
            Logger::event('modificacion', $datos);
        } else {
            $this->errores = $query->errorCode;
            return $this->errores;
        }
        return $res;
    }

    public function baja(){
        $conexion = new Conexiones;
        $params = [':id' => $this->id];
        $sql = <<<SQL
		UPDATE estados SET  borrado = 1 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Estado';
            Logger::event('baja', $datos);
        } else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_baja", $datos);
        }
        return $res;
    }

    public function validar(){
        static::$FLAG  = false;
        $campos = (array)$this;
        $rules = [
            'id'        => ['numeric'],
            'descripcion'  => ['required', 'texto', 'max_length(250)']
             ];

            if(!empty($campos['estado'])){
            $rules     += [
                'estado'  => ['texto', 'max_length(250)', 'UnicoRegistroActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':estado'           => '%'.$input.'%',
                        ':estado_uppercase' => '%'.strtoupper($input).'%',
                        ':estado_lowercase' => '%'.strtolower($input).'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT estado FROM estados WHERE (estado LIKE :estado OR estado LIKE :estado_uppercase OR estado LIKE :estado_lowercase) AND borrado = 0' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                 
                    return empty($resp);
            },
            'UnicoRegistroInactivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':estado'           => '%'.$input.'%',
                        ':estado_uppercase' => '%'.strtoupper($input).'%',
                        ':estado_lowercase' => '%'.strtolower($input).'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                   $sql        = 'SELECT estado FROM estados WHERE (estado LIKE :estado OR estado LIKE :estado_uppercase OR estado LIKE :estado_lowercase) AND borrado = 1' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    if(!empty($resp)){
                        static::$FLAG  = true;
                    }
                
                    return empty($resp);
                }]
            ];

        }else{
            $rules     += [
                'estado'  => ['required']
            ];
        }

        $nombres    = [
            'estado'       => 'Estado',
            'descripcion'  => 'Descripcion'
        ];
        $validator = Validator::validate((array)$this, $rules, $nombres);
        $validator->customErrors([
            'UnicoRegistroActivo()'   => ' Ya existe un Estado con el Nombre ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => ' Ya existe un Estado con el Nombre, debe activarla.'
        ]);
        if ($validator->isSuccess()) {
            return true;
        } else {
            $this->errores = $validator->getErrors();
            return false;
        }
    }

    static public function arrayToObject($res = []){
        $campos    = [
            'id' =>  'int',
            'estado' => 'string',
            'descripcion' => 'string',
        ];
        $obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}    = isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                case 'json':
                    $obj->{$campo}    = isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}    = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}    = isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
                default:
                    $obj->{$campo}    = isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }

        return $obj;
    }

    static public function lista_estados() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, estado as nombre, descripcion, borrado
        FROM estados
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
    }
    public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE estados SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'estado';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }

    static public function obtenerPorNombre($nombre = null){
        if ($nombre === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':estado'   => $nombre,
        ];
        $campos = implode(',', [
            'estado',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM estados
            WHERE estado = :estado
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

}
