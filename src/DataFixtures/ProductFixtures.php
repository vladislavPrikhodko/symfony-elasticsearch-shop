<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setPrice(random_int(1000, 10000));
            $product->setDescription("Description for product $i");
            $product->setIsActive(true);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setSlug("Product-$i");

            $manager->persist($product);
        }

        $manager->flush();
    }
}
