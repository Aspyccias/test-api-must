<?php


namespace App\Controller\Store;


use App\Controller\ApiController;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Transformers\ProductTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store/products")
 */
class ProductController extends ApiController
{
    /**
     * Get all existing products
     * @Route("", methods={"GET"})
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getAllAction(ProductRepository $productRepository)
    {
        try {
            return $this->respondWithItems($productRepository->findAll(), new ProductTransformer());
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
                return $this->errorBadRequest('Please provide valid product information');
            }

            $product = new Product();
            $form = $this->createForm(ProductType::class, $product);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->createdResponse($product, new ProductTransformer());
            }

            return $this->errorBadRequest('Please provide valid product information');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Get one product
     * @Route("/{id}", methods={"GET"})
     * @param Product|null $product
     * @return JsonResponse
     */
    public function getAction(?Product $product)
    {
        try {
            if (is_null($product)) {
                return $this->errorNotFound('Unknown product');
            }

            return $this->respondWithItems($product, new ProductTransformer());
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Create a brand and return it
     * @Route("/{id}", methods={"PUT"})
     * @param Product $product
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function editAction(Product $product, Request $request, EntityManagerInterface $entityManager)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (is_null($data)) {
                return $this->errorBadRequest('Please provide valid product information');
            }

            $form = $this->createForm(ProductType::class, $product);

            $form->submit($data);
            if ($form->isValid()) {
                $entityManager->flush();

                return $this->createdResponse($product, new ProductTransformer());
            }

            return $this->errorBadRequest('Please provide valid product information');
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }

    /**
     * Delete a product
     * @Route("/{id}", methods={"DELETE"})
     * @param Product|null $product
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteAction(?Product $product, EntityManagerInterface $entityManager)
    {
        try {
            if (is_null($product)) {
                return $this->errorNotFound('Unknown product');
            }

            $entityManager->remove($product);
            $entityManager->flush();

            return $this->noContentResponse();
        } catch (\Exception $e) {
            return $this->errorInternalError();
        }
    }
}
