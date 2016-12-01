<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 30.11.16.
 * Time: 02.03
 */

namespace App\Handlers\MateyUser;


use App\Validators\UserId;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserHandler extends AbstractUserHandler
{

    public function getUser(Request $request, $id)
    {
        if($id == "me") $id = $request->request->get('user_id');
        else {
            $errors = $this->validator->validate($id, [
                new NotBlank(),
                new UserId()
            ]);
            if (count($errors) > 0) {
                throw new InvalidRequestException([
                    'error_description' => 'The request includes an invalid parameter value.',
                ]);
            }
        }

        $userManager = $this->modelManagerFactory->getModelManager('user', 'mysql');

        $user = $userManager->readModelOneBy(array(
            'user_id' => $id
        ));

        if(!$user) throw new ResourceNotFoundException();

        $userMangerRedis = $this->modelManagerFactory->getModelManager('user', 'redis');
        $user = $userMangerRedis->getUserStatistics($user);

        return new JsonResponse($user->getValuesAsArray(), 200);
    }

}