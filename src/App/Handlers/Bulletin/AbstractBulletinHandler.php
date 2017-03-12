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
use App\Handlers\Activity\Activity;
use App\MateyModels\Group;
use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractBulletinHandler extends Activity
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

    public function getJsonObjects ($objects, $users, $type) {
        $manager = $this->modelManagerFactory->getModelManager($type);

        if(!is_array($objects)) $objects = array($objects);
        if(!is_array($users)) $users = array($users);

        $locationManager = $this->modelManagerFactory->getModelManager('location');

        $finalResult = array();
        foreach($objects as $object) {
            $arr = $object->asArray(array_diff($manager->getAllFields(), array('user_id')));
            foreach($users as $user) {
                if($user->getUserId() == $object->getUserId()) {
                    $arr['user'] = $user->asArray();
                    break;
                }
            }
            if($type == 'post' || $type == 'reply') {
                if ($object->getAttachsNum() > 0)
                    $arr['attachs'] = $object->getAttachsLocation($object->getAttachsNum());
                if ($object->getLocationsNum() > 0)
                    $arr['locations'] = $this->getLocations($object, $locationManager, $type);
            }

            $finalResult[]= $arr;
        }

        return $finalResult;
    }

    public function getLocations ($object, $locationManager, $type) {
        if($type == 'post') {
            $locations = $locationManager->readModelBy(array(
                'parent_id' => $object->getPostId(),
                'parent_type' => \App\MateyModels\Activity::POST_TYPE
            ), null, $object->getLocationsNum(), null, array('latt', 'longt'));
        } else {
            $locations = $locationManager->readModelBy(array(
                'parent_id' => $object->getReplyId(),
                'parent_type' => \App\MateyModels\Activity::REPLY_TYPE
            ), null, $object->getLocationsNum(), null, array('latt', 'longt'));
        }

        $arr = array();
        foreach($locations as $location) {
            $arr[] = $location->asArray();
        }

        return $arr;
    }

    public function fetchObjects($criteria, $limit, $offset, $type) {
        $manager = $this->modelManagerFactory->getModelManager($type);

        $objects = $manager->readModelBy($criteria, array('time_c' => 'DESC'), $limit, $offset, $manager->getAllFields());

        if($limit == 1 && is_array($objects)) return reset($objects);
        else return $objects;
    }

    public function getObjectOwners ($objects, $limit) {
        if(!is_array($objects)) $objects = array($objects);

        $userManager = $this->modelManagerFactory->getModelManager('user');
        $userIds = array();
        foreach ($objects as $object) {
            $userIds[] = $object->getUserId();
        }

        $users = $userManager->readModelBy(array(
            'user_id' => array_unique($userIds)
        ), null, $limit, null, array('user_id', 'first_name', 'last_name'));

        if($limit == 1 && is_array($users)) return reset($users);
        else return $users;
    }

}