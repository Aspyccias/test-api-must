<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Services\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Get all existing users
     * @Route("/admin/users", methods={"GET"})
     * @param UserService $userService
     * @return JsonResponse|Response
     */
    public function getAllAction(UserService $userService)
    {
        try {
            return $this->json($userService->getAllUsers());
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new user with the required POST parameters:
     *   - name: string
     *   - login: string
     *   - password: string
     * @Route("/admin/users", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function createAction(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return new Response('Please provide valid user information', Response::HTTP_BAD_REQUEST);
            }

            $user = new User();
            $form = $this->createForm(UserType::class, $user);

            $form->submit($data);
            if ($form->isValid()) {
                return $this->json(
                    [
                        'id' => $user->getId(),
                        'name' => $user->getUserName(),
                        'login' => $user->getLogin(),
                    ],
                    Response::HTTP_CREATED
                );
            }

            return new Response('Please provide valid user information', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a user
     * @Route("/admin/users/{userId}", methods={"DELETE"})
     * @param User|null $user
     * @param UserService $userService
     * @return Response
     */
    public function deleteAction(?User $user, UserService $userService)
    {
        try {
            if ($userService->deleteUser($user) === false) {
                return new Response('Unknown user', Response::HTTP_NOT_FOUND);
            }

            return new Response('User deleted', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
