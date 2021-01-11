<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use \src\handlers\PostHandler;


class HomeController extends Controller {

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


    public function index() {

        //pega por GET qual o NÚMERO DA PÁGINA
        $page = intval(filter_input(INPUT_GET, 'page'));

       

        $feed = PostHandler::getHomeFeed(
            //Passo como Parametro o ID do User Logado (EU)
            $this->loggedUser->id,
            $page 
        );

        $this->render('home', [
            'loggedUser' => $this->loggedUser,
            'feed' => $feed
        ]);
    }

    

}