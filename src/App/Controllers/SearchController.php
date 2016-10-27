<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.16.
 * Time: 15.21
 */

namespace App\Controllers;


use App\Services\Redis\UserRedisService;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{

    public function searchUserByNameAction(Request $request) {
        $user_id = $request->request->get("user_id");
        $name = $request->get("name");

        $result = $this->service->searchByName($name, $user_id);

        $this->redisService = new UserRedisService();
        foreach($result as $user) {
            $this->redisService->calculateUserSimilarity($user_id, $user['user_id']);
        }

        $finalResult = [];
        foreach ($result as $user) {
            $user['rank'] = $this->redisService->getUserSimilarityRank($user_id, $user['user_id']);
            $user['mutual_interests'] = $this->redisService->getMutualInterests($user_id, $user['user_id']);
            $finalResult[]= $user;
        }

        $this->redisService->removeSimilarityCalculations($user_id, $result);

        return $this->returnOk($finalResult);

    }

}