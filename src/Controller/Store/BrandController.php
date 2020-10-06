<?php

namespace App\Controller\Store;

use App\Services\BrandService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store/brands")
 */
class BrandController extends AbstractController
{
    /**
     * Get all existing users
     * @Route("", methods={"GET"})
     * @param BrandService $brandService
     * @return JsonResponse|Response
     */
    public function getAllAction(BrandService $brandService)
    {
        try {
            return $this->json($brandService->getAllBrands());
        } catch (\Exception $e) {
            return new Response('Unexpected error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
