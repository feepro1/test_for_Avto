<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $stock = new Stock();
        $stock->setName("stock1")->setCity("someCity");


        $manager->persist($stock);
        $manager->flush();


        $manager->persist((new Product())->setName("Gaika")->setCount(100)->setCost(10)->setStock($stock));
        $manager->persist((new Product())->setName("Bolt")->setCount(100)->setCost(15)->setStock($stock));
        $manager->persist((new Product())->setName("Shina")->setCount(100)->setCost(100)->setStock($stock));
        $manager->persist((new Product())->setName("Kovric")->setCount(100)->setCost(50)->setStock($stock));

        $manager->flush();
    }
}
