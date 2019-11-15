<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;

class LoginFilter extends InputFilter
{
    public function __construct()
    {
        $this->add(
            array(
                'name' => 'dni',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 15,
                            'max' => 15,
                            'messages' => array(
                                'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                            )
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'El campo Número de Documento no puede quedar vacío.'
                        )
                    ),
                    array(
                        'name' => 'Alnum',
                        'options' => array(
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
        $this->add(
            array(
                'name' => 'subdominio',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 15,
                            'max' => 15,
                            'messages' => array(
                                'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                            )
                        ),
                    ),
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'El campo Número de Documento no puede quedar vacío.'
                        )
                    )
                ),
            )
        );
    }
}
