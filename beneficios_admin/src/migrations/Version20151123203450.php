<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151123203450 extends AbstractMigration
{
    public static $description = "Seed BNF_Configuraciones";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_Configuraciones` VALUES
        (NULL,'terminoscondicioneslead','Los datos ingresados son legítimos y correctos.
          Conozco el uso que se le dará a los datos que he proporcionado a Beneficios. Entiendo que los Proveedores son
          los únicos responsables por cualquier inconveniente con losbienes y servicios ofrecidos a través
           de la Página Web. Sé cual es la información y datos personales requeridos para poder acceder a los
           bienes yservicios ofrecidos a través de la Página Web. He autorizado a mi Empleador a brindar o he
           brindado directamente a Beneficios mis datos personalespara poder ingresar a la Pagina Web.\r\n',NULL,NULL),
           (NULL,'textobannerlead','¿Quieres recibir nuestras mejores ofertas y promociones directamente en tu correo?
           Llena tus datos y serás el primero en conocer nuestras ofertas.',NULL,NULL);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
