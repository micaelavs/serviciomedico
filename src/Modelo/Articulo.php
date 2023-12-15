<?php

namespace App\Modelo;


use App\Helper\Conexiones;
use App\Helper\Validator;
use FMT\Logger;

class Articulo extends Modelo
{

    /** @var  int*/
    public $id;
    /** @var String */
    public $nombre;
    /** @var String */
    public $descripcion;
    /** @var String*/
    public $cantidad_dias_norma;
    /** @var int */
    public $periodo_norma;
    /** @var int */
    public $borrado;

    const APTO_MEDICO = 'Apto (Emisión certificado)';

    /*opciones periodo por norma*/
    const MENSUAL = 1;
    const TRIMESTRAL = 2;
    const SEMESTRAL = 3;
    const ANUAL = 4;
    const BIANUAL = 5;


    static public $PERIODO_POR_NORMA = [
        self::MENSUAL                 => ['id' => self::MENSUAL, 'nombre' => 'Mensual','borrado'=> 0],
        self::TRIMESTRAL  => ['id' => self::TRIMESTRAL, 'nombre' => 'Trimestral','borrado'=> 0],
        self::SEMESTRAL            => ['id' => self::SEMESTRAL, 'nombre' => 'Semestral','borrado'=> 0],
        self::ANUAL              => ['id' => self::ANUAL, 'nombre' => 'Anual','borrado'=> 0],
        self::BIANUAL              => ['id' => self::BIANUAL, 'nombre' => 'Bianual','borrado'=> 0],

    ];

    static public $FLAG   = false;

