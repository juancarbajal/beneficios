<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 18/06/16
 * Time: 09:36 PM
 */

namespace Premios\Model\Filter;

use Zend\InputFilter\InputFilter;

class CampaniasPremiosFilter
{
    protected $inputFilter;

    public function getInputFilter($filter)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Empresa',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['emp'],
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
                    'name' => 'NombreCampania',
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
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'FechaCampania',
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
                    'name' => 'VigenciaInicio',
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
                    'name' => 'VigenciaFin',
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
                    'name' => 'PresupuestoNegociado',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Digits',
                            'options' => array(
                                'message' => 'La entrada debe contener sólo dígitos'
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'ParametroAlerta',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Between',
                            'options' => array(
                                'min' => 1,
                                'max' => 100,
                                'inclusive' => true,
                                'messages' => array(
                                    'notBetween' => "La entrada no está entre '%min%' y '%max%'",
                                    'notBetweenStrict' => "La entrada no es estrictamente entre '%min%' y '%max%'"
                                )
                            )
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Comentario',
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
                    'name' => 'Relacionado',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'Digits',
                            'options' => array(
                                'message' => 'La entrada debe contener sólo dígitos'
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'TipoSegmento',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('Clasico', 'Personalizado'),
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
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
