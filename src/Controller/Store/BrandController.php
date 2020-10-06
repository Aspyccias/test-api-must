<?php

namespace App\Controller\Store;

use App\Controller\ApiController;
use App\Repository\BrandRepository;
use App\Transformers\BrandTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store/brands")
 */
class BrandController extends ApiController
{
    /**
     * Get all existing users
     * @Route("", methods={"GET"})
     * @param BrandRepository $brandRepository
     * @return JsonResponse|Response
     */
    public function getAllAction(BrandRepository $brandRepository)
    {
        try {
            return $this->respondWithItems($brandRepository->findAll(), new BrandTransformer());
        } catch (\Exception $e) {
            $this->errorInternalError();
        }
    }
}
