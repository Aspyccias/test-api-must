<?php

namespace App\Controller\Admin;

use App\Controller\ApiController;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Transformers\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends ApiController
{
    /**
     * Get all existing users
     * @Route("/admin/users", methods={"GET"})
     * @param UserRepository $userRepository
     * @return JsonResponse|Response
     */
    public function getAllAction(UserRepository $userRepository)
    {
        try {
            return $this->respondWithItems($userRepository->findAll(), new UserTransformer());
        } catch (\Exception $e) {
            return $this->errorInternalError();
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
                return $this->errorBadRequest('Please provide valid user information');
            }

            $user = new User();
            $form = $this->createForm(UserType::class, $user);

            $form->submit($data);
            if ($form->isValid()) {
                return $this->setStatusCode(Response::HTTP_CREATED)
                    ->respondWithItems($user, new UserTransformer());
            }

            return new Response('Please provide valid user information', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Delete a user
     * @Route("/admin/users/{userId}", methods={"DELETE"})
     * @param User|null $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteAction(?User $user, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($user)) {
                return $this->errorNotFound('Unknown user');
            }

            $entityManager->remove($user);
            $entityManager->flush();

            return $this->noContentResponse('User deleted');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }
}
