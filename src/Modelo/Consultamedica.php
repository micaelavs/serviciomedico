<?php
namespace App\Modelo;

use FMT\Logger;
use App\Helper\Validator;
use App\Helper\Conexiones;

class Consultamedica extends Modelo {

    const MEDICO = 1;
    const ENFERMERA = 2;

    static public $TIPO_INTERVINIENTE = [
        self::MEDICO   => ['id' => self::MEDICO, 'nombre' => 'Médico', 'borrado' => '0'],
        self::ENFERMERA   => ['id' => self::ENFERMERA, 'nombre' => 'Enfermera', 'borrado' => '0']
    ];

    /*para el correo*/
    const NO_ENVIADO = 0;
    const ENVIADO = 1;

	/**@var int */
    public $id;
    /**@var int */
    public $id_persona;
	/**@var int */
    public $id_estado;
    /**@var int */
    public $id_articulo;
    /**@var int */
    public $id_interviniente;
    /**@var int */
    public $tipo_interviniente;
    /**@var date */
    public $fecha_intervencion;
    /**@var date */
    public $fecha_desde;
    /**@var date */
    public $fecha_hasta;
    /**@var date */
    public $fecha_regreso_trabajo;
    /**@var date */
    public $fecha_nueva_revision;
    /**@var string */
    public $observacion;
    /**@var string */
    public $medico_tratante;
    /**@var int */
    public $telefono_contacto_tratante;
    /**@var array */
    public $adjuntos = [];
	/**@var int */
	public $borrado;


	public static function obtener($id = null){
		$esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;
		$obj	= new static;
		if ($id === null) {
			return static::arrayToObject();
		}
		$sql_params	= [
			':id'	=> $id,
		];

		$sql	= <<<SQL

			SELECT cm.id, cm.id_persona, cm.id_estado, cm.id_articulo, cm.id_interviniente, cm.tipo_interviniente, cm.fecha_intervencion, cm.fecha_desde, cm.fecha_hasta, cm.fecha_regreso_trabajo, cm.fecha_nueva_revision, cm.observacion, cm.medico_tratante, cm.telefono_contacto_tratante, cm.borrado
			FROM consultas_medicas cm
			INNER JOIN personas p ON
	        p.id = cm.id_persona
			INNER JOIN estados es ON
			es.id = cm.id_estado
			INNER JOIN articulos a ON
        	a.id = cm.id_articulo
        	INNER JOIN medicos m ON
       		(m.id = cm.id_interviniente AND cm.tipo_interviniente = $esMedico)
			WHERE cm.borrado = 0 AND m.borrado = 0 AND cm.id = :id
			UNION ALL

	       	SELECT cm.id, cm.id_persona, cm.id_estado, cm.id_articulo, cm.id_interviniente, cm.tipo_interviniente, cm.fecha_intervencion, cm.fecha_desde, cm.fecha_hasta, cm.fecha_regreso_trabajo, cm.fecha_nueva_revision, cm.observacion, cm.medico_tratante, cm.telefono_contacto_tratante, cm.borrado
			FROM consultas_medicas cm
            INNER JOIN personas p ON
	        p.id = cm.id_persona
	        INNER JOIN estados es ON
	        es.id = cm.id_estado
			INNER JOIN articulos a ON
	        a.id = cm.id_articulo
	        INNER JOIN enfermeras e ON
	       	(e.id = cm.id_interviniente AND cm.tipo_interviniente = $esEnfermera)
	       	WHERE cm.borrado = 0 AND e.borrado = 0 AND cm.id = :id
SQL;
		$res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
		if (!empty($res)) {
			return static::arrayToObject($res[0]);
		}
		return static::arrayToObject();
	}

	public function alta(){
        
        $this->upload_archivos();

        $sql_params = [
            ':id_persona'  => $this->id_persona,
            ':id_estado'   => $this->id_estado,
            ':id_articulo' => $this->id_articulo,
            ':id_interviniente' => $this->id_interviniente,
            ':tipo_interviniente' => $this->tipo_interviniente,
            ':observacion' => $this->observacion,
            ':medico_tratante' => $this->medico_tratante,
            ':telefono_contacto_tratante' => $this->telefono_contacto_tratante,        
        ];

        if($this->fecha_intervencion instanceof \DateTime){
             $sql_params[':fecha_intervencion'] = $this->fecha_intervencion->format('Y-m-d H:i');
        }else{
            $sql_params[':fecha_intervencion'] = $this->fecha_intervencion;
        }

        if($this->fecha_desde instanceof \DateTime){
             $sql_params[':fecha_desde'] = $this->fecha_desde->format('Y-m-d');
        }else{
            $sql_params[':fecha_desde'] = $this->fecha_desde;
        }

        if($this->fecha_hasta instanceof \DateTime){
             $sql_params[':fecha_hasta'] = $this->fecha_hasta->format('Y-m-d');
        }else{
            $sql_params[':fecha_hasta'] = $this->fecha_hasta;
        }

        if($this->fecha_regreso_trabajo instanceof \DateTime){
             $sql_params[':fecha_regreso_trabajo'] = $this->fecha_regreso_trabajo->format('Y-m-d');
        }else{
            $sql_params[':fecha_regreso_trabajo'] = $this->fecha_regreso_trabajo;
        }

         if($this->fecha_nueva_revision instanceof \DateTime){
             $sql_params[':fecha_nueva_revision'] = $this->fecha_nueva_revision->format('Y-m-d');
        }else{
            $sql_params[':fecha_nueva_revision'] = $this->fecha_nueva_revision;
        }
     
        $cnx = new Conexiones();
	    $sql = 'INSERT INTO consultas_medicas (id_persona, id_estado, id_articulo,id_interviniente, tipo_interviniente,fecha_intervencion,fecha_desde,fecha_hasta,fecha_regreso_trabajo, fecha_nueva_revision,observacion, medico_tratante, telefono_contacto_tratante) VALUES (:id_persona,:id_estado, :id_articulo, :id_interviniente, :tipo_interviniente,:fecha_intervencion, :fecha_desde,:fecha_hasta,:fecha_regreso_trabajo, :fecha_nueva_revision,:observacion,:medico_tratante,:telefono_contacto_tratante)';

		$res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);

