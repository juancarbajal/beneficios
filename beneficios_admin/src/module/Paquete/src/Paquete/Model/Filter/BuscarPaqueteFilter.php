<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Paquete\Model\Filter;

use Zend\InputFilter\InputFilter;

class BuscarPaqueteFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//

            if ($data['NombrePais']){
                $inputFilter->add(
                    array(
                        'name' => 'NombrePais',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
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
                            )
                        )
                    )
                );
            }else{
                $inputFilter->add(
                    array(
                        'name' => 'NombrePais',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }


            if ($data['TipoPaquete']){
                $inputFilter->add(
                    array(
                        'name' => 'TipoPaquete',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $filter['tip'],
                                    'messages' => array(
                                        'notInArray' => "El valor seleccionado, no es válido."
                                    )
                                )
                            )
                        )
                    )
                );
            }else{
                $inputFilter->add(
                    array(
                        'name' => 'TipoPaquete',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }


            if (isset($data['RazonSocial']) ? $data['RazonSocial'] : null){
                $inputFilter->add(
                    array(
                        'name' => 'RazonSocial',
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
            }else{
                $inputFilter->add(
                    array(
                        'name' => 'RazonSocial',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }

            if ($data['FechaInicio']){
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
            }else{
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

            if ($data['FechaFin']){
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
            }else{
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
