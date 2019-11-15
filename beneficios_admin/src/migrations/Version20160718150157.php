<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150157 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD COLUMN `BNF2_Asignacion_Puntos_id` INT NULL AFTER `PuntosUtilizados`,
            ADD INDEX `fk_BNF2_Cupon_Puntos_8_idx` (`BNF2_Asignacion_Puntos_id` ASC);
            ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD CONSTRAINT `fk_BNF2_Cupon_Puntos_8`
              FOREIGN KEY (`BNF2_Asignacion_Puntos_id`)
              REFERENCES `BNF2_Asignacion_Puntos` (`id`)
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
