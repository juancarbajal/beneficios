<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120125 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD COLUMN `BNF2_Oferta_Puntos_Atributos_id` INT NULL DEFAULT NULL AFTER `BNF2_Oferta_Puntos_id`,
            ADD INDEX `fk_BNF2_Cupon_Puntos_6_idx` (`BNF2_Oferta_Puntos_Atributos_id` ASC);
            ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD CONSTRAINT `fk_BNF2_Cupon_Puntos_6`
              FOREIGN KEY (`BNF2_Oferta_Puntos_Atributos_id`)
              REFERENCES `BNF2_Oferta_Puntos_Atributos` (`id`)
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
