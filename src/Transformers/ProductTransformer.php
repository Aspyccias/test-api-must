<?php


namespace App\Transformers;


use App\Entity\Product;
use Doctrine\ORM\PersistentCollection;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * @param Product $product
     * @return array
     */
    private function brandToArray(Product $product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'brand' => (new BrandTransformer())->transform($product->getBrand()),
            'categories' => (new CategoryTransformer())->transform($product->getCategories()),
            'url' => $product->getUrl(),
            'md5' => md5($product->getId()),
        ];
    }

    /**
     * @param Product|PersistentCollection $products
     * @return array
     */
    public function transform($products)
    {
        if (!is_countable($products)) {
            return $this->brandToArray($products);
        }

        $productsArray = [];
        foreach ($products as $brand) {
            $productsArray[] = $this->brandToArray($brand);
        }

        return $productsArray;
    }
}
