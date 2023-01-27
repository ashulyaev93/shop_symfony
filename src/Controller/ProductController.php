<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepository): Response
    {
        $items = $productRepository->findAll();

        return $this->render(
            'product/index.html.twig',
            [
                'items' => $items,
            ]
        );
    }

    #[Route('/order', name: 'order')]
    public function order(ProductRepository $productRepository): Response
    {
        $items = $productRepository->findAll();

        return $this->render(
            'product/order.html.twig',
            [
                'selected_prod' => isset($_GET['id']) ? (int) $_GET['id'] : 0,
                'items' => $items,
            ]
        );
    }

    private function divide_sample($str, $out = []) {
        preg_match_all('~(\d+)?(?(1)|\D+)~', $str, $arr);

        foreach ($arr[0] as $item) {
            ctype_digit($item) ? $out['int'] = $item : $out['txt'] = $item;
        }

        return $out['txt'];
    }

    #[Route('/new_order', name: 'newOrder')]
    public function newOrder(ProductRepository $productRepository): Response
    {
        $id = $productRepository->find($_POST["product"]);

        if(!isset($id)){
            return new Response("Не выбран продукт");
        }

        $price = $id->getPrice();
        $name = $id->getName();

        $value = $_POST["value"];
        $pieces = $this->divide_sample($value);

        if ($pieces == "IT"){
            $sum = $price + $price*0.22;
            return new Response("Конечная стоимость для продукта ".$name." = ".$sum." евро");
        } elseif ($pieces == "GE"){
            $sum = $price + $price*0.24;
            return new Response("Конечная стоимость для продукта ".$name." = ".$sum." евро");
        } elseif ($pieces == "DE"){
            $sum = $price + $price*0.19;
            return new Response("Конечная стоимость для продукта ".$name." = ".$sum." евро");
        } else {
            return new Response("Введён неверный tax номер");
        }
    }
}
