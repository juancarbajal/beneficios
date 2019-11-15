<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 22/12/15
 * Time: 05:29 PM
 */
namespace EmpresaCliente\Service;

use Intervention\Image\Image;

class Resize extends Image
{

    /**
     * @param string $path     direccion donde se guardara la imagen
     * @param Image  $image    objeto de image
     * @param string $ext      estencion de la imagen
     * @param string $fileName nombre de la imagen sin extencion
     * @param array  $config   array de las variable globales
     * @param string $size     nombre de la variable global
     * @param string $postname postfijo que se agregara al nombre de la imagen
     * @param bool   $resize   valor booleano que indica si la imagen va hacer redimencionado
     */
    public function resizeWidth($path, $image, $ext, $fileName, $config, $size, $postname, $resize)
    {
        if ($resize[$size]['width']) {
            $image->widen($config[$size]['width']);
            $image->resizeCanvas($config[$size]['width'], $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        } else {
            $image->resizeCanvas($config[$size]['width'], $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        }
    }

    /**
     * @param string $path     direccion donde se guardara la imagen
     * @param Image  $image    objeto de image
     * @param string $ext      estencion de la imagen
     * @param string $fileName nombre de la imagen sin extencion
     * @param array  $config   array de las variable globales
     * @param string $size     nombre de la variable global
     * @param string $postname postfijo que se agregara al nombre de la imagen
     * @param bool   $resize   valor booleano que indica si la imagen va hacer redimencionado
     */
    public function resizeHeight($path, $image, $ext, $fileName, $config, $size, $postname, $resize)
    {
        if ($resize[$size]['height']) {
            $image->heighten($config[$size]['height']);
            $image->resizeCanvas($config[$size]['width'], $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        } else {
            $image->resizeCanvas($config[$size]['width'], $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        }
    }

    /**
     * @param $image Image objeto de image
     * @param $config array de las variable globales
     * @param $size string nombre de la variable global
     * @return bool
     */
    public function isResize($image, $config, $size)
    {
        $isResize['width'] = false;
        $isResize['height'] = false;

        if ($image->getWidth() > $config[$size]['width']) {
            $isResize['width'] = true;
        }
        if ($image->getHeight() > $config[$size]['height']) {
            $isResize['height'] = true;
        }
        
        return $isResize;
    }

    /**
     * @param string $path     direccion donde se guardara la imagen
     * @param Image  $image    objeto de image
     * @param string $ext      estencion de la imagen
     * @param string $fileName nombre de la imagen sin extencion
     * @param string $postname postfijo que se agregara al nombre de la imagen
     */
    public function rename($path, $image, $ext, $fileName, $postname)
    {
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
    }

    /**
     * @param string $path direccion donde se guardara la imagen
     * @param Image $image objeto de image
     * @param string $ext estencion de la imagen
     * @param string $fileName nombre de la imagen sin extencion
     * @param array $config array de las variable globales
     * @param string $size nombre de la variable global
     * @param string $postname postfijo que se agregara al nombre de la imagen
     * @param bool $resize valor booleano que indica si la imagen va hacer redimencionado
     * @param string $height
     */
    public function resizeWidthLogo($path, $image, $ext, $fileName, $config, $size, $postname, $resize)
    {
        if ($resize[$size]['width']) {
            $image->widen($config[$size]['width']);
            $image->resizeCanvas($config[$size]['width'], $image->getHeight(), 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        } else {
            $image->resizeCanvas($config[$size]['width'], $image->getHeight(), 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        }
    }

    /**
     * @param string $path direccion donde se guardara la imagen
     * @param Image $image objeto de image
     * @param string $ext estencion de la imagen
     * @param string $fileName nombre de la imagen sin extencion
     * @param array $config array de las variable globales
     * @param string $size nombre de la variable global
     * @param string $postname postfijo que se agregara al nombre de la imagen
     * @param bool $resize valor booleano que indica si la imagen va hacer redimencionado
     * @param string $height
     */
    public function resizeHeightLogo($path, $image, $ext, $fileName, $config, $size, $postname, $resize)
    {
        if ($resize[$size]['height']) {
            $image->heighten($config[$size]['height']);
            $image->resizeCanvas($image->getWidth(), $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        } else {
            $image->resizeCanvas($image->getWidth(), $config[$size]['height'], 'center', false, '#FFFFFF');
            $image->encode($ext, 75);
            $image->save($path . $fileName . $postname . '.' . $ext);
        }
    }
}
