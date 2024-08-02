<?php
namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\Client;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\Stock;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\ClientRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    public function testGetPendingOrders()
    {
        $client = new Client();
        $client->setFio('John Doe');

        $product = new Product();
        $product->setName('Product 1');

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setCount(2);

        $order = new Order();
        $order->setId(1);
        $order->setClient($client);
        $order->setStatus('Ожидает обработки');
        $order->setCreatedAt('2024-08-01');
        $order->addOrderItem($orderItem);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->once())
            ->method('findBy')
            ->with(['status' => 'Ожидает обработки'])
            ->willReturn([$order]);

        $productRepository = $this->createMock(ProductRepository::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $orderService = new OrderService($em, $productRepository, $orderRepository);
        $result = $orderService->getPendingOrders();

        $expected = [
            [
                'id' => 1,
                'client' => [
                    'fio' => 'John Doe',
                ],
                'items' => [
                    [
                        'productName' => 'Product 1',
                        'quantity' => 2,
                    ]
                ],
                'createdAt' => $order->getCreatedAt(),
                'status' => 'Ожидает обработки',
            ]
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSendOrderToTk()
    {
        $order = new Order();
        $order->setId(1);
        $order->setStatus('Ожидает обработки');

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($order);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())
            ->method('persist')
            ->with($order);
        $em->expects($this->once())
            ->method('flush');

        $productRepository = $this->createMock(ProductRepository::class);

        $orderService = new OrderService($em, $productRepository, $orderRepository);
        $orderService->sendOrderToTk(1, 'TRACK123');

        $this->assertEquals('Отправлено в ТК', $order->getStatus());
        $this->assertEquals('TRACK123', $order->getTrackTk());
    }

    public function testCreateOrder()
    {
        $client = (new Client())
            ->setFio('Test Client')
            ->setNumber('1234567890')
            ->setEmail('test@example.com')
            ->setAddress('Test City, Test Street, 1');

        $stock = (new Stock())
            ->setId(1)
            ->setName('Main Warehouse')
            ->setCity('Test City');

        $product = (new Product())
            ->setId(1)
            ->setName('Test Product')
            ->setCost(100)
            ->setCount(10)
            ->setStock($stock);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($product);

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'fio' => 'Test Client',
                'email' => 'test@example.com',
                'number' => '1234567890',
                'address' => 'Test City, Test Street, 1'
            ])
            ->willReturn($client);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->with(Client::class)
            ->willReturn($clientRepository);

        $entityManager->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive(
                [$this->isInstanceOf(OrderItem::class)],
                [$this->isInstanceOf(Order::class)]
            );

        $entityManager->expects($this->once())
            ->method('flush');

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderService = new OrderService($entityManager, $productRepository,$orderRepository);

        $orderData = [
            'items' => [
                ['productId' => 1, 'quantity' => 2]
            ]
        ];

        $order = $orderService->createOrder($client, $orderData);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('Ожидает обработки', $order->getStatus());
        $this->assertEquals(100, $order->getDeliveryCost());
//        $this->assertCount(1, $order->getOrderItem());
//        $this->assertEquals(200, $order->getTotalPrice());
    }
}
