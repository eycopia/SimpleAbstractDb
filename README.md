# SimpleAbstractDb
Una manera sencilla de trabajar con una base de datos.

Para comenzar a trabajar debe instanciar la clase con la que 
quiere trabajar, segun su gestor de base de datos; para este ejemplo Mysql.

`$db = new MysqlDB('host', 'user', 'password', 'database');`

Obtener los resultados de una consulta.
$query = "SELECT * FROM abstract_table";

Resultados en un array de objetos
`$db->query($query);
foreach ($db->fetch_all()  as $data){
    print_r($data);
}`

Resultados en un array de arrays
`$db->query($query);
foreach ($db->fetch_all_array()  as $data){
    print_r($data);
}`

Obtener una sola columna del query:
`$db->query($query);
$data = $db->getValues('name'); //array('juan', 'pedro')`


Puede revisar todas las funciones disponibles en la interface interfaceDB.php.
