<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160705055554 extends AbstractMigration
{
    public static $description = "Add fields celular,nivel_estudios BNF_DM_Met_Cliente_Preguntas Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_DM_Met_Cliente_Preguntas` ADD  `celular` VARCHAR(45) NULL AFTER  `distrito_trabaja` ,
                      ADD  `nivel_estudios` VARCHAR(45) NULL AFTER  `celular` ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
