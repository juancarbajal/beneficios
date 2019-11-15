<?php

namespace Auth\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class BaseForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');

        $csrf = array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 300
                )
            )
        );
        $this->add($csrf);


        // set InputFilter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
    }
}
