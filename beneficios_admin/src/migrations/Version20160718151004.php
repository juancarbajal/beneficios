<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151004 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos_Log` 
            DROP FOREIGN KEY `fk_BNF2_Cupon_Puntos_Log_5`;
            ALTER TABLE `BNF2_Cupon_Puntos_Log` 
            CHANGE COLUMN `CodigoCupon` `CodigoCupon` VARCHAR(45) CHARACTER SET 'utf8' NULL ,
            CHANGE COLUMN `BNF_Cliente_id` `BNF_Cliente_id` INT(11) NULL ;
            ALTER TABLE `BNF2_Cupon_Puntos_Log` 
            ADD CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_5`
              FOREIGN KEY (`BNF_Cliente_id`)
              REFERENCES `BNF_Cliente` (`id`)
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
