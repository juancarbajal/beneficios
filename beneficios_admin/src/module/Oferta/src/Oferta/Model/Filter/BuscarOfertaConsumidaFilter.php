<?php
/**
 * Created by PhpStorm.
 * User: janaqlap2
 * Date: 22/01/16
 * Time: 12:21 PM
 */

namespace Oferta\Model\Filter;

use Zend\InputFilter\InputFilter;


class BuscarOfertaConsumidaFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//

            if ($data['Titulo']) {
                $inputFilter->add(
                    array(
                        'name' => 'Titulo',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $filter['ofe'],
                                    'messages' => array(
                                        'notInArray' => "El valor seleccionado, no es válido."
                                    )
                                )
                            )
                        )
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Titulo',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }

            if ($data['Estado']) {
                $inputFilter->add(
                    array(
                        'name' => 'Estado',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $filter['est'],
                                    'messages' => array(
                                        'notInArray' => "El valor seleccionado, no es válido."
                                    )
                                )
                            )
                        )
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Estado',
                        'required' => false,
                    )
                );
            }


            if ($data['FechaInicio']) {
                $inputFilter->add(
                    array(
                        'name' => 'FechaInicio',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'FechaInicio',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                );
            }

            if ($data['FechaFin']) {
                $inputFilter->add(
                    array(
                        'name' => 'FechaFin',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'FechaFin',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                    )
                );
            }

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}