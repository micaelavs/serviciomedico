<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;

class Persona extends Modelo {

	const APTO_SI = 1;
    const APTO_NO = 2;

    const CERO_NEGATIVO = 1;
    const CERO_POSITIVO = 2;
    const A_NEGATIVO = 3;
    const A_POSITIVO = 4;
    const B_POSITIVO = 5;
    const B_NEGATIVO = 6;
    const AB_NEGATIVO = 7;
    const AB_POSITIVO = 8;

    const A = 1;
    const B_C_PREEXISTENCIA = 2;
    const C_C_PREEXISTENCIA = 3;
    const D_C_PREEXISTENCIA = 4;
    const NO_APTO = 5;

    static public $TIPO_APTO = [
        self::A 				=> ['id' => self::A, 'nombre' => 'A', 'borrado' => '0'],
        self::B_C_PREEXISTENCIA => ['id' => self::B_C_PREEXISTENCIA, 'nombre' => 'B c/ preexistencia', 'borrado' => '0'],
       	self::C_C_PREEXISTENCIA => ['id' => self::C_C_PREEXISTENCIA, 'nombre' => 'C c/ preexistencia', 'borrado' => '0'],
       	self::D_C_PREEXISTENCIA => ['id' => self::D_C_PREEXISTENCIA, 'nombre' => 'D c/ preexistencia', 'borrado' => '0'],
       	self::NO_APTO  			=> ['id' => self::NO_APTO, 'nombre' => 'No apto', 'borrado' => '0']
    ];

    static public $APTO = [
        self::APTO_SI   => ['id' => self::APTO_SI, 'nombre' => 'Si', 'borrado' => '0'],
        self::APTO_NO   => ['id' => self::APTO_NO, 'nombre' => 'No', 'borrado' => '0']
    ];

    static public $GRUPO_SANGUINEO = [
    	self::CERO_NEGATIVO		=> ['id' => self::CERO_NEGATIVO, 'nombre' => '0-', 'borrado' => '0'],
        self::CERO_POSITIVO  	=> ['id' => self::CERO_POSITIVO, 'nombre' => '0+', 'borrado' => '0'],
        self::A_NEGATIVO   		=> ['id' => self::A_NEGATIVO, 'nombre' => 'A-', 'borrado' => '0'],
        self::A_POSITIVO   		=> ['id' => self::A_POSITIVO, 'nombre' => 'A+', 'borrado' => '0'],
        self::B_POSITIVO   		=> ['id' => self::B_POSITIVO, 'nombre' => 'B+', 'borrado' => '0'],
		self::B_NEGATIVO   		=> ['id' => self::B_NEGATIVO, 'nombre' => 'B-', 'borrado' => '0'],
		self::AB_NEGATIVO  		=> ['id' => self::AB_NEGATIVO, 'nombre' => 'AB-', 'borrado' => '0'],
		self::AB_POSITIVO   	=> ['id' => self::AB_POSITIVO, 'nombre' => 'AB+', 'borrado' => '0']

    ];

	/**@var int**/
    public $id; 
	/**@var int**/
    public $id_sigarhu;
    /**@var int**/
	Public $dni;
	/**@var int**/
	Public $cuit;
	/**@var int**/
	Public $edad;
	/**@var string**/
	public $apellido_nombre;
	/**@var int**/
	public $apto;
	/**@var int**/
	public $tipo_apto;
	/**@var date**/
	public $fecha_nacimiento;
	/**@var int**/
	public $grupo_sanguineo;
	/**@var string**/
	public $modalidad_vinculacion;
	/**@var date**/
	public $fecha_apto;
	/**@var int */
	public $borrado;


	public static function obtener($id = null){
		$obj	= new static;
		if ($id === null) {
			return static::arrayToObject();
		}
		$sql_params	= [
			':id'	=> $id,
		];
		$campos	= implode(',', [
			'id', 'id_sigarhu','dni','cuit', 'apellido_nombre', 'apto','tipo_apto','fecha_nacimiento','grupo_sanguineo','modalidad_vinculacion','fecha_apto','borrado'
		]);
		$sql	= <<<SQL
			SELECT {$campos}
			FROM personas
			WHERE id = :id
SQL;
		$res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
		if (!empty($res)) {
			return static::arrayToObject($res[0]);
		}
		return static::arrayToObject();
	}

