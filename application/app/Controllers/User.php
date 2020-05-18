<?php

namespace RestAPI\Controllers;

use RestAPI\Models\User as UserModel;

class User extends Controller
{
    private $userModel;
    private $input;

    public function __construct($router)
    {
        $this->userModel = new UserModel;

        $this->input = $this->parseJsonParameters();

        parent::__construct();
    }

    public function index(): void
    {
        // Validate access
        $this->validateAccess();

        $page = $this->input['page'] ?? 1;
        $perPage = $this->input['perPage'] ?? 20;

        $allUsers = $this->userModel->getAllUsers($page, $perPage);

        echo $this->json([
            'count' => count($allUsers['data']),
            'page' => $page,
            'totalPages' => $allUsers['totalPages'],
            'data' => $allUsers['data']
        ], 200);
    }

    public function show(array $data)
    {
        // Validate access
        $this->validateAccess();

        $id = $data['id'];

        $user = $this->userModel->getUserById($id);

        if (!$user or count($user) == 0) {
            echo $this->json([
                'success' => false,
                'error' => 'Usuário não encontrado.'
            ], 404);

            die;
        };

        unset($user[0]['password']);

        $mlCount = $this->userModel->getDrinkWater($user[0]['iduser']);

        echo $this->json([
            'success' => true,
            'data' => array_merge($user[0], ['drink_counter' => $mlCount])
        ], 200);
    }

    public function store($data)
    {
        if ($this->_validate($this->input)) {
            $this->userModel->createUser($this->input);
            
            echo $this->json([], 201);
        }
    }

    public function update(array $data)
    {
        // Validate access
        $this->validateAccess();

        // Get user by auth token
        $user = $this->userModel->getUserByToken($this->getAcessToken());

        $id = $data['id'];

        if (!$user or count($user) == 0) {
            echo $this->json([
                'success' => false,
                'error' => 'Usuário não encontrado.'
            ], 404);

            die;
        };

        // Check if user id found by token is equals incoming id
        if ($user[0]['user_id'] != $id) {
            echo $this->json([
                'success' => false,
                'error' => 'Você só pode editar seu prório usuário.'
            ], 403);

            die;
        }

        // If all correct, update user info
        if ($this->_validate($this->input, true)) {
            $this->userModel->updateUser($id, $this->input);

            echo $this->json([], 204);
        }
    }

    private function _validate(array $input, bool $updating = false)
    {
        // Validate empty fields
        if (empty($input['name']) or empty($input['email']) or empty($input['password']) or !is_array($input)) {
            echo $this->json([
                'success' => false,
                'error' => 'Preencha todos os campos.'
            ], 422);

            die;
        }

        // Validate if is a valid email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            echo $this->json([
                'success' => false,
                'error' => 'Preencha um email válido.'
            ], 422);

            die;
        }

        // Checks if email already exists
        $user = $this->userModel->getUserByEmail($input['email']);
        if (!$updating and ($user and count($user) >= 1)) {
            echo $this->json([
                'success' => false,
                'error' => 'Email já cadastrado.'
            ], 422);

            die;
        }

        return true;
    }

    public function login()
    {
        // Validate empty fields
        if (empty($this->input['email']) or empty($this->input['password']) or !is_array($this->input)) {
            echo $this->json([
                'success' => false,
                'error' => 'Preencha todos os campos.'
            ], 422);

            die;
        }

        $user = $this->userModel->getUserByEmail($this->input['email']);

        if (is_array($user) and count($user) == 1) {
            $user = $user[0];

            if (password_verify($this->input['password'], $user['password'])) {
                $token = md5(uniqid(rand(), true));

                if ($savedToken = $this->userModel->authenticateUser($user['iduser'], $token)) {
                    $user['token'] = $savedToken;

                    unset($user['password']);

                    $mlCount = $this->userModel->getDrinkWater($user['iduser']);

                    echo $this->json([
                        'success' => true,
                        'data' => array_merge($user, ['drink_counter' => $mlCount]),
                    ], 200);
                } else {
                    echo $this->json([
                        'success' => false
                    ], 422);
                }

                die;
            }
        }

        echo $this->json([
            'success' => false,
            'error' => 'Acesso não autorizado. Verifique seu usuário e senha.'
        ], 403);

        die;
    }

    public function destroy(array $data)
    {
        // Validate access
        $this->validateAccess();

        // Get user by auth token
        $user = $this->userModel->getUserByToken($this->getAcessToken());

        $id = $data['id'];

        if (!$user or count($user) == 0) {
            echo $this->json([
                'success' => false,
                'error' => 'Usuário não encontrado.'
            ], 404);

            die;
        };

        // Check if user id found by token is equals incoming id
        if ($user[0]['user_id'] != $id) {
            echo $this->json([
                'success' => false,
                'error' => 'Você só pode excluir seu prório usuário.'
            ], 403);

            die;
        }

        if ($this->userModel->destroyUser($id)) {
            echo $this->json([], 204);

            die;
        };

        echo $this->json([
            'success' => false,
            'error' => 'Não foi possível excluir o usuário.'
        ], 422);
    }
    
    public function drink(array $data)
    {
        // Validate access
        $this->validateAccess();
        
        $id = $data['id'];

        // Get user by id
        $user = $this->userModel->getUserById($id);
        
        unset($user[0]['password']);

        if (!$user or count($user) == 0) {
            echo $this->json([
                'success' => false,
                'error' => 'Usuário não encontrado.'
            ], 404);

            die;
        };

        if (empty($this->input['drink_ml'])) {
            echo $this->json([
                'success' => false,
                'error' => 'Especifique a quantidade em ML.'
            ], 422);

            die;
        }

        $mlCount = $this->userModel->drinkWater($id, $this->input['drink_ml']);
        
        if (!$mlCount) {
            if (empty($this->input['drink_ml'])) {
                echo $this->json([
                    'success' => false,
                    'error' => 'Não foi possível realizar a operação.'
                ], 422);

                die;
            }
        }

        echo $this->json([
            'success' => true,
            'message' => 'Atualizado com sucesso.',
            'data' => array_merge($user[0], ['drink_counter' => $mlCount])
        ], 422);
    }
}
