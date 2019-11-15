<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/08/15
 * Time: 08:57 AM
 */
namespace Usuario\Model;

class Usuario
{
    public $id;
    public $BNF_TipoUsuario_id;
    public $BNF_TipoDocumento_id;
    public $Nombres;
    public $Apellidos;
    public $NombreUsuario;
    public $Contrasenia;
    public $NumeroDocumento;
    public $Correo;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $FechaUltimoAcceso;
    public $NombreTipoUsuario;
    public $NombreTipoDocumento;
    public $BNF_Empresa_id;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_TipoUsuario_id = (!empty($data['BNF_TipoUsuario_id'])) ? $data['BNF_TipoUsuario_id'] : null;
        $this->BNF_TipoDocumento_id = (!empty($data['BNF_TipoDocumento_id'])) ? $data['BNF_TipoDocumento_id'] : null;
        $this->Nombres = (!empty($data['Nombres'])) ? ucwords(strtolower($data['Nombres'])) : null;
        $this->Apellidos = (!empty($data['Apellidos'])) ? ucwords(strtolower($data['Apellidos'])) : null;
        $this->Correo = (!empty($data['Correo'])) ? $data['Correo'] : null;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->NombreUsuario = (!empty($data['NombreUsuario'])) ? $data['NombreUsuario'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->NombreTipoUsuario = (!empty($data['NombreTipoUsuario'])) ? $data['NombreTipoUsuario'] : null;
        $this->Contrasenia = (!empty($data['Contrasenia'])) ? $data['Contrasenia'] : null;
        $this->NombreTipoDocumento = (!empty($data['NombreTipoDocumento'])) ? $data['NombreTipoDocumento'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->FechaUltimoAcceso = (!empty($data['FechaUltimoAcceso'])) ? $data['FechaUltimoAcceso'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
