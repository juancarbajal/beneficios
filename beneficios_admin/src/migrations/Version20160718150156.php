<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150156 extends AbstractMigration
{
    public static $description = "Seed BNF2_Oferta_Puntos_Rubro update";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD COLUMN `BNF_Rubro_id` INT NULL AFTER `BNF_Categoria_id`,
            ADD INDEX `fk_BNF2_Cupon_Puntos_7_idx` (`BNF_Rubro_id` ASC);
            ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD CONSTRAINT `fk_BNF2_Cupon_Puntos_7`
              FOREIGN KEY (`BNF_Rubro_id`)
              REFERENCES `BNF_Rubro` (`id`)
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
