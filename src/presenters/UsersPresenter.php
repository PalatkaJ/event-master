<?php

namespace presenters;

use models\UserModel;
use src;

require_once MODELS_DIR.'/UserModel.php';

class UsersPresenter extends BasePresenter
{
    private UserModel $userModel;

    private function isUserEmailValid(array $data) {
        $this->formValidator->validateEmail($data);
        return $this->formValidator->isValid();
    }

    private function tryLogInUser(array $data) {
        $email = $data['email'];
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

    private function processLogin(string $reqMethod, mixed $data): void {
        $this->templateFilename = 'login.php';
        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                if (!$this->isUserEmailValid($data)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }
                $this->tryLogInUser($data);
        }
    }

    private function processLogout(): void {
        $this->container->logoutUser();
        header("Location: " . BASE_URL . "/");
        exit;
    }

    private function isUserFormValid(array $data): bool {
        $this->formValidator->validateUser($data);
        return $this->formValidator->isValid();
    }

    private function tryCreateUser(array $data): void {
        $email = $data['email'];
        $fullName = $data['full_name'];

        try {
            $this->userModel->createUser(['email' => $email, 'full_name' => $fullName]);
            $this->container->loginUser($email, $fullName);
            header("Location: " . BASE_URL . "/");
            exit;
        } catch (src\UserAlreadyExistsException $ue) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }
    }

    private function processRegister(string $reqMethod, mixed $data): void {
        $this->templateFilename = 'register_new_user.php';

        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                if (!$this->isUserFormValid($data)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }

                $this->tryCreateUser($data);
        }
    }

    private function updateUser(array $data, array $currentUser): void {
        $this->userModel->updateUser($currentUser['email'], $data['full_name']);
        $this->container->loginUser($currentUser['email'], $data['full_name']);
    }

    private function processSettings(string $reqMethod, mixed $data): void {
        $currentUser = $this->requireLogin();
        $this->templateFilename = 'user_detail.php';

        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                if (!$this->isUserFormValid($data)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }

                $this->updateUser($data, $currentUser);
                header("Location: " . BASE_URL . "/settings");
                exit;
        }
    }

    private function processAccountDelete(string $reqMethod): void {
        if ($reqMethod !== 'POST') {
            throw new src\NotFoundException();
        }

        $currentUser = $this->requireLogin();
        $this->container->logoutUser();
        $this->userModel->deleteUser($currentUser['email']);
        header("Location: " . BASE_URL . "/");
        exit;
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
            case 'delete':
                $this->processAccountDelete($requestMethod);
                break;
            default:
                throw new src\NotFoundException();
        }
    }
}