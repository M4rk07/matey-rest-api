<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 13.12.16.
 * Time: 21.42
 */

namespace App\Handlers\Group;


use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\Handlers\AbstractHandler;
use App\Handlers\Activity\Activity;
use App\MateyModels\Group;
use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractGroupHandler extends Activity implements GroupHandlerInterface
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

    public function gValidateGroupName($jsonData) {
        // Next validating all provided values and setting defaults
        // ---------TITLE
        if(!isset($jsonData->group_name)) throw new InvalidRequestException();

        $this->validateValue($jsonData->group_name, [
            new NotBlank(),
            new Length(array(
                'min' => DefaultNumbers::MIN_TITLE_CHARS,
                'max' => DefaultNumbers::MAX_TITLE_CHARS,
                'minMessage' => "Title must be at least {{ limit }} characters long.",
                'maxMessage' => "Title cannot be longer than {{ limit }} characters."
            ))
        ]);

        return $jsonData->group_name;
    }

    public function gValidateDescription($jsonData) {

        if(isset($jsonData->description)) {
            $this->validateValue($jsonData->description, [
                new NotBlank()
            ]);

            $returnValue = $jsonData->description;
        } else $returnValue = "";

        return $returnValue;

    }

    public function validateNumOfFiles (Request $request) {

        if($request->files->count() > 5) throw new InvalidRequestException(
            array('error' => ResponseMessages::TOO_MUCH_FILES)
        );

    }

}