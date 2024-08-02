<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Model\ProductListItem;
use App\Model\ProductListResponse;
use App\Model\ProductResponse;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{

    private ProductService $productService;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em,
                                ProductService $productService)
    {
        $this->productService=$productService;
        $this->em = $em;
    }

    /**
     * @Route("/api/v1/products/get-product-by-id/{id}", name="get-product-by-id", methods={"GET"})
     * @OA\Get(
     *     path="/api/v1/products/get-product-by-id/{id}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the product"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(ref=@Model(type=ProductResponse::class))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function getProductById(int $id):Response
    {
        $product = $this->productService->getProductById($id);
        if (!$product->getProduct()) {
            return $this->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    /**
     * @Route("/api/v1/products/get-all-products", name="get-products", methods={"GET"})
     * @OA\Get(
     *     path="/api/v1/products/get-all-products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all products or empty list if none found",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=ProductResponse::class))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No products found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No products found")
     *         )
     *     )
     * )
     */
    public function getAllProducts(): Response
    {
        $products = $this->productService->getAllProducts();

        if (empty($products->getItems())) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No products found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($products);
    }
}
