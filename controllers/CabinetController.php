<?php

class CabinetController
{
    public function actionIndex()
    {
        $categories = Category::getCategoryList();
        if (!$categories) {$categories = array();}

        $email = User::isLogged();
        $user = User::getUserByEmail($email);

        require_once(ROOT . '/views/cabinet/index.php');
        return true;
    }

    public function actionEdit()
    {
        $categories = Category::getCategoryList();
        if (!$categories) {$categories = array();}

        $email = User::isLogged();
        $user = User::getUserByEmail($email);

        $name     = $user['name'];
        $password = '';
        $result   = '';

        if (isset($_POST['submit'])) {
            $name     = FunctionLibrary::clearStr($_POST['name']);
            $password = FunctionLibrary::clearStr($_POST['password']);

            $errors = array();

            if (!User::checkName($name)) {
                $errors[] = 'Имя должно быть больше 1 символа.';
            }

            if (!User::checkPassword($password)) {
                $errors[] = 'Пароль должен быть больше 5 символов.';
            }

            if (empty($errors)) {
                $result = User::edit($user['id'], $name, $password);
            }
        }

        require_once(ROOT . '/views/cabinet/edit.php');
        return true;
    }
}