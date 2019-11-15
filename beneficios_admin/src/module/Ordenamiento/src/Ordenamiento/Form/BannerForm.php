<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 16/10/15
 * Time: 08:46 PM
 */

namespace Ordenamiento\Form;

use Auth\Form\BaseForm;


class BannerForm extends BaseForm
{
    public function __construct($categorias = array(), $campanias = array(), $empresas = array(), $tipo = 0)
    {
        parent::__construct('banner');
        $this->init($categorias, $campanias, $empresas, $tipo);
    }

    public function init($categorias = array(), $campanias = array(), $empresas = array(), $tipo = 0)
    {
        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
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
        //Banner 01
        $this->add(
            array(
                'name' => 'Banner01',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner01Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //Banner 02
        $this->add(
            array(
                'name' => 'Banner02',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner02Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //Banner 03
        $this->add(
            array(
                'name' => 'Banner03',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner03Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //Banner 04
        $this->add(
            array(
                'name' => 'Banner04',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner04Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //Banner 05
        $this->add(
            array(
                'name' => 'Banner05',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner05Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //Banner 06
        $this->add(
            array(
                'name' => 'Banner06',
                'type' => 'File',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'galeria',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Banner06Url',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );
        //empresa
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
