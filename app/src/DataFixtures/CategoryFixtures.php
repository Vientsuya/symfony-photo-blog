<?php

namespace App\DataFixtures;

use App\Entity\Category;

class CategoryFixtures extends AbstractBaseFixtures
{    
    public function loadData(): void
    {
        $titles = ["People", "Animals", "Mountains", "City", "Sea"];
        $category_images = array(
            "People" => "https://www.centreforcities.org/wp-content/uploads/2019/04/Sheffield_city_centre_x1650-1630x796.jpg",
            "Animals" => "https://www.worldanimalprotection.org/sites/default/files/styles/600x400/public/media/us_files/1014873_0.jpg?h=de92a0b7&itok=HWjFVXjf",
            "Mountains" => "https://img.freepik.com/free-photo/mesmerizing-scenery-green-mountains-with-cloudy-sky-surface_181624-27189.jpg?w=2000",
            "City" => "https://cdn.britannica.com/48/179448-138-40EABF32/Overview-New-York-City.jpg",
            "Sea" => "https://climate.nasa.gov/rails/active_storage/blobs/redirect/eyJfcmFpbHMiOnsibWVzc2FnZSI6IkJBaHBBbjRyIiwiZXhwIjpudWxsLCJwdXIiOiJibG9iX2lkIn19--0a7f9ec62ad04559ccea084556300e01789e456a/9827327865_98e0f0dc2d_o.jpg"
        );
        for ($i = 0; $i < count($titles); $i++) {
            $category = new Category();
            $category->setTitle($titles[$i]);
            $category->setImgUrl($category_images[$titles[$i]]);
            $category->setCreatedAt(new \DateTimeImmutable());
            $category->setSlug(null);
            $category->setUpdatedAt(new \DateTimeImmutable());
            
            $this->manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }

        $this->manager->flush();
    }
}
