<?php

namespace App\Controller;
use App\Support\Auth;

class BaseController{
  public $template;
  public $data = [];
  public $status = 200;
  public $response;
  public $dbQuery;
  public $json = false;
  public $redirect = false;

  public function __construct($action, $request, $response, $dbQuery){

    if (!method_exists($this, $action)){
      $this->template = "page404";
      $this->status = 404;
      return;
    }
    $this->dbQuery = $dbQuery;
    $this->$action($request);
    if(!Auth::isLoggedIn() && !in_array($action, ["login", "register", "forgot-password"])){
      $this->redirect = true;
      $this->data = [
        "url" => "/login"
      ];
      $this->makeRedirect($response);
      return;
    }else{
      if($this->redirect){
        $this->makeRedirect($response);
      }else if($this->json){
        $this->makeJson($response);
      }else{
        $this->makePage($response);
      }
    }

  }

  public function makePage($response){
    global $twig;
    $html = $twig->render($this->template.".twig", $this->data);
    $response->getBody()->write($html);
    $this->response  = $response;
  }

  public function makeJson($response){
    $response->getBody()->write(json_encode($this->data));
    $this->response  = $response->withHeader('Content-Type', 'application/json');
  }

  public function makeRedirect($response){
    $this->response = $response->withHeader('Location', $this->data['url'])->withStatus(302);
  }
}