<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 06:54 PM
 */

namespace Campania\Form;

use Auth\Form\BaseForm;


class BuscarForm extends BaseForm
{

    public function __construct()
    {
        parent::__construct('campania');
        $this->init();
    }

    public function init()
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'Nombre',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NombrePais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'empty_option' => 'Listar Todos',
                ),
            )
        );
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
