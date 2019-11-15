<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151117164839 extends AbstractMigration
{
    public static $description = "Seed Departamentos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'AMAZONAS',`id_padre`= null WHERE `id`= 1;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'ANCASH',`id_padre`= null WHERE `id`= 2;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'APURIMAC',`id_padre`= null WHERE `id`= 3;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'AREQUIPA',`id_padre`= null WHERE `id`= 4;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'AYACUCHO',`id_padre`= null WHERE `id`= 5;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'CAJAMARCA',`id_padre`= null WHERE `id`= 6;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'CUSCO',`id_padre`= null WHERE `id`= 7;
            UPDATE `BNF_Ubigeo` SET `Nombre`= 'HUANCAVELICA',`id_padre`= null WHERE `id`= 8;
            INSERT INTO `BNF_Ubigeo`(`id`, `Nombre`, `id_padre`, `BNF_Pais_id`)
            VALUES (9,'HUANUCO', null, 1),(10,'ICA', null, 1),(11,'JUNIN', null, 1),(12,'LA LIBERTAD', null, 1),
            (13,'LAMBAYEQUE', null, 1),(14,'LIMA', null, 1),(15,'LORETO', null, 1),(16,'MADRE DE DIOS', null, 1),
            (17,'MOQUEGUA', null, 1),(18,'PASCO', null, 1),(19,'PIURA', null, 1),(20,'PUNO', null, 1),
            (21,'SAN MARTIN', null, 1),(22,'TACNA', null, 1),(23,'TUMBES', null, 1),(24,'CALLAO', 14, 1),
            (25,'UCAYALI', null, 1);
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
