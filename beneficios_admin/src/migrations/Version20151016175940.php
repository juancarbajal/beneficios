<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151016175940 extends AbstractMigration
{
    public static $description = "Create BNF_Banners table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Banners` (`id` int(11) NOT NULL AUTO_INCREMENT,
                `Imagen` varchar(100) NOT NULL,
                `Descripcion` varchar(100) NOT NULL,
                `Eliminado` enum('0','1') NOT NULL,
                PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
