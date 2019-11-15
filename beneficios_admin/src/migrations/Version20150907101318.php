<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150907101318 extends AbstractMigration
{
    public static $description = "Seed BNF_Ubigeo";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO BNF_Ubigeo
	        VALUES 	(1,'La Libertad',NULL,1),
			(2,'Trujillo',1,1),
            (3,'Pacasmayo',1,1),
            (4,'Chepen',1,1),
			(5,'Trujillo',2,1),
            (6,'Huanchaco',2,1),
            (7,'Esperanza',2,1),
            (8,'Laredo',2,1);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
