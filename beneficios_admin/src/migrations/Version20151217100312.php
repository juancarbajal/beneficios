<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151217100312 extends AbstractMigration
{
    public static $description = "Create BNF_Preguntas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Preguntas` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `BNF_Cliente_id` INT NOT NULL COMMENT '',
              `Pregunta01` TEXT NULL COMMENT '',
              `Pregunta02` TEXT NULL COMMENT '		',
              `Pregunta03` TEXT NULL COMMENT '',
              `Pregunta04` TEXT NULL COMMENT '',
              `Pregunta05` TEXT NULL COMMENT '',
              `Pregunta06` TEXT NULL COMMENT '',
              `Pregunta07` TEXT NULL COMMENT '',
              `Pregunta08` TEXT NULL COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              INDEX `fk_BNF_Preguntas_1_idx` (`BNF_Cliente_id` ASC)  COMMENT '',
              CONSTRAINT `fk_BNF_Preguntas_1`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
