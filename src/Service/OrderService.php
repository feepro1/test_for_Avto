<?php
namespace App\Service;

use App\Entity\Order;
use App\Entity\Client;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService implements OrderServiceInterface
{
    private EntityManagerInterface $em;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;

    public function __construct(EntityManagerInterface $em, ProductRepository $productRepository,OrderRepository $orderRepository)
    {
        $this->em = $em;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }
    public function getPendingOrders(): array
    {
        $orders = $this->orderRepository->findBy(['status' => 'Ожидает обработки']);

        return array_map(function ($order) {
            return [
                'id' => $order->getId(),
                'client' => [
                    'fio' => $order->getClient()->getFio(),
                ],
                'items' => array_map(function ($orderItem) {
                    return [
                        'productName' => $orderItem->getProduct()->getName(),
                        'quantity' => $orderItem->getCount(),
                    ];
                }, $order->getOrderItem()->toArray()),
                'createdAt' => $order->getCreatedAt(),//->format('Y-m-d H:i:s'),
                'status' => $order->getStatus(),
            ];
        }, $orders);
    }

    public function sendOrderToTk(int $id,string $track_tk): void
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw new \Exception('Order not found.');
        }

        // Логика для отправки заявки в ТК
        // Например, обновление статуса заявки
        $order->setStatus('Отправлено в ТК');
        $order->setTrackTk($track_tk);
        $this->em->persist($order);
        $this->em->flush();
    }
    public function createOrder(Client $client, $orderData):Order
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $existedClient = $clientRepository->findOneBy([
            'fio' =>$client->getFio(),
            'email' =>$client->getEmail(),
            'number' =>$client->getNumber(),
            'address' =>$client->getAddress(),
        ]);
        if ($existedClient) {
            // Если клиент не найден, создаем нового
            $client = $existedClient;
        }



        $order = new Order();
        $order->setClient($client);
        $order->setCreatedAt("today");
        $order->setStatus('Ожидает обработки');

        $totalPrice = 0;
        $isSameCity = true;

        foreach ($orderData['items'] as $itemData) {
            $product = $this->productRepository->find($itemData['productId']);
            if ($product) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setCount($itemData['quantity']);
                $orderItem->setOrder($order);

                $totalPrice += $product->getCost() * $itemData['quantity'];

                if ($client->getAddress() !== $product->getStock()->getCity()) {
                    $isSameCity = false;
                }

                $this->em->persist($orderItem);
            }
        }

        if ($isSameCity) {
            $order->setStatus('Ожидает отправки');
            $order->setDeliveryCost(0);
        } else {
            $order->setDeliveryCost($this->calculateDeliveryPrice($orderData['items']));
        }

        $order->setTrackId($this->generateInternalTrackNumber());
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    private function calculateDeliveryPrice($items)
    {
        // Логика расчета стоимости доставки
        return 100.00; // Примерная стоимость
    }

    private function generateInternalTrackNumber()
    {
        return uniqid('track_');
    }
}
