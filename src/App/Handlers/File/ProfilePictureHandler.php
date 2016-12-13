<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 12.12.16.
 * Time: 14.18
 */

namespace App\Handlers\File;


use App\Upload\CloudStorageUpload;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilePictureHandler extends AbstractFileHandler
{

    public function upload (Application $app, Request $request)
    {
        $user_id = $request->request->get('user_id');
        $picture = $request->files->get('picture');

        $errors = $this->validator->validate($picture, [
            new NotBlank(),
            new Image(array(
                'allowLandscape' => false,
                'allowPortrait' => false,
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'maxSize' => '500k'
            ))
        ]);
        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }

        $originalPicture = $picture->getRealPath();

            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 100, 100), NULL, 90);
                $picture100x100 = ob_get_contents();
            ob_end_clean(); // stop this output buffer
            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 200, 200), NULL, 90);
                $picture200x200 = ob_get_contents();
            ob_end_clean(); // stop this output buffer
            ob_start(); // start a new output buffer
                imagejpeg( $this->resizeImage($originalPicture, 480, 480), NULL, 90);
                $picture480x480 = ob_get_contents();
            ob_end_clean(); // stop this output buffer

        $uploads = array(
            array(
                'file' => file_get_contents($originalPicture),
                'name' => 'profile_pictures/originals/'.$user_id.'.jpg'
            ),
            array(
                'file' => $picture100x100,
                'name' => 'profile_pictures/100x100/'.$user_id.'.jpg'
            ),
            array(
                'file' => $picture200x200,
                'name' => 'profile_pictures/200x200/'.$user_id.'.jpg'
            ),
            array(
                'file' => $picture480x480,
                'name' => 'profile_pictures/480x480/'.$user_id.'.jpg'
            ),
        );

        $cloudStorage = new CloudStorageUpload($uploads);
        $cloudStorage->upload();

        return new JsonResponse(null, 200);
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

    function compress($source, $destination, $quality) {

        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);

        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);

        else throw new InvalidArgumentException();

        imagejpeg($image, $destination, $quality);

        return $destination;
    }

}