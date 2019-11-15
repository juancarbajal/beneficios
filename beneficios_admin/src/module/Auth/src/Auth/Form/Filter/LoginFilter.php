<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\EmailAddress;

class LoginFilter
{

    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        $isEmpty = \Zend\Validator\NotEmpty::IS_EMPTY;
        $invalidEmail = EmailAddress::INVALID_FORMAT;
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'Correo',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StripTags'
                        ),
                        array(
                            'name' => 'StringTrim'
                        )
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    $isEmpty => 'El email no puede ser vacío.'
                                )
                            ),
                            'break_chain_on_failure' => true
                        ),
                        array(
                            'name' => 'EmailAddress',
                            'options' => array(
                                'message' => array(
                                    'emailAddressInvalidFormat' => 'Formato no válido.'.
                                        ' Utilice el formato básico ejemplo@email.com',
                                ),
                            )
                        ),
                    )
                )
            );


            $inputFilter->add(
                array(
                    'name' => 'Contrasenia',
                    'required' => true,
                    'filters' => array(
                        array(
                            'name' => 'StripTags'
                        ),
                        array(
                            'name' => 'StringTrim'
                        )
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    $isEmpty => 'La Contraseña no puede quedar vacía'
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
