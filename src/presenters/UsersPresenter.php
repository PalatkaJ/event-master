<?php

namespace presenters;

use models\UserModel;
use presenters\BasePresenter;
use src\Container;

require_once MODELS_DIR.'/UserModel.php';

class UsersPresenter extends BasePresenter
{
    private UserModel $userModel;

    private function processLogin(string $reqMethod, mixed $data): void {
        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'login.php';
                break;
            case 'POST':
                $email = $data['email'] ?? '';
                $user = $this->userModel->getUserByEmail($email);

                if ($user) {
                    $this->container->loginUser($user['email'], $user['full_name']);
                    header("Location: " . BASE_URL . "/");
                    exit;
                } else {
                    header("Location: " . BASE_URL . "/register");
                    exit;
                }
        }
    }

    private function processLogout(): void {
        $this->container->logoutUser();
        header("Location: " . BASE_URL . "/");
        exit;
    }

    private function processRegister(string $reqMethod, mixed $data): void {
        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'register_new_user.php';
                break;
            case 'POST':
                $email = $data['email'] ?? '';
                $fullName = $data['full_name'] ?? '';

                try {
                    $this->userModel->createUser(['email' => $email, 'full_name' => $fullName]);
                    $this->container->loginUser($email, $fullName);
                    header("Location: " . BASE_URL . "/");
                    exit;
                } catch (\UserAlreadyExistsException $ue) {
                    header("Location: " . BASE_URL . "/login");
                    exit;
                }
        }
    }

    private function processSettings(string $reqMethod, mixed $data): void {
        $email = $this->container->getLoggedUser()['email'];

        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'user_detail.php';
                break;
            case 'POST':
                if (isset($data['_method']) && $data['_method'] === 'DELETE') {
                    // TODO
                    // $this->userModel->deleteUser($email);
                    $this->container->logoutUser();
                    header("Location: " . BASE_URL . "/");
                } else {
                    $newName = $data['full_name'] ?? '';
                    $this->userModel->updateUser($email, $newName);
                    $this->container->loginUser($email, $newName);
                    header("Location: " . BASE_URL . "/settings");
                }
                exit;
        }
    }

    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void
    {
        if (!isset($this->userModel)) {
            $this->userModel = new UserModel($this->mysqli);
        }

        switch ($url[0]) {
            case 'login':
                $this->processLogin($requestMethod, $data);
                break;
            case 'register':
                $this->processRegister($requestMethod, $data);
                break;
            case 'logout':
                $this->processLogout();
                break;
            case 'settings':
                $this->processSettings($requestMethod, $data);
                break;
            default:
                throw new \NotFoundException("invalid url");
        }
    }
}