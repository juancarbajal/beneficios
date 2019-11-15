<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150902123405 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoPaquete";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_TipoPaquete`
            VALUES (1, 'Descarga'), (2, 'Presencia'), (3, 'Lead');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
