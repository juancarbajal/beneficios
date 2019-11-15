<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/11/15
 * Time: 06:35 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\OfertaFormulario;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaFormularioTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getOfertaFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Busqueda $id");
        }
        return $row;
    }

    public function getOfertaFormularioXOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaFormularioXOfertaData($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        if (!$rowset) {
            return false;
        }
        return $rowset;
    }

    public function saveOfertaFormulario(OfertaFormulario $busqueda)
    {
        $data = array(
            'BNF_Oferta_id' => $busqueda->BNF_Oferta_id,
            'BNF_Formulario_id' => $busqueda->BNF_Formulario_id,
            'Descripcion' => $busqueda->Descripcion,
            'Activo' => $busqueda->Activo,
            'Requerido' => $busqueda->Requerido
        );
        $id = (int)$busqueda->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaFormulario($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Formulario no existe');
            }
        }
        return $id;
    }

    public function updateOfertaFormulario(OfertaFormulario $busqueda)
    {
        $data = array(
            'Descripcion' => $busqueda->Descripcion,
            'Activo' => $busqueda->Activo,
            'Requerido' => $busqueda->Requerido
        );
        $id = (int)$busqueda->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaFormulario($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Formulario no existe');
            }
        }
        return $id;
    }

    public function deleteOfertaFormulario($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getFormularios($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaFormulario');
        $select->columns(
            array('id', 'Activo', 'Requerido', 'BNF_Formulario_id', 'BNF_Oferta_id', 'valor' => 'Descripcion')
        );
        $select->join('BNF_Oferta', 'BNF_OfertaFormulario.BNF_Oferta_id = BNF_Oferta.id', array());
        $select->join(
            'BNF_Formulario',
            'BNF_OfertaFormulario.BNF_Formulario_id = BNF_Formulario.id',
            array('Descripcion')
        );
        $select->where
            ->equalTo('BNF_OfertaFormulario.BNF_Oferta_id', $id)
            ->and
            ->equalTo('BNF_OfertaFormulario.Eliminado', '0');
        $select->order('BNF_Formulario.Posicion ASC');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function setActivo($id, $val)
    {
        $sql = "UPDATE `BNF_OfertaFormulario` SET `Activo`='" . $val . "' WHERE `BNF_Oferta_id` = " . $id;

        $dbAdapter = $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($sql);
        $statement->execute();
        return true;
    }

    public function updateOfertaFormularioXOferta(OfertaFormulario $busqueda)
    {
        $data = array(
            'Descripcion' => $busqueda->Descripcion,
            'Activo' => $busqueda->Activo
        );
        $BNF_Oferta_id = (int)$busqueda->BNF_Oferta_id;
        $BNF_Formulario_id = (int)$busqueda->BNF_Formulario_id;

        if ($this->getOfertaFormularioXOferta($BNF_Oferta_id)) {
            $this->tableGateway->update(
                $data,
                array('BNF_Oferta_id' => $BNF_Oferta_id, 'BNF_Formulario_id' => $BNF_Formulario_id)
            );
        } else {
            throw new \Exception('El Formulario no existe');
        }
        return true;
    }
}
