<?php

interface DB {

    /**
     * Ingresa el query a procesar
     * @param  string
     * @throws \Exception cuando no se encuentra el registro
     * @return void
     */
    function query($query);

    /**
     * Obtiene $row de los resultados de un query
     * @param  string $row valor a extraer
     * @return array
     */
    function getValues($row);

    /**
     * Busca un registro en especifico
     *
     * @param string $table
     * @param string $value
     * @param null $pk
     * @throws cuando no se encuentra el registro
     * @return object
     */
    function find($table,  $value, $pk = null);

    /**
     * Retorna cada registro como un objeto
     * @return Array
     */
    function fetch_all();

    /**
     * Retonarna cada registro como un array asociativo
     */
    function fetch_all_array();

    /**
     * Retorna un solo registro
     * @return array associative
     */
    function getRow();

    /**
     * Ingresa un nuevo registro en la base de datos
     * @param string $table table name
     * @param array $params array asociativo table_column => value
     * @throws Exception  cuando hay un error en la base de datos
     * @return  void
     */
    function insert($table, $params);

    /**
     * Obtiene el ultimo pk generado por la base de datos
     * siempre que el pk sea un funcion autoincrementable
     * @return int
     */
    function get_last_id();

    /**
     * Realiza una actualizacion, esta funcion no acepta actualizaciones sin where
     *
     * @param $table
     * @param $params
     * @param $where
     * @return int numero de filas afectadas
     */
    function update($table, $params, $where);

    /**
     * Cierra la conexion a la base de datos
     * @return void
     */
    function close();
}