	public static  function listar(){
        $sql_params	= [];
        $sql	= <<<SQL
		SELECT id, id_sigarhu,dni,cuit,apellido_nombre,apto, tipo_apto, fecha_nacimiento,grupo_sanguineo,modalidad_vinculacion,fecha_apto
		FROM personas WHERE borrado=0
SQL;
        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(empty($res)){
            return [];
        }
        return $res;
    }


	public function alta(){

        $sql_params = [
        	':id_sigarhu'		=> $this->id_sigarhu,
        	':dni'          	=> $this->dni,
            ':cuit'          	=> $this->cuit,
            ':apellido_nombre'  => $this->apellido_nombre,
            ':apto'             => $this->apto,
            ':tipo_apto'       	=> $this->tipo_apto,
          	':grupo_sanguineo'	=> $this->grupo_sanguineo,
          	':modalidad_vinculacion' =>$this->modalidad_vinculacion,

        ];

        if($this->fecha_nacimiento instanceof \DateTime){
            $sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento->format('Y-m-d');
        }else{
        	$sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento;
        }

        if($this->fecha_apto instanceof \DateTime){
            $sql_params[':fecha_apto'] = $this->fecha_apto->format('Y-m-d');
        }else{
        	$sql_params[':fecha_apto'] = $this->fecha_apto;
          
        }
     
		$sql	= 'INSERT INTO personas (id_sigarhu,dni,cuit,apellido_nombre,apto,tipo_apto,fecha_nacimiento,grupo_sanguineo,modalidad_vinculacion,fecha_apto) VALUES (:id_sigarhu, :dni,:cuit, :apellido_nombre, :apto, :tipo_apto, :fecha_nacimiento, :grupo_sanguineo, :modalidad_vinculacion, :fecha_apto)';
		$query = new Conexiones();
		$res	= $query->consulta(Conexiones::INSERT, $sql, $sql_params);


		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Persona';
			Logger::event('alta', $datos);
		} else {
			$this->errores = $query->errorCode;
		}
		return $res;
	}

	public function modificacion_sigarhu()	{
		$campos	= [
			'dni','cuit', 'apellido_nombre', 'apto', 'tipo_apto', 'fecha_nacimiento','grupo_sanguineo','modalidad_vinculacion','fecha_apto'
		];

		$sql_params	= [
			':id_sigarhu'	=> $this->id_sigarhu,
		];
		foreach ($campos as $key => $campo) {
			$sql_params[':' . $campo]	= $this->{$campo};
			unset($campos[$key]);
			$campos[$campo]	= $campo . ' = :' . $campo;
		}

		if($this->fecha_nacimiento instanceof \DateTime){
            $sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento->format('Y-m-d');
        }else{
        	$sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento;
        }
        
        if($this->fecha_apto instanceof \DateTime){
            $sql_params[':fecha_apto'] = $this->fecha_apto->format('Y-m-d');
        }else{
        	$sql_params[':fecha_apto'] = $this->fecha_apto;
          
        }

		$sql	= 'UPDATE personas SET ' . implode(',', $campos) . ' WHERE id_sigarhu = :id_sigarhu';
		$query = new Conexiones();
		$res	= $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Persona';
			Logger::event('modificacion', $datos);
		} else {
			$this->errores = $query->errorCode;
			return $this->errores;
		}
		return $res;
	}

	public function modificacion()	{
		$campos	= [
			'dni','cuit', 'apellido_nombre', 'apto', 'tipo_apto','fecha_nacimiento','grupo_sanguineo','modalidad_vinculacion','fecha_apto'
		];

		$sql_params	= [
			':id'	=> $this->id,
		];
		foreach ($campos as $key => $campo) {
			$sql_params[':' . $campo]	= $this->{$campo};
			unset($campos[$key]);
			$campos[$campo]	= $campo . ' = :' . $campo;
		}

		if($this->fecha_nacimiento instanceof \DateTime){
            $sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento->format('Y-m-d');
        }else{
        	$sql_params[':fecha_nacimiento'] = $this->fecha_nacimiento;
        }
        
        if($this->fecha_apto instanceof \DateTime){
            $sql_params[':fecha_apto'] = $this->fecha_apto->format('Y-m-d');
        }else{
        	$sql_params[':fecha_apto'] = $this->fecha_apto;
          
        }

		$sql	= 'UPDATE personas SET ' . implode(',', $campos) . ' WHERE id = :id';
		$query = new Conexiones();
		$res	= $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Persona';
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
		UPDATE personas SET  borrado = 1 WHERE id = :id
SQL;
		$res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Persona';
			Logger::event('baja', $datos);
		} else {
			$datos['error_db'] = $conexion->errorInfo;
			Logger::event("error_baja", $datos);
		}
		return $res;
	}

	public function validar(){
		$rules = [
			'dni'				=> ['required', 'documento'],
			'apellido_nombre' 	=> ['required', 'texto',  'max_length(250)'],
			'apto'  			=> ['numeric'],
			'tipo_apto'  		=> ['numeric'],
			'fecha_nacimiento'  => ['fecha'],
			'grupo_sanguineo'  	=> ['numeric'],
			'modalidad_vinculacion' => ['texto',  'max_length(250)'],
			'fecha_apto'  => ['fecha','despuesDe(:fecha_nacimiento)']
		];

		 if(!empty($this->cuit)){
            $rules     += [
                'cuit'  => ['cuit']
            ];
        }

		$nombres	= [
			'dni'				=> 'DNI',
			'cuit'				=> 'Cuit',
			'apellido_nombre' 	=> 'Apellido y Nombre',
			'apto'  			=> 'Apto Médico',
			'tipo_apto'			=> 'Tipo de apto médico',
			'fecha_nacimiento' 	=> 'Fecha de Nacimento',
			'grupo_sanguineo'	=> 'Grupo Sanguíneo',
			'modalidad_vinculacion' => 'Modalidad de Vinculación',
			'fecha_apto' 			=> 'Fecha de apto'


		];

		$validator = Validator::validate((array)$this, $rules, $nombres);
		if ($validator->isSuccess()) {
			return true;
		} else {
			$this->errores = $validator->getErrors();
			return false;
		}
	}

	static public function arrayToObject($res = []){
		$campos	= [
			'id' 				=> 'int',
			'id_sigarhu'		=> 'int',	
			'dni'				=> 'int',
			'cuit'				=> 'int',
			'apellido_nombre' 	=> 'string',
			'apto' 				=> 'int',
			'tipo_apto'			=> 'int',
			'fecha_nacimiento' 	=> 'date',
			'grupo_sanguineo' 	=> 'int',
			'modalidad_vinculacion' => 'string',
			'fecha_apto' 			=> 'date'

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

	static public function obtenerPorCuit($cuit=null){
        if($cuit==null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':cuit'   => $cuit,
        ];
        $campos = implode(',', [
        	'id_sigarhu',
        	'dni',
            'cuit',
            'apellido_nombre',
            'apto',
            'tipo_apto',
            'fecha_nacimiento',
            'grupo_sanguineo',
            'modalidad_vinculacion',
            'fecha_apto',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM personas
            WHERE cuit = :cuit
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

    static public function lista_personas() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, dni, concat(dni,' - ',apellido_nombre) as nombre, borrado
        FROM personas
        WHERE borrado = 0
        ORDER BY id ASC"
    	);
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['dni']] = $value;
        }
        return $aux;
    }

    static public function obtenerPorDNI($dni=null){
        if($dni==null){
            return static::arrayToObject();
        }
        $sql_params = [
            ':dni'   => $dni,
        ];
        $campos = implode(',', [
        	'id_sigarhu',
        	'dni',
            'cuit',
            'apellido_nombre',
            'apto',
            'tipo_apto',
            'fecha_nacimiento',
            'grupo_sanguineo',
            'modalidad_vinculacion',
            'fecha_apto',
            'borrado'
        ]);
        $sql    = <<<SQL
            SELECT id, {$campos}
            FROM personas
            WHERE dni = :dni
SQL;
        $res    = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return static::arrayToObject();
    }

}