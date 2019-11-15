<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 02:46 PM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaTable
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

    public function getOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Oferta $id");
        }
        return $row;
    }


    public function getOfertaEmpresa($idOferta)
    {

        try{}
        catch(\Exception $ex){var_dump($ex->getMessage());exit;}
        $id = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->join(
            'BNF_OfertaEmpresaCliente',
            'BNF_OfertaEmpresaCliente.BNF_Oferta_id = BNF_Oferta.id',
            array()
        );
              $select->join(
                 'BNF_Empresa',
                  'BNF_Empresa.id = BNF_OfertaEmpresaCliente.BNF_Empresa_id',
                  array(

                  )
              );

        $select->where->equalTo('BNF_Oferta.id', $id)
            ->AND->equalTo('BNF_OfertaEmpresaCliente.Eliminado', 0);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }


    public function getOfertaBySlug($slug)
    {
        $rowset = $this->tableGateway->select(array('Slug' => $slug));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getOfertaName($id, $name)
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->join('BNF_OfertaUbigeo', 'BNF_OfertaUbigeo.BNF_Oferta_id = BNF_Oferta.id', array());
        $select->where
            ->like('Titulo', '%' . $name . '%')
            ->and
            ->equalTo('BNF_Oferta.Eliminado', '0')
            ->and
            ->equalTo('BNF_OfertaUbigeo.BNF_Ubigeo_id', $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getLogoEmpresaXOferta($segmento, $ubigeo, $empresa, $subgrupo)
    {
        $select = new Select();
        $select->from(array('O' => 'BNF_Oferta'));
        $select->columns(array());
        $select->join(
            array('OEC' => 'BNF_OfertaEmpresaCliente'),
            'O.id = OEC.BNF_Oferta_id',
            array()
        );
        $select->join(
            array('EP' => 'BNF_Empresa'),
            'EP.id = O.BNF_BolsaTotal_Empresa_id',
            array('Nombre' => 'Logo', 'Slug')
        );
        //Segmento
        if ($segmento > 0) {
            $select->join(
                array('OS' => 'BNF_OfertaSegmento'),
                'OS.BNF_Oferta_id = O.id',
                array()
            );
        }
        //Subgrupo
        if ($subgrupo > 0) {
            $select->join(
                array('OSG' => 'BNF_OfertaSubgrupo'),
                "OSG.BNF_Oferta_id = O.id",
                array()
            );
        }
        $select->join(
            array('OU' => 'BNF_OfertaUbigeo'),
            'OU.BNF_Oferta_id = O.id',
            array()
        );

        $select->where->greaterThan('O.Stock', 0)
            ->and->equalTo('O.Estado', 'Publicado')
            ->and->literal('IFNULL( Timestampdiff(day,CURDATE(),O.FechaFinPublicacion) ,1) >= 1')
            ->and->literal('Timestampdiff(day,O.FechaInicioPublicacion,CURDATE()) >= 0')
            ->and->equalTo('OEC.Eliminado', 0)
            ->and->equalTo('OEC.BNF_Empresa_id', $empresa)
            ->and->equalTo('EP.Proveedor', 1)
            ->and->equalTo('OU.Eliminado', 0)
            ->and->equalTo('OU.BNF_Ubigeo_id', $ubigeo);

        if ($subgrupo > 0) {
            $select->where->equalTo('OS.Eliminado', 0)
                ->and->equalTo('OS.BNF_Segmento_id', $segmento);
        }
        if ($subgrupo > 0) {
            $select->where->equalTo('OSG.Eliminado', 0)
                ->and->equalTo('OSG.BNF_Subgrupo_id', $subgrupo);
        }

        $select->group('EP.id');
        //$select->order('O.Premium DESC');
        //$select->order('O.FechaCreacion DESC');

        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getImagenCupon($idOferta)
    {
        $id = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->join(
            'BNF_Imagen',
            'BNF_Imagen.BNF_Oferta_id = BNF_Oferta.id',
            array(
                'imagenOferta' => 'Nombre',
                'Principal'
            )
        );
        $select->where
            ->equalTo('BNF_Oferta.id', $idOferta);
        $select->order('BNF_Imagen.Principal DESC');
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function updateOferta($data, $id)
    {
        if ($this->getOferta($id)) {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->update($data, array('id' => $id));
        } else {
            throw new \Exception('La Oferta no existe');
        }
        return $id;
    }

    public function getCorreoContactoEmpresa($id)
    {
        $select = new Select();
        $select->from('BNF_Empresa');
        $select->columns(array('CorreoContacto'));
        $select->where
            ->equalTo('id', $id);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        foreach ($resultSet as $dato) {
            $resultSet = $dato->CorreoContacto;
        }
        return $resultSet;
    }
}