    public static function obtener($id = null){
        $obj	= new static;
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params	= [
            ':id'	=> $id,
        ];
        $campos	= implode(',', [
            'id','nombre','descripcion','cantidad_dias_norma','periodo_norma','borrado'
        ]);
        $sql	= <<<SQL
			SELECT {$campos}
			FROM articulos 
			WHERE id = :id
SQL;
        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    public function validar()
    {
        static::$FLAG  = false;
        $campos = (array)$this;
        $rules = [
            'id'        => ['numeric'],
            'descripcion'=>['required','max_length(250)'],
            'cantidad_dias_norma'=>['required','numeric','max_length(8)'],
            'periodo_norma' =>['required','numeric']

        ];

         if(!empty($campos['nombre'])){
            $rules     += [
                'nombre'  => ['texto', 'max_length(250)', 'UnicoRegistroActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                          ':nombre_uppercase' => '%'.strtoupper($input).'%',
                        ':nombre_lowercase' => '%'.strtolower($input).'%',
                        ':nombre'           => '%'.$input.'%',
                      
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT nombre FROM articulos WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 0' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    
                    return empty($resp);
            },
            'UnicoRegistroInactivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                      
                        ':nombre_uppercase' => '%'.strtoupper($input).'%',
                        ':nombre_lowercase' => '%'.strtolower($input).'%',
                          ':nombre'           => '%'.$input.'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                   $sql        = 'SELECT nombre FROM articulos WHERE (nombre LIKE :nombre OR nombre LIKE :nombre_uppercase OR nombre LIKE :nombre_lowercase) AND borrado = 1' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                    if(!empty($resp)){
                        static::$FLAG  = true;
                    }
                
                    return empty($resp);
                }]
            ];

        }else{
            $rules     += [
                'nombre'  => ['required']
            ];
        }




        $nombres	= [
            'nombre'=>'Nombre',
            'descripcion'=>'Descripción',
            'cantidad_dias_norma'=>'Cantidad días norma',
            'periodo_norma' => 'Período Norma'

        ];

        $validator = Validator::validate((array)$this, $rules, $nombres);
        
        $validator->customErrors([
            'UnicoRegistroActivo()'   => ' Ya existe un Artículo con el Nombre ingresado, por favor verifique.',
            'UnicoRegistroInactivo()' => ' Ya existe un Artículo con el Nombre ingresado, debe activarlo.'
        ]);

       

        if ($validator->isSuccess()) {
            return true;
        }
        else {
            $this->errores = $validator->getErrors();
            return false;
        }
    }

    public function alta()
    {
        $campos	= [
            'nombre','descripcion','cantidad_dias_norma','periodo_norma'
        ];
        $sql_params	= [
        ];
        foreach ($campos as $campo) {
            $sql_params[':'.$campo]	= $this->{$campo};
        }

        $sql	= 'INSERT INTO articulos ('.implode(',', $campos).') VALUES (:'.implode(',:', $campos).')';
        $query = new Conexiones();
        $res	= $query->consulta(Conexiones::INSERT, $sql, $sql_params);


        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Articulo';
            Logger::event('alta', $datos);
        }else{
            $this->errores = $query->errorCode;
        }
        return $res;
    }

    public function baja()
    {
        $conexion = new Conexiones;
        $params = [':id' => $this->id];
        $sql = <<<SQL
		UPDATE articulos SET  borrado = 1 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Articulo';
            Logger::event('baja', $datos);
        } else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_baja",$datos);
        }
        return $res;
    }

    public function modificacion()
    {
        $campos	= [
            'nombre','descripcion','cantidad_dias_norma','periodo_norma'
        ];

        $sql_params	= [
            ':id'	=> $this->id,
        ];
        foreach ($campos as $key => $campo) {
            $sql_params[':'.$campo]	= $this->{$campo};
            unset($campos[$key]);
            $campos[$campo]	= $campo .' = :'.$campo;
        }

        $sql	= 'UPDATE articulos SET '.implode(',', $campos).' WHERE id = :id';
        $query = new Conexiones();
        $res	= $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Articulo';
            Logger::event('modificacion', $datos);

        }else{
            $this->errores = $query->errorCode;
            return $this->errores;
        }
        return $res;
    }


    public static  function  listar_articulos(){

        $sql_params	= [
        ];

        $sql	= <<<SQL
		SELECT id,nombre,descripcion,cantidad_dias_norma,periodo_norma FROM articulos WHERE borrado=0
SQL;
        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(empty($res)){
            return [];
        }
        return $res;
    }

    static public function arrayToObject($res = []) {

        $campos	= [
            'id' =>  'int',
            'nombre'=>'string',
            'descripcion'=>'string',
            'cantidad_dias_norma'=>'int',
            'periodo_norma'=>'int'
        ];
        $obj = new self();
        foreach ($campos as $campo => $type) {
            switch ($type) {
                case 'int':
                    $obj->{$campo}	= isset($res[$campo]) ? (int)$res[$campo] : null;
                    break;
                case 'json':
                    $obj->{$campo}	= isset($res[$campo]) ? json_decode($res[$campo], true) : null;
                    break;
                case 'datetime':
                    $obj->{$campo}	= isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo]) : null;
                    break;
                case 'date':
                    $obj->{$campo}	= isset($res[$campo]) ? \DateTime::createFromFormat('Y-m-d H:i:s', $res[$campo] . ' 0:00:00') : null;
                    break;
                default:
                    $obj->{$campo}	= isset($res[$campo]) ? $res[$campo] : null;
                    break;
            }
        }

        return $obj;
    }

    /**
     * Obtiene los valores de los array parametricos.
     * E.J.: Nota::getParam('SELECT');
     */
    static public function getParam($attr=null){
        if($attr === null || empty(static::${$attr})){
            return [];
        }
        return static::${$attr};
    }

    static public function lista_articulos() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, nombre, borrado
        FROM articulos
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id']] = $value;
        }
        return $aux;
    }

     static public function obtenerPorNombre($nombre = null){
        if ($nombre === null) {
            return static::arrayToObject();
        }
        $sql_params = [
            ':nombre'   => $nombre,
        ];
        $campos = implode(',', [
            'nombre',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM articulos
            WHERE nombre = :nombre
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if (!empty($res)) {
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

      public function activar(){
        $cnx    = new Conexiones();
        $sql_params = [
            ':id'       => $this->id,
        ];
        $sql    = 'UPDATE articulos SET borrado = 0 WHERE id = :id';
        $res    = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);

        $flag   = false;
        if (!empty($res) && $res > 0) {
            $datos              = (array)$this;
            $datos['modelo']    = 'articulo';
            if (is_numeric($res) && $res > 0) {
                $flag = true;
            } else {
                $datos['error_db'] = $cnx->errorInfo;
            }
            Logger::event('activar', $datos);
        }
        return $flag;
    }
}