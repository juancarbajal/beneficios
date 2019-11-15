<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 26/07/16
 * Time: 04:08 PM
 */

namespace Application\Service;


class MenuCategorias
{
    protected $serviceLocator;

    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getDataCategorias($pais)
    {
        $categoriaTable = $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
        $categorias = $categoriaTable->getBuscarCategoriaXPais($pais);
        $categoriasfooter = $categoriaTable->getBuscarCategoriaXPais($pais);
        $categories = $categoriaTable->getBuscarCategoriaXPais($pais);
        $catotros = $categoriaTable->getBuscarCatOtros($pais);

        $category = null;
        foreach ($categories as $key => $dato) {
            if ($key == 0) {
                $category = $dato->Slug;
            }
        }

        return array($categorias, $categoriasfooter, $catotros, $category);
    }
}