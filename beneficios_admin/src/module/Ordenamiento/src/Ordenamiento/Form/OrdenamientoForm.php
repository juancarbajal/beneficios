<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/09/15
 * Time: 05:28 PM
 */

namespace Ordenamiento\Form;

use Auth\Form\BaseForm;


class OrdenamientoForm extends BaseForm
{
    public function __construct($name = 'ordenamiento', $nombre_submit = 'Registrar')
    {
        parent::__construct($name);
        $this->init($nombre_submit);
    }

    public function init($nombre_submit = 'Registrar')
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            )
        );
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
                    'value' => $nombre_submit,
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );

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
