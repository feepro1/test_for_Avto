<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Client;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Model\ProductListResponse;
use App\Model\ProductResponse;

interface OrderServiceInterface
{
    public function createOrder(Client $client, $orderData):Order;
}