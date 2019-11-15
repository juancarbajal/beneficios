<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/08/16
 * Time: 08:06 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class FormCancelar extends BaseForm
{
    public function __construct($name = 'eliminarForm')
    {
        parent::__construct('eliminarForm');
        $this->init();
    }

    public function init()
    {
        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'name' => 'action',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'desactivar',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Desactivar',
                    'id' => 'submitDesactivar',
                    'class' => 'enviar btn btn-danger',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'reactivar',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Reactivar',
                    'id' => 'submitReactivar',
                    'class' => 'enviar btn btn-primary',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'eliminar',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Eliminar',
                    'id' => 'submitEliminar',
                    'class' => 'enviar btn btn-danger',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'cancelar',
                'attributes' => array(
                    'value' => 'Cancelar',
                    'id' => 'resetForm',
                    'class' => 'btn btn-default',
                ),
            )
        );
    }
}
