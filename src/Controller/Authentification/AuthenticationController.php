<?php

namespace App\Controller\Authentification;

use App\Controller\ApiController;
use App\Services\AuthenticationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationController extends ApiController
{
    /**
     * Authenticate user with login and password and get a new token in response.
     * If a token already exists and has not expired, returns this token.
     * @Route("/login", methods={"POST"})
     * @param Request $request
     * @param AuthenticationService $authenticationService
     * @return Response|JsonResponse
     */
    public function loginAction(Request $request, AuthenticationService $authenticationService)
    {
        try {
            $authenticationToken = $authenticationService->getToken($request);

            return $this->json(
                [
                    'token' => $authenticationToken,
                ]
            );
        } catch (\InvalidArgumentException $e) {
            return $this->errorUnauthorized($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Logout a user from its authentication token
     * @Route("/logout", methods={"GET"})
     * @param Request $request
     * @param AuthenticationService $authenticationService
     * @throws \Exception
     */
    public function logoutAction(Request $request, AuthenticationService $authenticationService)
    {
        try {
            $authenticationService->deleteToken($request);

            return $this->respondWithArray('Logout successful');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }
}
