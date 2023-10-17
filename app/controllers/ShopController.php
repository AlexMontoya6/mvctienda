<?php

class ShopController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = $this->model('Shop');
    }

    public function index()
    {
        $session = new Session();

        if ($session->getLogin()) {

            $mostSold = $this->model->getMostSold();

            $data = [
                'title' => 'Bienvenid@ a nuestra exclusiva tienda de productos',
                'menu' => true,
                'subtitle' => 'Bienvenid@ a nuestra tienda',
                'data' => $mostSold,
            ];

            $this->view('shop/index', $data);
        } else {
            header('location:' . ROOT);
        }
    }

    public function logout()
    {
        $session = new Session();
        $session->logout();
        header('location:' . ROOT);
    }

    public function show($id)
    {
        echo $id;
    }
}