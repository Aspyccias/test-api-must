<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * Get all existing users
     * @Route("/admin/user", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse|Response
     */
    public function getAllAction(UserRepository $userRepository)
    {
        try {
            $users = $userRepository->findAll();

            $jsonUsers = [];
            foreach ($users as $user) {
                $jsonUsers[] = [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'login' => $user->getLogin(),
                ];
            }

            return $this->json($jsonUsers);
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new user with the required POST parameters:
     *   - name: string
     *   - login: string
     *   - password: string
     * @Route("/admin/user", methods={"POST"})
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
                        'name' => $user->getName(),
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
     * @Route("/admin/user/{id}", methods={"DELETE"})
     * @param User|null $user
     * @return Response
     */
    public function deleteAction(?User $user)
    {
        try {
            if (is_null($user)) {
                return new Response('Unknown user', Response::HTTP_NOT_FOUND);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return new Response('User deleted', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
