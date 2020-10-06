<?php


namespace App\Services;


use App\Repository\BrandRepository;

class BrandService
{
    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * BrandService constructor.
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * @return array
     */
    public function getAllBrands(): array
    {
        $brands = $this->brandRepository->findAll();

        $jsonBrands = [];
        foreach ($brands as $brand) {
            $jsonBrands[] = [
                'id' => $brand->getId(),
                'name' => $brand->getName(),
            ];
        }

        return $jsonBrands;
    }
}
