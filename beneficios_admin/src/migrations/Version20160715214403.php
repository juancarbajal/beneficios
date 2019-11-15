<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160715214403 extends AbstractMigration
{
    public static $description = "Update column BNF_ClienteCorreo_id BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon` 
            DROP FOREIGN KEY `BNF_Cupon_ibfk_1`;
            ALTER TABLE `BNF_Cupon` 
            CHANGE COLUMN `BNF_ClienteCorreo_id` `BNF_ClienteCorreo_id` VARCHAR(255) NULL DEFAULT NULL ,
            DROP INDEX `BNF_ClienteCorreo_id` ;
            UPDATE `BNF_Cupon` AS cu 
            SET cu.`BNF_ClienteCorreo_id`= (SELECT Correo FROM BNF_ClienteCorreo WHERE id = cu.BNF_ClienteCorreo_id)");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
