<?php

namespace App\Model;

use App\Entity\Product;

class  ProductResponse
{
    private ?Product $product;

    /**
     * @param Product $product
     */
    public function __construct(?Product $product)
    {
        $this->product = $product;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }


}