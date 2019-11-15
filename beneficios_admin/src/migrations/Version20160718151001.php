<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151001 extends AbstractMigration
{
    public static $description = "Alter BNF2_Asignacion_Puntos_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
            DROP FOREIGN KEY `fk_BNF2_Asignacion_Puntos_Estado_Log_4`;
            ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
            CHANGE COLUMN `BNF_Usuario_id` `BNF_Usuario_id` INT(11) NULL ;
            ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
            ADD CONSTRAINT `fk_BNF2_Asignacion_Puntos_Estado_Log_4`
              FOREIGN KEY (`BNF_Usuario_id`)
              REFERENCES `BNF_Usuario` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
