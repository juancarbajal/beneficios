<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151207130557 extends AbstractMigration
{
    public static $description = "Add Indices 03";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta` 
            ADD INDEX `Stock_index` (`Stock` ASC)  COMMENT '',
            ADD INDEX `FechaInicioPublicacion_index` (`FechaInicioPublicacion` ASC)  COMMENT '',
            ADD INDEX `FechaFinPublicacion_index` (`FechaFinPublicacion` ASC)  COMMENT '',
            ADD INDEX `Estado_index` (`Estado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_Cliente` 
            ADD INDEX `NumeroDocumento_index` (`NumeroDocumento` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaEmpresaCliente` 
            ADD INDEX `OEC_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaSegmento` 
            ADD INDEX `OS_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaSubgrupo` 
            ADD INDEX `OSU_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_EmpresaTipoEmpresa` 
            ADD INDEX `ETE_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaUbigeo` 
            ADD INDEX `OU_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaCategoriaUbigeo` 
            ADD INDEX `OCU_Eliminado_index` (`Eliminado` ASC)  COMMENT '';
            
            ALTER TABLE `BNF_OfertaCampaniaUbigeo` 
            ADD INDEX `OCPU_Eliminado_index` (`Eliminado` ASC)  COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
