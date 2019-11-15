<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120129 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            DROP FOREIGN KEY `fk_BNF2_Cupon_Puntos_1`;
            ALTER TABLE `BNF2_Cupon_Puntos` 
            DROP COLUMN `BNF2_OfertaEmpresaCliente_id`,
            ADD COLUMN `BNF2_Segmento_id` INT NULL AFTER `id`,
            ADD INDEX `fk_BNF2_Cupon_Puntos_7_idx` (`BNF2_Segmento_id` ASC),
            DROP INDEX `fk_BNF2_Cupon_Puntos_1_idx` ;
            ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD CONSTRAINT `fk_BNF2_Cupon_Puntos_7`
              FOREIGN KEY (`BNF2_Segmento_id`)
              REFERENCES `BNF2_Segmentos` (`id`)
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
