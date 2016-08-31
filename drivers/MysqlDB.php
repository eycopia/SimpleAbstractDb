<?php
require_once('interfaceDB.php');
require_once('Utilities.php');

class MysqlDB extends Utilities implements DB{

    public $con;

    protected  $debugMode;

    /**
     * Query preparados
     * @var Object
     */
    private $result;

    /**
     * Nombre de la base de datos
     * @var string
     */
    private $db;

    /**
     * Nombre de la base de datos
     * @var string
     */
    private $pass;

    /**
     * Nombre de la base de datos
     * @var string
     */
    private $user;

    /**
     * Nombre de la base de datos
     * @var string
     */
    private $host;

    /**
     * MysqlDB constructor.
     *
     * @param      $host
     * @param      $user username
     * @param      $pass password
     * @param      $db database name
     * @param bool $debug
     */
    public function __construct($host, $user, $pass, $db, $debug=false){
        $this->debugMode = $debug;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->db = $db;
        $this->connect();
    }

    /**
     * Realiza la conexion con la base de datos
     *
     * @return \void
     * @throws \Exception
     */
    public function connect(){
        try{
            $this->con = new mysqli($this->host, $this->user, $this->pass, $this->db);
            $this->con->set_charset("utf8");
        }catch (Exception $e){
            throw new Exception("Error al conectar db, Host: {$this->host}, "
                . "User: {$this->user}, Database: {$this->db}, Error: "
                . $e->getMessage());
        }
    }


    /**
     * Ingresa el query a procesar
     * @param  string
     * @throws \Exception cuando no se encuentra el registro
     * @return void
     */
    public function query($query){
        $this->debug($query);
        $this->result = $this->con->query($query);
        if($this->result === false){
            throw new Exception( "Error en el query: " . $this->con->error);
        }
    }

    /**
     * Obtiene todos los registros encontrados
     * en una consulta, en formato object
     * @return Array
     */
    public function fetch_all(){
        $data = array();
        while( $obj = $this->result->fetch_object() ){
            $data[] = $obj;
        }
        return $data;
    }

    /**
     * Obtiene todos los registros encontrados
     * en una consulta, en formato array
     * @return Array
     */
    public function fetch_all_array(){
        $data = array();
        while( $r = $this->result->fetch_assoc() ){
            $data[] = $r;
        }
        return $data;
    }

    /**
     * Obtiene $row de los resultados de un query
     * @param  string $row valor a extraer
     * @return array
     */
    public function getValues($row){
        $data = array();
        while( $obj = $this->result->fetch_assoc() ){
            $data[] = $obj[$row];
        }
        return $data;
    }

    /**
     * Retorna un solo registro
     * @return array associative
     */
    public function getRow(){
        return  $this->result->fetch_assoc();
    }

    /**
     * Busca un registro en especifico
     *
     * @param string $table
     * @param string $value
     * @param null $pk
     * @throws cuando no se encuentra el registro
     * @return object
     */
    public function find($table,  $value, $pk = null){
        if(is_null($pk)){
            $pk = "id";
        }
        $this->query("SELECT * FROM $table WHERE $pk = '$value'");
        return $this->result->fetch_assoc();
    }


    /**
     * Ingresa un nuevo registro en la base de datos
     * @param string $table table name
     * @param array $params array asociativo table_column => value
     * @throws Exception cuando hay un error en la base de datos
     * @return  void
     */
    public function insert($table, $params){
        $sql = " INSERT INTO `{$table}` ";
        $campos = array();
        $valores = array();
        foreach ($params as $campo => $valor) {
            array_push($campos, "`{$campo}`");
            array_push($valores, "'{$valor}'");
        }
        $sql .= " ( ". join(',', $campos) . ") VALUES "
             .  " ( " . join(',', $valores) . ")";
        $this->debug($sql);
        try {
            $this->query($sql);
        }catch(Exception $e){
            throw new Exception(
                str_replace('Error en el query: ','Error al insertar: ',$e->getMessage())
            );
        }
    }

    /**
     * Realiza una actualizacion, esta funcion no acepta actualizaciones sin where
     *
     * @param $table
     * @param $params
     * @param $where
     * @throws Exception
     * @return int numero de filas afectadas
     */
    public function update($table, $params, $where){
        if( empty($where) || is_null($where)){
            throw new Exception("Debe ingresar una condicion para actualizar");
        }
        $changes = array();
        foreach ($params as $campo => $valor) {
            array_push($changes, "{$campo} = '{$valor}'");
        }
        $sql = "UPDATE $table SET ". join(',', $changes) ." WHERE $where ";
        $this->debug($sql);
        $this->query($sql);
        return $this->con->affected_rows;
    }

    public function close(){
        $this->con->close();
    }

    /**
     * Obtiene el ultimo pk generado por la base de datos
     * siempre que el pk sea un funcion autoincrementable
     *
     * @return int
     */
    public function get_last_id()
    {
        return $this->con->insert_id;
    }
}
