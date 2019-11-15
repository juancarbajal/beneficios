<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 12/01/16
 * Time: 10:44 AM
 */

namespace Reportes\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class DmMetClienteTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function getDataCliente($array, $list_categorias_ids, $empresa_id, $fechaInicio, $fechaFin)
    {
        $id = (int)$empresa_id;
        $consulta_estatica = array(
            'DiasUltimoLogin',
            'Descargas' => new Expression("SUM(IF(EstadoCupon = 'Caducado',1,0) + IF(EstadoCupon = 'Generado',1,0)"
                . " + IF(TipoOferta = 'Lead',1,0))"),
            'Redimidos' => new Expression("SUM(IF(EstadoCupon = 'Redimido',1,0))"),
            'DesCatBus' => new Expression("SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'bus' AND `TipoOferta` = 'Lead',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatCom' => new Expression("SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'com' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatCam' => new Expression("SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'cam' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatTie' => new Expression("SUM(IF(`BNF_Categoria_id` = 'tei' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'tie' AND `TipoOferta` = 'Lead',1,0))"),
            'RedCatBus' => new Expression("SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Redimido',1,0) +"
                . "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatCom' => new Expression("SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatCam' => new Expression("SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatTie' => new Expression("SUM(IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Redimido',1,0))"),
            'Edad',
            'Genero',
            'Nombre' => 'nombres',
            'Apellido' => 'apellidos',
            'distrito_vive',
            'distrito_trabaja'
        );
        $consulta_dim = array();
        foreach ($list_categorias_ids as $data) {
            $consulta_dim['DesCat' . $data->id] = new Expression(
                "SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$data->id' AND `TipoOferta` = 'Lead',1,0))"
            );
            $consulta_dim['RedCat' . $data->id] = new Expression(
                "SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Redimido',1,0))"
            );
        }
        $consulta =  array_merge($consulta_dim, $consulta_estatica);
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns($consulta);
        $select->join(
            array('C' => 'BNF_Cliente'),
            'D.BNF_Cliente_id = C.id',
            array('FechaCreacion', 'NumeroDocumento')
        );
        $select->join(array('E' => 'BNF_DM_Dim_EstadoCivil'), 'D.BNF_DM_Dim_EstadoCivil_id = E.id', array('estado'));
        $select->join(array('H' => 'BNF_DM_Dim_Hijos'), 'D.BNF_DM_Dim_Hijos_id = H.id', array('hijos'));

        if ($empresa_id != '') {
            $select->where->in('C.NumeroDocumento', $array)
                ->and->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        } else {
            $select->where->in('C.NumeroDocumento', $array)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        }

        $select->group('C.id');
        /*$query = $select->getSqlString();
                echo str_replace('"', '', $query);
                exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getDescargasOrRedimidos($empresa_id, $fechaInicio, $fechaFin)
    {
        $id = (int)$empresa_id;
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns(
            array(
                'Descargas' => new Expression("SUM(IF(EstadoCupon = 'Caducado',1,0) + IF(EstadoCupon = 'Generado',1,0)"
                    . " + IF(TipoOferta = 'Lead',1,0))"),
                'Redimidos' => new Expression("SUM(IF(EstadoCupon = 'Redimido',1,0))"),
            )
        );

        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        } else {
            $select->where
                ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        }

        $resultSet = $this->tableGateway->selectWith($select);
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/

        return $resultSet->current();
    }

    public function getDataDescargasRedimidos($empresa_id, $list_categorias_ids, $fechaInicio, $fechaFin)
    {
        $id = (int)$empresa_id;
        $consulta_estatica = array(
            'DesCatBus' => new Expression("SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'bus' AND `TipoOferta` = 'Lead',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatCom' => new Expression("SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'com' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatCam' => new Expression("SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'cam' AND `TipoOferta` = 'Lead',1,0))"),
            'DesCatTie' => new Expression("SUM(IF(`BNF_Categoria_id` = 'tei' AND `EstadoCupon` = 'Caducado',1,0) +"
                . "IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = 'tie' AND `TipoOferta` = 'Lead',1,0))"),
            'RedCatBus' => new Expression("SUM(IF(`BNF_Categoria_id` = 'bus' AND `EstadoCupon` = 'Redimido',1,0) +"
                . "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatCom' => new Expression("SUM(IF(`BNF_Categoria_id` = 'com' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatCam' => new Expression("SUM(IF(`BNF_Categoria_id` = 'cam' AND `EstadoCupon` = 'Redimido',1,0))"),
            'RedCatTie' => new Expression("SUM(IF(`BNF_Categoria_id` = 'tie' AND `EstadoCupon` = 'Redimido',1,0))"),
        );
        $consulta_dim = array();
        foreach ($list_categorias_ids as $data) {
            $consulta_dim['DesCat' . $data->id] = new Expression(
                "SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                "IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Generado',1,0) + " .
                "IF(`BNF_Categoria_id` = '$data->id' AND `TipoOferta` = 'Lead',1,0))"
            );
            $consulta_dim['RedCat' . $data->id] = new Expression(
                "SUM(IF(`BNF_Categoria_id` = '$data->id' AND `EstadoCupon` = 'Redimido',1,0))"
            );
        }
        $consulta =  array_merge($consulta_dim, $consulta_estatica);

        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns($consulta);

        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        } else {
            $select->where
                ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"))
                ->or->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        }
        /*$query = $select->getSqlString();
                echo str_replace('"', '', $query);
                exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet->current();
    }

    public function getDescargasRedimidos($empresa_id, $fechaInicio, $fechaFin, $categoria_id)
    {
        $id = (int)$empresa_id;
        $categoria_id = ($categoria_id == 0) ? 'bus' : $categoria_id;
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        if ($categoria_id == 'bus') {
            $select->columns(
                array(
                    'Redimidos' => new Expression(
                        "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Redimido',1,0) + " .
                        "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Redimido',1,0))"
                    )
                )
            );
        } else {
            $select->columns(
                array(
                    'Redimidos' => new Expression(
                        "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Redimido',1,0))"
                    )
                )
            );
        }
        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id);
        }
        $select->where
            ->between('D.FechaRedimido', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        /* $query = $select->getSqlString();
                 echo str_replace('"', '', $query);
                 exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet->current();
    }

    public function getDescargasCategoria($empresa_id, $fechaInicio, $fechaFin, $categoria_id)
    {
        $id = (int)$empresa_id;
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        if ($categoria_id == 'bus') {
            $select->columns(
                array(
                    'Descargas' => new Expression(
                        "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                        "IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Generado',1,0) + " .
                        "IF(`BNF_Categoria_id` = '$categoria_id' AND `TipoOferta` = 'Lead',1,0) + " .
                        "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Caducado',1,0) +" .
                        "IF(`BNF_Categoria_id` = '0' AND `EstadoCupon` = 'Generado',1,0) + " .
                        "IF(`BNF_Categoria_id` = '0' AND `TipoOferta` = 'Lead',1,0))"
                    )
                )
            );
        } else {
            $select->columns(
                array(
                    'Descargas' => new Expression(
                        "SUM(IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Caducado',1,0) +" .
                        "IF(`BNF_Categoria_id` = '$categoria_id' AND `EstadoCupon` = 'Generado',1,0) + " .
                        "IF(`BNF_Categoria_id` = '$categoria_id' AND `TipoOferta` = 'Lead',1,0))"
                    )
                )
            );
        }
        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id);
        }
        $select->where
            ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        /* $query = $select->getSqlString();
                 echo str_replace('"', '', $query);
                 exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet->current();
    }

    public function getListClientesId($fechaInicio, $fechaFin)
    {
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns(array('BNF_Cliente_id'));
        $select->where
            ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        $select->group('BNF_Cliente_id');
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getDataRubros($list_rubros_ids, $empresa_id, $fechaInicio, $fechaFin)
    {
        $id = (int)$empresa_id;
        $consulta_estatica = array(
            'ClienteCorreo' =>  new Expression("CONCAT(BNF_Cliente_id,'-',Correo)"),
            'Correo',
            'Nombre' => 'nombres',
            'Apellido' => 'apellidos',
            'FechaGenerado',
            'Edad',
            'Genero',
            'distrito_vive',
            'distrito_trabaja'
        );
        $consulta_dim = array();
        foreach ($list_rubros_ids as $data) {
            $consulta_dim['Rubro' . $data->id] = new Expression("SUM(IF(`BNF_Rubro_id` = '" . $data->id . "',1,0))");
        }

        $consulta =  array_merge($consulta_dim, $consulta_estatica);
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns($consulta);
        $select->join(
            array('C' => 'BNF_Cliente'),
            'D.BNF_Cliente_id = C.id',
            array('FechaCreacion', 'NumeroDocumento')
        );
        $select->join(array('E' => 'BNF_DM_Dim_EstadoCivil'), 'D.BNF_DM_Dim_EstadoCivil_id = E.id', array('estado'));
        $select->join(array('H' => 'BNF_DM_Dim_Hijos'), 'D.BNF_DM_Dim_Hijos_id = H.id', array('hijos'));

        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        } else {
            $select->where
                ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        }

        $select->group('ClienteCorreo');
        $select->order('FechaGenerado DESC');

        /*$query = $select->getSqlString();
                echo str_replace('"', '', $query);
                exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    public function getDescargaRubros($list_rubros_ids, $empresa_id, $fechaInicio, $fechaFin)
    {
        $id = (int)$empresa_id;

        $consulta_dim = array();
        foreach ($list_rubros_ids as $data) {
            $consulta_dim['Rubro' . $data->id] = new Expression("SUM(IF(`BNF_Rubro_id` = '" . $data->id . "',1,0))");
        }

        $consulta =  $consulta_dim;
        $select = new Select();
        $select->from(array('D' => 'BNF_DM_Met_Cliente'));
        $select->columns($consulta);

        if ($empresa_id != '') {
            $select->where->equalTo('D.BNF_DM_Dim_Empresa_id', $id)
                ->and->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        } else {
            $select->where
                ->between('D.FechaGenerado', $fechaInicio, new Expression("ADDDATE('$fechaFin', INTERVAL 1 DAY)"));
        }

        $select->where->isNull('D.FechaRedimido');

        /*$query = $select->getSqlString();
                echo str_replace('"', '', $query);
                exit;*/
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }
}
