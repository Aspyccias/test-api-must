<?php

namespace App\Controller\Store;

use App\Controller\ApiController;
use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use App\Transformers\BrandTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store/brands")
 */
class BrandController extends ApiController
{
    /**
     * Get all existing brands
     * @Route("", methods={"GET"})
     * @param BrandRepository $brandRepository
     * @return JsonResponse
     */
    public function getAllAction(BrandRepository $brandRepository)
    {
        try {
            return $this->respondWithItems($brandRepository->findAll(), new BrandTransformer());
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Create a brand and return it
     * @Route("", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function createAction(Request $request, EntityManagerInterface $entityManager)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return $this->errorBadRequest('Please provide valid brand information');
            }

            $brand = new Brand();
            $form = $this->createForm(BrandType::class, $brand);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->persist($brand);
                $entityManager->flush();

                return $this->createdResponse($brand, new BrandTransformer());
            }

            return $this->errorBadRequest('Please provide valid brand information');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Update a brand and return it
     * @Route("/{id}", methods={"PUT"})
     * @param Brand|null $brand
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function editAction(?Brand $brand, Request $request, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($brand)) {
                return $this->errorNotFound('Unknown brand');
            }

            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return $this->errorBadRequest('Please provide valid brand information');
            }

            $form = $this->createForm(BrandType::class, $brand);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->flush();

                return $this->respondWithItems($brand, new BrandTransformer());
            }

            return $this->errorBadRequest('Please provide valid brand information');
        } catch (\Exception $e) {
            echo $e->getMessage();
            return $this->errorInternalError();
        }
    }

    /**
     * Delete a brand
     * @Route("/{id}", methods={"DELETE"})
     * @param Brand|null $brand
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteAction(?Brand $brand, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($brand)) {
                return $this->errorNotFound('Unknown brand');
            }

            $entityManager->remove($brand);
            $entityManager->flush();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }
}
