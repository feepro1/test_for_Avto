<?php
namespace App\Controller;

use App\Entity\Client;
use App\Service\OrderService;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @Route("/api/v1/orders/pending-shipment", methods={"GET"})
     * @OA\Get(
     *     path="/api/v1/orders/pending-shipment",
     *     summary="Get all pending orders for shipment",
     *     tags={"Orders"},
     *     @OA\Response(
     *         response=200,
     *         description="List of pending orders",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="client", type="object",
     *                     @OA\Property(property="fio", type="string", example="John Doe")
     *                 ),
     *                 @OA\Property(property="items", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="productName", type="string", example="Product 1"),
     *                         @OA\Property(property="quantity", type="integer", example=2)
     *                     )
     *                 ),
     *                 @OA\Property(property="createdAt", type="string", example="2024-08-01T12:00:00Z"),
     *                 @OA\Property(property="status", type="string", example="Ожидает обработки")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function getPendingOrders(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $orders = $this->orderService->getPendingOrders();
        return $this->json($orders);
    }

    /**
     * @Route("/api/v1/orders/{id}/send-to-tk", methods={"POST"})
     *
     * @OA\Post(
     *     path="/api/v1/orders/{id}/send-to-tk",
     *     summary="Send an order to the transport company",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Order ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"trackingNumber"},
     *                 @OA\Property(property="trackingNumber", type="string", example="TRACK123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order sent to TK successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Order sent to TK successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function sendOrderToTk(int $id,Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->orderService->sendOrderToTk($id,$data['trackingNumber']);
            return $this->json(['message' => 'Order sent to TK successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * @Route("/api/v1/order/createOrder", methods={"POST"})
     * @OA\Post(
     *     path="/api/v1/order/createOrder",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"client", "order"},
     *                 @OA\Property(
     *                     property="client",
     *                     type="object",
     *                     @OA\Property(property="fio", type="string", example="John Doe"),
     *                     @OA\Property(property="number", type="string", example="+123456789"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="address", type="string", example="123 Main St, Springfield")
     *                 ),
     *                 @OA\Property(
     *                     property="order",
     *                     type="object",
     *                     @OA\Property(property="productIds", type="array", @OA\Items(type="integer")),
     *                     @OA\Property(property="quantities", type="array", @OA\Items(type="integer"))
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=123),
     *             @OA\Property(property="status", type="string", example="Ожидает отправки"),
     *             @OA\Property(property="internalTrackNumber", type="string", example="track_66abe6ec69820")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid data provided"))
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An unexpected error occurred"))
     *     )
     * )
     */
    public function createOrder(Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = new Client();
        $client->setFio($data['client']['fio']);
        $client->setNumber($data['client']['number']);
        $client->setEmail($data['client']['email']);
        $client->setAddress($data['client']['address']);


        try {
            $order = $this->orderService->createOrder($client, $data['order']);

            return $this->json([
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'internalTrackNumber' => $order->getTrackId()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
//            $this->logger->error('Order creation failed: ' . $e->getMessage());

            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
