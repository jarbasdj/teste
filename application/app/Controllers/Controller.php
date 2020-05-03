<?php

namespace RestAPI\Controllers;

use RestAPI\Models\User as UserModel;

class Controller
{
    private $accessToken;

    public function __construct()
    {
        $headers = apache_request_headers();

        $this->accessToken = $headers['AccessToken'] ?? false;
    }

    public function index()
    {
        require_once VIEWSPATH . '/welcome.php';
    }

    protected function json(array $array, int $httpCode = 200): string
    {
        header('Content-type: application/json');

        http_response_code($httpCode);

        return json_encode($array);
    }

    protected function parseJsonParameters()
    {
        return (array) json_decode(file_get_contents('php://input'), true);
    }

    public function validateAccess()
    {
        $userModel = new UserModel;


        if (empty($this->accessToken)) {
            echo $this->json([
                'success' => false,
                'error' => 'Preencha um token de autenticação.'
            ], 422);

            die;
        }

        if (!$userModel->validateToken($this->accessToken)) {
            echo $this->json([
                'success' => false,
                'error' => 'Acesso não autorizado.'
            ], 403);

            die;
        }

        return true;
    }

    public function getAcessToken()
    {
        return $this->accessToken;
    }
}
