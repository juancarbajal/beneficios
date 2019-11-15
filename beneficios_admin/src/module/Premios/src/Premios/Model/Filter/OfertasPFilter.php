<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 20/06/16
 * Time: 12:39 PM
 */

namespace Premios\Model\Filter;

use Zend\InputFilter\InputFilter;

class OfertasPFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $activator = true, $fecha = '2015-01-01')
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'EmpresaProv',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['empprov'],
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
                    'name' => 'EmpresaCli',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
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
                    'name' => 'CampaniaPremios',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'SegmentoPremios',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Nombre',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'El nombre no de Tener mas de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El nombre es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Titulo',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'El título no de Tener mas de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El título es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'TituloCorto',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'El título corto no de tener mas de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El título corto es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CondicionesUso',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'messages' => array(
                                    'stringLengthTooLong' => 'Las Condiciones de Uso no deben ' .
                                        'tener más de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'Las Condiciones de Uso son requeridas y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Direccion',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'La Dirección no de tener más de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'La Dirección es requerida y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Telefono',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 50,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El Telefono es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Premium',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'No seleccionó el estado Premium.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Correo',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 45,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Correo no puede quedar vacío.'
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[a-zA-Z0-9+]+(?:([\.\_\-][a-zA-Z0-9+]+))*@(?:([a-zA-Z0-9]+(\-[a-zA-Z0-9]+)*)\.)+[a-zA-Z]+$/",
                                "messages" => array(
                                    "regexInvalid" => "Regex es inválido.",
                                    "regexNotMatch" => "El Email ingresado no es válido.",
                                    "regexErrorous" => "Error interno al validar el correo."
                                )
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Rubro',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['rubro'],
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
                    'name' => 'TipoPrecio',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('Split', 'Unico'),
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'No se Selecciono ningún TipoPrecio.'
                            )
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PrecioVentaPublico',
                    'required' => $activator,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'IsInt',
                            'options' => array(
                                'messages' => array(
                                    'intInvalid' => "El valor ingresado no es un número entero.",
                                    'notInt' => "El valor ingresado no es un número entero."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo es requerido y no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PrecioBeneficio',
                    'required' => $activator,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'IsInt',
                            'options' => array(
                                'messages' => array(
                                    'intInvalid' => "El valor ingresado no es un número entero.",
                                    'notInt' => "El valor ingresado no es un número entero."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo es requerido y no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Pais',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['pais'],
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Seleccionó ningún País.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Departamento',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se selecciono ningún Departamento.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'FechaVigencia',
                    'required' => $activator,
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
                                'message' => 'El campo no puede quedar vacío.'
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
                                'min' => $fecha,
                                'inclusive' => true,
                                'messages' => array(
                                    'notGreaterThan' => "La fecha ingresada es mayor a %min%",
                                    'notGreaterThanInclusive' => "La fecha no es mayor o igual a %min%",
                                )
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DescargaMaxima',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'Digits',
                            'options' => array(
                                'messages' => array(
                                    'notDigits' => 'La entrada debe contener sólo dígitos.',
                                    'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                    'digitsInvalid' => 'Tipo de dato inválido.'
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'Las Descargas Máxima son requeridas y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Estado',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('Borrador', 'Publicado', 'Caducado'),
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Selecciono ningún Estado.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Stock',
                    'required' => $activator,
                    'validators' => array(
                        array(
                            'name' => 'Step',
                            'options' => array(
                                'baseValue' => 0,
                                'step' => 1,
                                'messages' => array(
                                    'typeInvalid' => 'El valor ingresado en Stock, no es un número entero.',
                                    'stepInvalid' => 'El valor ingresado en Stock, no es válido.'
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El Stock es requerido y no puede quedar vacío.'
                                )
                            )
                        )
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
