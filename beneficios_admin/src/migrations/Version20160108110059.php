<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160108110059 extends AbstractMigration
{
    public static $description = "Add fields BNF_Preguntas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Preguntas`
                ADD COLUMN `FechaPregunta01` DATETIME NULL AFTER `Pregunta08`,
                ADD COLUMN `FechaPregunta02` DATETIME NULL AFTER `FechaPregunta01`,
                ADD COLUMN `FechaPregunta03` DATETIME NULL AFTER `FechaPregunta02`,
                ADD COLUMN `FechaPregunta04` DATETIME NULL AFTER `FechaPregunta03`,
                ADD COLUMN `FechaPregunta05` DATETIME NULL AFTER `FechaPregunta04`,
                ADD COLUMN `FechaPregunta06` DATETIME NULL AFTER `FechaPregunta05`,
                ADD COLUMN `FechaPregunta07` DATETIME NULL AFTER `FechaPregunta06`,
                ADD COLUMN `FechaPregunta08` DATETIME NULL AFTER `FechaPregunta07`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
