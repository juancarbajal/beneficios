<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160108104003 extends AbstractMigration
{
    public static $description = "Seed BNF_Configuraciones table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Configuraciones` (`Campo`,`Atributo`,`FechaCreacion`)
              VALUES ('mensaje_proveedor','Le informamos que se ha realizado un total de 5 descargas de cupones de sus ofertas en nuestra web.',NOW()),
              ('total_redimidos',5,NOW());"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
