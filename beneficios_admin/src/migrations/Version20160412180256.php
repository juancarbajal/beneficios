<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412180256 extends AbstractMigration
{
    public static $description = "Create BNF_Tarjetas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Tarjetas` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `Descripcion` VARCHAR(255) NOT NULL,
              `Imagen` VARCHAR(100) NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`));"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
