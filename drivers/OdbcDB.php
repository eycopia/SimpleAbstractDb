<?php
require_once('interfaceDB.php');
require_once('Utilities.php');


class OdbcDB extends Utilities implements DB
{
    public $con;

    protected $debugMode;

    /**
     * Query preparados
     * @var Object
     */
    private $result;

    /**
     * El nombre de la fuente de base de datos para la conexión.
     * Alternativamente se puede usar una cadena de conexión sin DSN.
     * @var string
     */
    private $dsn;


    /**
     * El nombre del usuario
     * @var string
     */
    private $user;

    /**
     * La Contrasenia
     * @var string
     */
    private $pass;

    /**
     * OdbcDB constructor.
     *
     * @param      $host
     * @param      $user username
     * @param      $pass password
     * @param      $db database name
     * @param bool $debug
     */
    public function __construct($dsn, $user, $pass, $debug = false)
    {
        $this->debugMode = $debug;
        $this->dsn = $dsn;
        $this->user = $user;
        $this->pass = $pass;
        $this->connect();
    }

    /**
     * Realiza la conexion con la base de datos
     *
     * @return \void
     * @throws \Exception
     */
    public function connect()
    {
        $this->con = odbc_connect($this->dsn, $this->user, $this->pass);
        if (!$this->con) {
            throw new Exception(sprintf("Error al conectar DSN: %s, Error:", $this->dsn, odbc_error()));
        }
    }


    /**
     * Ingresa el query a procesar
     * @param  string
     * @throws \Exception cuando no se encuentra el registro
     * @return void
     */
    public function query($query)
    {
        $this->debug($query);
        $this->result = odbc_exec($this->con, $query);

        if ($this->result === false) {
            throw new Exception("Error en el query: " . odbc_error($this->con));
        }
    }

    /**
     * Obtiene todos los registros encontrados
     * en una consulta, en formato object
     * @return Array
     */
    public function fetch_all()
    {
        $data = array();
        while ($obj = odbc_fetch_object($this->result)) {
            $data[] = $obj;
        }
        return $data;
    }

    /**
     * Obtiene todos los registros encontrados
     * en una consulta, en formato array
     * @return Array
     */
    public function fetch_all_array()
    {
        $data = array();
        while ($r = $this->getRow()) {
            $data[] = $r;
        }
        return $data;
    }

    /**
     * Obtiene $field de los resultados de un query
     * @param  string $row valor a extraer
     * @return array
     */
    public function getValues($field)
    {
        $data = array();
        while ($obj = $this->getRow()) {
            $data[] = $obj[$field];
        }
        return $data;
    }

    /**
     * Retorna un solo registro
     * @return array associative
     */
    public function getRow()
    {
        return odbc_fetch_array($this->result);
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
    public function find($table, $value, $pk = null)
    {
        if (is_null($pk)) {
            $pk = "id";
        }
        $this->query("SELECT * FROM $table WHERE $pk = '$value'");
        return $this->getRow();
    }


    /**
     * Ingresa un nuevo registro en la base de datos
     * @param string $table table name
     * @param array $params array asociativo table_column => value
     * @throws Exception cuando hay un error en la base de datos
     * @return  void
     */
    public function insert($table, $params)
    {
        $sql = " INSERT INTO {$table} ";
        $campos = array();
        $valores = array();
        foreach ($params as $campo => $valor) {
            array_push($campos, "{$campo}");
            array_push($valores, '"' . $valor . '"');
        }
        $sql .= " ( " . join(',', $campos) . ") VALUES "
            . " ( " . join(',', $valores) . ")";
        $this->debug($sql);
        try {
            $this->query($sql);
        } catch (Exception $e) {
            throw new Exception(
                str_replace('Error en el query: ', 'Error al insertar: ', $e->getMessage())
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
    public function update($table, $params, $where)
    {
        if (empty($where) || is_null($where)) {
            throw new Exception("Debe ingresar una condicion para actualizar");
        }
        $changes = array();
        foreach ($params as $campo => $valor) {
            array_push($changes, "{$campo} = '{$valor}'");
        }
        $sql = "UPDATE $table SET " . join(',', $changes) . " WHERE $where ";
        $this->debug($sql);
        $this->query($sql);
        return $this->con->affected_rows;
    }

    public function close()
    {
        odbc_close($this->con);
    }

    /**
     * Obtiene el ultimo pk generado por la base de datos
     * siempre que el pk sea un funcion autoincrementable
     *
     * @return int
     */
    public function get_last_id()
    {
        // TODO: Implement get_last_id() method.
    }
}
