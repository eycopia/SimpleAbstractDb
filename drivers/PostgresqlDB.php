    <?php
    require_once('interfaceDB.php');
    require_once('Utilities.php');


    class PostgresqlDB extends Utilities implements DB
    {
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
        /**
         * Realiza la conexion con la base de datos
         *
         * @throws Cuando no se puede conectar la base de datos
         * @return void
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
            $conn_string = "host={$this->host} port=5432 dbname={$this->db}" .
                " user='{$this->user}' password='{$this->pass}' " .
                " options='--client_encoding=UTF8'";
            $this->con = pg_connect($conn_string);

            if($this->con === FALSE){
                throw new Exception("Error al conectar db, Host: {$this->host}, "
                    . "User: {$this->user}, Database: {$this->db}");
            }
        }

        /**
         * Ingresa el query a procesar
         *
         * @param  string
         *
         * @throws \Exception cuando no se encuentra el registro
         * @return void
         */
        function query($query)
        {
            $this->debug($query);
            $this->result = pg_query($this->con, $query);
            if($this->result === false){
                throw new Exception( "Error en el query: " . pg_last_error($this->con));
            }
        }

        /**
         * Obtiene $row de los resultados de un query
         *
         * @param  string $row valor a extraer
         *
         * @return array
         */
        function getValues($row)
        {
            $data = array();
            while( $obj = pg_fetch_assoc($this->result) ){
                $data[] = $obj[$row];
            }
            return $data;
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
            if(is_null($pk)){
                $pk = "id";
            }
            $this->query("SELECT * FROM $table WHERE $pk = '$value'");
            return pg_fetch_assoc($this->result);
        }

        /**
         * Retorna cada registro como un objeto
         *
         * @return Array
         */
        function fetch_all()
        {
            $data = array();
            while( $obj = pg_fetch_object($this->result) ){
                $data[] = $obj;
            }
            return $data;
        }

        /**
         * Retonarna cada registro como un array asociativo
         * @return array
         */
        function fetch_all_array()
        {
            $data = array();
            while( $r = pg_fetch_assoc($this->result) ){
                $data[] = $r;
            }
            return $data;
        }

        /**
         * Retorna un solo registro
         *
         * @return array associative
         */
        function getRow()
        {
            return pg_fetch_assoc($this->result);
        }

        /**
         * Ingresa un nuevo registro en la base de datos
         *
         * @param string $table  table name
         * @param array  $params array asociativo table_column => value
         *
         * @throws Exception  cuando hay un error en la base de datos
         * @return  void
         */
        function insert($table, $params)
        {
            $this->result = pg_insert($this->con, $table, $params);
            if($this->result == FALSE){
                throw new Exception(
                    str_replace('Error en el query: ','Error al insertar: ',pg_last_error($this->con))
                );
            }
        }

        /**
         * Obtiene el ultimo pk generado por la base de datos
         * siempre que el pk sea un funcion autoincrementable
         *
         * @return int
         */
        function get_last_id()
        {
            return pg_fetch_row($this->result);
        }

        /**
         * Realiza una actualizacion, esta funcion no acepta actualizaciones sin where
         *
         * @param $table
         * @param $params
         * @param $where
         *
         * @return int numero de filas afectadas
         */
        function update($table, $params, $where)
        {
            if(!is_array($where)){
                throw new Exception("Las condiciones \$where debe ser un array asociativo");
            }
            $this->result = pg_update($this->con, $table, $params, $where);
            if (!$this->result) {
                throw new Exception ('No se pudo actualizar' . pg_last_error($this->con));
            }
            return pg_affected_rows($this->result);
        }

        /**
         * Cierra la conexion a la base de datos
         *
         * @return void
         */
        function close()
        {
            pg_close($this->con);
        }
    }
