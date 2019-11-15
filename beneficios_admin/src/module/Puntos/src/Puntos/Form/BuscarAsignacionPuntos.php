<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 08/07/16
 * Time: 12:59 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class BuscarAsignacionPuntos extends BaseForm
{
    public function __construct($name = null, $value = array(), $tipo = 0)
    {
        parent::__construct('buscar ofertas');
        $this->init($value, $tipo);
    }

    public function init($value = array(), $tipo = 0)
    {
        if ($tipo == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'Empresas',
                    'attributes' => array(
                        'id' => 'empresa-cli',
                        'value' => $value
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'Empresas',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                        'id' => 'empresa-cli',
                    ),
                    'options' => array(
                        'value_options' => $value['empresa'],
                        'disable_inarray_validator' => true,
                        'empty_option' => 'Listar Todos'
                    ),
                )
            );
        }

        $this->add(
            array(
                'name' => 'Campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias',
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitButton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
