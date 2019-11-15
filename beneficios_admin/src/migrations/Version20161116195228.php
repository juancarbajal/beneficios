<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161116195228 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF2_Cupon_Puntos` 
                    DROP FOREIGN KEY `fk_BNF2_Cupon_Puntos_8`;
                    ALTER TABLE `BNF2_Cupon_Puntos` 
                    DROP COLUMN `BNF2_Asignacion_Puntos_id`,
                    DROP INDEX `fk_BNF2_Cupon_Puntos_8_idx` ;
                    ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
