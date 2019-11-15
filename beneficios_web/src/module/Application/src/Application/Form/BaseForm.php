<?php

namespace Application\Form;

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

        $csrf = new Element\Csrf('csrf');
        $this->add($csrf);


        // set InputFilter
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name' => 'csrf',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'Csrf',
                            'options' => array(
                                'timeout' => 600
                            )
                        )
                    )
                )
            )
        );

        $this->setInputFilter($inputFilter);
    }
}
