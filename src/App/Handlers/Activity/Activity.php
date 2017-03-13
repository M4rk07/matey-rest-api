<?php
namespace App\Handlers\Activity;

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 11.3.17.
 * Time: 16.53
 */
class Activity extends AbstractActivity
{
    public function createActivity($sourceId, $userId, $parentId, $parentType, $activityType) {
        $activityManager = $this->modelManagerFactory->getModelManager('activity');
        $activity = $activityManager->getModel();

        $activity->setSourceId($sourceId)
            ->setUserId($userId)
            ->setParentType($parentType)
            ->setActivityType($activityType)
            ->setParentId($parentId);

        // Writing Activity model to database
        $activityManager->createModel($activity);
    }
}