<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160322104951 extends AbstractMigration
{
    public static $description = "add field BNF_ClienteCorreo_id BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Cupon` ADD `BNF_ClienteCorreo_id` INT NULL AFTER `BNF_Categoria_id`;
              ALTER TABLE `BNF_Cupon` ADD  FOREIGN KEY (`BNF_ClienteCorreo_id`) REFERENCES `BNF_ClienteCorreo`(`id`)
               ON DELETE RESTRICT ON UPDATE RESTRICT;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("ALTER TABLE BNF_Cupon DROP FOREIGN KEY BNF_Cupon_ibfk_1;
              ALTER TABLE `BNF_Cupon` DROP `BNF_ClienteCorreo_id`;");
    }
}
