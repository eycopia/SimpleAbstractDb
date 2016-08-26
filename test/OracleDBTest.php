<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "OracleDB.php";

class OracleDBTest extends PHPUnit_Framework_TestCase
{
    protected  $db = null;

    public function __construct()
    {
        //$dir = dirname(__FILE__);
        //aun no encuentro la forma de cargar la base de datos por defecto
        //shell_exec('mysql -u root < ' . $dir.'/dbMysql.sql');
        $this->db = new OracleDB('system', 'sys', 'localhost/XE');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #Error en el query*#
     */
    public function testWrongQuery()
    {
        $query = "SELECT * FROM roles";
        $this->db->query($query);
    }

    public function testFetchAll(){
        $query = "SELECT * FROM abstract_table";
        $this->db->query($query);
        $data = $this->db->fetch_all();
        $this->assertEquals(2, count($data));
        $this->assertEquals('juan', $data[0]->NAME);
    }

    public function testFetchAllArray(){
        $query = "SELECT * FROM abstract_table";
        $this->db->query($query);
        $data = $this->db->fetch_all_array();
        $this->assertEquals(2, count($data));
        $this->assertEquals('juan', $data[0]['NAME']);
    }

    public function testGetValues(){
        $query = "SELECT * FROM abstract_table";
        $this->db->query($query);
        $data = $this->db->getValues('NAME');
        $expected  = array('juan', 'pedro');
        $this->assertEquals($expected, $data);
    }

    public function testGetRow(){
        $query = "SELECT * FROM abstract_table";
        $this->db->query($query);
        $data = $this->db->getRow();
        $this->assertEquals('juan', $data['NAME']);
    }

    public function testFindWithoutPk(){
        $data = $this->db->find('abstract_table', 1);
        $this->assertEquals('juan', $data['NAME']);
    }

    public function testFindWithPk(){
        $data = $this->db->find('abstract_table', 'pedro', 'name');
        $this->assertEquals('pedro', $data['NAME']);
    }

    public function testFindNone(){
        $data = $this->db->find('abstract_table', 4);
        $this->assertEquals( null, $data);
    }

    public function testInsertData(){
        $params = array('name' => 'lucas', 'id'=> 3);
        $this->db->insert('abstract_table', $params);
        $data = $this->db->find('abstract_table', 'lucas', 'name');
        $this->assertEquals('lucas', $data['NAME']);
        $this->assertEquals(3, $data['ID']);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #Error al insertar*#
     */
    public function testInsertWrongData(){
        $params = array('namee' => 'judas');
        $this->db->insert('abstract_table', $params);
    }

    public function testUpdate(){
        $params = array('name' => 'San Juan');
        $this->db->update('abstract_table', $params, 'id = 1');
        $data = $this->db->find('abstract_table', 1);
        $this->assertEquals('San Juan', $data['NAME']);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp #Debe ingresar una condicion para actualizar#
     */
    public function testUpdateWithOutWhere(){
        $params = array('name' => 'San Juan');
        $this->db->update('abstract_table', $params, null);
        $data = $this->db->find('user', 1);
        $this->assertEquals('San Juan', $data['name']);
    }
}
