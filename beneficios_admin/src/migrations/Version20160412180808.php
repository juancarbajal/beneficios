<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412180808 extends AbstractMigration
{
    public static $description = "Seed BNF_Tarjetas";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 01', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 02', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 03', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 04', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 05', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 06', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 07', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 08', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 09', 'bankcards.png');
             INSERT INTO `BNF_Tarjetas` (`Descripcion`, `Imagen`) VALUES ('Tarjeta 10', 'bankcards.png');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
