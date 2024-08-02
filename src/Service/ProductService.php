<?php

namespace App\Service;

use App\Entity\Product;
use App\Model\ProductListItem;
use App\Model\ProductListResponse;
use App\Model\ProductResponse;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductService implements ProductServiceInterface{

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }


    public function getProductById(int $id): ProductResponse
    {
        $product = $this->productRepository->findOneBy(['id' => $id]);



        return new ProductResponse($product);
    }

    public function getAllProducts(): ProductListResponse
    {
        $products = $this->productRepository->findAll([],['name'=>Criteria::ASC]);
        $items = array_map(
            fn(Product $item) => new ProductListItem(
                $item->getId(),$item->getName(),$item->getCost(),$item->getStock()
            ),
            $products
        );
        return new ProductListResponse($items);
    }
}