<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 12:38 AM
 */
namespace Paquete\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\Factory as InputFactory;

class Paquete
{
    public $id;
    public $BNF_TipoPaquete_id;
    public $Nombre;
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
    //join
    public $NombreTipoPaquete;
    public $CNombres;
    public $CApellidos;
    public $NombrePais;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Precio = (!empty($data['Precio'])) ? $data['Precio'] : null;
        $this->CantidadDescargas = (!empty($data['CantidadDescargas']) and $data['CantidadDescargas'] != null)
            ? (int)$data['CantidadDescargas'] : 0;
        $this->Bonificacion = (!empty($data['Bonificacion']) and $data['Bonificacion'] != null)
            ? (int)$data['Bonificacion'] : 0;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->PrecioUnitarioDescarga = (
        !empty($data['PrecioUnitarioDescarga'])) ? $data['PrecioUnitarioDescarga'] : 0.00;
        $this->PrecioUnitarioBonificacion = (
        !empty($data['PrecioUnitarioBonificacion'])) ? $data['PrecioUnitarioBonificacion'] : 0.00;
        $this->NumeroDias = (!empty($data['NumeroDias']) and $data['NumeroDias'] != null)
            ? (int)$data['NumeroDias'] : 0;
        $this->CostoDia = (!empty($data['CostoDia'])) ? $data['CostoDia'] : 0.00;
        $this->Bolsa = (!empty($data['Bolsa'])) ? (int)$data['Bolsa'] : 0;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;

        $this->NombreTipoPaquete = (!empty($data['NombreTipoPaquete'])) ? $data['NombreTipoPaquete'] : null;
        $this->CNombres = (!empty($data['CNombres'])) ? $data['CNombres'] : null;
        $this->CApellidos = (!empty($data['CApellidos'])) ? $data['CApellidos'] : null;
        $this->BNF_TipoPaquete_id = (!empty($data['BNF_TipoPaquete_id'])) ? $data['BNF_TipoPaquete_id'] : null;
        $this->NombrePais = (!empty($data['NombrePais'])) ? $data['NombrePais'] : null;
        $this->Cantidad = (!empty($data['Cantidad'])) ? (int)$data['Cantidad'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter($pais = array(), $tipo = array())
    {
        if ($pais == array()) {
            $pais_id[1] = '1';
        }
        if ($tipo == array()) {
            $tipo_id[1] = '1';
        }
        foreach ($pais as $key => $dato) {
            $pais_id[] = $key;
        }
        foreach ($tipo as $key => $dato) {
            $tipo_id[] = $key;
        }
        //var_dump($pais_id);exit;
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
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo País no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un País',
                                )
                            )
                        )
                    ),
                )
            );
            //tipopaquete
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoPaquete_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Paquete no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $tipo_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Tipo de Paquete',
                                )
                            )
                        )
                    ),
                )
            );
            //nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombre',
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
                                'message' => 'El campo Nombre no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                    'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                    'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                )
                            )
                        )
                    ),
                )
            );
            //precio
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'Precio',
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
                                    "pattern" => "/(^[0-9]{1}$)|(^[0-9][0-9]+$)|(^[0-9]+\.+[0-9]{1,2}$)/",
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
                                    'message' => 'El campo Precio no puede quedar vacío.'
                                )
                            )
                        ),
                    )
                )
            );
            //CantidadDescargas
            $inputFilter->add(
                array(
                    'name' => 'CantidadDescargas',
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
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Cantidad Descargas no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //PrecioUnitarioDescarga
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'PrecioUnitarioDescarga',
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
                                    "pattern" => "/(^[0-9]{1}$)|(^[0-9][0-9]+$)|(^[0-9]+\.+[0-9]{1,2}$)/",
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
                                    'message' => 'El campo Precio por Descarga no puede quedar vacío.'
                                )
                            )
                        ),
                    )
                )
            );
            //Bonificacion
            $inputFilter->add(
                array(
                    'name' => 'Bonificacion',
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
                                'message' => 'El campo Bonificación no puede quedar vacío.'
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
            //PrecioUnitarioBonificacion
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'PrecioUnitarioBonificacion',
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
                                    "pattern" => "/(^[0-9]{1}$)|(^[0-9][0-9]+$)|(^[0-9]+\.+[0-9]{1,2}$)/",
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
                                    'message' => 'El campo Precio por Bonificación no puede quedar vacío.'
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

    public function getInputFilterP($pais = array(), $tipo = array())
    {
        if ($pais == array()) {
            $pais_id[1] = '1';
        }
        if ($tipo == array()) {
            $tipo_id[1] = '1';
        }
        foreach ($pais as $key => $dato) {
            $pais_id[] = $key;
        }
        foreach ($tipo as $key => $dato) {
            $tipo_id[] = $key;
        }
        //var_dump($pais_id);exit;
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
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo País no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un País',
                                )
                            )
                        )
                    ),
                )
            );
            //tipopaquete
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoPaquete_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Paquete no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $tipo_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Tipo de Paquete',
                                )
                            )
                        )
                    ),
                )
            );
            //nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombre',
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
                                'message' => 'El campo Nombre no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                    'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                    'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                )
                            )
                        )
                    ),
                )
            );
            //precio
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'Precio',
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
                                    "pattern" => "/(^[0-9]{1}$)|(^[0-9][0-9]+$)|(^[0-9]+\.+[0-9]{2}$)/",
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
                                    'message' => 'El campo Precio no puede quedar vacío.'
                                )
                            )
                        ),
                    )
                )
            );
            //NumeroDias
            $inputFilter->add(
                array(
                    'name' => 'NumeroDias',
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
                            'name' => 'Digits',
                            'options' => array(
                                'messages' => array(
                                    'notDigits' => 'La entrada debe contener sólo dígitos',
                                    'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                    'digitsInvalid' => 'Dato ingresado no válido'
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Número de Días no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );
            //CostoDia
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'CostoDia',
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
                                    "pattern" => "/(^[0-9]{1}$)|(^[0-9][0-9]+$)|(^[0-9]+\.+[0-9]{1,2}$)/",
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
                                    'message' => 'El campo Costo por Día no puede quedar vacío.'
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

    public function getInputFilterL($pais = array(), $tipo = array())
    {
        if ($pais == array()) {
            $pais_id[1] = '1';
        }
        if ($tipo == array()) {
            $tipo_id[1] = '1';
        }
        foreach ($pais as $key => $dato) {
            $pais_id[] = $key;
        }
        foreach ($tipo as $key => $dato) {
            $tipo_id[] = $key;
        }
        //var_dump($pais_id);exit;
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
            //pais
            $inputFilter->add(
                array(
                    'name' => 'NombrePais',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo País no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $pais_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un País',
                                )
                            )
                        )
                    ),
                )
            );
            //tipopaquete
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoPaquete_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Paquete no debe de quedar vacío'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $tipo_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Tipo de Paquete',
                                )
                            )
                        )
                    ),
                )
            );
            //nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombre',
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
                                'message' => 'El campo Nombre no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alnum',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                    'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                    'alnumStringEmpty' => 'La entrada es una cadena vacía'
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
}
