<?php

namespace App\DataFixtures;

use App\Entity\Category;

class CategoryFixtures extends AbstractBaseFixtures
{    
    public function loadData(): void
    {
        $titles = ["People", "Animals", "Mountains", "City", "Sea"];
        for ($i = 0; $i < count($titles); $i++) {
            $category = new Category();
            $category->setTitle($titles[$i]);
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setSlug(null);
            $category->setUpdatedAt(new \DateTimeImmutable());
            
            $this->manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }

        $this->manager->flush();
    }
}
