<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 06/09/15
 * Time: 11:19 PM
 */

namespace Paquete\Form;

use Auth\Form\BaseForm;


class EditarAsignacionForm extends BaseForm
{
    public function __construct($asesor = null, $name = null)
    {
        parent::__construct('paquete');
        $this->init($asesor);
    }

    public function init($asesor = null)
    {
        // we want to ignore the name passed


        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
            )
        );
        $this->add(
            array(
                'name' => 'NombrePais',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control _mg',
                    'disabled' => true,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NombreComercial',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md',
                    'disabled' => true,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'TipoPaquete',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                    'disabled' => true,
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NombrePaquete',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                    'disabled' => true,
                ),
            )
        );
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaCompra',
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Factura',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md',
                )
            )
        );
        $this->add(
            array(
                'name' => 'BNF_Usuario_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipopaq',
                ),
                'options' => array(
                    'value_options' => $asesor,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'NumeroDias',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md pre',
                )
            )
        );

        $this->add(
            array(
                'name' => 'MaximoLeads',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md lead',
                )
            )
        );

        $this->add(
            array(
                'name' => 'CostoPorLead',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md lead',
                    'placeholder' => 'S/.00',
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Editar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary mg-b',
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
