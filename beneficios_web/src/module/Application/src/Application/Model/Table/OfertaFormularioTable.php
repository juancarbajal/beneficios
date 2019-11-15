<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/11/15
 * Time: 06:35 PM
 */

namespace Application\Model\Table;

use Application\Model\OfertaFormulario;
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

    public function saveOfertaFormulario(OfertaFormulario $busqueda)
    {
        $data = array(
            'BNF_Oferta_id' => $busqueda->BNF_Oferta_id,
            'BNF_Formulario_id' => $busqueda->BNF_Formulario_id,
            'Descripcion' => $busqueda->Descripcion,
            'Activo' =>$busqueda->Activo
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
            'Activo' =>$busqueda->Activo
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

    public function getFormularios($slug)
    {
        $select = new Select();
        $select->from('BNF_OfertaFormulario');
        $select->columns(
            array('id', 'Activo', 'Requerido', 'BNF_Formulario_id', 'BNF_Oferta_id', 'valor' => 'Descripcion')
        );
        $select->join(
            'BNF_Oferta',
            'BNF_OfertaFormulario.BNF_Oferta_id = BNF_Oferta.id',
            array('oferta_id' => 'id', 'Stock','Estado')
        );
        $select->join('BNF_Empresa', 'BNF_Empresa.id = BNF_Oferta.BNF_BolsaTotal_Empresa_id', array('NombreComercial'));
        $select->join(
            'BNF_Formulario',
            'BNF_OfertaFormulario.BNF_Formulario_id = BNF_Formulario.id',
            array('Descripcion', 'id_form' => 'id')
        );
        $select->where
            ->equalTo('BNF_Oferta.Slug', $slug)
            ->and
            ->equalTo('BNF_OfertaFormulario.Eliminado', '0');
        $select->order('BNF_Formulario.Posicion ASC');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function setActivo($id, $val)
    {
        $sql="UPDATE `BNF_OfertaFormulario` SET `Activo`='".$val."' WHERE `BNF_Oferta_id` = ".$id;

        $dbAdapter =  $this->tableGateway->adapter->getDriver();
        $statement = $dbAdapter->createStatement($sql);
        $statement->execute();
        return true;
    }
}
