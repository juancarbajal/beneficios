<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 30/09/15
 * Time: 06:35 PM
 */

namespace Ordenamiento\Form;

use Auth\Form\BaseForm;

class AssignForm extends BaseForm
{
    public function __construct($categorias = array(), $campanias = array(), $layout = array(), $empresas = array(), $tipo = 0, $name = null)
    {
        parent::__construct('assign');
        $this->init($categorias, $campanias, $layout, $empresas, $tipo);
    }


    public function init($categorias = array(), $campanias = array(), $layout = array(), $empresas = array(), $tipo = 0)
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
        if ($tipo == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'id' => 'empresa',
                        'value' => $empresas
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'id' => 'empresa',
                        'class' => 'form-control select2',
                    ),
                    'options' => array(
                        'value_options' => $empresas
                    )
                )
            );
        }
        //Categoria
        $this->add(
            array(
                'name' => 'BNF_Categoria_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'categoria',
                ),
                'options' => array(
                    'value_options' => $categorias,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        //Campaña
        $this->add(
            array(
                'name' => 'BNF_Campanias_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'campania',
                    'disabled' => true
                ),
                'options' => array(
                    'value_options' => $campanias,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        //Fila_1
        $this->add(
            array(
                'name' => 'Fila_1',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'fila1',
                ),
                'options' => array(
                    'value_options' => $layout,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_1_1',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila1 pos-offer hidden',
                    'id' => 'fila1_1'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_1_2',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila1 pos-offer hidden',
                    'id' => 'fila1_2'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_1_3',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila1 pos-offer hidden',
                    'id' => 'fila1_3'
                ),

            )
        );
        //Fila_2
        $this->add(
            array(
                'name' => 'Fila_2',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'fila2',
                ),
                'options' => array(
                    'value_options' => $layout,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_2_1',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila2 pos-offer hidden',
                    'id' => 'fila2_1'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_2_2',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila2 pos-offer hidden',
                    'id' => 'fila2_2'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_2_3',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila2 pos-offer hidden',
                    'id' => 'fila2_3'
                ),
            )
        );
        //Fila_3
        $this->add(
            array(
                'name' => 'Fila_3',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'fila3',
                ),
                'options' => array(
                    'value_options' => $layout,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_3_1',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila3 pos-offer hidden',
                    'id' => 'fila3_1'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_3_2',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila3 pos-offer hidden',
                    'id' => 'fila3_2'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Fila_3_3',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control fila3 pos-offer hidden',
                    'id' => 'fila3_3'
                ),
            )
        );
        ///radio
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Radio',
                'name' => 'type',
                'options' => array(
                    'value_options' => array(
                        'categoria' => 'Categoría',
                        'campania' => 'Campaña',
                        'tienda' => 'Tienda',
                    ),
                ),
                'attributes' => array(
                    'value' => 'campania'
                )
            )
        );
        ///submit
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
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
