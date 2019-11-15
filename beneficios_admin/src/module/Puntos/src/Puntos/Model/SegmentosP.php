<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 12:35 PM
 */

namespace Puntos\Model;

class SegmentosP
{
    public $id;
    public $BNF2_Campania_id;
    public $NombreSegmento;
    public $CantidadPuntos;
    public $CantidadPersonas;
    public $Subtotal;
    public $Comentario;
    public $Eliminado;
    public $FechaCreacion;
    public $FechaActualizacion;

    public $Empresa;
    public $Campania;
    public $AsignadoActivo;
    public $AsignadoEliminado;
    public $DisponibleAsignar;
    public $AplicadoActivo;
    public $AplicadoInactivo;
    public $RedimidoActivo;
    public $RedimidoInactivo;
    public $UsuariosAsignados;
    public $Tipo;
    public $EstadoCampania;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Campania_id = (!empty($data['BNF2_Campania_id'])) ? $data['BNF2_Campania_id'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
        $this->CantidadPuntos = (!empty($data['CantidadPuntos'])) ? $data['CantidadPuntos'] : null;
        $this->CantidadPersonas = (!empty($data['CantidadPersonas'])) ? $data['CantidadPersonas'] : null;
        $this->Subtotal = (!empty($data['Subtotal'])) ? $data['Subtotal'] : 0;
        $this->Comentario = (!empty($data['Comentario'])) ? $data['Comentario'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : 0;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;

        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->Campania = (!empty($data['Campania'])) ? $data['Campania'] : null;
        $this->AsignadoActivo = (!empty($data['AsignadoActivo'])) ? $data['AsignadoActivo'] : 0;
        $this->AsignadoEliminado = (!empty($data['AsignadoEliminado'])) ? $data['AsignadoEliminado'] : 0;
        $this->AplicadoActivo = (!empty($data['AplicadoActivo'])) ? $data['AplicadoActivo'] : 0;
        $this->AplicadoInactivo = (!empty($data['AplicadoInactivo'])) ? $data['AplicadoInactivo'] : 0;
        $this->RedimidoActivo = (!empty($data['RedimidoActivo'])) ? $data['RedimidoActivo'] : 0;
        $this->RedimidoInactivo = (!empty($data['RedimidoInactivo'])) ? $data['RedimidoInactivo'] : 0;
        $this->UsuariosAsignados = (!empty($data['UsuariosAsignados'])) ? $data['UsuariosAsignados'] : 0;
        $this->Tipo = (!empty($data['Tipo'])) ? $data['Tipo'] : 0;
        $this->EstadoCampania = (!empty($data['EstadoCampania'])) ? $data['EstadoCampania'] : null;
        $this->DisponibleAsignar = (!empty($data['DisponibleAsignar'])) ? $data['DisponibleAsignar'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
