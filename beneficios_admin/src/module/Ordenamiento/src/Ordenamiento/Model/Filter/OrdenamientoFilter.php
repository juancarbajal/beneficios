<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/09/15
 * Time: 05:29 PM
 */

namespace Ordenamiento\Model\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class OrdenamientoFilter
{
    protected $inputFilter;

    public function getInputFilter($data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            if (isset($data['id']) ? $data['id'] : null) {
                $inputFilter->add(
                    array(
                        'name' => 'id',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'Int',
                                'options' => array(
                                    'messages' => array(
                                        'notInt' => 'El campo id es un dato no válido.',
                                    )
                                )
                            )
                        )
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'id',
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
