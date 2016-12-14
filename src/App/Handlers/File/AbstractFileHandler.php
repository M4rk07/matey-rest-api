<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 14.17
 */

namespace App\Handlers\File;


use App\MateyModels\ModelManagerFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractFileHandler implements FileHandlerInterface
{

    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    function resizeImage($file, $w, $h, $crop=FALSE) {

        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        unset($src);

        return $dst;
    }

}