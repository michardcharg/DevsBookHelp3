<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use \src\handlers\PostHandler;


class PostController extends Controller {

    //Tem as infos do User Logado
    private $loggedUser;

    public function __construct() {
        //Uso uma função da Classe LOGINHANDLER para checar se tem user logado
        //E guardo dentro da VAR LOGGEDUSER
        $this->loggedUser = LoginHandler::checkLogin();

        //Caso o retorno da VERIFICAÇÃO seja FALSE, ja direciona para LOGIN
        if($this->loggedUser === false) {
            $this->redirect('/login');
        }
        
       
    }


    public function new() {
        $body = trim(strip_tags(filter_input(INPUT_POST, 'body')));


       if($body) {

            PostHandler::addPost(
                $this->loggedUser->id,
                'text',
                $body
            );
       }

       $this->redirect('/');

    }

    

}