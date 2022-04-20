<?php


namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;

class SignupController extends Controller
{
    public function indexAction()
    {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $this->mongo->users->insertOne($data);
            header('location:/login/index');
        }
    }
}