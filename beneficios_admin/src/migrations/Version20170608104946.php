<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170608104946 extends AbstractMigration
{
    public static $description = "Alter BNF_OfertaFormCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "
            
            ALTER TABLE `BNF_OfertaFormCliente` 
ADD COLUMN `BNF_Oferta_Atributo_id` INT(11) NULL AFTER `FechaActualizacion`,
ADD COLUMN `BNF_OfertaEmpresaCliente_id` INT(11) NULL AFTER `BNF_Oferta_Atributo_id`;

"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("

            ALTER TABLE `BNF_OfertaFormCliente` 
            DROP COLUMN `BNF_Oferta_Atributo_id`;
            
            ALTER TABLE `BNF_OfertaFormCliente` 
            DROP COLUMN `BNF_OfertaEmpresaCliente_id`;

");
    }
}
