<?php
/**
 * Created by PhpStorm.
 * User: janaqlap2
 * Date: 22/01/16
 * Time: 12:21 PM
 */

namespace Categoria\Model\Filter;

use Zend\InputFilter\InputFilter;


class BuscarCategoriaFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//

            if ($data['Pais']) {
                $inputFilter->add(
                    array(
                        'name' => 'Pais',
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Pais',
                        'required' => false,
                    )
                );
            }

            if ($data['Nombre']) {
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Nombre',
                        'required' => false
                    )
                );

            }
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}