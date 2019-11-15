<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160211110317 extends AbstractMigration
{
    public static $description = "Seed BNF_OfertaFormulario table ";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO BNF_OfertaFormulario
                (`BNF_Oferta_id`,`BNF_Formulario_id`,`Descripcion`)
                SELECT
                    BNF_Oferta_id, 13, NULL
                FROM
                    BNF_OfertaFormulario
                GROUP BY BNF_Oferta_id;
                ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
