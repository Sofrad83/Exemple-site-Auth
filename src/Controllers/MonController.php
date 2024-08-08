<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Support\Auth;
use App\Support\Route;
use App\Support\User;
use Psr\Http\Message\ServerRequestInterface as Request;

class MonController extends BaseController{

  public function index(){
    $this->data = [
      'connected' => Auth::isLoggedIn(),
      'user' => Auth::getUserById($_SESSION['user_id'] ?? 0)
    ];
    $this->template = "index";
  }

  public function register()
  {
    $this->template = "register";
  }

  public function createUser(Request $request)
  {
    $data = $request->getParsedBody();
    $sql = "Insert into user (username, email, password) values (:username, :email, :password)";
    $params = ['username' => $data["username"], "email" => $data["email"], "password" => User::hashPassword($data["password"])];
    $result = $this->dbQuery->execute($sql, $params);

    Auth::login($data['username'], $data['password'], false);
    
    $this->redirect = true;
    $this->data = [
      "url" => "/"
    ];
  }

  public function login()
  {
    $this->template = "login";
  }

  public function connect(Request $request)
  {
    $data = $request->getParsedBody();
    Auth::login($data['username'], $data['password'], isset($data["remember_me"]) ? boolval($data["remember_me"]) : false );
    $this->redirect = true;
    $this->data = [
      "url" => "/"
    ];
  }

  public function logout()
  {
    Auth::logout();
    $this->redirect = true;
    $this->data = [
      "url" => "/"
    ];
  }
}