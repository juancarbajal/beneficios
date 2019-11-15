<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160705055553 extends AbstractMigration
{
    public static $description = "Add fields Pregunta09,Pregunta10,FechaPregunta09,FechaPregunta10 BNF_Preguntas Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_Preguntas` ADD  `Pregunta09` TEXT NULL AFTER  `Pregunta08` ,
                      ADD  `Pregunta10` TEXT NULL AFTER  `Pregunta09` ;
                      ALTER TABLE  `BNF_Preguntas` ADD  `FechaPregunta09` DATETIME NULL ,
                      ADD  `FechaPregunta10` DATETIME NULL ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
