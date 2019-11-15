<?php

namespace Referido\Form;

use Auth\Form\BaseForm;

class FinalUserSearchForm extends BaseForm
{
    public function __construct($name = 'buscar')
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
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

        $this->add(
            array(
                'name' => 'fecha_ini',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'fecha-ini',
                    'class' => 'form-control datepicker',
                )
            )
        );

        $this->add(
            array(
                'name' => 'fecha_fin',
                'type' => 'text',
                'attributes' => array(
                    'id' => 'fecha-fin',
                    'class' => 'form-control datepicker',
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submit-button',
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
