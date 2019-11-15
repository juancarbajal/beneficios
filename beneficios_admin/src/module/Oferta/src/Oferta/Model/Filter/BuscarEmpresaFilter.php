<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Oferta\Model\Filter;

use Zend\InputFilter\InputFilter;

class BuscarEmpresaFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//

            if ($data['Empresa']) {
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Empresa',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }


            if ($data['Oferta']) {
                $inputFilter->add(
                    array(
                        'name' => 'Oferta',
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
                        'name' => 'Oferta',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        )
                    )
                );
            }


            if ($data['Ruc']) {
                $inputFilter->add(
                    array(
                        'name' => 'Ruc',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'min' => 11,
                                    'max' => 11,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'El tamaño del Ruc es de 11 dígitos.',
                                    )
                                )
                            ),
                            array(
                                'name' => 'Int',
                                'options' => array(
                                    'messages' => array(
                                        'notInt' => 'El campo Ruc es un dato no válido.',
                                    )
                                )
                            )
                        )
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Ruc',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        )
                    )
                );
            }

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
