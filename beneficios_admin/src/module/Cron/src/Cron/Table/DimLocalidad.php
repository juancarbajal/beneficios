<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/01/16
 * Time: 03:26 PM
 */

namespace Cron\Table;

use Zend\Db\Adapter\Adapter;

class DimLocalidad
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function delete()
    {
        $sql_str = "DELETE FROM BNF_DM_DIM_Localidad";
        $statement = $this->adapter->createStatement($sql_str);
        return $statement->execute();
    }

    public function insert()
    {
        $sql_str = "INSERT INTO `BNF_DM_DIM_Localidad` SELECT u.id, u.Nombre FROM `BNF_Ubigeo` u
                        INNER JOIN `BNF_OfertaUbigeo` ou ON ou.BNF_Ubigeo_id = u.id GROUP BY u.id";
        $statement = $this->adapter->createStatement($sql_str);
        return $statement->execute();
    }
}