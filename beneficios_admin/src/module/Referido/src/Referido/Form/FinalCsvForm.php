<?php

namespace Referido\Form;

use Auth\Form\BaseForm;
use Zend\Form\Element;

class FinalCsvForm extends BaseForm
{
    public function __construct($name = 'upload', $campanias = array(), $tipo = 0)
    {
        parent::__construct($name);
        $this->init($campanias, $tipo);
    }

    public function init($campanias = array(), $tipo = 0)
    {

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');


            $this->add(
                array(
                    'name' => 'campania',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                    ),
                    'options' => array(
                        'value_options' => $campanias,
                        'empty_option' => 'Seleccione...'
                    ),
                )
            );


        $this->add(
            array(
                'name' => 'file_csv',
                'attributes' => array(
                    'type' => 'file',
                ),
                'validators' => array(
                    array(
                        'name' => 'Zend\Validator\File\Size',
                        'options' => array(
                            'min' => 120,
                            'max' => 500000,
                        ),
                    ),
                    array(
                        'name' => 'Zend\Validator\File\Extension',
                        'options' => array(
                            'extension' => 'csv',
                        ),
                    ),
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Cargar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
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
