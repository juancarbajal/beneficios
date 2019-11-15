<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 05:09 PM
 */

namespace Rubro\Form;

use Auth\Form\BaseForm;

class RubroForm extends BaseForm
{
    public function __construct($name = 'rubro', $nombre_submit = "Registrar")
    {
        parent::__construct($name);
        $this->init($nombre_submit);
    }

    public function init($nombre_submit = "Registrar")
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
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Descripcion',
                'attributes' => array(
                    'type' => 'textarea',
                    'class' => 'form-control md',
                    'rows' => 10,
                    'cols' => 10,
                    'maxLength' => 255
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
