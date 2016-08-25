<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "AbstractTest.php";
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "OracleDB.php";

class OracleDBTest extends AbstractTest
{
    protected  $db = null;

    public function __construct()
    {
        $dir = dirname(__FILE__);
//        shell_exec('mysql -u root < ' . $dir.'/dbMysql.sql');
        $this->db = new OracleDB('system', 'sys', 'localhost/XE');
    }
}
