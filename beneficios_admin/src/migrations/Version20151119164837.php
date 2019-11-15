<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151119164837 extends AbstractMigration
{
    public static $description = "Add field StockInicial BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_Oferta` ADD  `StockInicial` INT NOT NULL AFTER  `Stock` ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
