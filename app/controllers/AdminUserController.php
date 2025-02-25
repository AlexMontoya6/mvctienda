<?php

class AdminUserController extends Controller
{
    private $model;
    private $errorsCreate = [];
    private $errorsUpdate = [];
    private $errorsDelete = [];

    public function __construct()
    {
        $this->model = $this->model('AdminUser');
    }

    public function index()
    {
        $session = new Session();

        $users = $this->model->getUsers();

        if ($session->getLogin()) {

            $data = [
                'title' => 'Administración de usuarios',
                'menu' => false,
                'admin' => true,
                'data' => $users,
            ];

            $this->view('admin/users/index', $data);

        } else {

            header('location:' . ROOT . 'admin');

        }

    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password1 = $_POST['password1'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            $dataForm = [
                'name' => $name,
                'email' => $email,
                'password' => $password1,
            ];

            if (store($name, $email, $password1, $password2)) {

                if ($this->model->createAdminUser($dataForm)) {

                    header('location:' . ROOT . 'adminUser');

                } else {

                    $data = [
                        'title' => 'Error durante la creación del usuario',
                        'menu' => false,
                        'subtitle' => 'Error al crear un nuevo usuario administrador',
                        'text' => 'Sucedió un error durante la creación de un nuevo administrador',
                        'color' => 'alert-danger',
                        'url' => 'adminUser',
                        'colorButton' => 'btn-danger',
                        'textButton' => 'Volver',
                    ];

                    $this->view('mensaje', $data);

                }
            } else {

                $data = [
                    'title' => 'Administración de usuarios - Alta',
                    'menu' => false,
                    'admin' => true,
                    'errors' => $this->errorsCreate,
                    'data' => $dataForm,
                ];

                $this->view('admin/users/create', $data);
            }
        }
    }

    private function store($name = '', $email = '', $password1 = '', $password2 = '')
    {


        if (empty($name)) {
            array_push($this->errorsCreate, 'El nombre es requerido');
        }
        if (empty($email)) {
            array_push($this->errorsCreate, 'El correo electrónico es requerido');
        }
        if (empty($password1)) {
            array_push($this->errorsCreate, 'La contraseña es requerida');
        }
        if (empty($password2)) {
            array_push($this->errorsCreate, 'Repetir la contraseña es requerida');
        }
        if ($password1 != $password2) {
            array_push($this->errorsCreate, 'Las contraseñas deben ser iguales');
        }

        if (count($this->errorsCreate) == 0) {

            return true;
        }
        return false;
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password1 = $_POST['password1'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            $status = $_POST['status'] ?? '';

            if (update($name, $email, $password1, $password2, $status)) {
                $data = [
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'password' => $password1,
                    'status' => $status,
                ];
                $errors = $this->model->setUser($data);

                if (empty($errors)) {
                    header('location:' . ROOT . 'adminUser');
                }

            } else {
                $user = $this->model->getUserById($id);
                $status = $this->model->getConfig('adminStatus');

                $data = [
                    'title' => 'Administración de usuarios - Modificación',
                    'menu' => false,
                    'admin' => true,
                    'errors' => $this->errorsUpdate,
                    'status' => $status,
                    'data' => $user,
                ];

                $this->view('admin/users/update', $data);
            }
        }
    }
    private function update($name = '', $email = '', $password1 = '', $password2 = '', $status = '')
    {

        if (empty($name)) {
            array_push($this->errorsUpdate, 'El nombre de usuario es requerido');
        }
        if (empty($email)) {
            array_push($this->errorsUpdate, 'El email del usuario es requerido');
        }
        if ($status == '') {
            array_push($$this->errorsUpdate, 'Selecciona el estado del usuario');
        }
        if (!empty($password1) || !empty($password2)) {
            if ($password1 != $password2) {
                array_push($this->errorsUpdate, 'Las contraseñas no coinciden');
            }
        }

        if (empty($this->errorsUpdate)) {

            return true;
        }
        return false;

    }


    public function delete($id)
    {


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            destroy($id);
        }

        $user = $this->model->getUserById($id);
        $status = $this->model->getConfig('adminStatus');
        $data = [
            'title' => 'Administración de usuarios - Eliminación',
            'menu' => false,
            'admin' => true,
            'errors' => $this->errors,
            'status' => $status,
            'data' => $user,
        ];

        $this->view('admin/users/delete', $data);
    }


    private function destroy($id)
    {
        $this->errorsDelete = $this->model->delete($id);
        if (empty($this->errorsDelete)) {
            header('location:' . ROOT . 'adminUser');
        }
    }







}