<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/12/15
 * Time: 07:46 PM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;

class OfertaBlockForm extends BaseForm
{
    public function __construct($name = 'asignar', $value = array(), $valueof = array())
    {
        parent::__construct('asignar');
        $this->init($value, $valueof);
    }

    public function init($value = array(), $valueof = array())
    {
        // we want to ignore the name passed

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Asignar Ofertas',
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