<?php
require_once('interfaceDB.php');
require_once('Utilities.php');

class OracleDB extends Utilities implements DB{
    private $con;
    private $result;

    public function __construct($user, $pass, $db){
        $this->con = oci_connect($user, $pass, $db, 'AL32UTF8');
        if( !$this->con){
            $e = oci_error();
            echo "Un Error de oracle "; print_r(oci_error());
            exit;
        }
    }

    /**
     * Ingresa el query a procesar
     * @param  string
     * @throws \Exception cuando no se encuentra el registro
     * @return void
     */
    public function query($query){
        $this->result = oci_parse($this->con, $query);
        if($this->result === false){
            throw new Exception("Error en el query: " . $this->con->error);
        }
        oci_execute($this->result);
    }

    /**
     * Obtiene todos los registros encontrados
     * en una consulta, en formato object
     * @return Array
     */
    public function fetch_all(){
        $data = array();
        while( ($obj = oci_fetch_object($this->result)) != false ){
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
        while( ($r = oci_fetch_assoc($this->result)) != false ){
            $data[] = $r;
        }
        return $data;
    }

    /**
     * Extrae solo el valor solicitado de todos los
     * registros solicitados
     * @param  string $row valor a extraer
     * @return array
     */
    public function getValues($row){
        $data = array();
        while( ($obj = oci_fetch_assoc($this->result)) != false ){
            $data[] = $obj[$row];
        }
        return $data;
    }

    /**
     * Devuelve solo un registro
     * @return [type] [description]
     */
    public function getRow(){
        return  oci_fetch_array($this->result);

    }

    public function close(){
        oci_close($this->con);
    }

    /**
     * Busca un registro en especifico
     *
     * @param string $table
     * @param string $value
     * @param null   $pk
     *
     * @throws cuando no se encuentra el registro
     * @return object
     */
    function find($table, $value, $pk = null)
    {
        // TODO: Implement find() method.
    }

    /**
     * Ingresa un nuevo registro en la base de datos
     *
     * @param string $table  table name
     * @param array  $params array asociativo table_column => value
     *
     * @throws cuando hay un error en la base de datos
     * @return  void
     */
    function insert($table, $params)
    {
        // TODO: Implement insert() method.
    }

    /**
     * Obtiene el ultimo pk generado por la base de datos
     * siempre que el pk sea un funcion autoincrementable
     *
     * @return int
     */
    function get_last_id()
    {
        // TODO: Implement get_last_id() method.
    }

    /**
     * Realiza una actualizacion, esta funcion no acepta actualizaciones sin where
     *
     * @param $table
     * @param $params
     * @param $where
     *
     * @return void
     */
    function update($table, $params, $where)
    {
        // TODO: Implement update() method.
}}
