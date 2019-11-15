<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/06/16
 * Time: 03:53 PM
 */

namespace Demanda\Model\Filter;

use Zend\InputFilter\InputFilter;

class DemandaPremiosFilter
{
    protected $inputFilter;

    public function getInputFilter($filter)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'EmpresaCliente',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['empcli'],
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'FechaDemanda',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
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
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Rubros',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                        )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'EmpresaProveedor',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Departamentos',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Campania',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Segmento',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'EmpresasAdicionales',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );


            $inputFilter->add(
                array(
                    'name' => 'PrecioMin',
                    'required' => false,
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
                            "name" => "Digits",
                            "options" => array(
                                "messages" => array(
                                    "notDigits" => "La entrada solo debe contener digitos",
                                    "digitsStringEmpty" => "La entrada no es un entero",
                                    "digitsInvalid" => "Se ha producido un error interno mientras"
                                        . " se usa el patrón de enteros"
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
            );

            $inputFilter->add(
                array(
                    'name' => 'PrecioMax',
                    'required' => false,
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
                            "name" => "Digits",
                            "options" => array(
                                "messages" => array(
                                    "notDigits" => "La entrada solo debe contener digitos",
                                    "digitsStringEmpty" => "La entrada no es un entero",
                                    "digitsInvalid" => "Se ha producido un error interno mientras"
                                        . " se usa el patrón de enteros"
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
            );


            $inputFilter->add(
                array(
                    'name' => 'Target',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );


            $inputFilter->add(
                array(
                    'name' => 'Comentarios',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Actualizaciones',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
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
