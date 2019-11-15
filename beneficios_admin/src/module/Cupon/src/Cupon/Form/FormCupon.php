<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/11/15
 * Time: 12:30 PM
 */

namespace Cupon\Form;

use Auth\Form\BaseForm;

class FormCupon extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct('addform');
        $this->init();
    }

    public function init()
    {
        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
                'attributes' => array(
                    'class' => 'form-cupon',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CodigoCupon',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Titulo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CondicionesUso',
                'type' => 'textarea',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled',
                    'rows' => 10
                ),
            )
        );

        $this->add(
            array(
                'name' => 'FechaFinVigencia',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control form-cupon',
                    'disabled' => 'disabled'
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Submit',
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Redimir',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
