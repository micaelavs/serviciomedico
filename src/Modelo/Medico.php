<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;

class Medico extends Modelo {

	/**@var int */
    public $id;
	/**@var String**/
	Public $matricula;
	/**@var Varchar**/
	public $nombre;
	/**@var Varchar**/
	public $apellido;
	/**@var string */
    public $firma;
	/**@var int */
	public $borrado;

	static public $FLAG   = false;

	public static function obtener($id = null){
		$obj	= new static;
		if ($id === null) {
			return static::arrayToObject();
		}
		$sql_params	= [
			':id'	=> $id,
		];
		$campos	= implode(',', [
			'id', 'matricula', 'nombre', 'apellido','firma','borrado'
		]);
		$sql	= <<<SQL
			SELECT {$campos}
			FROM medicos
			WHERE id = :id
SQL;
		$res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
		if (!empty($res)) {
			return static::arrayToObject($res[0]);
		}
		return static::arrayToObject();
	}

	public static  function listar_medicos(){
        $sql_params	= [];
        $sql	= <<<SQL
		SELECT id,matricula,nombre,apellido FROM medicos WHERE borrado=0
SQL;
        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(empty($res)){
            return [];
        }
        return $res;
    }


	public function alta(){
		
		$this->upload_archivo();
		
		 $sql_params = [
            ':matricula'        => $this->matricula,
            ':nombre'           => $this->nombre,
            ':apellido'         => $this->apellido,
            ':firma'            => $this->firma['name']
        ];

		$sql	= 'INSERT INTO medicos(matricula,nombre,apellido,firma) VALUES (:matricula,:nombre,:apellido,:firma)';
		$query = new Conexiones();
		$res	= $query->consulta(Conexiones::INSERT, $sql, $sql_params);


		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Medico';
			Logger::event('alta', $datos);
		} else {
			$this->errores = $query->errorCode;
		}
		return $res;
	}

	public function modificacion()	{

		$this->upload_archivo();
		
		$sql_params = [
            ':matricula'        => $this->matricula,
            ':nombre'           => $this->nombre,
            ':apellido'         => $this->apellido,
            ':firma'            => $this->firma['name'],
            ':id'               => $this->id
        ];

		$sql	= 'UPDATE medicos SET matricula=:matricula, nombre=:nombre, apellido=:apellido, firma=:firma WHERE id=:id';
		$query = new Conexiones();
		$res	= $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Medico';
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
		UPDATE medicos SET  borrado = 1 WHERE id = :id
SQL;
		$res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
		if ($res !== false) {
			$datos = (array) $this;
			$datos['modelo'] = 'Medico';
			Logger::event('baja', $datos);
		} else {
			$datos['error_db'] = $conexion->errorInfo;
			Logger::event("error_baja", $datos);
		}
		return $res;
	}

	public function validar(){
		$campos = (array)$this;
		$rules = [
			'id'        => ['numeric'],
		   	'firma' => ['formatoValido' => function ($input) {
               if(!empty($input['name'])){
                        $doc_ingresado = explode('.',$input['name']);
                        $extension = $doc_ingresado[1];
                        switch ($extension) {
                            case 'jpg':
                                return true; 
                                break;
                             case 'jpeg':
                                return true;
                                break;
                            default;
                                return false;
                            break;    
                        }   
               }
               return true;
               
            }],
		];

		if(!empty($campos['matricula'])){
            $rules     += [
                'matricula'  => ['max_length(9)','numeric', 'UnicoRegistroActivo()' => function($input) use ($campos){
                    $where  = '';
                    $input      = trim($input);
                    $sql_params = [
                        ':matricula'           => '%'.$input.'%',
                        ':matricula_uppercase' => '%'.strtoupper($input).'%',
                        ':matricula_lowercase' => '%'.strtolower($input).'%',
                    ];
                    if(!empty($campos['id'])){
                        $where              = ' AND id != :id';
                        $sql_params[':id']  = $campos['id'];
                    }
                    $sql        = 'SELECT matricula FROM medicos WHERE (matricula LIKE :matricula OR matricula LIKE :matricula_uppercase OR matricula LIKE :matricula_lowercase) AND borrado = 0' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                 
                    return empty($resp);
            }]
            ];

        }else{
            $rules     += [
                'matricula'  => ['required']
            ];
        }
	
		if(!empty($this->nombre)){
			$rules += ['nombre' => ['texto',  'max_length(250)']];
		}else{
			$rules += ['nombre' => ['required']];
		}

		$nombres	= [
			'matricula' => 'Matricula',
			'nombre' 	=> 'Nombre',
			'apellido'  => 'Apellido'
		];

		if(!empty($this->apellido)){
			$rules += ['apellido' => ['texto',  'max_length(250)']];
		}else{
			$rules += ['apellido' => ['required']];
		}

		$nombres	= [
			'matricula' => 'Matricula',
			'nombre' 	=> 'Nombre',
			'apellido'  => 'Apellido'
		];

		$validator = Validator::validate((array)$this, $rules, $nombres);

		$validator->customErrors([
            'UnicoRegistroActivo()'   => ' Ya existe un Médico con la matrícula ingresada, por favor verifique.',
            'formatoValido' => 'El Archivo adjunto ingresado, no tiene el formato correcto, verifique que sea jpg.'

        ]);

		$validator->customErrors([
			'unica' => 'La Matricula no es unica, ya existe.'
		]);
		if ($validator->isSuccess()) {
			return true;
		} else {
			$this->errores = $validator->getErrors();
			return false;
		}
	}

	static public function arrayToObject($res = []){
		$campos	= [
			'id' =>  'int',
			'matricula' =>  'string',
			'nombre' => 'string',
			'apellido' 	=> 'string',
			'firma'		=>'string'
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

	 static public function lista_medicos() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, CONCAT(apellido,' ',nombre) as nombre, borrado
        FROM medicos
        WHERE borrado = 0
        ORDER BY id ASC"
    	);
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id'].'M'] = $value;
        }
        return $aux;
    }

     public static function obtener_por_nombre_si_borrado($matricula)
    {
        $obj = new static;
        if (is_null($matricula))
            return false;

        $sql_params = [':matricula' => $matricula,];
        $sql = <<<SQL
                    SELECT id, matricula
                    FROM medicos
                    WHERE matricula = :matricula
                    AND borrado = 1
SQL;
        $res = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(!empty($res)){
            return static::arrayToObject($res[0]);
        }
        return false;
    }

    public function reactivar(){
        $conexion = new Conexiones;
        $params = [':id' => $this->id];
        $sql = <<<SQL
		UPDATE medicos SET borrado = 0 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
        if ($res !== false)
        {
            $datos = (array)$this;
            $datos['modelo'] = 'Medico';
            Logger::event('reactivar', $datos);
            return true;
        }

        else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_reactivar", $datos);
        }
        return false;
    }

    protected function upload_archivo(){
                $rta = false;
                $directorio = BASE_PATH.'/uploads/firma';
                $name= '';
                $temp_name = '';
                $adjunto =$this->firma;
                $name = $adjunto['name'];
                $nombre_archivo = $name;
                $temp_name = $adjunto['tmp_name'];
                if(!is_dir($directorio)){
                    mkdir($directorio, 0777, true);
                }
                if(move_uploaded_file($temp_name, $directorio."/".$nombre_archivo)){
                    $adjunto['name'] = $nombre_archivo;
                    $rta = true; 
                }
              
            return $rta;
         
    }

    static public function obtener_firma_medico($id_interviniente = null){
        if($id_interviniente){
        $Conexiones = new Conexiones();
        $resultado = $Conexiones->consulta(Conexiones::SELECT,
<<<SQL

            SELECT  firma
            FROM medicos
            WHERE id= :id_interviniente
SQL
        ,[':id_interviniente'=>$id_interviniente]);
        return $resultado[0];
        }
        return null;
    }

}