<?php

namespace Auth\Form;

use Zend\Form\Element;


class LoginForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct('login');
        $this->init();
    }


    public function init(){
        $submit = new Element\Submit('submit');
        $submit->setValue('Log In');
        $this->add($submit);

        $this->add(
            array(
                'name' => 'Correo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'correo',
                    'placeholder'=> 'example@example.com',
                    'size' => '40'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Contrasenia',
                'type' => 'Password',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'contrasenia',
                    'size' => '20'
                ),
            )
        );
        $this->setInputFilter($this->getInputFilter());
    }
}
