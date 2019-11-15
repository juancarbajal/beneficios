<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160413114849 extends AbstractMigration
{
    public static $description = "Update BNF_Tarjetas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Tarjetas` 
            SET 
                `Descripcion` = 'Tarjeta Provis',
                `Imagen` = 'Tarjeta_Provis.png',
                `Eliminado` = 0
            WHERE
                `id` = 1;
                
            UPDATE `BNF_Tarjetas` 
            SET 
                `Descripcion` = 'Tarjeta Plata Compras',
                `Imagen` = 'Tarjeta_Plata_Compras.png',
                `Eliminado` = 0
            WHERE
                `id` = 2;
            
            UPDATE `BNF_Tarjetas` 
            SET 
                `Descripcion` = 'Tarjeta Plata Clásica',
                `Imagen` = 'Tarjeta_Plata_Clasica.png',
                `Eliminado` = 0
            WHERE
                `id` = 3;
            
            UPDATE `BNF_Tarjetas` 
            SET 
                `Descripcion` = 'Tarjeta Unique 1',
                `Imagen` = 'Tarjeta_Unique_01.png',
                `Eliminado` = 0
            WHERE
                `id` = 4;
            
            UPDATE `BNF_Tarjetas` 
            SET 
                `Descripcion` = 'Tarjeta Unique 2',
                `Imagen` = 'Tarjeta_Unique_02.png',
                `Eliminado` = 0
            WHERE
                `id` = 5;
                
            UPDATE `BNF_Tarjetas` SET `Eliminado` = 1 WHERE `id` = 6;
            UPDATE `BNF_Tarjetas` SET `Eliminado` = 1 WHERE `id` = 7;
            UPDATE `BNF_Tarjetas` SET `Eliminado` = 1 WHERE `id` = 8;
            UPDATE `BNF_Tarjetas` SET `Eliminado` = 1 WHERE `id` = 9;
            UPDATE `BNF_Tarjetas` SET `Eliminado` = 1 WHERE `id` = 10;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
