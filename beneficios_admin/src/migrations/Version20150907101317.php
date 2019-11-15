<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150907101317 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoEmpresa";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO BNF_TipoEmpresa
                      VALUES (1,'Proveedor',NULL,NULL,NULL,0),
                      (2,'Cliente',NULL,NULL,NULL,0);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
