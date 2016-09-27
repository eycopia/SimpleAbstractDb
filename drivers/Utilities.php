<?php

/**
 * Class UtilDB
 *
 * Metodos que funcionan en
 * cualquier gestor de base de datos.
 */
class Utilities
{
    /**
     * SQL que se arma para una insercion multiple
     * @var string
     */
    private $sqlMultipleInsert = "";

    /**
     * Volores a insertar en una insercion multiple
     * @var array
     */
    private $values = array();

    /**
     * Construye un query que agrupa 100 sentencias Insert
     * para realizar una sola llamada a la base de datos
     * @param  string $table  nombre de la tabla
     * @param  array $params  campos
     * @return void
     */
    public function multiple_insert($table, $params){
        $total = count($this->values);
        if($total == 0){
            $this->open_multiple($table, $params);
        }

        $valores = array();
        foreach ($params as $campo => $valor) {
            if(is_null($valor)){
                array_push($valores, 'NULL');
            }else{
                $valor = str_replace('"', "'", $valor);
                array_push($valores, "\"{$valor}\"");
            }
        }
        array_push($this->values, " ( " . join(',', $valores) . ") ");
        if($total == 100){
            $this->sqlMultipleInsert .= join(',', $this->values);
            $this->query($this->sqlMultipleInsert);
            $this->sqlMultipleInsert = '';
            $this->values = array();
        }
    }

    /**
     * Inicializa una insercion multiple
     * @param  string $table  tabla donde ingresar los datos
     * @param  array $params los campos de la tabla
     */
    public function open_multiple($table, $params){
        $this->sqlMultipleInsert = " INSERT IGNORE INTO {$table} ";
        $campos = array();
        foreach ($params as $campo => $valor) {
            array_push($campos, "`{$campo}`");
        }
        $this->sqlMultipleInsert .= " ( ". join(',', $campos) . ") VALUES ";
    }

    /**
     * Termina una inserción de datos y limpia la cache
     */
    public function close_multiple(){
        $this->sqlMultipleInsert  .= join(',', $this->values);
        if(!empty($this->sqlMultipleInsert)){
            $this->query($this->sqlMultipleInsert);
        }
        $this->sqlMultipleInsert = '';
        $this->values = array();
    }

    protected function debug($query){
        if($this->debugMode){
            print date('Y-m-d H:i:s') . " " . $query . PHP_EOL;
        }
    }
}
