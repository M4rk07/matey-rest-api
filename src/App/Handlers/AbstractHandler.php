<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.3.17.
 * Time: 19.47
 */

namespace App\Handlers;


use App\MateyModels\ModelManagerFactoryInterface;
use App\Validators\UnsignedInteger;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractHandler
{

    protected $validator;
    protected $modelManagerFactory;

    public function __construct(
        ValidatorInterface $validator,
        \AuthBucket\OAuth2\Model\ModelManagerFactoryInterface $modelManagerFactory
    )
    {
        $this->validator = $validator;
        $this->modelManagerFactory = $modelManagerFactory;
    }

    public function validateValue ($value, array $constraints) {
        $errors = $this->validator->validate($value, $constraints);

        if (count($errors) > 0) {
            throw new InvalidRequestException([
                'error_description' => $errors->get(0)->getMessage(),
            ]);
        }
    }

    protected function getPaginationData(Request $request, array $defaults) {
        $maxId = $request->query->get('max_id');
        $count = $request->query->get('count');

        $params = array();
        if(empty($maxId)) $params['max_id'] = $defaults['def_max_id'];
        else {
            $params['max_id'] = $maxId;
            $this->validateValue($params['max_id'], array(
                new NotBlank(),
                new UnsignedInteger()
            ));
        }
        if(empty($count)) $params['count'] = $defaults['def_count'];
        else {
            $params['count'] = $count;
            $this->validateValue($params['count'], array(
                new NotBlank(),
                new UnsignedInteger()
            ));
        }

        return $params;
    }

}