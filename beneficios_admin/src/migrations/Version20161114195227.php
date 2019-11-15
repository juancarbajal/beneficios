<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161114195227 extends AbstractMigration
{
    public static $description = "CHANGE FechaVigencia table BNF_Oferta_Atributos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Oferta_Atributos` CHANGE `FechaVigencia` `FechaVigencia` DATE NULL;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
