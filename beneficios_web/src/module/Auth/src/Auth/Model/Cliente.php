<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/10/15
 * Time: 05:08 PM
 */

namespace Auth\Model;

class Cliente
{
    public $id;
    public $BNF_TipoDocumento_id;
    public $Nombre;
    public $Apellido;
    public $NumeroDocumento;
    public $Genero;
    public $FechaNacimiento;
    public $Eliminado;
    public $idEmpresa;
    public $NombreComercial;
    public $NombreSegmento;
    public $NombreSubgrupo;
    public $TipoDocumento;
    public $Estado;
    public $ClaseEmpresaCliente;
    public $UltimaConexion;
    public $Correo;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_TipoDocumento_id = (!empty($data['BNF_TipoDocumento_id'])) ? $data['BNF_TipoDocumento_id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (!empty($data['Apellido'])) ? $data['Apellido'] : null;
        $this->TipoDocumento = (!empty($data['TipoDocumento'])) ? $data['TipoDocumento'] : null;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->Genero = (!empty($data['Genero'])) ? $data['Genero'] : null;
        $this->FechaNacimiento = (!empty($data['FechaNacimiento'])) ? $data['FechaNacimiento'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->idEmpresa = (!empty($data['idEmpresa'])) ? $data['idEmpresa'] : null;
        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
        $this->NombreSubgrupo = (!empty($data['NombreSubgrupo'])) ? $data['NombreSubgrupo'] : null;
        $this->ClaseEmpresaCliente = (!empty($data['ClaseEmpresaCliente'])) ? $data['ClaseEmpresaCliente'] : null;
        $this->UltimaConexion = (!empty($data['UltimaConexion'])) ? $data['UltimaConexion'] : null;
        $this->Correo = (!empty($data['Correo'])) ? $data['Correo'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
