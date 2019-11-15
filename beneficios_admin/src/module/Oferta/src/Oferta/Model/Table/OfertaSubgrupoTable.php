<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/09/15
 * Time: 08:48 PM
 */

namespace Oferta\Model\Table;


use Oferta\Model\OfertaSubgrupo;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaSubgrupoTable
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

    public function getOfertaSubgrupo($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Subgrupo $id");
        }
        return $row;
    }

    public function getOfertaSubgrupoSeach($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $rowset = $this->tableGateway->select(
            array('BNF_Oferta_id = ' . $idOferta, 'BNF_Subgrupo_id = ' . $idSegmento)
        );
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Subgrupo");
        }
        return $row;
    }

    public function getOfertaSubgrupoExist($idOferta, $idSegmento)
    {
        $idOferta = (int)$idOferta;
        $idSegmento = (int)$idSegmento;
        $select = new Select();
        $select->from('BNF_OfertaSubgrupo');
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Subgrupo_id = ' . $idSegmento);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaSubgrupos($id)
    {
        $select = new Select();
        $select->from('BNF_OfertaSubgrupo');
        $select->where("BNF_Oferta_id = " . $id . " AND Eliminado = '0'");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaSubgruposEmp($id, $emp)
    {
        $select = new Select();
        $select->from('BNF_OfertaSubgrupo');
        $select->join('BNF_Subgrupo', 'BNF_OfertaSubgrupo.BNF_Subgrupo_id = BNF_Subgrupo.id', array());
        $select->where
            ->equalTo("BNF_Subgrupo.BNF_Empresa_id", $emp)
            ->and
            ->equalTo("BNF_Oferta_id", $id)
            ->and
            ->equalTo("BNF_OfertaSubgrupo.Eliminado", '0');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaSubgrupo(OfertaSubgrupo $ofertaSubgrupo)
    {
        $data = array(
            'BNF_Subgrupo_id' => $ofertaSubgrupo->BNF_Subgrupo_id,
            'BNF_Oferta_id' => $ofertaSubgrupo->BNF_Oferta_id,
            'Eliminado' => $ofertaSubgrupo->Eliminado,
        );
        $id = (int)$ofertaSubgrupo->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaSubgrupo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Subgrupo no existe');
            }
        }
        return $id;
    }

    public function deleteAllSubgrupos($id)
    {
        $data['Eliminado'] = '1';
        $id = (int)$id;
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }
}
