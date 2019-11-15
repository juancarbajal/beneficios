<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111322 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_BannersTienda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_BannersTienda`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NOT NULL COMMENT '' ;

            UPDATE `BNF_BannersTienda`
            SET `BNF_BannersTienda`.Eliminado = case `BNF_BannersTienda`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
