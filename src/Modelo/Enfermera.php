<?php

namespace App\Modelo;

use App\Helper\Conexiones;
use App\Helper\Validator;
use FMT\Logger;

class Enfermera extends Modelo
{

    /** @var  int*/
    public $id;
    /** @var String */
    public $matricula;
    /** @var String */
    public $nombre;
    /** @var String*/
    public $apellido;
    /**@var string */
    public $firma;
    /** @var int */
    public $borrado;

    public static function obtener($id = null){
        $obj	= new static;
        if($id===null){
            return static::arrayToObject();
        }
        $sql_params	= [
            ':id'	=> $id,
        ];
        $campos	= implode(',', [
            'id','matricula','nombre','apellido','firma','borrado'
        ]);
        $sql	= <<<SQL
			SELECT {$campos}
			FROM enfermeras 
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
        $campos = (array)$this;
        $rules = [
            'id'        => ['numeric'],
            'nombre'=>['required','texto','max_length(250)'],
            'apellido'=>['required','texto','max_length(250)'],
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
                'matricula'  => ['max_length(8)','numeric', 'UnicoRegistroActivo()' => function($input) use ($campos){
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
                    $sql        = 'SELECT matricula FROM enfermeras WHERE (matricula LIKE :matricula OR matricula LIKE :matricula_uppercase OR matricula LIKE :matricula_lowercase) AND borrado = 0' . $where;
                    $resp   = (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
                 
                    return empty($resp);
            }]
            ];

        }else{
            $rules     += [
                'matricula'  => ['required']
            ];
        }

        $nombres	= [
            'matricula'=>'Matricula',
            'nombre'=>'Nombre',
            'apellido'=>'Apellido',

        ];

        $validator = Validator::validate((array)$this, $rules, $nombres);

         $validator->customErrors([
            'UnicoRegistroActivo()'   => ' Ya existe una Enfermera con la matrícula ingresada, por favor verifique.',
            'formatoValido' => 'El Archivo adjunto ingresado, no tiene el formato correcto, verifique que sea jpg.'
        ]);

        if ($validator->isSuccess()) {
            return true;
        }
        else {
            $this->errores = $validator->getErrors();
            return false;
        }
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

    public function alta()
    {
        $this->upload_archivo();
        
        $sql_params = [
            ':matricula'        => $this->matricula,
            ':nombre'           => $this->nombre,
            ':apellido'         => $this->apellido,
            ':firma'            => $this->firma['name']
        ];

        $sql	= 'INSERT INTO enfermeras(matricula,nombre,apellido,firma) VALUES (:matricula,:nombre,:apellido,:firma)';
        $query = new Conexiones();
        $res	= $query->consulta(Conexiones::INSERT, $sql, $sql_params);

        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Enfermeras';
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
		UPDATE enfermeras SET  borrado = 1 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Enfermera';
            Logger::event('baja', $datos);
        } else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_baja",$datos);
        }
        return $res;
    }

    public function modificacion()
    {

        $this->upload_archivo();

         $sql_params = [
            ':matricula'        => $this->matricula,
            ':nombre'           => $this->nombre,
            ':apellido'         => $this->apellido,
            ':firma'            => $this->firma['name'],
            ':id'               => $this->id
        ];

        $sql	= 'UPDATE enfermeras SET matricula=:matricula, nombre=:nombre, apellido=:apellido, firma=:firma WHERE id=:id';
        $query = new Conexiones();
        $res	= $query->consulta(Conexiones::UPDATE, $sql, $sql_params);

        if($res !== false){
            $datos = (array) $this;
            $datos['modelo'] = 'Enfermeras';
            Logger::event('modificacion', $datos);

        }else{
            $this->errores = $query->errorCode;
            return $this->errores;
        }
        return $res;
    }


    public static  function  listar_enfermeras(){

        $sql_params	= [
        ];

        $sql	= <<<SQL
		SELECT id,matricula,nombre,apellido FROM enfermeras WHERE borrado=0
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
            'matricula' =>  'string',
            'nombre'=>'string',
            'apellido'=>'string',
            'firma'=>'string'
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

     static public function lista_enfermeras() {
        $aux=[];
        $mbd = new Conexiones;
        $resultado = $mbd->consulta(Conexiones::SELECT,
        "SELECT
        id, CONCAT(apellido,' ',nombre) as nombre, borrado
        FROM enfermeras
        WHERE borrado = 0
        ORDER BY id ASC");
        if(empty($resultado)) { return []; }
        foreach ($resultado as $value) {
            $aux[$value['id'].'E'] = $value;
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
                    FROM enfermeras
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
		UPDATE enfermeras SET borrado = 0 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::SELECT, $sql, $params);
        if ($res !== false)
        {
            $datos = (array)$this;
            $datos['modelo'] = 'Enfermera';
            Logger::event('reactivar', $datos);
            return true;
        }

        else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_reactivar", $datos);
        }
        return false;
    }

    static public function obtener_firma_enfermera($id_interviniente = null){
        if($id_interviniente){
        $Conexiones = new Conexiones();
        $resultado = $Conexiones->consulta(Conexiones::SELECT,
<<<SQL

            SELECT  firma
            FROM enfermeras
            WHERE id= :id_interviniente
SQL
        ,[':id_interviniente'=>$id_interviniente]);
        return $resultado[0];
        }
        return null;
    }

}
