<?php


namespace App\Transformers;


use App\Entity\Brand;
use Doctrine\ORM\PersistentCollection;
use League\Fractal\TransformerAbstract;

class BrandTransformer extends TransformerAbstract
{
    /**
     * @param Brand $brand
     * @return array
     */
    private function brandToArray(Brand $brand)
    {
        return [
            'id' => $brand->getId(),
            'name' => $brand->getName(),
        ];
    }

    /**
     * @param Brand|PersistentCollection $brands
     * @return array
     */
    public function transform($brands)
    {
        if (!is_countable($brands)) {
            return $this->brandToArray($brands);
        }

        $brandsArray = [];
        foreach ($brands as $brand) {
            $brandsArray[] = $this->brandToArray($brand);
        }

        return $brandsArray;
    }
}
