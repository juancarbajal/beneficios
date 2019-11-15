<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 30/08/16
 * Time: 04:32 PM
 */

namespace Cupon\Form;

use Auth\Form\BaseForm;

class FormEnvioCuponPremios extends BaseForm
{
    public function __construct($name = "enviar")
    {
        parent::__construct($name);
        $this->init();
    }

    public function init()
    {
        $this->add(
            array(
                'name' => 'porPagarButton',
                'attributes' => array(
                    'type' => 'button',
                    'value' => 'Por Pagar',
                    'id'=>  'porPagarButton',
                    'class' => 'btn btn-info enviar',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'pagadoButton',
                'attributes' => array(
                    'type' => 'button',
                    'value' => 'Pagados',
                    'id'=>  'pagadoButton',
                    'class' => 'btn btn-info enviar',
                ),
            )
        );
    }
}
