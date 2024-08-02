<?php

namespace App\Service;

use App\Entity\Product;
use App\Model\ProductListResponse;
use App\Model\ProductResponse;

interface ProductServiceInterface
{
    public function getProductById(int $id): ProductResponse;
    public function getAllProducts(): ProductListResponse;
}