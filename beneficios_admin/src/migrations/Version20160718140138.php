<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718140138 extends AbstractMigration
{
    public static $description = "Alter BNF2_Oferta_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Oferta_Puntos` 
            DROP FOREIGN KEY `fk_BNF2_Oferta_Puntos_1`;
            ALTER TABLE `BNF2_Oferta_Puntos` 
            DROP COLUMN `BNF2_Segmento_id`,
            DROP INDEX `fk_BNF2_Oferta_Puntos_1_idx` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
