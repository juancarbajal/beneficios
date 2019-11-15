<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160106165049 extends AbstractMigration
{
    public static $description = "Update data BNF_Configuraciones table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Configuraciones`
              SET `Atributo` = 'Ya enviamos tu cupón ¡Disfrútalo!', `FechaActualizacion` = NOW()
            WHERE `Campo` = 'mensajeproceso';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
