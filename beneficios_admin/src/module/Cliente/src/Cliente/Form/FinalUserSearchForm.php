<?php

namespace Cliente\Form;

use Auth\Form\BaseForm;
use Zend\Debug\Debug;
use Zend\Form\Element;

class FinalUserSearchForm extends BaseForm
{
    public function __construct($name = 'buscar', $empresas = array(), $tipo = 0)
    {
        parent::__construct($name);
        $this->init($empresas, $tipo);
    }

    public function init($empresas = array(), $tipo = 0)
    {
        $this->setAttribute('method', 'post');
        $this->add(
            array(
                'name' => 'cliente',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'cliente-search',
                    'class' => 'form-control',
                ),
            )
        );

        if ($tipo == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'id' => 'empresa-search',
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
                        'id' => 'empresa-search',
                        'class' => 'form-control select2',
                    ),
                    'options' => array(
                        'value_options' => $empresas
                    )
                )
            );
        }

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
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
