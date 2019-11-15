<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 08/02/16
 * Time: 10:21 AM
 */

namespace Reportes\Model\Table;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class DmMetClientePreguntasTable
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

    public function getEdades($empresa_id = '', $array = array())
    {
        $id = (int)$empresa_id;

        $select = new Select();
        $select->from(array('DMC' => 'BNF_DM_Met_Cliente_Preguntas'));
        $select->columns(array('Cantidad' => new Expression('COUNT(*)')));
        $select->join(array('DDE' => 'BNF_DM_Dim_Edad'), 'DMC.BNF_DM_Dim_Edad_id = DDE.id', array('id'));
        if ($empresa_id != '') {
            $select->where->equalTo('DMC.BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('DMC.BNF_Cliente_id', $array);
        }
        $select->group('DDE.id');
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEstadoCivil($empresa_id = '', $array = array())
    {
        $id = (int)$empresa_id;

        $select = new Select();
        $select->from(array('DMC' => 'BNF_DM_Met_Cliente_Preguntas'));
        $select->columns(array('Cantidad' => new Expression('COUNT(*)')));
        $select->join(array('DDE' => 'BNF_DM_Dim_EstadoCivil'), 'DMC.BNF_DM_Dim_EstadoCivil_id = DDE.id', array('id'));
        if ($empresa_id != '') {
            $select->where->equalTo('DMC.BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('DMC.BNF_Cliente_id', $array);
        }
        $select->group('DDE.id');
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getHijos($empresa_id = '', $array = array())
    {
        $id = (int)$empresa_id;

        $select = new Select();
        $select->from(array('DMC' => 'BNF_DM_Met_Cliente_Preguntas'));
        $select->columns(
            array(
                'NoDef' => new Expression("SUM(IF(BNF_DM_Dim_Hijos_id = 1,1,0))"),
                'NoHijos' => new Expression("SUM(IF(BNF_DM_Dim_Hijos_id = 2,1,0))"),
                'SiHijos' => new Expression("SUM(IF(BNF_DM_Dim_Hijos_id > 2,1,0))")
            )
        );

        if ($empresa_id != '') {
            $select->where->equalTo('DMC.BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('DMC.BNF_Cliente_id', $array);
        }
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getPreguntaCampo($empresa_id, $campo, $array = array())
    {
        $id = (int)$empresa_id;
        $select = new Select();
        $select->from("BNF_DM_Met_Cliente_Preguntas");
        $select->where->isNotNull($campo);
        if ($empresa_id != '') {
            $select->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('BNF_Cliente_id', $array);
        }

        $resultSet = $this->tableGateway->selectWith($select);

        $select2 = new Select();
        $select2->from("BNF_DM_Met_Cliente_Preguntas");
        $select2->where->isNull($campo);
        if ($empresa_id != '') {
            $select2->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select2->where->in('BNF_Cliente_id', $array);
        }

        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet2 = $this->tableGateway->selectWith($select2);

        return array($resultSet->count(), $resultSet2->count());
    }

    public function getPreguntaGenero($empresa_id, $array = array())
    {
        $id = (int)$empresa_id;
        $select = new Select();
        $select->from("BNF_DM_Met_Cliente_Preguntas");
        $select->where->equalTo('Genero', 'H');
        if ($empresa_id != '') {
            $select->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('BNF_Cliente_id', $array);
        }
        $resultSet = $this->tableGateway->selectWith($select);

        $select2 = new Select();
        $select2->from("BNF_DM_Met_Cliente_Preguntas");
        $select2->where->equalTo('Genero', 'M');
        if ($empresa_id != '') {
            $select2->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select2->where->in('BNF_Cliente_id', $array);
        }
        $resultSet2 = $this->tableGateway->selectWith($select2);

        $select3 = new Select();
        $select3->from("BNF_DM_Met_Cliente_Preguntas");
        $select3->where->isNull('Genero');
        if ($empresa_id != '') {
            $select3->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select3->where->in('BNF_Cliente_id', $array);
        }

        /*$query = $select2->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet3 = $this->tableGateway->selectWith($select3);

        return array($resultSet->count(), $resultSet2->count(), $resultSet3->count());
    }

    public function getPreguntaDistrito($empresa_id, $campo, $array = array())
    {
        $id = (int)$empresa_id;
        $select = new Select();
        $select->from("BNF_DM_Met_Cliente_Preguntas");
        $select->columns(array($campo, 'Cantidad' => new Expression('COUNT(*)')));
        $select->where->isNotNull($campo);
        if ($empresa_id != '') {
            $select->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select->where->in('BNF_Cliente_id', $array);
        }
        $select->group($campo);

        $resultSet = $this->tableGateway->selectWith($select);

        $select2 = new Select();
        $select2->from("BNF_DM_Met_Cliente_Preguntas");
        $select2->where->isNull($campo);
        if ($empresa_id != '') {
            $select2->where->equalTo('BNF_DM_Dim_Empresa_id', $id);
        }
        if ($array != array()) {
            $select2->where->in('BNF_Cliente_id', $array);
        }

        /*$query = $select2->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet2 = $this->tableGateway->selectWith($select2);

        return array($resultSet, $resultSet2->count());
    }
}
