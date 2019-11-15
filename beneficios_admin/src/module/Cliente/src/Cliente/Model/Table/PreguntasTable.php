<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/12/15
 * Time: 06:17 PM
 */

namespace Cliente\Model\Table;

use Cliente\Model\Preguntas;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PreguntasTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function savePreguntasConMasDatos(Preguntas $preguntas)
    {
        $data = array(
            'BNF_Cliente_id' => $preguntas->BNF_Cliente_id,
            'Pregunta01' => $preguntas->Pregunta01,
            'Pregunta02' => $preguntas->Pregunta02,
            'Pregunta03' => $preguntas->Pregunta03,

        );

        $id = (int)$preguntas->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaCliente($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Cliente id no existe');
            }
        }
    }


    public function savePreguntas(Preguntas $preguntas)
    {
        $data = array(
            'BNF_Cliente_id' => $preguntas->BNF_Cliente_id
        );

        $id = (int)$preguntas->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getEmpresaCliente($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Cliente id no existe');
            }
        }
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function searchByDoc($doc)
    {
        $select = new Select();
        $select->from("BNF_Preguntas");
        $select->columns(array("id", "BNF_Cliente_id"));
        $select->join(
            "BNF_Cliente",
            "BNF_Cliente.id = BNF_Preguntas.BNF_Cliente_id",
            array()
        );
        $select->where->equalTo('NumeroDocumento', $doc);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
