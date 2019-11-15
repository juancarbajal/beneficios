<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 13/11/15
 * Time: 12:15 PM
 */

namespace Cupon\Form;

use Auth\Form\BaseForm;

class BusquedaCupon extends BaseForm
{
    public function __construct($name = null)
    {
        parent::__construct('buscar');
        $this->init();
    }

    public function init()
    {
        $this->add(
            array(
                'name' => 'cupon',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'cliente-search',
                    'class' => 'form-control',
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
}
