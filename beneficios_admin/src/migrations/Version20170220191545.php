<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170220191545 extends AbstractMigration
{
    public static $description = "DROP TABLES";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "DROP TABLE BNF_Referido;
              DROP TABLE BNF_Cliente_Landing;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
