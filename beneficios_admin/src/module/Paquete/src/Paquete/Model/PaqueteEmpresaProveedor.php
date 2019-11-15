<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 03/09/15
 * Time: 12:32 PM
 */

namespace Paquete\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class PaqueteEmpresaProveedor
{
    public $id;
    public $BNF_Empresa_id;
    public $BNF_Paquete_id;
    public $BNF_Usuario_id;
    public $FechaCompra;
    public $CostoPorLead;
    public $MaximoLeads;
    public $Factura;
    public $Precio;
    public $CantidadDescargas;
    public $PrecioUnitarioDescarga;
    public $Bonificacion;
    public $PrecioUnitarioBonificacion;
    public $NumeroDias;
    public $CostoDia;
    public $Bolsa;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $Eliminado;
    public $NombrePaquete;
    public $Empresa;
    public $BNF_TipoPaquete_id;
    public $NombrePais;
    public $Nombres;
    public $FechaInicio;
    public $FechaFin;
    public $NombreComercial;
    public $Apellidos;

    protected $inputFilter;


    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Paquete_id = (!empty($data['BNF_Paquete_id'])) ? $data['BNF_Paquete_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->FechaCompra = (!empty($data['FechaCompra'])) ? $data['FechaCompra'] : null;
        $this->CostoPorLead = (!empty($data['CostoPorLead'])) ? $data['CostoPorLead'] : null;
        $this->MaximoLeads = (!empty($data['MaximoLeads'])) ? $data['MaximoLeads'] : null;
        $this->Factura = (!empty($data['Factura'])) ? $data['Factura'] : null;
        $this->Precio = (!empty($data['Precio'])) ? $data['Precio'] : null;
        $this->CantidadDescargas = (!empty($data['CantidadDescargas'])) ? $data['CantidadDescargas'] : null;
        $this->PrecioUnitarioDescarga = (
        !empty($data['PrecioUnitarioDescarga'])) ? $data['PrecioUnitarioDescarga'] : null;
        $this->Bonificacion = (!empty($data['Bonificacion'])) ? $data['Bonificacion'] : null;
        $this->PrecioUnitarioBonificacion = (
        !empty($data['PrecioUnitarioBonificacion'])) ? $data['PrecioUnitarioBonificacion'] : null;
        $this->NumeroDias = (!empty($data['NumeroDias'])) ? $data['NumeroDias'] : null;
        $this->CostoDia = (!empty($data['CostoDia'])) ? $data['CostoDia'] : null;
        $this->Bolsa = (!empty($data['Bolsa'])) ? $data['Bolsa'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->BNF_TipoPaquete_id = (!empty($data['BNF_TipoPaquete_id'])) ? $data['BNF_TipoPaquete_id'] : null;

        $this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        $this->NombrePaquete = (!empty($data['NombrePaquete'])) ? $data['NombrePaquete'] : null;
        $this->TipoPaquete = (!empty($data['TipoPaquete'])) ? $data['TipoPaquete'] : null;
        $this->NombrePais = (!empty($data['NombrePais'])) ? $data['NombrePais'] : null;
        $this->Nombres = (!empty($data['Nombres'])) ? $data['Nombres'] : null;
        $this->Apellidos = (!empty($data['Apellidos'])) ? $data['Apellidos'] : null;
        $this->FechaInicio = (!empty($data['FechaInicio'])) ? $data['FechaInicio'] : null;
        $this->FechaFin = (!empty($data['FechaFin'])) ? $data['FechaFin'] : null;
        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->BolsaActual = (!empty($data['BolsaActual'])) ? $data['BolsaActual'] : null;
        $this->TEliminado = (!empty($data['TEliminado'])) ? $data['TEliminado'] : null;
        $this->Cantidad = (!empty($data['Cantidad'])) ? $data['Cantidad'] : null;
        $this->Monto = (!empty($data['Monto'])) ? $data['Monto'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter($empresa = array(), $paquetes = array(), $usuario = array(), $pais = array())
    {
        if ($empresa == array()) {
            $empresa_id = array(1 => 1);
        }
        if ($paquetes == array()) {
            $paquetes_id = array(1 => 1);
        }
        if ($usuario == array()) {
            $usuario_id = array(1 => 1);
        }
        if ($pais == array()) {
            $pais_id = array(1 => 1);
        }

        foreach ($empresa as $key => $dato) {
            $empresa_id[] = $key;
        }
        foreach ($paquetes as $key => $dato) {
            $paquetes_id[] = $dato->id;
        }
        foreach ($usuario as $key => $dato) {
            $usuario_id[] = $key;
        }
        foreach ($pais as $key => $dato) {
            $pais_id[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //Empresa
            $inputFilter->add(
                array(
                    'name' => 'BNF_Empresa_id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombre Comercial o Razón Social o RUC no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $empresa_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar una Empresa.'
                                )
                            )
                        )
                    ),
                )
            );
            //paquete
            $inputFilter->add(
                array(
                    'name' => 'BNF_Paquete_id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombre de Paquete no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $paquetes_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar un Paquete.'
                                )
                            )
                        )
                    ),
                )
            );
            //fechacompra
            $inputFilter->add(
                array(
                    'name' => 'FechaCompra',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fecha de Compra no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        ),
                        array(
                            'name' => 'LessThan',
                            'options' => array(
                                'inclusive' => true,
                                'max' => date('Y-m-d'),
                                'messages' => array(
                                    'notLessThanInclusive' => "La entrada no es menor o igual a '%max%'",
                                    'notLessThan' => "La entrada no es menor que '%max%'",
                                )
                            )
                        )
                    ),
                )
            );
            //factura
            $inputFilter->add(
                array(
                    'name' => 'Factura',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Factura no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //asesor
            $inputFilter->add(
                array(
                    'name' => 'BNF_Usuario_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Asesor no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $usuario_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Asesor',
                                )
                            )
                        )
                    ),
                )
            );
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'Tiene que Seleccionar un País.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar un País.'
                                )
                            )
                        )
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getInputFilterL($empresa = array(), $paquetes = array(), $usuario = array(), $pais = array())
    {
        if ($empresa == array()) {
            $empresa_id = array(1 => 1);
        }
        if ($paquetes == array()) {
            $paquetes_id = array(1 => 1);
        }
        if ($usuario == array()) {
            $usuario_id = array(1 => 1);
        }
        if ($pais == array()) {
            $pais_id = array(1 => 1);
        }

        foreach ($empresa as $key => $dato) {
            $empresa_id[] = $key;
        }
        foreach ($paquetes as $key => $dato) {
            $paquetes_id[] = $dato->id;
        }
        foreach ($usuario as $key => $dato) {
            $usuario_id[] = $key;
        }
        foreach ($pais as $key => $dato) {
            $pais_id[] = $key;
        }

        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //Empresa
            $inputFilter->add(
                array(
                    'name' => 'BNF_Empresa_id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombre Comercial o Razón Social o RUC no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $empresa_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar una Empresa.'
                                )
                            )
                        )
                    ),
                )
            );
            //paquete
            $inputFilter->add(
                array(
                    'name' => 'BNF_Paquete_id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombre de Paquete no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $paquetes_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar un Paquete.'
                                )
                            )
                        )
                    ),
                )
            );
            //fechacompra
            $inputFilter->add(
                array(
                    'name' => 'FechaCompra',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fecha de Compra no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        ),
                        array(
                            'name' => 'LessThan',
                            'options' => array(
                                'inclusive' => true,
                                'max' => date('Y-m-d'),
                                'messages' => array(
                                    'notLessThanInclusive' => "La entrada no es menor o igual a '%max%'",
                                    'notLessThan' => "La entrada no es menor que '%max%'",
                                )
                            )
                        )
                    ),
                )
            );
            //factura
            $inputFilter->add(
                array(
                    'name' => 'Factura',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Factura no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //asesor
            $inputFilter->add(
                array(
                    'name' => 'BNF_Usuario_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Asesor no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $usuario_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Asesor',
                                )
                            )
                        )
                    ),
                )
            );
            //MaximoLeads
            $inputFilter->add(
                array(
                    'name' => 'MaximoLeads',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 11,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Maximo de Leads no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Digits',
                            'options' => array(
                                'messages' => array(
                                    'notDigits' => 'La entrada debe contener sólo dígitos',
                                    'digitsStringEmpty' => 'La entrada no en un dígito',
                                    'digitsInvalid' => 'Dato ingresado Inválido'
                                )
                            )
                        ),
                    ),
                )
            );
            //CostoPorLead
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'CostoPorLead',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => 11,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                    )
                                ),
                            ),
                            array(
                                "name" => "Regex",
                                "options" => array(
                                    "pattern" => "/[0-9]+(\.[0-9][0-9]?)?/",
                                    "messages" => array(
                                        "regexInvalid" => "Regex es inválido.",
                                        "regexNotMatch" => "La entrada no es un decimal válido",
                                        "regexErrorous" => "Se ha producido un error interno mientras"
                                            . " se usa el patrón de decimal"
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Costo Por Lead no puede quedar vacío.'
                                )
                            )
                        ),
                    )
                )
            );
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'Tiene que Seleccionar un País.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Tiene que Seleccionar un País.'
                                )
                            )
                        )
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getInputFilterE($usuario = array())
    {
        if ($usuario == array()) {
            $usuario_id = array(1 => 1);
        }

        foreach ($usuario as $key => $dato) {
            $usuario_id[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //fechacompra
            $inputFilter->add(
                array(
                    'name' => 'FechaCompra',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fecha de Compra no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        ),
                        array(
                            'name' => 'LessThan',
                            'options' => array(
                                'inclusive' => true,
                                'max' => date('Y-m-d'),
                                'messages' => array(
                                    'notLessThanInclusive' => "La entrada no es menor o igual a '%max%'",
                                    'notLessThan' => "La entrada no es menor que '%max%'",
                                )
                            )
                        )
                    ),
                )
            );
            //factura
            $inputFilter->add(
                array(
                    'name' => 'Factura',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Factura no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //asesor
            $inputFilter->add(
                array(
                    'name' => 'BNF_Usuario_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Asesor no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $usuario_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Asesor',
                                )
                            )
                        )
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getInputFilterLE($usuario = array())
    {
        if ($usuario == array()) {
            $usuario_id = array(1 => 1);
        }

        foreach ($usuario as $key => $dato) {
            $usuario_id[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //fechacompra
            $inputFilter->add(
                array(
                    'name' => 'FechaCompra',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fecha de Compra no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        ),
                        array(
                            'name' => 'LessThan',
                            'options' => array(
                                'inclusive' => true,
                                'max' => date('Y-m-d'),
                                'messages' => array(
                                    'notLessThanInclusive' => "La entrada no es menor o igual a '%max%'",
                                    'notLessThan' => "La entrada no es menor que '%max%'",
                                )
                            )
                        )
                    ),
                )
            );
            //factura
            $inputFilter->add(
                array(
                    'name' => 'Factura',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Factura no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //asesor
            $inputFilter->add(
                array(
                    'name' => 'BNF_Usuario_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Asesor no debe de quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $usuario_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Asesor',
                                )
                            )
                        )
                    ),
                )
            );
            //MaximoLeads
            $inputFilter->add(
                array(
                    'name' => 'MaximoLeads',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 11,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Maximo de Leads no puede quedar vacío.'
                            )
                        ), array(
                            'name' => 'Digits',
                            'options' => array(
                                'messages' => array(
                                    'notDigits' => 'La entrada debe contener sólo dígitos',
                                    'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                    'digitsInvalid' => 'Dato ingresado Inválido'
                                )
                            )
                        ),
                    ),
                )
            );
            //CostoPorLead
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'CostoPorLead',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => 11,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                    )
                                ),
                            ),
                            array(
                                "name" => "Regex",
                                "options" => array(
                                    "pattern" => "/[0-9]+(\.[0-9][0-9]?)?/",
                                    "messages" => array(
                                        "regexInvalid" => "Regex es inválido.",
                                        "regexNotMatch" => "La entrada no es un decimal",
                                        "regexErrorous" => "Se ha producido un error interno mientras"
                                            . " se usa el patrón de decimal"
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Costo Por Lead no puede quedar vacío.'
                                )
                            )
                        ),
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
