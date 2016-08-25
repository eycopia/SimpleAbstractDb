drop table abstract_table;

CREATE TABLE abstract_table (
  id number(11) NOT NULL PRIMARY KEY,
  name varchar2(45) DEFAULT NULL,
  created date DEFAULT (sysdate)
);



INSERT INTO abstract_table (id, name) VALUES (1, 'juan');
INSERT INTO abstract_table (id, name) VALUES (2, 'pedro');

exit;
