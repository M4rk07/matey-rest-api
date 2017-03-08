<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 3.3.17.
 * Time: 16.10
 */

namespace App\Handlers\Post;

use App\Algos\FeedRank\FeedRank;
use App\Constants\Defaults\DefaultDates;
use App\Constants\Defaults\DefaultNumbers;
use App\Constants\Messages\ResponseMessages;
use App\MateyModels\Activity;
use App\MateyModels\FeedEntry;
use App\MateyModels\Group;
use App\MateyModels\Post;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use Mockery\CountValidator\Exception;
use Mockery\Matcher\Not;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class StandardPostHandler extends AbstractPostHandler
{

    public function createPost(Application $app, Request $request) {
        // Get user id based on token
        $userId = $request->request->get('user_id');

        // Getting json data in relation to Content-Type
        $contentType = $request->headers->get('Content-Type');
        $jsonData = $this->getJsonPostData($request, $contentType);

        if($request->files->count() > 5) throw new InvalidRequestException(
            array('error' => ResponseMessages::TOO_MUCH_FILES)
        );

        // Creating necessary data managers.
        $postManager = $this->modelManagerFactory->getModelManager('post');
        $post = $postManager->getModel();

        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $activity = $activityManager->getModel();

        // Creating a Post model
        $post->setTitle($jsonData['title'])
            ->setText($jsonData['text'])
            ->setAttachsNum($request->files->count())
            ->setLocationsNum(count($jsonData['locations']))
            ->setUserId($userId)
            ->setGroupId($jsonData['group_id']);

        // Starting transaction
        $postManager->startTransaction();
        try {
            // Writing Post model to database
            $post = $postManager->createModel($post);

            // Creating Activity model
            $activity->setSourceId($post->getPostId())
                ->setUserId($userId)
                ->setParentId($jsonData['group_id'])
                ->setParentType(Activity::GROUP_TYPE)
                ->setActivityType(Activity::POST_TYPE);

            // Writing Activity model to database
            $activityManager->createModel($activity);

            // Commiting transaction on success
            $postManager->commitTransaction();
        } catch (\Exception $e) {
            // Rollback transaction on failure
            $postManager->rollbackTransaction();
            throw $e;
        }

        // Calling the service for uploading Post attachments to S3 storage
        if(strpos($contentType, 'multipart/form-data') === 0) {
            $app['matey.file_handler.factory']->getFileHandler('post_attachment')->upload($app, $request, $post->getPostId());
        }

        // Pushing newly created post to news feed of followers
        $app['matey.feed_handler']->pushNewPost($post);

        return new JsonResponse(null, 200);

    }

    // Method for retrieving json file from request
    public function getJsonPostData (Request $request, $contentType) {
        /*
         * This variable will store values from json,
         * or default if none.
         */
        $returnValues = array();

        // Retrieving json based on Content-Type
        if($contentType == 'application/json') {
            $jsonData = $request->getContent();
            $jsonData = json_decode($jsonData);
        } else if(strpos($contentType, 'multipart/form-data') === 0) {
            $jsonData = $request->request->get('json_data');
            $jsonData = json_decode($jsonData);
        }

        // JSON must be provided, and title of the Post
        if(empty($jsonData) || !isset($jsonData->title)) throw new InvalidRequestException();

        // Next validating all provided values and setting defaults
        // ---------TITLE
        $this->validateValue($jsonData->title, [
            new NotBlank(),
            new Length(array(
                'min' => DefaultNumbers::MIN_TITLE_CHARS,
                'max' => DefaultNumbers::MAX_TITLE_CHARS,
                'minMessage' => "Title must be at least {{ limit }} characters long.",
                'maxMessage' => "Title cannot be longer than {{ limit }} characters."
            ))
        ]);

        $returnValues['title'] = $jsonData->title;

        // ---------GROUP ID
        if(isset($jsonData->group_id)) {

            $this->validateValue($jsonData->group_id, [
                new NotBlank(),
                new UnsignedInteger()
            ]);

            $returnValues['group_id'] = $jsonData->group_id;
        } else $returnValues['group_id'] = Group::DEFAULT_GROUP;

        // ---------TEXT
        if(isset($jsonData->text)) {
            $this->validateValue($jsonData->text, [
                new NotBlank()
            ]);

            $returnValues['text'] = $jsonData->text;
        } else $returnValues['text'] = "";

        // ---------LOCATIONS
        if(isset($jsonData->locations)) {

            foreach($jsonData->locations as $value) {
                $this->validateValue($value->latt, [
                    new NotBlank()
                ]);
                $this->validateValue($value->longt, [
                    new NotBlank()
                ]);
            }

            $returnValues['locations'] = $jsonData->locations;
        } else $returnValues['locations'] = array();

        return $returnValues;

    }

}