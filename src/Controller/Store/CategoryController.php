<?php

namespace App\Controller\Store;

use App\Controller\ApiController;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Transformers\CategoryTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store/categories")
 */
class CategoryController extends ApiController
{
    /**
     * Get all existing categories
     * @Route("", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse|Response
     */
    public function getAllAction(CategoryRepository $categoryRepository)
    {
        try {
            return $this->respondWithItems($categoryRepository->findAll(), new CategoryTransformer());
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Create a category
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse|Response
     */
    public function createAction(Request $request, EntityManagerInterface $entityManager)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return $this->errorBadRequest('Please provide valid category information');
            }

            $category = new Category();
            $form = $this->createForm(CategoryType::class, $category);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->persist($category);
                $entityManager->flush();

                return $this->setStatusCode(Response::HTTP_CREATED)
                    ->respondWithItems($category, new CategoryTransformer());
            }

            return $this->errorBadRequest('Please provide valid category information');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Update a category and return it
     * @Route("/{id}", methods={"PUT"})
     * @param Category|null $category
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function editAction(?Category $category, Request $request, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($category)) {
                return $this->errorNotFound('Unknown category');
            }

            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return $this->errorBadRequest('Please provide valid category information');
            }

            $form = $this->createForm(CategoryType::class, $category);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->flush();

                return $this->setStatusCode(Response::HTTP_CREATED)
                    ->respondWithItems($category, new CategoryTransformer());
            }

            return $this->errorBadRequest('Please provide valid category information');
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $this->errorInternalError();
        }
    }

    /**
     * Delete a category
     * @Route("/{id}", methods={"DELETE"})
     * @param Category|null $category
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteAction(?Category $category, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($category)) {
                return $this->errorNotFound('Unknown category');
            }

            $entityManager->remove($category);
            $entityManager->flush();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }
}
