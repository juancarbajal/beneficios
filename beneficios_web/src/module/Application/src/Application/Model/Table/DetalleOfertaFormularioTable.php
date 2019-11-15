<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 04/11/15
 * Time: 07:49 PM
 */

namespace Application\Model\Table;

use Application\Model\DetalleOfertaFormulario;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class DetalleOfertaFormularioTable
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

    public function saveDetalleOfertaFormulario(DetalleOfertaFormulario $busqueda)
    {
        $data = array(
            'BNF_OfertaFormulario_id' => $busqueda->BNF_OfertaFormulario_id,
            'Descripcion' => $busqueda->Descripcion,
        );
        $this->tableGateway->insert($data);
        return true;
    }

    public function deleteOfertaFormulario($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }

    public function getDescripcionFormulario($id)
    {
        $select = new Select();
        $select->from('BNF_Formulario');
        $select->columns(array('Descripcion'));
        $select->join('BNF_OfertaFormulario', 'BNF_Formulario.id = BNF_OfertaFormulario.BNF_Formulario_id', array());
        $select->where
            ->equalTo('BNF_OfertaFormulario.id', $id);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        foreach ($resultSet as $dato) {
            $resultSet = $dato->Descripcion;
        }
        return $resultSet;
    }
}
