<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 4.11.16.
 * Time: 15.59
 */

namespace App\Handlers\Registration;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface RegistrationHandlerInterface
{
    /**
     * Handle corresponding grant type logic.
     *
     * @param Request $request Incoming request object.
     *
     * @return JsonResponse The json response object for token endpoint.
     */
    public function handle(Request $request);
}