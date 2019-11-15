<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/11/15
 * Time: 05:55 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\Formulario;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class FormularioTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new Select();
        $select->from('BNF_Formulario');
        $select->where->equalTo('BNF_Formulario.Eliminado', '0');
        $select->order('BNF_Formulario.Posicion ASC');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Busqueda $id");
        }
        return $row;
    }

    public function saveFormulario(Formulario $busqueda)
    {
        $data = array(
            'Descripcion' => $busqueda->Descripcion
        );
        $id = (int)$busqueda->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getFormulario($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Formulario no existe');
            }
        }
        return $id;
    }

    public function deleteFormulario($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}
