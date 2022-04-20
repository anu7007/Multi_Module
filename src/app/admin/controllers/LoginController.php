<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->request->getPost()) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $collection = $this->mongo->users->findOne(['email' => $email, 'password' => $password]);
            if ($email == $collection->email && $password == $collection->password) {
                // $adapter = new Stream('../app/admin/logs/login.log');
                // $logger  = new Logger(
                //     'messages',
                //     [
                //         'main' => $adapter,
                //     ]
                // );
                // $logger->info("User Logged in successfully.");
                header('location:/index/index');
            } elseif ($email == "") {
                $this->view->msg="Please enter correct email/password!!";
            } else {
                $this->view->msg="Unauthorized Access!!";
            }
        }
    }
}
