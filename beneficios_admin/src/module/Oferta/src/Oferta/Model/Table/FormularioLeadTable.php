<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 03:38 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\FormularioLead;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class FormularioLeadTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id));
        return $rowset;
    }

    public function getExistFormulario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getIfNameExist($oferta_id, $nombre)
    {
        $select = new Select();
        $select->from('BNF_FormularioLead');
        $select->where->like('Nombre_Campo', $nombre)
            ->AND->equalTo('BNF_Oferta_id', $oferta_id);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function saveFormulario(FormularioLead $formlead)
    {
        $data = array(
            'BNF_Oferta_id' => $formlead->BNF_Oferta_id,
            'Nombre_Campo' => $formlead->Nombre_Campo,
            'Tipo_Campo' => $formlead->Tipo_Campo,
            'Detalle' => $formlead->Detalle,
            'Requerido' => $formlead->Requerido,
            'Activo' => $formlead->Activo,
        );
        $id = (int)$formlead->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getFormulario($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
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
