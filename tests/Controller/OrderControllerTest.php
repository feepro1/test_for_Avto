<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderControllerTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return \App\Kernel::class;
    }


    public function testCreateOrder()
    {
        $client = static::createClient();

        $data = [
            'client' => [
                'fio' => 'John Doe',
                'number' => '+123456789',
                'email' => 'john.doe@example.com',
                'address' => '123 Main St, Springfield',
            ],
            'order' => [
                'items' => [
                    ['productId' => 1, 'quantity' => 2]
                ]
            ]
        ];

        $client->request(
            'POST',
            '/api/v1/order/createOrder',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('internalTrackNumber', $responseData);

        $this->assertEquals('Ожидает обработки', $responseData['status']);
    }
}
