<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 11:41 AM
 */

namespace Rubro\Form;

use Auth\Form\BaseForm;


class BuscarForm extends BaseForm
{
    public function __construct( $name = null)
    {
        parent::__construct('rubro');
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
