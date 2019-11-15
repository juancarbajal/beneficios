<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 27/07/16
 * Time: 12:53 AM
 */

namespace Perfil\Form;

use Application\Form\BaseForm;

class PerfilForm extends BaseForm
{
    public function __construct()
    {
        parent::__construct('perfil');
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
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Apellido',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Correo',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Telefono',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Guargar Datos',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-default btn-user-m',
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