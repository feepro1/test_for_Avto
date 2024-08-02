<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsControllerTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return \App\Kernel::class;
    }

    public function testGetAllProducts()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/products/get-all-products');
        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonFile(
            __DIR__."/response/ProductControllerTest_getAllProducts.json",
            $responseContent
        );
    }

//    public function testGetProductById()
//    {
//
//    }
}
