<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 10.3.17.
 * Time: 19.11
 */

namespace App\Handlers\Post;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\AbstractHandler;
use App\MateyModels\Group;
use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractBulletinHandler extends AbstractHandler
{

    // Method for retrieving json file from request
    public function getJsonPostData (Request $request, $contentType) {
        // Retrieving json based on Content-Type
        if($contentType == 'application/json') {
            $jsonData = $request->getContent();
            $jsonData = json_decode($jsonData);
        } else if(strpos($contentType, 'multipart/form-data') === 0) {
            $jsonData = $request->request->get('json_data');
            $jsonData = json_decode($jsonData);
        }

        if(empty($jsonData)) throw new InvalidRequestException();

        return $jsonData;
    }

    public function gValidateTitle($jsonData) {
        // Next validating all provided values and setting defaults
        // ---------TITLE
        if(!isset($jsonData->title)) throw new InvalidRequestException();

        $this->validateValue($jsonData->title, [
            new NotBlank(),
            new Length(array(
                'min' => DefaultNumbers::MIN_TITLE_CHARS,
                'max' => DefaultNumbers::MAX_TITLE_CHARS,
                'minMessage' => "Title must be at least {{ limit }} characters long.",
                'maxMessage' => "Title cannot be longer than {{ limit }} characters."
            ))
        ]);

        return $jsonData->title;
    }

    public function gValidateText($jsonData) {

        if(isset($jsonData->text)) {
            $this->validateValue($jsonData->text, [
                new NotBlank()
            ]);

            $returnValue = $jsonData->text;
        } else $returnValue = "";

        return $returnValue;

    }

    public function gValidateGroupId($jsonData) {

        if(isset($jsonData->group_id)) {

            $this->validateValue($jsonData->group_id, [
                new NotBlank(),
                new UnsignedInteger()
            ]);

            $returnValue = $jsonData->group_id;
        } else $returnValue = Group::DEFAULT_GROUP;

        return $returnValue;

    }

    public function gValidateLocations($jsonData) {

        if(isset($jsonData->locations)) {

            foreach($jsonData->locations as $value) {
                $this->validateValue($value->latt, [
                    new NotBlank()
                ]);
                $this->validateValue($value->longt, [
                    new NotBlank()
                ]);
            }

            $returnValue = $jsonData->locations;
        } else $returnValue = array();

        return $returnValue;

    }

    public function validateNumOfFiles (Request $request) {

        if($request->files->count() > 5) throw new InvalidRequestException(
            array('error' => ResponseMessages::TOO_MUCH_FILES)
        );

    }

}