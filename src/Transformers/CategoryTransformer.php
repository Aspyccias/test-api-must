<?php


namespace App\Transformers;


use App\Entity\Category;
use Doctrine\ORM\PersistentCollection;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * @param Category $category
     * @return array
     */
    private function brandToArray(Category $category)
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];
    }

    /**
     * @param Category|PersistentCollection $categories
     * @return array
     */
    public function transform($categories)
    {
        if (!is_countable($categories)) {
            return $this->brandToArray($categories);
        }

        $categoriesArray = [];
        foreach ($categories as $category) {
            $categoriesArray[] = $this->brandToArray($category);
        }

        return $categoriesArray;
    }
}
