<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/08/15
 * Time: 10:38 AM
 */

namespace Usuario\Model\Table;

use Usuario\Model\Usuario;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class UsuarioTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new Select();
        $select->from('BNF_Usuario');
        $select->join('BNF_TipoUsuario', 'BNF_Usuario.BNF_TipoUsuario_id = BNF_TipoUsuario.id', array());
        $select->where('BNF_TipoUsuario.Descripcion != "super"');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getUsuarioAsesor()
    {
        $select = new Select();
        $select->from('BNF_Usuario');
        $select->join('BNF_TipoUsuario', 'BNF_Usuario.BNF_TipoUsuario_id = BNF_TipoUsuario.id', array());
        $select->where('BNF_TipoUsuario.Nombre = "Asesor"');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getUsuarioDetail($nombre, $order_by = null, $order = null)
    {
        $select = new Select();
        $select->from('BNF_Usuario');
        $select->columns(array('*'));
        $select->join(
            'BNF_TipoUsuario',
            'BNF_Usuario.BNF_TipoUsuario_id = BNF_TipoUsuario.id',
            array('NombreTipoUsuario' => 'Nombre')
        );
        $select->join(
            'BNF_TipoDocumento',
            'BNF_Usuario.BNF_TipoDocumento_id = BNF_TipoDocumento.id',
            array('NombreTipoDocumento' => 'Nombre')
        );

        $select->where->notEqualTo('BNF_Usuario.BNF_TipoUsuario_id', 1);
        if ($nombre != '') {
            $select->where
                ->like('BNF_Usuario.Nombres', $nombre . '%')
                ->or
                ->like('BNF_Usuario.Apellidos', $nombre . '%')
                ->or
                ->like('BNF_Usuario.NumeroDocumento', $nombre . '%');
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("FechaCreacion $order");
        }
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getUsuario($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCorreo($correo)
    {
        $rowset = $this->tableGateway->select(array('Correo' => $correo));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function getDocumento($documento)
    {
        $rowset = $this->tableGateway->select(array('NumeroDocumento' => $documento));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function getCorreoId($correo, $id)
    {
        $rowset = $this->tableGateway->select(array('Correo' => $correo, 'id !=' . $id));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function getDocumentoId($documento, $id)
    {
        $rowset = $this->tableGateway->select(array('NumeroDocumento' => $documento, 'id !=' . $id));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return 1;
    }

    public function saveUsuario(Usuario $usuario)
    {
        $data = $usuario->getArrayCopy();
        unset($data['NombreTipoUsuario']);
        unset($data['NombreTipoDocumento']);
        if ($data['Contrasenia'] == null) {
            unset($data['Contrasenia']);
        } else {
            $bcrypt = new Bcrypt();
            $securePass = $bcrypt->create($data['Contrasenia']);
            $data['Contrasenia'] = $securePass;
        }

        $id = (int)$usuario->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsuario($id)) {
                unset($data['FechaCreacion']);
                unset($data['FechaUltimoAcceso']);
                unset($data['Eliminado']);
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Usuario id does not exist');
            }
        }
    }

    public function deleteUsuario($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function saveLastLogin($id)
    {
        $data['FechaUltimoAcceso'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    public function getUsuarioPorCorreo($correo)
    {
        $rowset = $this->tableGateway->select(array('Correo' => $correo, 'Eliminado' => 0));
        $row = $rowset->current();
        if (!$row) {
            return 0;
        }
        return $row;
    }
}
