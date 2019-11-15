<?php

namespace Cliente\Form;

use Auth\Form\BaseForm;
use Zend\Form\Element;

class FinalCsvForm extends BaseForm
{
    public function __construct($name = 'upload', $empresas = array(), $tipo = 0)
    {
        parent::__construct($name);
        $this->init($empresas, $tipo);
    }

    public function init($empresas = array(), $tipo = 0)
    {

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        if ($tipo == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'value' => $empresas
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                    ),
                    'options' => array(
                        'value_options' => $empresas,
                        'empty_option' => 'Seleccione...'
                    ),
                )
            );
        }

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
