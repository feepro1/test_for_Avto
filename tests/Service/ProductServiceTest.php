<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Entity\Stock;
use App\Model\ProductListItem;
use App\Model\ProductListResponse;
use App\Model\ProductResponse;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{
    public function testGetProductById()
    {
        $stock = new Stock();
        $stock->setId(1)->setName("stock1")->setCity("someCity");

        $product = (new Product())
            ->setId(1)
            ->setName("test")
            ->setCost(1)
            ->setCount(1)
            ->setStock($stock);

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($product);

        $service = new ProductService($repository);
        $actual = $service->getProductById(1);

        $expected = new ProductResponse($product);

        $this->assertEquals($expected, $actual);
    }


    public function testGetAllProducts()
    {
        $stock = new Stock();
        $stock->setId(1)->setName("stock1")->setCity("someCity");

        $repository = $this->createMock(ProductRepository::class);
        $repository->expects($this->once())
            -> method('findAll')
            -> with([],['name'=>Criteria::ASC])
            -> willReturn([(new Product())->setId(1)->setName("test")->setCost(1)->setCount(1)->setStock($stock)]);

        $service = new ProductService($repository);
        $expected = new ProductListResponse([new ProductListItem(1,"test",1)]);

        $this->assertEquals($expected, $service->getAllProducts());
    }
}
