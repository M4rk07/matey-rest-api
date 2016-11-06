<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 8.10.16.
 * Time: 23.13
 */

namespace App\Controllers;


use App\Handlers\ImageHandler;
use App\MateyModels\Device;
use App\MateyModels\ModelManagerFactoryInterface;
use App\MateyModels\User;
use App\Paths\Paths;
use App\Security\SaltGenerator;
use App\Security\SecretGenerator;
use App\Services\CloudStorageService;
use App\Validators\Name;
use AuthBucket\OAuth2\Exception\InvalidRequestException;
use AuthBucket\OAuth2\Exception\ServerErrorException;
use AuthBucket\OAuth2\Validator\Constraints\Password;
use AuthBucket\OAuth2\Validator\Constraints\Username;
use AuthBucket\OAuth2\Validator\Constraints\UsernameValidator;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookRequest;
use GuzzleHttp\Client;
use App\Handlers\Registration\RegistrationHandlerFactoryInterface;
use App\Handlers\Registration\RegistrationHandlerInterface;
use Mockery\Matcher\Not;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EmailValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{

    protected $registrationHandlerFactory;

    public function __construct(
        ValidatorInterface $validator,
        ModelManagerFactoryInterface $modelManagerFactory,
        RegistrationHandlerFactoryInterface $registrationHandlerFactory
    ) {
        parent::__construct($validator, $modelManagerFactory);
        $this->registrationHandlerFactory = $registrationHandlerFactory;
    }

    public function registerUserAction (Request $request, $action) {

        return $this->registrationHandlerFactory
            ->getRegistrationHandler($action)
            ->handle($request);

    }

    public function mergeAccountsAction(Request $request, Application $app, $action){

        if($action == 'facebook') $this->mergeNewFacebookAccount($request);
        else if($action == 'standard') $this->mergeNewStandardAccount($request);
        else throw new InvalidRequestException();

        return $app['login.controller']->loginAction($request);

    }

    public function mergeNewFacebookAccount (Request $request) {
        $user_id = $request->request->get('user_id');
        $fbToken = $request->request->get("fb_token");

        $this->validate($fbToken, [
            new NotBlank()
        ]);


        $fbUser = $this->checkFacebookToken($fbToken);
        if(empty($fbUser)) throw new InvalidRequestException([
            'error' => 'invalid_fb_token'
        ]);

        $user = new User();
        $user->setUserId($user_id)
            ->setFbId($fbUser->getId());

        $userManager = new UserManager();
        $userManager->createFacebookInfo($user);

    }

    public function mergeNewStandardAccount(Request $request) {

        $user_id = $request->request->get('user_id');
        $password = $request->request->get('password');

        $this->validate($password, [
            new NotBlank(),
            new Password()
        ]);

        $userManager = new UserManager();
        $user = $userManager->loadUserDataById($user_id);

        $userManager->createUserCredentials($user, $password);

    }

    public function registerDeviceAction (Request $request) {
        /*
         * fetch required data for device registration
         */
        $device_id = $request->request->get("device_id");
        $gcm = $request->request->get("gcm");
        $old_gcm = $request->request->get("old_gcm");

        /*
         * Validating gcm
         */
        $this->validate($gcm, [
            new NotBlank()
        ]);

        /*
        * If all parameters are here, then it is updating device operation
        */
        if(!empty($old_gcm) && !empty($device_id) && !empty($gcm)) return $this->updateDeviceGcm($device_id, $gcm, $old_gcm);
        /*
         * Else if only gcm token is here, then register new device
         */
        else if(!empty($gcm)) return $this->registerDevice($gcm);
        /*
         * In every other case, throw invalid request
         */
        else throw new InvalidRequestException();

    }

    public function updateDeviceGcm($device_id, $gcm, $old_gcm) {
        /*
         * Validating parameters
         */
        $this->validateNumericUnsigned($device_id);
        $this->validate($old_gcm, [
            new NotBlank()
        ]);

        $device = new Device();
        $device->setGcm($gcm);
        $device->setDeviceId($device_id);
        $deviceManager = new DeviceManager();
        if(!$deviceManager->updateDevice($device, $old_gcm)) throw new InvalidRequestException();

        /*
         * Device is successfully UPDATED!
         */
        return $this->returnOk();
    }

    public function registerDevice($gcm) {
        // generating secret
        $secretGenerator = new SecretGenerator();
        $deviceSecret = $secretGenerator->generateDeviceSecret();
        $device = new Device();
        $device->setGcm($gcm);
        $device->setDeviceSecret($deviceSecret);
        $deviceManager = new DeviceManager();
        $deviceManager->createDevice($device);
        /*
         * Return new device data, ID and SECRET, and device is REGISTERED!
         */
        return new JsonResponse(array(
            'device_id' => $device->getDeviceId(),
            'device_secret' => $device->getDeviceSecret()
        ), 200);
    }

}