        if($res !== false){
            $this->id = $res;
            $id_consulta = $this->id;
            foreach ($this->adjuntos as $archivo) {
                if(!empty($archivo['name'])){
                    self::archivos_alta($archivo['name'],$res);
                }
                
            }
            $datos = (array) $this;
            $datos['modelo'] = 'Consultamedica';
            Logger::event('alta', $datos);
        }
        return $res;
	
    }

    public function archivos_alta($nombre_archivo = null, $id_consulta = null){
        $cnx = new Conexiones();
        $sql_params = [
            ':nombre' => $nombre_archivo,
            ':id_consulta_medica' => $id_consulta
        ];
        $sql = 'INSERT INTO documentos_adjuntos (nombre, id_consulta_medica) VALUES (:nombre, :id_consulta_medica)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
        if($res !== false){
            $this->id = $res;
            $datos = (array) $this;
            $datos['modelo'] = 'Consultamedica';
            Logger::event('alta', $datos);
        }
        return $res;
    }

	public function modificacion()	{

        $this->upload_archivos();
		
        $campos	= [
			'id_persona'    => 'id_persona = :id_persona', 
            'id_estado'     => 'id_estado = :id_estado', 
            'id_articulo'   => 'id_articulo = :id_articulo', 
            'id_interviniente'  => 'id_interviniente = :id_interviniente',
            'tipo_interviniente'=> 'tipo_interviniente = :tipo_interviniente',
            'fecha_intervencion'=> 'fecha_intervencion = :fecha_intervencion',
            'fecha_desde'       => 'fecha_desde = :fecha_desde',
            'fecha_hasta'       => 'fecha_hasta = :fecha_hasta',
            'fecha_regreso_trabajo' => 'fecha_regreso_trabajo = :fecha_regreso_trabajo',
            'fecha_nueva_revision'  =>'fecha_nueva_revision = :fecha_nueva_revision',
            'observacion'           => 'observacion = :observacion',
            'medico_tratante'       =>'medico_tratante = :medico_tratante',
            'telefono_contacto_tratante' => 'telefono_contacto_tratante =:telefono_contacto_tratante'
		];

		$sql_params	= [
            ':id_persona'   => $this->id_persona,
            ':id_estado'    => $this->id_estado,
            ':id_articulo'  => $this->id_articulo,
            ':id_interviniente'     => $this->id_interviniente,
            ':tipo_interviniente'   => $this->tipo_interviniente,
            ':observacion'          => $this->observacion,
            ':medico_tratante'      => $this->medico_tratante,
            ':telefono_contacto_tratante' => $this->telefono_contacto_tratante,
			':id'	=> $this->id

		];
        
        if($this->fecha_intervencion instanceof \DateTime){
            $sql_params[':fecha_intervencion'] = $this->fecha_intervencion->format('Y-m-d H:i');
        }

        if($this->fecha_desde instanceof \DateTime){
            $sql_params[':fecha_desde'] = $this->fecha_desde->format('Y-m-d');
        }

        if($this->fecha_hasta instanceof \DateTime){
            $sql_params[':fecha_hasta'] = $this->fecha_hasta->format('Y-m-d');
        }

        if($this->fecha_regreso_trabajo instanceof \DateTime){
            $sql_params[':fecha_regreso_trabajo'] = $this->fecha_regreso_trabajo->format('Y-m-d');
        }else{
            $sql_params[':fecha_regreso_trabajo'] = null;
        }

        if($this->fecha_nueva_revision instanceof \DateTime){
            $sql_params[':fecha_nueva_revision'] = $this->fecha_nueva_revision->format('Y-m-d');
        }else{
            $sql_params[':fecha_nueva_revision'] = null;
        }

		$sql	= 'UPDATE consultas_medicas SET ' . implode(',', $campos) . ' WHERE id = :id';
		$cnx = new Conexiones();
		$res = $cnx->consulta(Conexiones::UPDATE, $sql, $sql_params);
       // if($res !== false){
        if(!empty($this->adjuntos)){
            foreach ($this->adjuntos as $archivo) {
                if(!empty($archivo['name'])){
                    self::archivos_alta($archivo['name'], $this->id);
                }
                
            }
        }
            
        $datos = (array) $this;
        $datos['modelo'] = 'Consultamedica';
        Logger::event('modificacion', $datos);
        return true;
        //}
        return false;
	}

	public function baja(){	
        $conexion = new Conexiones;
		$params = [':id' => $this->id];
		$sql = <<<SQL
		UPDATE consultas_medicas SET  borrado = 1 WHERE id = :id
SQL;
		$res = $conexion->consulta(Conexiones::UPDATE, $sql, $params);
		if ($res !== false) {
            if(!empty(self::traer_archivos_adjuntos($this->id))){
                self::eliminar_archivos($this->id);
            }
			$datos = (array) $this;
			$datos['modelo'] = 'Consultamedica';
			Logger::event('baja', $datos);
		} else {
			$datos['error_db'] = $conexion->errorInfo;
			Logger::event("error_baja", $datos);
		}
		return $res;
	}


    public function traer_archivos_adjuntos($id_consulta_medica= null){
    
        if($id_consulta_medica){
        $Conexiones = new Conexiones();
        $resultado = $Conexiones->consulta(Conexiones::SELECT,
<<<SQL

            SELECT  id, nombre, id_consulta_medica
            FROM documentos_adjuntos
            WHERE id_consulta_medica= :id_consulta_medica
SQL
        ,[':id_consulta_medica'=> $id_consulta_medica]);
        return $resultado;
        }

    }

    public function eliminar_archivos($id_consulta_medica=null){
        $conexion = new Conexiones;
        $params = [':id_consulta_medica' => $id_consulta_medica];
        $sql = <<<SQL
        UPDATE documentos_adjuntos SET borrado = 1 WHERE id_consulta_medica = :id_consulta_medica
SQL;
        $res = $conexion->consulta(Conexiones::UPDATE, $sql, $params);
       
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Consultamedica';
            Logger::event('baja', $datos);
        } else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_baja", $datos);
        }
        return $res;
    }



	public function validar(){
		$rules = [
			'id'      		=> ['numeric'],
            'adjuntos'      => ['formatoValido' => function ($input) {
                   if(!empty($input[0]['name'])){
                        foreach ($input as $key => $archivo) {
                            $doc_ingresado = explode('.',$archivo['name']);
                            $extension = $doc_ingresado[1];
                            switch ($extension) {
                                case 'jpg':
                                    return true; 
                                    break;
                                 case 'jpeg':
                                    return true;
                                    break;
                                case 'pdf':
                                    return true;
                                    break;
                                default;
                                    return false;
                                break;    
                            }   
                        }
                   }
                   return true;
                   
                }],
			'id_persona' 	=> ['required', 'numeric'],
			'id_estado' 	=> ['required', 'numeric'],
			'id_articulo' 	=> ['required', 'numeric'],
			'id_interviniente' 	=> ['required'],
			'fecha_intervencion' => ['required', 'fecha'],
			'fecha_desde' => ['required', 'fecha'],
			'fecha_hasta' => ['required', 'fecha','despuesDe(:fecha_desde)'],
			'fecha_regreso_trabajo' => ['fecha','despuesDe(:fecha_hasta)'],
			'fecha_nueva_revision' => ['fecha'],
			'observacion'	 => ['texto', 'max_length(1000)'],
			'medico_tratante'  => ['texto', 'max_length(200)'],
			'telefono_contacto_tratante'  => ['numeric', 'max_length(20)'],
		];

		$nombres	= [
			'id_persona' 		=> 'DNI / Nombre y Apellido de la persona',
			'id_estado'  		=> 'Estado',
			'id_articulo'		=> 'Artículo',
			'id_interviniente'	=> 'Inerviniente',
			'fecha_intervencion'=> 'Fecha de intervención',
			'fecha_desde' 		=> 'Fecha desde',
			'fecha_hasta' 		=> 'Fecha hasta',
			'fecha_regreso_trabajo'	=> 'Fecha regreso al trabajo',
			'fecha_nueva_revision' 	=> 'Fecha nueva revisión',
			'observacion'			=> 'Observación',
			'medico_tratante'		=> 'Médico tratante',
			'telefono_contacto_tratante' => 'Teléfono de contacto de tratante',
            'adjuntos'                   => 'Archivo adjunto'
		];

		$validator = Validator::validate((array)$this, $rules, $nombres);
        $validator->customErrors([
            'formatoValido' => 'Al menos un Archivo adjunto ingresado, no tiene el formato correcto, verifique que sea, jpg, jpeg o pdf.'

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
			'id_persona' => 'int',
			'id_estado' => 'int',
			'id_articulo' => 'int',
			'id_interviniente' => 'int',
			'tipo_interviniente' => 'int',
			'fecha_intervencion' => 'datetime',
			'fecha_desde' => 'date',
			'fecha_hasta' => 'date',
			'fecha_regreso_trabajo' => 'date',
			'fecha_nueva_revision'	=> 'date',
			'observacion'			=> 'string',
			'medico_tratante'		=> 'string',
			'telefono_contacto_tratante' => 'int',
            'adjuntos'                   => 'array'
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

	public static function listar_consultas_medicas($params = array())
    {
        $campos    = 'id, dni, cuit, apellido_nombre, estado, articulo, nombre_interviniente, fecha_intervencion, fecha_desde, fecha_hasta';
        $sql_params = [];
        $where = [];

        $condicion1 = "AND c_m.borrado = 0 AND m.borrado = 0";
        $condicion2 = "AND c_m.borrado = 0 AND e.borrado = 0";

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $default_params = [
            'filtros'   => [
                'dni'         		=> null,
                'fecha_intervencion'=> null,
                'id_estado'  		=> null,
                'id_articulo'		=> null,
                'fecha_desde'       => null,
                'fecha_hasta'       => null

            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        /*Filtros */
        if(!empty($params['filtros']['dni'])){
            $where [] = "p.dni = :dni";
            $sql_params[':dni']    = $params['filtros']['dni'];

        }

        if(!empty($params['filtros']['id_estado'])){
            $where [] = "c_m.id_estado = :id_estado";
            $sql_params[':id_estado']    = $params['filtros']['id_estado'];

        }

        if(!empty($params['filtros']['id_articulo'])){
            $where [] = "c_m.id_articulo = :id_articulo";
            $sql_params[':id_articulo']    = $params['filtros']['id_articulo'];

        }


        if(!empty($params['filtros']['fecha_intervencion'])){
            $where [] = "c_m.fecha_intervencion like :fecha_intervencion";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_intervencion'])->format('Y-m-d');
            $sql_params[':fecha_intervencion']    = $fecha.'%';

        }
    
        if(!empty($params['filtros']['fecha_desde'])){
            $where [] = "c_m.fecha_desde >= :fecha_desde";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_desde'])->format('Y-m-d');
            $sql_params[':fecha_desde']    = $fecha;

        }

        if(!empty($params['filtros']['fecha_hasta'])){
            $where [] = "c_m.fecha_hasta <= :fecha_hasta";
            $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_hasta'])->format('Y-m-d');
            $sql_params[':fecha_hasta']    = $fecha;

        }

        $condicion1 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';
        $condicion2 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';

		$esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;

         if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (c_m.id like :search{$indice} OR p.dni like :search{$indice} OR p.cuit like :search{$indice} OR p.apellido_nombre like :search{$indice} OR a.nombre like :search{$indice} OR es.estado like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        }

        $consulta = <<<SQL
     	SELECT c_m.id, p.dni, p.cuit, p.apellido_nombre, es.estado, a.nombre as articulo, concat(m.nombre,' ', m.apellido) as nombre_interviniente, c_m.fecha_intervencion as fecha_intervencion, CONCAT(c_m.fecha_desde, ' 0:00:00') as fecha_desde, CONCAT(c_m.fecha_hasta, '0:00:00') as fecha_hasta
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id = c_m.id_persona
		INNER JOIN estados es ON
        es.id = c_m.id_estado
        INNER JOIN articulos a ON
        a.id = c_m.id_articulo
        INNER JOIN medicos m ON
       	(m.id = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico)
       	$condicion1
        UNION ALL
        SELECT c_m.id, p.dni, p.cuit, p.apellido_nombre, es.estado, a.nombre as articulo, concat(e.nombre,' ', e.apellido) as nombre_interviniente, c_m.fecha_intervencion as fecha_intervencion, CONCAT(c_m.fecha_desde, ' 0:00:00') as fecha_desde, CONCAT(c_m.fecha_hasta, '0:00:00') as fecha_hasta
        FROM consultas_medicas c_m
		INNER JOIN personas p ON
        p.id = c_m.id_persona
        INNER JOIN estados es ON
        es.id = c_m.id_estado
		INNER JOIN articulos a ON
        a.id = c_m.id_articulo
        INNER JOIN enfermeras e ON
       	(e.id = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera)
        $condicion2
SQL;
        
        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        return $data;
    }



    public static function listar_consultas_medicas_excel($params){
        $cero_negativo =  \App\Modelo\Persona::CERO_NEGATIVO;
        $cero_positivo = \App\Modelo\Persona::CERO_POSITIVO;
        $a_negativo    = \App\Modelo\Persona::A_NEGATIVO;
        $a_positivo    = \App\Modelo\Persona::A_POSITIVO;
        $b_positivo    = \App\Modelo\Persona::B_POSITIVO;
        $b_negativo    = \App\Modelo\Persona::B_NEGATIVO;
        $ab_negativo   = \App\Modelo\Persona::AB_NEGATIVO;
        $ab_positivo   = \App\Modelo\Persona::AB_POSITIVO;

        $apto_si = \App\Modelo\Persona::APTO_SI;
        $apto_no = \App\Modelo\Persona::APTO_NO;

        $tipo_apto_a = \App\Modelo\Persona::A;
        $tipo_apto_b = \App\Modelo\Persona::B_C_PREEXISTENCIA;
        $tipo_apto_c = \App\Modelo\Persona::C_C_PREEXISTENCIA;
        $tipo_apto_d = \App\Modelo\Persona::D_C_PREEXISTENCIA;
        $tipo_no_apto = \App\Modelo\Persona::NO_APTO;

        $cnx    = new Conexiones();
        $sql_params = [];
        $where = [];
        $condicion1 = '';
        $condicion2 = '';
        $order = '';
        $search = [];

        $esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [
                'dni'         		=> null,
                'fecha_intervencion'=> null,
                'id_estado'  		=> null,
                'id_articulo'		=> null,
                'fecha_desde'       => null,
                'fecha_hasta'       => null

            ],
            'count'     => false
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        $sql1= <<<SQL
      	SELECT c_m.id, es.estado, c_m.fecha_intervencion, a.nombre as articulo, p.dni,p.cuit, p.apellido_nombre, p.fecha_nacimiento, IF(p.grupo_sanguineo = $cero_negativo,"0-", IF(p.grupo_sanguineo = $cero_positivo, "0+", IF(p.grupo_sanguineo = $a_negativo, "A-", IF(p.grupo_sanguineo = $a_positivo, "A+", IF(p.grupo_sanguineo = $b_positivo, "B+", IF(p.grupo_sanguineo = $b_negativo, "B-", IF(p.grupo_sanguineo = $ab_negativo, "AB-", IF(p.grupo_sanguineo = $ab_positivo, "AB+", "")))))))) as grupo_sanguineo , p.modalidad_vinculacion, IF(p.apto=$apto_si, "SÍ", IF(p.apto=$apto_no, "NO", "")) as apto, IF(p.tipo_apto = $tipo_apto_a, "A", IF(p.tipo_apto = $tipo_apto_b, "B c/ preexistencia", IF(p.tipo_apto = $tipo_apto_c, "C c/ preexistencia", IF(p.tipo_apto = $tipo_apto_d, "D c/ preexistencia", IF(p.tipo_apto = $tipo_no_apto, "No apto", ""))))) as tipo_apto, p.fecha_apto, concat(m.nombre,' ', m.apellido) as nombre_interviniente, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo, c_m.fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos  
SQL;


        $sql2= <<<SQL
      	SELECT c_m.id, es.estado, c_m.fecha_intervencion, a.nombre as articulo, p.dni, p.cuit, p.apellido_nombre, p.fecha_nacimiento, IF(p.grupo_sanguineo = $cero_negativo,"0-", IF(p.grupo_sanguineo = $cero_positivo, "0+", IF(p.grupo_sanguineo = $a_negativo, "A-", IF(p.grupo_sanguineo = $a_positivo, "A+", IF(p.grupo_sanguineo = $b_positivo, "B+", IF(p.grupo_sanguineo = $b_negativo, "B-", IF(p.grupo_sanguineo = $ab_negativo, "AB-", IF(p.grupo_sanguineo = $ab_positivo, "AB+", "")))))))) as grupo_sanguineo, p.modalidad_vinculacion, IF(p.apto=$apto_si, "SÍ", IF(p.apto=$apto_no, "NO", "")) as apto, IF(p.tipo_apto = $tipo_apto_a, "A", IF(p.tipo_apto = $tipo_apto_b, "B c/ preexistencia", IF(p.tipo_apto = $tipo_apto_c, "C c/ preexistencia", IF(p.tipo_apto = $tipo_apto_d, "D c/ preexistencia", IF(p.tipo_apto = $tipo_no_apto, "No apto", ""))))) as tipo_apto, p.fecha_apto, concat(e.nombre,' ', e.apellido) as nombre_interviniente, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo, c_m.fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos
SQL;


    $from1 = <<<SQL
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id = c_m.id_persona
		INNER JOIN estados es ON
        es.id = c_m.id_estado
        INNER JOIN articulos a ON
        a.id = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id AND d.borrado = 0)
        INNER JOIN medicos m ON
       	(m.id = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico)
SQL;

 	$from2 = <<<SQL
        FROM consultas_medicas c_m
		INNER JOIN personas p ON
        p.id = c_m.id_persona
        INNER JOIN estados es ON
        es.id = c_m.id_estado
		INNER JOIN articulos a ON
        a.id = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id AND d.borrado = 0)
        INNER JOIN enfermeras e ON
       	(e.id = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera)
SQL;

    $condicion1 = <<<SQL
        WHERE
        c_m.borrado = 0 AND m.borrado = 0
SQL;

    $condicion2 = <<<SQL
        WHERE
        c_m.borrado = 0 AND e.borrado = 0
SQL;

    $group = <<<SQL
        GROUP BY c_m.id, c_m.id_persona
SQL;

    /**Filtros para la consulta 1*/

    if(!empty($params['filtros']['dni'])){
        $condicion1 .= " AND p.dni = :dni";
        $sql_params[':dni']    = $params['filtros']['dni'];
    }

    if(!empty($params['filtros']['id_estado'])){
        $condicion1 .= " AND c_m.id_estado = :id_estado";
        $sql_params[':id_estado']   = $params['filtros']['id_estado'];
    }

 	if(!empty($params['filtros']['id_articulo'])){
        $condicion1 .= " AND c_m.id_articulo = :id_articulo";
        $sql_params[':id_articulo']   = $params['filtros']['id_articulo'];
    }

    if(!empty($params['filtros']['fecha_intervencion'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_intervencion'])->format('Y-m-d');
        $condicion1 .=  " AND c_m.fecha_intervencion like :fecha_intervencion";
        $sql_params[':fecha_intervencion']   = $fecha.'%';
    }

    if(!empty($params['filtros']['fecha_desde'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_desde'])->format('Y-m-d');
        $condicion1 .=  " AND c_m.fecha_desde >= :fecha_desde";
        $sql_params[':fecha_desde']   = $fecha;
    }

     if(!empty($params['filtros']['fecha_hasta'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_hasta'])->format('Y-m-d');
        $condicion1 .=  " AND c_m.fecha_hasta <= :fecha_hasta";
        $sql_params[':fecha_hasta']   = $fecha;
    }


    /**Filtros para la consulta 2*/

    if(!empty($params['filtros']['dni'])){
        $condicion2 .= " AND p.dni = :dni";
        $sql_params[':dni']    = $params['filtros']['dni'];
    }

    if(!empty($params['filtros']['id_estado'])){
        $condicion2 .= " AND c_m.id_estado = :id_estado";
        $sql_params[':id_estado']   = $params['filtros']['id_estado'];
    }

 	if(!empty($params['filtros']['id_articulo'])){
        $condicion2 .= " AND c_m.id_articulo = :id_articulo";
        $sql_params[':id_articulo']   = $params['filtros']['id_articulo'];
    }

    if(!empty($params['filtros']['fecha_intervencion'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_intervencion'])->format('Y-m-d');
        $condicion2 .=  " AND c_m.fecha_intervencion like :fecha_intervencion";
        $sql_params[':fecha_intervencion']   = $fecha.'%';
    }

    if(!empty($params['filtros']['fecha_desde'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_desde'])->format('Y-m-d');
        $condicion2 .=  " AND c_m.fecha_desde >= :fecha_desde";
        $sql_params[':fecha_desde']   = $fecha;
    }

     if(!empty($params['filtros']['fecha_hasta'])){
        $fecha = \DateTime::createFromFormat('d/m/Y', $params['filtros']['fecha_hasta'])->format('Y-m-d');
        $condicion2 .=  " AND c_m.fecha_hasta <= :fecha_hasta";
        $sql_params[':fecha_hasta']   = $fecha;
    }

    $counter_query  = "SELECT COUNT(c_m.id) AS total {$from1}";

    $counter_query2  = "SELECT COUNT(c_m.id) AS total {$from2}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion1 . $group, $sql_params )[0]['total'];

    $recordsTotal2   =  $cnx->consulta(Conexiones::SELECT, $counter_query2 . $condicion2 . $group, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (c_m.id like :search{$indice} OR p.dni like :search{$indice} OR p.cuit like :search{$indice} OR p.apellido_nombre like :search{$indice} OR a.nombre like :search{$indice} OR es.estado like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        
        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }

        $order .= implode(',', $orderna);

        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion1.$group, $sql_params)[0]['total'];

        $recordsFiltered2= $cnx->consulta(Conexiones::SELECT, $counter_query2.$condicion2.$group, $sql_params)[0]['total'];

       	$order .= (($order =='') ? '' : ', ').'c_m.fecha_intervencion desc';

        $order = ' ORDER BY '.$order;

        $lista1 = $cnx->consulta(Conexiones::SELECT,  $sql1 .$from1.$condicion1.$group.$order.$limit,$sql_params);

        $lista2 = $cnx->consulta(Conexiones::SELECT,  $sql2 .$from2.$condicion2.$group.$order.$limit,$sql_params);

        $result = array_merge($lista1, $lista2);

        return ($result) ? $result : [];
    }

    protected function upload_archivos(){
                $rta = false;
                $date_time = gmdate('YmdHis');
                $directorio = BASE_PATH.'/uploads/intervencion';
                $name= '';
                $temp_name = '';
                
                foreach ($this->adjuntos as $clave => $valor) {
                    $name = $valor['name'];
                    $nombre_archivo = $date_time.'_'.$name;
                    $temp_name = $valor['tmp_name'];
                    if(!is_dir($directorio)){
                        mkdir($directorio, 0777, true);
                    }
                    if(move_uploaded_file($temp_name, $directorio."/".$nombre_archivo)){
                        $this->adjuntos[$clave]['name'] = $nombre_archivo;
                        $rta = true; 
                    }

                }
              return $rta;
         
    }

    public static function listar_archivos_adjuntos($params = array())
    {   $id_consulta = $params['id_consulta'];
        $campos    = 'id ,nombre, fecha_alta_operacion';
        $sql_params = [];

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $consulta = <<<SQL
        SELECT d.id, d.nombre, d.fecha_alta_operacion
        FROM documentos_adjuntos d
        INNER JOIN consultas_medicas c ON
        c.id = d.id_consulta_medica
        WHERE c.borrado = 0 AND d.borrado = 0 AND c.id = $id_consulta
SQL;

        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        foreach ($data['data'] as $key => $value) {
            $result = preg_replace("/\d{14}_/", "", $value->nombre); 
            $value->nombre = null;
            $value->nombre = $result;
               
        }
     
        return $data;
    }

    public static function listar_historia_clinica($params = array())
    { 
        $dni = $params['dni'];
        $campos    = 'id, numero_consulta, tipo_operacion, fecha_operacion, fecha_intervencion, articulo, estado, interviniente, fecha_desde, fecha_hasta, fecha_regreso_trabajo, fecha_nueva_revision, observacion, doc_adjuntos';
        $sql_params = [];
        $where = [];
        $condicion1 = "AND c_m.borrado = 0 AND m.borrado = 0";
        $condicion2 = "AND c_m.borrado = 0 AND en.borrado = 0"; 
        
        $esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $default_params = [
            'filtros'   => [
                'id_estado'         => null,
                'id_articulo'       => null,
                'id_interviniente'  => null
            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

         /*Filtros */
        if(!empty($params['filtros']['id_estado'])){
            $where [] = "c_m.id_estado = :id_estado";
            $sql_params[':id_estado']    = $params['filtros']['id_estado'];
        
        }

        if(!empty($params['filtros']['id_articulo'])){
            $where [] = "c_m.id_articulo = :id_articulo";
            $sql_params[':id_articulo']    = $params['filtros']['id_articulo'];
        
        }


        if(!empty($params['filtros']['id_interviniente'])){
                $id_interviniente = (int)substr($params['filtros']['id_interviniente'], 0, 1);
                $tipo = substr($params['filtros']['id_interviniente'], 1, 1);
                
                if($tipo=='M'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::MEDICO;
                }else if($tipo=='E'){
                    $tipo_interviniente = \App\Modelo\Consultamedica::ENFERMERA;
                }

            $where [] = "(c_m.id_interviniente= :id_interviniente AND c_m.tipo_interviniente = :tipo_interviniente)";
            
            $sql_params[':id_interviniente']   = $id_interviniente;   
            $sql_params[':tipo_interviniente'] = $tipo_interviniente;   

            }
        
        $condicion1 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';   
        $condicion2 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';  

         if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (e.estado like :search{$indice} OR a.nombre like :search{$indice} OR c_m.fecha_operacion like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice} OR c_m.fecha_regreso_trabajo like :search{$indice} OR c_m.fecha_nueva_revision like :search{$indice} OR c_m.id_consulta_medica like :search{$indice} OR c_m.tipo_operacion like :search{$indice} ) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        }
        
        $consulta = <<<SQL
        SELECT c_m.id, c_m.id_consulta_medica as numero_consulta, IF(c_m.tipo_operacion="A","ALTA","MODIFICACIÓN") as tipo_operacion, c_m.fecha_operacion, c_m.fecha_intervencion, a.nombre as articulo, e.estado,  concat(m.nombre,' ', m.apellido) as interviniente, CONCAT(c_m.fecha_desde, '0:00:00') as fecha_desde, CONCAT(c_m.fecha_hasta, '0:00:00') as fecha_hasta, CONCAT(c_m.fecha_regreso_trabajo, '0:00:00') as fecha_regreso_trabajo, CONCAT(c_m.fecha_nueva_revision, '0:00:00') as fecha_nueva_revision, c_m.observacion, group_concat(DISTINCT d.nombre) as doc_adjuntos
        FROM consultas_medicas c_m
        INNER JOIN articulos a ON
        c_m.id_articulo = a.id_articulo
        INNER JOIN estados e ON
        e.id_estado = c_m.id_estado
        INNER JOIN medicos m ON
        (m.id_medico = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico) 
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        AND p.dni = $dni
        $condicion1
        GROUP BY c_m.id, c_m.id_persona
        UNION ALL
        SELECT c_m.id, c_m.id_consulta_medica as numero_consulta, IF(c_m.tipo_operacion="A","ALTA","MODIFICACIÓN") as tipo_operacion, c_m.fecha_operacion, c_m.fecha_intervencion, a.nombre as articulo, e.estado,  concat(en.nombre,' ', en.apellido) as interviniente, CONCAT(c_m.fecha_desde, '0:00:00') as fecha_desde, CONCAT(c_m.fecha_hasta, '0:00:00') as fecha_hasta, CONCAT(c_m.fecha_regreso_trabajo, '0:00:00') as fecha_regreso_trabajo, CONCAT(c_m.fecha_nueva_revision, '0:00:00') as fecha_nueva_revision, c_m.observacion, group_concat(DISTINCT d.nombre) as doc_adjuntos
        FROM consultas_medicas c_m
        INNER JOIN articulos a ON
        c_m.id_articulo = a.id_articulo
        INNER JOIN estados e ON
        e.id_estado = c_m.id_estado
        INNER JOIN enfermeras en ON
        (en.id_enfermera = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera) 
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        AND p.dni = $dni
        $condicion2
        GROUP BY c_m.id, c_m.id_persona

SQL;    
        $data = self::listadoAjax_log($campos, $consulta, $params, $sql_params);
   
        foreach ($data['data'] as $key => $value) {
            $result = preg_replace("/\d{14}_/", "", $value->doc_adjuntos); 
            $value->doc_adjuntos = null;
            $value->doc_adjuntos = $result;
               
        }
     
        return $data;
    }

    public static function listar_historia_clinica_excel($params){
        $dni = $params['dni'];
        $cnx    = new Conexiones('db_log');
        $sql_params = [];
        $where = [];
        $condicion1 = '';
        $condicion2 = '';
        $order = '';
        $search = [];
        $group ='';

        $esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [
                'id_estado'         => null,
                'id_articulo'       => null,
                'id_interviniente'  => null,
            ],
            'count'     => false
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        $sql1= <<<SQL
        SELECT c_m.id, c_m.id_consulta_medica as numero_consulta, p.apellido_nombre, p.dni, IF(c_m.tipo_operacion="A","ALTA","MODIFICACIÓN") as tipo_operacion, c_m.fecha_operacion, c_m.fecha_intervencion, a.nombre as articulo, e.estado,  concat(m.nombre,' ', m.apellido) as interviniente, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo as fecha_regreso_trabajo, c_m.fecha_nueva_revision, c_m.observacion, group_concat(DISTINCT d.nombre) as doc_adjuntos
SQL;

    
        $sql2= <<<SQL
        SELECT c_m.id, c_m.id_consulta_medica as numero_consulta, p.apellido_nombre, p.dni, IF(c_m.tipo_operacion="A","ALTA","MODIFICACIÓN") as tipo_operacion, c_m.fecha_operacion, c_m.fecha_intervencion, a.nombre as articulo, e.estado,  concat(en.nombre,' ', en.apellido) as interviniente, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo, c_m.fecha_nueva_revision, c_m.observacion, group_concat(DISTINCT d.nombre) as doc_adjuntos
SQL;


    $from1 = <<<SQL
        FROM consultas_medicas c_m
        INNER JOIN articulos a ON
        c_m.id_articulo = a.id_articulo
        INNER JOIN estados e ON
        e.id_estado = c_m.id_estado
        INNER JOIN medicos m ON
        (m.id_medico = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico) 
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        AND p.dni = $dni

SQL;

    $from2 = <<<SQL
        FROM consultas_medicas c_m
        INNER JOIN articulos a ON
        c_m.id_articulo = a.id_articulo
        INNER JOIN estados e ON
        e.id_estado = c_m.id_estado
        INNER JOIN enfermeras en ON
        (en.id_enfermera = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera) 
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        AND p.dni = $dni
SQL;
  
    $condicion1 = <<<SQL
        WHERE 
        c_m.borrado = 0 AND m.borrado = 0
SQL;

  $condicion2 = <<<SQL
        WHERE 
        c_m.borrado = 0 AND en.borrado = 0
SQL;

    $group = <<<SQL
        GROUP BY c_m.id, c_m.id_persona
SQL;

    /**Filtros para la consulta 1*/
    if(!empty($params['filtros']['id_estado'])){
        $condicion1 .= " AND c_m.id_estado = :id_estado";
        $sql_params[':id_estado']   = $params['filtros']['id_estado'];
    }

    if(!empty($params['filtros']['id_articulo'])){
        $condicion1 .= " AND c_m.id_articulo = :id_articulo";
        $sql_params[':id_articulo']   = $params['filtros']['id_articulo'];
    }
     if(!empty($params['filtros']['id_interviniente'])){
        $id_interviniente = (int)substr($params['filtros']['id_interviniente'], 0, 1);
        $tipo = substr($params['filtros']['id_interviniente'], 1, 1);
        
        if($tipo=='M'){
            $tipo_interviniente = \App\Modelo\Consultamedica::MEDICO;
        }else if($tipo=='E'){
            $tipo_interviniente = \App\Modelo\Consultamedica::ENFERMERA;
        }

        $condicion1 .= " AND (c_m.id_interviniente= :id_interviniente AND c_m.tipo_interviniente = :tipo_interviniente)";
        
        $sql_params[':id_interviniente']   = $id_interviniente;   
        $sql_params[':tipo_interviniente'] = $tipo_interviniente;   

        }
        
   
    /**Filtros para la consulta 2*/
    if(!empty($params['filtros']['id_estado'])){
        $condicion2 .= " AND c_m.id_estado = :id_estado";
        $sql_params[':id_estado']   = $params['filtros']['id_estado'];
    }

    if(!empty($params['filtros']['id_articulo'])){
        $condicion2 .= " AND c_m.id_articulo = :id_articulo";
        $sql_params[':id_articulo']   = $params['filtros']['id_articulo'];
    }
     if(!empty($params['filtros']['id_interviniente'])){
        $id_interviniente = (int)substr($params['filtros']['id_interviniente'], 0, 1);
        $tipo = substr($params['filtros']['id_interviniente'], 1, 1);
        
        if($tipo=='M'){
            $tipo_interviniente = \App\Modelo\Consultamedica::MEDICO;
        }else if($tipo=='E'){
            $tipo_interviniente = \App\Modelo\Consultamedica::ENFERMERA;
        }

        $condicion2 .= " AND (c_m.id_interviniente= :id_interviniente AND c_m.tipo_interviniente = :tipo_interviniente)";
        
        $sql_params[':id_interviniente']   = $id_interviniente;   
        $sql_params[':tipo_interviniente'] = $tipo_interviniente;   

        }

        $counter_query  = "SELECT COUNT(c_m.id) AS total {$from1}";

        $counter_query2  = "SELECT COUNT(c_m.id) AS total {$from2}";

        $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion1 . $group, $sql_params )[0]['total'];

        $recordsTotal2   =  $cnx->consulta(Conexiones::SELECT, $counter_query2 . $condicion2. $group, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (e.estado like :search{$indice} OR a.nombre like :search{$indice} OR c_m.fecha_operacion like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice} OR c_m.fecha_regreso_trabajo like :search{$indice} OR c_m.fecha_nueva_revision like :search{$indice} OR c_m.id_consulta_medica like :search{$indice} OR c_m.tipo_operacion like :search{$indice} ) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }
      
        $order .= implode(',', $orderna);
      
        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion1.$group, $sql_params)[0]['total'];

        $recordsFiltered2= $cnx->consulta(Conexiones::SELECT, $counter_query2.$condicion2.$group, $sql_params)[0]['total'];
       
        $order .= (($order =='') ? '' : ', ').'c_m.id_consulta_medica desc, c_m.fecha_operacion desc'; 
        
        $order = ' ORDER BY '.$order;

        $lista1 = $cnx->consulta(Conexiones::SELECT,  $sql1 .$from1.$condicion1.$group.$order.$limit,$sql_params);
       

        $lista2 = $cnx->consulta(Conexiones::SELECT,  $sql2 .$from2.$condicion2.$group.$order.$limit,$sql_params);
     
        $result = array_merge($lista1, $lista2);
     
        return ($result) ? $result : [];
    }


    static public function obtener_archivo($id_archivo=null){
    
        if($id_archivo){
        $Conexiones = new Conexiones();
        $resultado = $Conexiones->consulta(Conexiones::SELECT,
<<<SQL

            SELECT  id, nombre, id_consulta_medica
            FROM documentos_adjuntos
            WHERE id= :id_archivo
            LIMIT 1
SQL
        ,[':id_archivo'=>$id_archivo]);
        return $resultado[0];
        }

    }

    public function baja_archivo($id_archivo=null){
        $conexion = new Conexiones;
        $params = [':id' => $id_archivo];
        $sql = <<<SQL
        UPDATE documentos_adjuntos SET borrado = 1 WHERE id = :id
SQL;
        $res = $conexion->consulta(Conexiones::UPDATE, $sql, $params);
       
        if ($res !== false) {
            $datos = (array) $this;
            $datos['modelo'] = 'Consultamedica';
            Logger::event('baja', $datos);
        } else {
            $datos['error_db'] = $conexion->errorInfo;
            Logger::event("error_baja", $datos);
        }
        return $res;
    }
       
    public static  function get_resumen_consultas_medicas_empleados(){
        $sql_params	= [
        ];

        $sql	= <<<SQL
	SELECT consultas.id, consultas.id_articulo, p.dni, p.cuit,p.apellido_nombre as nombre_apellido ,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , YEAR(consultas.fecha_hasta) AS periodo
	FROM consultas_medicas as consultas 
    INNER JOIN articulos as a ON a.id = consultas.id_articulo
    INNER JOIN estados as e ON e.id =consultas.id_estado
    INNER join personas p on p.id = consultas.id_persona 
    WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=4 GROUP BY consultas.id_persona ,consultas.id_articulo,a.periodo_norma, YEAR (consultas.fecha_hasta) 
     UNION
SELECT consultas.id, consultas.id_articulo, p.dni, p.cuit,p.apellido_nombre as nombre_apellido,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , 
	CONCAT('I SEMESTRE', ' ', YEAR(consultas.fecha_hasta)) AS  periodo
	FROM consultas_medicas as consultas 
    INNER JOIN articulos as a ON a.id = consultas.id_articulo
    INNER JOIN estados as e ON e.id =consultas.id_estado
    INNER join personas p on p.id = consultas.id_persona 
    WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=3 AND MONTH(consultas.fecha_hasta) >=01  AND MONTH(consultas.fecha_hasta) <=06 GROUP BY consultas.id_persona ,consultas.id_articulo,a.periodo_norma, YEAR(consultas.fecha_hasta) 
    UNION
SELECT consultas.id, consultas.id_articulo, p.dni, p.cuit,p.apellido_nombre as nombre_apellido,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , 
	CONCAT('II SEMESTRE', ' ', YEAR(consultas.fecha_hasta)) AS  periodo
	FROM consultas_medicas as consultas 
    INNER JOIN articulos as a ON a.id = consultas.id_articulo
    INNER JOIN estados as e ON e.id =consultas.id_estado
    INNER join personas p on p.id = consultas.id_persona 
    WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=3 AND MONTH(consultas.fecha_hasta) >=07  AND MONTH(consultas.fecha_hasta) <=12 GROUP BY consultas.id_persona,consultas.id_articulo,a.periodo_norma, YEAR(consultas.fecha_hasta) 
    
SQL;
        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(empty($res)){
            return [];
        }
        return $res;
    }

    public static function  busqueda_avanzada_consultasmedicas($dni,$apellido_nombre){
        $apto_medico = \App\Modelo\Articulo::APTO_MEDICO;
        $sql	= <<<SQL
		SELECT consultas.id, consultas.id_articulo, personas.dni, personas.cuit,personas.apellido_nombre as nombre_apellido,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , YEAR(consultas.fecha_hasta) AS periodo
	FROM consultas_medicas as consultas 
    INNER JOIN articulos as a ON a.id = consultas.id_articulo AND a.nombre != '$apto_medico'
    INNER JOIN estados as e ON e.id =consultas.id_estado
    INNER JOIN personas on personas.id = consultas.id_persona
    WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=4
SQL;
        if($dni !=null){
            $sql_params[':dni']= $dni;
            $sql.=" AND personas.dni= :dni ";
        }

        if($apellido_nombre !=null){
            $sql_params[':nombre_apellido']= '%' . strtolower($apellido_nombre) . '%';
            $sql.=" AND personas.apellido_nombre LIKE :nombre_apellido ";
        }

        $sql.=" GROUP BY consultas.id_persona,consultas.id_articulo,a.periodo_norma, YEAR (consultas.fecha_hasta) ";

        $sql.="UNION ";

        $sql.=" SELECT consultas.id, consultas.id_articulo,  personas.dni, personas.cuit,personas.apellido_nombre as nombre_apellido,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , 
        CONCAT('I SEMESTRE', ' ', YEAR(consultas.fecha_hasta)) AS  periodo
        FROM consultas_medicas as consultas 
        INNER JOIN articulos as a ON a.id = consultas.id_articulo AND a.nombre != '$apto_medico'
        INNER JOIN estados as e ON e.id =consultas.id_estado
        INNER JOIN personas on personas.id = consultas.id_persona
        WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=3 AND MONTH(consultas.fecha_hasta) >=01  AND MONTH(consultas.fecha_hasta) <=06
        ";

        if($dni !=null){
            $sql_params[':dni']= $dni;
            $sql.=" AND personas.dni= :dni ";
        }

        if($apellido_nombre !=null){
            $sql_params[':nombre_apellido']= '%' . strtolower($apellido_nombre) . '%';
            $sql.=" AND personas.apellido_nombre LIKE :nombre_apellido ";
        }

        $sql.=" GROUP BY consultas.id_persona,consultas.id_articulo,a.periodo_norma, YEAR (consultas.fecha_hasta) ";

        $sql.="UNION ";


        $sql.=" SELECT consultas.id, consultas.id_articulo, personas.dni, personas.cuit,personas.apellido_nombre as nombre_apellido,  a.periodo_norma,a.nombre as nombre_articulos,a.cantidad_dias_norma, e.estado as estado, SUM(TIMESTAMPDIFF(DAY,consultas.fecha_desde, consultas.fecha_hasta)) AS dias_tomados , 
	CONCAT('II SEMESTRE', ' ', YEAR(consultas.fecha_hasta)) AS  periodo
	FROM consultas_medicas as consultas 
    INNER JOIN articulos as a ON a.id = consultas.id_articulo AND a.nombre != '$apto_medico'
    INNER JOIN estados as e ON e.id =consultas.id_estado
    INNER JOIN personas on personas.id = consultas.id_persona
    WHERE consultas.borrado=0 AND a.borrado=0 AND e.borrado=0 AND a.periodo_norma=3 AND MONTH(consultas.fecha_hasta) >=07  AND MONTH(consultas.fecha_hasta) <=12
    ";

        if($dni !=null){
            $sql_params[':dni']= $dni;
            $sql.=" AND personas.dni= :dni ";
        }

        if($apellido_nombre !=null){
            $sql_params[':nombre_apellido']= '%' . strtolower($apellido_nombre) . '%';
            $sql.=" AND personas.apellido_nombre LIKE :nombre_apellido ";
        }

        $sql.=" GROUP BY consultas.id_persona,consultas.id_articulo,a.periodo_norma, YEAR (consultas.fecha_hasta) ";


        $res	= (new Conexiones())->consulta(Conexiones::SELECT, $sql, $sql_params);
        if(empty($res)){
            return [];
        }
        return $res;
    }



    public static function listar_resumen_eventos($params = array()){
        $mensual =\App\Modelo\Articulo::MENSUAL;
        $trimestral = \App\Modelo\Articulo::TRIMESTRAL;
        $semestral = \App\Modelo\Articulo::SEMESTRAL;
        $anual = \App\Modelo\Articulo::ANUAL;
        $bianual = \App\Modelo\Articulo::BIANUAL;

        $apto_medico = \App\Modelo\Articulo::APTO_MEDICO;
    
        $dni = $params['dni'];
        $campos    = 'id, dni,cuit, apellido_nombre, periodo_norma, articulo, cantidad_dias_norma, dias_tomados, flag_alerta';
        $sql_params = [];
        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $consulta = <<<SQL
        SELECT
           	IF(SUM(TIMESTAMPDIFF(DAY,
			consultas.fecha_desde,
			consultas.fecha_hasta)) <= a.cantidad_dias_norma,1 , 0) as flag_alerta,
            p.id,
            p.dni,
            p.cuit,
            p.apellido_nombre,
            IF(a.periodo_norma = $mensual,"MENSUAL", IF(a.periodo_norma = $trimestral,"TRIMESTRAL", IF(a.periodo_norma = $semestral, "SEMESTRAL", IF(a.periodo_norma = $anual, "ANUAL", IF(a.periodo_norma = $bianual, "BIANUAL", "NO APLICA"))))) as periodo_norma,
            a.nombre AS articulo,
            a.cantidad_dias_norma,
			SUM(TIMESTAMPDIFF(DAY,
			consultas.fecha_desde,
			consultas.fecha_hasta)) as dias_tomados
        FROM
            consultas_medicas AS consultas
                INNER JOIN
            articulos AS a ON a.id = consultas.id_articulo AND a.nombre != '$apto_medico'
                INNER JOIN
            personas AS p ON p.id = consultas.id_persona
        WHERE
            p.dni = $dni AND consultas.borrado = 0
        GROUP BY consultas.id_articulo
SQL;
        $data = self::listadoAjax($campos, $consulta, $params, $sql_params);
        return $data;
    }

    public static function listar_log_cambios($params = array())
    {

        $usuarios = \FMT\Usuarios::getUsuarios();
        $select = '';
  
        foreach($usuarios as $usuario){
                $select .= ($select == '') ? 'SELECT '.$usuario['idUsuario'].' as id_usuario, "'.$usuario['user'].'" as nombre_usuario, concat("'.$usuario['apellido'].'"," ","'.$usuario['nombre'].'") as apellido_nombre_usu '
                                              : 'UNION ALL SELECT "'.$usuario['idUsuario'].'" as id_usuario, "'.$usuario['user'].'" as nombre_usuario, concat("'.$usuario['apellido'].'"," " ,"'.$usuario['nombre'].'") as apellido_nombre_usu ';    
        }    
       
        $campos    = 'id, fecha_operacion, nombre_usuario, apellido_nombre_usu, tipo_operacion, numero_consulta, dni, cuit, apellido_nombre_pers, estado, articulo, interviniente, fecha_intervencion, fecha_desde, fecha_hasta, fecha_regreso_trabajo, fecha_nueva_revision, observacion, medico_tratante, contacto_tratante, doc_adjuntos';
        $sql_params = [];
        $where = [];

        $condicion1 = "AND m.borrado = 0";
        $condicion2 = "AND e.borrado = 0";

        $params['order']['campo'] = (!isset($params['order']['campo']) || empty($params['order']['campo'])) ? 'tipo' : $params['order']['campo'];
        $params['order']['dir']   = (!isset($params['order']['dir'])   || empty($params['order']['dir']))   ? 'asc' : $params['order']['dir'];
        $params['start']  = (!isset($params['start'])  || empty($params['start']))  ? 0 :
        $params['start'];
        $params['lenght'] = (!isset($params['lenght']) || empty($params['lenght'])) ? 10 :
        $params['lenght'];
        $params['search'] = (!isset($params['search']) || empty($params['search'])) ? '' :
        $params['search'];

        $default_params = [
            'filtros'   => [
                'dni'   => null,
                'usuario'=> null,
            ]
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

        /*Filtros */
        if(!empty($params['filtros']['dni'])){
            $where [] = "p.dni = :dni";
            $sql_params[':dni']    = $params['filtros']['dni'];

        }

        if(!empty($params['filtros']['usuario'])){
            $where [] = "c_m.id_usuario = :id_usuario";
            $sql_params[':id_usuario']    = $params['filtros']['usuario'];

        }

        $condicion1 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';
        $condicion2 .= !empty($where) ? ' WHERE ' . \implode(' AND ',$where) : '';

        $esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;
      
         if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (p.cuit like :search{$indice} OR p.dni like :search{$indice} OR p.apellido_nombre like :search{$indice} OR a.nombre like :search{$indice} OR es.estado like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.id_consulta_medica like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice} OR c_m.fecha_regreso_trabajo like :search{$indice}  OR c_m.fecha_nueva_revision like :search{$indice} OR c_m.medico_tratante OR c_m.telefono_contacto_tratante like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        }

        $group = 'GROUP BY c_m.id, c_m.id_persona';

        $consulta = <<<SQL
        SELECT c_m.id, c_m.fecha_operacion, u.nombre_usuario, u.apellido_nombre_usu, IF(c_m.tipo_operacion="A","ALTA", IF(c_m.tipo_operacion="M","MODIFICACIÓN", IF(c_m.tipo_operacion="B", "BAJA", "ERROR"))) as tipo_operacion, c_m.id_consulta_medica as numero_consulta, p.dni, p.cuit, p.apellido_nombre as apellido_nombre_pers, es.estado, a.nombre as articulo, concat(m.nombre,' ', m.apellido) as interviniente, c_m.fecha_intervencion, concat(c_m.fecha_desde,' 0:00:00') as fecha_desde, concat(c_m.fecha_hasta,' 0:00:00') as fecha_hasta, concat(c_m.fecha_regreso_trabajo, ' 0:00:00') as fecha_regreso_trabajo, concat(c_m.fecha_nueva_revision,' 0:00:00') as fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante as contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos 
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        INNER JOIN estados es ON
        es.id_estado = c_m.id_estado
        INNER JOIN articulos a ON
        a.id_articulo = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN ($select) u ON
        u.id_usuario = c_m.id_usuario
        INNER JOIN medicos m ON
        (m.id_medico = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico)
        $condicion1
        $group
        UNION ALL
        SELECT c_m.id, c_m.fecha_operacion, u.nombre_usuario, u.apellido_nombre_usu, IF(c_m.tipo_operacion="A","ALTA", IF(c_m.tipo_operacion="M","MODIFICACIÓN", IF(c_m.tipo_operacion="B", "BAJA", "ERROR"))) as tipo_operacion, c_m.id_consulta_medica as numero_consulta, p.dni, p.cuit, p.apellido_nombre as apellido_nombre_pers, es.estado, a.nombre as articulo, concat(e.nombre,' ', e.apellido) as interviniente, c_m.fecha_intervencion, concat(c_m.fecha_desde, ' 0:00:00') as fecha_desde, concat(c_m.fecha_hasta, ' 0:00:00') as fecha_hasta, concat(c_m.fecha_regreso_trabajo, ' 0:00:00') as fecha_regreso_trabajo, concat(c_m.fecha_nueva_revision,' 0:00:00') as fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante as contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos 
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        INNER JOIN estados es ON
        es.id_estado= c_m.id_estado
        INNER JOIN articulos a ON
        a.id_articulo = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN ($select) u ON
        u.id_usuario = c_m.id_usuario
        INNER JOIN enfermeras e ON
        (e.id_enfermera = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera)
        $condicion2
        $group
SQL;

        $data = self::listadoAjax_log($campos, $consulta, $params, $sql_params);
        return $data;
    }

    public static function listar_log_cambios_excel($params){
        $cnx    = new Conexiones('db_log');
        $sql_params = [];
        $where = [];
        $condicion1 = '';
        $condicion2 = '';
        $order = '';
        $search = [];

        $esMedico = \App\Modelo\Consultamedica::MEDICO;
        $esEnfermera = \App\Modelo\Consultamedica::ENFERMERA;

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [
                'dni'      => null,
                'usuario'   =>null
            ],
            'count'     => false
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);
        $usuarios = \FMT\Usuarios::getUsuarios();
        $select = '';
  
        foreach($usuarios as $usuario){
                $select .= ($select == '') ? 'SELECT '.$usuario['idUsuario'].' as id_usuario, "'.$usuario['user'].'" as nombre_usuario, concat("'.$usuario['apellido'].'"," ","'.$usuario['nombre'].'") as apellido_nombre_usu '
                                              : 'UNION ALL SELECT "'.$usuario['idUsuario'].'" as id_usuario, "'.$usuario['user'].'" as nombre_usuario, concat("'.$usuario['apellido'].'"," " ,"'.$usuario['nombre'].'") as apellido_nombre_usu ';    
        }    
        $sql1= <<<SQL
        SELECT c_m.id, c_m.fecha_operacion, u.nombre_usuario, u.apellido_nombre_usu, IF(c_m.tipo_operacion="A","ALTA", IF(c_m.tipo_operacion="M", "MODIFICACIÓN", IF(c_m.tipo_operacion="B", "BAJA", "ERROR"))) as tipo_operacion, c_m.id_consulta_medica as numero_consulta,p.dni, p.cuit, p.apellido_nombre as apellido_nombre_pers, es.estado, a.nombre as articulo, concat(m.nombre,' ', m.apellido) as interviniente, c_m.fecha_intervencion, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo, c_m.fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante as contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos
SQL;


        $sql2= <<<SQL
        SELECT c_m.id, c_m.fecha_operacion, u.nombre_usuario, u.apellido_nombre_usu, IF(c_m.tipo_operacion="A","ALTA", IF(c_m.tipo_operacion="M", "MODIFICACIÓN", IF(c_m.tipo_operacion="B", "BAJA", "ERROR"))) as tipo_operacion, c_m.id_consulta_medica as numero_consulta, p.dni, p.cuit, p.apellido_nombre as apellido_nombre_pers, es.estado, a.nombre as articulo, concat(e.nombre,' ', e.apellido) as interviniente, c_m.fecha_intervencion, c_m.fecha_desde, c_m.fecha_hasta, c_m.fecha_regreso_trabajo,c_m.fecha_nueva_revision, c_m.observacion, c_m.medico_tratante, c_m.telefono_contacto_tratante as contacto_tratante, group_concat(DISTINCT d.nombre) as doc_adjuntos 
SQL;


    $from1 = <<<SQL
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        INNER JOIN estados es ON
        es.id_estado = c_m.id_estado
        INNER JOIN articulos a ON
        a.id_articulo = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN ($select) u ON
        u.id_usuario = c_m.id_usuario
        INNER JOIN medicos m ON
        (m.id_medico = c_m.id_interviniente AND c_m.tipo_interviniente = $esMedico)
SQL;

    $from2 = <<<SQL
        FROM consultas_medicas c_m
        INNER JOIN personas p ON
        p.id_persona = c_m.id_persona
        INNER JOIN estados es ON
        es.id_estado= c_m.id_estado
        INNER JOIN articulos a ON
        a.id_articulo = c_m.id_articulo
        LEFT JOIN documentos_adjuntos d ON
        (d.id_consulta_medica = c_m.id_consulta_medica AND d.fecha_operacion = c_m.fecha_operacion)
        INNER JOIN ($select) u ON
        u.id_usuario = c_m.id_usuario
        INNER JOIN enfermeras e ON
        (e.id_enfermera = c_m.id_interviniente AND c_m.tipo_interviniente = $esEnfermera)
SQL;

    $condicion1 = <<<SQL
        WHERE
        m.borrado = 0
SQL;

    $condicion2 = <<<SQL
        WHERE
        e.borrado = 0
SQL;

    $group = <<<SQL
        GROUP BY c_m.id, c_m.id_persona
SQL;
    /**Filtros para la consulta 1*/
    if(!empty($params['filtros']['dni'])){
        $condicion1 .= " AND p.dni = :dni";
        $sql_params[':dni']    = $params['filtros']['dni'];
    }

    if(!empty($params['filtros']['usuario'])){
        $condicion1 .= " AND c_m.id_usuario = :id_usuario";
        $sql_params[':id_usuario']   = $params['filtros']['usuario'];
    }

    /**Filtros para la consulta 2*/
    if(!empty($params['filtros']['dni'])){
        $condicion2 .= " AND p.dni = :dni";
        $sql_params[':dni']    = $params['filtros']['dni'];
    }

    if(!empty($params['filtros']['usuario'])){
        $condicion2 .= " AND c_m.id_usuario = :id_usuario";
        $sql_params[':id_usuario']   = $params['filtros']['usuario'];
    }

    $counter_query  = "SELECT COUNT(c_m.id) AS total {$from1}";

    $counter_query2  = "SELECT COUNT(c_m.id) AS total {$from2}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion1, $sql_params )[0]['total'];

    $recordsTotal2   =  $cnx->consulta(Conexiones::SELECT, $counter_query2 . $condicion2, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (p.cuit like :search{$indice} OR p.dni like :search{$indice} OR p.apellido_nombre like :search{$indice} OR a.nombre like :search{$indice} OR es.estado like :search{$indice} OR c_m.fecha_intervencion like :search{$indice} OR c_m.id_consulta_medica like :search{$indice} OR c_m.fecha_desde like :search{$indice} OR c_m.fecha_hasta like :search{$indice} OR c_m.fecha_regreso_trabajo like :search{$indice}  OR c_m.fecha_nueva_revision like :search{$indice} OR c_m.medico_tratante OR c_m.telefono_contacto_tratante like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        
        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }

        $order .= implode(',', $orderna);

        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion1, $sql_params)[0]['total'];

        $recordsFiltered2= $cnx->consulta(Conexiones::SELECT, $counter_query2.$condicion2, $sql_params)[0]['total'];

        $order .= (($order =='') ? '' : ', ').'c_m.fecha_operacion desc ';

        $order = ' ORDER BY '.$order;

        $lista1 = $cnx->consulta(Conexiones::SELECT,  $sql1 .$from1.$condicion1.$group.$order.$limit,$sql_params);

        $lista2 = $cnx->consulta(Conexiones::SELECT,  $sql2 .$from2.$condicion2.$group.$order.$limit,$sql_params);

        $result = array_merge($lista1, $lista2);

        return ($result) ? $result : [];
    }


    public static function listar_resumen_eventos_excel($params){
        $mensual =\App\Modelo\Articulo::MENSUAL;
        $trimestral = \App\Modelo\Articulo::TRIMESTRAL;
        $semestral = \App\Modelo\Articulo::SEMESTRAL;
        $anual = \App\Modelo\Articulo::ANUAL;
        $bianual = \App\Modelo\Articulo::BIANUAL;

        $apto_medico = \App\Modelo\Articulo::APTO_MEDICO;
        
        $dni = $params['dni'];
        $cnx    = new Conexiones();
        $sql_params = [];
        $where = [];
        $condicion = '';
        $order = '';
        $search = [];

        $default_params = [
            'order'     => [
                [
                    'campo' => 'id',
                    'dir'   => 'ASC',
                ],
            ],
            'start'     => 0,
            'lenght'    => 10,
            'search'    => '',
            'filtros'   => [
            ],
            'count'     => false
        ];

        $params['filtros']  = array_merge($default_params['filtros'], $params['filtros']);
        $params = array_merge($default_params, $params);

    $sql= <<<SQL
        SELECT 
            p.id,
            p.dni,
            p.cuit,
            p.apellido_nombre,
            a.nombre AS articulo,
            a.cantidad_dias_norma,
            IF(a.periodo_norma = $mensual,"MENSUAL", IF(a.periodo_norma = $trimestral,"TRIMESTRAL", IF(a.periodo_norma = $semestral, "SEMESTRAL", IF(a.periodo_norma = $anual, "ANUAL", IF(a.periodo_norma = $bianual, "BIANUAL", "NO APLICA"))))) as periodo_norma,
            SUM(TIMESTAMPDIFF(DAY,
            consultas.fecha_desde,
            consultas.fecha_hasta)) as dias_tomados,
            IF(SUM(TIMESTAMPDIFF(DAY,
            consultas.fecha_desde,
            consultas.fecha_hasta)) <= a.cantidad_dias_norma,1 , 0) as flag_alerta
SQL;


    $from = <<<SQL
            FROM consultas_medicas AS consultas
                INNER JOIN
            articulos AS a ON a.id = consultas.id_articulo AND a.nombre != '$apto_medico'
                INNER JOIN
            personas AS p ON p.id = consultas.id_persona
SQL;

    $condicion = <<<SQL
        WHERE
          p.dni = $dni AND consultas.borrado = 0
SQL;

    $group = <<<SQL
        GROUP BY consultas.id_articulo   
SQL;

    $counter_query  = "SELECT COUNT(consultas.id) AS total {$from}";

    $recordsTotal   =  $cnx->consulta(Conexiones::SELECT, $counter_query . $condicion, $sql_params )[0]['total'];

        //Los campos que admiten en el search (buscar) para concatenar al filtrado de la consulta
        if(!empty($params['search'])){
            $indice = 0;
            $search[]   = <<<SQL
            (p.dni like :search{$indice} OR p.cuit like :search{$indice} OR p.apellido_nombre like :search{$indice}) 
SQL;
            $texto = $params['search'];
            $sql_params[":search{$indice}"] = "%{$texto}%";

            $buscar =  implode(' AND ', $search);
            $condicion1 .= empty($condicion1) ? "{$buscar}" : " AND {$buscar} ";

            $buscar =  implode(' AND ', $search);
            $condicion2 .= empty($condicion2) ? "{$buscar}" : " AND {$buscar} ";
        
        }

        /**Orden de las columnas */
        $orderna = [];
        foreach ($params['order'] as $i => $val) {
            $orderna[]  = "{$val['campo']} {$val['dir']}";
        }

        $order .= implode(',', $orderna);

        $limit = (isset($params['lenght']) && isset($params['start']) && $params['lenght'] != '')
            ? " LIMIT  {$params['start']}, {$params['lenght']}" : ' ';

        $recordsFiltered= $cnx->consulta(Conexiones::SELECT, $counter_query.$condicion, $sql_params)[0]['total'];

        $order .= (($order =='') ? '' : ', ').'consultas.id ';

        $order = ' ORDER BY '.$order;

        $lista = $cnx->consulta(Conexiones::SELECT,  $sql .$from.$condicion.$group.$order.$limit,$sql_params);

        return ($lista) ? $lista : [];
    }

    static public function consultar_comprobante_enviado($id_consulta_medica=null){
        $no_enviado = \App\Modelo\Consultamedica::NO_ENVIADO;
        if($id_consulta_medica){
        $Conexiones = new Conexiones();
        $resultado = $Conexiones->consulta(Conexiones::SELECT,
<<<SQL

            SELECT enviado
            FROM comprobantes_enviados
            WHERE id_consulta_medica= :id_consulta_medica
SQL
        ,[':id_consulta_medica'=>$id_consulta_medica]);

        if(!empty($resultado)){
            return $resultado[0]['enviado'];
        }
       
        return $resultado[0]['enviado'] = $no_enviado;
        }

    }

    public static function comprobante_enviado_alta($id_consulta = null, $emails = []){
        $json_emails = json_encode($emails);
        $enviado =\App\Modelo\Consultamedica::ENVIADO;
        $cnx = new Conexiones();
        $sql_params = [
            ':emails' => $json_emails,
            ':id_consulta' => $id_consulta,
            ':enviado'     => $enviado
        ];
        $sql = 'INSERT INTO comprobantes_enviados (id_consulta_medica, enviado, email) VALUES (:id_consulta, :enviado, :emails)';
        $res = $cnx->consulta(Conexiones::INSERT, $sql, $sql_params);
       
        return $res;
    }

}