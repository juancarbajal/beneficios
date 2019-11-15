<?php

namespace Auth\Form;

use Application\Form\BaseForm;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\InputFilter\Factory as InputFactory;

class LoginForm extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct('login');
        $this->init();
    }

    public function init()
    {
        $this->setName('loginForm');
        $this->setAttribute('method', 'post');
        $this->setAttribute('autocomplete', 'off');

        $NumeroDocumento = new Element\Text('dni');
        $NumeroDocumento->setLabel('dni')
            ->setAttribute(
                'id',
                'NumeroDocumento'
            )
            ->setAttribute(
                'class',
                'form-control'
            )
            ->setAttribute(
                'placeholder',
                'Nro. de Documento'
            )
            ->setAttribute(
                'autocomplete',
                'off'
            )
            ->setAttribute('size', '40');
        $this->add($NumeroDocumento);

        $email = new Element\Text('email');
        $email->setAttribute(
            'id',
            'email'
        )
            ->setAttribute(
                'class',
                'form-control'
            )
            ->setAttribute(
                'placeholder',
                'Correo'
            )
            ->setAttribute(
                'autocomplete',
                'off'
            )
            ->setAttribute('size', '40');
        $this->add($email);

        $empresa_id = new Element\Hidden('empresa_id');
        $empresa_id->setLabel('empresa_id')
            ->setAttribute(
                'id',
                'empresa_id'
            )
            ->setAttribute(
                'class',
                'form-control'
            )
            ->setAttribute('size', '40');
        $this->add($empresa_id);

        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);

        $submit = new Element\Submit('submit');
        $submit->setValue('Ingresa aquí');
        $this->add($submit);

        // set InputFilter
        $inputFilter = $this->getInputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'empresa_id'
                )
            )
        );
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'dni',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 40
                            )
                        )
                    )
                )
            )
        );
        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'email',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 60
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
                    )
                )
            )
        );

        $this->setInputFilter($inputFilter);
        $this->setUseInputFilterDefaults(false);
    }

    /**
     * Set a single option for an element
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        // TODO: Implement setOption() method.
    }
}
